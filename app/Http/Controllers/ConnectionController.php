<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use Illuminate\Http\Request;

class ConnectionController extends Controller
{
    public function send(Request $request, $userId)
    {
        $connection = new Connection();
        $connection->user_id = auth()->id();
        $connection->connected_user_id = $userId;
        $connection->status = 'pendente';
        $connection->save();

        return redirect()->back()->with('success', 'Solicitação de conexão enviada!');
    }

    public function accept($connectionId)
    {
        $connection = Connection::findOrFail($connectionId);
        $connection->status = 'aceita';
        $connection->save();

        return redirect()->back()->with('success', 'Conexão aceita!');
    }

    public function reject($connectionId)
    {
        $connection = Connection::findOrFail($connectionId);
        $connection->status = 'recusada';
        $connection->save();

        return redirect()->back()->with('success', 'Conexão recusada!');
    }
} 