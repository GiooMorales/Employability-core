@extends('layouts.app')

@section('title', 'Mensagens')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-dyZtM3Q6QJ6Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
.clip-label i.fas.fa-paperclip {
    color: #0d6efd !important;
    transition: color 0.2s;
}
.clip-label:hover {
    background: #d0e2ff !important;
}
.clip-label:hover i.fas.fa-paperclip {
    color: #084298 !important;
}
</style>
@endpush

@section('content')
<div class="messages-container">
    <!-- Conversations List -->
    <div class="conversations-list {{ $selectedConversation ?? 'active' }}">
        <div class="conversations-header">
            <div class="conversations-title">Mensagens</div>
            <button class="new-message-btn" id="newMessageBtn">
                <i class="fas fa-plus"></i>
            </button>
        </div>
        <div class="conversations-search">
            <input type="text" id="searchConversations" placeholder="Pesquisar mensagens...">
        </div>
        <div class="conversations" id="conversationsList">
            @forelse ($conversations as $conversation)
                <a href="{{ route('conversations.show', $conversation) }}" class="conversation-item {{ isset($selectedConversation) && $selectedConversation->id == $conversation->id ? 'active' : '' }}" data-id="{{ $conversation->id }}">
                    @php
                        $otherUser = $conversation->user_one_id == Auth::id() ? $conversation->userTwo : $conversation->userOne;
                    @endphp
                    <img src="{{ $otherUser->url_foto ?: 'https://placehold.co/50x50' }}" alt="{{ $otherUser->nome }}" class="conversation-avatar">
                    <div class="conversation-info">
                        <div class="conversation-header">
                            <div class="conversation-name">{{ $otherUser->nome }}</div>
                            @if ($conversation->messages->isNotEmpty())
                                <div class="conversation-time">{{ $conversation->messages->first()->created_at->diffForHumans() }}</div>
                            @endif
                        </div>
                        <div class="conversation-preview">
                            @if ($conversation->messages->isNotEmpty())
                                {{ Str::limit($conversation->messages->first()->content, 30) }}
                            @else
                                Nenhuma mensagem
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="no-conversations">Nenhuma conversa encontrada</div>
            @endforelse
        </div>
    </div>
    
    <!-- Chat Area -->
    <div class="chat-area {{ isset($selectedConversation) ? 'active' : '' }}">
        @if (isset($selectedConversation))
            <div class="chat-header">
                 @php
                    $otherUser = $selectedConversation->user_one_id == Auth::id() ? $selectedConversation->userTwo : $selectedConversation->userOne;
                @endphp
                <div class="mobile-back" id="mobileBack">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <img src="{{ $otherUser->url_foto ?: 'https://placehold.co/40x40' }}" alt="Avatar" class="conversation-avatar" id="currentChatAvatar">
                <div class="chat-title">
                    <div class="chat-name" id="currentChatName">{{ $otherUser->nome }}</div>
                </div>
            </div>
            
            <div class="messages" id="messageArea">
                @forelse ($messages as $message)
                    <div class="message-wrapper" style="position:relative; display:flex; align-items:flex-start;">
                        <div class="message {{ $message->sender_id == Auth::id() ? 'outgoing' : 'incoming' }}" data-id="{{ $message->id }}" style="position:relative; min-width:0; margin-left: {{ $message->sender_id == Auth::id() ? 'auto' : '0' }}; margin-right: {{ $message->sender_id == Auth::id() ? '0' : 'auto' }}; {{ $message->sender_id == Auth::id() ? 'padding-right: 32px;' : '' }}">
                            @if ($message->sender_id == Auth::id())
                                <div class="message-menu-hover" style="position:absolute; top:8px; right:10px; z-index:10; display:block;">
                                    <button class="message-menu-btn" style="background:none; border:none; cursor:pointer; font-size:18px; color:#888; padding:0 4px;" onclick="toggleMessageMenu({{ $message->id }})">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="message-menu" id="message-menu-{{ $message->id }}" style="display:none; position:absolute; right:0; top:28px; background:#fff; border:1px solid #ddd; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.08); z-index:10; min-width:100px;">
                                        <button class="edit-message-btn" data-id="{{ $message->id }}" style="display:block; width:100%; background:none; border:none; padding:8px 12px; text-align:left; cursor:pointer;">Editar</button>
                                        <button class="delete-message-btn" data-id="{{ $message->id }}" style="display:block; width:100%; background:none; border:none; padding:8px 12px; text-align:left; cursor:pointer; color:#d9534f;">Excluir</button>
                                    </div>
                                </div>
                            @endif
                            @if ($message->content_type === 'image' && $message->image_path)
                                <div class="message-image">
                                    <img src="{{ asset('storage/' . $message->image_path) }}" alt="Imagem enviada" style="max-width:200px; max-height:200px; border-radius:8px; margin-bottom:5px; cursor:pointer;" class="zoomable-image">
                                </div>
                            @elseif ($message->content_type === 'file' && $message->image_path)
                                <div class="message-file" style="display:flex; align-items:center; gap:6px;">
                                    <span style="font-size:20px; color:#d9534f;">
                                        <i class="fas fa-file-alt"></i>
                                    </span>
                                    <a href="{{ asset('storage/' . $message->image_path) }}" target="_blank" style="color:#007bff; text-decoration:underline;">
                                        {{ $message->file_name ?? 'Arquivo enviado' }}
                                    </a>
                                </div>
                            @endif
                            <div class="message-content">
                                @if ($message->deleted_at)
                                    <span style="color:#888;font-style:italic;">mensagem excluída</span>
                                @else
                                    {{ $message->content }}
                                    @if ($message->edited_at)
                                        <span style="color:#888; font-size:12px; margin-left:4px;">(editado)</span>
                                    @endif
                                @endif
                            </div>
                            <div style="display:flex; justify-content:flex-end; align-items:center; margin-top:2px;">
                                <span class="message-time" style="color:#888; font-size:12px; display:flex; align-items:center; gap:4px; background:transparent;">
                                    {{ $message->created_at ? \Carbon\Carbon::parse($message->created_at)->format('H:i') : '' }}
                                    @if ($message->sender_id == Auth::id() && !empty($message->read_at))
                                        <span class="seen" style="color:#0d6efd; font-size:13px; margin-left:2px;">✔️ Visto</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="select-conversation-placeholder">
                        <i class="far fa-comments"></i>
                        <p>Selecione uma conversa para iniciar</p>
                    </div>
                @endforelse
            </div>
            
            <div class="message-input-container">
                <form id="sendMessageForm" action="{{ route('messages.store', $selectedConversation) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="display:flex; align-items:center; gap:8px; width:100%;">
                        <textarea class="message-input" name="content" id="messageInput" placeholder="Digite uma mensagem..." rows="1" style="flex:1 1 auto; width:100%; min-width:0; max-width:100%; resize:none; padding:10px 12px;"></textarea>
                        <label for="fileInput" style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:50%; background:#e9ecef; cursor:pointer; box-shadow:0 1px 3px rgba(0,0,0,0.07);">
                            <i class="fas fa-paperclip" style="font-size:20px; color:#007bff;"></i>
                        </label>
                        <input type="file" name="file" id="fileInput" style="display:none;">
                        <button class="send-btn" id="sendMessageBtn" type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
            <input type="hidden" id="currentConversationId" value="{{ $selectedConversation->id }}">
        @else
            <div class="select-conversation-placeholder">
                <i class="far fa-comments"></i>
                <p>Selecione uma conversa para iniciar</p>
            </div>
        @endif
    </div>
