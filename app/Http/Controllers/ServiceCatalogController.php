<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCategory;

class ServiceCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('sac_code', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = ServiceCategory::orderBy('name')->get();

        return view('services.index', compact('services', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:50|unique:services,code',
            'standard_time_hours' => 'required|numeric|min:0.1',
            'standard_labour_cost' => 'required|numeric|min:0',
            'default_gst_rate' => 'required|numeric',
            'sac_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        Service::create(array_merge($validated, [
            'sac_code' => $validated['sac_code'] ?? '998729',
            'is_active' => true,
        ]));

        return back()->with('success', "Service operation '{$validated['name']}' added to catalog!");
    }
}
