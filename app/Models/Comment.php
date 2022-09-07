<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comment';

    protected $fillable = [
        'content',
        'rating',
        'user_account_id',
        'teacher_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'rating' => 'float',
        'user_account_id' => 'integer',
        'teacher_id' => 'integer',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
    ];

    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsTo(UserAccount::class, 'teacher_id', 'id');
    }
}
