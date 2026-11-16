@extends('layouts.app')

@section('title', 'Mechanics & Technicians')
@section('page_title', 'Mechanics & Floor Technicians')

@section('content')
<div class="space-y-6">

    <!-- Active Repair Timers (Section 12.4 in todo.txt) -->
    @if($activeTimeLogs->count() > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 mb-3 flex items-center gap-1.5">
                <i data-lucide="clock" class="w-4 h-4 text-amber-600 animate-spin"></i> Active Repair Timers Running on Floor
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($activeTimeLogs as $tl)
                    <div class="bg-white p-3.5 rounded-xl border border-amber-200 shadow-sm flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-slate-800">{{ $tl->mechanic->name ?? 'Technician' }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $tl->jobCard->job_card_number }} • {{ $tl->jobCard->vehicle->registration_number ?? 'Vehicle' }}</div>
                            <div class="text-[10px] text-amber-600 font-medium mt-1">Started {{ $tl->started_at->diffForHumans() }}</div>
                        </div>
                        <form method="POST" action="{{ route('mechanics.stop-timer', $tl->id) }}">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded text-[11px] font-bold transition">
                                Stop Timer
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Technicians Roster Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($mechanics as $m)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4 hover:border-blue-300 transition">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 font-bold text-xl flex items-center justify-center shrink-0">
                        <i data-lucide="wrench" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug">{{ $m->name }}</h3>
                        <div class="text-xs text-blue-600 font-semibold">{{ $m->employee->designation ?? 'Master Technician' }}</div>
                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $m->employee->employee_code ?? 'EMP' }} • {{ $m->phone }}</div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50 rounded-xl text-xs">
                    <div>
                        <span class="text-slate-400 font-medium">Hourly Rate:</span>
                        <div class="font-mono font-bold text-slate-800 mt-0.5">₹{{ number_format($m->employee->hourly_rate ?? 0, 2) }}/hr</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Active Jobs:</span>
                        <div class="font-bold text-blue-600 mt-0.5">{{ $m->jobCardsAssigned->count() }} Vehicle(s)</div>
                    </div>
                </div>

                <!-- Active Assigned Vehicles -->
                <div>
                    <div class="text-[11px] font-bold uppercase text-slate-400 tracking-wider mb-2">Currently Assigned Jobs</div>
                    <div class="space-y-1.5">
                        @forelse($m->jobCardsAssigned as $jc)
                            <a href="{{ route('job-cards.show', $jc->id) }}" class="flex items-center justify-between p-2 rounded-lg bg-slate-50 hover:bg-blue-50 text-xs text-slate-700 transition">
                                <span class="font-mono font-bold text-blue-600">{{ $jc->job_card_number }}</span>
                                <span class="font-bold text-slate-800">{{ $jc->vehicle->registration_number ?? 'Car' }}</span>
                                <span class="px-1.5 py-0.5 text-[9px] uppercase font-bold rounded bg-slate-200 text-slate-700">{{ str_replace('_', ' ', $jc->status) }}</span>
                            </a>
                        @empty
                            <div class="text-[11px] text-slate-400 italic py-1">No active job cards currently assigned. Available for next dispatch.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
