@extends('layouts.app')

@section('title', 'Add Spare Part')
@section('page_title', 'Add New Spare Part SKU')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Inventory
        </a>
    </div>

    <form method="POST" action="{{ route('inventory.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Part Name / Description *</label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Synthetic Engine Oil 5W-30" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Category *</label>
                <select name="category_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Part Number (OEM / Aftermarket) *</label>
                <input type="text" name="part_number" required value="{{ old('part_number') }}" placeholder="PART-IN-1234" class="w-full px-3 py-2 text-xs font-mono font-bold uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                @error('part_number') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">SKU / Barcode</label>
                <input type="text" name="sku" value="{{ old('sku') }}" placeholder="SKU-890123" class="w-full px-3 py-2 text-xs font-mono uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Manufacturer Brand</label>
                <input type="text" name="brand" value="{{ old('brand', 'Bosch') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Purchase Cost (₹) *</label>
                <input type="number" step="0.01" name="purchase_price" required value="{{ old('purchase_price', '500') }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Selling Price (₹) *</label>
                <input type="number" step="0.01" name="selling_price" required value="{{ old('selling_price', '700') }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">MRP (₹) *</label>
                <input type="number" step="0.01" name="mrp" required value="{{ old('mrp', '800') }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Unit of Measure *</label>
                <select name="unit" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="Pcs">Pcs (Pieces)</option>
                    <option value="Litre">Litre</option>
                    <option value="Set">Set</option>
                    <option value="Meter">Meter</option>
                    <option value="Kg">Kg</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Initial Stock Count *</label>
                <input type="number" name="current_stock" required value="{{ old('current_stock', '15') }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Min Stock Alert *</label>
                <input type="number" name="min_stock_alert" required value="{{ old('min_stock_alert', '5') }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Bin / Rack Location</label>
                <input type="text" name="bin_location" value="{{ old('bin_location', 'RACK-A-1') }}" class="w-full px-3 py-2 text-xs font-mono uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">GST Rate (%) *</label>
                <select name="gst_rate" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none font-bold">
                    <option value="18.00" selected>18% (Standard Auto Parts)</option>
                    <option value="28.00">28% (Luxury / AC / Tires)</option>
                    <option value="12.00">12%</option>
                    <option value="5.00">5%</option>
                    <option value="0.00">0% (Nil / Exempt)</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('inventory.index') }}" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Save Part
            </button>
        </div>
    </form>
</div>
@endsection
