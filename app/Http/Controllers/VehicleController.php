<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\Customer;
use App\Models\Branch;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with(['customer', 'brand', 'model', 'vehicleType']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhere('vin_chassis_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type_id')) {
            $query->where('vehicle_type_id', $request->type_id);
        }

        $vehicles = $query->latest()->paginate(15)->withQueryString();
        $vehicleTypes = VehicleType::all();

        return view('vehicles.index', compact('vehicles', 'vehicleTypes'));
    }

    public function create(Request $request)
    {
        $selectedCustomerId = $request->query('customer_id');
        $customers = Customer::orderBy('first_name')->get();
        $vehicleTypes = VehicleType::all();
        $brands = VehicleBrand::orderBy('name')->get();
        $models = VehicleModel::orderBy('name')->get();

        return view('vehicles.create', compact('customers', 'selectedCustomerId', 'vehicleTypes', 'brands', 'models'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'brand_id' => 'required|exists:vehicle_brands,id',
            'model_id' => 'required|exists:vehicle_models,id',
            'registration_number' => 'required|string|max:30|unique:vehicles,registration_number',
            'vin_chassis_number' => 'nullable|string|max:50',
            'engine_number' => 'nullable|string|max:50',
            'model_year' => 'nullable|digits:4',
            'color' => 'nullable|string|max:50',
            'fuel_type' => 'required|string',
            'transmission' => 'required|in:Manual,Automatic',
            'current_odometer' => 'required|integer|min:0',
            'insurance_company' => 'nullable|string|max:100',
            'insurance_policy_number' => 'nullable|string|max:100',
            'insurance_expiry_date' => 'nullable|date',
            'puc_expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $branch = Branch::first();
        $vehicle = Vehicle::create(array_merge($validated, [
            'branch_id' => $branch->id ?? 1,
            'status' => 'active',
        ]));

        return redirect()->route('vehicles.show', $vehicle->id)->with('success', 'Vehicle registered successfully!');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['customer', 'brand', 'model', 'vehicleType', 'jobCards.invoice', 'inspections.inspector', 'serviceReminders']);
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $customers = Customer::orderBy('first_name')->get();
        $vehicleTypes = VehicleType::all();
        $brands = VehicleBrand::orderBy('name')->get();
        $models = VehicleModel::orderBy('name')->get();

        return view('vehicles.edit', compact('vehicle', 'customers', 'vehicleTypes', 'brands', 'models'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'brand_id' => 'required|exists:vehicle_brands,id',
            'model_id' => 'required|exists:vehicle_models,id',
            'registration_number' => 'required|string|max:30|unique:vehicles,registration_number,' . $vehicle->id,
            'vin_chassis_number' => 'nullable|string|max:50',
            'engine_number' => 'nullable|string|max:50',
            'model_year' => 'nullable|digits:4',
            'color' => 'nullable|string|max:50',
            'fuel_type' => 'required|string',
            'transmission' => 'required|in:Manual,Automatic',
            'current_odometer' => 'required|integer|min:0',
            'insurance_company' => 'nullable|string|max:100',
            'insurance_policy_number' => 'nullable|string|max:100',
            'insurance_expiry_date' => 'nullable|date',
            'puc_expiry_date' => 'nullable|date',
            'status' => 'required|in:active,in_service,archived',
            'notes' => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.show', $vehicle->id)->with('success', 'Vehicle updated successfully!');
    }
}

