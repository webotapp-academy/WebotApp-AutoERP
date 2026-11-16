@extends('layouts.app')

@section('title', 'Register Vehicle')
@section('page_title', 'Register New Vehicle')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('vehicles.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Vehicles
        </a>
    </div>

    <form method="POST" action="{{ route('vehicles.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <!-- Owner / Customer Assignment -->
        <div class="border-b border-slate-100 pb-4">
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Vehicle Owner / Customer *</label>
            <select name="customer_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Select Customer --</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ (old('customer_id') == $c->id || $selectedCustomerId == $c->id) ? 'selected' : '' }}>
                        {{ $c->full_name }} ({{ $c->phone }}) {{ $c->company_name ? '- ' . $c->company_name : '' }}
                    </option>
                @endforeach
            </select>
            @error('customer_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Vehicle Classification & Make -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Vehicle Type *</label>
                <select name="vehicle_type_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($vehicleTypes as $vt)
                        <option value="{{ $vt->id }}" {{ old('vehicle_type_id') == $vt->id ? 'selected' : '' }}>{{ $vt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Make / Brand *</label>
                <select name="brand_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" {{ old('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Model *</label>
                <select name="model_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($models as $m)
                        <option value="{{ $m->id }}" {{ old('model_id') == $m->id ? 'selected' : '' }}>{{ $m->brand->name ?? '' }} - {{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Identification Numbers -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Registration Plate # *</label>
                <input type="text" name="registration_number" required value="{{ old('registration_number') }}" placeholder="MH-02-AB-1234" class="w-full px-3 py-2 text-xs font-mono font-bold uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                @error('registration_number') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">VIN / Chassis Number</label>
                <input type="text" name="vin_chassis_number" value="{{ old('vin_chassis_number') }}" placeholder="MA3ER..." class="w-full px-3 py-2 text-xs font-mono uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Engine Number</label>
                <input type="text" name="engine_number" value="{{ old('engine_number') }}" placeholder="ENG123456" class="w-full px-3 py-2 text-xs font-mono uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <!-- Specs & Odometer -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Fuel Type *</label>
                <select name="fuel_type" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="Petrol">Petrol</option>
                    <option value="Diesel">Diesel</option>
                    <option value="CNG">CNG</option>
                    <option value="Electric">Electric (EV)</option>
                    <option value="Hybrid">Hybrid</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Transmission *</label>
                <select name="transmission" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="Manual">Manual</option>
                    <option value="Automatic">Automatic</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Manufacturing Year</label>
                <input type="number" name="model_year" value="{{ old('model_year', date('Y')) }}" min="1990" max="{{ date('Y') + 1 }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Current Odometer (km) *</label>
                <input type="number" name="current_odometer" required value="{{ old('current_odometer', 15000) }}" min="0" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <!-- Insurance & Compliance -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-2">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Color</label>
                <input type="text" name="color" value="{{ old('color', 'White') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Insurance Company</label>
                <input type="text" name="insurance_company" value="{{ old('insurance_company', 'ICICI Lombard') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Insurance Expiry Date</label>
                <input type="date" name="insurance_expiry_date" value="{{ old('insurance_expiry_date', now()->addYear()->toDateString()) }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">PUC Expiry Date</label>
                <input type="date" name="puc_expiry_date" value="{{ old('puc_expiry_date', now()->addMonths(6)->toDateString()) }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('vehicles.index') }}" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Save Vehicle
            </button>
        </div>
    </form>
</div>
@endsection

