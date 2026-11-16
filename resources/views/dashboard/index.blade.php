@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title')
    @if($roleCategory === 'mechanic')
        Mechanic Workbench
    @elseif($roleCategory === 'inventory')
        Spare Parts & Inventory Hub
    @elseif($roleCategory === 'accountant')
        Financial & Indian GST Portal
    @elseif($roleCategory === 'advisor')
        Service Advisory Desk
    @else
        Workshop Operations Executive Dashboard
    @endif
@endsection

@section('content')
<div class="space-y-6">

    <!-- Greeting Banner with Role Context -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30">
                    {{ $user->role->name ?? ucfirst($roleCategory) }}
                </span>
                <span class="text-xs text-slate-400">• {{ auth()->user()->branch->name ?? 'Main Workshop' }}</span>
            </div>
            <h1 class="text-2xl font-bold mt-1 text-white">Welcome back, {{ $user->name }}</h1>
            <p class="text-xs text-slate-300 mt-1">
                @if($roleCategory === 'mechanic')
                    Here are your current repair assignments, lift bays, and job checklist.
                @elseif($roleCategory === 'inventory')
                    Monitor stock thresholds, incoming purchase orders, and parts issuance.
                @elseif($roleCategory === 'accountant')
                    Track daily cash/UPI collections, GST tax liabilities, and customer credit.
                @elseif($roleCategory === 'advisor')
                    Manage today's vehicle check-ins, repair estimates, and delivery gate passes.
                @else
                    Full overview of garage throughput, bay utilization, and financial performance.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-300 bg-slate-800/80 px-3 py-1.5 rounded-lg border border-slate-700 flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-emerald-400"></i>
                <span>{{ now()->format('l, d M Y') }}</span>
            </span>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- 1. MECHANIC WORKBENCH VIEW                                --}}
    {{-- ========================================================= --}}
    @if($roleCategory === 'mechanic')
        <!-- Mechanic KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">My Active Jobs</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-blue-600">{{ $myAssignedJobCards->count() }}</span>
                        <span class="text-xs text-slate-500">In Progress</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="wrench" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Completed This Month</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-emerald-600">{{ $myCompletedThisMonth }}</span>
                        <span class="text-xs text-slate-500">Delivered</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending QC Inspection</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-amber-600">{{ $myPendingQc }}</span>
                        <span class="text-xs text-slate-500">Awaiting Sign-off</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="shield-alert" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">My Assigned Bay</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-lg font-bold text-slate-900 truncate">{{ $myAssignedBay ? $myAssignedBay->name : 'Lift Station 1' }}</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i data-lucide="layout-grid" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Mechanic Active Tasks Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="text-base font-bold text-slate-900">My Assigned Repair Work Orders</h3>
                    <p class="text-xs text-slate-500">Vehicles currently on the lift or assigned to your bay</p>
                </div>
                <a href="{{ route('workshop.board') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    Live Floor Kanban <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-100/70 text-xs uppercase text-slate-700 font-semibold border-b">
                        <tr>
                            <th class="p-4">Job Card</th>
                            <th class="p-4">Vehicle Details</th>
                            <th class="p-4">Customer Complaints</th>
                            <th class="p-4">Assigned Bay</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Quick Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($myAssignedJobCards as $jc)
                        <tr class="hover:bg-slate-50">
                            <td class="p-4">
                                <a href="{{ route('job-cards.show', $jc->id) }}" class="font-mono font-bold text-blue-600 hover:underline">
                                    {{ $jc->job_card_number }}
                                </a>
                                <div class="text-[11px] text-slate-400">{{ $jc->created_at->format('d M, h:i A') }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">{{ $jc->vehicle->registration_number ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-500">{{ $jc->vehicle->brand->name ?? '' }} {{ $jc->vehicle->model->name ?? '' }}</div>
                            </td>
                            <td class="p-4 max-w-xs">
                                <p class="text-xs text-slate-700 line-clamp-2">{{ $jc->customer_complaints ?? 'Routine general maintenance.' }}</p>
                            </td>
                            <td class="p-4 text-xs font-medium text-slate-800">
                                <span class="px-2 py-1 bg-slate-100 rounded text-slate-700">
                                    {{ $jc->serviceBay->name ?? 'Unassigned Bay' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                    {{ $jc->status === 'work_in_progress' ? 'bg-amber-100 text-amber-800' : 
                                       ($jc->status === 'quality_check' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucwords(str_replace('_', ' ', $jc->status)) }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('job-cards.show', $jc->id) }}" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition">
                                        Open Job Card
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                <i data-lucide="check-circle" class="w-8 h-8 mx-auto text-emerald-400 mb-2"></i>
                                <p>No pending jobs assigned right now. Check the Workshop Floor Board to pick up new work orders.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    {{-- ========================================================= --}}
    {{-- 2. INVENTORY MANAGER VIEW                                 --}}
    {{-- ========================================================= --}}
    @elseif($roleCategory === 'inventory')
        <!-- Inventory KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Low Stock Alerts</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-rose-600">{{ $lowStockParts->count() }}</span>
                        <span class="text-xs text-rose-500 font-semibold">Need Reorder</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Stock Valuation</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-emerald-600">₹{{ number_format($totalInventoryValuation, 2) }}</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="boxes" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending Part Requests</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-amber-600">{{ $unissuedPartRequests->count() }}</span>
                        <span class="text-xs text-slate-500">From Workshop</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Open Purchase Orders</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-blue-600">{{ $openPurchaseOrdersCount }}</span>
                        <span class="text-xs text-slate-500">Awaiting Delivery</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="truck" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Critical Low Stock Alerts -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="alert-octagon" class="w-4 h-4 text-rose-500"></i>
                        <span>Critical Low Stock Items (Reorder Required)</span>
                    </h3>
                    <a href="{{ route('purchases.create') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                        + New PO
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($lowStockParts->take(6) as $part)
                    <div class="p-3 bg-rose-50/50 rounded-lg border border-rose-100 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-slate-800">{{ $part->name }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $part->part_number }} • HSN: {{ $part->hsn_code ?? '8708' }}</div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-rose-700">{{ $part->current_stock }} {{ $part->unit ?? 'pcs' }}</span>
                            <span class="text-[10px] text-slate-500 block">Min: {{ $part->min_stock_alert }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-4 text-center">All parts are well above minimum threshold.</p>
                    @endforelse
                </div>
            </div>

            <!-- Pending Parts Requisitions from Workshop -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="wrench" class="w-4 h-4 text-amber-500"></i>
                        <span>Parts Requisitions From Job Cards</span>
                    </h3>
                    <span class="text-xs bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-full">{{ $unissuedPartRequests->count() }} Pending</span>
                </div>
                <div class="space-y-3">
                    @forelse($unissuedPartRequests as $req)
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-slate-900">{{ $req->part_name }}</div>
                            <div class="text-[11px] text-slate-500">
                                Job #{{ $req->jobCard->job_card_number ?? 'JC' }} • Vehicle: {{ $req->jobCard->vehicle->registration_number ?? '' }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-slate-800">{{ $req->quantity }} pcs</span>
                            <form action="{{ route('job-cards.issue-part', $req->job_card_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="spare_part_id" value="{{ $req->spare_part_id }}">
                                <input type="hidden" name="quantity" value="{{ $req->quantity }}">
                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white rounded text-[11px] font-semibold hover:bg-emerald-700">
                                    Issue
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No unissued part requests right now.</p>
                    @endforelse
                </div>
            </div>
        </div>

    {{-- ========================================================= --}}
    {{-- 3. ACCOUNTANT & CASHIER VIEW                              --}}
    {{-- ========================================================= --}}
    @elseif($roleCategory === 'accountant')
        <!-- Accountant KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Today's Collections</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-emerald-600">₹{{ number_format($todayCollections, 2) }}</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="indian-rupee" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Month Invoiced Total</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-slate-900">₹{{ number_format($monthInvoicedTotal, 2) }}</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="receipt" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Outstanding Receivables</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-rose-600">₹{{ number_format($outstandingReceivables, 2) }}</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="credit-card" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">GST Output Tax Liability</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-purple-600">₹{{ number_format($monthlyCgst + $monthlySgst + $monthlyIgst, 2) }}</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Indian GST Liability Card -->
        <div class="bg-indigo-900 text-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-indigo-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-800 flex items-center justify-center text-amber-400">
                        <i data-lucide="landmark" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm">Indian GST Output Tax Breakdown (Current Month)</h3>
                        <p class="text-xs text-indigo-300">Compliant with GSTR-1 & Electronic Cash Ledger requirements</p>
                    </div>
                </div>
                <a href="{{ route('reports.export-sales-csv') }}" class="px-3 py-1.5 bg-indigo-700 hover:bg-indigo-600 rounded-lg text-xs font-semibold flex items-center gap-2">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i> Export GSTR-1 CSV
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                <div class="bg-indigo-800/60 p-4 rounded-xl border border-indigo-700/60">
                    <span class="text-xs text-indigo-300 uppercase font-semibold">Intra-State CGST (9%)</span>
                    <p class="text-xl font-black mt-1">₹{{ number_format($monthlyCgst, 2) }}</p>
                </div>
                <div class="bg-indigo-800/60 p-4 rounded-xl border border-indigo-700/60">
                    <span class="text-xs text-indigo-300 uppercase font-semibold">Intra-State SGST (9%)</span>
                    <p class="text-xl font-black mt-1">₹{{ number_format($monthlySgst, 2) }}</p>
                </div>
                <div class="bg-indigo-800/60 p-4 rounded-xl border border-indigo-700/60">
                    <span class="text-xs text-indigo-300 uppercase font-semibold">Inter-State IGST (18%)</span>
                    <p class="text-xl font-black mt-1">₹{{ number_format($monthlyIgst, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Unpaid Invoices & Recent Collections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center justify-between">
                    <span>Unpaid & Overdue Invoices</span>
                    <a href="{{ route('invoices.index') }}" class="text-xs text-blue-600 font-semibold">View All</a>
                </h3>
                <div class="space-y-3">
                    @forelse($unpaidInvoices as $inv)
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center justify-between text-xs">
                        <div>
                            <a href="{{ route('invoices.show', $inv->id) }}" class="font-bold text-blue-600 hover:underline">
                                {{ $inv->invoice_number }}
                            </a>
                            <div class="text-[11px] text-slate-600">{{ $inv->customer->full_name ?? 'Customer' }} • {{ $inv->vehicle->registration_number ?? '' }}</div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-rose-600 block">Due: ₹{{ number_format($inv->balance_due, 2) }}</span>
                            <a href="{{ route('payments.index') }}" class="text-[11px] text-blue-600 font-semibold hover:underline">Collect Payment</a>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No outstanding unpaid invoices!</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center justify-between">
                    <span>Recent Payment Receipts</span>
                    <a href="{{ route('payments.index') }}" class="text-xs text-blue-600 font-semibold">View All</a>
                </h3>
                <div class="space-y-3">
                    @forelse($recentPayments as $pmt)
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-900">{{ $pmt->payment_number }}</span>
                            <div class="text-[11px] text-slate-500">{{ $pmt->customer->full_name ?? '' }} • Method: <span class="uppercase font-semibold">{{ $pmt->payment_method }}</span></div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-emerald-600 block">+ ₹{{ number_format($pmt->amount, 2) }}</span>
                            <a href="{{ route('payments.receipt', $pmt->id) }}" target="_blank" class="text-[11px] text-slate-500 hover:text-blue-600">Print Receipt</a>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No payment transactions recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

    {{-- ========================================================= --}}
    {{-- 4. SERVICE ADVISOR VIEW                                   --}}
    {{-- ========================================================= --}}
    @elseif($roleCategory === 'advisor')
        <!-- Advisor KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Today's Appointments</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-blue-600">{{ $todayAppointmentsList->count() }}</span>
                        <span class="text-xs text-slate-500">Booked</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Active In-Floor Jobs</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-amber-600">{{ $activeJobCards }}</span>
                        <span class="text-xs text-slate-500">In Workshop</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="wrench" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Estimates Pending</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-purple-600">{{ $pendingEstimatesList->count() }}</span>
                        <span class="text-xs text-slate-500">Need Approval</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i data-lucide="calculator" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ready for Delivery</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-2xl font-bold text-emerald-600">{{ $readyDeliveriesList->count() }}</span>
                        <span class="text-xs text-slate-500">Gate Pass Ready</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="car" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Ready for Delivery Banner -->
        @if($readyDeliveriesList->count() > 0)
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
            <h3 class="text-sm font-bold text-emerald-900 mb-2 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                <span>Vehicles Ready for Delivery & Security Gate Pass</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mt-3">
                @foreach($readyDeliveriesList as $rd)
                <div class="bg-white p-3.5 rounded-lg border border-emerald-200 shadow-sm flex items-center justify-between text-xs">
                    <div>
                        <div class="font-bold text-slate-900">{{ $rd->vehicle->registration_number ?? 'Reg No' }}</div>
                        <div class="text-slate-500">{{ $rd->customer->full_name ?? 'Customer' }}</div>
                        <span class="font-mono text-[10px] text-blue-600 font-bold">{{ $rd->gate_pass_number }}</span>
                    </div>
                    <a href="{{ route('job-cards.gate-pass', $rd->id) }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 text-white rounded text-xs font-semibold hover:bg-emerald-700 flex items-center gap-1">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i> Gate Pass
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Appointments & Estimates Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Today's Appointments -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                        <span>Today's Booked Appointments</span>
                    </h3>
                    <a href="{{ route('appointments.create') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">+ New Appointment</a>
                </div>
                <div class="space-y-3">
                    @forelse($todayAppointmentsList as $apt)
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-slate-900">{{ $apt->customer->full_name ?? 'Customer' }} ({{ $apt->customer->phone ?? '' }})</div>
                            <div class="text-[11px] text-slate-500">{{ $apt->vehicle->registration_number ?? 'Vehicle' }} • Time: {{ $apt->appointment_time }}</div>
                        </div>
                        <a href="{{ route('inspections.create', ['customer_id' => $apt->customer_id, 'vehicle_id' => $apt->vehicle_id]) }}" class="px-2.5 py-1 bg-blue-600 text-white rounded font-semibold text-[11px] hover:bg-blue-700">
                            Check-In
                        </a>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No more appointments scheduled for today.</p>
                    @endforelse
                </div>
            </div>

            <!-- Pending Estimates -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-purple-600"></i>
                        <span>Estimates Pending Customer Approval</span>
                    </h3>
                    <a href="{{ route('estimates.create') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">+ New Estimate</a>
                </div>
                <div class="space-y-3">
                    @forelse($pendingEstimatesList as $est)
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center justify-between text-xs">
                        <div>
                            <a href="{{ route('estimates.show', $est->id) }}" class="font-bold text-purple-700 hover:underline">
                                {{ $est->estimate_number }}
                            </a>
                            <div class="text-[11px] text-slate-500">{{ $est->customer->full_name ?? '' }} • Total: ₹{{ number_format($est->grand_total, 2) }}</div>
                        </div>
                        <a href="{{ route('estimates.show', $est->id) }}" class="px-2.5 py-1 bg-purple-600 text-white rounded font-semibold text-[11px] hover:bg-purple-700">
                            Review & Convert
                        </a>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-4 text-center">All repair estimates approved!</p>
                    @endforelse
                </div>
            </div>
        </div>

    {{-- ========================================================= --}}
    {{-- 5. EXECUTIVE / MANAGEMENT VIEW (Admin & Manager)          --}}
    {{-- ========================================================= --}}
    @else
        <!-- Top 360° Executive KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
                    <p class="text-xs text-slate-500">Real-time status of lift stations and diagnostic bays</p>
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
                            <div class="pt-2 border-t border-slate-200/40 text-[11px] text-slate-400 italic">
                                Ready for vehicle
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Workflow Pipeline & Recent Job Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Recent Job Cards</h3>
                    <a href="{{ route('job-cards.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-semibold border-b">
                            <tr>
                                <th class="py-2.5 px-3">Job #</th>
                                <th class="py-2.5 px-3">Vehicle</th>
                                <th class="py-2.5 px-3">Customer</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentJobCards as $jc)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-3 font-mono font-bold text-blue-600">{{ $jc->job_card_number }}</td>
                                <td class="py-3 px-3 font-semibold text-slate-800">{{ $jc->vehicle->registration_number ?? 'N/A' }}</td>
                                <td class="py-3 px-3">{{ $jc->customer->full_name ?? 'N/A' }}</td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-slate-100 text-slate-800">
                                        {{ str_replace('_', ' ', $jc->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('job-cards.show', $jc->id) }}" class="text-blue-600 font-semibold hover:underline">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pipeline Distribution Chart -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-2">Job Card Pipeline</h3>
                <p class="text-xs text-slate-500 mb-4">Stage distribution of all active workshop orders</p>
                <div class="relative h-64">
                    <canvas id="jobPipelineChart"></canvas>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('jobPipelineChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_keys($jobStatusCounts)) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($jobStatusCounts)) !!},
                        backgroundColor: [
                            '#94a3b8',
                            '#60a5fa',
                            '#f59e0b',
                            '#a855f7',
                            '#10b981',
                            '#64748b'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11 }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    });
</script>
@endpush
