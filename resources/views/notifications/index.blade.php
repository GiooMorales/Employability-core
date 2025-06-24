@extends('layouts.app')

@section('title', 'Notificações')

@section('content')
<div class="notifications-container">
    <div class="notifications-header">
        <h1>Notificações</h1>
        @if($notifications->count() > 0)
            <button class="btn btn-outline" onclick="markAllAsRead()">
                <i class="fas fa-check-double"></i> Marcar todas como lidas
            </button>
        @endif
    </div>

    <div class="notifications-list">
        @forelse($notifications as $notification)
            <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}" data-id="{{ $notification->id }}">
                <div class="notification-avatar">
                    @if($notification->data['from_user_avatar'])
                        <img src="{{ asset('storage/' . $notification->data['from_user_avatar']) }}" alt="{{ $notification->data['from_user_name'] }}">
                    @else
                        <img src="{{ asset('images/default-avatar.png') }}" alt="{{ $notification->data['from_user_name'] }}">
                    @endif
                </div>
                
                <div class="notification-content">
                    <div class="notification-message">
                        {{ $notification->data['message'] }}
                    </div>
                    <div class="notification-time">
                        {{ $notification->created_at->diffForHumans() }}
                    </div>
                </div>

                @if($notification->data['type'] === 'connection_request' && !$notification->read_at)
                    <div class="notification-actions">
                        <button class="btn-accept" onclick="acceptConnection('{{ $notification->id }}')" title="Aceitar">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn-reject" onclick="rejectConnection('{{ $notification->id }}')" title="Recusar">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="no-notifications">
                <i class="fas fa-bell-slash"></i>
                <p>Nenhuma notificação encontrada</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="notifications-pagination">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

<style>
.notifications-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.notifications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.notifications-header h1 {
    margin: 0;
    color: #333;
}

.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.notification-item {
    display: flex;
    align-items: center;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.notification-item.unread {
    border-left-color: #0a66c2;
    background-color: #f8f9ff;
}

.notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

.notification-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 15px;
    flex-shrink: 0;
}

.notification-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-message {
    font-weight: 500;
    color: #333;
    margin-bottom: 5px;
}

.notification-time {
    font-size: 12px;
    color: #666;
}

.notification-actions {
    display: flex;
    gap: 10px;
    margin-left: 15px;
}

.btn-accept, .btn-reject {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 16px;
}

.btn-accept {
    background-color: #28a745;
    color: white;
}

.btn-accept:hover {
    background-color: #218838;
    transform: scale(1.1);
}

.btn-reject {
    background-color: #dc3545;
    color: white;
}

.btn-reject:hover {
    background-color: #c82333;
    transform: scale(1.1);
}

.no-notifications {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-notifications i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.no-notifications p {
    font-size: 16px;
    margin: 0;
}

.notifications-pagination {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

.btn {
    padding: 8px 16px;
    border-radius: 20px;
    border: 1px solid #ddd;
    background: white;
    color: #333;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn:hover {
    background-color: #f8f9fa;
    border-color: #0a66c2;
    color: #0a66c2;
}

.btn-outline {
    border-color: #0a66c2;
    color: #0a66c2;
}

.btn-outline:hover {
    background-color: #0a66c2;
    color: white;
}
</style>

<script>
function acceptConnection(notificationId) {
    fetch(`/notifications/${notificationId}/accept-connection`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
            notificationItem.classList.remove('unread');
            notificationItem.classList.add('read');
            
            // Remover os botões de ação
            const actions = notificationItem.querySelector('.notification-actions');
            if (actions) {
                actions.remove();
            }
            
            // Mostrar mensagem de sucesso
            showMessage(data.message, 'success');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showMessage('Erro ao aceitar conexão', 'error');
    });
}

function rejectConnection(notificationId) {
    fetch(`/notifications/${notificationId}/reject-connection`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
            notificationItem.classList.remove('unread');
            notificationItem.classList.add('read');
            
            // Remover os botões de ação
            const actions = notificationItem.querySelector('.notification-actions');
            if (actions) {
                actions.remove();
            }
            
            // Mostrar mensagem de sucesso
            showMessage(data.message, 'success');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showMessage('Erro ao recusar conexão', 'error');
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Marcar todas as notificações como lidas
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
                item.classList.add('read');
            });
            
            showMessage('Todas as notificações foram marcadas como lidas', 'success');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showMessage('Erro ao marcar notificações como lidas', 'error');
    });
}

function showMessage(message, type) {
    // Criar elemento de mensagem
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        z-index: 1000;
        animation: slideIn 0.3s ease;
        ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
    `;
    
    document.body.appendChild(messageDiv);
    
    // Remover mensagem após 3 segundos
    setTimeout(() => {
        messageDiv.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(messageDiv);
        }, 300);
    }, 3000);
}

// Adicionar estilos para animações
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
@endsection 