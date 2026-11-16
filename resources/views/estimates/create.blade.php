@extends('layouts.app')

@section('title', 'New Estimate')
@section('page_title', 'Generate Repair Estimate')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('estimates.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Estimates
        </a>
    </div>

    <form method="POST" action="{{ route('estimates.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Select Job Card *</label>
            <select name="job_card_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Choose Job Card --</option>
                @foreach($jobCards as $jc)
                    <option value="{{ $jc->id }}" {{ ($selectedJobCardId == $jc->id || old('job_card_id') == $jc->id) ? 'selected' : '' }}>
                        {{ $jc->job_card_number }} - {{ $jc->vehicle->registration_number ?? 'Vehicle' }} ({{ $jc->customer->full_name ?? 'Customer' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Subtotal Labour (₹) *</label>
                <input type="number" step="0.01" name="subtotal_labour" required value="{{ old('subtotal_labour', '1500') }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Subtotal Parts (₹) *</label>
                <input type="number" step="0.01" name="subtotal_parts" required value="{{ old('subtotal_parts', '2500') }}" class="w-full px-3 py-2 text-xs font-mono font-bold rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Special Discount (₹)</label>
                <input type="number" step="0.01" name="discount_amount" value="{{ old('discount_amount', '0') }}" class="w-full px-3 py-2 text-xs font-mono rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Customer / Estimation Notes</label>
            <textarea name="customer_notes" rows="3" placeholder="Estimate valid for 7 days. Quote includes parts and labour charges with 18% GST..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('customer_notes') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('estimates.index') }}" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Generate Estimate
            </button>
        </div>
    </form>
</div>
@endsection
