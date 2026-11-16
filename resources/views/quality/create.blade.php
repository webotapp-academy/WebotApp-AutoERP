@extends('layouts.app')

@section('title', 'Perform Quality Inspection')
@section('page_title', 'Final Quality Inspection & Road Test Audit')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('quality.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Quality Control
        </a>
    </div>

    <form method="POST" action="{{ route('quality.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Select Job Card For Quality Audit *</label>
            <select name="job_card_id" required class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- Choose Job Card --</option>
                @foreach($jobCards as $jc)
                    <option value="{{ $jc->id }}" {{ ($selectedJobCardId == $jc->id || old('job_card_id') == $jc->id) ? 'selected' : '' }}>
                        {{ $jc->job_card_number }} - {{ $jc->vehicle->registration_number ?? 'Vehicle' }} ({{ $jc->customer->full_name ?? 'Customer' }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Section 17.1 Quality Checklist -->
        <div class="border border-slate-200 rounded-xl p-5 space-y-3 bg-slate-50/50">
            <h4 class="text-xs font-bold uppercase text-slate-800 tracking-wider">Mandatory Safety & Work Verification Points</h4>
            
            <div class="space-y-2.5 text-xs">
                <label class="flex items-center text-slate-800 font-semibold cursor-pointer">
                    <input type="checkbox" name="road_test_done" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2.5">
                    Road Test Completed (No steering pull, no abnormal drivetrain vibration)
                </label>
                <label class="flex items-center text-slate-800 font-semibold cursor-pointer">
                    <input type="checkbox" name="brake_test_ok" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2.5">
                    Brake Response & Pedal Firmness Verified (No squeals or sponginess)
                </label>
                <label class="flex items-center text-slate-800 font-semibold cursor-pointer">
                    <input type="checkbox" name="engine_sound_ok" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2.5">
                    Engine Idle Sound & Fluid Levels Re-checked
                </label>
                <label class="flex items-center text-slate-800 font-semibold cursor-pointer">
                    <input type="checkbox" name="ac_cooling_ok" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2.5">
                    Air Conditioning Cooling & Vent Airflow Optimal
                </label>
                <label class="flex items-center text-slate-800 font-semibold cursor-pointer">
                    <input type="checkbox" name="wheel_nut_torqued" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2.5">
                    Wheel Lug Nuts Torqued to Manufacturer Specification with Torque Wrench
                </label>
                <label class="flex items-center text-slate-800 font-semibold cursor-pointer">
                    <input type="checkbox" name="cleaning_washing_ok" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2.5">
                    Vehicle Washed, Vacuumed, Glass Cleaned & Paper Floor Mats Placed
                </label>
            </div>
        </div>

        <!-- Overall Verdict -->
        <div class="border-t border-slate-100 pt-4">
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-2">Final Quality Verdict *</label>
            <div class="grid grid-cols-2 gap-4">
                <label class="border-2 border-emerald-500 bg-emerald-50/50 rounded-xl p-4 flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="passed" value="1" checked class="text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <div class="text-xs font-bold text-emerald-900">PASS - Approve for Delivery</div>
                        <div class="text-[11px] text-emerald-700">Vehicle is road-worthy. Generate Gate Pass.</div>
                    </div>
                </label>

                <label class="border border-rose-300 bg-rose-50/50 rounded-xl p-4 flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="passed" value="0" class="text-rose-600 focus:ring-rose-500">
                    <div>
                        <div class="text-xs font-bold text-rose-900">FAIL - Rework Required</div>
                        <div class="text-[11px] text-rose-700">Send back to technician for corrections.</div>
                    </div>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Rework Notes / Inspector Remarks</label>
            <textarea name="rework_notes" rows="2" placeholder="Any specific adjustment needed..." class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('quality.index') }}" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4"></i> Submit Quality Inspection
            </button>
        </div>
    </form>
</div>
@endsection
