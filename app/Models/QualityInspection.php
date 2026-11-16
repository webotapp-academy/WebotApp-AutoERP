<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityInspection extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'road_test_done' => 'boolean',
        'engine_sound_ok' => 'boolean',
        'brake_test_ok' => 'boolean',
        'ac_cooling_ok' => 'boolean',
        'electrical_ok' => 'boolean',
        'wheel_nut_torqued' => 'boolean',
        'cleaning_washing_ok' => 'boolean',
        'passed' => 'boolean',
        'rework_required' => 'boolean',
        'inspected_at' => 'datetime',
    ];

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
