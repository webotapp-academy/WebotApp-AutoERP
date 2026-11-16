@extends('layouts.app')

@section('title', 'New Insurance Claim')
@section('page_title', 'Register Accidental Insurance Claim')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('insurance.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Claims
        </a>
    </div>

    <form method="POST" action="{{ route('insurance.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Select Job Card *</label>
                <select name="job_card_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Choose Job Card --</option>
                    @foreach($jobCards as $jc)
                        <option value="{{ $jc->id }}">{{ $jc->job_card_number }} - {{ $jc->vehicle->registration_number ?? 'Vehicle' }} ({{ $jc->customer->full_name ?? 'Customer' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Insurance Company *</label>
                <select name="insurance_company_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Select Insurance Provider --</option>
                    @foreach($insuranceCompanies as $ic)
                        <option value="{{ $ic->id }}">{{ $ic->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Policy Number *</label>
                <input type="text" name="policy_number" required value="{{ old('policy_number', 'POL-IN-123456') }}" class="w-full px-3 py-2 text-xs font-mono uppercase rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Claim Date *</label>
                <input type="date" name="claim_date" required value="{{ today()->toDateString() }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Estimated Claim Amount (₹) *</label>
                <input type="number" step="0.01" name="estimated_claim_amount" required value="25000" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Assigned Surveyor Name</label>
                <input type="text" name="surveyor_name" placeholder="e.g. S. K. Sharma" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Surveyor Phone</label>
                <input type="text" name="surveyor_phone" placeholder="+91 98333 12345" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Accident Damage Description & Remarks</label>
            <textarea name="remarks" rows="2" placeholder="Front bumper, headlight and bonnet impact damage..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('insurance.index') }}" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Submit Claim
            </button>
        </div>
    </form>
</div>
@endsection

