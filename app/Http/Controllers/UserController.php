<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'branch', 'employee'])->orderBy('name')->get();
        $roles = Role::withCount('users')->get();
        $branches = Branch::all();

        return view('users.index', compact('users', 'roles', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'required|exists:branches,id',
            'phone' => 'required|string|max:25',
            'designation' => 'required|string|max:100',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        $company = Company::first();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role_id' => $validated['role_id'],
            'branch_id' => $validated['branch_id'],
            'company_id' => $company->id ?? 1,
            'status' => 'active',
        ]);

        Employee::create([
            'user_id' => $user->id,
            'branch_id' => $validated['branch_id'],
            'employee_code' => 'EMP-' . rand(200, 999),
            'designation' => $validated['designation'],
            'hourly_rate' => $validated['hourly_rate'] ?? 300,
            'salary' => ($validated['hourly_rate'] ?? 300) * 160,
            'status' => 'active',
        ]);

        return back()->with('success', "User '{$user->name}' created and assigned role!");
    }
}

