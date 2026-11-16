@extends('layouts.app')

@section('title', 'GST Invoices')
@section('page_title', 'Billing & GST Tax Invoices')

@section('content')
<div class="space-y-6">

    <!-- Top Financial Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Invoiced</p>
                <div class="text-2xl font-bold text-slate-900 mt-1">₹{{ number_format($totalInvoiced, 2) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="receipt" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Collected Revenue</p>
                <div class="text-2xl font-bold text-emerald-600 mt-1">₹{{ number_format($totalPaid, 2) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending Receivables</p>
                <div class="text-2xl font-bold text-rose-600 mt-1">₹{{ number_format($totalOutstanding, 2) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Invoice #, Customer, Phone..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition" />
            </div>

            <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                <option value="">All Payment Statuses</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('invoices.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900">Reset</a>
            @endif
        </form>

        <a href="{{ route('invoices.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center justify-center gap-2 shrink-0">
            <i data-lucide="plus" class="w-4 h-4"></i> Create Invoice
        </a>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Invoice #</th>
                        <th class="px-6 py-3.5">Date</th>
                        <th class="px-6 py-3.5">Customer</th>
                        <th class="px-6 py-3.5">Vehicle</th>
                        <th class="px-6 py-3.5">Taxable Value (₹)</th>
                        <th class="px-6 py-3.5">GST Tax (₹)</th>
                        <th class="px-6 py-3.5">Grand Total (₹)</th>
                        <th class="px-6 py-3.5">Balance Due (₹)</th>
                        <th class="px-6 py-3.5">Payment Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                <a href="{{ route('invoices.show', $inv->id) }}">{{ $inv->invoice_number }}</a>
                            </td>
                            <td class="px-6 py-4">
                                {{ $inv->invoice_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('customers.show', $inv->customer_id) }}" class="font-bold text-slate-900 hover:text-blue-600">
                                    {{ $inv->customer->full_name ?? 'N/A' }}
                                </a>
                                <div class="text-[11px] text-slate-500">{{ $inv->customer->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $inv->vehicle->registration_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono">
                                ₹{{ number_format($inv->taxable_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600">
                                ₹{{ number_format($inv->total_tax, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                ₹{{ number_format($inv->grand_total, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold {{ $inv->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                ₹{{ number_format($inv->balance_due, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $inv->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($inv->payment_status === 'partially_paid' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $inv->payment_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded text-xs transition">
                                        View
                                    </a>
                                    <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="p-1 text-slate-500 hover:text-blue-600" title="Print GST Invoice">
                                        <i data-lucide="printer" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-400">
                                No invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

