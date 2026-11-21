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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'customer' => $customer,
                'message' => 'Customer profile registered successfully!',
            ], 201);
        }

        if ($request->filled('return_to') && $request->return_to === 'appointments.create') {
            return redirect()->route('appointments.create', ['customer_id' => $customer->id])
                ->with('success', "Customer '{$customer->full_name}' registered successfully!");
        }

        return redirect()->route('customers.show', $customer->id)->with('success', 'Customer profile registered successfully!');
    }

    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'required|string|max:25',
            'email' => 'nullable|email|max:150',
            'customer_type' => 'nullable|in:individual,corporate,fleet',
            'company_name' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:30|unique:vehicles,registration_number',
            'brand_id' => 'nullable|exists:vehicle_brands,id',
            'model_name' => 'nullable|string|max:100',
            'fuel_type' => 'nullable|string|max:50',
        ]);

        $company = Company::first();
        $branch = Branch::first();

        $customer = Customer::create([
            'company_id' => $company->id ?? 1,
            'branch_id' => $branch->id ?? 1,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'customer_type' => $validated['customer_type'] ?? 'individual',
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'city' => $validated['city'] ?? null,
            'outstanding_balance' => 0.00,
            'status' => 'active',
        ]);

        $vehicle = null;
        if (!empty($validated['registration_number'])) {
            $brandId = $validated['brand_id'] ?? null;
            if (!$brandId) {
                $brand = \App\Models\VehicleBrand::first();
                $brandId = $brand?->id;
            }

            $vehicleType = \App\Models\VehicleType::first();
            $vehicleTypeId = $vehicleType?->id ?? 1;

            $modelId = null;
            if (!empty($validated['model_name']) && $brandId) {
                $model = \App\Models\VehicleModel::firstOrCreate(
                    ['name' => trim($validated['model_name']), 'brand_id' => $brandId],
                    ['vehicle_type_id' => $vehicleTypeId]
                );
                $modelId = $model->id;
            } elseif ($brandId) {
                $model = \App\Models\VehicleModel::where('brand_id', $brandId)->first();
                $modelId = $model?->id;
            }

            $vehicle = \App\Models\Vehicle::create([
                'customer_id' => $customer->id,
                'branch_id' => $branch->id ?? 1,
                'vehicle_type_id' => $vehicleTypeId,
                'brand_id' => $brandId,
                'model_id' => $modelId,
                'registration_number' => strtoupper(trim($validated['registration_number'])),
                'fuel_type' => $validated['fuel_type'] ?? 'Petrol',
                'model_year' => date('Y'),
                'current_odometer' => 0,
                'status' => 'active',
            ]);

            $vehicle->load(['brand', 'model']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->full_name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                ],
                'vehicle' => $vehicle ? [
                    'id' => $vehicle->id,
                    'display' => $vehicle->registration_number . ' - ' . ($vehicle->brand->name ?? '') . ' ' . ($vehicle->model->name ?? ''),
                    'registration_number' => $vehicle->registration_number,
                ] : null,
                'message' => "Customer '{$customer->full_name}' registered successfully!",
            ], 201);
        }

        if ($request->filled('return_to') && $request->return_to === 'appointments.create') {
            return redirect()->route('appointments.create', [
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicle?->id,
            ])->with('success', "Customer '{$customer->full_name}' registered successfully!");
        }

        return back()->with('success', "Customer '{$customer->full_name}' registered successfully!");
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

