@extends('layouts.app')

@section('title', 'New Purchase Order')
@section('page_title', 'Create Purchase Order (Procurement)')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('purchases.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to POs
        </a>
    </div>

    <form method="POST" action="{{ route('purchases.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Select Supplier *</label>
                <select name="supplier_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choose Vendor / Supplier --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->city }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Order Date *</label>
                <input type="date" name="order_date" required value="{{ today()->toDateString() }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div class="border-t border-slate-100 pt-4">
            <h4 class="text-xs font-bold uppercase text-slate-700 tracking-wider mb-3">Item To Procure</h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Spare Part SKU *</label>
                    <select name="spare_part_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Choose Spare Part --</option>
                        @foreach($parts as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->part_number }}) - Cost: ₹{{ number_format($p->purchase_price) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Quantity *</label>
                    <input type="number" name="quantity" min="1" value="10" required class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>
            </div>

            <div class="mt-4 sm:w-1/3">
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Agreed Unit Cost (₹) *</label>
                <input type="number" step="0.01" name="unit_price" value="450" required class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Place Purchase Order
            </button>
        </div>
    </form>
</div>
@endsection

