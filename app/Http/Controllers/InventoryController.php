<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use App\Models\StockLedger;
use App\Models\Branch;
use App\Models\Company;
use App\Services\InventoryService;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = SparePart::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('part_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('current_stock', '<=', 'min_stock_alert');
        }

        $spareParts = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = SparePartCategory::orderBy('name')->get();

        $totalPartsCount = SparePart::count();
        $lowStockCount = SparePart::whereColumn('current_stock', '<=', 'min_stock_alert')->count();
        $totalValuation = SparePart::selectRaw('SUM(current_stock * purchase_price) as valuation')->value('valuation') ?? 0;

        return view('inventory.index', compact('spareParts', 'categories', 'totalPartsCount', 'lowStockCount', 'totalValuation'));
    }

    public function create()
    {
        $categories = SparePartCategory::orderBy('name')->get();
        return view('inventory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:spare_part_categories,id',
            'part_number' => 'required|string|max:100|unique:spare_parts,part_number',
            'sku' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'unit' => 'required|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'min_stock_alert' => 'required|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'bin_location' => 'nullable|string|max:50',
            'hsn_sac_code' => 'nullable|string|max:20',
            'gst_rate' => 'required|numeric',
        ]);

        $company = Company::first();
        $branch = Branch::first();

        $part = SparePart::create(array_merge($validated, [
            'company_id' => $company->id ?? 1,
            'branch_id' => $branch->id ?? 1,
            'is_active' => true,
        ]));

        if ($request->current_stock > 0) {
            StockLedger::create([
                'spare_part_id' => $part->id,
                'branch_id' => $branch->id ?? 1,
                'transaction_type' => 'purchase_in',
                'reference_type' => 'InitialStock',
                'quantity' => $request->current_stock,
                'unit_cost' => $request->purchase_price,
                'balance_after' => $request->current_stock,
                'remarks' => 'Initial stock on catalog creation',
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('inventory.index')->with('success', "Spare part '{$part->name}' added to inventory!");
    }

    public function edit(SparePart $sparePart)
    {
        $categories = SparePartCategory::orderBy('name')->get();
        return view('inventory.edit', compact('sparePart', 'categories'));
    }

    public function update(Request $request, SparePart $sparePart)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:spare_part_categories,id',
            'part_number' => 'required|string|max:100|unique:spare_parts,part_number,' . $sparePart->id,
            'sku' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'unit' => 'required|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'min_stock_alert' => 'required|integer|min:0',
            'bin_location' => 'nullable|string|max:50',
            'hsn_sac_code' => 'nullable|string|max:20',
            'gst_rate' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        $sparePart->update($validated);

        return redirect()->route('inventory.index')->with('success', "Spare part '{$sparePart->name}' updated!");
    }

    public function adjust(Request $request, SparePart $sparePart)
    {
        $validated = $request->validate([
            'new_stock' => 'required|integer|min:0',
            'reason' => 'required|string',
        ]);

        $this->inventoryService->adjustStock($sparePart->id, $request->new_stock, $request->reason, auth()->id());

        return back()->with('success', "Stock for '{$sparePart->name}' adjusted to {$request->new_stock}.");
    }

    public function ledger(SparePart $sparePart)
    {
        $sparePart->load(['category', 'stockLedgers.user']);
        $ledgers = $sparePart->stockLedgers()->latest()->paginate(25);
        return view('inventory.ledger', compact('sparePart', 'ledgers'));
    }
}
