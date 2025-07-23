<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     * 
     */
     protected $table = 'usuarios';

     protected $primaryKey = 'id_usuarios';


    protected $fillable = [
        'nome',
        'titulo',
        'email',
        'senha',
        'sobre',
        'cidade',
        'estado',
        'pais',
        'url_foto',
        'link',
        'quantidade_conn',
        'github_username',
        'github_token',
        'github_refresh_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'senha',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'data_criacao' => 'datetime',
    ];

    // Sobrescrever o método getAuthPassword para usar o campo senha
    public function getAuthPassword()
    {
        return $this->senha;
    }

    // Relacionamentos
    public function skills()
    {
        return $this->hasMany(Skill::class, 'user_id', 'id_usuarios');
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class, 'usuario_id', 'id_usuarios');
    }

    public function education()
    {
        return $this->hasMany(Education::class, 'usuario_id', 'id_usuarios');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'user_id', 'id_usuarios');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'user_id', 'id_usuarios');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'user_id', 'id_usuarios');
    }

    public function repositories()
    {
        return $this->hasMany(Repository::class, 'user_id', 'id_usuarios');
    }

    public function connections()
    {
        return $this->hasMany(Connection::class, 'user_id');
    }

    public function connectedUsers()
    {
        return $this->hasMany(Connection::class, 'connected_user_id');
    }
}
