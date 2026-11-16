<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobCard;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ServiceBay;
use App\Models\SparePart;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\ServiceReminder;

class DashboardController extends Controller
{
    public function index()
    {
        // Key Performance Indicators (KPIs)
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
            ->take(5)
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

        return view('dashboard.index', compact(
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
            'jobStatusCounts'
        ));
    }
}
