<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Branch;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with(['vehicles', 'jobCards']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('customer_type', $request->type);
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('customers.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'customer_type' => 'required|in:individual,corporate,fleet',
            'company_name' => 'nullable|string|max:150',
            'phone' => 'required|string|max:25',
            'email' => 'nullable|email|max:150',
            'gstin' => 'nullable|string|max:20',
            'pan' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $company = Company::first();
        $branch = Branch::first();

        $customer = Customer::create(array_merge($validated, [
            'company_id' => $company->id ?? 1,
            'branch_id' => $branch->id ?? 1,
            'outstanding_balance' => 0.00,
            'status' => 'active',
        ]));

        return redirect()->route('customers.show', $customer->id)->with('success', 'Customer profile registered successfully!');
    }

    public function show(Customer $customer)
    {
        $customer->load(['vehicles.model.brand', 'vehicles.vehicleType', 'jobCards.invoice', 'invoices', 'payments']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'customer_type' => 'required|in:individual,corporate,fleet',
            'company_name' => 'nullable|string|max:150',
            'phone' => 'required|string|max:25',
            'email' => 'nullable|email|max:150',
            'gstin' => 'nullable|string|max:20',
            'pan' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,blocked',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer->id)->with('success', 'Customer profile updated successfully!');
    }
}

