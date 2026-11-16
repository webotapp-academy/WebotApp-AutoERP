<?php

namespace App\Services;

use App\Models\JobCard;
use App\Models\ServiceBay;
use Carbon\Carbon;
use Exception;

class JobCardService
{
    /**
     * Allowed state transitions for Job Card lifecycle.
     */
    protected array $allowedTransitions = [
        'draft' => ['pending_estimate', 'estimate_approved', 'cancelled'],
        'pending_estimate' => ['estimate_approved', 'cancelled'],
        'estimate_approved' => ['work_in_progress', 'cancelled'],
        'work_in_progress' => ['quality_check', 'cancelled'],
        'quality_check' => ['ready_for_delivery', 'work_in_progress'], // can go back to WIP if rework needed
        'ready_for_delivery' => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];

    /**
     * Transition job card to new status.
     */
    public function transitionStatus(JobCard $jobCard, string $newStatus, ?string $remarks = null): JobCard
    {
        $currentStatus = $jobCard->status;

        if ($currentStatus === $newStatus) {
            return $jobCard;
        }

        if (!isset($this->allowedTransitions[$currentStatus]) || 
            !in_array($newStatus, $this->allowedTransitions[$currentStatus])) {
            throw new Exception("Invalid status transition from '{$currentStatus}' to '{$newStatus}'.");
        }

        $jobCard->status = $newStatus;

        // Specific actions on transition
        if ($newStatus === 'ready_for_delivery' && empty($jobCard->gate_pass_number)) {
            $jobCard->gate_pass_number = 'GP-' . date('Ymd') . '-' . sprintf('%04d', $jobCard->id);
            $jobCard->gate_pass_generated_at = now();
        }

        if ($newStatus === 'delivered') {
            $jobCard->actual_delivery_time = now();
            $jobCard->delivered_at = now();

            // Release service bay
            if ($jobCard->service_bay_id) {
                ServiceBay::where('id', $jobCard->service_bay_id)->update(['status' => 'available']);
            }

            // Update vehicle odometer and last service date
            $vehicle = $jobCard->vehicle;
            if ($vehicle) {
                $vehicle->last_service_date = now()->toDateString();
                if ($jobCard->odometer_in > $vehicle->current_odometer) {
                    $vehicle->current_odometer = $jobCard->odometer_in;
                }
                $vehicle->save();
            }
        }

        if ($newStatus === 'work_in_progress' && $jobCard->service_bay_id) {
            ServiceBay::where('id', $jobCard->service_bay_id)->update(['status' => 'occupied']);
        }

        if ($remarks) {
            $jobCard->notes = ($jobCard->notes ? $jobCard->notes . "\n" : '') . "[" . now()->toDateTimeString() . "] " . $remarks;
        }

        $jobCard->save();

        return $jobCard;
    }

    /**
     * Generate sequential Job Card Number (JC-YYYY-XXXX).
     */
    public function generateJobCardNumber(): string
    {
        $year = date('Y');
        $count = JobCard::whereYear('created_at', $year)->count() + 1;
        return sprintf("JC-%s-%04d", $year, $count);
    }
}
