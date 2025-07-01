<header class="header">
    <div class="logo">
        <a href="{{ route('home') }}">Employability Core</a>
    </div>
    
    <div class="search-container">
        <input type="text" class="search-input" placeholder="Pesquisar...">
    </div>
    
    <div class="user-menu">
        <div class="notification" id="notificationBell" style="position: relative;" onclick="window.location.href='{{ route('notifications.index') }}'">
            <i class="fas fa-bell"></i>
            <span class="notification-count" id="notificationCount">{{ Auth::user()->unreadNotifications->count() }}</span>
            <div id="notificationPopover" style="display:none; position:absolute; top:35px; right:0; width:340px; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.15); border-radius:8px; z-index:1001;">
                <div id="popoverContent" style="padding:10px 0;"></div>
            </div>
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

// Popover de notificações
const bell = document.getElementById('notificationBell');
const popover = document.getElementById('notificationPopover');
const popoverContent = document.getElementById('popoverContent');
let popoverTimeout;

bell.addEventListener('mouseenter', function() {
    clearTimeout(popoverTimeout);
    popover.style.display = 'block';
    fetch('/notifications/recent-unread')
        .then(response => response.json())
        .then(data => {
            if (data.notifications.length === 0) {
                popoverContent.innerHTML = '<div style="padding: 16px; text-align:center; color:#888;"><i class="fas fa-bell-slash"></i> Nenhuma notificação nova</div>';
            } else {
                popoverContent.innerHTML = data.notifications.map(n => `
                    <div style="display:flex; align-items:center; gap:10px; padding:10px 16px; border-bottom:1px solid #f0f0f0;">
                        <img src="${n.from_user_avatar ? '/storage/' + n.from_user_avatar : '/images/default-avatar.png'}" alt="${n.from_user_name || ''}" style="width:36px; height:36px; border-radius:50%; object-fit:cover;">
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:14px; color:#333; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${n.message}</div>
                            <div style="font-size:12px; color:#888;">${n.created_at}</div>
                        </div>
                    </div>
                `).join('');
            }
        });
});

bell.addEventListener('mouseleave', function() {
    popoverTimeout = setTimeout(() => {
        popover.style.display = 'none';
    }, 200);
});
popover.addEventListener('mouseenter', function() {
    clearTimeout(popoverTimeout);
    popover.style.display = 'block';
});
popover.addEventListener('mouseleave', function() {
    popoverTimeout = setTimeout(() => {
        popover.style.display = 'none';
    }, 200);
});
</script> 