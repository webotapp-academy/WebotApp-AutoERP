@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Workshop Operations Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Active Job Cards -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Active Job Cards</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-slate-900">{{ $activeJobCards }}</span>
                    <span class="text-xs text-blue-600 font-semibold">{{ $totalJobCards }} total</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Month Revenue</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-emerald-600">₹{{ number_format($monthlyRevenue, 2) }}</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="indian-rupee" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Bay Occupancy Rate -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Bay Occupancy</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-slate-900">{{ $bayOccupancyRate }}%</span>
                    <span class="text-xs text-amber-600 font-semibold">{{ $serviceBays->where('status', 'occupied')->count() }}/{{ $serviceBays->count() }} Bays</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="layout-grid" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Outstanding Receivables -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Receivables</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-rose-600">₹{{ number_format($outstandingReceivables, 2) }}</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <i data-lucide="credit-card" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Workshop Service Bays Floor Status -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Service Bay Live Floor</h3>
                <p class="text-xs text-slate-500">Real-time status of service lift stations and diagnostic bays</p>
            </div>
            <a href="{{ route('workshop.board') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                View Workshop Board <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($serviceBays as $bay)
                <div class="p-4 rounded-xl border {{ $bay->status === 'occupied' ? 'bg-amber-50/50 border-amber-300' : ($bay->status === 'maintenance' ? 'bg-rose-50/50 border-rose-200' : 'bg-emerald-50/40 border-emerald-200') }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-mono font-bold text-slate-700">{{ $bay->code }}</span>
                        <span class="px-2 py-0.5 text-[10px] uppercase font-bold rounded-full {{ $bay->status === 'occupied' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $bay->status }}
                        </span>
                    </div>
                    <div class="text-xs font-semibold text-slate-900 truncate mb-1" title="{{ $bay->name }}">
                        {{ $bay->name }}
                    </div>
                    <div class="text-[11px] text-slate-500 mb-3">{{ $bay->bay_type }}</div>

                    @if($bay->activeJobCards->count() > 0)
                        @php $activeJc = $bay->activeJobCards->first(); @endphp
                        <div class="pt-2 border-t border-slate-200/60 text-xs">
                            <div class="font-bold text-slate-800 truncate">{{ $activeJc->vehicle->registration_number ?? 'Vehicle' }}</div>
                            <div class="text-[11px] text-slate-600 truncate">{{ $activeJc->vehicle->brand->name ?? '' }} {{ $activeJc->vehicle->model->name ?? '' }}</div>
                            <span class="inline-block mt-1 px-1.5 py-0.5 text-[10px] font-semibold bg-blue-100 text-blue-800 rounded">
                                {{ str_replace('_', ' ', $activeJc->status) }}
                            </span>
                        </div>
                    @else
                        <div class="pt-2 border-t border-slate-200/60 text-[11px] text-slate-400 italic">
                            Station Ready
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Charts & Inventory Alert Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Job Card Pipeline Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-1">Service Job Card Pipeline</h3>
            <p class="text-xs text-slate-500 mb-4">Current distribution of jobs across lifecycle stages</p>
            <div class="h-64">
                <canvas id="jobPipelineChart"></canvas>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Stock Alerts</h3>
                    <p class="text-xs text-slate-500">{{ $lowStockParts->count() }} items need reorder</p>
                </div>
                <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">All Parts</a>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto max-h-64">
                @forelse($lowStockParts->take(5) as $part)
                    <div class="p-3 rounded-lg border border-rose-100 bg-rose-50/50 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-semibold text-slate-800">{{ $part->name }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $part->part_number }} • Rack: {{ $part->bin_location ?? 'N/A' }}</div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 font-bold rounded">
                                Stock: {{ $part->current_stock }}
                            </span>
                            <div class="text-[10px] text-slate-500 mt-0.5">Min: {{ $part->min_stock_alert }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-slate-400">
                        <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500 mx-auto mb-2"></i>
                        All inventory stock levels are healthy!
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Active Job Cards Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Recent Job Cards</h3>
                <p class="text-xs text-slate-500">Live service orders being serviced in workshop</p>
            </div>
            <a href="{{ route('job-cards.index') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                View All Job Cards
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Job Card #</th>
                        <th class="px-6 py-3.5">Vehicle & Model</th>
                        <th class="px-6 py-3.5">Customer</th>
                        <th class="px-6 py-3.5">Service Advisor</th>
                        <th class="px-6 py-3.5">Mechanic</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Total</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($recentJobCards as $jc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                <a href="{{ route('job-cards.show', $jc->id) }}">{{ $jc->job_card_number }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $jc->vehicle->registration_number ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $jc->vehicle->brand->name ?? '' }} {{ $jc->vehicle->model->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $jc->customer->full_name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $jc->customer->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $jc->serviceAdvisor->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $jc->leadMechanic->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeClass = match($jc->status) {
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
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full capitalize {{ $badgeClass }}">
                                    {{ str_replace('_', ' ', $jc->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                ₹{{ number_format($jc->final_grand_total, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('job-cards.show', $jc->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md transition">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('jobPipelineChart').getContext('2d');
        const labels = @json(array_keys($jobStatusCounts));
        const data = @json(array_values($jobStatusCounts));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Job Cards',
                    data: data,
                    backgroundColor: [
                        '#94a3b8',
                        '#f59e0b',
                        '#6366f1',
                        '#3b82f6',
                        '#a855f7',
                        '#10b981'
                    ],
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
@endpush