</div>

<!-- New Message Modal -->
<div class="modal-overlay" id="newMessageModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Nova Mensagem</h3>
            <button class="modal-close" id="closeNewMessageModal">&times;</button>
        </div>
        <div class="modal-content">
            <input type="text" class="contact-search" id="contactSearch" placeholder="Buscar contatos...">
            <div class="contact-list" id="contactList">
                <!-- Contacts will be loaded dynamically here -->
            </div>
        </div>
    </div>
</div>

<!-- Modal para imagem ampliada -->
<div id="imageModal" class="image-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.8); align-items:center; justify-content:center;">
    <span id="closeImageModal" style="position:absolute; top:30px; right:40px; color:#fff; font-size:40px; cursor:pointer;">&times;</span>
    <img id="modalImage" src="" style="max-width:90vw; max-height:90vh; border-radius:12px; box-shadow:0 0 20px #000;">
</div>
<!-- Modal de edição de mensagem -->
<div class="modal-overlay" id="editMessageModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Editar mensagem</h3>
            <button class="modal-close" id="closeEditMessageModal" style="background:none; border:none; font-size:22px; color:#888; cursor:pointer;">&times;</button>
        </div>
        <div class="modal-content">
            <textarea id="editMessageInput" rows="3" style="width:100%; border-radius:8px; border:1px solid #ddd; padding:10px; font-size:15px;"></textarea>
        </div>
        <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:10px; margin-top:10px;">
            <button id="cancelEditMessageBtn" style="background:#f3f3f3; color:#333; border:none; border-radius:20px; padding:7px 20px; font-size:15px; cursor:pointer; transition:background 0.2s;">Cancelar</button>
            <button id="saveEditMessageBtn" style="background:#0d6efd; color:#fff; border:none; border-radius:20px; padding:7px 20px; font-size:15px; cursor:pointer; transition:background 0.2s;">Salvar</button>
        </div>
    </div>
</div>
<!-- Modal de confirmação de exclusão -->
<div class="modal-overlay" id="deleteMessageModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Excluir mensagem</h3>
            <button class="modal-close" id="closeDeleteMessageModal" style="background:none; border:none; font-size:22px; color:#888; cursor:pointer;">&times;</button>
        </div>
        <div class="modal-content">
            <p>Tem certeza que deseja excluir esta mensagem?</p>
        </div>
        <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:10px; margin-top:10px;">
            <button id="cancelDeleteMessageBtn" style="background:#f3f3f3; color:#333; border:none; border-radius:20px; padding:7px 20px; font-size:15px; cursor:pointer; transition:background 0.2s;">Cancelar</button>
            <button id="confirmDeleteMessageBtn" style="background:#d9534f; color:#fff; border:none; border-radius:20px; padding:7px 20px; font-size:15px; cursor:pointer; transition:background 0.2s;">Excluir</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/chat.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.zoomable-image');
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const closeBtn = document.getElementById('closeImageModal');
    images.forEach(img => {
        img.addEventListener('click', function() {
            modal.style.display = 'flex';
            modalImg.src = this.src;
        });
    });
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        modalImg.src = '';
    });
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            modalImg.src = '';
        }
    });
    // Clique no botão de clipe aciona o input file
    const fileLabel = document.querySelector('label[for="fileInput"]');
    const fileInput = document.getElementById('fileInput');
    if (fileLabel && fileInput) {
        fileLabel.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                fileInput.click();
            }
        });
    }
    // Efeito de hover no clipe
    const clipLabel = document.querySelector('.clip-label');
    if (clipLabel) {
        clipLabel.addEventListener('mouseenter', function() {
            this.style.background = '#d0e2ff';
            this.querySelector('i').style.color = '#084298';
        });
        clipLabel.addEventListener('mouseleave', function() {
            this.style.background = '#e9ecef';
            this.querySelector('i').style.color = '#0d6efd';
        });
    }
});
</script>
@endpush