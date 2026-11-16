@extends('layouts.app')

@section('title', 'Edit Vehicle')
@section('page_title', 'Edit Vehicle: ' . $vehicle->registration_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('vehicles.show', $vehicle->id) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Details
        </a>
    </div>

    <form method="POST" action="{{ route('vehicles.update', $vehicle->id) }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf
        @method('PUT')

        <!-- Owner & Status -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-b border-slate-100 pb-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Owner / Customer *</label>
                <select name="customer_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ $vehicle->customer_id == $c->id ? 'selected' : '' }}>
                            {{ $c->full_name }} ({{ $c->phone }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Vehicle Status *</label>
                <select name="status" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="active" {{ $vehicle->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="in_service" {{ $vehicle->status === 'in_service' ? 'selected' : '' }}>In Service</option>
                    <option value="archived" {{ $vehicle->status === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
        </div>

        <!-- Classification & Models -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Vehicle Type *</label>
                <select name="vehicle_type_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($vehicleTypes as $vt)
                        <option value="{{ $vt->id }}" {{ $vehicle->vehicle_type_id == $vt->id ? 'selected' : '' }}>{{ $vt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Make / Brand *</label>
                <select name="brand_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" {{ $vehicle->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Model *</label>
                <select name="model_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($models as $m)
                        <option value="{{ $m->id }}" {{ $vehicle->model_id == $m->id ? 'selected' : '' }}>{{ $m->brand->name ?? '' }} - {{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- IDs -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Registration Plate # *</label>
                <input type="text" name="registration_number" required value="{{ old('registration_number', $vehicle->registration_number) }}" class="w-full px-3 py-2 text-xs font-mono font-bold uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">VIN / Chassis</label>
                <input type="text" name="vin_chassis_number" value="{{ old('vin_chassis_number', $vehicle->vin_chassis_number) }}" class="w-full px-3 py-2 text-xs font-mono uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Engine Number</label>
                <input type="text" name="engine_number" value="{{ old('engine_number', $vehicle->engine_number) }}" class="w-full px-3 py-2 text-xs font-mono uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <!-- Specs -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Fuel Type *</label>
                <select name="fuel_type" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="Petrol" {{ $vehicle->fuel_type === 'Petrol' ? 'selected' : '' }}>Petrol</option>
                    <option value="Diesel" {{ $vehicle->fuel_type === 'Diesel' ? 'selected' : '' }}>Diesel</option>
                    <option value="CNG" {{ $vehicle->fuel_type === 'CNG' ? 'selected' : '' }}>CNG</option>
                    <option value="Electric" {{ $vehicle->fuel_type === 'Electric' ? 'selected' : '' }}>Electric (EV)</option>
                    <option value="Hybrid" {{ $vehicle->fuel_type === 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Transmission *</label>
                <select name="transmission" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="Manual" {{ $vehicle->transmission === 'Manual' ? 'selected' : '' }}>Manual</option>
                    <option value="Automatic" {{ $vehicle->transmission === 'Automatic' ? 'selected' : '' }}>Automatic</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Year</label>
                <input type="number" name="model_year" value="{{ old('model_year', $vehicle->model_year) }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Current Odometer (km) *</label>
                <input type="number" name="current_odometer" required value="{{ old('current_odometer', $vehicle->current_odometer) }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('vehicles.show', $vehicle->id) }}" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Update Vehicle
            </button>
        </div>
    </form>
</div>
@endsection
