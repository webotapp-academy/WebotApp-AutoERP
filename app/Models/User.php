<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'branch_id',
        'company_id',
        'phone',
        'status',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function jobCardsAssigned(): HasMany
    {
        return $this->hasMany(JobCard::class, 'lead_mechanic_id');
    }

    public function jobCardsAdvising(): HasMany
    {
        return $this->hasMany(JobCard::class, 'service_advisor_id');
    }

    public function hasRole(string|array $roles): bool
    {
        if (!$this->role) return false;
        if (is_array($roles)) {
            return in_array($this->role->slug, $roles);
        }
        return $this->role->slug === $roles;
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->hasRole('super_admin')) return true;
        if (!$this->role) return false;
        return $this->role->permissions->contains('slug', $permissionSlug);
    }
}
