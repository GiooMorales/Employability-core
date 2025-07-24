document.addEventListener('DOMContentLoaded', function () {
    const sendMessageForm = document.getElementById('sendMessageForm');
    const messageInput = document.getElementById('messageInput');
    const messageArea = document.getElementById('messageArea');
    const conversationsList = document.getElementById('conversationsList');
    const mobileBack = document.getElementById('mobileBack');
    const newMessageBtn = document.getElementById('newMessageBtn');
    const newMessageModal = document.getElementById('newMessageModal');
    const closeNewMessageModal = document.getElementById('closeNewMessageModal');
    const contactList = document.getElementById('contactList');
    const contactSearch = document.getElementById('contactSearch');

    if (messageArea) {
        messageArea.scrollTop = messageArea.scrollHeight;
    }

    if (sendMessageForm) {
        sendMessageForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const url = this.action;

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        if (data.error) {
                            alert(data.error);
                        } else if (data.errors) {
                            let msg = Object.values(data.errors).flat().join('\n');
                            alert(msg);
                        } else {
                            alert('Erro ao enviar mensagem.');
                        }
                        throw new Error('Erro na requisição');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.message) {
                    messageInput.value = '';
                    // Atualiza a lista de mensagens imediatamente
                    if (typeof fetchMessages === 'function') {
                        fetchMessages();
                    }
                    // Código antigo de adicionar mensagem manualmente pode ser removido se fetchMessages já renderiza tudo
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
    
    if (mobileBack) {
        mobileBack.addEventListener('click', function() {
            document.querySelector('.chat-area').classList.remove('active');
            document.querySelector('.conversations-list').classList.add('active');
        });
    }

    if (conversationsList) {
        const conversationItems = conversationsList.querySelectorAll('.conversation-item');
        conversationItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const conversationId = this.dataset.id;
                // AJAX para buscar dados da conversa
                fetch(`/conversations/${conversationId}`)
                    .then(response => response.text())
                    .then(html => {
                        // Extrai apenas o conteúdo da área do chat
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newChatArea = doc.querySelector('.chat-area');
                        if (newChatArea) {
                            document.querySelector('.chat-area').replaceWith(newChatArea);
                        }
                        // Atualiza o campo hidden do id da conversa
                        const newConversationIdInput = document.getElementById('currentConversationId');
                        if (newConversationIdInput) {
                            window.LaravelUserId = newConversationIdInput.dataset.userid || document.querySelector('meta[name="user-id"]')?.content || null;
                        }
                        // Atualiza polling
                        if (pollingInterval) clearInterval(pollingInterval);
                        fetchMessages();
                        pollingInterval = setInterval(fetchMessages, 3000);
                        // Atualiza evento do formulário de envio
                        const newSendMessageForm = document.getElementById('sendMessageForm');
                        if (newSendMessageForm) {
                            newSendMessageForm.addEventListener('submit', function (e) {
                                e.preventDefault();
                                const formData = new FormData(this);
                                const url = this.action;
                                fetch(url, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json',
                                    }
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.json().then(data => {
                                            if (data.error) {
                                                alert(data.error);
                                            } else if (data.errors) {
                                                let msg = Object.values(data.errors).flat().join('\n');
                                                alert(msg);
                                            } else {
                                                alert('Erro ao enviar mensagem.');
                                            }
                                            throw new Error('Erro na requisição');
                                        });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.message) {
                                        document.getElementById('messageInput').value = '';
                                        fetchMessages();
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });
                            });
                        }
                        // Mobile: alterna visibilidade
                        if (window.innerWidth <= 768) {
                            document.querySelector('.conversations-list').classList.remove('active');
                            document.querySelector('.chat-area').classList.add('active');
                        }
                    });
            });
        });
    }

    if (newMessageBtn) {
        newMessageBtn.addEventListener('click', function() {
            newMessageModal.classList.add('active');
            loadContacts();
        });
    }

    if (closeNewMessageModal) {
        closeNewMessageModal.addEventListener('click', function() {
            newMessageModal.classList.remove('active');
        });
    }

    function loadContacts() {
        fetch('/contatos')
            .then(response => response.json())
            .then(data => {
                renderContacts(data.contacts);
            })
            .catch(error => console.error('Error:', error));
    }

    function renderContacts(contacts) {
        contactList.innerHTML = '';
        if (contacts.length === 0) {
            contactList.innerHTML = '<div class="no-contacts">Nenhum contato encontrado</div>';
            return;
        }

        contacts.forEach(contact => {
            const contactItem = document.createElement('div');
            contactItem.classList.add('contact-item');
            contactItem.dataset.id = contact.id;
            contactItem.innerHTML = `
                <img src="${contact.avatar || 'https://placehold.co/40x40'}" alt="${contact.name}" class="contact-avatar">
                <div class="contact-info">
                    <div class="contact-name">${contact.name}</div>
                    <div class="contact-title">${contact.title || ''}</div>
                </div>
            `;
            contactItem.addEventListener('click', function() {
                startConversation(contact.id);
            });
            contactList.appendChild(contactItem);
        });
    }

    function startConversation(contactId) {
        fetch('/conversations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ contact_id: contactId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.conversation) {
                window.location.href = `/conversations/${data.conversation.id}`;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    if (contactSearch) {
        contactSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const contacts = contactList.querySelectorAll('.contact-item');
            contacts.forEach(contact => {
                const name = contact.querySelector('.contact-name').textContent.toLowerCase();
                const title = contact.querySelector('.contact-title').textContent.toLowerCase();
                if (name.includes(searchTerm) || title.includes(searchTerm)) {
                    contact.style.display = '';
                } else {
                    contact.style.display = 'none';
                }
            });
        });
    }

    // --- INÍCIO: Polling AJAX para buscar mensagens periodicamente ---
    const conversationIdInput = document.getElementById('currentConversationId');
    let pollingInterval = null;
    let lastMessageIds = [];

    function renderMessages(messages) {
        if (!messageArea) return;
        messageArea.innerHTML = '';
        messages.forEach(msg => {
            const messageElement = document.createElement('div');
            messageElement.classList.add('message');
            messageElement.classList.add(msg.sender_id == window.LaravelUserId ? 'outgoing' : 'incoming');
            messageElement.dataset.id = msg.id;

            if (msg.content_type === 'image' && msg.image_path) {
                const imgDiv = document.createElement('div');
                imgDiv.classList.add('message-image');
                const img = document.createElement('img');
                img.src = msg.image_path;
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';
                img.style.borderRadius = '8px';
                img.style.marginBottom = '5px';
                img.classList.add('zoomable-image');
                imgDiv.appendChild(img);
                messageElement.appendChild(imgDiv);
            } else if (msg.content_type === 'file' && msg.image_path) {
                const fileDiv = document.createElement('div');
                fileDiv.classList.add('message-file');
                fileDiv.style.display = 'flex';
                fileDiv.style.alignItems = 'center';
                fileDiv.style.gap = '6px';
                fileDiv.innerHTML = `<span style="font-size:20px; color:#d9534f;"><i class="fas fa-file-alt"></i></span>`;
                const a = document.createElement('a');
                a.href = msg.image_path;
                a.target = '_blank';
                a.style.color = '#007bff';
                a.style.textDecoration = 'underline';
                a.textContent = msg.file_name || 'Arquivo enviado';
                fileDiv.appendChild(a);
                messageElement.appendChild(fileDiv);
            }
            const contentDiv = document.createElement('div');
            contentDiv.classList.add('message-content');
            contentDiv.textContent = msg.content;
            messageElement.appendChild(contentDiv);
            const timeDiv = document.createElement('div');
            timeDiv.classList.add('message-time');
            timeDiv.textContent = msg.created_at;
            messageElement.appendChild(timeDiv);
            messageArea.appendChild(messageElement);
        });
        messageArea.scrollTop = messageArea.scrollHeight;
    }

    function fetchMessages() {
        if (!conversationIdInput) return;
        const conversationId = conversationIdInput.value;
        fetch(`/conversations/${conversationId}/messages`)
            .then(response => response.json())
            .then(data => {
                if (data.messages) {
                    // Só atualiza se mudou
                    const ids = data.messages.map(m => m.id);
                    if (JSON.stringify(ids) !== JSON.stringify(lastMessageIds)) {
                        renderMessages(data.messages);
                        lastMessageIds = ids;
                    }
                }
            })
            .catch(error => console.error('Erro ao buscar mensagens:', error));
    }

    if (conversationIdInput) {
        // Pega o id do usuário logado para diferenciar outgoing/incoming
        window.LaravelUserId = conversationIdInput.dataset.userid || document.querySelector('meta[name="user-id"]')?.content || null;
        fetchMessages();
        pollingInterval = setInterval(fetchMessages, 3000);
    }
    // --- FIM: Polling AJAX ---

    // --- INÍCIO: WebSocket para mensagens em tempo real ---
    function subscribeToConversation(conversationId) {
        if (window.Echo && conversationId) {
            if (window.currentEchoChannel) {
                window.currentEchoChannel.stopListening('.MessageSent');
            }
            window.currentEchoChannel = window.Echo.private('conversation.' + conversationId)
                .listen('.MessageSent', (e) => {
                    // Adiciona a mensagem recebida em tempo real
                    if (e && e.id) {
                        fetchMessages(); // Ou renderiza só a nova mensagem
                    }
                });
        }
    }
    if (conversationIdInput) {
        subscribeToConversation(conversationIdInput.value);
    }
    // Atualizar assinatura ao trocar de conversa
    if (conversationsList) {
        const conversationItems = conversationsList.querySelectorAll('.conversation-item');
        conversationItems.forEach(item => {
            item.addEventListener('click', function(e) {
                setTimeout(() => {
                    const newConversationIdInput = document.getElementById('currentConversationId');
                    if (newConversationIdInput) {
                        subscribeToConversation(newConversationIdInput.value);
                    }
                }, 500);
            });
        });
    }
    // --- FIM: WebSocket ---

    // Função global robusta para alternar o menu de opções
    window.toggleMessageMenu = function(messageId) {
        // Fecha todos os outros menus
        document.querySelectorAll('.message-menu').forEach(menu => menu.style.display = 'none');
        const menu = document.getElementById('message-menu-' + messageId);
        if (menu) {
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }
    };
    // Fecha o menu se clicar fora
    if (!window._messageMenuClickListener) {
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.message-menu-hover')) {
                document.querySelectorAll('.message-menu').forEach(menu => menu.style.display = 'none');
            }
        });
        window._messageMenuClickListener = true;
    }

    let editingMessageId = null;
    let deletingMessageId = null;

    function openEditMessageModal(messageId, currentContent) {
        editingMessageId = messageId;
        document.getElementById('editMessageInput').value = currentContent;
        document.getElementById('editMessageModal').style.display = 'flex';
    }
    function closeEditMessageModal() {
        editingMessageId = null;
        document.getElementById('editMessageModal').style.display = 'none';
    }
    function openDeleteMessageModal(messageId) {
        deletingMessageId = messageId;
        document.getElementById('deleteMessageModal').style.display = 'flex';
    }
    function closeDeleteMessageModal() {
        deletingMessageId = null;
        document.getElementById('deleteMessageModal').style.display = 'none';
    }

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-message-btn')) {
            const messageId = e.target.dataset.id;
            const messageDiv = document.querySelector('.message[data-id="' + messageId + '"] .message-content');
            if (messageDiv) {
                const currentContent = messageDiv.textContent.replace('(editado)', '').trim();
                openEditMessageModal(messageId, currentContent);
            }
        }
        if (e.target.classList.contains('delete-message-btn')) {
            const messageId = e.target.dataset.id;
            openDeleteMessageModal(messageId);
        }
    });

    document.getElementById('closeEditMessageModal').onclick = closeEditMessageModal;
    document.getElementById('cancelEditMessageBtn').onclick = closeEditMessageModal;
    document.getElementById('closeDeleteMessageModal').onclick = closeDeleteMessageModal;
    document.getElementById('cancelDeleteMessageBtn').onclick = closeDeleteMessageModal;

    document.getElementById('saveEditMessageBtn').onclick = function() {
        if (!editingMessageId) return;
        const newContent = document.getElementById('editMessageInput').value.trim();
        if (!newContent) {
            alert('A mensagem não pode ser vazia.');
            return;
        }
        fetch(`/messages/${editingMessageId}/edit`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ content: newContent })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeEditMessageModal();
                if (typeof fetchMessages === 'function') fetchMessages();
            } else if (data.error) {
                alert(data.error);
            }
        })
        .catch(() => alert('Erro ao editar mensagem.'));
    };

    document.getElementById('confirmDeleteMessageBtn').onclick = function() {
        if (!deletingMessageId) return;
        fetch(`/messages/${deletingMessageId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeDeleteMessageModal();
                if (typeof fetchMessages === 'function') fetchMessages();
            } else if (data.error) {
                alert(data.error);
            }
        })
        .catch(() => alert('Erro ao excluir mensagem.'));
    };

    function applyMessageMenuHover() {
        document.querySelectorAll('.message.outgoing').forEach(function(msg) {
            msg.addEventListener('mouseenter', function() {
                const menu = msg.querySelector('.message-menu-hover');
                if (menu) menu.style.display = 'block';
            });
            msg.addEventListener('mouseleave', function() {
                const menu = msg.querySelector('.message-menu-hover');
                if (menu) menu.style.display = 'none';
                // Fecha o menu de opções se aberto
                const dropdown = msg.querySelector('.message-menu');
                if (dropdown) dropdown.style.display = 'none';
            });
        });
    }
}); 