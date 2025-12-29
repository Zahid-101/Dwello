document.addEventListener('DOMContentLoaded', function () {
    const chatForm = document.getElementById('chat-form');
    // Exit if not on a chat page
    if (!chatForm) return;

    const messagesContainer = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendButton = chatForm.querySelector('button[type="submit"]');
    const conversationId = chatForm.dataset.conversationId;

    // Get Auth ID from meta tag
    const authMeta = document.querySelector('meta[name="user-id"]');
    const authUserId = authMeta ? parseInt(authMeta.content) : null;
    let isPolling = false;

    // Helper: Get CSRF token
    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]').content;

    // Helper: Scroll to bottom
    const scrollToBottom = () => {
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    };

    // Initial scroll
    scrollToBottom();

    // Helper: Append message to UI
    const appendMessage = (message) => {
        // Check for duplicates
        if (document.querySelector(`.message-wrapper[data-id="${message.id}"]`)) return;

        const isMine = message.sender_id === authUserId;

        const wrapper = document.createElement('div');
        wrapper.className = `message-wrapper ${isMine ? 'items-end' : 'items-start'}`;
        wrapper.dataset.id = message.id;

        const bubble = document.createElement('div');
        // Add opacity transition for "appearing" effect
        bubble.style.opacity = '0';
        bubble.style.transition = 'opacity 0.3s ease';

        bubble.className = `message-bubble ${isMine ? 'mine' : 'theirs'}`;
        bubble.textContent = message.body;

        const time = document.createElement('div');
        time.className = 'message-time';

        // Format time
        const date = new Date(message.created_at);
        time.textContent = date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });

        wrapper.appendChild(bubble);
        wrapper.appendChild(time);
        messagesContainer.appendChild(wrapper);

        // Trigger reflow/animation
        requestAnimationFrame(() => {
            bubble.style.opacity = '1';
        });

        scrollToBottom();
    };

    // Handle Submit
    chatForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const body = messageInput.value.trim();
        if (!body) return;

        // UI Optimistic Updates / Loading State
        messageInput.value = '';
        messageInput.style.height = 'auto'; // Reset auto-resize
        messageInput.focus();

        // Disable button optionally or show spinner
        if (sendButton) {
            const originalText = sendButton.innerHTML;
            sendButton.disabled = true;
            sendButton.innerHTML = '<span class="animate-spin">↻</span>'; // Simple spinner

            try {
                const response = await fetch(`/messages/${conversationId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ body: body })
                });

                if (!response.ok) throw new Error('Send failed');

                const message = await response.json();

                // Ensure ID types match for comparison
                message.sender_id = parseInt(message.sender_id);

                appendMessage(message);
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Failed to send message.');
                messageInput.value = body; // Restore input
            } finally {
                sendButton.disabled = false;
                sendButton.innerHTML = originalText;
            }
        }
    });

    // Support Enter to send
    messageInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    // Polling Logic
    const pollMessages = async () => {
        // Optimization: Don't poll if tab is hidden
        if (document.hidden) return;

        if (isPolling) return;
        isPolling = true;

        try {
            const lastMsg = messagesContainer.querySelector('.message-wrapper:last-child');
            const lastId = lastMsg ? lastMsg.dataset.id : 0;

            const response = await fetch(`/messages/${conversationId}/poll?after=${lastId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });

            if (response.ok) {
                const messages = await response.json();
                if (Array.isArray(messages) && messages.length > 0) {
                    messages.forEach(msg => {
                        msg.sender_id = parseInt(msg.sender_id);
                        appendMessage(msg);
                    });
                }
            }
        } catch (error) {
            console.error('Polling error:', error);
            // Exponential backoff logic could determine next poll interval, but fixed is fine for now
        } finally {
            isPolling = false;
        }
    };

    // Start Polling
    const pollInterval = setInterval(pollMessages, 5000); // 5 seconds

    // Cleanup on page unload (optional in MPA but good practice)
    window.addEventListener('beforeunload', () => {
        clearInterval(pollInterval);
    });
});
