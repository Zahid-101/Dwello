document.addEventListener('DOMContentLoaded', function () {
    const chatForm = document.getElementById('chat-form');
    const messagesContainer = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');

    if (!chatForm || !messagesContainer) return;

    const conversationId = chatForm.dataset.conversationId;
    let isPolling = false;

    // Helper: Get CSRF token
    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Helper: Scroll to bottom
    const scrollToBottom = () => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    // Helper: Get last message ID
    const getLastMessageId = () => {
        const lastMessage = messagesContainer.lastElementChild;
        return lastMessage ? lastMessage.dataset.id : 0;
    };

    // Helper: Append message to UI
    const appendMessage = (message, isMine) => {
        const wrapper = document.createElement('div');
        wrapper.className = `message-wrapper ${isMine ? 'items-end' : 'items-start'}`;
        wrapper.dataset.id = message.id;

        const bubble = document.createElement('div');
        bubble.className = `message-bubble ${isMine ? 'mine' : 'theirs'}`;
        bubble.textContent = message.body;

        const time = document.createElement('div');
        time.className = 'message-time';
        // Simple time formatting
        const date = new Date(message.created_at);
        time.textContent = date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });

        wrapper.appendChild(bubble);
        wrapper.appendChild(time);
        messagesContainer.appendChild(wrapper);
    };

    // Send Message
    chatForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const body = messageInput.value.trim();
        if (!body) return;

        // Optimistic UI update could happen here, but for simplicity/accuracy wait for server
        // Actually, let's clear input immediately for better feel
        messageInput.value = '';
        messageInput.style.height = 'auto'; // Reset height

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
            appendMessage(message, true); // It's mine
            scrollToBottom();
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Failed to send message. Please try again.');
            // Restore input if failed (optional but good UX)
            messageInput.value = body;
        }
    });

    // Poll for new messages
    const pollMessages = async () => {
        if (isPolling) return;
        isPolling = true;

        try {
            const lastId = getLastMessageId();
            const response = await fetch(`/messages/${conversationId}/poll?after=${lastId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken() // Good practice though usually not needed for GET
                }
            });

            if (response.ok) {
                const messages = await response.json();
                if (messages.length > 0) {
                    messages.forEach(msg => {
                        // Check if we already have this message (edge case with optimistic UI or race)
                        // But since we strictly filter > lastId, it should be fine.
                        // However, we must ensure we don't re-append our own message if we just sent it
                        // The 'after' param handles this logic mostly, but if we just sent ID 100, and poll asks > 99, 
                        // we might get 100 back. 
                        // The UI should already have 100 from the POST response.
                        // Let's double check if it exists in DOM.

                        const existing = messagesContainer.querySelector(`[data-id="${msg.id}"]`);
                        if (!existing) {
                            const isMine = msg.sender_id == document.querySelector('meta[name="user-id"]')?.content;
                            // We didn't set user-id meta. Let's infer isMine:
                            // Actually, simpler: PHP can pass auth ID, or we assume incoming polling messages 
                            // that are NOT mine (since mine are added via POST response). 
                            // BUT, if I sent a message from another tab, I want to see it here too!
                            // So we need to know "my" user ID.

                            // Let's assume the view sets a global variable or meta tag.
                            // I'll grab it from a new meta tag I'll add to app layout or just inject here.
                            // For now, let's check class usage in appendMessage logic.

                            // Strategy: We need to know who "I" am to style it. 
                            // Since I didn't add the meta tag yet, I'll update the JS to check a data attribute 
                            // on the body or form? 
                            // The form doesn't have it.

                            // Let's fallback: In `poll`, we return `sender_id`. 
                            // We can compare it to... wait, we don't have auth ID in JS.
                            // I will add a meta tag for user-id in the main layout in a separate step or 
                            // allow the show view to pass it in a script tag.

                            // Let's assume `window.authUserId` is set. I'll add it to the view.
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Polling error:', error);
        } finally {
            isPolling = false;
        }
    };

    // Polling Interval
    setInterval(pollMessages, 7000);
});

// We need to implement the message processing loop properly to handle "my" vs "their" messages
// Re-implementing the poll loop slightly to be more robust:

document.addEventListener('DOMContentLoaded', function () {
    // re-defining to ensure scope is clean
    const chatForm = document.getElementById('chat-form');
    if (!chatForm) return;

    const messagesContainer = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const conversationId = chatForm.dataset.conversationId;

    // We need auth ID. Let's try to get it from value of a hidden input if exists, or global.
    // I'll inject it into the view in a script tag in the next step. 
    // For now assuming `window.authUserId` exists.

    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]').content;

    const scrollToBottom = () => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    // Support Enter to send (without Shift)
    messageInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    const appendMessage = (message) => {
        // Check duplication
        if (document.querySelector(`.message-wrapper[data-id="${message.id}"]`)) return;

        const isMine = message.sender_id === window.authUserId;

        const wrapper = document.createElement('div');
        wrapper.className = `message-wrapper ${isMine ? 'items-end' : 'items-start'}`;
        wrapper.dataset.id = message.id;

        const bubble = document.createElement('div');
        bubble.className = `message-bubble ${isMine ? 'mine' : 'theirs'}`;
        bubble.textContent = message.body;

        const time = document.createElement('div');
        time.className = 'message-time';
        const date = new Date(message.created_at);
        time.textContent = date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });

        wrapper.appendChild(bubble);
        wrapper.appendChild(time);
        messagesContainer.appendChild(wrapper);
        scrollToBottom();
    };

    chatForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const body = messageInput.value.trim();
        if (!body) return;

        messageInput.value = '';
        messageInput.style.height = 'auto';

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

            // Fix type casting for comparison
            message.sender_id = parseInt(message.sender_id);
            appendMessage(message);
        } catch (error) {
            console.error(error);
            alert('Error sending message');
            messageInput.value = body;
        }
    });

    const poll = async () => {
        try {
            const lastMsg = messagesContainer.querySelector('.message-wrapper:last-child');
            const lastId = lastMsg ? lastMsg.dataset.id : 0;

            const response = await fetch(`/messages/${conversationId}/poll?after=${lastId}`);
            if (response.ok) {
                const messages = await response.json();
                messages.forEach(msg => {
                    msg.sender_id = parseInt(msg.sender_id);
                    appendMessage(msg);
                });
            }
        } catch (e) {
            console.error('Poll error', e);
        }
    };

    setInterval(poll, 7000);
});
