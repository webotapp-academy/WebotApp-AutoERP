@extends('layouts.app')

@section('title', 'Mechanics & Technicians')
@section('page_title', 'Mechanics & Floor Technicians')

@section('content')
<div x-data="{
    showAddModal: false,
    showEditModal: false,
    editMech: {
        id: null,
        name: '',
        email: '',
        phone: '',
        designation: '',
        hourly_rate: 350,
        status: 'active',
        updateUrl: ''
    },
    openEdit(m) {
        this.editMech = {
            id: m.id,
            name: m.name,
            email: m.email,
            phone: m.phone,
            designation: m.employee ? m.employee.designation : 'Master Technician',
            hourly_rate: m.employee ? m.employee.hourly_rate : 350,
            status: m.employee ? m.employee.status : (m.status === 'active' ? 'active' : 'inactive'),
            updateUrl: '{{ url('/mechanics') }}/' + m.id
        };
        this.showEditModal = true;
    }
}" class="space-y-6">

    <!-- Top Action & Title Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-base font-bold text-slate-900">Workshop Floor Technicians</h2>
                <span class="px-2.5 py-0.5 text-xs font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200 rounded-full">
                    {{ $mechanics->count() }} Staff
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Manage technician profiles, hourly billable rates, floor dispatch and job card assignments</p>
        </div>

        <button type="button" 
                @click="showAddModal = true" 
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5 self-start sm:self-auto">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Add Technician
        </button>
    </div>

    <!-- Active Repair Timers (Section 12.4 in todo.txt) -->
    @if($activeTimeLogs->count() > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 mb-3 flex items-center gap-1.5">
                <i data-lucide="clock" class="w-4 h-4 text-amber-600 animate-spin"></i> Active Repair Timers Running on Floor
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($activeTimeLogs as $tl)
                    <div class="bg-white p-3.5 rounded-xl border border-amber-200 shadow-sm flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-slate-800">{{ $tl->mechanic->name ?? 'Technician' }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $tl->jobCard->job_card_number }} • {{ $tl->jobCard->vehicle->registration_number ?? 'Vehicle' }}</div>
                            <div class="text-[10px] text-amber-600 font-medium mt-1">Started {{ $tl->started_at->diffForHumans() }}</div>
                        </div>
                        <form method="POST" action="{{ route('mechanics.stop-timer', $tl->id) }}">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded text-[11px] font-bold transition">
                                Stop Timer
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Technicians Roster Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($mechanics as $m)
            @php
                $status = $m->employee->status ?? ($m->status === 'active' ? 'active' : 'inactive');
                $badgeStyle = match($status) {
                    'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'on_leave' => 'bg-amber-100 text-amber-800 border-amber-200',
                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                };
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4 hover:border-blue-300 transition flex flex-col justify-between">
                <div>
                    <!-- Card Top Header -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 text-amber-600 font-bold flex items-center justify-center shrink-0">
                                <i data-lucide="wrench" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base leading-snug">{{ $m->name }}</h3>
                                <div class="text-xs text-blue-600 font-semibold">{{ $m->employee->designation ?? 'Master Technician' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $m->employee->employee_code ?? 'EMP-' . $m->id }} • {{ $m->phone }}</div>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <span class="px-2 py-0.5 text-[10px] uppercase font-bold rounded-full border {{ $badgeStyle }}">
                            {{ str_replace('_', ' ', $status) }}
                        </span>
                    </div>

                    <!-- Stats Bar -->
                    <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50 rounded-xl text-xs mt-4">
                        <div>
                            <span class="text-slate-400 font-medium">Hourly Charge:</span>
                            <div class="font-mono font-bold text-slate-800 mt-0.5">₹{{ number_format($m->employee->hourly_rate ?? 0, 2) }}/hr</div>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium">Active Jobs:</span>
                            <div class="font-bold text-blue-600 mt-0.5">{{ $m->jobCardsAssigned->count() }} Vehicle(s)</div>
                        </div>
                    </div>

                    <!-- Active Assigned Vehicles -->
                    <div class="mt-4">
                        <div class="text-[11px] font-bold uppercase text-slate-400 tracking-wider mb-2">Currently Assigned Jobs</div>
                        <div class="space-y-1.5 max-h-36 overflow-y-auto">
                            @forelse($m->jobCardsAssigned as $jc)
                                <a href="{{ route('job-cards.show', $jc->id) }}" class="flex items-center justify-between p-2 rounded-lg bg-slate-50 hover:bg-blue-50 text-xs text-slate-700 transition">
                                    <span class="font-mono font-bold text-blue-600">{{ $jc->job_card_number }}</span>
                                    <span class="font-bold text-slate-800">{{ $jc->vehicle->registration_number ?? 'Car' }}</span>
                                    <span class="px-1.5 py-0.5 text-[9px] uppercase font-bold rounded bg-slate-200 text-slate-700">{{ str_replace('_', ' ', $jc->status) }}</span>
                                </a>
                            @empty
                                <div class="text-[11px] text-slate-400 italic py-1">No active job cards currently assigned. Available for next dispatch.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Card Bottom Actions: Edit & Delete -->
                <div class="flex items-center justify-between pt-4 border-t border-slate-100 text-xs mt-4">
                    <span class="text-[11px] text-slate-400 truncate max-w-[140px]">{{ $m->email }}</span>
                    <div class="flex items-center gap-1.5">
                        <!-- Edit Button -->
                        <button type="button" 
                                @click="openEdit(@json($m))" 
                                class="px-2.5 py-1 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition flex items-center gap-1">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-slate-500"></i> Edit
                        </button>

                        <!-- Delete Button -->
                        <form method="POST" action="{{ route('mechanics.destroy', $m->id) }}" onsubmit="return confirm('Are you sure you want to remove {{ addslashes($m->name) }} from the technician roster?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2.5 py-1 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition flex items-center gap-1" title="Delete Technician">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200">
                <i data-lucide="users" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                <h4 class="text-sm font-bold text-slate-700">No technicians registered yet</h4>
                <p class="text-xs text-slate-400 mt-1">Click the button above to add mechanics to your workshop team.</p>
            </div>
        @endforelse
    </div>

    <!-- Add Technician Modal -->
    <div x-show="showAddModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         role="dialog" 
         aria-modal="true">
        
        <div x-show="showAddModal" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             @click="showAddModal = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

        <div x-show="showAddModal" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0 scale-95 translate-y-2" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
             x-transition:leave-end="opacity-0 scale-95 translate-y-2" 
             class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full p-6 sm:p-7 z-10 my-8">

            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Add Technician / Mechanic</h3>
                        <p class="text-xs text-slate-500">Register a new floor mechanic and assign their hourly rate</p>
                    </div>
                </div>
                <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('mechanics.store') }}" class="mt-4 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Full Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Ramesh Kulkarni" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Mobile Phone *</label>
                        <input type="text" name="phone" required placeholder="e.g. 9820011223" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email Address *</label>
                        <input type="email" name="email" required placeholder="e.g. ramesh@automobile.com" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Password (Min 6)</label>
                        <input type="password" name="password" placeholder="Default: mechanic123" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Designation / Role *</label>
                        <select name="designation" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="Master Diagnostic Technician">Master Diagnostic Technician</option>
                            <option value="Senior Engine & Mechanical Specialist" selected>Senior Engine Specialist</option>
                            <option value="Auto Electrician & EV Technician">Auto Electrician & EV Technician</option>
                            <option value="Brake, Suspension & Alignment Expert">Brake & Suspension Expert</option>
                            <option value="AC & Climate Control Specialist">AC & Climate Control Specialist</option>
                            <option value="Denting & Paint Booth Specialist">Denting & Paint Booth Specialist</option>
                            <option value="General Service Mechanic">General Service Mechanic</option>
                            <option value="Apprentice / Trainee">Apprentice / Trainee</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Hourly Billing Rate (₹) *</label>
                        <input type="number" step="10" name="hourly_rate" required value="400" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="active" selected>Active & Ready for Dispatch</option>
                        <option value="on_leave">On Leave</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i> Save Technician
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Technician Modal -->
    <div x-show="showEditModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         role="dialog" 
         aria-modal="true">
        
        <div x-show="showEditModal" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             @click="showEditModal = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

        <div x-show="showEditModal" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0 scale-95 translate-y-2" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
             x-transition:leave-end="opacity-0 scale-95 translate-y-2" 
             class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full p-6 sm:p-7 z-10 my-8">

            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i data-lucide="edit-3" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Edit Technician Profile</h3>
                        <p class="text-xs text-slate-500">Update mechanic details, hourly billing rate and status</p>
                    </div>
                </div>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" :action="editMech.updateUrl" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Full Name *</label>
                        <input type="text" name="name" required x-model="editMech.name" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Mobile Phone *</label>
                        <input type="text" name="phone" required x-model="editMech.phone" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email Address *</label>
                        <input type="email" name="email" required x-model="editMech.email" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Reset Password</label>
                        <input type="password" name="password" placeholder="Leave blank to keep" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Designation / Role *</label>
                        <input type="text" name="designation" required x-model="editMech.designation" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Hourly Billing Rate (₹) *</label>
                        <input type="number" step="10" name="hourly_rate" required x-model="editMech.hourly_rate" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Status *</label>
                    <select name="status" required x-model="editMech.status" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="active">Active & Ready for Dispatch</option>
                        <option value="on_leave">On Leave</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i> Update Technician
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
