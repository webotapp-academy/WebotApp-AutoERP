<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RepairEstimate;
use App\Models\RepairEstimateItem;
use App\Models\JobCard;
use App\Models\Branch;

class EstimateController extends Controller
{
    public function index(Request $request)
    {
        $query = RepairEstimate::with(['jobCard', 'customer', 'vehicle.brand', 'vehicle.model']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $estimates = $query->latest()->paginate(15)->withQueryString();

        return view('estimates.index', compact('estimates'));
    }

    public function create(Request $request)
    {
        $jobCards = JobCard::whereNotIn('status', ['delivered', 'cancelled'])->with(['customer', 'vehicle'])->get();
        $selectedJobCardId = $request->job_card_id;

        return view('estimates.create', compact('jobCards', 'selectedJobCardId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_card_id' => 'required|exists:job_cards,id',
            'subtotal_labour' => 'required|numeric|min:0',
            'subtotal_parts' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'customer_notes' => 'nullable|string',
        ]);

        $jobCard = JobCard::findOrFail($request->job_card_id);
        $branch = Branch::first();
        $count = RepairEstimate::whereYear('created_at', date('Y'))->count() + 1;
        $estNumber = sprintf("EST-%s-%04d", date('Y'), $count);

        $taxable = ($request->subtotal_labour + $request->subtotal_parts) - ($request->discount_amount ?? 0);
        $tax = round($taxable * 0.18, 2);
        $grandTotal = $taxable + $tax;

        $estimate = RepairEstimate::create([
            'estimate_number' => $estNumber,
            'job_card_id' => $jobCard->id,
            'customer_id' => $jobCard->customer_id,
            'vehicle_id' => $jobCard->vehicle_id,
            'branch_id' => $branch->id ?? 1,
            'subtotal_labour' => $request->subtotal_labour,
            'subtotal_parts' => $request->subtotal_parts,
            'discount_amount' => $request->discount_amount ?? 0,
            'tax_amount' => $tax,
            'grand_total' => $grandTotal,
            'status' => 'sent',
            'customer_notes' => $request->customer_notes,
            'created_by' => auth()->id(),
        ]);

        RepairEstimateItem::create([
            'repair_estimate_id' => $estimate->id,
            'item_type' => 'service',
            'description' => 'Estimated Labour & Diagnostic Work',
            'quantity' => 1,
            'unit_price' => $request->subtotal_labour,
            'tax_percent' => 18.00,
            'tax_amount' => round($request->subtotal_labour * 0.18, 2),
            'total_amount' => round($request->subtotal_labour * 1.18, 2),
        ]);

        if ($request->subtotal_parts > 0) {
            RepairEstimateItem::create([
                'repair_estimate_id' => $estimate->id,
                'item_type' => 'part',
                'description' => 'Estimated Replacement Parts & Consumables',
                'quantity' => 1,
                'unit_price' => $request->subtotal_parts,
                'tax_percent' => 18.00,
                'tax_amount' => round($request->subtotal_parts * 0.18, 2),
                'total_amount' => round($request->subtotal_parts * 1.18, 2),
            ]);
        }

        return redirect()->route('estimates.show', $estimate->id)->with('success', "Repair Estimate #{$estNumber} generated and ready for approval!");
    }

    public function show(RepairEstimate $estimate)
    {
        $estimate->load(['jobCard', 'customer', 'vehicle.brand', 'vehicle.model', 'items', 'creator']);
        return view('estimates.show', compact('estimate'));
    }

    public function approve(RepairEstimate $estimate)
    {
        $estimate->status = 'approved';
        $estimate->approved_at = now();
        $estimate->save();

        if ($estimate->jobCard && in_array($estimate->jobCard->status, ['draft', 'pending_estimate'])) {
            $estimate->jobCard->status = 'estimate_approved';
            $estimate->jobCard->save();
        }

        return back()->with('success', "Estimate #{$estimate->estimate_number} marked as Approved. Job Card transitioned to 'Estimate Approved'.");
    }

    public function reject(RepairEstimate $estimate)
    {
        $estimate->status = 'rejected';
        $estimate->save();

        return back()->with('info', "Estimate #{$estimate->estimate_number} marked as Rejected by customer.");
    }

    public function print(RepairEstimate $estimate)
    {
        $estimate->load(['jobCard', 'customer', 'vehicle.brand', 'vehicle.model', 'items']);
        return view('estimates.print', compact('estimate'));
    }
}

