@extends('layouts.app')

@section('title', 'Service Catalog')
@section('page_title', 'Labour Service Master Catalog')

@section('content')
<div class="space-y-6">

    <!-- Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('services.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search service name, code, SAC..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>

            <select name="category_id" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition">Filter</button>
            @if(request()->hasAny(['search', 'category_id']))
                <a href="{{ route('services.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900">Reset</a>
            @endif
        </form>

        <button @click="$dispatch('open-add-service-modal')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center justify-center gap-2 shrink-0">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Service
        </button>
    </div>

    <!-- Quick Add Service Form (collapsible) -->
    <div x-data="{ showAdd: false }" @open-add-service-modal.window="showAdd = true" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6" x-show="showAdd" x-cloak>
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Add New Labour Service Master</h3>
            <button @click="showAdd = false" class="text-slate-400 hover:text-slate-700"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>

        <form method="POST" action="{{ route('services.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Service Name *</label>
                <input type="text" name="name" required placeholder="e.g. Front Disc Brake Pad Overhaul" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Category *</label>
                <select name="category_id" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Service Code *</label>
                <input type="text" name="code" required placeholder="SRV-200" class="w-full px-3 py-2 text-xs font-mono uppercase border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Standard Hours</label>
                <input type="number" step="0.1" name="standard_time_hours" value="1.0" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Standard Labour Charge (₹) *</label>
                <input type="number" step="0.01" name="standard_labour_cost" value="600" required class="w-full px-3 py-2 text-xs font-mono font-bold border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">SAC Code *</label>
                <input type="text" name="sac_code" value="998729" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Default GST (%) *</label>
                <input type="number" step="0.01" name="default_gst_rate" value="18.00" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition">
                    Save Service
                </button>
            </div>
        </form>
    </div>

    <!-- Services Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Code</th>
                        <th class="px-6 py-3.5">Service Operation</th>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5">SAC Code</th>
                        <th class="px-6 py-3.5">Est. Time</th>
                        <th class="px-6 py-3.5">Standard Labour (₹)</th>
                        <th class="px-6 py-3.5">GST Rate</th>
                        <th class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($services as $srv)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                {{ $srv->code }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900 text-sm">
                                {{ $srv->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-700 rounded">
                                    {{ $srv->category->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-500">
                                {{ $srv->sac_code }}
                            </td>
                            <td class="px-6 py-4 font-mono">
                                {{ $srv->standard_time_hours }} hrs
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                ₹{{ number_format($srv->standard_labour_cost, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono">
                                {{ $srv->default_gst_rate }}%
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase bg-emerald-100 text-emerald-800">
                                    Active
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No services found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($services->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

