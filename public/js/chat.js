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
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    messageInput.value = '';
                    const messageElement = document.createElement('div');
                    messageElement.classList.add('message', 'outgoing');
                    messageElement.dataset.id = data.message.id;

                    const messageContent = document.createElement('div');
                    messageContent.classList.add('message-content');
                    messageContent.textContent = data.message.content;

                    const messageTime = document.createElement('div');
                    messageTime.classList.add('message-time');
                    const time = new Date(data.message.created_at);
                    messageTime.textContent = time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    messageElement.appendChild(messageContent);
                    messageElement.appendChild(messageTime);
                    messageArea.appendChild(messageElement);

                    messageArea.scrollTop = messageArea.scrollHeight;
                }
            })
            .catch(error => console.error('Error:', error));
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
                if (window.innerWidth <= 768) {
                    document.querySelector('.conversations-list').classList.remove('active');
                    document.querySelector('.chat-area').classList.add('active');
                }
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
}); 