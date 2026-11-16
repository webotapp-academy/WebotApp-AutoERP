@extends('layouts.app')

@section('title', 'Edit ' . $sparePart->name)
@section('page_title', 'Edit Part: ' . $sparePart->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Inventory
        </a>
        <a href="{{ route('inventory.ledger', $sparePart->id) }}" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
            <i data-lucide="history" class="w-4 h-4"></i> View Movement Ledger
        </a>
    </div>

    <!-- Edit Catalog Form -->
    <form method="POST" action="{{ route('inventory.update', $sparePart->id) }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Part Name *</label>
                <input type="text" name="name" required value="{{ old('name', $sparePart->name) }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Category *</label>
                <select name="category_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $sparePart->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Part Number *</label>
                <input type="text" name="part_number" required value="{{ old('part_number', $sparePart->part_number) }}" class="w-full px-3 py-2 text-xs font-mono font-bold uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $sparePart->sku) }}" class="w-full px-3 py-2 text-xs font-mono uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Brand</label>
                <input type="text" name="brand" value="{{ old('brand', $sparePart->brand) }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Purchase Cost (₹) *</label>
                <input type="number" step="0.01" name="purchase_price" required value="{{ old('purchase_price', $sparePart->purchase_price) }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Selling Price (₹) *</label>
                <input type="number" step="0.01" name="selling_price" required value="{{ old('selling_price', $sparePart->selling_price) }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">MRP (₹) *</label>
                <input type="number" step="0.01" name="mrp" required value="{{ old('mrp', $sparePart->mrp) }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Unit *</label>
                <input type="text" name="unit" required value="{{ old('unit', $sparePart->unit) }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Min Stock Warning *</label>
                <input type="number" name="min_stock_alert" required value="{{ old('min_stock_alert', $sparePart->min_stock_alert) }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Rack / Bin Location</label>
                <input type="text" name="bin_location" value="{{ old('bin_location', $sparePart->bin_location) }}" class="w-full px-3 py-2 text-xs font-mono uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">GST Rate (%) *</label>
                <select name="gst_rate" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none font-bold">
                    <option value="18.00" {{ $sparePart->gst_rate == 18 ? 'selected' : '' }}>18%</option>
                    <option value="28.00" {{ $sparePart->gst_rate == 28 ? 'selected' : '' }}>28%</option>
                    <option value="12.00" {{ $sparePart->gst_rate == 12 ? 'selected' : '' }}>12%</option>
                    <option value="5.00" {{ $sparePart->gst_rate == 5 ? 'selected' : '' }}>5%</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('inventory.index') }}" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Update Part
            </button>
        </div>
    </form>

    <!-- Stock Adjustment Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h3 class="text-base font-bold text-slate-900 mb-1">Physical Stock Audit & Adjustment</h3>
        <p class="text-xs text-slate-500 mb-4">Current recorded stock balance is: <strong class="text-slate-900 font-mono text-sm">{{ $sparePart->current_stock }} {{ $sparePart->unit }}</strong></p>

        <form method="POST" action="{{ route('inventory.adjust', $sparePart->id) }}" class="flex flex-col sm:flex-row items-end gap-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">New Physical Count *</label>
                <input type="number" name="new_stock" required value="{{ $sparePart->current_stock }}" class="w-32 px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Reason for Adjustment *</label>
                <input type="text" name="reason" required placeholder="e.g. Physical inventory audit discrepancy, damaged seal write-off..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition shrink-0">
                Apply Stock Adjustment
            </button>
        </form>
    </div>
</div>
@endsection
