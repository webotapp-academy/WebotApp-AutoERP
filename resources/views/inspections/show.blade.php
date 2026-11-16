@extends('layouts.app')

@section('title', $inspection->inspection_number)
@section('page_title', 'Inspection Report: ' . $inspection->inspection_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('inspections.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Inspections
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i> Print Report
            </button>
            <a href="{{ route('job-cards.create', ['vehicle_id' => $inspection->vehicle_id]) }}" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                <i data-lucide="file-plus" class="w-4 h-4"></i> Open Job Card
            </a>
        </div>
    </div>

    <!-- Printable Inspection Document Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 pb-6">
            <div>
                <span class="text-[11px] font-bold uppercase text-blue-600 tracking-wider">ABC Automobile Care & Service Centre</span>
                <h2 class="text-xl font-black text-slate-900 mt-1">VEHICLE CHECK-IN INSPECTION REPORT</h2>
                <div class="text-xs text-slate-500 mt-0.5">Report Reference: <span class="font-mono font-bold text-slate-800">{{ $inspection->inspection_number }}</span></div>
            </div>
            <div class="text-right text-xs">
                <div class="font-bold text-slate-800">{{ $inspection->inspection_date->format('d M Y, h:i A') }}</div>
                <div class="text-slate-500">Inspector: {{ $inspection->inspector->name ?? 'Advisor' }}</div>
            </div>
        </div>

        <!-- Vehicle & Customer Info -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-slate-50 rounded-xl text-xs">
            <div>
                <span class="text-slate-400 font-medium">Vehicle Reg No:</span>
                <div class="font-mono font-bold text-slate-900 text-sm mt-0.5">{{ $inspection->vehicle->registration_number ?? 'N/A' }}</div>
            </div>
            <div>
                <span class="text-slate-400 font-medium">Make & Model:</span>
                <div class="font-bold text-slate-800 mt-0.5">{{ $inspection->vehicle->brand->name ?? '' }} {{ $inspection->vehicle->model->name ?? '' }}</div>
            </div>
            <div>
                <span class="text-slate-400 font-medium">Customer:</span>
                <div class="font-bold text-slate-800 mt-0.5">{{ $inspection->customer->full_name ?? 'N/A' }}</div>
            </div>
            <div>
                <span class="text-slate-400 font-medium">Odometer & Fuel:</span>
                <div class="font-mono font-bold text-slate-800 mt-0.5">
                    {{ number_format($inspection->odometer_reading) }} km • {{ strtoupper(str_replace('_', ' ', $inspection->fuel_level)) }}
                </div>
            </div>
        </div>

        <!-- Accessories & Tool Check -->
        <div>
            <h4 class="text-xs font-bold uppercase text-slate-800 tracking-wider mb-2">Inventory Check Inside Vehicle</h4>
            <div class="grid grid-cols-3 gap-3 text-xs">
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-slate-600">Tool Kit:</span>
                    <span class="font-bold {{ $inspection->tool_kit_present ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $inspection->tool_kit_present ? '✓ Present' : '✗ Missing' }}
                    </span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-slate-600">Spare Wheel:</span>
                    <span class="font-bold {{ $inspection->spare_wheel_present ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $inspection->spare_wheel_present ? '✓ Present' : '✗ Missing' }}
                    </span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg flex items-center justify-between">
                    <span class="text-slate-600">Jack & Handle:</span>
                    <span class="font-bold {{ $inspection->jack_handle_present ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $inspection->jack_handle_present ? '✓ Present' : '✗ Missing' }}
                    </span>
                </div>
            </div>
            @if($inspection->customer_items_inside)
                <div class="mt-2 p-3 bg-blue-50/60 border border-blue-100 rounded-lg text-xs text-blue-900">
                    <span class="font-bold">Customer Belongings Noted:</span> {{ $inspection->customer_items_inside }}
                </div>
            @endif
        </div>

        <!-- Checklist details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 border border-slate-200 rounded-xl space-y-2">
                <h4 class="font-bold uppercase text-slate-800 tracking-wider">Exterior & Glass</h4>
                @if(is_array($inspection->exterior_checks))
                    @foreach($inspection->exterior_checks as $k => $v)
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="capitalize">{{ str_replace('_', ' ', $k) }}</span>
                            <span class="font-bold uppercase text-slate-800">{{ $v }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="text-slate-400">Standard checks cleared</div>
                @endif
            </div>

            <div class="p-4 border border-slate-200 rounded-xl space-y-2">
                <h4 class="font-bold uppercase text-slate-800 tracking-wider">Mechanical & Fluids</h4>
                @if(is_array($inspection->mechanical_checks))
                    @foreach($inspection->mechanical_checks as $k => $v)
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="capitalize">{{ str_replace('_', ' ', $k) }}</span>
                            <span class="font-bold uppercase text-slate-800">{{ $v }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="text-slate-400">Standard checks cleared</div>
                @endif
            </div>
        </div>

        <!-- Signatures Section -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-2 gap-8 text-xs">
            <div>
                <div class="border-b border-slate-300 pb-8 text-slate-400 italic">Customer Signature</div>
                <div class="mt-2 font-bold text-slate-700">{{ $inspection->customer->full_name ?? 'Customer' }}</div>
                <div class="text-[10px] text-slate-400">Confirmed vehicle condition at intake</div>
            </div>
            <div class="text-right">
                <div class="border-b border-slate-300 pb-8 text-slate-400 italic">Authorized Inspector</div>
                <div class="mt-2 font-bold text-slate-700">{{ $inspection->inspector->name ?? 'Workshop Staff' }}</div>
                <div class="text-[10px] text-slate-400">ABC Automobile Care</div>
            </div>
        </div>
    </div>
</div>
@endsection

