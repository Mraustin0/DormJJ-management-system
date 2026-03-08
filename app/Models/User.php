<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'admin_role',
        'tenant_role',
        'staff_role',
        'tenant_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->admin_role === 'admin';
    }

    /**
     * Check if user is tenant
     */
    public function isTenant(): bool
    {
        return $this->tenant_role === 'tenant' || $this->tenant_role === true || $this->tenant_role === '1';
    }

    /**
     * Check if user is staff
     */
    public function isStaff(): bool
    {
        return $this->staff_role === 'staff';
    }

    /**
     * Get user's contract (for tenant)
     */
    public function contract()
    {
        return $this->hasOne(Contract::class, 'user_id')->latest();
    }

    /**
     * Get all contracts for this user
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'user_id');
    }
}
