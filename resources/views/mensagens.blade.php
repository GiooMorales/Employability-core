@extends('layouts.app')

@section('title', 'Mensagens')

@push('styles')
<link rel="stylesheet" href="{{ asset('/css/chat.css') }}">
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
                        <div class="message-content">{{ $message->content }}</div>
                        <div class="message-time">{{ $message->created_at->format('H:i') }}</div>
                    </div>
                @empty
                    <div class="select-conversation-placeholder">
                        <i class="far fa-comments"></i>
                        <p>Selecione uma conversa para iniciar</p>
                    </div>
                @endforelse
            </div>
            
            <div class="message-input-container">
                <form id="sendMessageForm" action="{{ route('messages.store', $selectedConversation) }}" method="POST">
                    @csrf
                    <textarea class="message-input" name="content" id="messageInput" placeholder="Digite uma mensagem..." rows="1"></textarea>
                    <button class="send-btn" id="sendMessageBtn" type="submit">
                        <i class="fas fa-paper-plane"></i>
                    </button>
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
@endsection

@push('scripts')
<script src="{{ asset('js/chat.js') }}"></script>
@endpush