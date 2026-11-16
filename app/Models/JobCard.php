<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobCard extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'check_in_time' => 'datetime',
        'promised_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
        'gate_pass_generated_at' => 'datetime',
        'delivered_at' => 'datetime',
        'estimated_labour' => 'decimal:2',
        'estimated_parts' => 'decimal:2',
        'estimated_total' => 'decimal:2',
        'final_labour' => 'decimal:2',
        'final_parts' => 'decimal:2',
        'final_discount' => 'decimal:2',
        'final_tax' => 'decimal:2',
        'final_grand_total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function serviceAdvisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'service_advisor_id');
    }

    public function leadMechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_mechanic_id');
    }

    public function serviceBay(): BelongsTo
    {
        return $this->belongsTo(ServiceBay::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(JobCardService::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(JobCardPart::class);
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(JobCardTimeLog::class);
    }

    public function estimates(): HasMany
    {
        return $this->hasMany(RepairEstimate::class);
    }

    public function qualityInspection(): HasOne
    {
        return $this->hasOne(QualityInspection::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function recalculateTotals(): void
    {
        $labourTotal = $this->services()->sum('total');
        $partsTotal = $this->parts()->whereIn('status', ['allocated', 'issued'])->sum('total_price');

        $this->final_labour = $labourTotal;
        $this->final_parts = $partsTotal;
        $this->final_grand_total = $labourTotal + $partsTotal - $this->final_discount;
        $this->save();
    }
}

