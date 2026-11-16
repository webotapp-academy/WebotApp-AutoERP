<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\Payment;
use App\Models\SparePart;
use App\Models\Customer;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        // 1. Sales Register & Revenue
        $invoices = Invoice::whereBetween('invoice_date', [$startDate, $endDate])->get();
        $salesTaxable = $invoices->sum('taxable_amount');
        $salesCgst = $invoices->sum('cgst_amount');
        $salesSgst = $invoices->sum('sgst_amount');
        $salesGrand = $invoices->sum('grand_total');

        // 2. Collections
        $collectionsTotal = Payment::whereBetween('payment_date', [$startDate, $endDate])->sum('amount');

        // 3. Outstanding Customer Receivables
        $receivables = Customer::where('outstanding_balance', '>', 0)->orderByDesc('outstanding_balance')->take(10)->get();

        // 4. Mechanic Productivity
        $mechanicStats = User::whereHas('role', fn($q) => $q->where('slug', 'mechanic'))
            ->withCount(['jobCardsAssigned as completed_jobs' => fn($q) => $q->where('status', 'delivered')])
            ->get();

        // 5. Inventory Valuation
        $inventoryValuation = SparePart::selectRaw('SUM(current_stock * purchase_price) as total_cost, SUM(current_stock * selling_price) as total_retail')
            ->first();

        // 6. Job Card Statistics
        $jobCardStats = [
            'total' => JobCard::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count(),
            'delivered' => JobCard::where('status', 'delivered')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count(),
            'in_progress' => JobCard::where('status', 'work_in_progress')->count(),
        ];

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'invoices',
            'salesTaxable',
            'salesCgst',
            'salesSgst',
            'salesGrand',
            'collectionsTotal',
            'receivables',
            'mechanicStats',
            'inventoryValuation',
            'jobCardStats'
        ));
    }

    public function exportSalesCsv(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        $invoices = Invoice::with(['customer', 'vehicle'])->whereBetween('invoice_date', [$startDate, $endDate])->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=sales_report_{$startDate}_{$endDate}.csv",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($invoices) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice #', 'Date', 'Customer Name', 'Customer GSTIN', 'Vehicle Plate', 'Taxable Value', 'CGST (9%)', 'SGST (9%)', 'Grand Total', 'Payment Status']);

            foreach ($invoices as $inv) {
                fputcsv($handle, [
                    $inv->invoice_number,
                    $inv->invoice_date->format('Y-m-d'),
                    $inv->customer->full_name ?? '',
                    $inv->customer->gstin ?? '',
                    $inv->vehicle->registration_number ?? '',
                    $inv->taxable_amount,
                    $inv->cgst_amount,
                    $inv->sgst_amount,
                    $inv->grand_total,
                    $inv->payment_status,
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}

