<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $table = 'experiencias_profissionais';

    protected $fillable = [
        'usuario_id',
        'cargo',
        'empresa_nome',
        'descricao',
        'data_inicio',
        'data_fim',
        'tipo',
        'modalidade',
        'conquistas',
        'atual',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id_usuarios');
    }
} 