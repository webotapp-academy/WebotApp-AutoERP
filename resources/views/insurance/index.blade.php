@extends('layouts.app')

@section('title', 'Insurance Claims')
@section('page_title', 'Insurance Claim & Surveyor Management')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h3 class="text-base font-bold text-slate-900">Accident Insurance Claims</h3>
            <p class="text-xs text-slate-500">Track surveyor visits, claim approvals, insurance liabilities, and cashless settlements</p>
        </div>
        <a href="{{ route('insurance.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5">
            <i data-lucide="shield" class="w-4 h-4"></i> Register New Claim
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Claim #</th>
                        <th class="px-6 py-3.5">Insurance Company</th>
                        <th class="px-6 py-3.5">Policy Number</th>
                        <th class="px-6 py-3.5">Vehicle Plate</th>
                        <th class="px-6 py-3.5">Surveyor</th>
                        <th class="px-6 py-3.5">Estimated Claim (₹)</th>
                        <th class="px-6 py-3.5">Approved Amount (₹)</th>
                        <th class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($claims as $clm)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                {{ $clm->claim_number }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $clm->insuranceCompany->name ?? 'Insurance Co.' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600">
                                {{ $clm->policy_number }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                {{ $clm->vehicle->registration_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $clm->surveyor_name ?? 'Surveyor TBD' }}
                            </td>
                            <td class="px-6 py-4 font-mono">
                                ₹{{ number_format($clm->estimated_claim_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-emerald-600">
                                ₹{{ number_format($clm->approved_claim_amount, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase {{ $clm->status === 'settled' ? 'bg-emerald-100 text-emerald-800' : ($clm->status === 'approved' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ str_replace('_', ' ', $clm->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No insurance claims recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
