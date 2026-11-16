@extends('layouts.app')

@section('title', 'Service Reminders')
@section('page_title', 'Customer CRM & Service Reminders')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Filter Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('crm.reminders') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <select name="type" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                <option value="">All Reminder Types</option>
                <option value="periodic_service" {{ request('type') === 'periodic_service' ? 'selected' : '' }}>Periodic Maintenance</option>
                <option value="insurance_renewal" {{ request('type') === 'insurance_renewal' ? 'selected' : '' }}>Insurance Renewal</option>
                <option value="puc_renewal" {{ request('type') === 'puc_renewal' ? 'selected' : '' }}>PUC Renewal</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="customer_booked" {{ request('status') === 'customer_booked' ? 'selected' : '' }}>Booked</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition">Filter</button>
            @if(request()->hasAny(['type', 'status']))
                <a href="{{ route('crm.reminders') }}" class="px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900">Reset</a>
            @endif
        </form>

        <button @click="$dispatch('open-add-reminder-modal')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5 shrink-0">
            <i data-lucide="bell-plus" class="w-4 h-4"></i> Schedule Reminder
        </button>
    </div>

    <!-- Quick Schedule Reminder Form (collapsible) -->
    <div x-data="{ open: false }" @open-add-reminder-modal.window="open = true" x-show="open" x-cloak class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Schedule Service Follow-up Reminder</h3>
            <button @click="open = false" class="text-slate-400 hover:text-slate-700"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>

        <form method="POST" action="{{ route('crm.store-reminder') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Vehicle *</label>
                <select name="vehicle_id" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}">{{ $v->registration_number }} ({{ $v->customer->full_name ?? 'Owner' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Reminder Type *</label>
                <select name="reminder_type" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="periodic_service">Periodic Maintenance Service</option>
                    <option value="insurance_renewal">Motor Insurance Renewal</option>
                    <option value="puc_renewal">PUC Emission Test</option>
                    <option value="battery_check">Battery Health Inspection</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Due Date *</label>
                <input type="date" name="due_date" required value="{{ now()->addDays(14)->toDateString() }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <input type="hidden" name="channel" value="whatsapp">
                <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition">
                    Schedule Follow-up
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Vehicle</th>
                        <th class="px-6 py-3.5">Customer / Contact</th>
                        <th class="px-6 py-3.5">Reminder Scope</th>
                        <th class="px-6 py-3.5">Due Date</th>
                        <th class="px-6 py-3.5">Channel</th>
                        <th class="px-6 py-3.5">Last Dispatched</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($reminders as $rem)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('vehicles.show', $rem->vehicle_id) }}" class="font-mono font-bold text-blue-600 hover:underline">
                                    {{ $rem->vehicle->registration_number ?? 'N/A' }}
                                </a>
                                <div class="text-[11px] text-slate-500">{{ $rem->vehicle->brand->name ?? '' }} {{ $rem->vehicle->model->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('customers.show', $rem->customer_id) }}" class="font-bold text-slate-800 hover:text-blue-600">
                                    {{ $rem->customer->full_name ?? 'Customer' }}
                                </a>
                                <div class="text-[11px] text-slate-500">{{ $rem->customer->phone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-800 capitalize">{{ str_replace('_', ' ', $rem->reminder_type) }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold {{ $rem->due_date->isPast() ? 'text-rose-600' : 'text-slate-800' }}">
                                {{ $rem->due_date->format('d M Y') }}
                                @if($rem->due_date->isPast())
                                    <span class="block text-[10px] text-rose-500 font-normal">Overdue</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 uppercase font-bold text-[10px] text-emerald-700">
                                <span class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200">
                                    {{ $rem->channel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $rem->last_sent_at ? $rem->last_sent_at->format('d M, h:i A') : 'Not sent' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $rem->status === 'sent' ? 'bg-blue-100 text-blue-800' : ($rem->status === 'customer_booked' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $rem->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('crm.send-reminder', $rem->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold transition shadow-sm flex items-center gap-1 ml-auto">
                                        <i data-lucide="send" class="w-3 h-3"></i> Dispatch
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No reminders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reminders->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $reminders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

