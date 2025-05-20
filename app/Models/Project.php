<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'url',
        'image',
        'tags',
        'stars',
        'forks',
        'user_id'
    ];

    protected $casts = [
        'tags' => 'array',
        'stars' => 'integer',
        'forks' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 