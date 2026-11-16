<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceBay extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function activeJobCards(): HasMany
    {
        return $this->hasMany(JobCard::class)->whereNotIn('status', ['delivered', 'cancelled']);
    }
}

