@extends('layouts.app')

@section('title', 'Service Packages')
@section('page_title', 'Service Packages & Maintenance Plans')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h3 class="text-base font-bold text-slate-900">Customized Maintenance Packages</h3>
            <p class="text-xs text-slate-500">Bundled service offerings combining labour operations, fluid replacements, and safety checks</p>
        </div>

        <button @click="$dispatch('open-add-pkg-modal')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center justify-center gap-2 shrink-0">
            <i data-lucide="package-plus" class="w-4 h-4"></i> Create Package
        </button>
    </div>

    <!-- Package Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($packages as $pkg)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-blue-400 hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold bg-slate-100 text-slate-700 rounded">{{ $pkg->code }}</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-emerald-100 text-emerald-800">Active</span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 mb-1">{{ $pkg->name }}</h3>
                    <p class="text-xs text-slate-500 mb-4 line-clamp-2">{{ $pkg->description }}</p>

                    <!-- Inclusions -->
                    <div class="space-y-2 pt-3 border-t border-slate-100 mb-4">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Package Inclusions</span>
                        @foreach($pkg->items as $it)
                            <div class="flex items-center gap-2 text-xs text-slate-700">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                <span class="truncate">{{ $it->service->name ?? $it->sparePart->name ?? 'Service Item' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-baseline justify-between">
                    <div>
                        <span class="text-[10px] uppercase text-slate-400 font-semibold">Package Price</span>
                        <div class="text-2xl font-mono font-bold text-blue-600">₹{{ number_format($pkg->package_price, 2) }}</div>
                    </div>
                    <div class="text-right text-[11px] text-slate-500">
                        Valid for {{ $pkg->valid_days }} days
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
