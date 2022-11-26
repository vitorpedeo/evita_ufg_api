<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class UserAccount extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'user_account';

    protected $fillable = [
        'id',
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
        'updated_at',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_account_id', 'id');
    }
}
