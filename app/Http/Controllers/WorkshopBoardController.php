<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceBay;
use App\Models\JobCard;
use App\Models\User;
use App\Models\Branch;

class WorkshopBoardController extends Controller
{
    public function index()
    {
        $serviceBays = ServiceBay::with(['activeJobCards.vehicle', 'activeJobCards.leadMechanic', 'activeJobCards.customer'])->get();

        $kanbanColumns = [
            'estimate_approved' => [
                'title' => 'Ready for Work',
                'color' => 'indigo',
                'jobs' => JobCard::where('status', 'estimate_approved')->with(['vehicle', 'customer', 'leadMechanic'])->get(),
            ],
            'work_in_progress' => [
                'title' => 'In Progress (On Bays)',
                'color' => 'blue',
                'jobs' => JobCard::where('status', 'work_in_progress')->with(['vehicle', 'customer', 'leadMechanic', 'serviceBay'])->get(),
            ],
            'quality_check' => [
                'title' => 'Quality Control (QC)',
                'color' => 'purple',
                'jobs' => JobCard::where('status', 'quality_check')->with(['vehicle', 'customer', 'leadMechanic'])->get(),
            ],
            'ready_for_delivery' => [
                'title' => 'Ready for Delivery',
                'color' => 'teal',
                'jobs' => JobCard::where('status', 'ready_for_delivery')->with(['vehicle', 'customer'])->get(),
            ],
        ];

        $mechanics = User::whereHas('role', function ($q) {
            $q->where('slug', 'mechanic');
        })->get();

        return view('workshop.board', compact('serviceBays', 'kanbanColumns', 'mechanics'));
    }

    public function storeBay(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:20|unique:service_bays,code',
            'bay_type' => 'required|string|max:100',
            'status' => 'required|in:available,occupied,maintenance',
            'is_active' => 'nullable|boolean',
        ]);

        $branch = Branch::first();

        $bay = ServiceBay::create([
            'branch_id' => $branch->id ?? 1,
            'name' => $validated['name'],
            'code' => strtoupper(trim($validated['code'])),
            'bay_type' => $validated['bay_type'],
            'status' => $validated['status'] ?? 'available',
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'bay' => $bay,
                'message' => "Service Bay '{$bay->name}' ({$bay->code}) created successfully!",
            ], 201);
        }

        return redirect()->route('workshop.board')->with('success', "Service Bay '{$bay->name}' ({$bay->code}) created successfully!");
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'job_card_id' => 'required|exists:job_cards,id',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $jobCard = JobCard::findOrFail($validated['job_card_id']);
        app(\App\Services\JobCardService::class)->transitionStatus($jobCard, $validated['status'], $validated['remarks'] ?? null);

        return back()->with('success', "Job Card #{$jobCard->job_card_number} updated to " . ucfirst(str_replace('_', ' ', $validated['status'])) . ".");
    }
}
