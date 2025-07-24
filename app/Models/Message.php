<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'content_type',
        'read_at',
        'edited_at',
        'deleted_at',
        'deleted_content',
        'image_path',
        'file_name',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id_usuarios');
    }
} 