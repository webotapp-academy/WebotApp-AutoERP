@extends('layouts.app')

@section('title', 'Reports & Analytics')
@section('page_title', 'Workshop Reports & GST Tax Analytics')

@section('content')
<div class="space-y-6">

    <!-- Date Range Filter & Export Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-500 uppercase">From:</span>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-500 uppercase">To:</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition">Update Period</button>
        </form>

        <a href="{{ route('reports.export-sales-csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5 shrink-0">
            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Export Sales CSV (Excel)
        </a>
    </div>

    <!-- GST Return GSTR-1 Summary Banner (Section 19.6 in todo.txt) -->
    <div class="bg-gradient-to-r from-slate-900 to-blue-950 text-white rounded-2xl p-6 sm:p-8 shadow-lg border border-slate-800">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
            <div>
                <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-full">
                    GSTR-1 SUMMARY
                </span>
                <h3 class="text-xl font-bold text-white mt-1">Goods & Services Tax (GST) Liability Breakdown</h3>
                <p class="text-xs text-slate-400">Reconciled tax liability for selected period: {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}</p>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400 uppercase font-semibold">Total Revenue Invoiced</span>
                <div class="text-3xl font-mono font-black text-emerald-400">₹{{ number_format($salesGrand, 2) }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-slate-800/60 border border-slate-700">
                <span class="text-[11px] font-bold uppercase text-slate-400">Taxable Value (B2B + B2C)</span>
                <div class="text-xl font-mono font-bold text-white mt-1">₹{{ number_format($salesTaxable, 2) }}</div>
            </div>
            <div class="p-4 rounded-xl bg-slate-800/60 border border-slate-700">
                <span class="text-[11px] font-bold uppercase text-blue-400">Central Tax (CGST 9%)</span>
                <div class="text-xl font-mono font-bold text-white mt-1">₹{{ number_format($salesCgst, 2) }}</div>
            </div>
            <div class="p-4 rounded-xl bg-slate-800/60 border border-slate-700">
                <span class="text-[11px] font-bold uppercase text-blue-400">State Tax (SGST 9%)</span>
                <div class="text-xl font-mono font-bold text-white mt-1">₹{{ number_format($salesSgst, 2) }}</div>
            </div>
            <div class="p-4 rounded-xl bg-slate-800/60 border border-slate-700">
                <span class="text-[11px] font-bold uppercase text-emerald-400">Total Tax Payable (CGST+SGST)</span>
                <div class="text-xl font-mono font-bold text-emerald-300 mt-1">₹{{ number_format($salesCgst + $salesSgst, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Analytics Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Mechanic Productivity Leaderboard -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Mechanic Productivity</h3>
                    <p class="text-xs text-slate-500">Completed jobs and turnaround performance</p>
                </div>
                <a href="{{ route('mechanics.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">All Techs &rarr;</a>
            </div>

            <div class="space-y-3">
                @foreach($mechanicStats as $tech)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center">
                                {{ substr($tech->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-800">{{ $tech->name }}</div>
                                <div class="text-[10px] text-slate-500 font-mono">{{ $tech->employee->employee_code ?? 'EMP' }} • ₹{{ number_format($tech->employee->hourly_rate ?? 0) }}/hr</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-100 text-emerald-800">
                                {{ $tech->completed_jobs }} Jobs Completed
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Accounts Receivable Aging / Outstanding -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Customer Outstanding Ledger</h3>
                    <p class="text-xs text-slate-500">Uncollected balances from fleet and individual clients</p>
                </div>
                <a href="{{ route('customers.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">All Customers &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($receivables as $rcv)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                        <div>
                            <div class="font-bold text-slate-800">{{ $rcv->full_name }}</div>
                            <div class="text-[11px] text-slate-500">{{ $rcv->company_name ?? $rcv->phone }}</div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-mono font-bold text-rose-600">
                                ₹{{ number_format($rcv->outstanding_balance, 2) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-slate-400 italic">
                        No outstanding customer balances!
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sales Register Table for Period -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Sales Register for Selected Period</h3>
            <p class="text-xs text-slate-500">Individual tax invoices issued between {{ date('d M Y', strtotime($startDate)) }} and {{ date('d M Y', strtotime($endDate)) }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Invoice #</th>
                        <th class="px-6 py-3.5">Date</th>
                        <th class="px-6 py-3.5">Customer</th>
                        <th class="px-6 py-3.5">Vehicle</th>
                        <th class="px-6 py-3.5">Taxable (₹)</th>
                        <th class="px-6 py-3.5">CGST 9% (₹)</th>
                        <th class="px-6 py-3.5">SGST 9% (₹)</th>
                        <th class="px-6 py-3.5">Total Amount (₹)</th>
                        <th class="px-6 py-3.5">Payment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                <a href="{{ route('invoices.show', $inv->id) }}">{{ $inv->invoice_number }}</a>
                            </td>
                            <td class="px-6 py-4">{{ $inv->invoice_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $inv->customer->full_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-mono">{{ $inv->vehicle->registration_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-800">₹{{ number_format($inv->taxable_amount, 2) }}</td>
                            <td class="px-6 py-4 font-mono text-slate-600">₹{{ number_format($inv->cgst_amount, 2) }}</td>
                            <td class="px-6 py-4 font-mono text-slate-600">₹{{ number_format($inv->sgst_amount, 2) }}</td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900 text-sm">₹{{ number_format($inv->grand_total, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase {{ $inv->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $inv->payment_status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                No sales invoices issued within this date period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

