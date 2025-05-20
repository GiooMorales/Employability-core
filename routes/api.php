// Rotas do perfil
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/perfil/{id}', [PerfilController::class, 'mostrar']);
    Route::put('/perfil/{id}', [PerfilController::class, 'atualizar']);
    Route::put('/perfil/{id}/senha', [PerfilController::class, 'atualizarSenha']);
    Route::put('/perfil/{id}/status', [PerfilController::class, 'atualizarStatus']);
}); 