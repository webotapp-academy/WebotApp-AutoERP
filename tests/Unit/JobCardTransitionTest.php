<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\JobCard;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Company;
use App\Services\JobCardService;
use Exception;

class JobCardTransitionTest extends TestCase
{
    protected JobCardService $jobCardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jobCardService = new JobCardService();
    }

    public function test_valid_job_card_lifecycle_transitions(): void
    {
        $company = Company::first();
        $branch = Branch::first();
        $customer = Customer::first();
        $vehicle = Vehicle::first();

        $jobCard = JobCard::create([
            'company_id' => $company->id ?? 1,
            'branch_id' => $branch->id ?? 1,
            'customer_id' => $customer->id ?? 1,
            'vehicle_id' => $vehicle->id ?? 1,
            'service_advisor_id' => 1,
            'check_in_time' => now(),
            'job_card_number' => 'JC-TEST-' . rand(1000, 9999),
            'status' => 'draft',
            'fuel_level' => '50%',
            'odometer_in' => 45000,
        ]);

        // 1. draft -> pending_estimate
        $this->jobCardService->transitionStatus($jobCard, 'pending_estimate');
        $this->assertEquals('pending_estimate', $jobCard->fresh()->status);

        // 2. pending_estimate -> estimate_approved
        $this->jobCardService->transitionStatus($jobCard, 'estimate_approved');
        $this->assertEquals('estimate_approved', $jobCard->fresh()->status);

        // 3. estimate_approved -> work_in_progress
        $this->jobCardService->transitionStatus($jobCard, 'work_in_progress');
        $this->assertEquals('work_in_progress', $jobCard->fresh()->status);

        // 4. work_in_progress -> quality_check
        $this->jobCardService->transitionStatus($jobCard, 'quality_check');
        $this->assertEquals('quality_check', $jobCard->fresh()->status);

        // 5. quality_check -> ready_for_delivery (generates gate pass)
        $this->jobCardService->transitionStatus($jobCard, 'ready_for_delivery');
        $fresh = $jobCard->fresh();
        $this->assertEquals('ready_for_delivery', $fresh->status);
        $this->assertNotNull($fresh->gate_pass_number);
        $this->assertStringStartsWith('GP-', $fresh->gate_pass_number);

        // 6. ready_for_delivery -> delivered
        $this->jobCardService->transitionStatus($jobCard, 'delivered');
        $freshDelivered = $jobCard->fresh();
        $this->assertEquals('delivered', $freshDelivered->status);
        $this->assertNotNull($freshDelivered->delivered_at);

        // Clean up test record
        $jobCard->delete();
    }

    public function test_invalid_lifecycle_transition_throws_exception(): void
    {
        $company = Company::first();
        $branch = Branch::first();
        $customer = Customer::first();
        $vehicle = Vehicle::first();

        $jobCard = JobCard::create([
            'company_id' => $company->id ?? 1,
            'branch_id' => $branch->id ?? 1,
            'customer_id' => $customer->id ?? 1,
            'vehicle_id' => $vehicle->id ?? 1,
            'service_advisor_id' => 1,
            'check_in_time' => now(),
            'job_card_number' => 'JC-ERR-' . rand(1000, 9999),
            'status' => 'draft',
            'odometer_in' => 30000,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid status transition from 'draft' to 'delivered'.");

        try {
            // Attempt illegal jump from draft to delivered
            $this->jobCardService->transitionStatus($jobCard, 'delivered');
        } finally {
            $jobCard->delete();
        }
    }
}
