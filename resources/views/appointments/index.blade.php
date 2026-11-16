@extends('layouts.app')

@section('title', 'Appointments')
@section('page_title', 'Service Appointments & Booking Schedule')

@section('content')
<div class="space-y-6">
    <!-- Top Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('appointments.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">All Statuses</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="arrived" {{ request('status') === 'arrived' ? 'selected' : '' }}>Arrived</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition">Filter</button>
            @if(request()->hasAny(['status', 'date']))
                <a href="{{ route('appointments.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900">Reset</a>
            @endif
        </form>

        <a href="{{ route('appointments.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center justify-center gap-2 shrink-0">
            <i data-lucide="calendar-plus" class="w-4 h-4"></i> Book Appointment
        </a>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Appointment #</th>
                        <th class="px-6 py-3.5">Date & Time</th>
                        <th class="px-6 py-3.5">Customer</th>
                        <th class="px-6 py-3.5">Vehicle</th>
                        <th class="px-6 py-3.5">Service Requested</th>
                        <th class="px-6 py-3.5">Assigned Bay</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($appointments as $apt)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                {{ $apt->appointment_number }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $apt->appointment_date->format('d M Y') }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ date('h:i A', strtotime($apt->appointment_time)) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('customers.show', $apt->customer_id) }}" class="font-bold text-slate-800 hover:text-blue-600">
                                    {{ $apt->customer->full_name ?? 'N/A' }}
                                </a>
                                <div class="text-[11px] text-slate-500">{{ $apt->customer->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $apt->vehicle->registration_number ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $apt->vehicle->brand->name ?? '' }} {{ $apt->vehicle->model->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-800">{{ $apt->service_type }}</span>
                                @if($apt->estimated_cost > 0)
                                    <div class="text-[11px] text-slate-500">Est: ₹{{ number_format($apt->estimated_cost, 2) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-700 rounded">
                                    {{ $apt->serviceBay->code ?? 'Unallocated' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $apt->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : ($apt->status === 'arrived' ? 'bg-emerald-100 text-emerald-800' : ($apt->status === 'cancelled' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700')) }}">
                                    {{ $apt->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($apt->status !== 'completed' && $apt->status !== 'cancelled')
                                        <a href="{{ route('job-cards.create', ['customer_id' => $apt->customer_id, 'vehicle_id' => $apt->vehicle_id, 'appointment_id' => $apt->id]) }}" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-bold transition">
                                            Check-in
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <i data-lucide="calendar" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                No appointments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
