@extends('layouts.app')

@section('title', 'Spare Parts')
@section('page_title', 'Spare Parts & Inventory Master')

@section('content')
<div class="space-y-6">

    <!-- Inventory Top Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Catalog Items</p>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $totalPartsCount }} SKUs</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="boxes" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Low Stock Warnings</p>
                <div class="text-2xl font-bold text-rose-600 mt-1">{{ $lowStockCount }} Items</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Stock Valuation</p>
                <div class="text-2xl font-bold text-emerald-600 mt-1">₹{{ number_format($totalValuation, 2) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[220px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search part name, part #, brand, SKU..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition" />
            </div>

            <select name="category_id" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <label class="flex items-center text-xs font-bold text-slate-700 cursor-pointer">
                <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 mr-1.5"> Low Stock Only
            </label>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition">Filter</button>
            @if(request()->hasAny(['search', 'category_id', 'low_stock']))
                <a href="{{ route('inventory.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900">Reset</a>
            @endif
        </form>

        <a href="{{ route('inventory.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center justify-center gap-2 shrink-0">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Spare Part
        </a>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Part Details</th>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5">Brand</th>
                        <th class="px-6 py-3.5">Rack / Bin</th>
                        <th class="px-6 py-3.5">Purchase Cost</th>
                        <th class="px-6 py-3.5">Selling Price</th>
                        <th class="px-6 py-3.5">Stock Level</th>
                        <th class="px-6 py-3.5">GST Rate</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($spareParts as $part)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('inventory.ledger', $part->id) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm">
                                    {{ $part->name }}
                                </a>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $part->part_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-700 rounded">
                                    {{ $part->category->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $part->brand ?? 'OEM' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-600">
                                {{ $part->bin_location ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-mono">
                                ₹{{ number_format($part->purchase_price, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                ₹{{ number_format($part->selling_price, 2) }}
                                <div class="text-[10px] text-slate-400 font-normal">MRP: ₹{{ number_format($part->mrp, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php $isLow = $part->isLowStock(); @endphp
                                <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-lg {{ $isLow ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $part->current_stock }} {{ $part->unit }}
                                </span>
                                @if($isLow)
                                    <div class="text-[10px] text-rose-600 font-semibold mt-1 flex items-center gap-0.5">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i> Low (Min: {{ $part->min_stock_alert }})
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono">
                                {{ $part->gst_rate }}%
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('inventory.ledger', $part->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Stock Movement Ledger">
                                        <i data-lucide="history" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('inventory.edit', $part->id) }}" class="p-1.5 text-slate-600 hover:bg-slate-100 rounded" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                No spare parts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($spareParts->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $spareParts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
