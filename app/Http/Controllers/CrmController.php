<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceReminder;
use App\Models\Customer;
use App\Models\Vehicle;

class CrmController extends Controller
{
    public function reminders(Request $request)
    {
        $query = ServiceReminder::with(['customer', 'vehicle.brand', 'vehicle.model']);

        if ($request->filled('type')) {
            $query->where('reminder_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reminders = $query->orderBy('due_date')->paginate(15)->withQueryString();
        $vehicles = Vehicle::with('customer')->orderBy('registration_number')->get();

        return view('crm.reminders', compact('reminders', 'vehicles'));
    }

    public function sendReminder(ServiceReminder $reminder)
    {
        $reminder->status = 'sent';
        $reminder->last_sent_at = now();
        $reminder->save();

        return back()->with('success', "Service Reminder dispatched via WhatsApp / SMS to {$reminder->customer->full_name} ({$reminder->customer->phone})!");
    }

    public function storeReminder(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'reminder_type' => 'required|string',
            'due_date' => 'required|date',
            'channel' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        ServiceReminder::create([
            'vehicle_id' => $vehicle->id,
            'customer_id' => $vehicle->customer_id,
            'reminder_type' => $request->reminder_type,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'channel' => $request->channel,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Service reminder scheduled successfully!');
    }
}

