@extends('layouts.app')

@section('title', $customer->full_name)
@section('page_title', 'Customer Profile: ' . $customer->full_name)

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('customers.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Customers
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('vehicles.create', ['customer_id' => $customer->id]) }}" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                <i data-lucide="car" class="w-4 h-4"></i> Add Vehicle
            </a>
            <a href="{{ route('job-cards.create', ['customer_id' => $customer->id]) }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                <i data-lucide="file-plus" class="w-4 h-4"></i> New Job Card
            </a>
            <a href="{{ route('customers.edit', $customer->id) }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                <i data-lucide="edit" class="w-4 h-4"></i> Edit
            </a>
        </div>
    </div>

    <!-- Profile Overview Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-100 pb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 border border-blue-200 text-blue-600 font-bold text-2xl flex items-center justify-center">
                    {{ strtoupper(substr($customer->first_name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-slate-900">{{ $customer->full_name }}</h2>
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full uppercase {{ $customer->customer_type === 'fleet' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $customer->customer_type }}
                        </span>
                    </div>
                    @if($customer->company_name)
                        <div class="text-xs font-semibold text-slate-600 mt-0.5">{{ $customer->company_name }}</div>
                    @endif
                    <div class="text-xs text-slate-400 mt-1">Customer ID: #CUST-{{ sprintf('%05d', $customer->id) }} • Joined {{ $customer->created_at->format('M Y') }}</div>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="text-right">
                    <div class="text-[11px] font-semibold uppercase text-slate-400 tracking-wider">Outstanding Balance</div>
                    <div class="text-2xl font-bold {{ $customer->outstanding_balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        ₹{{ number_format($customer->outstanding_balance, 2) }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-[11px] font-semibold uppercase text-slate-400 tracking-wider">Credit Limit</div>
                    <div class="text-lg font-bold text-slate-700">
                        ₹{{ number_format($customer->credit_limit, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact & GST details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-6 text-xs">
            <div>
                <span class="text-slate-400 font-medium">Primary Phone:</span>
                <div class="font-bold text-slate-800 mt-0.5">{{ $customer->phone }}</div>
            </div>
            <div>
                <span class="text-slate-400 font-medium">Email Address:</span>
                <div class="font-bold text-slate-800 mt-0.5">{{ $customer->email ?? 'Not provided' }}</div>
            </div>
            <div>
                <span class="text-slate-400 font-medium">GSTIN:</span>
                <div class="font-mono font-bold text-slate-800 mt-0.5">{{ $customer->gstin ?? 'Unregistered' }}</div>
            </div>
            <div>
                <span class="text-slate-400 font-medium">Address:</span>
                <div class="text-slate-700 mt-0.5">{{ $customer->address ?? 'N/A' }}, {{ $customer->city ?? '' }} {{ $customer->pincode ?? '' }}</div>
            </div>
        </div>
    </div>

    <!-- Vehicles Registered -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Registered Vehicles</h3>
                <p class="text-xs text-slate-500">Fleet and personal automobiles associated with this account</p>
            </div>
            <a href="{{ route('vehicles.create', ['customer_id' => $customer->id]) }}" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-semibold rounded-lg transition">
                + Register New Vehicle
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($customer->vehicles as $veh)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-blue-400 transition shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold bg-white text-slate-800 rounded border border-slate-200">
                            {{ $veh->registration_number }}
                        </span>
                        <span class="text-[10px] font-bold uppercase text-slate-500">{{ $veh->fuel_type }}</span>
                    </div>
                    <div class="font-bold text-slate-900 text-sm">
                        {{ $veh->brand->name ?? '' }} {{ $veh->model->name ?? '' }}
                    </div>
                    <div class="text-xs text-slate-500 mb-3">Year: {{ $veh->model_year ?? 'N/A' }} • Color: {{ $veh->color ?? 'Standard' }}</div>

                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs text-slate-600">
                        <span>Odo: {{ number_format($veh->current_odometer) }} km</span>
                        <a href="{{ route('vehicles.show', $veh->id) }}" class="text-blue-600 font-semibold hover:underline">View History &rarr;</a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-6 text-xs text-slate-400 italic">
                    No vehicles registered for this customer yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Job Cards History -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Service History & Job Cards</h3>
            <p class="text-xs text-slate-500">Record of all past and active repairs for this customer</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3">Job Card #</th>
                        <th class="px-6 py-3">Vehicle</th>
                        <th class="px-6 py-3">Check-in Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($customer->jobCards as $jc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-3.5 font-mono font-bold text-blue-600">
                                <a href="{{ route('job-cards.show', $jc->id) }}">{{ $jc->job_card_number }}</a>
                            </td>
                            <td class="px-6 py-3.5 font-bold text-slate-800">
                                {{ $jc->vehicle->registration_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-600">
                                {{ $jc->check_in_time->format('d M Y, h:i A') }}
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
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                No service records found for this customer.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
