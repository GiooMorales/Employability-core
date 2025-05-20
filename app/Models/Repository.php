<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repository extends Model
{
    protected $fillable = [
        'name',
        'description',
        'url',
        'visibility',
        'stars',
        'forks',
        'watchers',
        'user_id'
    ];

    protected $casts = [
        'stars' => 'integer',
        'forks' => 'integer',
        'watchers' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 