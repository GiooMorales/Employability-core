<?php

namespace App\Observers;

use App\Models\Connection;
use App\Models\Usuario;

class ConnectionObserver
{
    /**
     * Handle the Connection "created" event.
     */
    public function created(Connection $connection): void
    {
        // Não faz nada na criação, pois começa como 'pendente'
    }

    /**
     * Handle the Connection "updated" event.
     */
    public function updated(Connection $connection): void
    {
        // Verificar se o status mudou para 'aceita'
        if ($connection->wasChanged('status') && $connection->status === 'aceita') {
            $this->updateUserConnectionCounts($connection);
        }
        
        // Verificar se o status mudou de 'aceita' para outro status
        if ($connection->wasChanged('status') && $connection->getOriginal('status') === 'aceita' && $connection->status !== 'aceita') {
            $this->decreaseUserConnectionCounts($connection);
        }
    }

    /**
     * Handle the Connection "deleted" event.
     */
    public function deleted(Connection $connection): void
    {
        // Se a conexão estava aceita, diminuir o contador
        if ($connection->status === 'aceita') {
            $this->decreaseUserConnectionCounts($connection);
        }
    }

    /**
     * Handle the Connection "restored" event.
     */
    public function restored(Connection $connection): void
    {
        // Se a conexão restaurada estava aceita, aumentar o contador
        if ($connection->status === 'aceita') {
            $this->updateUserConnectionCounts($connection);
        }
    }

    /**
     * Handle the Connection "force deleted" event.
     */
    public function forceDeleted(Connection $connection): void
    {
        // Se a conexão estava aceita, diminuir o contador
        if ($connection->status === 'aceita') {
            $this->decreaseUserConnectionCounts($connection);
        }
    }

    /**
     * Atualizar contadores de conexões dos usuários
     */
    private function updateUserConnectionCounts(Connection $connection): void
    {
        $user = Usuario::find($connection->user_id);
        $connectedUser = Usuario::find($connection->connected_user_id);
        
        if ($user) {
            $user->increment('quantidade_conn');
        }
        
        if ($connectedUser) {
            $connectedUser->increment('quantidade_conn');
        }
    }

    /**
     * Diminuir contadores de conexões dos usuários
     */
    private function decreaseUserConnectionCounts(Connection $connection): void
    {
        $user = Usuario::find($connection->user_id);
        $connectedUser = Usuario::find($connection->connected_user_id);
        
        if ($user && $user->quantidade_conn > 0) {
            $user->decrement('quantidade_conn');
        }
        
        if ($connectedUser && $connectedUser->quantidade_conn > 0) {
            $connectedUser->decrement('quantidade_conn');
        }
    }
}
