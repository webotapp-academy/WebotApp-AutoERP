@extends('layouts.app')

@section('title', 'Suppliers')
@section('page_title', 'Parts Suppliers & Vendors')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h3 class="text-base font-bold text-slate-900">Vendors & Manufacturers</h3>
            <p class="text-xs text-slate-500">Registered parts suppliers, lubricants distributors, and authorized vendors</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Supplier
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Supplier Name</th>
                        <th class="px-6 py-3.5">GSTIN</th>
                        <th class="px-6 py-3.5">Contact Phone</th>
                        <th class="px-6 py-3.5">City</th>
                        <th class="px-6 py-3.5">Payment Terms</th>
                        <th class="px-6 py-3.5">Total Orders</th>
                        <th class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($suppliers as $sup)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-bold text-slate-900 text-sm">
                                {{ $sup->name }}
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600">
                                {{ $sup->gstin ?? 'URP' }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $sup->phone }}
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $sup->city ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $sup->payment_terms_days }} Days Credit
                            </td>
                            <td class="px-6 py-4 font-bold text-blue-600">
                                {{ $sup->purchase_orders_count }} POs
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase bg-emerald-100 text-emerald-800">
                                    Active
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No suppliers registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

