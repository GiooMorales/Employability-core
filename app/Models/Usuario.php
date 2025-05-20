<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuarios';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'bio',
        'cidade',
        'estado',
        'pais',
        'cargo_atual',
        'empresa_atual',
        'habilidades',
        'experiencia',
        'formacao',
        'linkedin_url',
        'github_url',
        'portfolio_url',
        'perfil_publico',
        'disponivel_para_contato',
        'link',
        'url_foto'
    ];

    protected $hidden = [
        'senha',
    ];

    protected $casts = [
        'perfil_publico' => 'boolean',
        'disponivel_para_contato' => 'boolean',
        'data_criacao' => 'datetime',
        'data_atualizacao' => 'datetime',
        'ultimo_acesso' => 'datetime',
    ];

    // Método para atualizar o último acesso
    public function atualizarUltimoAcesso()
    {
        $this->ultimo_acesso = now();
        $this->save();
    }

    // Método para verificar se o perfil está completo
    public function perfilCompleto()
    {
        return !empty($this->nome) && 
               !empty($this->email) && 
               !empty($this->cargo_atual) && 
               !empty($this->habilidades);
    }
} 