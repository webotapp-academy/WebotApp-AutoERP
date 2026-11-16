@extends('layouts.app')

@section('title', $jobCard->job_card_number)
@section('page_title', 'Job Card: ' . $jobCard->job_card_number)

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('job-cards.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Job Cards
        </a>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Print Work Order -->
            <a href="{{ route('job-cards.print-work-order', $jobCard->id) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="printer" class="w-4 h-4"></i> Print Floor Work Order
            </a>

            <!-- Print Gate Pass (if ready or delivered) -->
            @if(in_array($jobCard->status, ['ready_for_delivery', 'delivered']))
                <a href="{{ route('job-cards.print-gate-pass', $jobCard->id) }}" target="_blank" class="px-3 py-1.5 bg-purple-50 text-purple-700 hover:bg-purple-100 text-xs font-bold rounded-lg transition flex items-center gap-1.5 border border-purple-200 shadow-sm">
                    <i data-lucide="shield-check" class="w-4 h-4"></i> Security Gate Pass
                </a>
            @endif

            <!-- Generate GST Invoice -->
            @if(!$jobCard->invoice && in_array($jobCard->status, ['work_in_progress', 'quality_check', 'ready_for_delivery', 'delivered']))
                <form method="POST" action="{{ route('job-cards.create-invoice', $jobCard->id) }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                        <i data-lucide="receipt" class="w-4 h-4"></i> Generate GST Invoice
                    </button>
                </form>
            @elseif($jobCard->invoice)
                <a href="{{ route('invoices.show', $jobCard->invoice->id) }}" class="px-3.5 py-1.5 bg-emerald-100 text-emerald-800 hover:bg-emerald-200 text-xs font-bold rounded-lg transition flex items-center gap-1.5 border border-emerald-300">
                    <i data-lucide="file-check" class="w-4 h-4"></i> View Invoice #{{ $jobCard->invoice->invoice_number }}
                </a>
            @endif
        </div>
    </div>

    <!-- Lifecycle Status Tracker Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Current Lifecycle Status</span>
                <div class="flex items-center gap-3 mt-1">
                    <h2 class="text-xl font-mono font-black text-slate-900">{{ $jobCard->job_card_number }}</h2>
                    @php
                        $badgeClass = match($jobCard->status) {
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
                    <span class="px-3 py-1 text-xs font-bold rounded-full uppercase {{ $badgeClass }}">
                        {{ str_replace('_', ' ', $jobCard->status) }}
                    </span>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase {{ $jobCard->priority === 'urgent' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700' }}">
                        {{ $jobCard->priority }} Priority
                    </span>
                </div>
            </div>

            <!-- Transition Status Dropdown -->
            <form method="POST" action="{{ route('job-cards.update-status', $jobCard->id) }}" class="flex items-center gap-2">
                @csrf
                <select name="status" class="px-3 py-1.5 text-xs font-bold bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="" disabled selected>-- Transition Stage --</option>
                    @if($jobCard->status === 'draft')
                        <option value="pending_estimate">Send for Customer Estimate</option>
                        <option value="estimate_approved">Approve Estimate Directly</option>
                    @elseif($jobCard->status === 'pending_estimate')
                        <option value="estimate_approved">Mark Estimate Approved</option>
                    @elseif($jobCard->status === 'estimate_approved')
                        <option value="work_in_progress">Start Work on Floor</option>
                    @elseif($jobCard->status === 'work_in_progress')
                        <option value="quality_check">Send to Quality Control (QC)</option>
                    @elseif($jobCard->status === 'quality_check')
                        <option value="ready_for_delivery">Pass QC & Make Ready</option>
                        <option value="work_in_progress">Rework Required (Back to WIP)</option>
                    @elseif($jobCard->status === 'ready_for_delivery')
                        <option value="delivered">Customer Handover / Delivered</option>
                    @endif
                </select>
                <button type="submit" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition">
                    Update Status
                </button>
            </form>
        </div>

        <!-- Step Progress Bar -->
        @php
            $steps = ['draft', 'estimate_approved', 'work_in_progress', 'quality_check', 'ready_for_delivery', 'delivered'];
            $currentIndex = array_search($jobCard->status, $steps);
            if ($currentIndex === false) $currentIndex = 0;
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-6 gap-2 text-center text-xs">
            @foreach($steps as $idx => $step)
                <div class="p-2.5 rounded-xl border {{ $idx <= $currentIndex ? 'bg-blue-50/70 border-blue-300 text-blue-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <div class="text-[10px] font-mono mb-0.5">0{{ $idx + 1 }}</div>
                    <div class="truncate capitalize">{{ str_replace('_', ' ', $step) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Vehicle, Customer & Station Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Vehicle Info -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-100">
                <span class="font-bold uppercase text-slate-400 tracking-wider">Vehicle Details</span>
                <a href="{{ route('vehicles.show', $jobCard->vehicle_id) }}" class="text-blue-600 hover:underline font-bold">History &rarr;</a>
            </div>
            <div>
                <div class="font-mono font-black text-slate-900 text-lg">{{ $jobCard->vehicle->registration_number ?? 'N/A' }}</div>
                <div class="text-xs font-bold text-slate-700">{{ $jobCard->vehicle->brand->name ?? '' }} {{ $jobCard->vehicle->model->name ?? '' }}</div>
                <div class="text-[11px] text-slate-500">{{ $jobCard->vehicle->fuel_type }} • {{ $jobCard->vehicle->transmission }} • {{ $jobCard->vehicle->color ?? 'Standard' }}</div>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                <span>Odometer In: <strong class="text-slate-900 font-mono">{{ number_format($jobCard->odometer_in) }} km</strong></span>
                <span>Fuel: <strong class="text-slate-900">{{ $jobCard->fuel_level }}</strong></span>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-100">
                <span class="font-bold uppercase text-slate-400 tracking-wider">Customer Details</span>
                <a href="{{ route('customers.show', $jobCard->customer_id) }}" class="text-blue-600 hover:underline font-bold">Profile &rarr;</a>
            </div>
            <div>
                <div class="font-bold text-slate-900 text-base">{{ $jobCard->customer->full_name ?? 'N/A' }}</div>
                <div class="text-xs font-semibold text-slate-700 mt-0.5">{{ $jobCard->customer->phone ?? '' }}</div>
                <div class="text-[11px] text-slate-500">{{ $jobCard->customer->email ?? 'No email' }}</div>
            </div>
            <div class="pt-2 border-t border-slate-100 text-xs text-slate-600">
                <span>Balance: <strong class="{{ $jobCard->customer->outstanding_balance > 0 ? 'text-rose-600' : 'text-emerald-600' }} font-bold">₹{{ number_format($jobCard->customer->outstanding_balance, 2) }}</strong></span>
            </div>
        </div>

        <!-- Workshop Assignments -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="text-xs font-bold uppercase text-slate-400 tracking-wider pb-2 border-b border-slate-100">
                Workshop Station & Staff
            </div>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="text-slate-400">Assigned Lift Station:</span>
                    <div class="font-bold text-slate-900">{{ $jobCard->serviceBay->name ?? 'Workshop Floor (No Lift)' }}</div>
                </div>
                <div>
                    <span class="text-slate-400">Lead Master Mechanic:</span>
                    <div class="font-bold text-slate-900">{{ $jobCard->leadMechanic->name ?? 'Unassigned' }}</div>
                </div>
                <div>
                    <span class="text-slate-400">Service Advisor:</span>
                    <div class="font-bold text-slate-900">{{ $jobCard->serviceAdvisor->name ?? 'Staff' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Complaints & Diagnosis -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h4 class="text-xs font-bold uppercase text-slate-500 tracking-wider mb-2">Customer Complaints & Job Scope</h4>
            <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-800 whitespace-pre-line border border-slate-200 leading-relaxed font-medium">
                {{ $jobCard->customer_complaints }}
            </div>
        </div>
        <div>
            <h4 class="text-xs font-bold uppercase text-slate-500 tracking-wider mb-2">Technician Diagnosis & Inspection Findings</h4>
            <div class="p-3 bg-blue-50/50 rounded-xl text-xs text-slate-800 whitespace-pre-line border border-blue-100 leading-relaxed font-medium">
                {{ $jobCard->diagnosis_remarks ?? 'Pending complete diagnosis.' }}
            </div>
        </div>
    </div>

    <!-- Section 10 & 16: Labour Services List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Labour Services & Operations</h3>
                <p class="text-xs text-slate-500">Service operations performed by mechanics</p>
            </div>

            <!-- Add Service Form -->
            <form method="POST" action="{{ route('job-cards.add-service', $jobCard->id) }}" class="flex flex-wrap items-center gap-2">
                @csrf
                <select name="service_id" required class="px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Add Service Master --</option>
                    @foreach($allServices as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} (₹{{ number_format($s->standard_labour_cost) }})</option>
                    @endforeach
                </select>

                <input type="number" step="0.01" name="labour_charge" placeholder="Labour ₹" required value="500" class="w-24 px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg font-mono font-bold" />

                <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition flex items-center gap-1">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3">Service Name</th>
                        <th class="px-6 py-3">Mechanic</th>
                        <th class="px-6 py-3">Standard Hours</th>
                        <th class="px-6 py-3">Labour Charge</th>
                        <th class="px-6 py-3">GST Rate</th>
                        <th class="px-6 py-3">Tax Amount</th>
                        <th class="px-6 py-3">Total (₹)</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($jobCard->services as $srv)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-3.5 font-bold text-slate-800">
                                {{ $srv->service_name }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-700">
                                {{ $srv->mechanic->name ?? $jobCard->leadMechanic->name ?? 'General Staff' }}
                            </td>
                            <td class="px-6 py-3.5 font-mono">
                                {{ $srv->estimated_hours }} hrs
                            </td>
                            <td class="px-6 py-3.5 font-mono font-bold text-slate-800">
                                ₹{{ number_format($srv->labour_charge, 2) }}
                            </td>
                            <td class="px-6 py-3.5 font-mono">
                                {{ $srv->gst_rate }}%
                            </td>
                            <td class="px-6 py-3.5 font-mono text-slate-600">
                                ₹{{ number_format($srv->tax_amount, 2) }}
                            </td>
                            <td class="px-6 py-3.5 font-mono font-bold text-slate-900">
                                ₹{{ number_format($srv->total, 2) }}
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase {{ $srv->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $srv->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400">
                                No labour services attached yet. Use the dropdown above to add operations.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 14: Spare Parts Allocation & Inventory Issue -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Spare Parts & Materials Allocation</h3>
                <p class="text-xs text-slate-500">Live inventory deduction and stock issue tracking</p>
            </div>

            <!-- Allocate Part Form -->
            <form method="POST" action="{{ route('job-cards.add-part', $jobCard->id) }}" class="flex flex-wrap items-center gap-2">
                @csrf
                <select name="spare_part_id" required class="px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Allocate Spare Part --</option>
                    @foreach($allParts as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (In Stock: {{ $p->current_stock }} • ₹{{ number_format($p->selling_price) }})</option>
                    @endforeach
                </select>

                <input type="number" name="quantity" min="1" value="1" placeholder="Qty" required class="w-16 px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg font-mono font-bold" />

                <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition flex items-center gap-1">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Allocate
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3">Part Name</th>
                        <th class="px-6 py-3">Part #</th>
                        <th class="px-6 py-3">Qty</th>
                        <th class="px-6 py-3">Unit Price</th>
                        <th class="px-6 py-3">GST Rate</th>
                        <th class="px-6 py-3">Tax Amount</th>
                        <th class="px-6 py-3">Total Price</th>
                        <th class="px-6 py-3">Inventory Status</th>
                        <th class="px-6 py-3 text-right">Stock Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($jobCard->parts as $pt)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-3.5 font-bold text-slate-800">
                                {{ $pt->part_name }}
                            </td>
                            <td class="px-6 py-3.5 font-mono text-[11px] text-slate-500">
                                {{ $pt->part_number }}
                            </td>
                            <td class="px-6 py-3.5 font-mono font-bold text-slate-800">
                                {{ $pt->quantity }}
                            </td>
                            <td class="px-6 py-3.5 font-mono">
                                ₹{{ number_format($pt->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-3.5 font-mono">
                                {{ $pt->gst_rate }}%
                            </td>
                            <td class="px-6 py-3.5 font-mono text-slate-600">
                                ₹{{ number_format($pt->tax_amount, 2) }}
                            </td>
                            <td class="px-6 py-3.5 font-mono font-bold text-slate-900">
                                ₹{{ number_format($pt->total_price, 2) }}
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded uppercase {{ $pt->status === 'issued' ? 'bg-emerald-100 text-emerald-800' : ($pt->status === 'returned' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $pt->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                @if($pt->status === 'allocated' || $pt->status === 'requested')
                                    <form method="POST" action="{{ route('job-cards.issue-part', $pt->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-bold transition">
                                            Issue Part
                                        </button>
                                    </form>
                                @elseif($pt->status === 'issued')
                                    <form method="POST" action="{{ route('job-cards.return-part', $pt->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 rounded text-[11px] font-bold transition">
                                            Return Part
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-slate-400">
                                No spare parts allocated yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Final Financial Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-1 text-xs">
            <div class="font-bold uppercase text-slate-400 tracking-wider">Job Card Financials (GST Inclusive)</div>
            <div class="text-slate-600">Subtotal Labour: <strong class="text-slate-800">₹{{ number_format($jobCard->final_labour, 2) }}</strong></div>
            <div class="text-slate-600">Subtotal Spare Parts: <strong class="text-slate-800">₹{{ number_format($jobCard->final_parts, 2) }}</strong></div>
        </div>

        <div class="text-right">
            <span class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Total Estimated / Payable</span>
            <div class="text-3xl font-mono font-black text-slate-900 mt-1">
                ₹{{ number_format($jobCard->final_grand_total, 2) }}
            </div>
            <div class="text-[11px] text-slate-500">Includes applicable GST (CGST + SGST)</div>
        </div>
    </div>

</div>
@endsection

