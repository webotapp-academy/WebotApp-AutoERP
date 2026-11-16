<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobCard;
use App\Models\JobCardPart;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ServiceBay;
use App\Models\SparePart;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\ServiceReminder;
use App\Models\PurchaseOrder;
use App\Models\RepairEstimate;
use App\Models\StockLedger;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roleCategory = $user ? $user->role_category : 'management';

        // 1. General Executive / Management KPIs
        $totalJobCards = JobCard::count();
        $activeJobCards = JobCard::whereNotIn('status', ['delivered', 'cancelled'])->count();
        $deliveredToday = JobCard::where('status', 'delivered')->whereDate('delivered_at', today())->count();
        $todayAppointments = Appointment::whereDate('appointment_date', today())->count();

        // Revenue metrics
        $totalRevenue = Payment::sum('amount');
        $monthlyRevenue = Payment::whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount');
        $outstandingReceivables = Invoice::sum('balance_due');

        // Floor & Service Bays
        $serviceBays = ServiceBay::with('activeJobCards.vehicle')->get();
        $totalBays = $serviceBays->count();
        $occupiedBays = $serviceBays->where('status', 'occupied')->count();
        $bayOccupancyRate = $totalBays > 0 ? round(($occupiedBays / $totalBays) * 100) : 0;

        // Inventory Stock Alerts
        $lowStockParts = SparePart::whereColumn('current_stock', '<=', 'min_stock_alert')->where('is_active', true)->get();

        // Pending Service Reminders
        $pendingRemindersCount = ServiceReminder::where('status', 'pending')->whereDate('due_date', '<=', now()->addDays(7))->count();

        // Recent Job Cards
        $recentJobCards = JobCard::with(['customer', 'vehicle.brand', 'vehicle.model', 'leadMechanic', 'serviceBay'])
            ->latest()
            ->take(8)
            ->get();

        // Recent Payments
        $recentPayments = Payment::with(['customer', 'invoice'])
            ->latest()
            ->take(8)
            ->get();

        // Status counts for chart
        $jobStatusCounts = [
            'Pending' => JobCard::whereIn('status', ['draft', 'pending_estimate'])->count(),
            'Approved' => JobCard::where('status', 'estimate_approved')->count(),
            'In Progress' => JobCard::where('status', 'work_in_progress')->count(),
            'Quality Check' => JobCard::where('status', 'quality_check')->count(),
            'Ready' => JobCard::where('status', 'ready_for_delivery')->count(),
            'Delivered' => JobCard::where('status', 'delivered')->count(),
        ];

        // 2. Mechanic Specific Data
        $myAssignedJobCards = collect();
        $myCompletedThisMonth = 0;
        $myPendingQc = 0;
        $myAssignedBay = null;
        $myRequestedParts = collect();

        if ($roleCategory === 'mechanic' && $user) {
            $myAssignedJobCards = JobCard::where(function ($q) use ($user) {
                $q->where('lead_mechanic_id', $user->id)
                  ->orWhereNull('lead_mechanic_id'); // allow mechanics to claim unassigned active jobs
            })->whereNotIn('status', ['delivered', 'cancelled'])
              ->with(['vehicle.brand', 'vehicle.model', 'customer', 'serviceBay'])
              ->latest()
              ->get();

            $myCompletedThisMonth = JobCard::where('lead_mechanic_id', $user->id)
                ->where('status', 'delivered')
                ->whereMonth('delivered_at', now()->month)
                ->count();

            $myPendingQc = JobCard::where('lead_mechanic_id', $user->id)
                ->where('status', 'quality_check')
                ->count();

            $myAssignedBay = ServiceBay::whereHas('activeJobCards', function ($q) use ($user) {
                $q->where('lead_mechanic_id', $user->id);
            })->first();

            $myRequestedParts = JobCardPart::whereHas('jobCard', function ($q) use ($user) {
                $q->where('lead_mechanic_id', $user->id);
            })->latest()->take(5)->get();
        }

        // 3. Inventory Manager Specific Data
        $totalInventoryValuation = 0.00;
        $openPurchaseOrdersCount = 0;
        $unissuedPartRequests = collect();
        $recentStockMovements = collect();

        if ($roleCategory === 'inventory' || $roleCategory === 'management') {
            $totalInventoryValuation = SparePart::selectRaw('SUM(current_stock * purchase_price) as total_val')->value('total_val') ?? 0;
            $openPurchaseOrdersCount = PurchaseOrder::whereIn('status', ['draft', 'ordered'])->count();
            $unissuedPartRequests = JobCardPart::where('status', 'requested')
                ->with(['jobCard.vehicle', 'jobCard.customer', 'sparePart'])
                ->latest()
                ->take(10)
                ->get();
            $recentStockMovements = StockLedger::with(['sparePart', 'creator'])
                ->latest()
                ->take(8)
                ->get();
        }

        // 4. Accountant Specific Data
        $todayCollections = 0.00;
        $monthInvoicedTotal = 0.00;
        $monthlyCgst = 0.00;
        $monthlySgst = 0.00;
        $monthlyIgst = 0.00;
        $unpaidInvoices = collect();

        if ($roleCategory === 'accountant' || $roleCategory === 'management') {
            $todayCollections = Payment::whereDate('payment_date', today())->sum('amount');
            $monthInvoicedTotal = Invoice::whereMonth('invoice_date', now()->month)->sum('grand_total');
            $monthlyCgst = Invoice::whereMonth('invoice_date', now()->month)->sum('cgst_amount');
            $monthlySgst = Invoice::whereMonth('invoice_date', now()->month)->sum('sgst_amount');
            $monthlyIgst = Invoice::whereMonth('invoice_date', now()->month)->sum('igst_amount');
            $unpaidInvoices = Invoice::where('payment_status', '!=', 'paid')
                ->with(['customer', 'vehicle'])
                ->orderByDesc('balance_due')
                ->take(10)
                ->get();
        }

        // 5. Service Advisor Specific Data
        $todayAppointmentsList = collect();
        $pendingEstimatesList = collect();
        $readyDeliveriesList = collect();

        if ($roleCategory === 'advisor' || $roleCategory === 'management') {
            $todayAppointmentsList = Appointment::whereDate('appointment_date', today())
                ->with(['customer', 'vehicle.brand', 'vehicle.model'])
                ->orderBy('appointment_time')
                ->get();
            $pendingEstimatesList = RepairEstimate::where('status', 'pending_approval')
                ->with(['customer', 'vehicle'])
                ->latest()
                ->take(8)
                ->get();
            $readyDeliveriesList = JobCard::where('status', 'ready_for_delivery')
                ->with(['customer', 'vehicle', 'invoice'])
                ->latest()
                ->get();
        }

        return view('dashboard.index', compact(
            'roleCategory',
            'user',
            'totalJobCards',
            'activeJobCards',
            'deliveredToday',
            'todayAppointments',
            'totalRevenue',
            'monthlyRevenue',
            'outstandingReceivables',
            'serviceBays',
            'bayOccupancyRate',
            'lowStockParts',
            'pendingRemindersCount',
            'recentJobCards',
            'recentPayments',
            'jobStatusCounts',
            // Mechanic
            'myAssignedJobCards',
            'myCompletedThisMonth',
            'myPendingQc',
            'myAssignedBay',
            'myRequestedParts',
            // Inventory
            'totalInventoryValuation',
            'openPurchaseOrdersCount',
            'unissuedPartRequests',
            'recentStockMovements',
            // Accountant
            'todayCollections',
            'monthInvoicedTotal',
            'monthlyCgst',
            'monthlySgst',
            'monthlyIgst',
            'unpaidInvoices',
            // Advisor
            'todayAppointmentsList',
            'pendingEstimatesList',
            'readyDeliveriesList'
        ));
    }
}


