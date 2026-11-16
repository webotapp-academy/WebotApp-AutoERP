@extends('layouts.app')

@section('title', 'Workshop Settings & GST Configuration')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">System Settings & Compliance</h1>
            <p class="text-sm text-gray-500">Configure garage branding, Indian GSTIN registration, invoice numbering rules, and default tax rates.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Settings Form -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h2 class="font-bold text-gray-900 flex items-center space-x-2">
                    <i class="fa-solid fa-sliders text-indigo-600"></i>
                    <span>Workshop Profile & GST Settings</span>
                </h2>
                <span class="text-xs bg-emerald-100 text-emerald-800 font-semibold px-2 py-0.5 rounded">GST Compliant</span>
            </div>
            <form action="{{ route('settings.update') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-600 border-b pb-1">1. Business Profile</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Company / Workshop Name *</label>
                            <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Support Email *</label>
                            <input type="email" name="email" value="{{ old('email', $company->email ?? '') }}" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Contact Phone *</label>
                            <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">GSTIN Number *</label>
                            <input type="text" name="gstin" value="{{ old('gstin', $company->gstin ?? '') }}" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border font-mono uppercase">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">PAN Number</label>
                            <input type="text" name="pan_number" value="{{ old('pan_number', $company->pan_number ?? '') }}" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border font-mono uppercase">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Registered Address *</label>
                        <textarea name="address" rows="2" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">{{ old('address', $company->address ?? '') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">City *</label>
                            <input type="text" name="city" value="{{ old('city', $company->city ?? '') }}" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">State * (determines CGST/SGST)</label>
                            <input type="text" name="state" value="{{ old('state', $company->state ?? '') }}" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Postal Code *</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $company->postal_code ?? '') }}" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">
                        </div>
                    </div>
                </div>

                <div class="space-y-4 pt-4 border-t">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-600 border-b pb-1">2. Billing & Invoicing Parameters</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Invoice Prefix *</label>
                            <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $company->invoice_prefix ?? 'INV-') }}" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border font-mono uppercase">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Default GST Rate (%) *</label>
                            <input type="number" step="0.5" name="default_tax_rate" value="{{ old('default_tax_rate', $settings['default_tax_rate'] ?? 18) }}" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Currency Symbol *</label>
                            <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '₹') }}" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Invoice Footer Terms & Note</label>
                        <textarea name="invoice_footer_note" rows="2" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border">{{ old('invoice_footer_note', $settings['invoice_footer_note'] ?? 'Thank you for choosing ABC Automobile Care! Drive Safe.') }}</textarea>
                    </div>
                </div>

                <div class="pt-4 border-t flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 shadow flex items-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Info & Branches Overview -->
        <div class="space-y-6">
            <div class="bg-indigo-900 text-white p-6 rounded-xl shadow-sm">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-800 flex items-center justify-center text-amber-400">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <h3 class="font-bold">Indian GST Compliance</h3>
                </div>
                <p class="text-xs text-indigo-200 leading-relaxed">
                    Under Indian GST Law:
                </p>
                <ul class="text-xs text-indigo-200 space-y-2 mt-2">
                    <li class="flex items-start space-x-2">
                        <i class="fa-solid fa-circle-check text-emerald-400 mt-0.5"></i>
                        <span><strong>Intra-State:</strong> When customer state matches workshop state (e.g., Maharashtra), tax splits into equal <strong>9% CGST + 9% SGST</strong>.</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i class="fa-solid fa-circle-check text-emerald-400 mt-0.5"></i>
                        <span><strong>Inter-State:</strong> When customer state differs, full <strong>18% IGST</strong> applies.</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i class="fa-solid fa-circle-check text-emerald-400 mt-0.5"></i>
                        <span><strong>HSN / SAC:</strong> Spare parts require HSN code 8708; labour services require SAC code 998729.</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-bold text-gray-900 mb-3 flex items-center justify-between">
                    <span>Registered Branches</span>
                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded font-mono">{{ $branches->count() }} active</span>
                </h3>
                <div class="space-y-3">
                    @foreach($branches as $b)
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="flex items-center justify-between font-semibold text-sm text-gray-900">
                            <span>{{ $b->name }}</span>
                            <span class="text-xs px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded">Open</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1 flex items-center space-x-2">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $b->city }}, {{ $b->state }}</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1 flex items-center space-x-2">
                            <i class="fa-solid fa-phone"></i>
                            <span>{{ $b->phone }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

