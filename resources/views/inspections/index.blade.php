@extends('layouts.app')

@section('title', 'Vehicle Inspections')
@section('page_title', 'Vehicle Check-In & Digital Inspections')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h3 class="text-base font-bold text-slate-900">Check-In Inspections</h3>
            <p class="text-xs text-slate-500">Exterior scratch checks, fuel levels, tires, toolkits, and condition logs</p>
        </div>

        <a href="{{ route('inspections.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center justify-center gap-2 shrink-0">
            <i data-lucide="clipboard-plus" class="w-4 h-4"></i> New Inspection Check-In
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Inspection #</th>
                        <th class="px-6 py-3.5">Date & Time</th>
                        <th class="px-6 py-3.5">Vehicle</th>
                        <th class="px-6 py-3.5">Customer</th>
                        <th class="px-6 py-3.5">Odometer In</th>
                        <th class="px-6 py-3.5">Fuel Level</th>
                        <th class="px-6 py-3.5">Inspector</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($inspections as $ins)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                <a href="{{ route('inspections.show', $ins->id) }}">{{ $ins->inspection_number }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $ins->inspection_date->format('d M Y') }}</div>
                                <div class="text-[11px] text-slate-500">{{ $ins->inspection_date->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('vehicles.show', $ins->vehicle_id) }}" class="font-mono font-bold text-slate-900 hover:text-blue-600">
                                    {{ $ins->vehicle->registration_number ?? 'N/A' }}
                                </a>
                                <div class="text-[11px] text-slate-500">{{ $ins->vehicle->brand->name ?? '' }} {{ $ins->vehicle->model->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $ins->customer->full_name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $ins->customer->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-800">
                                {{ number_format($ins->odometer_reading) }} km
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase bg-slate-100 text-slate-700">
                                    {{ str_replace('_', ' ', $ins->fuel_level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $ins->inspector->name ?? 'Inspector' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('inspections.show', $ins->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md transition">
                                    View Report
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <i data-lucide="clipboard-check" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                No inspections recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inspections->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $inspections->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
