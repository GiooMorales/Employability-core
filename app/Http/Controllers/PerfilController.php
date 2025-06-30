<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Connection;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $usuario = Auth::user();
        $habilidades = $usuario->skills;
        $experiencias = $usuario->experiences;
        $formacoes = $usuario->education;
        
        $estatisticas = [
            'conexoes' => $usuario->connections()->where('status', 'aceita')->count(),
            'projetos' => $usuario->projects()->count(),
            'certificados' => $usuario->certificates()->count(),
            'contribuicoes' => 0 // TODO: Implementar contagem de contribuições
        ];
        
        return view('perfil', compact('usuario', 'habilidades', 'experiencias', 'formacoes', 'estatisticas'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('editar', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Debug temporário
        \Log::info('Dados recebidos:', $request->all());
        
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'profissao' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:100',
            'link' => 'nullable|url|max:255',
            'url_foto' => 'nullable|image|mimes:jpeg,png|max:2048'
        ]);
        
        // Processar upload da foto
        if ($request->hasFile('url_foto')) {
            // Deletar foto antiga se existir
            if ($user->url_foto) {
                Storage::disk('public')->delete($user->url_foto);
            }
            
            // Salvar nova foto
            $path = $request->file('url_foto')->store('fotos', 'public');
            $validated['url_foto'] = $path;
        }
        
        $user->update($validated);
        
        // Salvar experiências profissionais
        if ($request->filled('experiencias_json')) {
            $experiencias = json_decode($request->input('experiencias_json'), true);
            if (is_array($experiencias)) {
                // Remove experiências antigas do usuário
                $user->experiences()->delete();
                foreach ($experiencias as $exp) {
                    // Validação básica dos campos
                    if (!empty($exp['cargo']) && !empty($exp['empresa']) && !empty($exp['dataInicio']) && !empty($exp['descricao'])) {
                        $user->experiences()->create([
                            'cargo' => $exp['cargo'],
                            'empresa_nome' => $exp['empresa'],
                            'descricao' => $exp['descricao'],
                            'data_inicio' => $exp['dataInicio'],
                            'data_fim' => $exp['dataFim'] ?? null,
                            'tipo' => $exp['tipo'] ?? null,
                            'modalidade' => $exp['modalidade'] ?? null,
                            'conquistas' => $exp['conquistas'] ?? null,
                            'atual' => $exp['atual'] ?? false,
                        ]);
                    }
                }
            }
        }
        
        // Debug temporário
        \Log::info('Usuário após atualização:', $user->toArray());
        
        return redirect()->route('perfil')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $user->senha = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Senha atualizada com sucesso!');
    }

    public function deleteAccount()
    {
        $user = Auth::user();
        
        if ($user->url_foto) {
            Storage::delete('public/' . $user->url_foto);
        }
        
        $user->delete();
        
        Auth::logout();
        
        return redirect('/')->with('success', 'Sua conta foi excluída com sucesso.');
    }

    public function addEducation(Request $request)
    {
        $validated = $request->validate([
            'instituicao' => 'required|string|max:255',
            'curso' => 'required|string|max:255',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date',
            'logo' => 'nullable|image|max:2048'
        ]);

        $user = Auth::user();
        
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('education-logos', 'public');
            $validated['logo'] = $path;
        }

        $user->education()->create($validated);

        return redirect()->back()->with('success', 'Formação adicionada com sucesso!');
    }

    public function addSkill(Request $request)
    {
        \Log::info('Recebendo requisição para adicionar habilidade:', $request->all());

        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'nivel' => 'required|string|max:50'
        ]);

        \Log::info('Dados validados:', $validated);

        $user = Auth::user();
        $skill = $user->skills()->create($validated);

        \Log::info('Habilidade criada:', $skill->toArray());

        return response()->json([
            'success' => true,
            'skill' => $skill
        ]);
    }

    public function removeSkill($id)
    {
        $user = Auth::user();
        $skill = $user->skills()->findOrFail($id);
        $skill->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function removePhoto()
    {
        $user = Auth::user();
        
        if ($user->url_foto) {
            // Deletar o arquivo físico
            Storage::disk('public')->delete($user->url_foto);
            
            // Limpar o campo no banco
            $user->url_foto = null;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Foto removida com sucesso!'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Nenhuma foto encontrada.'
        ]);
    }

    public function show($id)
    {
        $usuario = User::findOrFail($id);
        $habilidades = $usuario->skills;
        $experiencias = $usuario->experiences;
        $formacoes = $usuario->education;
        
        $estatisticas = [
            'conexoes' => 0, // TODO: Implementar contagem de conexões
            'projetos' => $usuario->projects()->count(),
            'certificados' => $usuario->certificates()->count(),
            'contribuicoes' => 0 // TODO: Implementar contagem de contribuições
        ];
        
        $statusConexao = null;

        if (auth()->check()) {
            $connection = Connection::where('user_id', auth()->id())
                ->where('connected_user_id', $id)
                ->first();
            if ($connection) {
                $statusConexao = $connection->status;
            }
        }

        return view('perfil', compact('usuario', 'habilidades', 'experiencias', 'formacoes', 'estatisticas', 'statusConexao'));
    }
}
