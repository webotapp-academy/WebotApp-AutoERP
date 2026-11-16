@extends('layouts.app')

@section('title', 'Customers')
@section('page_title', 'Customer Directory')

@section('content')
<div class="space-y-6">
    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('customers.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer by name, phone, email, GSTIN..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition" />
            </div>

            <select name="type" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">All Customer Types</option>
                <option value="individual" {{ request('type') === 'individual' ? 'selected' : '' }}>Individual</option>
                <option value="corporate" {{ request('type') === 'corporate' ? 'selected' : '' }}>Corporate</option>
                <option value="fleet" {{ request('type') === 'fleet' ? 'selected' : '' }}>Fleet</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition">Filter</button>
            @if(request()->hasAny(['search', 'type']))
                <a href="{{ route('customers.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900">Reset</a>
            @endif
        </form>

        <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center justify-center gap-2 shrink-0">
            <i data-lucide="user-plus" class="w-4 h-4"></i> New Customer
        </a>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Customer Name</th>
                        <th class="px-6 py-3.5">Type</th>
                        <th class="px-6 py-3.5">Contact Details</th>
                        <th class="px-6 py-3.5">City / State</th>
                        <th class="px-6 py-3.5">Registered Vehicles</th>
                        <th class="px-6 py-3.5">Outstanding Balance</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('customers.show', $customer->id) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm">
                                    {{ $customer->full_name }}
                                </a>
                                @if($customer->company_name)
                                    <div class="text-[11px] text-slate-500 font-medium">{{ $customer->company_name }}</div>
                                @endif
                                @if($customer->gstin)
                                    <span class="inline-block mt-0.5 px-1.5 py-0.2 text-[10px] bg-slate-100 text-slate-600 rounded font-mono">GSTIN: {{ $customer->gstin }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase {{ $customer->customer_type === 'fleet' ? 'bg-purple-100 text-purple-800' : ($customer->customer_type === 'corporate' ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $customer->customer_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $customer->phone }}</div>
                                <div class="text-[11px] text-slate-500">{{ $customer->email ?? 'No email' }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $customer->city ?? 'N/A' }}, {{ $customer->state ?? '' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-800 rounded-lg font-bold">
                                    <i data-lucide="car" class="w-3.5 h-3.5 text-slate-500"></i>
                                    {{ $customer->vehicles->count() }} vehicles
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold {{ $customer->outstanding_balance > 0 ? 'text-rose-600' : 'text-slate-800' }}">
                                ₹{{ number_format($customer->outstanding_balance, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase {{ $customer->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $customer->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('customers.show', $customer->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="View Profile">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="p-1.5 text-slate-600 hover:bg-slate-100 rounded" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <i data-lucide="users" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                No customers found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

