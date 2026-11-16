@extends('layouts.app')

@section('title', 'Book Appointment')
@section('page_title', 'Book Customer Service Appointment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('appointments.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Schedule
        </a>
    </div>

    <form method="POST" action="{{ route('appointments.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Customer *</label>
                <select name="customer_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ ($selectedCustomerId == $c->id || old('customer_id') == $c->id) ? 'selected' : '' }}>
                            {{ $c->full_name }} ({{ $c->phone }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Vehicle *</label>
                <select name="vehicle_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Select Vehicle --</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ ($selectedVehicleId == $v->id || old('vehicle_id') == $v->id) ? 'selected' : '' }}>
                            {{ $v->registration_number }} - {{ $v->brand->name ?? '' }} {{ $v->model->name ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Appointment Date *</label>
                <input type="date" name="appointment_date" required value="{{ old('appointment_date', today()->toDateString()) }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Time Slot *</label>
                <input type="time" name="appointment_time" required value="{{ old('appointment_time', '10:00') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Preferred Lift / Bay (Optional)</label>
                <select name="service_bay_id" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Auto Assign --</option>
                    @foreach($serviceBays as $sb)
                        <option value="{{ $sb->id }}">{{ $sb->name }} ({{ $sb->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Service Type *</label>
                <select name="service_type" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="Periodic Maintenance">Periodic Maintenance</option>
                    <option value="General Mechanical Repair">General Mechanical Repair</option>
                    <option value="Brake & Suspension Service">Brake & Suspension Service</option>
                    <option value="3D Wheel Alignment & Balancing">3D Wheel Alignment & Balancing</option>
                    <option value="AC Cooling & Gas Refill">AC Cooling & Gas Refill</option>
                    <option value="Electric Vehicle (EV) Diagnostics">Electric Vehicle (EV) Diagnostics</option>
                    <option value="Denting & Painting">Denting & Painting</option>
                    <option value="Express Washing & Detailing">Express Washing & Detailing</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Estimated Budget (₹)</label>
                <input type="number" step="0.01" name="estimated_cost" value="{{ old('estimated_cost', '3500') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Customer Reported Complaints / Notes</label>
            <textarea name="customer_notes" rows="3" placeholder="e.g. Unusual rattling sound from front axle over speed bumps; low cooling from AC in afternoon..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('customer_notes') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('appointments.index') }}" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Confirm Booking
            </button>
        </div>
    </form>
</div>
@endsection
