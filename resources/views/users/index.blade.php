@extends('layouts.app')

@section('title', 'User Management & Roles')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Staff & Access Control</h1>
            <p class="text-sm text-gray-500">Manage workshop employees, assign operational roles, and enforce RBAC permissions.</p>
        </div>
        <div>
            <button onclick="document.getElementById('addUserModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold shadow hover:bg-indigo-700 flex items-center space-x-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Add Workshop Staff</span>
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Total Users</p>
                    <p class="text-2xl font-black text-gray-900 mt-1">{{ $users->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-xl">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Active Roles</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1">{{ $roles->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-xl">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Workshop Branches</p>
                    <p class="text-2xl font-black text-blue-600 mt-1">{{ $branches->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                    <i class="fa-solid fa-code-branch"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Active Mechanics</p>
                    <p class="text-2xl font-black text-amber-600 mt-1">
                        {{ $users->where('role.name', 'mechanic')->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center text-xl">
                    <i class="fa-solid fa-wrench"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-5 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <h2 class="font-bold text-gray-900">Registered Staff & Personnel</h2>
            <span class="text-xs text-gray-500">Role-Based Access Control Active</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700 font-semibold border-b">
                    <tr>
                        <th class="p-4">Name & Designation</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Phone</th>
                        <th class="p-4">Assigned Role</th>
                        <th class="p-4">Branch</th>
                        <th class="p-4">Hourly Cost</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->employee->designation ?? 'Staff Member' }} ({{ $user->employee->employee_code ?? 'EMP' }})</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 font-mono text-xs">{{ $user->email }}</td>
                        <td class="p-4 text-xs">{{ $user->phone ?? 'N/A' }}</td>
                        <td class="p-4">
                            @php
                                $badgeColor = match($user->role->name ?? '') {
                                    'admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'manager' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'service_advisor' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                    'mechanic' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'inventory_manager' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'accountant' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeColor }}">
                                {{ $user->role->display_name ?? ucfirst($user->role->name ?? 'Staff') }}
                            </span>
                        </td>
                        <td class="p-4 text-xs">{{ $user->branch->name ?? 'Main Workshop' }}</td>
                        <td class="p-4 font-semibold text-gray-900">₹{{ number_format($user->employee->hourly_rate ?? 300, 2) }}/hr</td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                Active
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">{{ $role->display_name }}</h3>
                <span class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-700 font-semibold">{{ $role->users_count }} Users</span>
            </div>
            <p class="text-xs text-gray-600 mt-3 leading-relaxed">{{ $role->description ?? 'Workshop role access.' }}</p>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Role Slug: <code class="bg-gray-50 px-1 py-0.5 rounded text-indigo-600 font-bold">{{ $role->name }}</code></span>
                <span class="text-emerald-600 font-medium"><i class="fa-solid fa-shield-halved mr-1"></i>Secured</span>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal Add User -->
<div id="addUserModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4 pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-900">Add New Workshop Staff</h3>
            <button onclick="document.getElementById('addUserModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Full Name *</label>
                <input type="text" name="name" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border" placeholder="e.g. Ramesh Kumar">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email Address *</label>
                    <input type="email" name="email" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border" placeholder="staff@automobile.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Password *</label>
                    <input type="password" name="password" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border" placeholder="Min 6 characters">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phone *</label>
                    <input type="text" name="phone" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border" placeholder="+91 98765 43210">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Designation *</label>
                    <input type="text" name="designation" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border" placeholder="e.g. Diagnostic Technician">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Assign Role *</label>
                    <select name="role_id" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Assigned Branch *</label>
                    <select name="branch_id" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Hourly Labour Billing Rate (₹)</label>
                <input type="number" step="10" name="hourly_rate" value="350" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">
            </div>
            <div class="pt-4 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Save Staff Member</button>
            </div>
        </form>
    </div>
</div>
@endsection
