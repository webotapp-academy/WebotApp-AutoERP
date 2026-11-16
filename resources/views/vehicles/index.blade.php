@extends('layouts.app')

@section('title', 'Vehicles')
@section('page_title', 'Vehicle Directory & Fleet')

@section('content')
<div class="space-y-6">
    <!-- Top Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('vehicles.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[220px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Reg No, VIN, Customer, Model..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition" />
            </div>

            <select name="type_id" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">All Vehicle Types</option>
                @foreach($vehicleTypes as $vt)
                    <option value="{{ $vt->id }}" {{ request('type_id') == $vt->id ? 'selected' : '' }}>{{ $vt->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition">Filter</button>
            @if(request()->hasAny(['search', 'type_id']))
                <a href="{{ route('vehicles.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900">Reset</a>
            @endif
        </form>

        <a href="{{ route('vehicles.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center justify-center gap-2 shrink-0">
            <i data-lucide="car" class="w-4 h-4"></i> Add Vehicle
        </a>
    </div>

    <!-- Vehicles Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Registration Number</th>
                        <th class="px-6 py-3.5">Make & Model</th>
                        <th class="px-6 py-3.5">Customer / Owner</th>
                        <th class="px-6 py-3.5">Fuel & Transmission</th>
                        <th class="px-6 py-3.5">Current Odometer</th>
                        <th class="px-6 py-3.5">Insurance Expiry</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($vehicles as $vehicle)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('vehicles.show', $vehicle->id) }}" class="font-mono font-bold text-blue-600 text-sm hover:underline">
                                    {{ $vehicle->registration_number }}
                                </a>
                                @if($vehicle->vin_chassis_number)
                                    <div class="text-[10px] text-slate-400 font-mono">VIN: {{ substr($vehicle->vin_chassis_number, 0, 10) }}...</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $vehicle->brand->name ?? '' }} {{ $vehicle->model->name ?? '' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $vehicle->vehicleType->name ?? '' }} • {{ $vehicle->model_year ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('customers.show', $vehicle->customer_id) }}" class="font-semibold text-slate-800 hover:text-blue-600">
                                    {{ $vehicle->customer->full_name ?? 'N/A' }}
                                </a>
                                <div class="text-[11px] text-slate-500">{{ $vehicle->customer->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase {{ $vehicle->fuel_type === 'Electric' ? 'bg-emerald-100 text-emerald-800' : ($vehicle->fuel_type === 'Diesel' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $vehicle->fuel_type }}
                                </span>
                                <span class="text-[11px] text-slate-500 ml-1 font-medium">{{ $vehicle->transmission }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-800">
                                {{ number_format($vehicle->current_odometer) }} km
                            </td>
                            <td class="px-6 py-4">
                                @if($vehicle->insurance_expiry_date)
                                    @php
                                        $isExpiring = $vehicle->insurance_expiry_date->isPast() || $vehicle->insurance_expiry_date->diffInDays(now()) < 30;
                                    @endphp
                                    <div class="{{ $isExpiring ? 'text-rose-600 font-bold' : 'text-slate-700' }}">
                                        {{ $vehicle->insurance_expiry_date->format('d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-500">{{ $vehicle->insurance_company ?? '' }}</div>
                                @else
                                    <span class="text-slate-400 italic">Not recorded</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase {{ $vehicle->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $vehicle->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('job-cards.create', ['vehicle_id' => $vehicle->id]) }}" class="px-2 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded text-[11px] font-bold" title="Open Job Card">
                                        + Job Card
                                    </a>
                                    <a href="{{ route('vehicles.show', $vehicle->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="View Vehicle History">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="p-1.5 text-slate-600 hover:bg-slate-100 rounded" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <i data-lucide="car" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                No vehicles found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vehicles->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
