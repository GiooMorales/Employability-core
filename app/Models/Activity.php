<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'type',
        'text',
        'details',
        'icon',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 