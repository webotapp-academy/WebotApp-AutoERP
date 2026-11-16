@extends('layouts.app')

@section('title', $estimate->estimate_number)
@section('page_title', 'Repair Estimate: ' . $estimate->estimate_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('estimates.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Estimates
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('estimates.print', $estimate->id) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i> Print Quotation
            </a>
            @if($estimate->status !== 'approved')
                <form method="POST" action="{{ route('estimates.approve', $estimate->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-4 h-4"></i> Approve Estimate
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Estimate Details Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-200 pb-6">
            <div>
                <span class="text-[11px] font-bold uppercase text-blue-600 tracking-wider">ABC Automobile Care & Service Centre</span>
                <h2 class="text-2xl font-black text-slate-900 mt-1">REPAIR ESTIMATE QUOTATION</h2>
                <div class="text-xs text-slate-500 mt-0.5">Quote Ref: <span class="font-mono font-bold text-slate-800">{{ $estimate->estimate_number }}</span> • Job Card: <a href="{{ route('job-cards.show', $estimate->job_card_id) }}" class="text-blue-600 font-mono font-bold">{{ $estimate->jobCard->job_card_number }}</a></div>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 text-xs font-bold rounded-full uppercase {{ $estimate->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $estimate->status }}
                </span>
                <div class="text-xs text-slate-400 mt-2">Date: {{ $estimate->created_at->format('d M Y') }}</div>
            </div>
        </div>

        <!-- Vehicle & Customer Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl text-xs">
            <div>
                <span class="text-slate-400 font-medium">Customer:</span>
                <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $estimate->customer->full_name ?? 'N/A' }}</div>
                <div class="text-[11px] text-slate-500">{{ $estimate->customer->phone ?? '' }}</div>
            </div>
            <div>
                <span class="text-slate-400 font-medium">Vehicle Registration:</span>
                <div class="font-mono font-bold text-slate-900 text-sm mt-0.5">{{ $estimate->vehicle->registration_number ?? 'N/A' }}</div>
                <div class="text-[11px] text-slate-500">{{ $estimate->vehicle->brand->name ?? '' }} {{ $estimate->vehicle->model->name ?? '' }}</div>
            </div>
            <div>
                <span class="text-slate-400 font-medium">Service Advisor:</span>
                <div class="font-bold text-slate-900 mt-0.5">{{ $estimate->creator->name ?? 'Staff' }}</div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Qty</th>
                        <th class="px-4 py-3">Unit Price (₹)</th>
                        <th class="px-4 py-3">GST Rate</th>
                        <th class="px-4 py-3">Tax Amount (₹)</th>
                        <th class="px-4 py-3 text-right">Total Amount (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($estimate->items as $item)
                        <tr>
                            <td class="px-4 py-3 uppercase font-bold text-[10px] text-slate-500">
                                {{ $item->item_type }}
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-800">
                                {{ $item->description }}
                            </td>
                            <td class="px-4 py-3 font-mono">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-4 py-3 font-mono">
                                ₹{{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-4 py-3 font-mono">
                                {{ $item->tax_percent }}%
                            </td>
                            <td class="px-4 py-3 font-mono">
                                ₹{{ number_format($item->tax_amount, 2) }}
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-900 text-right">
                                ₹{{ number_format($item->total_amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Financial Summary -->
        <div class="flex justify-end pt-4">
            <div class="w-72 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal Labour:</span>
                    <span class="font-mono font-bold text-slate-800">₹{{ number_format($estimate->subtotal_labour, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal Parts:</span>
                    <span class="font-mono font-bold text-slate-800">₹{{ number_format($estimate->subtotal_parts, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Total GST (18%):</span>
                    <span class="font-mono font-bold text-slate-800">₹{{ number_format($estimate->tax_amount, 2) }}</span>
                </div>
                @if($estimate->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600">
                        <span>Discount:</span>
                        <span class="font-mono font-bold">-₹{{ number_format($estimate->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-base font-bold text-slate-900 pt-2 border-t border-slate-200">
                    <span>Estimated Total:</span>
                    <span class="font-mono text-xl text-blue-600">₹{{ number_format($estimate->grand_total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

