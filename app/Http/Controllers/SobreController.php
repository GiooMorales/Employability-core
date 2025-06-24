<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Connection;

class SobreController extends Controller
{

        public function show($id)
        {
            $users = User::findOrFail($id);
            $sobre = $users->sobre;
            $statusConexao = null;
    
            if (auth()->check()) {
                $connection = Connection::where('user_id', auth()->id())
                    ->where('connected_user_id', $id)
                    ->first();
                if ($connection) {
                    $statusConexao = $connection->status;
                }
            }
    
            return view('sobre', [
                'sobre' => $users->sobre,

       
        ]);
    }

}

