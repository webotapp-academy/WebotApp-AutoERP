<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Company;
use App\Models\Branch;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('purchaseOrders');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:25',
            'email' => 'nullable|email|max:150',
            'gstin' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'payment_terms_days' => 'nullable|integer|min:0',
        ]);

        $company = Company::first();
        $branch = Branch::first();

        Supplier::create(array_merge($validated, [
            'company_id' => $company->id ?? 1,
            'branch_id' => $branch->id ?? 1,
            'status' => 'active',
            'outstanding_balance' => 0.00,
        ]));

        return redirect()->route('suppliers.index')->with('success', 'Supplier registered successfully!');
    }
}

