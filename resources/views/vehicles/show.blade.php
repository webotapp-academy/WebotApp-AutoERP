@extends('layouts.app')

@section('title', $vehicle->registration_number)
@section('page_title', 'Vehicle Details: ' . $vehicle->registration_number)

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('vehicles.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Vehicles
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('job-cards.create', ['vehicle_id' => $vehicle->id]) }}" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                <i data-lucide="file-plus" class="w-4 h-4"></i> Open Job Card
            </a>
            <a href="{{ route('inspections.create', ['vehicle_id' => $vehicle->id]) }}" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                <i data-lucide="clipboard-check" class="w-4 h-4"></i> Check-in Inspection
            </a>
            <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                <i data-lucide="edit" class="w-4 h-4"></i> Edit
            </a>
        </div>
    </div>

    <!-- Vehicle Hero Details Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-100 pb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-900 text-white font-mono font-bold text-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="car" class="w-8 h-8 text-blue-400"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-mono font-black text-slate-900 tracking-tight">{{ $vehicle->registration_number }}</h2>
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full uppercase {{ $vehicle->fuel_type === 'Electric' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $vehicle->fuel_type }}
                        </span>
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full uppercase bg-slate-100 text-slate-700">
                            {{ $vehicle->status }}
                        </span>
                    </div>
                    <div class="text-sm font-bold text-slate-700 mt-1">
                        {{ $vehicle->brand->name ?? '' }} {{ $vehicle->model->name ?? '' }} ({{ $vehicle->vehicleType->name ?? 'Car' }})
                    </div>
                    <div class="text-xs text-slate-400 mt-0.5">
                        Year: {{ $vehicle->model_year ?? 'N/A' }} • Color: {{ $vehicle->color ?? 'Standard' }} • Transmission: {{ $vehicle->transmission }}
                    </div>
                </div>
            </div>

            <!-- Owner & Odometer badge -->
            <div class="flex items-center gap-6">
                <div class="text-right">
                    <div class="text-[11px] font-semibold uppercase text-slate-400 tracking-wider">Registered Owner</div>
                    <a href="{{ route('customers.show', $vehicle->customer_id) }}" class="text-sm font-bold text-blue-600 hover:underline">
                        {{ $vehicle->customer->full_name ?? 'N/A' }}
                    </a>
                    <div class="text-xs text-slate-500">{{ $vehicle->customer->phone ?? '' }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[11px] font-semibold uppercase text-slate-400 tracking-wider">Odometer Reading</div>
                    <div class="text-xl font-mono font-bold text-slate-900">
                        {{ number_format($vehicle->current_odometer) }} <span class="text-xs text-slate-400 font-sans">KM</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compliance & IDs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-6 text-xs">
            <div>
                <span class="text-slate-400 font-medium">VIN / Chassis:</span>
                <div class="font-mono font-bold text-slate-800 mt-0.5">{{ $vehicle->vin_chassis_number ?? 'Not Recorded' }}</div>
            </div>
            <div>
                <span class="text-slate-400 font-medium">Engine Number:</span>
                <div class="font-mono font-bold text-slate-800 mt-0.5">{{ $vehicle->engine_number ?? 'Not Recorded' }}</div>
            </div>
            <div>
                <span class="text-slate-400 font-medium">Insurance Policy:</span>
                <div class="font-bold text-slate-800 mt-0.5">
                    {{ $vehicle->insurance_company ?? 'N/A' }}
                    @if($vehicle->insurance_expiry_date)
                        <span class="block text-[11px] font-normal text-slate-500">Exp: {{ $vehicle->insurance_expiry_date->format('d M Y') }}</span>
                    @endif
                </div>
            </div>
            <div>
                <span class="text-slate-400 font-medium">PUC Expiry:</span>
                <div class="font-bold text-slate-800 mt-0.5">
                    {{ $vehicle->puc_expiry_date ? $vehicle->puc_expiry_date->format('d M Y') : 'Not Recorded' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Job Cards History -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Complete Repair & Service History</h3>
                <p class="text-xs text-slate-500">All workshop visits, parts replacements, and billings</p>
            </div>
            <a href="{{ route('job-cards.create', ['vehicle_id' => $vehicle->id]) }}" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold rounded-lg transition">
                + New Service Job
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3">Job Card #</th>
                        <th class="px-6 py-3">Check-In Date</th>
                        <th class="px-6 py-3">Mileage In</th>
                        <th class="px-6 py-3">Complaints & Scope</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Total Amount</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($vehicle->jobCards as $jc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-3.5 font-mono font-bold text-blue-600">
                                <a href="{{ route('job-cards.show', $jc->id) }}">{{ $jc->job_card_number }}</a>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600">
                                {{ $jc->check_in_time->format('d M Y') }}
                            </td>
                            <td class="px-6 py-3.5 font-mono font-bold text-slate-800">
                                {{ number_format($jc->odometer_in) }} km
                            </td>
                            <td class="px-6 py-3.5 text-slate-700 max-w-xs truncate">
                                {{ $jc->customer_complaints ?? 'Routine Service' }}
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full uppercase bg-blue-100 text-blue-800">
                                    {{ str_replace('_', ' ', $jc->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 font-bold text-slate-900">
                                ₹{{ number_format($jc->final_grand_total, 2) }}
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <a href="{{ route('job-cards.show', $jc->id) }}" class="text-blue-600 font-semibold hover:underline">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                No past service history found for this vehicle.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

