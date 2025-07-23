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
        $repos = [];
        if ($usuario->github_username && $usuario->github_token) {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json',
                'Authorization' => 'token ' . $usuario->github_token,
            ])->get("https://api.github.com/users/{$usuario->github_username}/repos");
            if ($response->ok()) {
                $repos = $response->json();
            }
        }
        $estatisticas = [
            'conexoes' => $usuario->connections()->where('status', 'aceita')->count(),
            'projetos' => $usuario->projects()->count(),
            'certificados' => $usuario->certificates()->count(),
            'contribuicoes' => 0 // TODO: Implementar contagem de contribuições
        ];
        return view('perfil', compact('usuario', 'habilidades', 'experiencias', 'formacoes', 'estatisticas', 'repos'));
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

        // Salvar formações acadêmicas
        if ($request->filled('formacoes_json')) {
            $formacoes = json_decode($request->input('formacoes_json'), true);
            if (is_array($formacoes)) {
                // Remove todas as formações antigas do usuário
                $user->education()->delete();
                $formacoesValidas = array_filter($formacoes, function($f) {
                    return !empty($f['curso']) && !empty($f['universidade']) && !empty($f['data_inicio']);
                });
                if (count($formacoesValidas) > 0) {
                    foreach ($formacoesValidas as $f) {
                        $user->education()->create([
                            'curso' => $f['curso'],
                            'instituicao' => $f['universidade'],
                            'data_inicio' => $f['data_inicio'],
                            'data_fim' => $f['data_fim'] ?? null,
                            'logo' => null,
                            'nivel' => $f['nivel'] ?? null,
                            'situacao' => $f['situacao'] ?? null
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

    // Adicionar experiência profissional via AJAX
    public function addExperience(Request $request)
    {
        \Log::info('addExperience chamado', $request->all());
        $validated = $request->validate([
            'cargo' => 'required|string|max:100',
            'empresa_nome' => 'required|string|max:100',
            'descricao' => 'required|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date',
            'tipo' => 'nullable|string|max:50',
            'modalidade' => 'nullable|string|max:50',
            'conquistas' => 'nullable|string',
            'atual' => 'nullable|boolean',
        ]);
        $user = Auth::user();
        $validated['usuario_id'] = $user->id_usuarios;
        $exp = \App\Models\Experience::create($validated);
        \Log::info('Experiência criada:', $exp->toArray());
        return response()->json(['success' => true, 'experience' => $exp]);
    }

    // Editar experiência profissional via AJAX
    public function updateExperience(Request $request, $id)
    {
        $validated = $request->validate([
            'cargo' => 'required|string|max:100',
            'empresa_nome' => 'required|string|max:100',
            'descricao' => 'required|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date',
            'tipo' => 'nullable|string|max:50',
            'modalidade' => 'nullable|string|max:50',
            'conquistas' => 'nullable|string',
            'atual' => 'nullable|boolean',
        ]);
        $user = Auth::user();
        $exp = $user->experiences()->findOrFail($id);
        $exp->update($validated);
        return response()->json(['success' => true, 'experience' => $exp]);
    }

    // Remover experiência profissional via AJAX
    public function deleteExperience($id)
    {
        $user = Auth::user();
        $exp = $user->experiences()->findOrFail($id);
        $exp->delete();
        return response()->json(['success' => true]);
    }

    // Retornar experiências profissionais do usuário autenticado em JSON
    public function experienciasJson()
    {
        $user = Auth::user();
        $experiencias = $user->experiences()->orderByDesc('data_inicio')->get();
        return response()->json(['experiencias' => $experiencias]);
    }

    public function deleteEducation($id)
    {
        $user = Auth::user();
        $formacao = $user->education()->find($id);
        if (!$formacao) {
            return response()->json(['success' => false, 'message' => 'Formação não encontrada.'], 404);
        }
        $formacao->delete();
        return response()->json(['success' => true]);
    }

    public function logoutGithub()
    {
        $user = Auth::user();
        $user->github_token = null;
        $user->github_refresh_token = null;
        $user->github_username = null;
        $user->save();
        return redirect()->route('perfil')->with('success', 'Desconectado do GitHub com sucesso!');
    }
}
