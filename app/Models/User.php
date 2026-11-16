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
        $slug = $this->role->slug ?? '';
        $name = strtolower($this->role->name ?? '');
        if (is_array($roles)) {
            return in_array($slug, $roles) || in_array($name, $roles);
        }
        return $slug === $roles || $name === $roles;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['super_admin', 'admin', 'Super Administrator']);
    }

    public function isManager(): bool
    {
        return $this->hasRole(['workshop_manager', 'manager', 'super_admin', 'admin', 'Workshop Manager', 'Super Administrator']);
    }

    public function isAdvisor(): bool
    {
        return $this->hasRole(['service_advisor', 'advisor', 'Service Advisor']);
    }

    public function isMechanic(): bool
    {
        return $this->hasRole(['mechanic', 'technician', 'Mechanic / Technician']);
    }

    public function isInventory(): bool
    {
        return $this->hasRole(['inventory_manager', 'inventory', 'Inventory Manager']);
    }

    public function isAccountant(): bool
    {
        return $this->hasRole(['accountant', 'cashier', 'Accountant / Cashier']);
    }

    public function getRoleCategoryAttribute(): string
    {
        if ($this->isAdmin() || $this->isManager()) return 'management';
        if ($this->isAdvisor()) return 'advisor';
        if ($this->isMechanic()) return 'mechanic';
        if ($this->isInventory()) return 'inventory';
        if ($this->isAccountant()) return 'accountant';
        return 'management';
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->hasRole('super_admin')) return true;
        if (!$this->role) return false;
        return $this->role->permissions->contains('slug', $permissionSlug);
    }
}
