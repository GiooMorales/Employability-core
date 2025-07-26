<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $fillable = [
        'conteudo',
        'user_id',
        'postagem_id',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id_usuarios');
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class, 'comentario_id');
    }
}
