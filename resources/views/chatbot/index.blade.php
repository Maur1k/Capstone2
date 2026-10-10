<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click2ServeBot</title>
    <link rel="stylesheet" href="/css/services.css">
    <link rel="stylesheet" href="/css/chatbot.css">
    <link rel="stylesheet" href="{{ asset('css/landing-kiosk.css') }}">
</head>

<body>
    @include('home.landing')
    <div class="viewport">
        <div class="stage">
            <div class="wrapper">
                <div class="chat-wrapper">
                    <div class="chat-header">
                        <button class="back-btn" onclick="window.history.back()" aria-label="Go back">◀</button>
                        <div class="brand">
                            <span class="brand-title">Click2ServeBot</span>
                        </div>
                    </div>

                    <div class="chat-body" id="chatBody">
                        <div class="usage-info">
                            <div class="info-card">
                                <div class="info-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                </div>
                                <div class="info-content">
                                    <h3>How to use Click2ServeBot</h3>
                                    <ul class="info-list">
                                        <li>Tap on any suggested question to start</li>
                                        <li>Type your question in the input field below</li>
                                        <li>Press Enter or tap the send button</li>
                                        <li>Ask follow-up questions for more details</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="tips" id="dynamicTips">Ask me about municipal services</div>
                        <div class="suggestions" id="dynamicSuggestions">
                            <!-- Dynamic suggestions will be loaded here -->
                        </div>
                    </div>

                    <div class="chat-input">
                        <input id="userInput" type="text" placeholder="Type your question here..." autocomplete="off" />
                        <button id="sendBtn" aria-label="Send" title="Send message" type="button">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="footer">Powered by: Click2Serve</div>
        </div>
    </div>

    <script>
        (function () {
            const body = document.getElementById('chatBody');
            const input = document.getElementById('userInput');
            const sendBtn = document.getElementById('sendBtn');
            const tipsElement = document.getElementById('dynamicTips');
            const suggestionsContainer = document.getElementById('dynamicSuggestions');

            let conversationContext = [];
            let allQuestions = [];
            let currentQuestions = [];

            // Fetch questions from knowledge base
            async function fetchQuestionsFromKnowledgeBase() {
                try {
                    const response = await fetch('/chatbot/knowledge-base/questions?limit=50');
                    const data = await response.json();
                    
                    if (data.success && data.questions && data.questions.length > 0) {
                        allQuestions = data.questions;
                        shuffleAndUpdateSuggestions();
                    } else {
                        // Fallback to default suggestions if API fails
                        useDefaultSuggestions();
                    }
                } catch (error) {
                    console.error('Error fetching questions:', error);
                    useDefaultSuggestions();
                }
            }

            // Shuffle array function
            function shuffleArray(array) {
                const shuffled = [...array];
                for (let i = shuffled.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
                }
                return shuffled;
            }

            // Shuffle and update suggestions
            function shuffleAndUpdateSuggestions() {
                if (allQuestions.length === 0) {
                    useDefaultSuggestions();
                    return;
                }

                // Shuffle all questions and take 3 random ones
                const shuffled = shuffleArray(allQuestions);
                currentQuestions = shuffled.slice(0, 3);
                
                // Update tip and suggestions
                tipsElement.textContent = "Ask me about municipal services";
                renderSuggestions(currentQuestions);
            }

            // Default fallback suggestions
            const defaultSuggestions = [
                "How can I request a Business Permit?",
                "Where is the Civil Registry Office located?",
                "What documents do I need for birth certificate?"
            ];

            function useDefaultSuggestions() {
                tipsElement.textContent = "Ask me about municipal services";
                renderSuggestions(defaultSuggestions);
            }

            // Contextual suggestions based on conversation topics
            const contextualSuggestions = {
                'business': [
                    "What are the business permit requirements?",
                    "How much does a business permit cost?",
                    "Where is the Business Permit Office?"
                ],
                'birth': [
                    "What documents are needed for birth certificate?",
                    "How long does birth certificate processing take?",
                    "Can I request a delayed birth certificate?"
                ],
                'marriage': [
                    "What are marriage certificate requirements?",
                    "How do I schedule a civil wedding?",
                    "What fees are required for marriage documents?"
                ],
                'tourism': [
                    "How do I book a tour to Hundred Islands?",
                    "What are the best restaurants in Alaminos?",
                    "Are there accommodations near the islands?"
                ],
                'tax': [
                    "How do I pay real property tax?",
                    "Where is the City Treasurer's Office?",
                    "Can I pay taxes online?"
                ]
            };

            function updateTipsAndSuggestions() {
                // Determine context from recent conversation
                const context = getConversationContext();

                if (context && contextualSuggestions[context]) {
                    // Show contextual suggestions
                    tipsElement.textContent = `Ask more about ${context} services`;
                    renderSuggestions(contextualSuggestions[context]);
                } else {
                    // Show shuffled questions from knowledge base
                    if (allQuestions.length > 0) {
                        shuffleAndUpdateSuggestions();
                    } else {
                        useDefaultSuggestions();
                    }
                }
            }

            function getConversationContext() {
                const recentMessages = conversationContext.slice(-3);
                const combinedText = recentMessages.join(' ').toLowerCase();

                if (combinedText.includes('business') || combinedText.includes('permit')) return 'business';
                if (combinedText.includes('birth') || combinedText.includes('certificate')) return 'birth';
                if (combinedText.includes('marriage') || combinedText.includes('wedding')) return 'marriage';
                if (combinedText.includes('tourist') || combinedText.includes('island') || combinedText.includes('alaminos')) return 'tourism';
                if (combinedText.includes('tax') || combinedText.includes('payment')) return 'tax';

                return null;
            }

            function renderSuggestions(suggestions) {
                suggestionsContainer.innerHTML = '';
                suggestions.forEach((suggestion, index) => {
                    const chip = document.createElement('button');
                    chip.className = 'chip';
                    chip.textContent = suggestion;
                    chip.dataset.text = suggestion;
                    chip.style.animationDelay = `${(index + 1) * 0.1}s`;
                    chip.addEventListener('click', () => sendMessage(suggestion));
                    suggestionsContainer.appendChild(chip);
                });
            }

            function rotateSuggestions() {
                // Only rotate if no contextual suggestions are shown
                if (!getConversationContext()) {
                    // Shuffle and show new questions from knowledge base
                    if (allQuestions.length > 0) {
                        shuffleAndUpdateSuggestions();
                    } else {
                        updateTipsAndSuggestions();
                    }
                }
            }

            function appendMessage(text, sender, isTyping = false, metadata = null) {
                // Hide suggestions and usage info when conversation starts
                if (sender === 'user') {
                    const usageInfo = document.querySelector('.usage-info');
                    if (usageInfo) {
                        usageInfo.style.display = 'none';
                    }
                    if (suggestionsContainer.children.length > 0) {
                        suggestionsContainer.style.display = 'none';
                        tipsElement.textContent = 'Continue the conversation';
                    }
                }

                const bubble = document.createElement('div');
                bubble.className = 'bubble ' + (sender === 'user' ? 'user' : 'bot');
                
                // Support basic markdown-like formatting
                const formattedText = text
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                    .replace(/\n/g, '<br>');
                
                bubble.innerHTML = formattedText;
                
                // Add 3D Map button if location question detected
                if (sender === 'bot' && metadata && metadata.show3DMapButton) {
                    const mapButton = document.createElement('button');
                    mapButton.className = 'map-redirect-btn';
                    mapButton.innerHTML = '<i class="fas fa-map-marked-alt"></i> View on 3D Map';
                    mapButton.onclick = function() {
                        window.location.href = '/3dmap';
                    };
                    bubble.appendChild(mapButton);
                }
                
                body.appendChild(bubble);
                
                // Smooth scroll to bottom
                setTimeout(() => {
                    body.scrollTo({
                        top: body.scrollHeight,
                        behavior: 'smooth'
                    });
                }, 100);

                // Track conversation for context
                if (sender === 'user') {
                    conversationContext.push(text);
                    if (conversationContext.length > 10) {
                        conversationContext.shift(); // Keep only last 10 messages
                    }
                }

                // Update suggestions after bot response
                if (sender === 'bot' && !isTyping) {
                    setTimeout(() => {
                        updateTipsAndSuggestions();
                        suggestionsContainer.style.display = 'flex';
                    }, 1000);
                }
            }

            async function sendMessage(text) {
                if (!text || !text.trim()) return;
                
                const messageText = text.trim();
                appendMessage(messageText, 'user');
                input.value = '';
                
                // Disable input and button while sending
                input.disabled = true;
                sendBtn.disabled = true;
                sendBtn.classList.add('sending');

                // Show typing indicator
                const typingIndicator = document.createElement('div');
                typingIndicator.className = 'bubble bot typing';
                typingIndicator.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
                body.appendChild(typingIndicator);
                
                // Smooth scroll to typing indicator
                setTimeout(() => {
                    body.scrollTo({
                        top: body.scrollHeight,
                        behavior: 'smooth'
                    });
                }, 100);

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    const token = csrfToken ? csrfToken.getAttribute('content') : '';
                    
                    const res = await fetch('/chatbot/message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ 
                            message: messageText, 
                            sessionId: localStorage.getItem('cb_session') || '' 
                        })
                    });
                    
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    
                    const data = await res.json();

                    // Remove typing indicator
                    if (body.contains(typingIndicator)) {
                        body.removeChild(typingIndicator);
                    }

                    if (data && data.reply) {
                        appendMessage(data.reply, 'bot', false, data.metadata);
                        if (data.sessionId) {
                            localStorage.setItem('cb_session', data.sessionId);
                        }
                    } else {
                        appendMessage('Sorry, I could not get a response right now. Please try rephrasing your question.', 'bot');
                    }
                } catch (e) {
                    console.error('Chatbot error:', e);
                    // Remove typing indicator
                    if (body.contains(typingIndicator)) {
                        body.removeChild(typingIndicator);
                    }
                    appendMessage('Network error. Please check your connection and try again.', 'bot');
                } finally {
                    // Re-enable input and button
                    input.disabled = false;
                    sendBtn.disabled = false;
                    sendBtn.classList.remove('sending');
                    input.focus();
                }
            }

            // Initialize: Fetch questions from knowledge base
            fetchQuestionsFromKnowledgeBase();

            // Rotate suggestions every 30 seconds when idle
            setInterval(rotateSuggestions, 30000);
            
            // Refresh questions from knowledge base every 5 minutes
            setInterval(fetchQuestionsFromKnowledgeBase, 300000);

            // Event listeners
            sendBtn.addEventListener('click', () => {
                const message = input.value.trim();
                if (message) {
                    sendMessage(message);
                }
            });
            
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    const message = input.value.trim();
                    if (message) {
                        sendMessage(message);
                    }
                }
            });
            
            // Prevent form submission on Enter
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                }
            });

            // Focus input for better UX
            setTimeout(() => input.focus(), 100);
            
            // Auto-resize input if needed (for future multi-line support)
            input.addEventListener('input', () => {
                if (input.value.length > 0) {
                    sendBtn.style.opacity = '1';
                }
            });
        })();
    </script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</body>

</html>