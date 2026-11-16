<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\JobCard;
use App\Models\Branch;
use App\Models\Company;
use App\Services\GstService;
use App\Services\BillingService;

class InvoiceController extends Controller
{
    protected GstService $gstService;
    protected BillingService $billingService;

    public function __construct(GstService $gstService, BillingService $billingService)
    {
        $this->gstService = $gstService;
        $this->billingService = $billingService;
    }

    public function index(Request $request)
    {
        $query = Invoice::with(['customer', 'vehicle.brand', 'vehicle.model', 'jobCard']);

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->latest('invoice_date')->paginate(15)->withQueryString();

        $totalInvoiced = Invoice::sum('grand_total');
        $totalPaid = Invoice::sum('paid_amount');
        $totalOutstanding = Invoice::sum('balance_due');

        return view('invoices.index', compact('invoices', 'totalInvoiced', 'totalPaid', 'totalOutstanding'));
    }

    public function create()
    {
        $customers = Customer::orderBy('first_name')->get();
        $vehicles = Vehicle::with(['brand', 'model'])->orderBy('registration_number')->get();
        $jobCards = JobCard::whereDoesntHave('invoice')->whereNotIn('status', ['cancelled'])->get();

        return view('invoices.create', compact('customers', 'vehicles', 'jobCards'));
    }

    public function store(Request $request)
    {
        if ($request->filled('job_card_id')) {
            $jobCard = JobCard::findOrFail($request->job_card_id);
            $invoice = $this->billingService->createInvoiceFromJobCard($jobCard, auth()->id());
            return redirect()->route('invoices.show', $invoice->id)->with('success', "Invoice #{$invoice->invoice_number} created from Job Card!");
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'item_name' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'gst_rate' => 'required|numeric',
        ]);

        $company = Company::first();
        $branch = Branch::first();
        $count = Invoice::whereYear('created_at', date('Y'))->count() + 1;
        $invNumber = sprintf("INV-%s-%04d", date('Y'), $count);

        $taxData = $this->gstService->calculate($validated['amount'], $validated['gst_rate']);

        $invoice = Invoice::create([
            'invoice_number' => $invNumber,
            'company_id' => $company->id ?? 1,
            'branch_id' => $branch->id ?? 1,
            'customer_id' => $request->customer_id,
            'vehicle_id' => $request->vehicle_id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'invoice_type' => 'tax_invoice',
            'subtotal_labour' => $validated['amount'],
            'subtotal_parts' => 0.00,
            'taxable_amount' => $taxData['taxable_amount'],
            'cgst_amount' => $taxData['cgst_amount'],
            'sgst_amount' => $taxData['sgst_amount'],
            'igst_amount' => $taxData['igst_amount'],
            'total_tax' => $taxData['total_tax'],
            'grand_total' => $taxData['grand_total'],
            'paid_amount' => 0.00,
            'balance_due' => $taxData['grand_total'],
            'payment_status' => 'unpaid',
            'created_by' => auth()->id(),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'labour',
            'name' => $validated['item_name'],
            'hsn_sac' => '998729',
            'quantity' => 1,
            'unit_price' => $validated['amount'],
            'taxable_value' => $taxData['taxable_amount'],
            'gst_rate' => $taxData['gst_rate'],
            'cgst_amount' => $taxData['cgst_amount'],
            'sgst_amount' => $taxData['sgst_amount'],
            'igst_amount' => $taxData['igst_amount'],
            'total_amount' => $taxData['grand_total'],
        ]);

        $customer = Customer::find($request->customer_id);
        if ($customer) {
            $customer->outstanding_balance += $taxData['grand_total'];
            $customer->save();
        }

        return redirect()->route('invoices.show', $invoice->id)->with('success', "GST Tax Invoice #{$invNumber} created successfully!");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'vehicle.brand', 'vehicle.model', 'jobCard', 'items', 'payments.receiver', 'company', 'branch']);
        return view('invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['customer', 'vehicle.brand', 'vehicle.model', 'jobCard', 'items', 'payments', 'company', 'branch']);
        return view('invoices.print', compact('invoice'));
    }
}

