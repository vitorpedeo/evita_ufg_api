<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
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
