<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class UserAccount extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'user_account';

    protected $fillable = [
        'name',
        'avatar_url',
        'email',
        'password',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    protected $attributes = [
        'avatar_url' => null,
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];
}
