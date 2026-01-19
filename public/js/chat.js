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

    // --- Helper: Show Toast ---
    const showToast = (message, type = 'success') => {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.cssText = `
            background: ${type === 'success' ? '#10B981' : '#EF4444'};
            color: white; padding: 12px 24px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-size: 14px; font-weight: 500; opacity: 0; transform: translateY(-20px); transition: all 0.3s ease;
        `;

        container.appendChild(toast);
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    // Helper: Append message to UI
    const appendMessage = (message, prepend = false) => {
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

        if (message.property) {
            // Rich Property Card
            bubble.className = `message-bubble ${isMine ? 'mine' : 'theirs'}`;
            bubble.style.padding = '0';
            bubble.style.overflow = 'hidden';
            bubble.style.maxWidth = '300px';
            bubble.style.background = 'white';
            bubble.style.color = 'black';
            bubble.style.border = '1px solid #e5e7eb';

            const photoHtml = message.property.photo_url
                ? `<img src="${message.property.photo_url}" alt="Property" style="width: 100%; height: 150px; object-fit: cover;">`
                : `<div style="width: 100%; height: 150px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #9ca3af;">No Image</div>`;

            bubble.innerHTML = `
                ${photoHtml}
                <div style="padding: 12px;">
                    <h4 style="font-weight: 600; font-size: 15px; margin-bottom: 4px; line-height: 1.3;">${message.property.title}</h4>
                    <div style="color: #f97316; font-weight: 700; margin-bottom: 8px;">
                        LKR ${(message.property.monthly_rent / 1000).toFixed(1)}k <span style="font-weight: 400; color: #6b7280; font-size: 12px;">/mo</span>
                    </div>
                    <a href="/properties/${message.property.id}" class="block w-full text-center bg-gray-900 text-white rounded-lg py-2 text-sm font-medium hover:bg-gray-800 transition">
                        View Property
                    </a>
                </div>
            `;
        } else {
            // Standard Text Message
            bubble.className = `message-bubble ${isMine ? 'mine' : 'theirs'}`;
            bubble.textContent = message.body;
        }

        const time = document.createElement('div');
        time.className = 'message-time';

        // Format time
        const date = new Date(message.created_at);
        time.textContent = date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });

        wrapper.appendChild(bubble);
        wrapper.appendChild(time);

        if (prepend) {
            // Insert after the load more button if it exists, or at top
            const loadBtnContainer = document.querySelector('#loadOlderBtn')?.parentNode;
            if (loadBtnContainer && loadBtnContainer.parentNode === messagesContainer) {
                messagesContainer.insertBefore(wrapper, loadBtnContainer.nextSibling);
            } else {
                messagesContainer.insertBefore(wrapper, messagesContainer.firstChild);
            }
        } else {
            bubble.style.opacity = '0';
            bubble.style.transition = 'opacity 0.3s ease';
            messagesContainer.appendChild(wrapper);
            requestAnimationFrame(() => {
                bubble.style.opacity = '1';
            });
            scrollToBottom();
        }
    };

    // --- Input Validation ---
    const updateSendButton = () => {
        if (!sendButton) return;
        const isValid = messageInput.value.trim().length > 0;
        sendButton.disabled = !isValid;
        sendButton.style.opacity = isValid ? '1' : '0.5';
        sendButton.style.cursor = isValid ? 'pointer' : 'not-allowed';
    };

    messageInput.addEventListener('input', updateSendButton);
    updateSendButton();

    // State for sending
    let isSending = false;

    // Capture original button content on load
    if (sendButton) {
        // Ensure we don't capture a spinner if the page somehow loaded with it
        if (!sendButton.innerHTML.includes('animate-spin')) {
            sendButton.dataset.defaultContent = sendButton.innerHTML;
        } else {
            sendButton.dataset.defaultContent = 'Send';
        }
    }

    // Helper: Reset button state strictly
    const resetButtonState = () => {
        if (!sendButton) return;

        isSending = false;
        sendButton.disabled = false;

        // Restore text
        if (sendButton.dataset.defaultContent) {
            sendButton.innerHTML = sendButton.dataset.defaultContent;
        } else {
            sendButton.textContent = 'Send';
        }

        updateSendButton();
    };

    // Handle Submit
    chatForm.addEventListener('submit', async function (e) {
        if (e.cancelable) e.preventDefault();

        if (isSending) return;

        const body = messageInput.value.trim();
        if (!body) return;

        isSending = true;

        // UI Optimistic Updates
        messageInput.value = '';
        messageInput.style.height = 'auto';
        messageInput.focus();
        updateSendButton();

        // 1. Store original visual state
        let originalContent = '';
        if (sendButton) {
            originalContent = sendButton.innerHTML;
            sendButton.disabled = true;
            // Simple loading spinner
            sendButton.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>`;
        }

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

            // 2. Handle Errors
            if (!response.ok) {
                // Try to get error message from JSON
                let errorMessage = 'Send failed';
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.error || errorMessage;
                } catch (e) { }

                throw new Error(errorMessage);
            }

            const message = await response.json();

            // Ensure ID types match for comparison
            message.sender_id = parseInt(message.sender_id);

            appendMessage(message);

        } catch (error) {
            console.error('Error sending message:', error);
            // Show user friendly error and restore input
            alert(error.message || 'Failed to send message. Please try again.');
            messageInput.value = body;
        } finally {
            // 3. Always restore button state
            if (sendButton) {
                sendButton.disabled = false;
                sendButton.innerHTML = originalContent;
            }
        }
    });

    // Support Enter to send
    messageInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!isSending) {
                const event = new Event('submit', {
                    bubbles: true,
                    cancelable: true
                });
                chatForm.dispatchEvent(event);
            }
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

    // --- Load Older Messages Logic ---
    const loadOlderBtn = document.getElementById('loadOlderBtn');
    if (loadOlderBtn) {
        loadOlderBtn.addEventListener('click', async () => {
            // Find the oldest message currently displayed
            // We need to skip the button itself if it's in the container
            const allWrappers = messagesContainer.querySelectorAll('.message-wrapper');
            const firstMsg = allWrappers[0];

            if (!firstMsg) return;

            const beforeId = firstMsg.dataset.id;
            loadOlderBtn.classList.add('loading');
            loadOlderBtn.disabled = true;
            const originalText = loadOlderBtn.textContent;
            loadOlderBtn.textContent = 'Loading...';

            try {
                const response = await fetch(`/messages/${conversationId}/poll?before=${beforeId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                });

                if (response.ok) {
                    const messages = await response.json();
                    if (Array.isArray(messages) && messages.length > 0) {
                        const oldScrollHeight = messagesContainer.scrollHeight;
                        const oldScrollTop = messagesContainer.scrollTop;

                        // They are returned sorted ASC (Oldest...Newest of that batch)
                        // To prepend correctly:
                        // [Msg1, Msg2... Msg50]
                        // We iterate Backwards: Msg50 prepended (Top), Msg49 prepended (Top)...
                        // Result: Msg1...Msg50 at Top.

                        for (let i = messages.length - 1; i >= 0; i--) {
                            messages[i].sender_id = parseInt(messages[i].sender_id);
                            appendMessage(messages[i], true);
                        }

                        // Adjust scroll
                        const newScrollHeight = messagesContainer.scrollHeight;
                        messagesContainer.scrollTop = newScrollHeight - oldScrollHeight + oldScrollTop;

                        if (messages.length < 50) {
                            loadOlderBtn.textContent = 'No more messages';
                            loadOlderBtn.style.display = 'none';
                        } else {
                            loadOlderBtn.textContent = originalText;
                        }

                    } else {
                        loadOlderBtn.textContent = 'No more messages';
                        loadOlderBtn.style.display = 'none';
                    }
                }
            } catch (err) {
                console.error('Error loading older messages', err);
                loadOlderBtn.textContent = originalText;
            } finally {
                loadOlderBtn.classList.remove('loading');
                loadOlderBtn.disabled = false;
            }
        });
    }

    // Cleanup on page unload (optional in MPA but good practice)
    window.addEventListener('beforeunload', () => {
        clearInterval(pollInterval);
    });
});
