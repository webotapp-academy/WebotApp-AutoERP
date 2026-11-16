<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QualityInspection;
use App\Models\JobCard;

class QualityController extends Controller
{
    public function index()
    {
        $inspections = QualityInspection::with(['jobCard.vehicle.brand', 'jobCard.vehicle.model', 'jobCard.customer', 'inspector'])
            ->latest()
            ->paginate(15);

        $readyForQcJobs = JobCard::where('status', 'quality_check')
            ->with(['vehicle', 'customer', 'leadMechanic'])
            ->get();

        return view('quality.index', compact('inspections', 'readyForQcJobs'));
    }

    public function create(Request $request)
    {
        $jobCards = JobCard::whereIn('status', ['work_in_progress', 'quality_check'])->with(['vehicle', 'customer'])->get();
        $selectedJobCardId = $request->job_card_id;

        return view('quality.create', compact('jobCards', 'selectedJobCardId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_card_id' => 'required|exists:job_cards,id',
            'road_test_done' => 'boolean',
            'engine_sound_ok' => 'boolean',
            'brake_test_ok' => 'boolean',
            'ac_cooling_ok' => 'boolean',
            'electrical_ok' => 'boolean',
            'wheel_nut_torqued' => 'boolean',
            'cleaning_washing_ok' => 'boolean',
            'passed' => 'required|boolean',
            'rework_required' => 'boolean',
            'rework_notes' => 'nullable|string',
        ]);

        $jobCard = JobCard::findOrFail($request->job_card_id);

        $qc = QualityInspection::create([
            'job_card_id' => $jobCard->id,
            'inspector_id' => auth()->id(),
            'road_test_done' => $request->boolean('road_test_done'),
            'engine_sound_ok' => $request->boolean('engine_sound_ok'),
            'brake_test_ok' => $request->boolean('brake_test_ok'),
            'ac_cooling_ok' => $request->boolean('ac_cooling_ok'),
            'electrical_ok' => $request->boolean('electrical_ok'),
            'wheel_nut_torqued' => $request->boolean('wheel_nut_torqued'),
            'cleaning_washing_ok' => $request->boolean('cleaning_washing_ok'),
            'passed' => $request->boolean('passed'),
            'rework_required' => $request->boolean('rework_required'),
            'rework_notes' => $request->rework_notes,
            'inspected_at' => now(),
        ]);

        if ($request->boolean('passed')) {
            $jobCard->status = 'ready_for_delivery';
            $jobCard->gate_pass_number = 'GP-' . date('Ymd') . '-' . sprintf('%04d', $jobCard->id);
            $jobCard->gate_pass_generated_at = now();
            $jobCard->save();
            return redirect()->route('quality.index')->with('success', "Quality Control PASSED for Job Card #{$jobCard->job_card_number}. Gate pass generated!");
        } else {
            $jobCard->status = 'work_in_progress';
            $jobCard->save();
            return redirect()->route('quality.index')->with('error', "Quality Control REJECTED for Job Card #{$jobCard->job_card_number}. Returned to mechanic for rework.");
        }
    }
}

