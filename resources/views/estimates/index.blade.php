@extends('layouts.app')

@section('title', 'Repair Estimates')
@section('page_title', 'Repair Estimates & Quotations')

@section('content')
<div class="space-y-6">
    <!-- Top Filter Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('estimates.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                <option value="">All Estimate Statuses</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent to Customer</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition">Filter</button>
            @if(request()->filled('status'))
                <a href="{{ route('estimates.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900">Reset</a>
            @endif
        </form>

        <a href="{{ route('estimates.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center justify-center gap-2 shrink-0">
            <i data-lucide="calculator" class="w-4 h-4"></i> New Estimate
        </a>
    </div>

    <!-- Estimates Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Estimate #</th>
                        <th class="px-6 py-3.5">Job Card</th>
                        <th class="px-6 py-3.5">Customer</th>
                        <th class="px-6 py-3.5">Vehicle</th>
                        <th class="px-6 py-3.5">Labour (₹)</th>
                        <th class="px-6 py-3.5">Parts (₹)</th>
                        <th class="px-6 py-3.5">Grand Total (₹)</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($estimates as $est)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                <a href="{{ route('estimates.show', $est->id) }}">{{ $est->estimate_number }}</a>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-800">
                                <a href="{{ route('job-cards.show', $est->job_card_id) }}" class="hover:underline">{{ $est->jobCard->job_card_number ?? 'N/A' }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $est->customer->full_name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $est->customer->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $est->vehicle->registration_number ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $est->vehicle->brand->name ?? '' }} {{ $est->vehicle->model->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono">
                                ₹{{ number_format($est->subtotal_labour, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono">
                                ₹{{ number_format($est->subtotal_parts, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                ₹{{ number_format($est->grand_total, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $est->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($est->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $est->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('estimates.show', $est->id) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-md text-xs font-bold transition">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                No estimates found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($estimates->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $estimates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
