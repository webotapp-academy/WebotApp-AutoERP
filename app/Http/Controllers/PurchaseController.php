<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\SparePart;
use App\Models\Branch;
use App\Services\InventoryService;

class PurchaseController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'items.sparePart', 'creator'])
            ->latest('order_date')
            ->paginate(15);

        return view('purchases.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $parts = SparePart::where('is_active', true)->orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'parts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'spare_part_id' => 'required|exists:spare_parts,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $branch = Branch::first();
        $count = PurchaseOrder::whereYear('created_at', date('Y'))->count() + 1;
        $poNumber = sprintf("PO-%s-%04d", date('Y'), $count);

        $subtotal = $request->quantity * $request->unit_price;
        $tax = round($subtotal * 0.18, 2);
        $total = $subtotal + $tax;

        $po = PurchaseOrder::create([
            'po_number' => $poNumber,
            'supplier_id' => $request->supplier_id,
            'branch_id' => $branch->id ?? 1,
            'order_date' => $request->order_date,
            'expected_date' => $request->expected_date,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'status' => 'ordered',
            'created_by' => auth()->id(),
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'spare_part_id' => $request->spare_part_id,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'tax_percent' => 18.00,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'received_quantity' => 0,
        ]);

        return redirect()->route('purchases.index')->with('success', "Purchase Order #{$poNumber} placed successfully!");
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        foreach ($purchaseOrder->items as $item) {
            $this->inventoryService->receivePurchaseOrderStock(
                $item->spare_part_id,
                $item->quantity,
                $item->unit_price,
                $purchaseOrder->id,
                $purchaseOrder->branch_id,
                auth()->id()
            );
            $item->received_quantity = $item->quantity;
            $item->save();
        }

        $purchaseOrder->status = 'received';
        $purchaseOrder->save();

        return back()->with('success', "Purchase Order #{$purchaseOrder->po_number} marked as Received. Stock balances incremented in inventory!");
    }
}

