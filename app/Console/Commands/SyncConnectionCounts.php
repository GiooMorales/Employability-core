<?php

namespace App\Console\Commands;

use App\Models\Connection;
use App\Models\Usuario;
use Illuminate\Console\Command;

class SyncConnectionCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'connections:sync-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizar contadores de conexões na tabela usuarios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronização dos contadores de conexões...');

        // Resetar todos os contadores para 0
        Usuario::query()->update(['quantidade_conn' => 0]);

        // Contar conexões aceitas para cada usuário
        $connections = Connection::where('status', 'aceita')->get();
        
        $userCounts = [];
        
        foreach ($connections as $connection) {
            // Contar para user_id
            if (!isset($userCounts[$connection->user_id])) {
                $userCounts[$connection->user_id] = 0;
            }
            $userCounts[$connection->user_id]++;
            
            // Contar para connected_user_id
            if (!isset($userCounts[$connection->connected_user_id])) {
                $userCounts[$connection->connected_user_id] = 0;
            }
            $userCounts[$connection->connected_user_id]++;
        }

        // Atualizar os contadores
        foreach ($userCounts as $userId => $count) {
            Usuario::where('id_usuarios', $userId)->update(['quantidade_conn' => $count]);
        }

        $this->info('Sincronização concluída!');
        $this->info('Total de usuários atualizados: ' . count($userCounts));
        $this->info('Total de conexões aceitas processadas: ' . $connections->count());
    }
}
