@extends('layouts.app')

@section('title', 'Stock Ledger - ' . $sparePart->name)
@section('page_title', 'Stock Ledger: ' . $sparePart->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Inventory
        </a>
    </div>

    <!-- Part Summary Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold text-slate-900">{{ $sparePart->name }}</h2>
                <span class="px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-700 rounded">{{ $sparePart->category->name ?? 'General' }}</span>
            </div>
            <div class="text-xs text-slate-500 font-mono mt-1">Part #: {{ $sparePart->part_number }} • Brand: {{ $sparePart->brand }} • Rack: {{ $sparePart->bin_location ?? 'N/A' }}</div>
        </div>

        <div class="flex items-center gap-6">
            <div class="text-right">
                <span class="text-[11px] font-semibold uppercase text-slate-400">Current Balance</span>
                <div class="text-2xl font-mono font-bold {{ $sparePart->isLowStock() ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ $sparePart->current_stock }} <span class="text-xs text-slate-500 font-sans">{{ $sparePart->unit }}</span>
                </div>
            </div>
            <div class="text-right">
                <span class="text-[11px] font-semibold uppercase text-slate-400">Unit Valuation</span>
                <div class="text-xl font-mono font-bold text-slate-800">
                    ₹{{ number_format($sparePart->purchase_price, 2) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Stock Movement Audit Trail</h3>
            <p class="text-xs text-slate-500">Immutable ledger of every receipt, job card issue, return, and reconciliation</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Date & Time</th>
                        <th class="px-6 py-3.5">Transaction Type</th>
                        <th class="px-6 py-3.5">Reference</th>
                        <th class="px-6 py-3.5">Qty Change</th>
                        <th class="px-6 py-3.5">Unit Cost (₹)</th>
                        <th class="px-6 py-3.5">Balance After</th>
                        <th class="px-6 py-3.5">Remarks / Reason</th>
                        <th class="px-6 py-3.5">Created By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($ledgers as $ledger)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono">
                                {{ $ledger->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded uppercase {{ $ledger->quantity > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ str_replace('_', ' ', $ledger->transaction_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-800">
                                {{ $ledger->reference_type }} {{ $ledger->reference_id ? '#' . $ledger->reference_id : '' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold {{ $ledger->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $ledger->quantity > 0 ? '+' : '' }}{{ $ledger->quantity }}
                            </td>
                            <td class="px-6 py-4 font-mono">
                                ₹{{ number_format($ledger->unit_cost, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                {{ $ledger->balance_after }}
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $ledger->remarks }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $ledger->user->name ?? 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No stock transactions recorded for this item.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ledgers->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $ledgers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

