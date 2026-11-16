<?php

namespace App\Services;

class GstService
{
    /**
     * Calculate GST breakdown.
     * In India:
     * - Intra-state sale (same state): Split equally into CGST (half) and SGST (half)
     * - Inter-state sale (different state): IGST (full)
     * 
     * @param float $taxableAmount
     * @param float $gstRate (e.g. 18.00, 28.00)
     * @param bool $isInterState
     * @return array
     */
    public function calculate(float $taxableAmount, float $gstRate = 18.00, bool $isInterState = false): array
    {
        $totalTax = round(($taxableAmount * $gstRate) / 100, 2);

        if ($isInterState) {
            $cgst = 0.00;
            $sgst = 0.00;
            $igst = $totalTax;
        } else {
            $cgst = round($totalTax / 2, 2);
            $sgst = round($totalTax - $cgst, 2); // Avoid 1 paisa rounding disparity
            $igst = 0.00;
        }

        return [
            'taxable_amount' => round($taxableAmount, 2),
            'gst_rate' => $gstRate,
            'cgst_amount' => $cgst,
            'sgst_amount' => $sgst,
            'igst_amount' => $igst,
            'total_tax' => $totalTax,
            'grand_total' => round($taxableAmount + $totalTax, 2),
        ];
    }

    /**
     * Calculate item tax from gross/inclusive price or net/exclusive price.
     */
    public function calculateFromNet(float $rate, float $qty, float $discount = 0.00, float $gstRate = 18.00, bool $isInterState = false): array
    {
        $subtotal = round($rate * $qty, 2);
        $taxable = max(0.00, round($subtotal - $discount, 2));
        $taxData = $this->calculate($taxable, $gstRate, $isInterState);

        return array_merge([
            'subtotal' => $subtotal,
            'discount' => round($discount, 2),
        ], $taxData);
    }
}
