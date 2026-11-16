<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServicePackage;
use App\Models\ServicePackageItem;
use App\Models\Service;
use App\Models\VehicleType;

class ServicePackageController extends Controller
{
    public function index()
    {
        $packages = ServicePackage::with(['items.service', 'items.sparePart', 'vehicleType'])->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $vehicleTypes = VehicleType::all();

        return view('packages.index', compact('packages', 'services', 'vehicleTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:50|unique:service_packages,code',
            'package_price' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'valid_days' => 'required|integer|min:30',
            'description' => 'nullable|string',
        ]);

        $pkg = ServicePackage::create(array_merge($validated, ['is_active' => true]));

        return back()->with('success', "Service Package '{$pkg->name}' created successfully!");
    }
}
