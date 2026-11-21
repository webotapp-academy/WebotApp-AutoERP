<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\JobCardTimeLog;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class MechanicController extends Controller
{
    public function index()
    {
        $mechanics = User::whereHas('role', fn($q) => $q->where('slug', 'mechanic'))
            ->with(['employee.skills', 'jobCardsAssigned' => fn($q) => $q->whereNotIn('status', ['delivered', 'cancelled'])])
            ->latest()
            ->get();

        $activeTimeLogs = JobCardTimeLog::with(['jobCard.vehicle', 'mechanic'])
            ->whereNull('ended_at')
            ->get();

        return view('mechanics.index', compact('mechanics', 'activeTimeLogs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:25',
            'password' => 'nullable|min:6',
            'designation' => 'required|string|max:100',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        $role = Role::where('slug', 'mechanic')->firstOrFail();
        $company = Company::first();
        $branch = Branch::first();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password'] ?? 'mechanic123'),
            'role_id' => $role->id,
            'branch_id' => $branch->id ?? 1,
            'company_id' => $company->id ?? 1,
            'status' => $validated['status'] === 'active' ? 'active' : 'inactive',
        ]);

        $count = Employee::count() + 1;
        Employee::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id ?? 1,
            'employee_code' => sprintf('MEC-%03d', $count),
            'designation' => $validated['designation'],
            'hourly_rate' => $validated['hourly_rate'],
            'salary' => $validated['hourly_rate'] * 160,
            'joining_date' => now(),
            'status' => $validated['status'],
        ]);

        return redirect()->route('mechanics.index')->with('success', "Technician '{$user->name}' added to roster!");
    }

    public function update(Request $request, User $mechanic)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $mechanic->id,
            'phone' => 'required|string|max:25',
            'password' => 'nullable|min:6',
            'designation' => 'required|string|max:100',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => $validated['status'] === 'active' ? 'active' : 'inactive',
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $mechanic->update($updateData);

        if ($mechanic->employee) {
            $mechanic->employee->update([
                'designation' => $validated['designation'],
                'hourly_rate' => $validated['hourly_rate'],
                'salary' => $validated['hourly_rate'] * 160,
                'status' => $validated['status'],
            ]);
        } else {
            $branch = Branch::first();
            Employee::create([
                'user_id' => $mechanic->id,
                'branch_id' => $branch->id ?? 1,
                'employee_code' => sprintf('MEC-%03d', $mechanic->id),
                'designation' => $validated['designation'],
                'hourly_rate' => $validated['hourly_rate'],
                'salary' => $validated['hourly_rate'] * 160,
                'joining_date' => now(),
                'status' => $validated['status'],
            ]);
        }

        return redirect()->route('mechanics.index')->with('success', "Technician '{$mechanic->name}' profile updated!");
    }

    public function destroy(User $mechanic)
    {
        // Check if mechanic has active unfinished jobs
        $activeJobs = $mechanic->jobCardsAssigned()->whereNotIn('status', ['delivered', 'cancelled'])->count();
        if ($activeJobs > 0) {
            return back()->with('error', "Cannot delete {$mechanic->name}. They are assigned to {$activeJobs} active job card(s). Please reassign those job cards first.");
        }

        $name = $mechanic->name;
        if ($mechanic->employee) {
            $mechanic->employee->delete();
        }
        $mechanic->delete();

        return redirect()->route('mechanics.index')->with('success', "Technician '{$name}' removed from workshop roster.");
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
