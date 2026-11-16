<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Branch;
use App\Models\SystemSetting;

class SettingController extends Controller
{
    public function index()
    {
        $company = Company::first();
        $branches = Branch::all();
        $settings = SystemSetting::pluck('value', 'key');

        return view('settings.index', compact('company', 'branches', 'settings'));
    }

    public function update(Request $request)
    {
        $company = Company::first();

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'required|string|max:30',
            'gstin' => 'required|string|max:20',
            'pan_number' => 'nullable|string|max:15',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'currency_symbol' => 'required|string|max:10',
            'invoice_prefix' => 'required|string|max:10',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'invoice_footer_note' => 'nullable|string',
        ]);

        if ($company) {
            $company->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gstin' => $validated['gstin'],
                'pan_number' => $validated['pan_number'] ?? $company->pan_number,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'postal_code' => $validated['postal_code'],
                'currency_symbol' => $validated['currency_symbol'],
                'invoice_prefix' => $validated['invoice_prefix'],
            ]);
        }

        SystemSetting::set('company_name', $validated['name']);
        SystemSetting::set('default_tax_rate', $validated['default_tax_rate']);
        SystemSetting::set('currency_symbol', $validated['currency_symbol']);
        if (isset($validated['invoice_footer_note'])) {
            SystemSetting::set('invoice_footer_note', $validated['invoice_footer_note']);
        }

        return back()->with('success', 'Workshop settings and GST configurations updated successfully!');
    }
}

