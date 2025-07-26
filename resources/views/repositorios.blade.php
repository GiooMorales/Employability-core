@if(isset($repos) && count($repos) > 0)
    @if(Auth::id() === $usuario->id_usuarios)
        <form method="POST" action="{{ route('perfil.github.logout') }}" style="text-align:right; margin-bottom: 18px;">
            @csrf
            <button type="submit" style="background:#dc3545; color:#fff; border:none; border-radius:5px; padding:8px 18px; font-weight:500; cursor:pointer; font-size:15px;">Desconectar do GitHub</button>
        </form>
    @endif
    <div style="display: flex; flex-wrap: wrap; gap: 24px; justify-content: center;">
        @foreach($repos as $repo)
            <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 24px; width: 320px; min-height: 120px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <strong style="font-size: 20px; color: #24292e;">{{ $repo['name'] }}</strong>
                    <p style="color: #586069; margin: 12px 0 0 0; font-size: 15px; min-height: 40px;">{{ $repo['description'] ?? 'Sem descrição' }}</p>
                </div>
                <div style="margin-top: 16px;">
                    <a href="{{ $repo['html_url'] }}" target="_blank" style="color: #fff; background: #24292e; padding: 8px 16px; border-radius: 5px; text-decoration: none; font-weight: 500; font-size: 15px; display: inline-block;">Ver no GitHub</a>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div style="text-align:center;">
        @if(Auth::id() === $usuario->id_usuarios)
            <a href="/auth/redirect" class="github-login-btn" style="display:inline-flex;align-items:center;justify-content:center;background-color:#24292e;color:white;border:none;border-radius:6px;padding:12px 24px;font-size:16px;font-weight:500;cursor:pointer;text-decoration:none;transition:background-color 0.2s ease;min-width:200px;margin-bottom:18px;">
                <svg class="github-icon" viewBox="0 0 24 24" style="width:20px;height:20px;margin-right:8px;fill:currentColor;"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.770.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                Entrar com GitHub
            </a>
        @endif
        <p style="color:#888;">Nenhum repositório encontrado para este usuário.</p>
    </div>
@endif