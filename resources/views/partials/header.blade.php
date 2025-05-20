<header class="header">
    <div class="logo">
        <a href="{{ route('home') }}">Employability Core</a>
    </div>
    
    <div class="search-container">
        <input type="text" class="search-input" placeholder="Pesquisar...">
    </div>
    
    <div class="user-menu">
        <div class="notification">
            <i class="fas fa-bell"></i>
            <span class="notification-count">0</span>
        </div>
        
        <div class="user-profile">
            <img src="{{ Auth::user()->url_foto ? asset('storage/' . Auth::user()->url_foto) : asset('images/default-avatar.png') }}" alt="{{ Auth::user()->nome }}" class="user-avatar">
            <span>{{ Auth::user()->nome }}</span>
        </div>
    </div>
</header> 