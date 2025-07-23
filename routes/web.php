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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ConversationController;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

// Rota de teste (opcional)
Route::get('/webtest', function () {
    return 'Web funcionando!';
});

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
    // Rota para logout/desvincular Github
    Route::post('/perfil/github-logout', [PerfilController::class, 'logoutGithub'])->name('perfil.github.logout');
    
    // Rota para visualizar perfil de outro usuário (deve ser a última rota do perfil)
    Route::get('/perfil/{id}', [PerfilController::class, 'show'])->name('perfil.show');
    
    // Rotas para Mensagens
    Route::get('/mensagens', [ConversationController::class, 'index'])->name('mensagens');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'store'])->name('messages.store');
    Route::get('/contatos', [ConversationController::class, 'contacts'])->name('contacts');
    Route::post('/conversations', [ConversationController::class, 'startConversation'])->name('conversations.start');

    // Rotas de páginas estáticas
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
    Route::get('/repositorios', [RepositorioController::class, 'index'])->name('repositorios.index');
    Route::get('/repositorios/atualizar', [RepositorioController::class, 'update'])->name('repositorios.atualizar');
    
    Route::get('/search', [SearchController::class, 'search'])->name('search');
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Rotas para conexões
    Route::post('/connections/send/{userId}', [ConnectionController::class, 'send'])->name('connections.send');
    Route::post('/connections/accept/{connectionId}', [ConnectionController::class, 'accept'])->name('connections.accept');
    Route::post('/connections/reject/{connectionId}', [ConnectionController::class, 'reject'])->name('connections.reject');
    Route::delete('/connections/{id}', [ConnectionController::class, 'destroy'])->name('connections.destroy');

    // Rotas para Notificações
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
    Route::post('/notifications/{id}/accept-connection', [NotificationController::class, 'acceptConnection'])->name('notifications.acceptConnection');
    Route::post('/notifications/{id}/reject-connection', [NotificationController::class, 'rejectConnection'])->name('notifications.rejectConnection');
    Route::get('/notifications/recent-unread', [NotificationController::class, 'getRecentUnread'])->name('notifications.recentUnread');

    // Rotas Login com Github
    Route::get('/auth/redirect', function () {
        return Socialite::driver('github')
            ->with(['prompt' => 'login'])
            ->redirect();
    });

    Route::get('/auth/callback', function () {
        $githubUser = Socialite::driver('github')->user();   

        $user = Auth::user();
        $user->github_username = $githubUser->getNickname();
        $user->github_token = $githubUser->token;
        $user->github_refresh_token = $githubUser->refreshToken ?? null;
        $user->save();

        return redirect('/perfil');
    });
});
