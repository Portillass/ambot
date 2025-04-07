<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'database_name',
    ];

    protected $hidden = [
        'password',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];
}
