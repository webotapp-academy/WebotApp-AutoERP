@extends('layouts.app')

@section('title', 'Expenses')
@section('page_title', 'Workshop Operational Expenses')

@section('content')
<div class="space-y-6">

    <!-- Expense Top Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Workshop Expenses</span>
            <div class="text-3xl font-mono font-black text-rose-600 mt-1">₹{{ number_format($totalExpenses, 2) }}</div>
            <p class="text-xs text-slate-500 mt-0.5">Rent, utility power, tooling maintenance, and staff welfare</p>
        </div>

        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('expenses.index') }}" class="flex items-center gap-2">
                <select name="category_id" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">All Expense Categories</option>
                    @foreach($categories as $ec)
                        <option value="{{ $ec->id }}" {{ request('category_id') == $ec->id ? 'selected' : '' }}>{{ $ec->name }}</option>
                    @endforeach
                </select>
                @if(request()->filled('category_id'))
                    <a href="{{ route('expenses.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900">Reset</a>
                @endif
            </form>

            <a href="{{ route('expenses.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5 shrink-0">
                <i data-lucide="plus" class="w-4 h-4"></i> Record Expense
            </a>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Expense #</th>
                        <th class="px-6 py-3.5">Date</th>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5">Payee / Vendor</th>
                        <th class="px-6 py-3.5">Payment Mode</th>
                        <th class="px-6 py-3.5">Description</th>
                        <th class="px-6 py-3.5">Approved By</th>
                        <th class="px-6 py-3.5 text-right">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($expenses as $exp)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-slate-800">
                                {{ $exp->expense_number }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $exp->expense_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $exp->category->name ?? 'General' }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">
                                {{ $exp->payee }}
                            </td>
                            <td class="px-6 py-4 uppercase font-bold text-[10px] text-slate-600">
                                {{ $exp->payment_method }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 max-w-xs truncate">
                                {{ $exp->description }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $exp->approver->name ?? 'Manager' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-rose-600 text-right text-sm">
                                ₹{{ number_format($exp->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No expenses recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

