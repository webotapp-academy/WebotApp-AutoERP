<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleInspection extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'inspection_date' => 'datetime',
        'scratches_dents_data' => 'array',
        'exterior_checks' => 'array',
        'interior_checks' => 'array',
        'mechanical_checks' => 'array',
        'tire_conditions' => 'array',
        'tool_kit_present' => 'boolean',
        'spare_wheel_present' => 'boolean',
        'jack_handle_present' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
