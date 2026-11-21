@extends('layouts.app')

@section('title', 'Book Appointment')
@section('page_title', 'Book Customer Service Appointment')

@section('content')
<div x-data="{
    showCustomerModal: false,
    showVehicleSection: true,
    newCust: {
        first_name: '',
        last_name: '',
        phone: '',
        email: '',
        customer_type: 'individual',
        company_name: '',
        city: '',
        registration_number: '',
        brand_id: '',
        model_name: '',
        fuel_type: 'Petrol'
    },
    submitting: false,
    errorMessage: '',
    successMessage: '',
    async submitQuickCustomer() {
        this.submitting = true;
        this.errorMessage = '';
        this.successMessage = '';
        try {
            const resp = await fetch('{{ route('customers.quick') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.newCust)
            });
            const data = await resp.json();
            if (!resp.ok) {
                let errText = data.message || 'Error creating customer';
                if (data.errors) {
                    errText = Object.values(data.errors).flat().join(' ');
                }
                this.errorMessage = errText;
                this.submitting = false;
                return;
            }

            // Append and select customer in dropdown
            const custSelect = document.getElementById('appointment-customer-select');
            const opt = document.createElement('option');
            opt.value = data.customer.id;
            opt.textContent = `${data.customer.name} (${data.customer.phone})`;
            opt.selected = true;
            custSelect.appendChild(opt);
            custSelect.value = data.customer.id;

            // If vehicle returned, append and select
            if (data.vehicle) {
                const vehSelect = document.getElementById('appointment-vehicle-select');
                const vOpt = document.createElement('option');
                vOpt.value = data.vehicle.id;
                vOpt.textContent = data.vehicle.display;
                vOpt.selected = true;
                vehSelect.appendChild(vOpt);
                vehSelect.value = data.vehicle.id;
            }

            this.successMessage = data.message;
            setTimeout(() => {
                this.showCustomerModal = false;
                this.successMessage = '';
                this.newCust = {
                    first_name: '',
                    last_name: '',
                    phone: '',
                    email: '',
                    customer_type: 'individual',
                    company_name: '',
                    city: '',
                    registration_number: '',
                    brand_id: '',
                    model_name: '',
                    fuel_type: 'Petrol'
                };
            }, 600);
        } catch (e) {
            this.errorMessage = 'Network or server error: ' + e.message;
        } finally {
            this.submitting = false;
        }
    }
}" class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('appointments.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Schedule
        </a>

        <!-- Quick Customer Creation Trigger -->
        <button type="button" @click="showCustomerModal = true; errorMessage = '';" class="px-3.5 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 text-xs font-bold rounded-lg transition flex items-center gap-1.5 shadow-sm">
            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i> + New Customer
        </button>
    </div>

    <!-- Booking Form -->
    <form method="POST" action="{{ route('appointments.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-semibold text-slate-700 uppercase">Customer *</label>
                    <button type="button" @click="showCustomerModal = true; errorMessage = '';" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition">
                        <i data-lucide="user-plus" class="w-3.5 h-3.5"></i> + New Customer
                    </button>
                </div>
                <select id="appointment-customer-select" name="customer_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ ($selectedCustomerId == $c->id || old('customer_id') == $c->id) ? 'selected' : '' }}>
                            {{ $c->full_name }} ({{ $c->phone }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-semibold text-slate-700 uppercase">Vehicle *</label>
                    <a href="{{ route('vehicles.create') }}" target="_blank" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1 transition" title="Add vehicle in new tab">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> + New Vehicle
                    </a>
                </div>
                <select id="appointment-vehicle-select" name="vehicle_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Select Vehicle --</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ ($selectedVehicleId == $v->id || old('vehicle_id') == $v->id) ? 'selected' : '' }}>
                            {{ $v->registration_number }} - {{ $v->brand->name ?? '' }} {{ $v->model->name ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('vehicle_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Appointment Date *</label>
                <input type="date" name="appointment_date" required value="{{ old('appointment_date', today()->toDateString()) }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                @error('appointment_date') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Time Slot *</label>
                <input type="time" name="appointment_time" required value="{{ old('appointment_time', '10:00') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                @error('appointment_time') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
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

    <!-- Quick Add Customer Modal -->
    <div x-show="showCustomerModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="showCustomerModal" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             @click="showCustomerModal = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Content Card -->
        <div x-show="showCustomerModal" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0 scale-95 translate-y-2" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
             x-transition:leave-end="opacity-0 scale-95 translate-y-2" 
             class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-xl w-full p-6 sm:p-7 z-10 my-8">

            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Quick Register Customer</h3>
                        <p class="text-xs text-slate-500">Add a new customer and link them immediately to this appointment</p>
                    </div>
                </div>
                <button type="button" @click="showCustomerModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Error / Success Alerts -->
            <div x-show="errorMessage" x-cloak class="mt-4 p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                <span x-text="errorMessage"></span>
            </div>
            <div x-show="successMessage" x-cloak class="mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-xs flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
                <span x-text="successMessage"></span>
            </div>

            <!-- Modal Form Body -->
            <form @submit.prevent="submitQuickCustomer" class="mt-4 space-y-4">
                
                <!-- Customer Classification -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1.5">Account Type</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg border text-xs font-semibold cursor-pointer transition"
                               :class="newCust.customer_type === 'individual' ? 'bg-blue-50 border-blue-500 text-blue-700 font-bold' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                            <input type="radio" value="individual" x-model="newCust.customer_type" class="hidden" />
                            <span>Individual</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg border text-xs font-semibold cursor-pointer transition"
                               :class="newCust.customer_type === 'corporate' ? 'bg-blue-50 border-blue-500 text-blue-700 font-bold' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                            <input type="radio" value="corporate" x-model="newCust.customer_type" class="hidden" />
                            <span>Corporate</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg border text-xs font-semibold cursor-pointer transition"
                               :class="newCust.customer_type === 'fleet' ? 'bg-blue-50 border-blue-500 text-blue-700 font-bold' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                            <input type="radio" value="fleet" x-model="newCust.customer_type" class="hidden" />
                            <span>Fleet</span>
                        </label>
                    </div>
                </div>

                <!-- Names -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">First Name / Contact Person *</label>
                        <input type="text" x-model="newCust.first_name" required placeholder="e.g. Vikram" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Last Name</label>
                        <input type="text" x-model="newCust.last_name" placeholder="e.g. Patil" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                </div>

                <!-- Company name if corporate -->
                <div x-show="newCust.customer_type !== 'individual'" x-cloak>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Company / Organization Name</label>
                    <input type="text" x-model="newCust.company_name" placeholder="e.g. Patil Logistics Pvt Ltd" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <!-- Contact -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Mobile Phone Number *</label>
                        <input type="text" x-model="newCust.phone" required placeholder="e.g. 9820011223" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email Address</label>
                        <input type="email" x-model="newCust.email" placeholder="e.g. vikram@example.com" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                </div>

                <!-- City -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">City / Area</label>
                    <input type="text" x-model="newCust.city" placeholder="e.g. Mumbai, Andheri East" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <!-- Vehicle Details Accordion (Optional) -->
                <div class="pt-2 border-t border-slate-100">
                    <button type="button" @click="showVehicleSection = !showVehicleSection" class="flex items-center justify-between w-full text-xs font-bold text-slate-700 py-1.5 focus:outline-none">
                        <span class="flex items-center gap-1.5 text-blue-600">
                            <i data-lucide="car" class="w-4 h-4"></i> Also Link Vehicle Directly (Optional)
                        </span>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="showVehicleSection ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="showVehicleSection" x-collapse class="mt-3 space-y-3 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Vehicle Plate / Registration Number</label>
                                <input type="text" x-model="newCust.registration_number" placeholder="e.g. MH 02 AB 1234" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 uppercase font-mono font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Brand / Make</label>
                                <select x-model="newCust.brand_id" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="">-- Select Brand --</option>
                                    @foreach($brands as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Model Name</label>
                                <input type="text" x-model="newCust.model_name" placeholder="e.g. Creta / City / Swift" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Fuel Type</label>
                                <select x-model="newCust.fuel_type" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="Petrol">Petrol</option>
                                    <option value="Diesel">Diesel</option>
                                    <option value="Electric">Electric (EV)</option>
                                    <option value="Hybrid">Hybrid</option>
                                    <option value="CNG">CNG</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <a href="{{ route('customers.create', ['return_to' => 'appointments.create']) }}" class="text-[11px] font-semibold text-slate-500 hover:text-blue-600 underline">
                        Open Full Profile Form (GSTIN/PAN/Address) &rarr;
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showCustomerModal = false" class="px-3.5 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit" :disabled="submitting" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5">
                            <span x-show="!submitting" class="flex items-center gap-1.5">
                                <i data-lucide="check" class="w-4 h-4"></i> Save & Select Customer
                            </span>
                            <span x-show="submitting" x-cloak class="flex items-center gap-1.5">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg> Saving...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection