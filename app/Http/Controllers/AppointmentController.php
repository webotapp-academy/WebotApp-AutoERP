<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\ServiceBay;
use App\Models\Branch;
use App\Models\Company;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['customer', 'vehicle.brand', 'vehicle.model', 'serviceBay']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->latest('appointment_date')->paginate(15)->withQueryString();

        return view('appointments.index', compact('appointments'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('first_name')->get();
        $vehicles = Vehicle::with(['brand', 'model'])->orderBy('registration_number')->get();
        $serviceBays = ServiceBay::all();
        $selectedCustomerId = $request->customer_id;
        $selectedVehicleId = $request->vehicle_id;

        return view('appointments.create', compact('customers', 'vehicles', 'serviceBays', 'selectedCustomerId', 'selectedVehicleId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_bay_id' => 'nullable|exists:service_bays,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'service_type' => 'required|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'customer_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $company = Company::first();
        $branch = Branch::first();
        $count = Appointment::whereYear('created_at', date('Y'))->count() + 1;
        $appointmentNumber = sprintf("APT-%s-%04d", date('Y'), $count);

        $appointment = Appointment::create(array_merge($validated, [
            'appointment_number' => $appointmentNumber,
            'company_id' => $company->id ?? 1,
            'branch_id' => $branch->id ?? 1,
            'status' => 'confirmed',
            'estimated_cost' => $request->estimated_cost ?? 0.00,
        ]));

        return redirect()->route('appointments.index')->with('success', "Appointment #{$appointmentNumber} booked successfully!");
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate(['status' => 'required|in:scheduled,confirmed,arrived,in_progress,completed,cancelled,no_show']);
        $appointment->status = $request->status;
        $appointment->save();

        return back()->with('success', "Appointment status updated to " . ucfirst(str_replace('_', ' ', $appointment->status)));
    }
}

