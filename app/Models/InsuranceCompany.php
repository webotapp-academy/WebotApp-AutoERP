<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceCompany extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function claims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class);
    }
}
