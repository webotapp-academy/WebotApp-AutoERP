@extends('layouts.app')

@section('title', 'Purchase Orders')
@section('page_title', 'Purchase Orders & Stock Procurement')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h3 class="text-base font-bold text-slate-900">Procurement & POs</h3>
            <p class="text-xs text-slate-500">Order spare parts from manufacturers and receive goods directly into stock</p>
        </div>

        <a href="{{ route('purchases.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5">
            <i data-lucide="shopping-cart" class="w-4 h-4"></i> New Purchase Order
        </a>
    </div>

    <!-- PO Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">PO Number</th>
                        <th class="px-6 py-3.5">Order Date</th>
                        <th class="px-6 py-3.5">Supplier</th>
                        <th class="px-6 py-3.5">Ordered Items</th>
                        <th class="px-6 py-3.5">Subtotal (₹)</th>
                        <th class="px-6 py-3.5">Tax (₹)</th>
                        <th class="px-6 py-3.5">Total Amount (₹)</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($purchaseOrders as $po)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                {{ $po->po_number }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $po->order_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $po->supplier->name ?? 'Supplier' }}
                            </td>
                            <td class="px-6 py-4">
                                @foreach($po->items as $it)
                                    <div>{{ $it->sparePart->name ?? 'Item' }} <span class="font-mono font-bold">({{ $it->quantity }} {{ $it->sparePart->unit ?? 'pcs' }})</span></div>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 font-mono">
                                ₹{{ number_format($po->subtotal, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono">
                                ₹{{ number_format($po->tax_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                ₹{{ number_format($po->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $po->status === 'received' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $po->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($po->status === 'ordered')
                                    <form method="POST" action="{{ route('purchases.receive', $po->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold transition shadow-sm">
                                            Receive Goods (GRN)
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-emerald-600 font-bold">Stock In Stock</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                No purchase orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
