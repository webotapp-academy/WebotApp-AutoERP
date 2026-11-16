@extends('layouts.app')

@section('title', 'Job Cards')
@section('page_title', 'Job Cards & Work Orders')

@section('content')
<div class="space-y-6">
    <!-- Top Filter & Search Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('job-cards.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Job Card #, Vehicle Plate, Customer..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition" />
            </div>

            <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="pending_estimate" {{ request('status') === 'pending_estimate' ? 'selected' : '' }}>Pending Estimate</option>
                <option value="estimate_approved" {{ request('status') === 'estimate_approved' ? 'selected' : '' }}>Estimate Approved</option>
                <option value="work_in_progress" {{ request('status') === 'work_in_progress' ? 'selected' : '' }}>Work In Progress</option>
                <option value="quality_check" {{ request('status') === 'quality_check' ? 'selected' : '' }}>Quality Check</option>
                <option value="ready_for_delivery" {{ request('status') === 'ready_for_delivery' ? 'selected' : '' }}>Ready For Delivery</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
            </select>

            <select name="priority" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                <option value="">All Priorities</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition">Filter</button>
            @if(request()->hasAny(['search', 'status', 'priority']))
                <a href="{{ route('job-cards.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900">Reset</a>
            @endif
        </form>

        <a href="{{ route('job-cards.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center justify-center gap-2 shrink-0">
            <i data-lucide="file-plus" class="w-4 h-4"></i> Create Job Card
        </a>
    </div>

    <!-- Job Cards Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Job Card #</th>
                        <th class="px-6 py-3.5">Check-In / Promised</th>
                        <th class="px-6 py-3.5">Vehicle & Model</th>
                        <th class="px-6 py-3.5">Customer</th>
                        <th class="px-6 py-3.5">Priority</th>
                        <th class="px-6 py-3.5">Lead Mechanic</th>
                        <th class="px-6 py-3.5">Lift / Bay</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Total (₹)</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($jobCards as $jc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                <a href="{{ route('job-cards.show', $jc->id) }}">{{ $jc->job_card_number }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $jc->check_in_time->format('d M Y') }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $jc->check_in_time->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('vehicles.show', $jc->vehicle_id) }}" class="font-mono font-bold text-slate-900 hover:text-blue-600">
                                    {{ $jc->vehicle->registration_number ?? 'N/A' }}
                                </a>
                                <div class="text-[11px] text-slate-500">{{ $jc->vehicle->brand->name ?? '' }} {{ $jc->vehicle->model->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('customers.show', $jc->customer_id) }}" class="font-semibold text-slate-800 hover:text-blue-600">
                                    {{ $jc->customer->full_name ?? 'N/A' }}
                                </a>
                                <div class="text-[11px] text-slate-500">{{ $jc->customer->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase {{ $jc->priority === 'urgent' ? 'bg-rose-100 text-rose-800' : ($jc->priority === 'high' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $jc->priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $jc->leadMechanic->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-700 rounded font-mono">
                                    {{ $jc->serviceBay->code ?? 'Floor' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badge = match($jc->status) {
                                        'draft' => 'bg-slate-100 text-slate-700',
                                        'pending_estimate' => 'bg-amber-100 text-amber-800',
                                        'estimate_approved' => 'bg-indigo-100 text-indigo-800',
                                        'work_in_progress' => 'bg-blue-100 text-blue-800',
                                        'quality_check' => 'bg-purple-100 text-purple-800',
                                        'ready_for_delivery' => 'bg-teal-100 text-teal-800',
                                        'delivered' => 'bg-emerald-100 text-emerald-800',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-full uppercase {{ $badge }}">
                                    {{ str_replace('_', ' ', $jc->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                ₹{{ number_format($jc->final_grand_total, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('job-cards.show', $jc->id) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-bold transition">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-400">
                                <i data-lucide="file-text" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                No job cards found matching filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jobCards->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $jobCards->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

