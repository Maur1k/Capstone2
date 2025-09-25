<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatBot</title>
    <link rel="stylesheet" href="/css/services.css">
    <link rel="stylesheet" href="/css/chatbot.css">
</head>

<body>
    <div class="viewport">
        <div class="stage">
            <div class="wrapper">
                <div class="chat-wrapper">
                    <div class="chat-header">
                        <button class="back-btn" onclick="window.history.back()" aria-label="Go back">◀</button>
                        <div class="brand">
                            <span class="brand-title">ChatBot</span>
                            <span class="brand-icon">🤖</span>
                        </div>
                    </div>

                    <div class="chat-body" id="chatBody">
                        <div class="tips">Tap to send</div>
                        <div class="suggestions">
                            <button class="chip" data-text="How can I request a Business Permit?">How can I request a
                                Business
                                Permit?</button>
                            <button class="chip" data-text="Where is the BFA Office located?">Where is the BFA Office
                                located?</button>
                            <button class="chip" data-text="What's the best tourist spot here in Alaminos City?">What's
                                the best
                                tourist spot here in Alaminos City?</button>
                        </div>
                    </div>

                    <div class="chat-input">
                        <input id="userInput" type="text" placeholder="Aa" autocomplete="off" />
                        <button id="sendBtn" aria-label="Send">▶</button>
                    </div>
                </div>
            </div>
            <div class="footer">Powered by: Smart Municipal Kiosk</div>
        </div>
    </div>

    <script>
        (function () {
            const body = document.getElementById('chatBody');
            const input = document.getElementById('userInput');
            const sendBtn = document.getElementById('sendBtn');
            const chips = document.querySelectorAll('.chip');

            function appendMessage(text, sender) {
                const bubble = document.createElement('div');
                bubble.className = 'bubble ' + (sender === 'user' ? 'user' : 'bot');
                bubble.textContent = text;
                body.appendChild(bubble);
                body.scrollTop = body.scrollHeight;
            }

            async function sendMessage(text) {
                if (!text) return;
                appendMessage(text, 'user');
                input.value = '';
                try {
                    const res = await fetch('/chatbot/message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ message: text, sessionId: localStorage.getItem('cb_session') || '' })
                    });
                    const data = await res.json();
                    if (data && data.reply) {
                        appendMessage(data.reply, 'bot');
                        if (data.sessionId) localStorage.setItem('cb_session', data.sessionId);
                    } else {
                        appendMessage('Sorry, I could not get a response right now.', 'bot');
                    }
                } catch (e) {
                    appendMessage('Network error. Please try again later.', 'bot');
                }
            }

            sendBtn.addEventListener('click', () => sendMessage(input.value.trim()));
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') sendMessage(input.value.trim());
            });
            chips.forEach(c => c.addEventListener('click', () => sendMessage(c.dataset.text)));
        })();
    </script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</body>

</html>
