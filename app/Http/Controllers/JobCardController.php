<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\JobCardPart;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\ServiceBay;
use App\Models\Service;
use App\Models\SparePart;
use App\Models\Company;
use App\Models\Branch;
use App\Services\JobCardService as JobCardWorkflowService;
use App\Services\InventoryService;
use App\Services\BillingService;
use Exception;

class JobCardController extends Controller
{
    protected JobCardWorkflowService $workflowService;
    protected InventoryService $inventoryService;
    protected BillingService $billingService;

    public function __construct(
        JobCardWorkflowService $workflowService,
        InventoryService $inventoryService,
        BillingService $billingService
    ) {
        $this->workflowService = $workflowService;
        $this->inventoryService = $inventoryService;
        $this->billingService = $billingService;
    }

    public function index(Request $request)
    {
        $query = JobCard::with(['customer', 'vehicle.brand', 'vehicle.model', 'serviceAdvisor', 'leadMechanic', 'serviceBay']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_card_number', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($vq) use ($search) {
                      $vq->where('registration_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $jobCards = $query->latest()->paginate(15)->withQueryString();

        return view('job_cards.index', compact('jobCards'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('first_name')->get();
        $vehicles = Vehicle::with(['brand', 'model'])->orderBy('registration_number')->get();
        $advisors = User::whereHas('role', fn($q) => $q->whereIn('slug', ['service_advisor', 'workshop_manager', 'super_admin']))->get();
        $mechanics = User::whereHas('role', fn($q) => $q->whereIn('slug', ['mechanic', 'workshop_manager']))->get();
        $serviceBays = ServiceBay::all();

        $selectedCustomerId = $request->customer_id;
        $selectedVehicleId = $request->vehicle_id;
        $appointmentId = $request->appointment_id;

        return view('job_cards.create', compact('customers', 'vehicles', 'advisors', 'mechanics', 'serviceBays', 'selectedCustomerId', 'selectedVehicleId', 'appointmentId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'service_advisor_id' => 'required|exists:users,id',
            'lead_mechanic_id' => 'nullable|exists:users,id',
            'service_bay_id' => 'nullable|exists:service_bays,id',
            'job_type' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'odometer_in' => 'required|integer|min:0',
            'fuel_level' => 'required|string',
            'promised_delivery_time' => 'nullable|date',
            'customer_complaints' => 'required|string',
            'diagnosis_remarks' => 'nullable|string',
        ]);

        $company = Company::first();
        $branch = Branch::first();
        $jobCardNumber = $this->workflowService->generateJobCardNumber();

        $jobCard = JobCard::create(array_merge($validated, [
            'job_card_number' => $jobCardNumber,
            'company_id' => $company->id ?? 1,
            'branch_id' => $branch->id ?? 1,
            'check_in_time' => now(),
            'status' => 'draft',
            'estimated_labour' => 0.00,
            'estimated_parts' => 0.00,
            'estimated_total' => 0.00,
            'final_labour' => 0.00,
            'final_parts' => 0.00,
            'final_tax' => 0.00,
            'final_grand_total' => 0.00,
        ]));

        return redirect()->route('job-cards.show', $jobCard->id)->with('success', "Job Card {$jobCardNumber} created successfully! You can now add services and required parts.");
    }

    public function show(JobCard $jobCard)
    {
        $jobCard->load([
            'customer',
            'vehicle.brand',
            'vehicle.model',
            'serviceAdvisor',
            'leadMechanic',
            'serviceBay',
            'services.service',
            'parts.sparePart',
            'estimates',
            'qualityInspection',
            'invoice'
        ]);

        $allServices = Service::where('is_active', true)->orderBy('name')->get();
        $allParts = SparePart::where('is_active', true)->where('current_stock', '>', 0)->orderBy('name')->get();
        $mechanics = User::whereHas('role', fn($q) => $q->where('slug', 'mechanic'))->get();
        $serviceBays = ServiceBay::all();

        return view('job_cards.show', compact('jobCard', 'allServices', 'allParts', 'mechanics', 'serviceBays'));
    }

    public function addService(Request $request, JobCard $jobCard)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'mechanic_id' => 'nullable|exists:users,id',
            'labour_charge' => 'required|numeric|min:0',
        ]);

        $service = Service::findOrFail($request->service_id);
        $taxAmount = round(($validated['labour_charge'] * $service->default_gst_rate) / 100, 2);

        JobCardService::create([
            'job_card_id' => $jobCard->id,
            'service_id' => $service->id,
            'service_name' => $service->name,
            'mechanic_id' => $request->mechanic_id ?? $jobCard->lead_mechanic_id,
            'estimated_hours' => $service->standard_time_hours,
            'labour_charge' => $validated['labour_charge'],
            'gst_rate' => $service->default_gst_rate,
            'tax_amount' => $taxAmount,
            'total' => $validated['labour_charge'] + $taxAmount,
            'status' => 'pending',
        ]);

        $jobCard->recalculateTotals();

        return back()->with('success', "Service '{$service->name}' added to job card.");
    }

    public function addPart(Request $request, JobCard $jobCard)
    {
        $validated = $request->validate([
            'spare_part_id' => 'required|exists:spare_parts,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $part = SparePart::findOrFail($request->spare_part_id);
        $unitPrice = $part->selling_price;
        $subtotal = $unitPrice * $request->quantity;
        $taxAmount = round(($subtotal * $part->gst_rate) / 100, 2);

        JobCardPart::create([
            'job_card_id' => $jobCard->id,
            'spare_part_id' => $part->id,
            'part_name' => $part->name,
            'part_number' => $part->part_number,
            'quantity' => $request->quantity,
            'unit_price' => $unitPrice,
            'discount' => 0.00,
            'gst_rate' => $part->gst_rate,
            'tax_amount' => $taxAmount,
            'total_price' => $subtotal + $taxAmount,
            'status' => 'allocated',
        ]);

        $jobCard->recalculateTotals();

        return back()->with('success', "Spare part '{$part->name}' allocated to job card.");
    }

    public function issuePart(JobCardPart $jobCardPart)
    {
        try {
            $this->inventoryService->issuePartToJobCard($jobCardPart, auth()->id());
            return back()->with('success', "Part '{$jobCardPart->part_name}' issued from inventory. Stock ledger updated.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function returnPart(JobCardPart $jobCardPart)
    {
        try {
            $this->inventoryService->returnPartFromJobCard($jobCardPart, auth()->id());
            return back()->with('success', "Part returned to inventory. Stock balance incremented.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, JobCard $jobCard)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        try {
            $this->workflowService->transitionStatus($jobCard, $validated['status'], $validated['remarks'] ?? null);
            return back()->with('success', "Job Card transitioned to '" . ucfirst(str_replace('_', ' ', $validated['status'])) . "'.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function createInvoice(JobCard $jobCard)
    {
        try {
            $invoice = $this->billingService->createInvoiceFromJobCard($jobCard, auth()->id());
            return redirect()->route('invoices.show', $invoice->id)->with('success', "GST Tax Invoice #{$invoice->invoice_number} generated!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function printWorkOrder(JobCard $jobCard)
    {
        $jobCard->load(['customer', 'vehicle.brand', 'vehicle.model', 'serviceAdvisor', 'leadMechanic', 'services', 'parts']);
        return view('job_cards.print_work_order', compact('jobCard'));
    }

    public function printGatePass(JobCard $jobCard)
    {
        if (empty($jobCard->gate_pass_number)) {
            $jobCard->gate_pass_number = 'GP-' . date('Ymd') . '-' . sprintf('%04d', $jobCard->id);
            $jobCard->gate_pass_generated_at = now();
            $jobCard->save();
        }
        $jobCard->load(['customer', 'vehicle.brand', 'vehicle.model', 'serviceAdvisor']);
        return view('job_cards.print_gate_pass', compact('jobCard'));
    }
}
