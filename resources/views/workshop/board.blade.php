@extends('layouts.app')

@section('title', 'Workshop Floor Board')
@section('page_title', 'Workshop Floor & Service Bay Live Board')

@section('content')
<div x-data="{ showCreateBayModal: false }" class="space-y-6">

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
            <div class="flex flex-wrap items-center gap-3">
                <!-- Create Bay Trigger Button -->
                <button type="button" 
                        @click="showCreateBayModal = true"
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> New Service Bay
                </button>

                <a href="{{ route('job-cards.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> New Job Card
                </a>
                
                <button onclick="window.location.reload()" class="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-semibold transition" title="Refresh Live Board">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <!-- Interactive Bays Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 mt-6">
            @foreach($serviceBays as $bay)
                @php
                    $isOccupied = $bay->status === 'occupied';
                    $jc = $bay->activeJobCards->first();
                @endphp
                <div class="p-4 rounded-xl border {{ $isOccupied ? 'bg-amber-950/40 border-amber-500/40' : ($bay->status === 'maintenance' ? 'bg-rose-950/30 border-rose-500/30' : 'bg-slate-800/60 border-slate-700') }} flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="font-mono font-bold text-slate-300">{{ $bay->code }}</span>
                            <span class="px-2 py-0.5 text-[10px] uppercase font-bold rounded-full {{ $isOccupied ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : ($bay->status === 'maintenance' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30') }}">
                                {{ $bay->status }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-white mb-0.5 truncate" title="{{ $bay->name }}">{{ $bay->name }}</h4>
                        <div class="text-[10px] text-slate-400 truncate">{{ $bay->bay_type }}</div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-700/60 text-xs">
                        @if($jc)
                            <div class="font-bold text-blue-400 truncate">{{ $jc->vehicle->registration_number ?? 'Vehicle' }}</div>
                            <div class="text-[11px] text-slate-300 truncate">{{ $jc->vehicle->brand->name ?? '' }} {{ $jc->vehicle->model->name ?? '' }}</div>
                            <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                <i data-lucide="user" class="w-3 h-3 text-slate-500"></i>
                                <span class="truncate">{{ $jc->leadMechanic->name ?? 'Unassigned' }}</span>
                            </div>
                        @else
                            <div class="text-[11px] text-slate-500 italic">No vehicle on lift</div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Add Service Bay Card Tile -->
            <button type="button" 
                    @click="showCreateBayModal = true"
                    class="p-4 rounded-xl border-2 border-dashed border-slate-700 hover:border-emerald-500/70 bg-slate-800/30 hover:bg-slate-800/70 text-slate-400 hover:text-emerald-400 transition flex flex-col items-center justify-center gap-2 group min-h-[140px]">
                <div class="w-8 h-8 rounded-lg bg-slate-800 group-hover:bg-emerald-500/20 flex items-center justify-center transition border border-slate-700 group-hover:border-emerald-500/40">
                    <i data-lucide="plus" class="w-4 h-4 text-slate-400 group-hover:text-emerald-400"></i>
                </div>
                <span class="text-xs font-bold">+ New Service Bay</span>
                <span class="text-[10px] text-slate-500 text-center">Add lift or station</span>
            </button>
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

    <!-- Create Service Bay Modal -->
    <div x-show="showCreateBayModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="showCreateBayModal" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             @click="showCreateBayModal = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Card -->
        <div x-show="showCreateBayModal" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0 scale-95 translate-y-2" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
             x-transition:leave-end="opacity-0 scale-95 translate-y-2" 
             class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full p-6 sm:p-7 z-10 my-8">

            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Add Workshop Service Bay</h3>
                        <p class="text-xs text-slate-500">Configure a new hydraulic lift, alignment pit, or paint booth</p>
                    </div>
                </div>
                <button type="button" @click="showCreateBayModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('workshop.bays.store') }}" class="mt-4 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Bay Code *</label>
                        @php
                            $nextBayNum = sprintf("BAY-%02d", $serviceBays->count() + 1);
                        @endphp
                        <input type="text" name="code" required value="{{ old('code', $nextBayNum) }}" placeholder="e.g. BAY-06" class="w-full px-3 py-2 text-xs font-mono font-bold uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                        @error('code') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Initial Status *</label>
                        <select name="status" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="available" selected>Available (Empty)</option>
                            <option value="maintenance">Under Maintenance</option>
                            <option value="occupied">Occupied</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Bay Name / Equipment Description *</label>
                    <input type="text" name="name" required placeholder="e.g. Bay {{ $serviceBays->count() + 1 }} - Heavy Duty Two-Post Hydraulic Lift" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                    @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Bay Specialization / Type *</label>
                    <select name="bay_type" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="General Mechanical">General Mechanical & Periodic Maintenance</option>
                        <option value="Two-Post Lift">Two-Post Hydraulic Lift</option>
                        <option value="Four-Post Heavy Lift">Four-Post Heavy Duty Lift</option>
                        <option value="Wheel Alignment">3D Wheel Alignment & Balancing Pit</option>
                        <option value="Quick Lube">Express Lube & Quick Inspection</option>
                        <option value="EV Bay">Electric Vehicle (EV) High Voltage Diagnostics</option>
                        <option value="Paint Booth">Down-Draft Heated Paint Booth</option>
                        <option value="Body Shop">Denting, Panel Beating & Chassis Straightening</option>
                        <option value="Washing & Detailing">Hydro-Pressure Washing & Detailing</option>
                        <option value="Quality Inspection">Final QC & Road Test Inspection</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <label class="flex items-center text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 mr-2">
                        Bay is Active & Available for Job Allocation
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="showCreateBayModal = false" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i> Create Service Bay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
