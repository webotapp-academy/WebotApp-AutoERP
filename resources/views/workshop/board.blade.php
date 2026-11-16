@extends('layouts.app')

@section('title', 'Workshop Floor Board')
@section('page_title', 'Workshop Floor & Service Bay Live Board')

@section('content')
<div class="space-y-6">

    <!-- Top Summary Banner -->
    <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-lg border border-slate-800">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-1 text-xs font-bold uppercase bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-full tracking-wider">
                    ⚡ Live Workshop Floor Feed
                </span>
                <h2 class="text-xl font-bold mt-2 text-white">Central Service Bays & Lift Status</h2>
                <p class="text-xs text-slate-400 mt-1">Real-time occupancy of hydraulic lifts, 3D alignment, EV stations and paint booth</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('job-cards.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> New Job Card
                </a>
                <button onclick="window.location.reload()" class="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-semibold transition" title="Refresh Live Board">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <!-- 5 Interactive Bays Bar -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mt-6">
            @foreach($serviceBays as $bay)
                @php
                    $isOccupied = $bay->status === 'occupied';
                    $jc = $bay->activeJobCards->first();
                @endphp
                <div class="p-4 rounded-xl border {{ $isOccupied ? 'bg-amber-950/40 border-amber-500/40' : 'bg-slate-800/60 border-slate-700' }} flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="font-mono font-bold text-slate-300">{{ $bay->code }}</span>
                            <span class="px-2 py-0.5 text-[10px] uppercase font-bold rounded-full {{ $isOccupied ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }}">
                                {{ $bay->status }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-white mb-0.5 truncate">{{ $bay->name }}</h4>
                        <div class="text-[10px] text-slate-400">{{ $bay->bay_type }}</div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-700/60 text-xs">
                        @if($jc)
                            <div class="font-bold text-blue-400 truncate">{{ $jc->vehicle->registration_number ?? 'Vehicle' }}</div>
                            <div class="text-[11px] text-slate-300 truncate">{{ $jc->vehicle->brand->name ?? '' }} {{ $jc->vehicle->model->name ?? '' }}</div>
                            <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                <i data-lucide="user" class="w-3 h-3 text-slate-500"></i>
                                <span>{{ $jc->leadMechanic->name ?? 'Unassigned' }}</span>
                            </div>
                        @else
                            <div class="text-[11px] text-slate-500 italic">No vehicle on lift</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Live Job Cards Kanban Columns -->
    <div>
        <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
            <i data-lucide="kanban" class="w-5 h-5 text-blue-600"></i>
            Active Repair Kanban Pipeline
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($kanbanColumns as $colKey => $col)
                <div class="bg-slate-100/70 p-4 rounded-xl border border-slate-200 flex flex-col">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-{{ $col['color'] }}-500"></span>
                            <span class="text-xs font-bold text-slate-800">{{ $col['title'] }}</span>
                        </div>
                        <span class="px-2 py-0.5 text-xs font-mono font-bold bg-white text-slate-700 rounded-full border border-slate-200 shadow-sm">
                            {{ $col['jobs']->count() }}
                        </span>
                    </div>

                    <div class="space-y-3 flex-1 overflow-y-auto max-h-[600px]">
                        @forelse($col['jobs'] as $job)
                            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm hover:border-blue-400 hover:shadow-md transition">
                                <div class="flex items-center justify-between text-xs mb-2">
                                    <a href="{{ route('job-cards.show', $job->id) }}" class="font-mono font-bold text-blue-600 hover:underline">
                                        {{ $job->job_card_number }}
                                    </a>
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded uppercase {{ $job->priority === 'urgent' ? 'bg-rose-100 text-rose-700' : ($job->priority === 'high' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                                        {{ $job->priority }}
                                    </span>
                                </div>

                                <div class="font-bold text-slate-900 text-sm mb-1">
                                    {{ $job->vehicle->registration_number ?? 'Vehicle' }}
                                </div>
                                <div class="text-xs text-slate-500 mb-2">
                                    {{ $job->vehicle->brand->name ?? '' }} {{ $job->vehicle->model->name ?? '' }}
                                </div>

                                <div class="p-2 bg-slate-50 rounded text-[11px] text-slate-600 mb-3 line-clamp-2">
                                    {{ $job->customer_complaints ?? 'No specific complaint registered' }}
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-100">
                                    <div class="flex items-center gap-1">
                                        <i data-lucide="user-check" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span class="truncate max-w-[100px]">{{ $job->leadMechanic->name ?? 'Unassigned' }}</span>
                                    </div>
                                    <a href="{{ route('job-cards.show', $job->id) }}" class="text-blue-600 font-semibold hover:underline flex items-center gap-0.5">
                                        Open <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-xs text-slate-400 italic">
                                No job cards in this stage
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

