<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'formacoes';
    protected $primaryKey = 'id_formacoes';

    protected $fillable = [
        'usuario_id',
        'instituicao',
        'curso',
        'data_inicio',
        'data_fim',
        'logo'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id_usuarios');
    }
} 