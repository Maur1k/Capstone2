<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Click2Serve - Alaminos City')</title>

    <link rel="stylesheet" href="{{ asset('css/website/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo/logo_click2serve.png') }}">
    @stack('styles')
</head>

<body>

    {{-- Header --}}
    @include('website.partials.header')

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('website.partials.footer')

    {{-- Floating Chatbot Button --}}
    <div class="floating-chatbot-btn" id="floatingChatbotBtn">
        <div class="chatbot-icon">
            <i class="fas fa-robot"></i>
        </div>
        <span class="chatbot-text">Chat</span>
    </div>

    {{-- Chatbot Modal --}}
    <div class="chatbot-modal" id="chatbotModal">
        <div class="chatbot-modal-content">
            <div class="chatbot-header">
                <div class="chatbot-title">
                    <i class="fas fa-robot"></i>
                    <span>Chat Assistant</span>
                </div>
                <div class="chatbot-controls">
                    <button class="minimize-btn" id="minimizeBtn" title="Minimize">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button class="close-btn" id="closeBtn" title="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="chatbot-body">
                <div class="chatbot-welcome">
                    <div class="welcome-message">
                        <h3>Ask me about municipal services</h3>
                        <p>I'm here to assist you with municipal services, document requirements, and general
                            information about Alaminos City.</p>
                    </div>
                    <div class="quick-suggestions">
                        <!-- Dynamic suggestions will be loaded here -->
                    </div>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <!-- Messages will be added here dynamically -->
                </div>
            </div>
            <div class="chatbot-input">
                <input type="text" id="chatInput" placeholder="Type your message..." autocomplete="off">
                <button id="sendMessageBtn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Minimized Chatbot --}}
    <div class="minimized-chatbot" id="minimizedChatbot">
        <div class="minimized-icon">
            <i class="fas fa-robot"></i>
        </div>
        <span>Chat Assistant</span>
    </div>

    {{-- Scripts --}}
    <script>
        let index = 0;
        const slides = document.querySelector(".carousel .slides");
        const images = document.querySelectorAll(".carousel .slides img");
        const total = images.length;

        function showSlide(i) {
            index = (i + total) % total;
            slides.style.transform = `translateX(-${index * 100}%)`;
        }

        setInterval(() => {
            showSlide(index + 1);
        }, 3000);
    </script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: false,
        });
    </script>

    {{-- Floating Chatbot Button Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatbotBtn = document.getElementById('floatingChatbotBtn');
            const chatbotModal = document.getElementById('chatbotModal');
            const minimizedChatbot = document.getElementById('minimizedChatbot');
            const closeBtn = document.getElementById('closeBtn');
            const minimizeBtn = document.getElementById('minimizeBtn');
            const chatInput = document.getElementById('chatInput');
            const sendMessageBtn = document.getElementById('sendMessageBtn');
            const chatMessages = document.getElementById('chatMessages');
            const suggestionChips = document.querySelectorAll('.suggestion-chip');

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
                updateWelcomeMessage("Ask me about municipal services");
                renderSuggestions(currentQuestions);
            }

            // Default fallback suggestions
            const defaultSuggestions = [
                "How can I request a Business Permit?",
                "Where is the Civil Registry Office located?",
                "What documents do I need for birth certificate?"
            ];

            function useDefaultSuggestions() {
                updateWelcomeMessage("Ask me about municipal services");
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

            // Show modal when chatbot button is clicked
            chatbotBtn.addEventListener('click', function () {
                chatbotModal.classList.add('show');
                chatbotBtn.style.display = 'none';
                chatInput.focus();
            });

            // Show modal when minimized chatbot is clicked
            minimizedChatbot.addEventListener('click', function () {
                chatbotModal.classList.add('show');
                minimizedChatbot.classList.remove('show');
                chatInput.focus();
            });

            // Close modal
            closeBtn.addEventListener('click', function () {
                chatbotModal.classList.remove('show');
                chatbotBtn.style.display = 'flex';
            });

            // Minimize modal
            minimizeBtn.addEventListener('click', function () {
                chatbotModal.classList.remove('show');
                minimizedChatbot.classList.add('show');
            });

            // Dynamic suggestion functions
            function updateTipsAndSuggestions() {
                // Determine context from recent conversation
                const context = getConversationContext();

                if (context && contextualSuggestions[context]) {
                    // Show contextual suggestions
                    updateWelcomeMessage(`Ask more about ${context} services`);
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
                const suggestionsContainer = document.querySelector('.quick-suggestions');
                suggestionsContainer.innerHTML = '';
                suggestions.forEach((suggestion, index) => {
                    const chip = document.createElement('button');
                    chip.className = 'suggestion-chip';
                    chip.textContent = suggestion;
                    chip.dataset.message = suggestion;
                    chip.style.animationDelay = `${(index + 1) * 0.1}s`;
                    suggestionsContainer.appendChild(chip);
                });
                // Attach listeners to new chips
                attachSuggestionListeners();
            }

            function updateWelcomeMessage(tip) {
                const welcomeTitle = document.querySelector('.welcome-message h3');
                if (welcomeTitle) {
                    welcomeTitle.textContent = tip;
                }
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

            // Send message function
            function sendMessage(message) {
                if (!message.trim()) return;

                console.log('Sending message:', message);

                // Add user message to chat
                addMessage(message, 'user');

                // Hide welcome message and suggestions when conversation starts
                const welcome = document.querySelector('.chatbot-welcome');
                if (welcome && welcome.style.display !== 'none') {
                    welcome.style.display = 'none';
                }

                // Show typing indicator
                const typingMessage = addMessage('Typing...', 'bot', true);

                // Set timeout to remove typing indicator after 30 seconds
                const typingTimeout = setTimeout(() => {
                    if (typingMessage && typingMessage.parentNode) {
                        typingMessage.remove();
                        addMessage('Sorry, the request timed out. Please try again.', 'bot');
                    }
                }, 30000);

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                console.log('CSRF Token:', csrfToken);

                // Send to chatbot API
                fetch('/chatbot/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message: message,
                        sessionId: localStorage.getItem('cb_session') || ''
                    })
                })
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        // Clear timeout
                        clearTimeout(typingTimeout);
                        // Remove typing indicator
                        if (typingMessage) {
                            typingMessage.remove();
                        }

                        if (data && data.reply) {
                            addMessage(data.reply, 'bot', false, data.metadata);
                            if (data.sessionId) {
                                localStorage.setItem('cb_session', data.sessionId);
                            }

                            // Update suggestions after bot response
                            setTimeout(() => {
                                updateTipsAndSuggestions();
                                const welcome = document.querySelector('.chatbot-welcome');
                                if (welcome) {
                                    welcome.style.display = 'block';
                                }
                            }, 1000);
                        } else {
                            addMessage('Sorry, I could not get a response right now.', 'bot');
                        }
                    })
                    .catch(error => {
                        console.error('Chatbot API error:', error);
                        // Clear timeout
                        clearTimeout(typingTimeout);
                        // Remove typing indicator
                        if (typingMessage) {
                            typingMessage.remove();
                        }
                        addMessage('Network error. Please try again later.', 'bot');
                    });

                // Track conversation for context
                conversationContext.push(message);
                if (conversationContext.length > 10) {
                    conversationContext.shift();
                }
            }

            // Add message to chat
            function addMessage(text, sender, isTyping = false, metadata = null) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${sender}`;

                const bubbleDiv = document.createElement('div');
                bubbleDiv.className = 'message-bubble';
                bubbleDiv.textContent = text;

                // 3D Map button is only available in kiosk mode, not in website chatbot
                // Skip adding the button for website chatbot modal

                messageDiv.appendChild(bubbleDiv);

                if (!isTyping) {
                    const timeDiv = document.createElement('div');
                    timeDiv.className = 'message-time';
                    timeDiv.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    messageDiv.appendChild(timeDiv);
                }

                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;

                // Track conversation for context
                if (sender === 'user') {
                    conversationContext.push(text);
                    if (conversationContext.length > 10) {
                        conversationContext.shift(); // Keep only last 10 messages
                    }
                }

                return messageDiv;
            }

            // Send message on button click
            sendMessageBtn.addEventListener('click', function () {
                const message = chatInput.value.trim();
                if (message) {
                    sendMessage(message);
                    chatInput.value = '';
                }
            });

            // Send message on Enter key
            chatInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    const message = chatInput.value.trim();
                    if (message) {
                        sendMessage(message);
                        chatInput.value = '';
                    }
                }
            });

            // Handle suggestion chips (both initial and dynamic)
            function attachSuggestionListeners() {
                const allChips = document.querySelectorAll('.suggestion-chip');
                allChips.forEach(chip => {
                    chip.addEventListener('click', function () {
                        const message = this.getAttribute('data-message');
                        sendMessage(message);
                    });
                });
            }

            // Attach listeners to initial chips
            attachSuggestionListeners();

            // Initialize: Fetch questions from knowledge base
            fetchQuestionsFromKnowledgeBase();

            // Rotate suggestions every 30 seconds when idle
            setInterval(rotateSuggestions, 30000);

            // Refresh questions from knowledge base every 5 minutes
            setInterval(fetchQuestionsFromKnowledgeBase, 300000);

            // Add pulse animation after 3 seconds to draw attention
            setTimeout(function () {
                chatbotBtn.classList.add('pulse');

                // Remove pulse after 10 seconds
                setTimeout(function () {
                    chatbotBtn.classList.remove('pulse');
                }, 10000);
            }, 3000);

            // Add hover effects
            chatbotBtn.addEventListener('mouseenter', function () {
                this.classList.remove('pulse');
            });
        });
    </script>

    @stack('scripts')
</body>

</html>