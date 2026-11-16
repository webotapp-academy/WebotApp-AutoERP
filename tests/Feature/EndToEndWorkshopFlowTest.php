<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\JobCard;
use App\Models\JobCardPart;
use App\Models\JobCardService as JobCardServiceModel;
use App\Models\SparePart;
use App\Models\Service;
use App\Models\QualityInspection;
use App\Models\StockLedger;
use App\Services\InventoryService;
use App\Services\JobCardService;
use App\Services\BillingService;

class EndToEndWorkshopFlowTest extends TestCase
{
    public function test_complete_workshop_lifecycle_from_checkin_to_gatepass_and_gst_billing(): void
    {
        // 1. Authenticate as Admin user
        $admin = User::where('email', 'admin@automobile.com')->first();
        $this->assertNotNull($admin, 'Admin demo user must exist.');
        $this->actingAs($admin);

        // 2. Select or create Customer & Vehicle
        $customer = Customer::first();
        $vehicle = Vehicle::where('customer_id', $customer->id)->first();
        $this->assertNotNull($customer);
        $this->assertNotNull($vehicle);

        // 3. Create a Job Card (Vehicle Check-in)
        $newOdometer = ($vehicle->current_odometer ?? 40000) + 1200;
        $jobCardService = new JobCardService();
        $jobCard = JobCard::create([
            'company_id' => $admin->company_id,
            'branch_id' => $admin->branch_id,
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'service_advisor_id' => $admin->id,
            'check_in_time' => now(),
            'job_card_number' => 'JC-E2E-' . rand(1000, 9999),
            'status' => 'draft',
            'fuel_level' => '75%',
            'odometer_in' => $newOdometer,
            'customer_complaints' => 'Periodic 50,000 km general maintenance & brake inspection.',
        ]);

        $this->assertDatabaseHas('job_cards', ['id' => $jobCard->id, 'status' => 'draft']);

        // Move to estimate_approved then work_in_progress
        $jobCardService->transitionStatus($jobCard, 'pending_estimate');
        $jobCardService->transitionStatus($jobCard, 'estimate_approved');
        $jobCardService->transitionStatus($jobCard, 'work_in_progress');
        $this->assertEquals('work_in_progress', $jobCard->fresh()->status);

        // 4. Add Service Labour (e.g. Periodic Maintenance Service)
        $service = Service::first();
        $jobService = JobCardServiceModel::create([
            'job_card_id' => $jobCard->id,
            'service_id' => $service->id,
            'service_name' => $service->name,
            'labour_charge' => 1200.00,
            'gst_rate' => 18.00,
            'tax_amount' => 216.00,
            'total' => 1416.00,
            'status' => 'completed',
        ]);

        // 5. Issue Spare Part from Inventory & Verify Stock Ledger
        $part = SparePart::where('current_stock', '>=', 5)->first();
        $initialStock = $part->current_stock;
        $issueQty = 2;
        $totalPrice = round($part->selling_price * $issueQty * 1.18, 2);

        $jobPart = JobCardPart::create([
            'job_card_id' => $jobCard->id,
            'spare_part_id' => $part->id,
            'part_number' => $part->part_number,
            'part_name' => $part->name,
            'quantity' => $issueQty,
            'unit_price' => $part->selling_price,
            'discount' => 0.00,
            'gst_rate' => 18.00,
            'tax_amount' => round(($part->selling_price * $issueQty * 18) / 100, 2),
            'total_price' => $totalPrice,
            'status' => 'requested',
        ]);

        $inventoryService = new InventoryService();
        $inventoryService->issuePartToJobCard($jobPart, $admin->id);

        // Verify stock was decremented
        $this->assertEquals($initialStock - $issueQty, $part->fresh()->current_stock);
        // Verify stock ledger entry was recorded
        $this->assertDatabaseHas('stock_ledgers', [
            'spare_part_id' => $part->id,
            'transaction_type' => 'job_card_issue',
            'reference_id' => $jobCard->id,
        ]);

        // 6. Complete Quality Inspection
        $qc = QualityInspection::create([
            'job_card_id' => $jobCard->id,
            'inspector_id' => $admin->id,
            'inspected_at' => now(),
            'road_test_done' => true,
            'engine_sound_ok' => true,
            'brake_test_ok' => true,
            'ac_cooling_ok' => true,
            'electrical_ok' => true,
            'wheel_nut_torqued' => true,
            'cleaning_washing_ok' => true,
            'passed' => true,
        ]);

        $jobCardService->transitionStatus($jobCard, 'quality_check');
        $jobCardService->transitionStatus($jobCard, 'ready_for_delivery');

        // 7. Verify Gate Pass Generated
        $readyJob = $jobCard->fresh();
        $this->assertEquals('ready_for_delivery', $readyJob->status);
        $this->assertNotEmpty($readyJob->gate_pass_number);

        // 8. Generate Indian GST Invoice
        $billingService = app(BillingService::class);
        $invoice = $billingService->createInvoiceFromJobCard($readyJob, $admin->id);

        $this->assertNotNull($invoice);
        $this->assertStringStartsWith('INV-', $invoice->invoice_number);
        $this->assertGreaterThan(0, $invoice->grand_total);
        $this->assertGreaterThan(0, $invoice->cgst_amount);
        $this->assertGreaterThan(0, $invoice->sgst_amount);
        $this->assertEquals(0, $invoice->igst_amount); // Intra-state sale
        $this->assertEquals($invoice->grand_total, $invoice->balance_due);

        // 9. Collect and Record Payment
        $payment = $billingService->recordPayment(
            invoice: $invoice,
            amount: (float)$invoice->grand_total,
            paymentMethod: 'upi',
            refNumber: 'UPI-TEST-TXN-1234',
            userId: $admin->id,
            notes: 'Paid via UPI QR code'
        );

        $this->assertNotNull($payment);
        $freshInvoice = $invoice->fresh();
        $this->assertEquals(0.00, (float)$freshInvoice->balance_due);
        $this->assertEquals('paid', $freshInvoice->payment_status);

        // 10. Complete Vehicle Delivery
        $jobCardService->transitionStatus($jobCard, 'delivered');
        $deliveredJob = $jobCard->fresh();
        $this->assertEquals('delivered', $deliveredJob->status);
        $this->assertNotNull($deliveredJob->delivered_at);
        $this->assertEquals($newOdometer, $vehicle->fresh()->current_odometer);
    }
}
