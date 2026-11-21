@extends('layouts.app')

@section('title', 'Vehicle Inspection Check-In')
@section('page_title', 'Digital Vehicle Inspection (Check-In)')

@section('content')
<div x-data="{
    searchQuery: '',
    results: [],
    loading: false,
    showResults: false,
    mode: 'search', // 'search' or 'dropdown'
    selectedVehicle: @json($selectedVehicleData),
    odometer: {{ $initialOdometer }},

    async searchVehicles() {
        if (!this.searchQuery || this.searchQuery.trim().length < 1) {
            this.results = [];
            this.showResults = false;
            return;
        }
        this.loading = true;
        try {
            const resp = await fetch('{{ route('vehicles.search') }}?q=' + encodeURIComponent(this.searchQuery.trim()));
            if (resp.ok) {
                this.results = await resp.json();
                this.showResults = true;
            }
        } catch (e) {
            console.error('Vehicle search error:', e);
        } finally {
            this.loading = false;
        }
    },

    selectVehicle(v) {
        this.selectedVehicle = v;
        this.searchQuery = '';
        this.showResults = false;
        if (v && v.current_odometer !== undefined) {
            this.odometer = v.current_odometer;
            const odoInput = document.getElementById('odometer_reading_input');
            if (odoInput) odoInput.value = v.current_odometer;
        }
    },

    clearSelection() {
        this.selectedVehicle = null;
        this.searchQuery = '';
        this.results = [];
        this.showResults = false;
    },

    onDropdownChange(e) {
        const val = e.target.value;
        if (!val) {
            this.selectedVehicle = null;
            return;
        }
        const opt = e.target.selectedOptions[0];
        if (opt) {
            this.selectedVehicle = {
                id: val,
                registration_number: opt.getAttribute('data-reg'),
                brand: opt.getAttribute('data-brand'),
                model: opt.getAttribute('data-model'),
                customer_name: opt.getAttribute('data-owner'),
                customer_phone: opt.getAttribute('data-phone'),
                current_odometer: parseInt(opt.getAttribute('data-odometer')) || 0,
                fuel_type: opt.getAttribute('data-fuel')
            };
            if (opt.getAttribute('data-odometer')) {
                this.odometer = parseInt(opt.getAttribute('data-odometer'));
                const odoInput = document.getElementById('odometer_reading_input');
                if (odoInput) odoInput.value = this.odometer;
            }
        }
    }
}" class="max-w-4xl mx-auto space-y-6">

    <!-- Top Navigation Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('inspections.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Inspections
        </a>
    </div>

    <!-- Inspection Check-in Form -->
    <form method="POST" action="{{ route('inspections.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <!-- Vehicle Selection Section with AJAX Search -->
        <div class="border-b border-slate-100 pb-5">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-semibold text-slate-700 uppercase flex items-center gap-1.5">
                    <i data-lucide="car" class="w-4 h-4 text-blue-600"></i> Select Vehicle For Check-In *
                </label>
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="mode = (mode === 'search' ? 'dropdown' : 'search')" 
                            class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition flex items-center gap-1">
                        <span x-text="mode === 'search' ? 'Switch to Dropdown List' : 'Switch to Live AJAX Search'"></span>
                    </button>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('vehicles.create') }}" target="_blank" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i> Register New Vehicle
                    </a>
                </div>
            </div>

            <!-- Hidden input for standard form submission -->
            <input type="hidden" name="vehicle_id" :value="selectedVehicle ? selectedVehicle.id : ''" required />

            <!-- Selected Vehicle Card (When chosen) -->
            <div x-show="selectedVehicle" x-cloak class="p-4 bg-blue-50/70 border border-blue-200 rounded-xl mb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="px-3.5 py-1.5 bg-slate-900 text-white font-mono font-black text-sm rounded-lg border-2 border-slate-800 tracking-wider shadow-sm flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-400"></i>
                        <span x-text="selectedVehicle ? selectedVehicle.registration_number : ''"></span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-900 flex items-center gap-2">
                            <span x-text="(selectedVehicle?.brand || '') + ' ' + (selectedVehicle?.model || '')"></span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white border border-blue-200 text-blue-700" x-text="selectedVehicle?.fuel_type || 'Petrol'"></span>
                        </div>
                        <div class="text-[11px] text-slate-600 mt-0.5 flex items-center gap-2">
                            <span>Owner: <strong x-text="selectedVehicle?.customer_name || 'N/A'"></strong></span>
                            <span x-show="selectedVehicle?.customer_phone">• <span x-text="selectedVehicle?.customer_phone"></span></span>
                            <span x-show="selectedVehicle?.current_odometer">• Last Recorded Odo: <span class="font-mono font-bold text-slate-800" x-text="selectedVehicle?.current_odometer + ' km'"></span></span>
                        </div>
                    </div>
                </div>
                <button type="button" @click="clearSelection()" class="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-bold rounded-lg transition shadow-sm flex items-center gap-1 self-start sm:self-auto">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Change Vehicle
                </button>
            </div>

            <!-- Mode 1: Live AJAX Search Input -->
            <div x-show="mode === 'search' && !selectedVehicle" class="relative">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" 
                           x-model="searchQuery" 
                           @input.debounce.250ms="searchVehicles()" 
                           @focus="if(searchQuery.length > 0) showResults = true"
                           placeholder="Type registration plate (e.g. MH 02...), customer name, phone, make or model..." 
                           class="w-full pl-10 pr-10 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm font-medium" />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg x-show="loading" x-cloak class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Live Results Dropdown -->
                <div x-show="showResults" 
                     x-cloak 
                     @click.away="showResults = false"
                     class="absolute z-30 left-0 right-0 mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200 max-h-72 overflow-y-auto divide-y divide-slate-100">
                    
                    <template x-if="results.length === 0 && !loading && searchQuery.length > 0">
                        <div class="p-4 text-center text-xs text-slate-500">
                            No vehicles found matching "<strong x-text="searchQuery"></strong>". 
                            <a href="{{ route('vehicles.create') }}" target="_blank" class="text-blue-600 font-bold hover:underline ml-1">+ Register this vehicle</a>
                        </div>
                    </template>

                    <template x-for="v in results" :key="v.id">
                        <button type="button" 
                                @click="selectVehicle(v)"
                                class="w-full text-left p-3 hover:bg-blue-50 transition flex items-center justify-between gap-3 group">
                            <div class="flex items-center gap-3">
                                <div class="px-2.5 py-1 bg-slate-100 group-hover:bg-blue-100 text-slate-900 group-hover:text-blue-900 font-mono font-bold text-xs rounded border border-slate-300 group-hover:border-blue-300">
                                    <span x-text="v.registration_number"></span>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-800" x-text="v.brand + ' ' + v.model"></div>
                                    <div class="text-[11px] text-slate-500">
                                        Owner: <span class="font-medium text-slate-700" x-text="v.customer_name"></span> 
                                        <span x-show="v.customer_phone" class="text-slate-400">• <span x-text="v.customer_phone"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-[11px] font-mono text-slate-500" x-text="v.current_odometer ? (v.current_odometer + ' km') : ''"></span>
                                <div class="text-[10px] text-blue-600 font-bold group-hover:underline">Select &rarr;</div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Mode 2: Traditional Dropdown Select -->
            <div x-show="mode === 'dropdown' && !selectedVehicle">
                <select @change="onDropdownChange($event)" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choose Vehicle from List --</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}"
                                data-reg="{{ $v->registration_number }}"
                                data-brand="{{ $v->brand->name ?? '' }}"
                                data-model="{{ $v->model->name ?? '' }}"
                                data-owner="{{ $v->customer->full_name ?? 'N/A' }}"
                                data-phone="{{ $v->customer->phone ?? '' }}"
                                data-fuel="{{ $v->fuel_type ?? 'Petrol' }}"
                                data-odometer="{{ $v->current_odometer ?? 0 }}"
                                {{ ($selectedVehicleId == $v->id || old('vehicle_id') == $v->id) ? 'selected' : '' }}>
                            {{ $v->registration_number }} - {{ $v->brand->name ?? '' }} {{ $v->model->name ?? '' }} (Owner: {{ $v->customer->full_name ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>
            @error('vehicle_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Initial Readings -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Current Odometer (km) *</label>
                <input type="number" id="odometer_reading_input" name="odometer_reading" required x-model="odometer" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
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