<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ProjetoController;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\RepositorioController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ConnectionController;
use Illuminate\Support\Facades\Http;

// Rota raiz
Route::get('/', function () {
    return redirect()->route('login.page');
});

// Rotas públicas
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.page');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::get('/registrar', [RegisterController::class, 'showRegisterForm'])->name('registrar');
Route::post('/registrar', [RegisterController::class, 'registrar'])->name('registrar.store');

// Rotas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Rotas do perfil
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('editar');
    Route::post('/perfil/atualizar', [PerfilController::class, 'update'])->name('perfil.atualizar');
    Route::put('/perfil/senha', [PerfilController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/perfil', [PerfilController::class, 'deleteAccount'])->name('profile.delete');
    
    // Rotas para adicionar itens ao perfil
    Route::post('/perfil/experiencia', [PerfilController::class, 'addExperience'])->name('profile.experience.add');
    Route::put('/perfil/experiencia/{id}', [PerfilController::class, 'updateExperience'])->name('profile.experience.update');
    Route::delete('/perfil/experiencia/{id}', [PerfilController::class, 'deleteExperience'])->name('profile.experience.delete');
    Route::post('/perfil/educacao', [PerfilController::class, 'addEducation'])->name('profile.education');
    Route::post('/perfil/projeto', [PerfilController::class, 'addProject'])->name('profile.project');
    Route::post('/perfil/certificado', [PerfilController::class, 'addCertificate'])->name('profile.certificate');
    Route::post('/perfil/habilidade', [PerfilController::class, 'addSkill'])->name('profile.skill');
    Route::delete('/perfil/habilidade/{id}', [PerfilController::class, 'removeSkill'])->name('profile.skill.delete');
    Route::post('/perfil/remover-foto', [PerfilController::class, 'removePhoto'])->name('perfil.remover-foto');
    
    // Rota para visualizar perfil de outro usuário (deve ser a última rota do perfil)
    Route::get('/perfil/{id}', [PerfilController::class, 'show'])->name('perfil.show');
    
    Route::get('/mensagens', function () {
        return view('mensagens');
    })->name('mensagens');
    
    Route::get('/empregos', function () {
        return view('empregos');
    })->name('empregos');
    
    Route::get('/projetos', function () {
        return view('projetos');
    })->name('projetos');
    
    // Rotas para Projetos
    Route::get('/projetos/criar', [ProjetoController::class, 'create'])->name('projetos.criar');
    Route::post('/projetos', [ProjetoController::class, 'store'])->name('projetos.store');
    
    // Rotas para Certificados
    Route::get('/certificados/criar', [CertificadoController::class, 'create'])->name('certificados.criar');
    Route::post('/certificados', [CertificadoController::class, 'store'])->name('certificados.store');
    
    // Rotas para Repositórios
    Route::get('/repositorios/atualizar', [RepositorioController::class, 'update'])->name('repositorios.atualizar');
    
    Route::get('/search', [SearchController::class, 'search'])->name('search');
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::post('/connections/send/{userId}', [ConnectionController::class, 'send'])->name('connections.send');
    Route::post('/connections/accept/{connectionId}', [ConnectionController::class, 'accept'])->name('connections.accept');
    Route::post('/connections/reject/{connectionId}', [ConnectionController::class, 'reject'])->name('connections.reject');

    Route::get('/perfil/experiencias-json', [PerfilController::class, 'experienciasJson'])->name('profile.experience.json');

    Route::delete('/perfil/formacao/{id}', [PerfilController::class, 'deleteEducation'])->name('perfil.formacao.remover');
});

Route::get('/proxy/universidades', function (\Illuminate\Http\Request $request) {
    $nome = $request->query('universityName');
    $url = 'https://brazil-universities-api.herokuapp.com/search';
    $response = Http::get($url, ['universityName' => $nome]);
    return response($response->body(), $response->status())
        ->header('Content-Type', 'application/json');
});
