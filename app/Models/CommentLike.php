<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    protected $fillable = [
        'user_id',
        'comentario_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_usuarios');
    }

    public function comentario()
    {
        return $this->belongsTo(Comentario::class, 'comentario_id');
    }
}
