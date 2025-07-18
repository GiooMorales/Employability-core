// Rotas do perfil
use App\Http\Controllers\ConversationController;

Route::middleware('auth')->group(function () {
    Route::get('/perfil/{id}', [PerfilController::class, 'mostrar']);
    Route::put('/perfil/{id}', [PerfilController::class, 'atualizar']);
    Route::put('/perfil/{id}/senha', [PerfilController::class, 'atualizarSenha']);
    Route::put('/perfil/{id}/status', [PerfilController::class, 'atualizarStatus']);
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::get('/conversations/{id}/messages', [ConversationController::class, 'messages']);
    Route::post('/conversations/{id}/messages', [ConversationController::class, 'sendMessage']);
    Route::post('/conversations/{id}/read', [ConversationController::class, 'markAsRead']);
    Route::get('/contacts', [ConversationController::class, 'contacts']);
    Route::post('/conversations', [ConversationController::class, 'startConversation']);
}); 
Route::get('/apitest', function () {
    return 'API test funcionando!';
});