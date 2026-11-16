@extends('layouts.app')

@section('title', 'Payments Received')
@section('page_title', 'Payment Receipts & Cash Register')

@section('content')
<div class="space-y-6">

    <!-- Top Summary Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Collections Recorded</span>
            <div class="text-3xl font-mono font-black text-emerald-600 mt-1">₹{{ number_format($totalCollected, 2) }}</div>
            <p class="text-xs text-slate-500 mt-0.5">Payments reconciled across Cash, UPI, Cards, and Net Banking</p>
        </div>

        <form method="GET" action="{{ route('payments.index') }}" class="flex items-center gap-2">
            <select name="method" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                <option value="">All Payment Modes</option>
                <option value="upi" {{ request('method') === 'upi' ? 'selected' : '' }}>UPI / QR Code</option>
                <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="card" {{ request('method') === 'card' ? 'selected' : '' }}>Card (POS)</option>
                <option value="net_banking" {{ request('method') === 'net_banking' ? 'selected' : '' }}>Net Banking</option>
            </select>
            @if(request()->filled('method'))
                <a href="{{ route('payments.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900">Reset</a>
            @endif
        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Receipt #</th>
                        <th class="px-6 py-3.5">Payment Date</th>
                        <th class="px-6 py-3.5">Invoice #</th>
                        <th class="px-6 py-3.5">Customer</th>
                        <th class="px-6 py-3.5">Payment Mode</th>
                        <th class="px-6 py-3.5">Transaction Ref / UTR</th>
                        <th class="px-6 py-3.5">Received By</th>
                        <th class="px-6 py-3.5">Amount (₹)</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($payments as $pmt)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                <a href="{{ route('payments.receipt', $pmt->id) }}" target="_blank">{{ $pmt->payment_number }}</a>
                            </td>
                            <td class="px-6 py-4">
                                {{ $pmt->payment_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-800">
                                <a href="{{ route('invoices.show', $pmt->invoice_id) }}" class="hover:underline">{{ $pmt->invoice->invoice_number ?? 'N/A' }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('customers.show', $pmt->customer_id) }}" class="font-bold text-slate-800 hover:text-blue-600">
                                    {{ $pmt->customer->full_name ?? 'N/A' }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase bg-slate-100 text-slate-800">
                                    {{ $pmt->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-500">
                                {{ $pmt->transaction_reference ?? 'Counter Cash' }}
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $pmt->receiver->name ?? 'Cashier' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-emerald-600 text-sm">
                                ₹{{ number_format($pmt->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('payments.receipt', $pmt->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-blue-600 rounded" title="Print Receipt">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                No payments recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
