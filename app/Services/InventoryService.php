<?php

namespace App\Services;

use App\Models\SparePart;
use App\Models\StockLedger;
use App\Models\JobCardPart;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Issue part to job card.
     */
    public function issuePartToJobCard(JobCardPart $jobCardPart, int $userId): bool
    {
        return DB::transaction(function () use ($jobCardPart, $userId) {
            $part = SparePart::lockForUpdate()->find($jobCardPart->spare_part_id);

            if (!$part) {
                throw new Exception("Spare part not found.");
            }

            if ($part->current_stock < $jobCardPart->quantity) {
                throw new Exception("Insufficient stock for {$part->name}. Current stock: {$part->current_stock}, Requested: {$jobCardPart->quantity}");
            }

            $previousStock = $part->current_stock;
            $newStock = $previousStock - $jobCardPart->quantity;
            $part->current_stock = $newStock;
            $part->save();

            // Create ledger entry
            StockLedger::create([
                'spare_part_id' => $part->id,
                'branch_id' => $part->branch_id ?? $jobCardPart->jobCard->branch_id,
                'transaction_type' => 'job_card_issue',
                'reference_type' => 'JobCard',
                'reference_id' => $jobCardPart->job_card_id,
                'quantity' => -$jobCardPart->quantity,
                'unit_cost' => $part->purchase_price,
                'balance_after' => $newStock,
                'remarks' => "Issued to Job Card #{$jobCardPart->jobCard->job_card_number}",
                'created_by' => $userId,
            ]);

            $jobCardPart->status = 'issued';
            $jobCardPart->issued_at = now();
            $jobCardPart->save();

            $jobCardPart->jobCard->recalculateTotals();

            return true;
        });
    }

    /**
     * Return issued part from job card back to inventory.
     */
    public function returnPartFromJobCard(JobCardPart $jobCardPart, int $userId, ?int $quantity = null): bool
    {
        return DB::transaction(function () use ($jobCardPart, $userId, $quantity) {
            $returnQty = $quantity ?? $jobCardPart->quantity;
            $part = SparePart::lockForUpdate()->find($jobCardPart->spare_part_id);

            if (!$part) {
                throw new Exception("Spare part not found.");
            }

            $newStock = $part->current_stock + $returnQty;
            $part->current_stock = $newStock;
            $part->save();

            // Create ledger entry
            StockLedger::create([
                'spare_part_id' => $part->id,
                'branch_id' => $part->branch_id ?? $jobCardPart->jobCard->branch_id,
                'transaction_type' => 'job_card_return',
                'reference_type' => 'JobCard',
                'reference_id' => $jobCardPart->job_card_id,
                'quantity' => $returnQty,
                'unit_cost' => $part->purchase_price,
                'balance_after' => $newStock,
                'remarks' => "Returned from Job Card #{$jobCardPart->jobCard->job_card_number}",
                'created_by' => $userId,
            ]);

            $jobCardPart->status = 'returned';
            $jobCardPart->returned_at = now();
            $jobCardPart->save();

            $jobCardPart->jobCard->recalculateTotals();

            return true;
        });
    }

    /**
     * Receive goods from purchase order.
     */
    public function receivePurchaseOrderStock(int $partId, int $quantity, float $unitCost, int $purchaseOrderId, int $branchId, int $userId): void
    {
        $part = SparePart::lockForUpdate()->find($partId);
        if (!$part) return;

        $newStock = $part->current_stock + $quantity;
        $part->current_stock = $newStock;
        $part->purchase_price = $unitCost;
        $part->save();

        StockLedger::create([
            'spare_part_id' => $part->id,
            'branch_id' => $branchId,
            'transaction_type' => 'purchase_in',
            'reference_type' => 'PurchaseOrder',
            'reference_id' => $purchaseOrderId,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'balance_after' => $newStock,
            'remarks' => "Stock received from PO #{$purchaseOrderId}",
            'created_by' => $userId,
        ]);
    }

    /**
     * Manual stock adjustment.
     */
    public function adjustStock(int $partId, int $newCount, string $reason, int $userId): void
    {
        DB::transaction(function () use ($partId, $newCount, $reason, $userId) {
            $part = SparePart::lockForUpdate()->find($partId);
            if (!$part) return;

            $diff = $newCount - $part->current_stock;
            $part->current_stock = $newCount;
            $part->save();

            StockLedger::create([
                'spare_part_id' => $part->id,
                'branch_id' => $part->branch_id,
                'transaction_type' => 'stock_adjustment',
                'reference_type' => 'Adjustment',
                'quantity' => $diff,
                'unit_cost' => $part->purchase_price,
                'balance_after' => $newCount,
                'remarks' => "Manual Adjustment: {$reason}",
                'created_by' => $userId,
            ]);
        });
    }
}

