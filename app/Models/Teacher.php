<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'teacher';

    protected $casts = [
        'id' => 'integer',
        'department_id' => 'integer',
        'rating' => 'double',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}
