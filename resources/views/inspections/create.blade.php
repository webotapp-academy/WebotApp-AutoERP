@extends('layouts.app')

@section('title', 'Vehicle Inspection Check-In')
@section('page_title', 'Digital Vehicle Inspection (Check-In)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('inspections.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Inspections
        </a>
    </div>

    <form method="POST" action="{{ route('inspections.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <!-- Vehicle Selection -->
        <div class="border-b border-slate-100 pb-4">
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Select Vehicle For Check-In *</label>
            <select name="vehicle_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Choose Vehicle --</option>
                @foreach($vehicles as $v)
                    <option value="{{ $v->id }}" {{ ($selectedVehicleId == $v->id || old('vehicle_id') == $v->id) ? 'selected' : '' }}>
                        {{ $v->registration_number }} - {{ $v->brand->name ?? '' }} {{ $v->model->name ?? '' }} (Owner: {{ $v->customer->full_name ?? 'N/A' }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Initial Readings -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Current Odometer (km) *</label>
                <input type="number" name="odometer_reading" required value="{{ old('odometer_reading', 25000) }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Fuel Gauge Level *</label>
                <select name="fuel_level" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none font-semibold">
                    <option value="empty">Empty / Reserve</option>
                    <option value="25_percent">1/4 Tank (25%)</option>
                    <option value="50_percent" selected>1/2 Tank (50%)</option>
                    <option value="75_percent">3/4 Tank (75%)</option>
                    <option value="full">Full Tank (100%)</option>
                </select>
            </div>
        </div>

        <!-- Equipment Present Checklist -->
        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
            <h4 class="text-xs font-bold uppercase text-slate-700 tracking-wider mb-3">Vehicle Accessories & Tools Received</h4>
            <div class="grid grid-cols-3 gap-4">
                <label class="flex items-center text-xs font-semibold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="tool_kit_present" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2"> Tool Kit Present
                </label>
                <label class="flex items-center text-xs font-semibold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="spare_wheel_present" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2"> Spare Wheel Present
                </label>
                <label class="flex items-center text-xs font-semibold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="jack_handle_present" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2"> Jack & Handle Present
                </label>
            </div>
        </div>

        <!-- Section 8.2 Inspection Checklists -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Exterior & Glass -->
            <div class="p-4 border border-slate-200 rounded-xl space-y-3">
                <h4 class="text-xs font-bold uppercase text-slate-800 tracking-wider flex items-center gap-1.5">
                    <i data-lucide="eye" class="w-4 h-4 text-blue-600"></i> Exterior & Visibility
                </h4>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Wiper Blades & Washer</span>
                        <select name="exterior_wipers" class="px-2 py-1 text-xs border border-slate-200 rounded">
                            <option value="ok">OK / Functional</option>
                            <option value="worn">Worn Out</option>
                            <option value="replace">Needs Replace</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Headlights & Taillights</span>
                        <select name="exterior_lights" class="px-2 py-1 text-xs border border-slate-200 rounded">
                            <option value="ok">All Working</option>
                            <option value="bulb_fused">Bulb Fused</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Rear View Mirrors</span>
                        <select name="exterior_mirrors" class="px-2 py-1 text-xs border border-slate-200 rounded">
                            <option value="ok">Intact</option>
                            <option value="damaged">Scratched / Damaged</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Mechanical & Fluids -->
            <div class="p-4 border border-slate-200 rounded-xl space-y-3">
                <h4 class="text-xs font-bold uppercase text-slate-800 tracking-wider flex items-center gap-1.5">
                    <i data-lucide="droplet" class="w-4 h-4 text-emerald-600"></i> Fluids & Mechanical
                </h4>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Engine Oil Dipstick</span>
                        <select name="mech_oil" class="px-2 py-1 text-xs border border-slate-200 rounded">
                            <option value="ok">Full / Good</option>
                            <option value="low">Low Level</option>
                            <option value="black_dirty">Degraded / Dirty</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Radiator Coolant</span>
                        <select name="mech_coolant" class="px-2 py-1 text-xs border border-slate-200 rounded">
                            <option value="ok">Normal Level</option>
                            <option value="low">Low Level</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">12V Battery Voltage</span>
                        <select name="mech_battery" class="px-2 py-1 text-xs border border-slate-200 rounded">
                            <option value="ok">Healthy (12.6V+)</option>
                            <option value="weak">Weak Charge</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tire Tread Depths -->
        <div class="p-4 border border-slate-200 rounded-xl">
            <h4 class="text-xs font-bold uppercase text-slate-800 tracking-wider mb-3">Tire Tread Depths</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <label class="block text-slate-500 mb-1">Front Left</label>
                    <input type="text" name="tire_fl" value="5 mm" class="w-full px-2 py-1 border border-slate-200 rounded font-mono" />
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">Front Right</label>
                    <input type="text" name="tire_fr" value="5 mm" class="w-full px-2 py-1 border border-slate-200 rounded font-mono" />
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">Rear Left</label>
                    <input type="text" name="tire_rl" value="6 mm" class="w-full px-2 py-1 border border-slate-200 rounded font-mono" />
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">Rear Right</label>
                    <input type="text" name="tire_rr" value="6 mm" class="w-full px-2 py-1 border border-slate-200 rounded font-mono" />
                </div>
            </div>
        </div>

        <!-- Scratches & Customer Belongings -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Exterior Scratches & Dents Notes</label>
                <textarea name="scratch_notes" rows="2" placeholder="e.g. Minor scratch on rear right bumper corner; hairline stone chip on left fender..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Customer Items Kept Inside Car</label>
                <textarea name="customer_items_inside" rows="2" placeholder="e.g. Sunglass in dashboard box; umbrella on back seat; pen drive..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('inspections.index') }}" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Complete Check-In Inspection
            </button>
        </div>
    </form>
</div>
@endsection
