<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function users(Request $request)
    {
        $query = $request->input('q');
        $usuarios = \App\Models\User::query();
        if ($query) {
            $usuarios = $usuarios->where(function($q) use ($query) {
                $q->where('nome', 'like', "%$query%")
                  ->orWhere('email', 'like', "%$query%");
            });
        }
        $usuarios = $usuarios->get();
        return view('admin.users', compact('usuarios', 'query'));
    }

    public function promoteToAdmin($id)
    {
        $usuario = \App\Models\User::findOrFail($id);
        $usuario->is_admin = true;
        $usuario->save();
        return redirect()->route('admin.users')->with('success', 'Usuário promovido a admin com sucesso!');
    }

    public function demoteFromAdmin($id)
    {
        $usuario = \App\Models\User::findOrFail($id);
        $usuario->is_admin = false;
        $usuario->save();
        return redirect()->route('admin.users')->with('success', 'Usuário rebaixado de admin com sucesso!');
    }

    public function banUser(Request $request, $id)
    {
        $usuario = \App\Models\User::findOrFail($id);
        $usuario->banido = true;
        $usuario->motivo = $request->input('motivo', null);
        $usuario->save();
        return redirect()->route('admin.users')->with('success', 'Usuário banido permanentemente!');
    }

    public function unbanUser($id)
    {
        $usuario = \App\Models\User::findOrFail($id);
        $usuario->banido = false;
        $usuario->motivo = null;
        $usuario->save();
        return redirect()->route('admin.users')->with('success', 'Usuário desbanido com sucesso!');
    }

    public function suspendUser(Request $request, $id)
    {
        $usuario = \App\Models\User::findOrFail($id);
        $dias = (int) $request->input('dias', 0);
        $horas = (int) $request->input('horas', 0);
        $minutos = (int) $request->input('minutos', 0);
        $motivo = $request->input('motivo', null);
        $suspensoAte = now()->addDays($dias)->addHours($horas)->addMinutes($minutos);
        $usuario->suspenso_ate = $suspensoAte;
        $usuario->motivo = $motivo;
        $usuario->save();
        return redirect()->route('admin.users')->with('success', 'Usuário suspenso até ' . $suspensoAte->format('d/m/Y H:i') . '!');
    }

    public function unsuspendUser($id)
    {
        $usuario = \App\Models\User::findOrFail($id);
        $usuario->suspenso_ate = null;
        $usuario->save();
        return redirect()->route('admin.users')->with('success', 'Suspensão removida!');
    }

    public function dashboard()
    {
        $totalUsuarios = \App\Models\Usuario::count();
        $totalEmpresas = 0; // Placeholder para empresas futuras
        // Removido: novosUsuariosMes, usuariosAtivos, usuariosInativos, totalAdmins (não existem essas colunas)

        $totalConexoes = \App\Models\Connection::count();
        $novasConexoesMes = 0; // Removido: whereMonth('created_at') pois não existe created_at
        $conexoesPendentes = \App\Models\Connection::where('status', 'pendente')->count();

        $totalMensagens = \App\Models\Message::count();
        $totalConversas = \App\Models\Conversation::count();
        $novasConversasMes = 0; // Removido: whereMonth('created_at') pois não existe created_at

        $totalCertificados = \App\Models\Certificate::count();
        $certificadosMes = 0; // Removido: whereMonth('created_at') pois não existe created_at

        $totalProjetos = \App\Models\Project::count();
        // Removido: novosProjetosMes

        $totalNotificacoes = \DB::table('notifications')->count();

        return view('admin.dashboard', compact(
            'totalUsuarios',
            'totalEmpresas',
            'totalConexoes', 'novasConexoesMes', 'conexoesPendentes',
            'totalMensagens', 'totalConversas', 'novasConversasMes',
            'totalCertificados', 'certificadosMes',
            'totalProjetos',
            'totalNotificacoes'
        ));
    }
} 