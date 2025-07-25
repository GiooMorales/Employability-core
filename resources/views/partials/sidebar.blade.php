<aside class="sidebar">
    <nav class="menu">
        <a href="{{ route('home') }}" class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Início</span>
        </a>
        
        <a href="{{ route('perfil') }}" class="menu-item {{ request()->routeIs('perfil*') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Meu Perfil</span>
        </a>
        
        <a href="{{ route('mensagens') }}" class="menu-item {{ request()->routeIs('mensagens') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i>
            <span>Mensagens</span>
        </a>
        
        <a href="{{ route('empregos') }}" class="menu-item {{ request()->routeIs('empregos') ? 'active' : '' }}">
            <i class="fas fa-briefcase"></i>
            <span>Empregos</span>
        </a>
        
        <a href="{{ route('projetos') }}" class="menu-item {{ request()->routeIs('projetos*') ? 'active' : '' }}">
            <i class="fas fa-project-diagram"></i>
            <span>Projetos</span>
        </a>

        {{-- Itens exclusivos para admin --}}
        @auth
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span>Painel de Admin</span>
                </a>
                {{-- Adicione mais abas de admin aqui --}}
            @endif
        @endauth
        
        <form action="{{ route('logout') }}" method="POST" class="menu-item">
            @csrf
            <button type="submit" class="menu-item-button">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </button>
        </form>
    </nav>
</aside> 
<!-- Barra inferior para mobile -->
<div class="bottom-navbar">
    <a href="{{ route('home') }}" class="@if(request()->routeIs('home')) active @endif"><i class="fa fa-home"></i><span class="nav-label">Início</span></a>
    <a href="{{ route('postagens.index') }}" class="@if(request()->routeIs('postagens.index')) active @endif"><i class="fa fa-bullhorn"></i><span class="nav-label">Posts</span></a>
    <a href="{{ route('perfil') }}" class="@if(request()->routeIs('perfil')) active @endif"><i class="fa fa-user"></i><span class="nav-label">Perfil</span></a>
    <a href="{{ route('mensagens') }}" class="@if(request()->routeIs('mensagens')) active @endif"><i class="fa fa-comments"></i><span class="nav-label">Chat</span></a>
</div> 