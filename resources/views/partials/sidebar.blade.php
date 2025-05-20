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
        
        <form action="{{ route('logout') }}" method="POST" class="menu-item">
            @csrf
            <button type="submit" class="menu-item-button">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </button>
        </form>
    </nav>
</aside> 