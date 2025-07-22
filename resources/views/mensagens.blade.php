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
                    <div class="message {{ $message->sender_id == Auth::id() ? 'outgoing' : 'incoming' }}" data-id="{{ $message->id }}">
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
                        <div class="message-content">{{ $message->content }}</div>
                        <div class="message-time">
                            {{ $message->created_at ? \Carbon\Carbon::parse($message->created_at)->format('H:i') : '' }}
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