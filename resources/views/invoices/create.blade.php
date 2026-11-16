@extends('layouts.app')

@section('title', 'Create Invoice')
@section('page_title', 'Generate GST Tax Invoice')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('invoices.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Invoices
        </a>
    </div>

    <!-- Option 1: Generate directly from Job Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold">1</div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Generate from Completed Job Card (Recommended)</h3>
                <p class="text-xs text-slate-500">Auto-pulls all labour services, allocated parts, mechanic logs and GST rates</p>
            </div>
        </div>

        <form method="POST" action="{{ route('invoices.store') }}" class="flex flex-col sm:flex-row items-end gap-3">
            @csrf
            <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Select Ready Job Card *</label>
                <select name="job_card_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choose Job Card --</option>
                    @foreach($jobCards as $jc)
                        <option value="{{ $jc->id }}">
                            {{ $jc->job_card_number }} - {{ $jc->vehicle->registration_number ?? 'Vehicle' }} ({{ $jc->customer->full_name ?? 'Customer' }} - ₹{{ number_format($jc->final_grand_total, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5 shrink-0">
                <i data-lucide="receipt" class="w-4 h-4"></i> Generate Full Invoice
            </button>
        </form>
    </div>

    <!-- Option 2: Quick Over-the-counter Direct Tax Invoice -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold">2</div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Direct Over-The-Counter Tax Invoice</h3>
                <p class="text-xs text-slate-500">For direct parts retail, express wash, or counter billing</p>
            </div>
        </div>

        <form method="POST" action="{{ route('invoices.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Customer *</label>
                    <select name="customer_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Choose Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->full_name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Vehicle *</label>
                    <select name="vehicle_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Choose Vehicle --</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->registration_number }} - {{ $v->brand->name ?? '' }} {{ $v->model->name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Service / Item Description *</label>
                    <input type="text" name="item_name" required placeholder="e.g. Express Foam Wash & Sanitization" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Amount (Excl. Tax) ₹ *</label>
                    <input type="number" step="0.01" name="amount" required value="1000" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">GST Rate (%) *</label>
                    <select name="gst_rate" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none font-bold">
                        <option value="18.00" selected>18% (9% CGST + 9% SGST)</option>
                        <option value="28.00">28% (14% CGST + 14% SGST)</option>
                        <option value="12.00">12%</option>
                        <option value="5.00">5%</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Create Direct Invoice
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

