<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleInspection;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Branch;

class InspectionController extends Controller
{
    public function index()
    {
        $inspections = VehicleInspection::with(['vehicle.brand', 'vehicle.model', 'customer', 'inspector'])
            ->latest('inspection_date')
            ->paginate(15);

        return view('inspections.index', compact('inspections'));
    }

    public function create(Request $request)
    {
        $selectedVehicleId = $request->vehicle_id;
        $vehicles = Vehicle::with(['brand', 'model', 'customer'])->orderBy('registration_number')->get();
        return view('inspections.create', compact('vehicles', 'selectedVehicleId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'odometer_reading' => 'required|integer|min:0',
            'fuel_level' => 'required|in:empty,reserve,25_percent,50_percent,75_percent,full',
            'tool_kit_present' => 'boolean',
            'spare_wheel_present' => 'boolean',
            'jack_handle_present' => 'boolean',
            'customer_items_inside' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $branch = Branch::first();
        $count = VehicleInspection::whereYear('created_at', date('Y'))->count() + 1;
        $inspectionNumber = sprintf("INS-%s-%04d", date('Y'), $count);

        $exterior = [
            'wipers' => $request->exterior_wipers ?? 'ok',
            'lights' => $request->exterior_lights ?? 'ok',
            'windshield' => $request->exterior_windshield ?? 'ok',
            'mirrors' => $request->exterior_mirrors ?? 'ok',
        ];

        $interior = [
            'ac' => $request->interior_ac ?? 'ok',
            'horn' => $request->interior_horn ?? 'ok',
            'infotainment' => $request->interior_infotainment ?? 'ok',
            'power_windows' => $request->interior_windows ?? 'ok',
        ];

        $mechanical = [
            'engine_oil' => $request->mech_oil ?? 'ok',
            'coolant' => $request->mech_coolant ?? 'ok',
            'brake_fluid' => $request->mech_brake_fluid ?? 'ok',
            'battery' => $request->mech_battery ?? 'ok',
        ];

        $tires = [
            'front_left' => $request->tire_fl ?? '4mm',
            'front_right' => $request->tire_fr ?? '4mm',
            'rear_left' => $request->tire_rl ?? '5mm',
            'rear_right' => $request->tire_rr ?? '5mm',
        ];

        $scratches = [];
        if ($request->filled('scratch_notes')) {
            $scratches[] = ['note' => $request->scratch_notes];
        }

        $inspection = VehicleInspection::create([
            'inspection_number' => $inspectionNumber,
            'branch_id' => $branch->id ?? 1,
            'vehicle_id' => $vehicle->id,
            'customer_id' => $vehicle->customer_id,
            'inspector_id' => auth()->id(),
            'inspection_date' => now(),
            'odometer_reading' => $request->odometer_reading,
            'fuel_level' => $request->fuel_level,
            'scratches_dents_data' => $scratches,
            'exterior_checks' => $exterior,
            'interior_checks' => $interior,
            'mechanical_checks' => $mechanical,
            'tire_conditions' => $tires,
            'tool_kit_present' => $request->boolean('tool_kit_present'),
            'spare_wheel_present' => $request->boolean('spare_wheel_present'),
            'jack_handle_present' => $request->boolean('jack_handle_present'),
            'customer_items_inside' => $request->customer_items_inside,
            'status' => 'completed',
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('inspections.show', $inspection->id)->with('success', "Vehicle Check-in Inspection #{$inspectionNumber} completed!");
    }

    public function show(VehicleInspection $inspection)
    {
        $inspection->load(['vehicle.brand', 'vehicle.model', 'customer', 'inspector']);
        return view('inspections.show', compact('inspection'));
    }
}

