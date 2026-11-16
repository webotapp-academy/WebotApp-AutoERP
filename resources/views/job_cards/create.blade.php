@extends('layouts.app')

@section('title', 'Create Job Card')
@section('page_title', 'Intake & Create New Job Card')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('job-cards.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Job Cards
        </a>
    </div>

    <form method="POST" action="{{ route('job-cards.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf
        @if($appointmentId)
            <input type="hidden" name="appointment_id" value="{{ $appointmentId }}">
        @endif

        <!-- Customer & Vehicle -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-b border-slate-100 pb-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Customer / Owner *</label>
                <select name="customer_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choose Customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ ($selectedCustomerId == $c->id || old('customer_id') == $c->id) ? 'selected' : '' }}>
                            {{ $c->full_name }} ({{ $c->phone }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Vehicle *</label>
                <select name="vehicle_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choose Vehicle --</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ ($selectedVehicleId == $v->id || old('vehicle_id') == $v->id) ? 'selected' : '' }}>
                            {{ $v->registration_number }} - {{ $v->brand->name ?? '' }} {{ $v->model->name ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('vehicle_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Service Advisor & Technician Assignment -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Service Advisor *</label>
                <select name="service_advisor_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach($advisors as $sa)
                        <option value="{{ $sa->id }}" {{ old('service_advisor_id', auth()->id()) == $sa->id ? 'selected' : '' }}>
                            {{ $sa->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Lead Mechanic</label>
                <select name="lead_mechanic_id" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Select Master Technician --</option>
                    @foreach($mechanics as $m)
                        <option value="{{ $m->id }}" {{ old('lead_mechanic_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Service Bay / Lift Station</label>
                <select name="service_bay_id" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Assign Bay --</option>
                    @foreach($serviceBays as $sb)
                        <option value="{{ $sb->id }}" {{ old('service_bay_id') == $sb->id ? 'selected' : '' }}>
                            {{ $sb->name }} ({{ $sb->status }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Job Classifications -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Job Type *</label>
                <select name="job_type" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="periodic_maintenance">Periodic Maintenance</option>
                    <option value="general_repair">General Repair</option>
                    <option value="accidental">Accidental / Insurance</option>
                    <option value="electrical">Electrical / EV</option>
                    <option value="body_paint">Body Shop & Paint</option>
                    <option value="express">Express Service</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Priority *</label>
                <select name="priority" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="medium" selected>Medium</option>
                    <option value="low">Low</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Odometer In (km) *</label>
                <input type="number" name="odometer_in" required value="{{ old('odometer_in', 28500) }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Fuel Gauge *</label>
                <input type="text" name="fuel_level" required value="{{ old('fuel_level', '50%') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <!-- Promised Delivery -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Promised Delivery Date & Time</label>
            <input type="datetime-local" name="promised_delivery_time" value="{{ old('promised_delivery_time', now()->addHours(6)->format('Y-m-d\TH:i')) }}" class="w-full sm:w-1/2 px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
        </div>

        <!-- Complaints & Scope -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Customer Stated Complaints / Work Scope *</label>
            <textarea name="customer_complaints" rows="3" required placeholder="1. Engine oil and oil filter replacement.&#10;2. Front brake squeak during low-speed braking.&#10;3. Air filter cleaning and general inspection." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('customer_complaints') }}</textarea>
            @error('customer_complaints') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Initial Technician Diagnosis Remarks</label>
            <textarea name="diagnosis_remarks" rows="2" placeholder="Preliminary visual check results..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('diagnosis_remarks') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('job-cards.index') }}" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Create Job Card
            </button>
        </div>
    </form>
</div>
@endsection
