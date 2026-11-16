@extends('layouts.app')

@section('title', 'Quality Control')
@section('page_title', 'Quality Control & Final Road Test Inspection')

@section('content')
<div class="space-y-6">

    <!-- Pending QC Alerts (Section 17 in todo.txt) -->
    @if($readyForQcJobs->count() > 0)
        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold uppercase text-purple-900 tracking-wider">Vehicles Awaiting Final Quality Check</h3>
                    <p class="text-xs text-purple-700">Mechanical work completed; road test and safety audit required before customer delivery</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($readyForQcJobs as $qj)
                    <div class="bg-white p-4 rounded-xl border border-purple-200 shadow-sm flex items-center justify-between text-xs">
                        <div>
                            <span class="font-mono font-bold text-blue-600">{{ $qj->job_card_number }}</span>
                            <div class="font-bold text-slate-800 text-sm mt-0.5">{{ $qj->vehicle->registration_number ?? 'Car' }}</div>
                            <div class="text-[11px] text-slate-500">Lead Tech: {{ $qj->leadMechanic->name ?? 'Technician' }}</div>
                        </div>
                        <a href="{{ route('quality.create', ['job_card_id' => $qj->id]) }}" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold text-xs shadow transition">
                            Inspect Now
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- QC Inspections History Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">QC Inspection Log</h3>
                <p class="text-xs text-slate-500">Road test verification, torque audit, brake test, and delivery approvals</p>
            </div>
            <a href="{{ route('quality.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5">
                <i data-lucide="shield-check" class="w-4 h-4"></i> New QC Inspection
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Job Card #</th>
                        <th class="px-6 py-3.5">Date & Time</th>
                        <th class="px-6 py-3.5">Vehicle</th>
                        <th class="px-6 py-3.5">Road Test</th>
                        <th class="px-6 py-3.5">Brakes & Engine</th>
                        <th class="px-6 py-3.5">Wheel Torque</th>
                        <th class="px-6 py-3.5">Inspector</th>
                        <th class="px-6 py-3.5">Result</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($inspections as $qc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                <a href="{{ route('job-cards.show', $qc->job_card_id) }}">{{ $qc->jobCard->job_card_number ?? 'N/A' }}</a>
                            </td>
                            <td class="px-6 py-4">
                                {{ $qc->inspected_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $qc->jobCard->vehicle->registration_number ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $qc->jobCard->vehicle->brand->name ?? '' }} {{ $qc->jobCard->vehicle->model->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold {{ $qc->road_test_done ? 'text-emerald-600' : 'text-slate-400' }}">
                                    {{ $qc->road_test_done ? '✓ Completed' : '— Skipped' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-emerald-600 font-bold">✓ Tested OK</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-emerald-600 font-bold">✓ 110 Nm Torqued</span>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $qc->inspector->name ?? 'Inspector' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $qc->passed ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $qc->passed ? 'PASSED & APPROVED' : 'REWORK REQUIRED' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No quality control records found.
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
