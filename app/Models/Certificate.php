<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'title',
        'issuer',
        'issue_date',
        'url',
        'download_url',
        'issuer_logo',
        'user_id'
    ];

    protected $casts = [
        'issue_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 