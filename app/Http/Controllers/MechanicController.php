<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\JobCardTimeLog;

class MechanicController extends Controller
{
    public function index()
    {
        $mechanics = User::whereHas('role', fn($q) => $q->where('slug', 'mechanic'))
            ->with(['employee.skills', 'jobCardsAssigned' => fn($q) => $q->whereNotIn('status', ['delivered', 'cancelled'])])
            ->get();

        $activeTimeLogs = JobCardTimeLog::with(['jobCard.vehicle', 'mechanic'])
            ->whereNull('ended_at')
            ->get();

        return view('mechanics.index', compact('mechanics', 'activeTimeLogs'));
    }

    public function startTimer(Request $request)
    {
        $validated = $request->validate([
            'job_card_id' => 'required|exists:job_cards,id',
            'job_card_service_id' => 'nullable|exists:job_card_services,id',
            'mechanic_id' => 'required|exists:users,id',
        ]);

        JobCardTimeLog::create([
            'job_card_id' => $request->job_card_id,
            'job_card_service_id' => $request->job_card_service_id,
            'mechanic_id' => $request->mechanic_id,
            'started_at' => now(),
            'notes' => 'Technician started operation on workshop bay',
        ]);

        return back()->with('success', 'Repair timer started for technician.');
    }

    public function stopTimer(JobCardTimeLog $timeLog)
    {
        $timeLog->ended_at = now();
        $timeLog->duration_minutes = $timeLog->started_at->diffInMinutes($timeLog->ended_at);
        $timeLog->save();

        return back()->with('success', "Timer stopped. Total duration: {$timeLog->duration_minutes} minutes.");
    }
}
