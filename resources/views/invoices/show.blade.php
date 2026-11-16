@extends('layouts.app')

@section('title', $invoice->invoice_number)
@section('page_title', 'Tax Invoice: ' . $invoice->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('invoices.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Invoices
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i> Print GST Tax Invoice
            </a>
            @if($invoice->balance_due > 0)
                <button @click="$dispatch('open-payment-modal')" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                    <i data-lucide="credit-card" class="w-4 h-4"></i> Collect Payment
                </button>
            @endif
        </div>
    </div>

    <!-- Main Tax Invoice Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-6">
        <!-- Tax Invoice Header -->
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 border-b border-slate-200 pb-6">
            <div>
                <span class="text-xs font-bold uppercase text-blue-600 tracking-wider">TAX INVOICE (GST COMPLIANT)</span>
                <h2 class="text-2xl font-black text-slate-900 mt-1">{{ $invoice->company->name ?? 'ABC Automobile Care & Service Centre' }}</h2>
                <div class="text-xs text-slate-500 mt-1">
                    {{ $invoice->company->address ?? 'Plot 42, Sector 18, Automotive Zone' }}, {{ $invoice->company->city ?? 'Mumbai' }}, {{ $invoice->company->state ?? 'Maharashtra' }} - {{ $invoice->company->postal_code ?? '400705' }}
                </div>
                <div class="text-xs font-mono font-bold text-slate-800 mt-1">
                    GSTIN: {{ $invoice->company->gstin ?? '27AABCU9603R1ZM' }} • PAN: {{ $invoice->company->pan_number ?? 'AABCU9603R' }}
                </div>
            </div>

            <div class="text-right text-xs">
                <div class="text-lg font-mono font-bold text-blue-600">{{ $invoice->invoice_number }}</div>
                <div class="text-slate-500 mt-1">Date: <strong>{{ $invoice->invoice_date->format('d M Y') }}</strong></div>
                @if($invoice->jobCard)
                    <div class="text-slate-500 mt-0.5">Job Card: <a href="{{ route('job-cards.show', $invoice->job_card_id) }}" class="font-mono font-bold text-blue-600 hover:underline">{{ $invoice->jobCard->job_card_number }}</a></div>
                @endif
                <div class="mt-2">
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $invoice->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $invoice->payment_status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Bill To & Vehicle Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 bg-slate-50 rounded-xl text-xs">
            <div>
                <span class="font-bold uppercase text-slate-400 tracking-wider">Billed To (Customer)</span>
                <div class="font-bold text-slate-900 text-sm mt-1">{{ $invoice->customer->full_name ?? 'N/A' }}</div>
                @if($invoice->customer->company_name)
                    <div class="text-slate-600 font-semibold">{{ $invoice->customer->company_name }}</div>
                @endif
                <div class="text-slate-600 mt-0.5">{{ $invoice->customer->phone }} • {{ $invoice->customer->email ?? '' }}</div>
                <div class="text-slate-500 mt-0.5">{{ $invoice->customer->address ?? '' }}, {{ $invoice->customer->city ?? '' }}</div>
                @if($invoice->customer->gstin)
                    <div class="font-mono font-bold text-slate-800 mt-1">Customer GSTIN: {{ $invoice->customer->gstin }}</div>
                @endif
            </div>

            <div>
                <span class="font-bold uppercase text-slate-400 tracking-wider">Vehicle Serviced</span>
                <div class="font-mono font-bold text-slate-900 text-base mt-1">{{ $invoice->vehicle->registration_number ?? 'N/A' }}</div>
                <div class="text-slate-700 font-semibold">{{ $invoice->vehicle->brand->name ?? '' }} {{ $invoice->vehicle->model->name ?? '' }}</div>
                <div class="text-slate-500">Fuel: {{ $invoice->vehicle->fuel_type }} • Year: {{ $invoice->vehicle->model_year ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Invoice Items Table with Indian GST Breakup -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">HSN/SAC</th>
                        <th class="px-4 py-3">Qty</th>
                        <th class="px-4 py-3">Rate (₹)</th>
                        <th class="px-4 py-3">Taxable (₹)</th>
                        <th class="px-4 py-3">CGST (₹)</th>
                        <th class="px-4 py-3">SGST (₹)</th>
                        <th class="px-4 py-3 text-right">Total (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($invoice->items as $idx => $it)
                        <tr>
                            <td class="px-4 py-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $it->name }}</div>
                                <span class="text-[10px] uppercase text-slate-400 font-semibold">{{ $it->item_type }}</span>
                            </td>
                            <td class="px-4 py-3 font-mono text-[11px] text-slate-600">{{ $it->hsn_sac ?? '998729' }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-800">{{ $it->quantity }}</td>
                            <td class="px-4 py-3 font-mono">₹{{ number_format($it->unit_price, 2) }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-800">₹{{ number_format($it->taxable_value, 2) }}</td>
                            <td class="px-4 py-3 font-mono text-slate-600">
                                ₹{{ number_format($it->cgst_amount, 2) }} <span class="text-[9px] text-slate-400">({{ $it->gst_rate / 2 }}%)</span>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600">
                                ₹{{ number_format($it->sgst_amount, 2) }} <span class="text-[9px] text-slate-400">({{ $it->gst_rate / 2 }}%)</span>
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-900 text-right">₹{{ number_format($it->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Balances -->
        <div class="flex flex-col sm:flex-row justify-between gap-6 pt-4 border-t border-slate-200">
            <div class="text-xs text-slate-500 space-y-1">
                <div class="font-bold uppercase tracking-wider text-slate-700">Bank Details for Payment</div>
                <div>Bank: <strong>HDFC Bank Ltd</strong></div>
                <div>Account Name: <strong>ABC Auto Care Solutions Pvt Ltd</strong></div>
                <div>Account Number: <strong>50200012345678</strong></div>
                <div>IFSC Code: <strong>HDFC0000123</strong></div>
                <div>UPI ID: <strong>abcautocare@hdfcbank</strong></div>
            </div>

            <div class="w-80 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Taxable Value:</span>
                    <span class="font-mono font-bold text-slate-800">₹{{ number_format($invoice->taxable_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Central GST (CGST 9%):</span>
                    <span class="font-mono font-bold text-slate-800">₹{{ number_format($invoice->cgst_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>State GST (SGST 9%):</span>
                    <span class="font-mono font-bold text-slate-800">₹{{ number_format($invoice->sgst_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-800 font-bold pt-2 border-t border-slate-200 text-sm">
                    <span>Grand Total:</span>
                    <span class="font-mono text-base text-slate-900">₹{{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-600 font-bold">
                    <span>Paid Amount:</span>
                    <span class="font-mono">₹{{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-black pt-1 border-t border-slate-200 {{ $invoice->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    <span>Balance Due:</span>
                    <span class="font-mono">₹{{ number_format($invoice->balance_due, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payments Log Table -->
        @if($invoice->payments->count() > 0)
            <div class="pt-6 border-t border-slate-200">
                <h4 class="text-xs font-bold uppercase text-slate-800 tracking-wider mb-3">Recorded Payment Receipts</h4>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[10px] uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-2.5">Receipt #</th>
                                <th class="px-4 py-2.5">Payment Date</th>
                                <th class="px-4 py-2.5">Method</th>
                                <th class="px-4 py-2.5">Ref #</th>
                                <th class="px-4 py-2.5">Received By</th>
                                <th class="px-4 py-2.5 text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($invoice->payments as $pmt)
                                <tr>
                                    <td class="px-4 py-2.5 font-mono font-bold text-blue-600">
                                        <a href="{{ route('payments.receipt', $pmt->id) }}" target="_blank">{{ $pmt->payment_number }}</a>
                                    </td>
                                    <td class="px-4 py-2.5">{{ $pmt->payment_date->format('d M Y') }}</td>
                                    <td class="px-4 py-2.5 uppercase font-bold text-[10px] text-slate-700">{{ $pmt->payment_method }}</td>
                                    <td class="px-4 py-2.5 font-mono text-slate-500">{{ $pmt->transaction_reference ?? '-' }}</td>
                                    <td class="px-4 py-2.5 text-slate-600">{{ $pmt->receiver->name ?? 'Cashier' }}</td>
                                    <td class="px-4 py-2.5 font-mono font-bold text-emerald-600 text-right">₹{{ number_format($pmt->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Collect Payment Form (if balance due) -->
    @if($invoice->balance_due > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <h3 class="text-base font-bold text-slate-900 mb-1">Record Counter Payment</h3>
            <p class="text-xs text-slate-500 mb-4">Accept cash, UPI (GPay/PhonePe), credit card, or bank transfer</p>

            <form method="POST" action="{{ route('payments.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Payment Amount (₹) *</label>
                    <input type="number" step="0.01" name="amount" required max="{{ $invoice->balance_due }}" value="{{ $invoice->balance_due }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Payment Mode *</label>
                    <select name="payment_method" required class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="upi">UPI / QR Code</option>
                        <option value="card">Credit / Debit Card (POS)</option>
                        <option value="cash">Cash Counter</option>
                        <option value="net_banking">Net Banking / NEFT</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Transaction Ref # (UTR)</label>
                    <input type="text" name="transaction_reference" placeholder="e.g. UPI-12345678" class="w-full px-3 py-2 text-xs font-mono rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <div>
                    <button type="submit" class="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center justify-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i> Receive Payment
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection

