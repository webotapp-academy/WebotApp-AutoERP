@extends('layouts.app')

@section('title', 'New Customer')
@section('page_title', 'Register New Customer')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('customers.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Directory
        </a>
    </div>

    <form method="POST" action="{{ route('customers.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf
        @if(request('return_to'))
            <input type="hidden" name="return_to" value="{{ request('return_to') }}">
        @endif

        <div class="border-b border-slate-100 pb-4">
            <h3 class="text-base font-bold text-slate-900">Customer Classification</h3>
            <p class="text-xs text-slate-500">Select whether customer is an individual owner, corporate client, or fleet account</p>
            
            <div class="grid grid-cols-3 gap-4 mt-4">
                <label class="border border-slate-200 rounded-xl p-3.5 flex items-center gap-3 cursor-pointer hover:border-blue-500 transition">
                    <input type="radio" name="customer_type" value="individual" checked class="text-blue-600 focus:ring-blue-500">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Individual</div>
                        <div class="text-[11px] text-slate-500">Personal car / bike owner</div>
                    </div>
                </label>
                <label class="border border-slate-200 rounded-xl p-3.5 flex items-center gap-3 cursor-pointer hover:border-blue-500 transition">
                    <input type="radio" name="customer_type" value="corporate" class="text-blue-600 focus:ring-blue-500">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Corporate</div>
                        <div class="text-[11px] text-slate-500">Company car / staff billing</div>
                    </div>
                </label>
                <label class="border border-slate-200 rounded-xl p-3.5 flex items-center gap-3 cursor-pointer hover:border-blue-500 transition">
                    <input type="radio" name="customer_type" value="fleet" class="text-blue-600 focus:ring-blue-500">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Fleet Account</div>
                        <div class="text-[11px] text-slate-500">Logistics & commercial taxis</div>
                    </div>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">First Name / Contact Person *</label>
                <input type="text" name="first_name" required value="{{ old('first_name') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                @error('first_name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Company / Organization Name (Optional)</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Acme Logistics Pvt Ltd" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Mobile Phone Number *</label>
                <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="+91 98200 12345" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                @error('phone') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">GSTIN (For Tax Invoices)</label>
                <input type="text" name="gstin" value="{{ old('gstin') }}" placeholder="27AABCU9603R1ZM" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">PAN Number</label>
                <input type="text" name="pan" value="{{ old('pan') }}" placeholder="ABCDE1234F" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono uppercase" />
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Street Address</label>
            <textarea name="address" rows="2" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('address') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">City</label>
                <input type="text" name="city" value="{{ old('city', 'Mumbai') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">State</label>
                <input type="text" name="state" value="{{ old('state', 'Maharashtra') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">PIN Code</label>
                <input type="text" name="pincode" value="{{ old('pincode', '400001') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('customers.index') }}" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Save Customer
            </button>
        </div>
    </form>
</div>
@endsection

