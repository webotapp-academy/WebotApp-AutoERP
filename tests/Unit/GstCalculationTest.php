<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\GstService;

class GstCalculationTest extends TestCase
{
    protected GstService $gstService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gstService = new GstService();
    }

    public function test_intra_state_gst_calculation_splits_cgst_and_sgst_equally(): void
    {
        // For Rs 10,000 at 18% intra-state, total tax = 1800 -> CGST: 900, SGST: 900
        $result = $this->gstService->calculate(10000.00, 18.00, false);

        $this->assertEquals(10000.00, $result['taxable_amount']);
        $this->assertEquals(18.00, $result['gst_rate']);
        $this->assertEquals(900.00, $result['cgst_amount']);
        $this->assertEquals(900.00, $result['sgst_amount']);
        $this->assertEquals(0.00, $result['igst_amount']);
        $this->assertEquals(1800.00, $result['total_tax']);
        $this->assertEquals(11800.00, $result['grand_total']);
    }

    public function test_inter_state_gst_calculation_assigns_full_tax_to_igst(): void
    {
        // For Rs 10,000 at 18% inter-state, IGST = 1800, CGST = 0, SGST = 0
        $result = $this->gstService->calculate(10000.00, 18.00, true);

        $this->assertEquals(10000.00, $result['taxable_amount']);
        $this->assertEquals(0.00, $result['cgst_amount']);
        $this->assertEquals(0.00, $result['sgst_amount']);
        $this->assertEquals(1800.00, $result['igst_amount']);
        $this->assertEquals(1800.00, $result['total_tax']);
        $this->assertEquals(11800.00, $result['grand_total']);
    }

    public function test_gst_calculation_with_discount_and_multiple_quantities(): void
    {
        // 2 items at Rs 2,500 = 5,000. Discount = Rs 500. Taxable = 4,500.
        // Tax at 18% = Rs 810. Intra-state: CGST = 405, SGST = 405. Grand total = 5,310.
        $result = $this->gstService->calculateFromNet(
            rate: 2500.00,
            qty: 2.0,
            discount: 500.00,
            gstRate: 18.00,
            isInterState: false
        );

        $this->assertEquals(5000.00, $result['subtotal']);
        $this->assertEquals(500.00, $result['discount']);
        $this->assertEquals(4500.00, $result['taxable_amount']);
        $this->assertEquals(405.00, $result['cgst_amount']);
        $this->assertEquals(405.00, $result['sgst_amount']);
        $this->assertEquals(0.00, $result['igst_amount']);
        $this->assertEquals(810.00, $result['total_tax']);
        $this->assertEquals(5310.00, $result['grand_total']);
    }

    public function test_gst_28_percent_slab_for_luxury_or_specialized_automotive_parts(): void
    {
        // Rate: 28% on Rs 20,000
        $result = $this->gstService->calculate(20000.00, 28.00, false);

        $this->assertEquals(2800.00, $result['cgst_amount']);
        $this->assertEquals(2800.00, $result['sgst_amount']);
        $this->assertEquals(5600.00, $result['total_tax']);
        $this->assertEquals(25600.00, $result['grand_total']);
    }
}

