@php
    $accountId = session('account_id');
    $accountRole = session('account_role', 'user');
@endphp

<style>
    .chat-dropdown-menu {
        min-width: 320px;
        max-width: 90vw;
        max-height: 420px;
        overflow-y: auto;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
    }

    .chat-conversation-item {
        padding: 12px 16px;
        border-bottom: 1px solid #e9ecef;
        cursor: pointer;
        transition: background-color 0.2s ease;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .chat-conversation-item:hover {
        background-color: #f8f9fa;
    }

    .chat-conversation-item:active {
        background-color: #e9ecef;
    }

    .chat-conversation-item:last-child {
        border-bottom: none;
    }

    .chat-conversation-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        flex-shrink: 0;
    }

    .chat-conversation-content {
        flex: 1;
        min-width: 0;
    }

    .chat-conversation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    .chat-conversation-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #212529;
    }

    .chat-conversation-time {
        font-size: 0.75rem;
        color: #999;
        white-space: nowrap;
    }

    .chat-conversation-preview {
        font-size: 0.85rem;
        color: #6c757d;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-conversation-unread {
        background: #dc3545;
        color: white;
        border-radius: 10px;
        padding: 2px 8px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-left: 8px;
    }

    .chat-panel {
        position: fixed;
        right: 20px;
        bottom: 20px;
        width: 380px;
        max-width: calc(100vw - 40px);
        height: 500px;
        max-height: calc(100vh - 100px);
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        display: none;
        flex-direction: column;
        z-index: 99999;
        overflow: hidden;
    }

    .chat-panel.show {
        display: flex;
    }

    .chat-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        min-height: 40px;
        flex-shrink: 0;
    }

    .chat-header h6 {
        margin: 0;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
        flex: 1;
        min-width: 0;
        max-width: calc(100% - 40px);
    }

    .chat-header h6 i {
        flex-shrink: 0;
        font-size: 0.85rem;
    }

    .chat-header button {
        flex-shrink: 0;
        margin-left: 8px;
        padding: 0;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .chat-header button:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .chat-header button i {
        font-size: 0.9rem;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .chat-message {
        display: flex;
        flex-direction: column;
        max-width: 75%;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chat-message.sent {
        align-self: flex-end;
    }

    .chat-message.received {
        align-self: flex-start;
    }

    .message-bubble {
        padding: 10px 14px;
        border-radius: 12px;
        word-wrap: break-word;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        line-height: 1.4;
    }

    .chat-message.sent .message-bubble {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .chat-message.received .message-bubble {
        background: white;
        color: #333;
        border-bottom-left-radius: 4px;
        border: 1px solid #e0e0e0;
    }

    .message-time {
        font-size: 0.7rem;
        color: #999;
        margin-top: 4px;
        align-self: flex-end;
    }

    .chat-message.received .message-time {
        align-self: flex-start;
    }

    .chat-input-container {
        padding: 12px;
        background: white;
        border-top: 1px solid #e0e0e0;
        display: flex;
        gap: 8px;
    }

    .chat-input-container input {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 10px 16px;
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.3s;
    }

    .chat-input-container input:focus {
        border-color: #667eea;
    }

    .chat-send-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .chat-send-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .chat-send-btn:active {
        transform: scale(0.95);
    }

    .chat-empty {
        text-align: center;
        color: #999;
        padding: 40px 20px;
        font-size: 0.9rem;
    }

    .message-delete-btn {
        opacity: 0;
        transition: opacity 0.2s ease;
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 4px;
        color: #dc3545;
        background: transparent;
        border: none;
        font-size: 0.75rem;
        margin-top: 2px;
    }

    .chat-message:hover .message-delete-btn {
        opacity: 1;
    }

    .message-delete-btn:hover {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .chat-header-delete-btn {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        cursor: pointer;
        transition: background-color 0.2s;
        white-space: nowrap;
    }

    .chat-header-delete-btn:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Chat Badge - Hidden by default */
    #chat-badge:empty,
    #chat-badge[data-count="0"] {
        display: none !important;
    }

    /* Mobile Responsive */
    @media (max-width: 576px) {
        .chat-panel {
            right: 10px;
            bottom: 10px;
            width: calc(100vw - 20px);
            height: calc(100vh - 80px);
            max-height: calc(100vh - 80px);
        }

        .chat-header {
            padding: 6px 10px;
            min-height: 36px;
        }

        .chat-header h6 {
            font-size: 0.85rem;
            gap: 4px;
            max-width: calc(100% - 32px);
        }

        .chat-header h6 i {
            font-size: 0.8rem;
        }

        .chat-header button {
            width: 24px;
            height: 24px;
            margin-left: 6px;
        }

        .chat-header button i {
            font-size: 0.8rem;
        }

        .chat-dropdown-menu {
            width: 90vw !important;
            max-width: 90vw !important;
        }

        .chat-conversation-item {
            padding: 10px 12px;
        }

        .chat-conversation-avatar {
            width: 36px;
            height: 36px;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 400px) {
        .chat-dropdown-menu {
            width: 95vw !important;
            max-width: 95vw !important;
        }

        .chat-header {
            padding: 5px 8px;
            min-height: 32px;
        }

        .chat-header h6 {
            font-size: 0.8rem;
            max-width: calc(100% - 28px);
        }

        .chat-header button {
            width: 22px;
            height: 22px;
            margin-left: 4px;
        }

        .chat-header button i {
            font-size: 0.75rem;
        }
    }
</style>

<div class="dropdown chat-icon-container" style="position:relative;">
    <button class="btn btn-light position-relative" id="chatIconBtn" data-bs-toggle="dropdown" aria-expanded="false"
        title="Messages" style="height: 40px; min-width: 48px; padding: 6px 16px;">
        <i class="bi bi-chat-dots" style="font-size:1.1rem"></i>
        <span id="chat-badge" class="badge bg-danger position-absolute"
            style="top:-4px;right:-8px;display:none;min-width:20px;height:20px;border-radius:10px;font-size:0.75rem;line-height:20px;padding:0 5px;"></span>
    </button>

    <!-- Dropdown menu for message list -->
    <div class="dropdown-menu dropdown-menu-end chat-dropdown-menu p-0" id="chatDropdown">
        <div class="d-flex flex-column px-3 py-2 border-bottom bg-light">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong style="font-size: 0.95rem;">Messages</strong>
            </div>
            <div class="d-flex">
                @if($accountRole === 'admin')
                    <input type="search" id="chatSearchInput" class="form-control form-control-sm"
                        placeholder="Search users by name or contact..." style="min-width:0;">
                @endif
            </div>
        </div>
        <div id="chatConversationList">
            <div class="text-center text-muted py-4">
                <i class="bi bi-chat-dots" style="font-size: 2rem; opacity: 0.3;"></i>
                <p class="mt-2 mb-0" style="font-size: 0.85rem;">Loading conversations...</p>
            </div>
        </div>
    </div>
</div>

<!-- Chat Panel (Hidden by default) -->
<div class="chat-panel" id="chatPanel">
    <div class="chat-header">
        <h6><i class="bi bi-chat-heart"></i>Chat with {{ $accountRole === 'admin' ? 'User' : 'Admin' }}</h6>
        <div class="d-flex gap-2 align-items-center">
            <button class="chat-header-delete-btn" id="deleteConversationBtn" title="Delete conversation">
                <i class="bi bi-trash"></i>
            </button>
            <button class="btn btn-link text-white p-0" id="closeChatBtn">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    <div class="chat-messages" id="chatMessages">
        <div class="chat-empty">
            <i class="bi bi-chat-dots" style="font-size: 3rem; opacity: 0.3;"></i>
            <p class="mt-2">No messages yet. Start the conversation!</p>
        </div>
    </div>
    <div class="chat-input-container">
        <input type="text" id="chatInput" placeholder="Type your message..." maxlength="2000">
        <button class="chat-send-btn" id="chatSendBtn">
            <i class="bi bi-send-fill"></i>
        </button>
    </div>
</div>

<script>
    // Chat functionality
    const chatPanel = document.getElementById('chatPanel');
    const chatIconBtn = document.getElementById('chatIconBtn');
    const closeChatBtn = document.getElementById('closeChatBtn');
    const chatInput = document.getElementById('chatInput');
    const chatSendBtn = document.getElementById('chatSendBtn');
    const chatMessages = document.getElementById('chatMessages');
    const chatBadge = document.getElementById('chat-badge');
    const chatConversationList = document.getElementById('chatConversationList');

    console.log('Chat system initialized:', {
        chatIconBtn: !!chatIconBtn,
        chatConversationList: !!chatConversationList,
        accountRole: '{{ $accountRole }}'
    });

    let currentPartnerId = null;
    let currentPartnerName = null;
    let chatRefreshInterval = null;

    // Load conversation list when dropdown opens
    chatIconBtn?.addEventListener('click', function () {
        console.log('Chat icon clicked, loading conversation list...');
        // Load existing conversations by default, and clear any search
        const searchInput = document.getElementById('chatSearchInput');
        if (searchInput) searchInput.value = '';
        setTimeout(() => loadConversationList(), 150);
    });

    // Debounced search handling
    const chatSearchInput = document.getElementById('chatSearchInput');
    let chatSearchTimer = null;
    if (chatSearchInput) {
        chatSearchInput.addEventListener('input', function (e) {
            const q = this.value.trim();
            if (chatSearchTimer) clearTimeout(chatSearchTimer);
            chatSearchTimer = setTimeout(() => {
                loadConversationList(q);
            }, 300);
        });
    }

    // Open chat panel when conversation is clicked
    function openChatPanel(partnerId, partnerName) {
        console.log('Opening chat panel for:', { partnerId, partnerName });

        // SECURITY FIX: Reset state completely to prevent cross-user contamination
        currentPartnerId = partnerId;
        currentPartnerName = partnerName;

        // Clear previous messages immediately
        if (chatMessages) {
            chatMessages.innerHTML = '<div class="text-center text-muted py-3">Loading messages...</div>';
        }

        // Close dropdown
        const dropdown = bootstrap.Dropdown.getInstance(chatIconBtn);
        if (dropdown) {
            dropdown.hide();
        }

        // Update chat header with partner name
        const chatHeaderTitle = document.querySelector('.chat-header h6');
        if (chatHeaderTitle) {
            chatHeaderTitle.innerHTML = `<i class="bi bi-chat-heart"></i>Chat with ${partnerName}`;
        }

        // Open chat panel
        chatPanel?.classList.add('show');
        loadChatMessages(true); // Force update when opening chat
        startChatRefresh();
        chatInput?.focus();
    }

    closeChatBtn?.addEventListener('click', function () {
        console.log('Closing chat panel');
        chatPanel?.classList.remove('show');
        stopChatRefresh();

        // SECURITY FIX: Clear all state and messages when closing
        currentPartnerId = null;
        currentPartnerName = null;
        if (chatMessages) {
            chatMessages.innerHTML = '';
        }
    });

    // Send message on button click
    chatSendBtn?.addEventListener('click', sendChatMessage);

    // Send message on Enter key
    chatInput?.addEventListener('keypress', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendChatMessage();
        }
    });

    async function fetchChatUnreadCount() {
        try {
            const res = await fetch('{{ route('messages.unread_count') }}');
            const data = await res.json();
            if (data.count && data.count > 0) {
                chatBadge.style.display = 'inline-block';
                chatBadge.style.visibility = 'visible';
                chatBadge.textContent = data.count;
                chatBadge.setAttribute('data-count', data.count);
            } else {
                chatBadge.style.display = 'none';
                chatBadge.style.visibility = 'hidden';
                chatBadge.textContent = '';
                chatBadge.removeAttribute('data-count');
            }
        } catch (e) {
            console.error('Failed to fetch chat unread count', e);
        }
    }

    async function loadConversationList(query = null) {
        try {
            @if($accountRole === 'admin')
                // Admin: Load list of users with conversations or search results
                console.log('Loading conversation partners for admin...', { query });
                const url = new URL('{{ route('messages.partners') }}', window.location.origin);
                if (query) url.searchParams.append('q', query);
                const res = await fetch(url);
                const data = await res.json();

                console.log('Partners API Response:', {
                    success: res.ok,
                    status: res.status,
                    hasPartners: !!data.partners,
                    partnerCount: data.partners ? data.partners.length : 0,
                    data: data
                });

                if (!data.partners || data.partners.length === 0) {
                    console.warn('No conversation partners found');
                    chatConversationList.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-people" style="font-size: 2rem; opacity: 0.3;"></i><p class="mt-2 mb-0" style="font-size: 0.85rem;">No results found</p></div>';
                    return;
                }

                console.log('Found partners:', data.partners);

                const conversationsHtml = data.partners.map(partner => {
                    const initials = partner.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
                    const unreadBadge = partner.unread_count > 0 ? `<span class="chat-conversation-unread">${partner.unread_count}</span>` : '';

                    // FIX: Use data attributes instead of inline onclick to handle special characters in names
                    return `
                                        <div class="chat-conversation-item" data-partner-id="${partner.user_id}" data-partner-name="${escapeHtml(partner.name)}">
                                            <div class="chat-conversation-avatar">${initials}</div>
                                            <div class="chat-conversation-content">
                                                <div class="chat-conversation-header">
                                                    <span class="chat-conversation-name">${escapeHtml(partner.name)}${unreadBadge}</span>
                                                </div>
                                                <div class="chat-conversation-preview">Click to open chat</div>
                                            </div>
                                        </div>
                                    `;
                }).join('');

                chatConversationList.innerHTML = conversationsHtml;

                // FIX: Add event listeners to conversation items
                document.querySelectorAll('.chat-conversation-item').forEach(item => {
                    item.addEventListener('click', function () {
                        const partnerId = parseInt(this.getAttribute('data-partner-id'));
                        const partnerName = this.getAttribute('data-partner-name');
                        console.log('Conversation item clicked:', { partnerId, partnerName });
                        openChatPanel(partnerId, partnerName);
                    });
                });
            @else
                // User: Show admin as only conversation
                chatConversationList.innerHTML = `
                                    <div class="chat-conversation-item" data-partner-id="1" data-partner-name="Admin">
                                        <div class="chat-conversation-avatar">AD</div>
                                        <div class="chat-conversation-content">
                                            <div class="chat-conversation-header">
                                                <span class="chat-conversation-name">Admin</span>
                                            </div>
                                            <div class="chat-conversation-preview">Click to open chat</div>
                                        </div>
                                    </div>
                                `;

                // FIX: Add event listener to admin conversation item
                const adminConvItem = document.querySelector('.chat-conversation-item');
                if (adminConvItem) {
                    adminConvItem.addEventListener('click', function () {
                        const partnerId = parseInt(this.getAttribute('data-partner-id'));
                        const partnerName = this.getAttribute('data-partner-name');
                        console.log('Admin conversation clicked:', { partnerId, partnerName });
                        openChatPanel(partnerId, partnerName);
                    });
                }
            @endif
        } catch (e) {
            console.error('Failed to load conversations', e);
            chatConversationList.innerHTML = '<div class="text-center text-danger py-3">Failed to load conversations</div>';
        }
    }

    let lastMessageCount = 0;
    let lastMessageTimestamp = null;

    async function loadChatMessages(forceUpdate = false) {
        try {
            const url = new URL('{{ route('messages.conversation') }}', window.location.origin);
            @if($accountRole === 'admin')
                if (currentPartnerId) {
                    url.searchParams.append('user_id', currentPartnerId);
                } else {
                    chatMessages.innerHTML = '<div class="chat-empty"><i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i><p class="mt-2">Select a user to start chatting</p></div>';
                    return;
                }
            @endif

            const res = await fetch(url);
            const data = await res.json();

            console.log('API Response:', {
                url: url.toString(),
                success: data.success,
                messagesType: typeof data.messages,
                isArray: Array.isArray(data.messages),
                messageCount: data.messages ? (Array.isArray(data.messages) ? data.messages.length : Object.keys(data.messages).length) : 0,
                currentPartnerId: currentPartnerId,
                fullData: data
            });

            // FIX: Handle both array and object responses
            let messages = data.messages;
            if (messages && !Array.isArray(messages)) {
                console.warn('Messages is not an array, converting...', messages);
                messages = Object.values(messages);
            }

            if (!messages || messages.length === 0) {
                console.warn('No messages found in conversation');
                if (forceUpdate || lastMessageCount !== 0) {
                    chatMessages.innerHTML = '<div class="chat-empty"><i class="bi bi-chat-dots" style="font-size: 3rem; opacity: 0.3;"></i><p class="mt-2">No messages yet. Start the conversation!</p></div>';
                    lastMessageCount = 0;
                    lastMessageTimestamp = null;
                }
                return;
            }

            const currentUserId = data.current_user_id;
            const currentUserType = data.current_user_type;
            const currentUserTypeFull = data.current_user_type_full;

            // Check if there are new messages
            const latestTimestamp = messages.length > 0 ? messages[messages.length - 1].created_at : null;
            const hasNewMessages = forceUpdate ||
                messages.length !== lastMessageCount ||
                latestTimestamp !== lastMessageTimestamp;

            // Only update DOM if there are changes (prevents blinking)
            if (!hasNewMessages) {
                return;
            }

            console.log('Current User Info:', {
                id: currentUserId,
                type: currentUserType,
                typeFull: currentUserTypeFull,
                messageCount: messages.length
            });

            const messagesHtml = messages.map(msg => {
                // SECURITY FIX: Properly determine if message was sent by current user
                // Compare both sender_id AND sender_type to ensure correct attribution
                const isSent = (msg.sender_id == currentUserId && msg.sender_type === currentUserTypeFull);

                const messageClass = isSent ? 'sent' : 'received';
                const time = new Date(msg.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

                console.log('Message:', {
                    id: msg.id,
                    sender_id: msg.sender_id,
                    sender_type: msg.sender_type,
                    isSent: isSent,
                    text: msg.message.substring(0, 20)
                });

                return `
                    <div class="chat-message ${messageClass}">
                        <div class="message-bubble">${escapeHtml(msg.message)}</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="message-time">${time}</span>
                            <button class="message-delete-btn" onclick="deleteMessage(${msg.id})" title="Delete message">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            chatMessages.innerHTML = messagesHtml;
            scrollToBottom();

            // Update tracking variables
            lastMessageCount = messages.length;
            lastMessageTimestamp = latestTimestamp;

            // Update unread count
            fetchChatUnreadCount();
        } catch (e) {
            console.error('Failed to load chat messages', e);
            chatMessages.innerHTML = '<div class="text-center text-danger py-3">Failed to load messages</div>';
        }
    }

    async function sendChatMessage() {
        const message = chatInput?.value?.trim();
        if (!message) return;

        try {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('message', message);

            @if($accountRole === 'admin')
                if (!currentPartnerId) {
                    alert('Please select a user from the message list first');
                    return;
                }
                formData.append('receiver_id', currentPartnerId);
            @endif

            const res = await fetch('{{ route('messages.send') }}', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                chatInput.value = '';
                await loadChatMessages(true); // Force update after sending message
            } else {
                alert('Failed to send message');
            }
        } catch (e) {
            console.error('Failed to send message', e);
            alert('Failed to send message');
        }
    }

    function scrollToBottom() {
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    function startChatRefresh() {
        stopChatRefresh();
        chatRefreshInterval = setInterval(loadChatMessages, 5000); // Refresh every 5 seconds
    }

    function stopChatRefresh() {
        if (chatRefreshInterval) {
            clearInterval(chatRefreshInterval);
            chatRefreshInterval = null;
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Delete individual message
    async function deleteMessage(messageId) {
        const result = await Swal.fire({
            title: 'Delete Message?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`{{ url('/messages') }}/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    await Swal.fire({
                        title: 'Deleted!',
                        text: 'Message has been deleted.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    await loadChatMessages(true);
                    fetchChatUnreadCount();
                } else {
                    throw new Error(data.message || 'Failed to delete message');
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to delete message. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        }
    }

    // Delete entire conversation
    async function deleteConversation() {
        if (!currentPartnerId) {
            Swal.fire({
                title: 'Error',
                text: 'No conversation selected.',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        const result = await Swal.fire({
            title: 'Delete Entire Conversation?',
            text: 'This will permanently delete all messages in this conversation. This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete all',
            cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`{{ url('/messages/conversation') }}/${currentPartnerId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    await Swal.fire({
                        title: 'Deleted!',
                        text: `Conversation deleted successfully. ${data.deleted_count} message(s) removed.`,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Close chat panel and refresh
                    chatPanel?.classList.remove('show');
                    stopChatRefresh();
                    currentPartnerId = null;
                    currentPartnerName = null;
                    if (chatMessages) {
                        chatMessages.innerHTML = '';
                    }
                    fetchChatUnreadCount();
                } else {
                    throw new Error(data.message || 'Failed to delete conversation');
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to delete conversation. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        }
    }

    // Fetch unread count on page load
    document.addEventListener('DOMContentLoaded', function () {
        fetchChatUnreadCount();
        // Poll unread count every 30 seconds
        setInterval(fetchChatUnreadCount, 30000);

        // Add event listener for delete conversation button
        const deleteConvBtn = document.getElementById('deleteConversationBtn');
        if (deleteConvBtn) {
            deleteConvBtn.addEventListener('click', deleteConversation);
        }
    });
</script>
