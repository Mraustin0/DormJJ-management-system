<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'ssn',
        'tenant_name',
        'phone',
        'email',
    ];
}
