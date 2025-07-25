<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postagem extends Model
{
    protected $fillable = [
        'titulo',
        'conteudo',
        'user_id',
        'imagem',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id_usuarios');
    }

    public function comentarios()
    {
        return $this->hasMany(\App\Models\Comentario::class, 'postagem_id');
    }

    public function likes()
    {
        return $this->hasMany(\App\Models\Like::class, 'postagem_id');
    }
}
