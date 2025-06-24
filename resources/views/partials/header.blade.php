<header class="header">
    <div class="logo">
        <a href="{{ route('home') }}">Employability Core</a>
    </div>
    
    <div class="search-container">
        <input type="text" class="search-input" placeholder="Pesquisar...">
    </div>
    
    <div class="user-menu">
        <div class="notification" onclick="window.location.href='{{ route('notifications.index') }}'">
            <i class="fas fa-bell"></i>
            <span class="notification-count" id="notificationCount">{{ Auth::user()->unreadNotifications->count() }}</span>
        </div>
        
        <div class="user-profile">
            <img src="{{ Auth::user()->url_foto ? asset('storage/' . Auth::user()->url_foto) : asset('images/default-avatar.png') }}" alt="{{ Auth::user()->nome }}" class="user-avatar">
            <span>{{ Auth::user()->nome }}</span>
        </div>
    </div>
</header>

<script>
// Atualizar contador de notificações periodicamente
function updateNotificationCount() {
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const countElement = document.getElementById('notificationCount');
            countElement.textContent = data.count;
            
            // Mostrar/ocultar o contador baseado no número
            if (data.count > 0) {
                countElement.style.display = 'block';
            } else {
                countElement.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar contador de notificações:', error);
        });
}

// Atualizar contador a cada 30 segundos
setInterval(updateNotificationCount, 30000);

// Atualizar contador na carga da página
document.addEventListener('DOMContentLoaded', updateNotificationCount);
</script> 