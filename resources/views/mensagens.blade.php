<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employability Core - Mensagens</title>
    <link rel="stylesheet" href="{{ asset('/css/chat.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
@extends('layouts.app')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="messages-container">
            <!-- Conversations List -->
            <div class="conversations-list">
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
                    <!-- Conversations will be loaded dynamically here -->
                </div>
            </div>
            
            <!-- Chat Area -->
            <div class="chat-area">
                <div class="chat-header">
                    <div class="mobile-back" id="mobileBack">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    <img src="/api/placeholder/40/40" alt="Avatar" class="conversation-avatar" id="currentChatAvatar">
                    <div class="chat-title">
                        <div class="chat-name" id="currentChatName">Selecione uma conversa</div>
                        <div class="chat-status" id="currentChatStatus"></div>
                    </div>
                    <div class="chat-actions">
                        <div class="chat-action">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="chat-action">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="chat-action">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                </div>
                
                <div class="messages" id="messageArea">
                    <!-- Messages will be loaded dynamically here -->
                    <div class="select-conversation-placeholder">
                        <i class="far fa-comments"></i>
                        <p>Selecione uma conversa para iniciar</p>
                    </div>
                </div>
                
                <div class="message-input-container">
                    <textarea class="message-input" id="messageInput" placeholder="Digite uma mensagem..." rows="1"></textarea>
                    <div class="message-actions">
                        <div class="message-action">
                            <label for="fileUpload">
                                <i class="fas fa-paperclip"></i>
                            </label>
                            <input type="file" id="fileUpload" style="display: none;">
                        </div>
                        <div class="message-action">
                            <i class="fas fa-code" id="codeSnippetBtn"></i>
                        </div>
                        <div class="message-action">
                            <i class="far fa-smile" id="emojiPickerBtn"></i>
                        </div>
                    </div>
                    <button class="send-btn" id="sendMessageBtn" disabled>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
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

    <!-- Code Snippet Modal -->
    <div class="modal-overlay" id="codeSnippetModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Inserir Código</h3>
                <button class="modal-close" id="closeCodeModal">&times;</button>
            </div>
            <div class="modal-content">
                <select id="codeLanguage">
                    <option value="javascript">JavaScript</option>
                    <option value="python">Python</option>
                    <option value="php">PHP</option>
                    <option value="java">Java</option>
                    <option value="html">HTML</option>
                    <option value="css">CSS</option>
                    <option value="sql">SQL</option>
                </select>
                <textarea id="codeContent" placeholder="Cole seu código aqui..." rows="10"></textarea>
                <button id="insertCodeBtn" class="primary-btn">Inserir Código</button>
            </div>
        </div>
    </div>

    <!-- Emoji Picker (will be implemented with a library) -->
    <div class="emoji-picker-container" id="emojiPicker">
        <!-- Emoji picker will be rendered here -->
    </div>

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    $(document).ready(function() {
        // Global variables
        let currentConversationId = null;
        let lastMessageId = 0;
        const userId = {{ Auth::id() }};
        
        // Initialize tooltips, emoji picker, etc.
        initComponents();
        
        // Load conversations
        loadConversations();
        
        // Event listeners
        $("#newMessageBtn").on("click", openNewMessageModal);
        $("#closeNewMessageModal").on("click", closeNewMessageModal);
        
        $("#codeSnippetBtn").on("click", openCodeSnippetModal);
        $("#closeCodeModal").on("click", closeCodeSnippetModal);
        $("#insertCodeBtn").on("click", insertCodeSnippet);
        
        $("#emojiPickerBtn").on("click", toggleEmojiPicker);
        
        $("#messageInput").on("input", function() {
            // Enable/disable send button based on input
            $("#sendMessageBtn").prop("disabled", $(this).val().trim() === "");
            
            // Auto resize the textarea
            this.style.height = '38px';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        $("#searchConversations").on("input", function() {
            const searchTerm = $(this).val().toLowerCase();
            filterConversations(searchTerm);
        });
        
        $("#contactSearch").on("input", function() {
            const searchTerm = $(this).val().toLowerCase();
            filterContacts(searchTerm);
        });
        
        $("#sendMessageBtn").on("click", sendMessage);
        $("#mobileBack").on("click", showConversationsList);
        
        // Handle message input enter key
        $("#messageInput").on("keydown", function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!$("#sendMessageBtn").prop("disabled")) {
                    sendMessage();
                }
            }
        });
        
        // Setup Pusher for real-time messaging
        setupPusher();
        
        // Functions
        function initComponents() {
            // Initialize components here (tooltips, emoji picker, etc.)
            // This will depend on the libraries you choose to use
        }
        
        function loadConversations() {
            $.ajax({
                url: '/api/conversations',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    renderConversations(response.conversations);
                },
                error: function(error) {
                    console.error('Error loading conversations:', error);
                }
            });
        }
        
        function renderConversations(conversations) {
            const conversationsList = $("#conversationsList");
            conversationsList.empty();
            
            if (conversations.length === 0) {
                conversationsList.html('<div class="no-conversations">Nenhuma conversa encontrada</div>');
                return;
            }
            
            conversations.forEach(conversation => {
                const conversationItem = $(`
                    <div class="conversation-item" data-id="${conversation.id}">
                        <img src="${conversation.avatar || '/api/placeholder/50/50'}" alt="${conversation.name}" class="conversation-avatar">
                        <div class="conversation-info">
                            <div class="conversation-header">
                                <div class="conversation-name">${conversation.name}</div>
                                <div class="conversation-time">${formatTime(conversation.last_message_time)}</div>
                            </div>
                            <div class="conversation-preview">${conversation.last_message || 'Nenhuma mensagem'}</div>
                        </div>
                        ${conversation.unread_count > 0 ? `<span class="unread-badge">${conversation.unread_count}</span>` : ''}
                    </div>
                `);
                
                conversationItem.on('click', function() {
                    loadConversation(conversation.id);
                });
                
                conversationsList.append(conversationItem);
            });
        }
        
        function loadConversation(conversationId) {
            // Set as current conversation
            currentConversationId = conversationId;
            
            // Mark conversation as active
            $(".conversation-item").removeClass("active");
            $(`.conversation-item[data-id="${conversationId}"]`).addClass("active");
            
            // Show chat area on mobile
            $(".chat-area").addClass("active");
            $(".conversations-list").addClass("hidden-mobile");
            
            // Load conversation messages
            $.ajax({
                url: `/api/conversations/${conversationId}/messages`,
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Update conversation header
                    $("#currentChatName").text(response.conversation.name);
                    $("#currentChatAvatar").attr("src", response.conversation.avatar || '/api/placeholder/40/40');
                    $("#currentChatStatus").html(response.conversation.is_online ? '<span class="online-indicator"></span>Online' : 'Offline');
                    
                    // Render messages
                    renderMessages(response.messages);
                    
                    // Mark messages as read
                    markMessagesAsRead(conversationId);
                    
                    // Enable message input
                    $("#messageInput").prop("disabled", false);
                    $("#messageInput").focus();
                },
                error: function(error) {
                    console.error('Error loading conversation:', error);
                }
            });
        }
        
        function renderMessages(messages) {
            const messageArea = $("#messageArea");
            messageArea.empty();
            
            if (messages.length === 0) {
                messageArea.html('<div class="no-messages">Nenhuma mensagem encontrada. Inicie a conversa!</div>');
                return;
            }
            
            let currentDate = null;
            
            messages.forEach(message => {
                // Check if we need to add a day separator
                const messageDate = new Date(message.created_at).toLocaleDateString();
                if (currentDate !== messageDate) {
                    currentDate = messageDate;
                    const separator = `<div class="day-separator"><span>${formatDate(message.created_at)}</span></div>`;
                    messageArea.append(separator);
                }
                
                // Create message element
                const messageElement = $(`
                    <div class="message ${message.sender_id === userId ? 'outgoing' : 'incoming'}" data-id="${message.id}">
                        <div class="message-content">${formatMessageContent(message.content, message.content_type)}</div>
                        <div class="message-time">${formatMessageTime(message.created_at)}</div>
                    </div>
                `);
                
                messageArea.append(messageElement);
                
                // Update last message id
                if (message.id > lastMessageId) {
                    lastMessageId = message.id;
                }
            });
            
            // Scroll to bottom
            scrollToBottom();
        }
        
        function formatMessageContent(content, contentType) {
            switch (contentType) {
                case 'text':
                    return content;
                case 'code':
                    try {
                        const codeData = JSON.parse(content);
                        return `<pre><code class="language-${codeData.language}">${codeData.code}</code></pre>`;
                    } catch (e) {
                        return content;
                    }
                case 'file':
                    try {
                        const fileData = JSON.parse(content);
                        if (fileData.type.startsWith('image/')) {
                            return `<img src="${fileData.url}" alt="${fileData.name}" class="message-image">`;
                        } else {
                            return `<div class="file-attachment">
                                <i class="fas fa-file"></i>
                                <a href="${fileData.url}" target="_blank">${fileData.name}</a>
                                <span class="file-size">${formatFileSize(fileData.size)}</span>
                            </div>`;
                        }
                    } catch (e) {
                        return content;
                    }
                default:
                    return content;
            }
        }
        
        function sendMessage() {
            if (!currentConversationId) return;
            
            const content = $("#messageInput").val().trim();
            if (!content) return;
            
            $.ajax({
                url: `/api/conversations/${currentConversationId}/messages`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    content: content,
                    content_type: 'text'
                },
                success: function(response) {
                    // Clear input
                    $("#messageInput").val('');
                    $("#messageInput").css('height', '38px');
                    $("#sendMessageBtn").prop("disabled", true);
                    
                    // Add message to UI
                    addMessageToUI(response.message);
                    
                    // Update conversation preview in list
                    updateConversationPreview(currentConversationId, content);
                },
                error: function(error) {
                    console.error('Error sending message:', error);
                }
            });
        }
        
        function addMessageToUI(message) {
            const messageArea = $("#messageArea");
            
            // Check if we need to add a day separator
            const lastSeparator = messageArea.find('.day-separator:last').text();
            const messageDate = formatDate(message.created_at);
            
            if (!lastSeparator || !lastSeparator.includes(messageDate)) {
                const separator = `<div class="day-separator"><span>${messageDate}</span></div>`;
                messageArea.append(separator);
            }
            
            // Create message element
            const messageElement = $(`
                <div class="message ${message.sender_id === userId ? 'outgoing' : 'incoming'}" data-id="${message.id}">
                    <div class="message-content">${formatMessageContent(message.content, message.content_type)}</div>
                    <div class="message-time">${formatMessageTime(message.created_at)}</div>
                </div>
            `);
            
            messageArea.append(messageElement);
            
            // Scroll to bottom
            scrollToBottom();
            
            // Update last message id
            if (message.id > lastMessageId) {
                lastMessageId = message.id;
            }
        }
        
        function scrollToBottom() {
            const messageArea = document.getElementById('messageArea');
            messageArea.scrollTop = messageArea.scrollHeight;
        }
        
        function markMessagesAsRead(conversationId) {
            $.ajax({
                url: `/api/conversations/${conversationId}/read`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    // Remove unread badge
                    $(`.conversation-item[data-id="${conversationId}"] .unread-badge`).remove();
                },
                error: function(error) {
                    console.error('Error marking messages as read:', error);
                }
            });
        }
        
        function updateConversationPreview(conversationId, lastMessage) {
            const conversationItem = $(`.conversation-item[data-id="${conversationId}"]`);
            const previewElement = conversationItem.find('.conversation-preview');
            const timeElement = conversationItem.find('.conversation-time');
            
            previewElement.text(lastMessage);
            timeElement.text('Agora');
            
            // Move the conversation to the top
            conversationItem.prependTo("#conversationsList");
        }
        
        function openNewMessageModal() {
            loadContacts();
            $("#newMessageModal").addClass("active");
        }
        
        function closeNewMessageModal() {
            $("#newMessageModal").removeClass("active");
        }
        
        function loadContacts() {
            $.ajax({
                url: '/api/contacts',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    renderContacts(response.contacts);
                },
                error: function(error) {
                    console.error('Error loading contacts:', error);
                }
            });
        }
        
        function renderContacts(contacts) {
            const contactList = $("#contactList");
            contactList.empty();
            
            if (contacts.length === 0) {
                contactList.html('<div class="no-contacts">Nenhum contato encontrado</div>');
                return;
            }
            
            contacts.forEach(contact => {
                const contactItem = $(`
                    <div class="contact-item" data-id="${contact.id}">
                        <img src="${contact.avatar || '/api/placeholder/40/40'}" alt="${contact.name}" class="contact-avatar">
                        <div class="contact-info">
                            <div class="contact-name">${contact.name}</div>
                            <div class="contact-title">${contact.title || ''}</div>
                        </div>
                    </div>
                `);
                
                contactItem.on('click', function() {
                    startConversation(contact.id);
                });
                
                contactList.append(contactItem);
            });
        }
        
        function startConversation(contactId) {
            $.ajax({
                url: '/api/conversations',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    contact_id: contactId
                },
                success: function(response) {
                    closeNewMessageModal();
                    
                    // Add conversation to list if new
                    if (response.is_new) {
                        const conversation = response.conversation;
                        const conversationItem = $(`
                            <div class="conversation-item" data-id="${conversation.id}">
                                <img src="${conversation.avatar || '/api/placeholder/50/50'}" alt="${conversation.name}" class="conversation-avatar">
                                <div class="conversation-info">
                                    <div class="conversation-header">
                                        <div class="conversation-name">${conversation.name}</div>
                                        <div class="conversation-time">Agora</div>
                                    </div>
                                    <div class="conversation-preview">Inicie uma conversa</div>
                                </div>
                            </div>
                        `);
                        
                        conversationItem.on('click', function() {
                            loadConversation(conversation.id);
                        });
                        
                        conversationItem.prependTo("#conversationsList");
                    }
                    
                    // Load the conversation
                    loadConversation(response.conversation.id);
                },
                error: function(error) {
                    console.error('Error starting conversation:', error);
                }
            });
        }
        
        function filterConversations(searchTerm) {
            $(".conversation-item").each(function() {
                const name = $(this).find('.conversation-name').text().toLowerCase();
                const preview = $(this).find('.conversation-preview').text().toLowerCase();
                
                if (name.includes(searchTerm) || preview.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
        
        function filterContacts(searchTerm) {
            $(".contact-item").each(function() {
                const name = $(this).find('.contact-name').text().toLowerCase();
                const title = $(this).find('.contact-title').text().toLowerCase();
                
                if (name.includes(searchTerm) || title.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
        
        function openCodeSnippetModal() {
            $("#codeSnippetModal").addClass("active");
        }
        
        function closeCodeSnippetModal() {
            $("#codeSnippetModal").removeClass("active");
        }
        
        function insertCodeSnippet() {
            const language = $("#codeLanguage").val();
            const code = $("#codeContent").val().trim();
            
            if (!code) return;
            
            // Prepare code message
            const codeData = {
                language: language,
                code: code
            };
            
            // Send message
            $.ajax({
                url: `/api/conversations/${currentConversationId}/messages`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    content: JSON.stringify(codeData),
                    content_type: 'code'
                },
                success: function(response) {
                    closeCodeSnippetModal();
                    $("#codeContent").val('');
                    
                    // Add message to UI
                    addMessageToUI(response.message);
                    
                    // Update conversation preview in list
                    updateConversationPreview(currentConversationId, 'Código compartilhado');
                },
                error: function(error) {
                    console.error('Error sending code snippet:', error);
                }
            });
        }
        
        function toggleEmojiPicker() {
            $("#emojiPicker").toggleClass("active");
        }
        
        function setupPusher() {
            // Initialize Pusher
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true
            });
            
            // Subscribe to private channel
            const channel = pusher.subscribe(`private-user.${userId}`);
            
            // Listen for new messages
            channel.bind('new-message', function(data) {
                // If the message is for the current conversation
                if (data.conversation_id === currentConversationId) {
                    // Add message to UI
                    addMessageToUI(data.message);
                    
                    // Mark as read
                    markMessagesAsRead(currentConversationId);
                } else {
                    // Update conversation preview and add unread badge
                    const conversationItem = $(`.conversation-item[data-id="${data.conversation_id}"]`);
                    const previewElement = conversationItem.find('.conversation-preview');
                    const timeElement = conversationItem.find('.conversation-time');
                    const unreadBadge = conversationItem.find('.unread-badge');
                    
                    previewElement.text(data.message.content);
                    timeElement.text('Agora');
                    
                    if (unreadBadge.length > 0) {
                        const count = parseInt(unreadBadge.text()) + 1;
                        unreadBadge.text(count);
                    } else {
                        conversationItem.append('<span class="unread-badge">1</span>');
                    }
                    
                    // Move the conversation to the top
                    conversationItem.prependTo("#conversationsList");
                }
            });
            
            // Listen for user online status updates
            channel.bind('user-status', function(data) {
                // Update user status if it's the current conversation
                if (currentConversationId && data.conversation_id === currentConversationId) {
                    if (data.is_online) {
                        $("#currentChatStatus").html('<span class="online-indicator"></span>Online');
                    } else {
                        $("#currentChatStatus").text('Offline');
                    }
                }
            });
        }
        
        function showConversationsList() {
            $(".chat-area").removeClass("active");
            $(".conversations-list").removeClass("hidden-mobile");
        }
        
        // Utility functions
        function formatTime(timestamp) {
            if (!timestamp) return '';
            
            const date = new Date(timestamp);
            const now = new Date();
            const diff = (now - date) / 1000; // Difference in seconds
            
            if (diff < 60) return 'Agora';
            if (diff < 3600) return `${Math.floor(diff / 60)}m`;
            if (diff < 86400) return `${Math.floor(diff / 3600)}h`;
            if (diff < 604800) {
                // Check if same week
                const days = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                return days[date.getDay()];
            }
            
            // Format as date
            return `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}`;
        }
        
        function formatDate(timestamp) {
            if (!timestamp) return '';
            
            const date = new Date(timestamp);
            const now = new Date();
            
            // Check if today
            if (date.toDateString() === now.toDateString()) {
                return 'Hoje';
            }
            
            // Check if yesterday
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            if (date.toDateString() === yesterday.toDateString()) {
                return 'Ontem';
            }
            
            // Format as date
            const months = [
                'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
            ];
            
            return `${date.getDate()} de ${months[date.getMonth()]} de ${date.getFullYear()}`;
        }
        
        function formatMessageTime(timestamp) {
            if (!timestamp) return '';
            
            const date = new Date(timestamp);
            return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
        }
        
        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            else if (bytes < 1073741824) return (bytes / 1048576).toFixed(1) + ' MB';
            else return (bytes / 1073741824).toFixed(1) + ' GB';
        }
    });
</script>
</body>
</html>