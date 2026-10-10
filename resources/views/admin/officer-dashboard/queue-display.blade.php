<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Queue Display - {{ $department }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --teal-primary: #008080;
            --teal-light: #00A0A0;
            --teal-dark: #006666;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-400: #94a3b8;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --green-500: #10b981;
            --green-600: #059669;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--white);
            color: var(--gray-800);
            overflow: hidden;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .display-header {
            background: linear-gradient(180deg, #0e90a6 0%, #087a93 50%, #0a6a80 100%);
            padding: 40px 50px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .display-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.15;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            animation: patternMove 20s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes patternMove {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            50% {
                transform: translate(-20px, -20px) scale(1.1);
            }
        }

        .display-header-content {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            width: 100%;
        }

        .display-header-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 
                0 8px 20px rgba(0, 0, 0, 0.2),
                inset 0 0 20px rgba(255, 255, 255, 0.1);
            justify-self: start;
        }

        .display-header-logo img {
            width: 65px;
            height: 65px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        .display-header-text {
            grid-column: 2;
            text-align: center;
            justify-self: center;
        }

        .display-header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 12px;
            letter-spacing: 1px;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            position: relative;
            word-wrap: break-word;
            line-height: 1.2;
        }

        .display-header h1::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 3px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 2px;
        }

        .display-header .date-time {
            font-size: 1.5rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.95);
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .display-header-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: 1;
        }

        .display-particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            animation: floatParticle 20s infinite ease-in-out;
        }

        .display-particle:nth-child(1) {
            width: 60px;
            height: 60px;
            left: 10%;
            top: 20%;
            animation-delay: 0s;
        }

        .display-particle:nth-child(2) {
            width: 80px;
            height: 80px;
            right: 15%;
            top: 60%;
            animation-delay: 2s;
        }

        .display-particle:nth-child(3) {
            width: 50px;
            height: 50px;
            left: 70%;
            top: 10%;
            animation-delay: 4s;
        }

        @keyframes floatParticle {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
                opacity: 0.15;
            }
            25% {
                transform: translate(20px, -20px) rotate(90deg);
                opacity: 0.2;
            }
            50% {
                transform: translate(-15px, 15px) rotate(180deg);
                opacity: 0.18;
            }
            75% {
                transform: translate(15px, 20px) rotate(270deg);
                opacity: 0.2;
            }
        }

        .display-content {
            flex: 1;
            padding: 20px 0;
            background: var(--gray-50);
            display: flex;
            align-items: flex-start;
            justify-content: stretch;
            overflow: hidden;
        }

        .queue-table-container {
            width: 100%;
            background: var(--white);
            border-radius: 0;
            box-shadow: none;
            overflow: hidden;
        }

        .queue-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .queue-table thead {
            background: transparent;
        }

        .queue-table th {
            padding: 30px 20px;
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 0.5px;
            border-right: 2px solid rgba(255, 255, 255, 0.2);
        }

        /* Default header colors - first column gray, second teal, last green, others teal */
        .queue-table th:first-child {
            background: linear-gradient(135deg, var(--gray-600) 0%, var(--gray-700) 100%);
        }

        .queue-table th:nth-child(2) {
            background: linear-gradient(135deg, var(--teal-primary) 0%, var(--teal-light) 100%);
        }

        .queue-table th:last-child {
            background: linear-gradient(135deg, var(--green-500) 0%, var(--green-600) 100%);
            border-right: none;
        }

        /* For columns beyond 3, use teal gradient */
        .queue-table th:nth-child(n+4) {
            background: linear-gradient(135deg, var(--teal-primary) 0%, var(--teal-light) 100%);
        }

        /* Dynamic column width - will be set inline */
        .queue-table {
            width: 100%;
        }

        .queue-table tbody tr {
            border-bottom: none;
        }

        .queue-table td {
            padding: 25px 20px;
            text-align: center;
            vertical-align: middle;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--gray-800);
            border-right: 1px solid var(--gray-200);
            min-height: 80px;
        }

        .queue-table td:last-child {
            border-right: none;
        }

        /* Default styles for known columns */
        .queue-table td.on-queue {
            color: var(--gray-700);
            background: var(--gray-50);
        }

        .queue-table td.to-submit {
            color: var(--teal-primary);
        }

        .queue-table td.processed {
            color: var(--green-600);
        }

        /* Generic styles for dynamic columns - apply teal color by default */
        .queue-table td:not(.on-queue):not(.to-submit):not(.processed) {
            color: var(--teal-primary);
        }

        .queue-number {
            display: inline-block;
            padding: 8px 16px;
            transition: all 0.3s ease;
            position: relative;
        }

        /* Queue transition animation - 5 second dramatic effect */
        .queue-number.queue-transition-in {
            animation: queueTransition 5s ease-in-out;
            position: relative;
        }

        @keyframes queueTransition {
            0% {
                transform: scale(0.5);
                opacity: 0;
                background: rgba(20, 184, 166, 0.1);
                border-radius: 12px;
                box-shadow: 0 0 0 0 rgba(20, 184, 166, 0.6);
            }
            5% {
                transform: scale(1.3);
                opacity: 1;
                background: rgba(20, 184, 166, 0.4);
                box-shadow: 0 0 40px 20px rgba(20, 184, 166, 0.5);
            }
            15% {
                transform: scale(1.15);
                background: rgba(255, 215, 0, 0.3);
                box-shadow: 0 0 30px 15px rgba(255, 215, 0, 0.4);
            }
            25% {
                transform: scale(1.25);
                background: rgba(20, 184, 166, 0.35);
                box-shadow: 0 0 35px 18px rgba(20, 184, 166, 0.45);
            }
            35% {
                transform: scale(1.1);
                background: rgba(255, 215, 0, 0.25);
                box-shadow: 0 0 25px 12px rgba(255, 215, 0, 0.35);
            }
            50% {
                transform: scale(1.2);
                background: rgba(20, 184, 166, 0.3);
                box-shadow: 0 0 30px 15px rgba(20, 184, 166, 0.4);
            }
            65% {
                transform: scale(1.08);
                background: rgba(255, 215, 0, 0.2);
                box-shadow: 0 0 20px 10px rgba(255, 215, 0, 0.3);
            }
            80% {
                transform: scale(1.12);
                background: rgba(20, 184, 166, 0.15);
                box-shadow: 0 0 15px 8px rgba(20, 184, 166, 0.25);
            }
            90% {
                transform: scale(1.05);
                background: rgba(20, 184, 166, 0.08);
                box-shadow: 0 0 8px 4px rgba(20, 184, 166, 0.15);
            }
            100% {
                transform: scale(1);
                opacity: 1;
                background: transparent;
                box-shadow: 0 0 0 0 rgba(20, 184, 166, 0);
            }
        }

        /* Additional pulse ring effects for enhanced visibility */
        .queue-number.queue-transition-in::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            transform: translate(-50%, -50%);
            border-radius: 12px;
            border: 3px solid rgba(20, 184, 166, 0.6);
            animation: pulseRing 2.5s ease-out 2;
            pointer-events: none;
        }

        .queue-number.queue-transition-in::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            transform: translate(-50%, -50%);
            border-radius: 12px;
            border: 2px solid rgba(255, 215, 0, 0.5);
            animation: pulseRingGold 2.5s ease-out 2;
            animation-delay: 0.3s;
            pointer-events: none;
        }

        @keyframes pulseRing {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.8;
                border-width: 3px;
            }
            50% {
                opacity: 0.4;
            }
            100% {
                transform: translate(-50%, -50%) scale(2.5);
                opacity: 0;
                border-width: 1px;
            }
        }

        @keyframes pulseRingGold {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.7;
                border-width: 2px;
            }
            50% {
                opacity: 0.3;
            }
            100% {
                transform: translate(-50%, -50%) scale(2.2);
                opacity: 0;
                border-width: 1px;
            }
        }

        /* Attention-grabbing glow effect on the text */
        .queue-number.queue-transition-in {
            text-shadow: 0 0 10px rgba(20, 184, 166, 0.8), 
                         0 0 20px rgba(20, 184, 166, 0.6),
                         0 0 30px rgba(20, 184, 166, 0.4);
            animation: queueTransition 5s ease-in-out, textGlow 1s ease-in-out 5;
        }

        @keyframes textGlow {
            0%, 100% {
                text-shadow: 0 0 10px rgba(20, 184, 166, 0.8), 
                             0 0 20px rgba(20, 184, 166, 0.6),
                             0 0 30px rgba(20, 184, 166, 0.4);
            }
            50% {
                text-shadow: 0 0 20px rgba(255, 215, 0, 0.9), 
                             0 0 40px rgba(255, 215, 0, 0.7),
                             0 0 60px rgba(255, 215, 0, 0.5);
            }
        }

        .empty-cell {
            color: var(--gray-400);
            font-size: 1.5rem;
            font-weight: 500;
        }

        .loading-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--gray-600);
        }

        .loading-state h3 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-top: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-600);
        }

        .empty-state h3 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--gray-700);
        }

        .empty-state p {
            font-size: 1.25rem;
            color: var(--gray-600);
        }

        /* Responsive adjustments for TV screens */
        @media (max-width: 1920px) {
            .display-header h1 {
                font-size: 3rem;
            }
            .display-header-logo {
                width: 70px;
                height: 70px;
            }
            .display-header-logo img {
                width: 55px;
                height: 55px;
            }
            .queue-table th {
                font-size: 1.75rem;
            }
            .queue-table td {
                font-size: 2.25rem;
            }
        }

        @media (max-width: 1366px) {
            .display-header {
                padding: 30px 40px;
            }
            .display-header h1 {
                font-size: 2.5rem;
            }
            .display-header .date-time {
                font-size: 1.25rem;
            }
            .display-header-logo {
                width: 60px;
                height: 60px;
            }
            .display-header-logo img {
                width: 45px;
                height: 45px;
            }
            .queue-table th {
                font-size: 1.5rem;
                padding: 25px 15px;
            }
            .queue-table td {
                font-size: 2rem;
                padding: 20px 15px;
            }
        }

        /* Handle very long department names */
        @media (max-width: 1600px) {
            .display-header h1 {
                font-size: 2.8rem;
            }
        }

        /* Ensure department name doesn't overflow on smaller screens */
        .display-header-text {
            min-width: 0;
        }
    </style>
</head>
<body>
    <div class="display-header">
        <div class="display-header-particles">
            <div class="display-particle"></div>
            <div class="display-particle"></div>
            <div class="display-particle"></div>
        </div>
        <div class="display-header-content">
            <div class="display-header-logo">
                <img src="{{ asset('assets/logo/logo_click2serve.png') }}" alt="Click2Serve Logo">
            </div>
            <div class="display-header-text">
                <h1 id="office-display-name">{{ $officeDisplayName ?? $department }}</h1>
                <div id="window-display-name" class="window-display-name" style="font-size: 1.8rem; font-weight: 600; color: rgba(255, 255, 255, 0.95); margin-top: 8px; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2); display: {{ $windowDisplayName ? 'block' : 'none' }};">
                    {{ $windowDisplayName ?? '' }}
                </div>
                <div class="date-time">
                    <span id="current-date">{{ now()->format('F d, Y') }}</span>
                    <span style="margin: 0 12px; opacity: 0.7;">|</span>
                    <span id="current-time">{{ now()->format('h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="display-content" id="queue-display-content">
        <div class="loading-state">
            <h3>Loading Queue Data...</h3>
        </div>
    </div>

    <script>
        // Update time every second
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            });
        }
        setInterval(updateTime, 1000);

        // Track previously called queues to detect new calls
        let previousCalledQueues = new Set();
        // Track previous queue states (queue_number -> {status, window_number}) to detect status/window changes
        let previousQueueStates = new Map();
        
        // Audio context for playing chime sound
        let audioContext = null;
        let audioInitialized = false;
        
        // Sound types for different events
        const SOUND_TYPES = {
            CALLING: 'calling',           // Queue called/moved to To Submit - Doorbell chime
            PROCESSED: 'processed',       // Queue marked as processed - Success chime
            WINDOW_CHANGE: 'windowChange', // Queue moved between windows - Attention/Alert chime
            NEW_QUEUE: 'newQueue'         // New queue added - Simple notification
        };
        
        // Initialize audio - resume context if suspended (browser requires user interaction)
        function initAudio() {
            if (audioInitialized && audioContext) {
                // Resume audio context if suspended (browser security requirement)
                if (audioContext.state === 'suspended') {
                    audioContext.resume().then(() => {
                        console.log('Audio context resumed');
                    }).catch(err => {
                        console.warn('Failed to resume audio context:', err);
                    });
                }
                return;
            }
            
            try {
                // Create a simple chime sound using Web Audio API
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
                audioInitialized = true;
                console.log('Audio context initialized, state:', audioContext.state);
                
                // Resume if suspended
                if (audioContext.state === 'suspended') {
                    audioContext.resume().then(() => {
                        console.log('Audio context resumed on init');
                    });
                }
            } catch (e) {
                console.warn('Web Audio API not supported:', e);
            }
        }
        
        // Play chime sound based on event type
        function playChime(soundType = SOUND_TYPES.CALLING) {
            console.log('Attempting to play chime...', soundType);
            
            if (!audioContext) {
                initAudio();
            }
            
            if (!audioContext) {
                console.warn('Audio context not available');
                return;
            }
            
            // Resume audio context if suspended (required by browser security)
            if (audioContext.state === 'suspended') {
                audioContext.resume().then(() => {
                    console.log('Audio context resumed, playing chime');
                    playChimeSound(soundType);
                }).catch(err => {
                    console.warn('Failed to resume audio context:', err);
                    // Fallback: try to play anyway
                    playChimeSound(soundType);
                });
            } else {
                playChimeSound(soundType);
            }
        }
        
        // Play specific chime sound based on type
        function playChimeSound(soundType) {
            try {
                switch(soundType) {
                    case SOUND_TYPES.CALLING:
                        playCallingChime();
                        break;
                    case SOUND_TYPES.PROCESSED:
                        playProcessedChime();
                        break;
                    case SOUND_TYPES.WINDOW_CHANGE:
                        playWindowChangeChime();
                        break;
                    case SOUND_TYPES.NEW_QUEUE:
                        playNewQueueChime();
                        break;
                    default:
                        playCallingChime();
                }
            } catch (e) {
                console.warn('Error playing chime:', e);
            }
        }
        
        // === CALLING CHIME - Doorbell-style for queue called/moved to To Submit ===
        function playCallingChime() {
            const now = audioContext.currentTime;
            
            // Rich, attention-grabbing doorbell chime (Ding-Dong-Ding)
            
            // === First Chime (Ding) ===
            const osc1 = audioContext.createOscillator();
            const osc1b = audioContext.createOscillator();
            const gain1 = audioContext.createGain();
            
            osc1.connect(gain1);
            osc1b.connect(gain1);
            gain1.connect(audioContext.destination);
            
            osc1.frequency.setValueAtTime(659.25, now); // E5
            osc1.type = 'sine';
            osc1b.frequency.setValueAtTime(1318.5, now); // E6 (harmonic)
            osc1b.type = 'sine';
            
            gain1.gain.setValueAtTime(0, now);
            gain1.gain.linearRampToValueAtTime(0.5, now + 0.02);
            gain1.gain.exponentialRampToValueAtTime(0.2, now + 0.3);
            gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.8);
            
            osc1.start(now);
            osc1b.start(now);
            osc1.stop(now + 0.8);
            osc1b.stop(now + 0.8);
            
            // === Second Chime (Dong) - Lower tone ===
            const osc2 = audioContext.createOscillator();
            const osc2b = audioContext.createOscillator();
            const gain2 = audioContext.createGain();
            
            osc2.connect(gain2);
            osc2b.connect(gain2);
            gain2.connect(audioContext.destination);
            
            osc2.frequency.setValueAtTime(523.25, now + 0.15); // C5
            osc2.type = 'sine';
            osc2b.frequency.setValueAtTime(1046.5, now + 0.15); // C6 (harmonic)
            osc2b.type = 'sine';
            
            gain2.gain.setValueAtTime(0, now + 0.15);
            gain2.gain.linearRampToValueAtTime(0.45, now + 0.17);
            gain2.gain.exponentialRampToValueAtTime(0.15, now + 0.5);
            gain2.gain.exponentialRampToValueAtTime(0.01, now + 1.0);
            
            osc2.start(now + 0.15);
            osc2b.start(now + 0.15);
            osc2.stop(now + 1.0);
            osc2b.stop(now + 1.0);
            
            // === Third Chime (Resolution) ===
            const osc3 = audioContext.createOscillator();
            const gain3 = audioContext.createGain();
            
            osc3.connect(gain3);
            gain3.connect(audioContext.destination);
            
            osc3.frequency.setValueAtTime(783.99, now + 0.4); // G5
            osc3.type = 'sine';
            
            gain3.gain.setValueAtTime(0, now + 0.4);
            gain3.gain.linearRampToValueAtTime(0.35, now + 0.42);
            gain3.gain.exponentialRampToValueAtTime(0.1, now + 0.8);
            gain3.gain.exponentialRampToValueAtTime(0.01, now + 1.5);
            
            osc3.start(now + 0.4);
            osc3.stop(now + 1.5);
            
            // === Shimmer effect ===
            const shimmer = audioContext.createOscillator();
            const shimmerGain = audioContext.createGain();
            
            shimmer.connect(shimmerGain);
            shimmerGain.connect(audioContext.destination);
            
            shimmer.frequency.setValueAtTime(2637, now); // E7
            shimmer.type = 'sine';
            
            shimmerGain.gain.setValueAtTime(0, now);
            shimmerGain.gain.linearRampToValueAtTime(0.08, now + 0.05);
            shimmerGain.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
            
            shimmer.start(now);
            shimmer.stop(now + 0.5);
            
            console.log('Calling chime played successfully');
        }
        
        // === PROCESSED CHIME - Success/completion sound ===
        function playProcessedChime() {
            const now = audioContext.currentTime;
            
            // Triumphant ascending chime (success sound)
            
            // === First tone (Low) ===
            const osc1 = audioContext.createOscillator();
            const gain1 = audioContext.createGain();
            osc1.connect(gain1);
            gain1.connect(audioContext.destination);
            
            osc1.frequency.setValueAtTime(523.25, now); // C5
            osc1.type = 'sine';
            
            gain1.gain.setValueAtTime(0, now);
            gain1.gain.linearRampToValueAtTime(0.4, now + 0.02);
            gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
            
            osc1.start(now);
            osc1.stop(now + 0.5);
            
            // === Second tone (Mid) ===
            const osc2 = audioContext.createOscillator();
            const gain2 = audioContext.createGain();
            osc2.connect(gain2);
            gain2.connect(audioContext.destination);
            
            osc2.frequency.setValueAtTime(659.25, now + 0.12); // E5
            osc2.type = 'sine';
            
            gain2.gain.setValueAtTime(0, now + 0.12);
            gain2.gain.linearRampToValueAtTime(0.45, now + 0.14);
            gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.6);
            
            osc2.start(now + 0.12);
            osc2.stop(now + 0.6);
            
            // === Third tone (High) ===
            const osc3 = audioContext.createOscillator();
            const osc3b = audioContext.createOscillator();
            const gain3 = audioContext.createGain();
            osc3.connect(gain3);
            osc3b.connect(gain3);
            gain3.connect(audioContext.destination);
            
            osc3.frequency.setValueAtTime(783.99, now + 0.24); // G5
            osc3.type = 'sine';
            osc3b.frequency.setValueAtTime(1567.98, now + 0.24); // G6 (harmonic)
            osc3b.type = 'sine';
            
            gain3.gain.setValueAtTime(0, now + 0.24);
            gain3.gain.linearRampToValueAtTime(0.5, now + 0.26);
            gain3.gain.exponentialRampToValueAtTime(0.2, now + 0.6);
            gain3.gain.exponentialRampToValueAtTime(0.01, now + 1.2);
            
            osc3.start(now + 0.24);
            osc3b.start(now + 0.24);
            osc3.stop(now + 1.2);
            osc3b.stop(now + 1.2);
            
            // === Final resolution (High C) ===
            const osc4 = audioContext.createOscillator();
            const gain4 = audioContext.createGain();
            osc4.connect(gain4);
            gain4.connect(audioContext.destination);
            
            osc4.frequency.setValueAtTime(1046.5, now + 0.4); // C6
            osc4.type = 'sine';
            
            gain4.gain.setValueAtTime(0, now + 0.4);
            gain4.gain.linearRampToValueAtTime(0.35, now + 0.42);
            gain4.gain.exponentialRampToValueAtTime(0.01, now + 1.5);
            
            osc4.start(now + 0.4);
            osc4.stop(now + 1.5);
            
            console.log('Processed chime played successfully');
        }
        
        // === WINDOW CHANGE CHIME - Alert/attention sound for window transfer ===
        function playWindowChangeChime() {
            const now = audioContext.currentTime;
            
            // Two-tone attention chime (like a PA system announcement)
            
            // === First tone (High) - Attention grabber ===
            const osc1 = audioContext.createOscillator();
            const osc1b = audioContext.createOscillator();
            const gain1 = audioContext.createGain();
            osc1.connect(gain1);
            osc1b.connect(gain1);
            gain1.connect(audioContext.destination);
            
            osc1.frequency.setValueAtTime(880, now); // A5
            osc1.type = 'sine';
            osc1b.frequency.setValueAtTime(1760, now); // A6 (octave)
            osc1b.type = 'sine';
            
            gain1.gain.setValueAtTime(0, now);
            gain1.gain.linearRampToValueAtTime(0.5, now + 0.02);
            gain1.gain.setValueAtTime(0.5, now + 0.15);
            gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.4);
            
            osc1.start(now);
            osc1b.start(now);
            osc1.stop(now + 0.4);
            osc1b.stop(now + 0.4);
            
            // === Second tone (Lower) - Information tone ===
            const osc2 = audioContext.createOscillator();
            const osc2b = audioContext.createOscillator();
            const gain2 = audioContext.createGain();
            osc2.connect(gain2);
            osc2b.connect(gain2);
            gain2.connect(audioContext.destination);
            
            osc2.frequency.setValueAtTime(659.25, now + 0.25); // E5
            osc2.type = 'sine';
            osc2b.frequency.setValueAtTime(1318.5, now + 0.25); // E6
            osc2b.type = 'sine';
            
            gain2.gain.setValueAtTime(0, now + 0.25);
            gain2.gain.linearRampToValueAtTime(0.45, now + 0.27);
            gain2.gain.setValueAtTime(0.45, now + 0.5);
            gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.9);
            
            osc2.start(now + 0.25);
            osc2b.start(now + 0.25);
            osc2.stop(now + 0.9);
            osc2b.stop(now + 0.9);
            
            // === Third tone (Back to high) - Resolution ===
            const osc3 = audioContext.createOscillator();
            const gain3 = audioContext.createGain();
            osc3.connect(gain3);
            gain3.connect(audioContext.destination);
            
            osc3.frequency.setValueAtTime(880, now + 0.6); // A5
            osc3.type = 'sine';
            
            gain3.gain.setValueAtTime(0, now + 0.6);
            gain3.gain.linearRampToValueAtTime(0.4, now + 0.62);
            gain3.gain.exponentialRampToValueAtTime(0.01, now + 1.3);
            
            osc3.start(now + 0.6);
            osc3.stop(now + 1.3);
            
            console.log('Window change chime played successfully');
        }
        
        // === NEW QUEUE CHIME - Simple notification for new queue added ===
        function playNewQueueChime() {
            const now = audioContext.currentTime;
            
            // Simple pleasant notification
            
            const osc1 = audioContext.createOscillator();
            const gain1 = audioContext.createGain();
            osc1.connect(gain1);
            gain1.connect(audioContext.destination);
            
            osc1.frequency.setValueAtTime(698.46, now); // F5
            osc1.type = 'sine';
            
            gain1.gain.setValueAtTime(0, now);
            gain1.gain.linearRampToValueAtTime(0.3, now + 0.02);
            gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
            
            osc1.start(now);
            osc1.stop(now + 0.5);
            
            // Second tone
            const osc2 = audioContext.createOscillator();
            const gain2 = audioContext.createGain();
            osc2.connect(gain2);
            gain2.connect(audioContext.destination);
            
            osc2.frequency.setValueAtTime(880, now + 0.1); // A5
            osc2.type = 'sine';
            
            gain2.gain.setValueAtTime(0, now + 0.1);
            gain2.gain.linearRampToValueAtTime(0.35, now + 0.12);
            gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.7);
            
            osc2.start(now + 0.1);
            osc2.stop(now + 0.7);
            
            console.log('New queue chime played successfully');
        }
        
        // Check if status is in "To Submit" category
        function isToSubmitStatus(status) {
            const statusLower = (status || '').toLowerCase();
            const toSubmitStatuses = ['accepted', 'checklist_given', 'incomplete_requirements', 
                                     'to_be_encoded_brap', 'negative_result', 'accepted_octogenarian', 
                                     'verified_with_problem', 'for_compliance'];
            return toSubmitStatuses.includes(statusLower);
        }
        
        // Check if status is in "On Queue" category
        function isOnQueueStatus(status) {
            const statusLower = (status || '').toLowerCase();
            return statusLower === 'inquiry' || statusLower === 'submitted';
        }
        
        // Check if status is in "Processed" category
        function isProcessedStatus(status) {
            const statusLower = (status || '').toLowerCase();
            const processedStatuses = ['registered', 'completed', 'positive_result', 
                                       'encoded_brap', 'psa_processing'];
            return processedStatuses.includes(statusLower);
        }
        
        // Get the category of a status
        function getStatusCategory(status) {
            if (isOnQueueStatus(status)) return 'on-queue';
            if (isToSubmitStatus(status)) return 'to-submit';
            if (isProcessedStatus(status)) return 'processed';
            return 'to-submit'; // Default for other statuses
        }

        // Load queue data
        function loadQueueDisplay() {
            const container = document.getElementById('queue-display-content');
            
            // Get window number from URL parameter or use default
            const urlParams = new URLSearchParams(window.location.search);
            const windowNumber = urlParams.get('window') || null;

            const url = '{{ route("officer.queue-data") }}' + (windowNumber ? `?window=${windowNumber}` : '');
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Queue data received:', data);
                    console.log('Columns config:', data.columns);
                    console.log('Column names:', data.column_names);
                    
                    // Always update office display name (use provided value or fallback to department)
                    const officeNameElement = document.getElementById('office-display-name');
                    if (officeNameElement) {
                        // Check if office_display_name exists and is not empty
                        const officeDisplayName = data.office_display_name && data.office_display_name.trim() !== '' 
                            ? data.office_display_name.trim() 
                            : (data.department || '{{ $department }}');
                        if (officeNameElement.textContent.trim() !== officeDisplayName) {
                            console.log('Updating office name from', officeNameElement.textContent, 'to', officeDisplayName);
                            officeNameElement.textContent = officeDisplayName;
                        }
                    }
                    
                    // Update window display name
                    const windowNameElement = document.getElementById('window-display-name');
                    if (windowNameElement) {
                        // Check if window_display_name exists and is not empty
                        const windowDisplayName = data.window_display_name && data.window_display_name.trim() !== '' 
                            ? data.window_display_name.trim() 
                            : null;
                        if (windowDisplayName) {
                            if (windowNameElement.textContent.trim() !== windowDisplayName) {
                                console.log('Updating window name from', windowNameElement.textContent, 'to', windowDisplayName);
                                windowNameElement.textContent = windowDisplayName;
                            }
                            windowNameElement.style.display = 'block';
                        } else {
                            // Hide window name if not filtering by window or if name is empty
                            windowNameElement.style.display = 'none';
                        }
                    }
                    
                    // Always render the display, even with empty data, to show all columns
                    if (data.success) {
                        const queueData = data.data || [];
                        // Check for newly called queues and play chime
                        if (queueData.length > 0) {
                            checkForNewCalledQueues(queueData);
                        }
                        renderQueueDisplay(queueData, data.department, data.column_names, data.queue_prefix, data.columns);
                    } else {
                        // Fallback: render with empty data but still show columns
                        renderQueueDisplay([], data.department, data.column_names, data.queue_prefix, data.columns);
                    }
                })
                .catch(error => {
                    console.error('Error loading queue data:', error);
                    container.innerHTML = `
                        <div class="empty-state">
                            <h3>Error Loading Queue Data</h3>
                            <p>Please check the connection and try again</p>
                        </div>
                    `;
                });
        }
        
        // Check for newly called queues and play appropriate chime
        function checkForNewCalledQueues(queueData) {
            const currentCalledQueues = new Set();
            const currentQueueStates = new Map(); // queue_number -> {status, window_number, called_at}
            
            // Sound queue - collect all sounds to play (prioritized)
            let soundsToPlay = [];
            
            console.log('Checking for queue changes. Previous states:', 
                Array.from(previousQueueStates.entries()).map(([k, v]) => `${k}: ${JSON.stringify(v)}`));
            
            // Collect all currently called queue numbers and their states
            queueData.forEach(item => {
                const queueKey = item.queue_number;
                const status = (item.status || '').toLowerCase();
                const windowNumber = item.window_number || null;
                const calledAt = item.called_at || null;
                
                // Store full state including window number
                currentQueueStates.set(queueKey, { status, windowNumber, calledAt });
                
                console.log(`Queue ${queueKey}: status=${status}, window=${windowNumber}, is_called=${item.is_called}, called_at=${calledAt}`);
                
                // Check if queue was called (has called_at timestamp)
                if (item.is_called && calledAt) {
                    const calledKey = `${item.queue_number}_${calledAt}`;
                    currentCalledQueues.add(calledKey);
                    
                    // If this is a new called queue, play calling chime
                    if (!previousCalledQueues.has(calledKey)) {
                        console.log('NEW: Queue called:', item.queue_number, 'at', calledAt);
                        soundsToPlay.push({ type: SOUND_TYPES.CALLING, priority: 1, queue: queueKey });
                    }
                }
                
                // Get previous state
                const previousState = previousQueueStates.get(queueKey);
                
                if (previousState) {
                    const prevStatus = previousState.status;
                    const prevWindow = previousState.windowNumber;
                    const prevCategory = getStatusCategory(prevStatus);
                    const currCategory = getStatusCategory(status);
                    
                    console.log(`Queue ${queueKey}: Previous=[status=${prevStatus}, window=${prevWindow}], Current=[status=${status}, window=${windowNumber}]`);
                    
                    // === WINDOW CHANGE DETECTION ===
                    // If window number changed (and both are valid), play window change chime
                    if (prevWindow !== null && windowNumber !== null && prevWindow !== windowNumber) {
                        console.log(`WINDOW CHANGE: Queue ${queueKey} moved from Window ${prevWindow} to Window ${windowNumber}`);
                        soundsToPlay.push({ type: SOUND_TYPES.WINDOW_CHANGE, priority: 2, queue: queueKey });
                    }
                    
                    // === STATUS CHANGE DETECTION ===
                    // On Queue -> To Submit (calling)
                    if (prevCategory === 'on-queue' && currCategory === 'to-submit') {
                        console.log('STATUS CHANGE: Queue moved to To Submit:', item.queue_number, 'from', prevStatus, 'to', status);
                        // Only add if not already added from called_at check
                        if (!soundsToPlay.find(s => s.queue === queueKey && s.type === SOUND_TYPES.CALLING)) {
                            soundsToPlay.push({ type: SOUND_TYPES.CALLING, priority: 1, queue: queueKey });
                        }
                    }
                    
                    // To Submit -> Processed (completion)
                    if (prevCategory === 'to-submit' && currCategory === 'processed') {
                        console.log('STATUS CHANGE: Queue processed:', item.queue_number, 'from', prevStatus, 'to', status);
                        soundsToPlay.push({ type: SOUND_TYPES.PROCESSED, priority: 3, queue: queueKey });
                    }
                    
                    // On Queue -> Processed (direct completion - less common)
                    if (prevCategory === 'on-queue' && currCategory === 'processed') {
                        console.log('STATUS CHANGE: Queue directly processed:', item.queue_number, 'from', prevStatus, 'to', status);
                        soundsToPlay.push({ type: SOUND_TYPES.PROCESSED, priority: 3, queue: queueKey });
                    }
                    
                } else {
                    // First time seeing this queue
                    // Check if it was just called recently
                    if (isToSubmitStatus(status) && item.is_called && calledAt) {
                        const calledAtTime = new Date(calledAt).getTime();
                        const now = Date.now();
                        const timeDiff = (now - calledAtTime) / 1000;
                        if (timeDiff < 30) {
                            console.log('NEW: Queue just moved to To Submit (recent called_at):', item.queue_number);
                            if (!soundsToPlay.find(s => s.queue === queueKey && s.type === SOUND_TYPES.CALLING)) {
                                soundsToPlay.push({ type: SOUND_TYPES.CALLING, priority: 1, queue: queueKey });
                            }
                        }
                    }
                }
            });
            
            // Play sounds based on priority (highest priority = lowest number)
            // Sort by priority and play the most important sound
            if (soundsToPlay.length > 0) {
                soundsToPlay.sort((a, b) => a.priority - b.priority);
                
                // Get unique sound types to play (avoid duplicate sounds)
                const uniqueSounds = [];
                const playedTypes = new Set();
                
                soundsToPlay.forEach(sound => {
                    if (!playedTypes.has(sound.type)) {
                        uniqueSounds.push(sound);
                        playedTypes.add(sound.type);
                    }
                });
                
                console.log('Sounds to play:', uniqueSounds.map(s => `${s.type} for queue ${s.queue}`));
                
                // Play sounds with staggered timing
                uniqueSounds.forEach((sound, index) => {
                    setTimeout(() => {
                        console.log(`Playing ${sound.type} chime for queue ${sound.queue}`);
                        playChime(sound.type);
                    }, index * 1500); // 1.5 second gap between different sound types
                });
            }
            
            // Update previous called queues set and queue states
            previousCalledQueues = currentCalledQueues;
            previousQueueStates = currentQueueStates;
        }

        // Map department names to their codes
        // Check for duplicates: All codes are unique
        function getDepartmentCode(departmentName) {
            if (!departmentName) return '';
            
            const departmentMap = {
                'Business Permit and Licensing Office': 'BPLO',
                'City Transportation Regulation Unit': 'CTRU',
                'City Engineering Office': 'CEO',
                'Civil Registry Office': 'CRO',
                // Add more departments as needed
            };
            
            // Return the code if found, otherwise generate from department name
            if (departmentMap[departmentName]) {
                return departmentMap[departmentName];
            }
            
            // Fallback: Generate code from department name (first letters of each word)
            const words = departmentName.trim().split(/\s+/);
            if (words.length === 1) {
                return words[0].substring(0, 4).toUpperCase();
            }
            return words.map(word => word.charAt(0).toUpperCase()).join('').substring(0, 4);
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Track previous queue positions for animation
        let previousQueuePositions = new Map(); // queue_number -> column type ('on-queue', 'to-submit', 'processed')

        function renderQueueDisplay(queueData, departmentFromResponse = null, columnNames = null, queuePrefix = null, columnsConfig = null) {
            const container = document.getElementById('queue-display-content');

            // Get department from response, first item, or use the department variable from server
            const department = departmentFromResponse 
                || (queueData.length > 0 && queueData[0].department ? queueData[0].department : null)
                || '{{ $department }}';
            
            // Use provided queue prefix or generate default from department
            const prefix = queuePrefix && queuePrefix.trim() !== '' 
                ? queuePrefix.trim().toUpperCase() 
                : getDepartmentCode(department);
            
            // Use provided columns configuration or fallback to column names or defaults
            let columns = columnsConfig;
            console.log('renderQueueDisplay - columnsConfig:', columnsConfig);
            console.log('renderQueueDisplay - columnNames:', columnNames);
            
            if (!columns || !Array.isArray(columns) || columns.length === 0) {
                console.log('Using fallback columns');
                // Fallback to old columnNames format or defaults
                const cols = columnNames || {
                    on_queue: 'On Queue',
                    to_submit: 'To Submit',
                    processed: 'Processed'
                };
                
                // Convert old format to new format
                columns = [
                    { key: 'on_queue', name: cols.on_queue || 'On Queue', statuses: ['inquiry', 'submitted'] },
                    { key: 'to_submit', name: cols.to_submit || 'To Submit', statuses: ['accepted', 'checklist_given', 'incomplete_requirements', 'rejected'] },
                    { key: 'processed', name: cols.processed || 'Processed', statuses: ['registered', 'completed', 'positive_result', 'negative_result', 'encoded_brap', 'psa_processing'] }
                ];
            }
            
            console.log('Final columns to render:', columns);

            // Categorize items into columns based on status
            const columnData = {};
            columns.forEach(col => {
                columnData[col.key] = [];
            });

            queueData.forEach(item => {
                // Normalize item status: lowercase and trim
                const rawStatus = item.status || '';
                const status = String(rawStatus).toLowerCase().trim();
                console.log('Categorizing item:', item.queue_number, 'status:', status, 'raw status:', rawStatus);
                
                let categorized = false;
                
                // Check each column's statuses to find a match
                for (const col of columns) {
                    if (col.statuses && Array.isArray(col.statuses) && col.statuses.length > 0) {
                        // Normalize statuses for comparison (trim and lowercase)
                        const normalizedStatuses = col.statuses.map(s => {
                            const normalized = String(s).toLowerCase().trim();
                            return normalized;
                        });
                        
                        console.log(`  Checking column "${col.name || col.key}" (key: ${col.key}): statuses=`, normalizedStatuses, 'looking for:', status);
                        
                        // Direct match after normalization
                        if (normalizedStatuses.includes(status)) {
                            console.log(`  ✓ MATCHED to column: ${col.name || col.key} (status: "${status}" matches one of: ${normalizedStatuses.join(', ')})`);
                            columnData[col.key].push(item);
                            categorized = true;
                            break;
                        } else {
                            console.log(`  ✗ No match in column "${col.name || col.key}": "${status}" not in [${normalizedStatuses.join(', ')}]`);
                        }
                    } else {
                        console.warn(`  Column "${col.name || col.key}" has no statuses array or empty statuses`);
                    }
                }
                
                // If no match found, put in first column as fallback
                if (!categorized && columns.length > 0) {
                    console.warn(`  ✗ No match found for status "${status}" (raw: "${rawStatus}"), using fallback column: ${columns[0].name || columns[0].key}`);
                    columnData[columns[0].key].push(item);
                }
            });

            // Sort each column by queue number
            const sortByQueueNumber = (a, b) => {
                const numA = parseInt(a.queue_number) || 0;
                const numB = parseInt(b.queue_number) || 0;
                return numA - numB;
            };

            Object.keys(columnData).forEach(key => {
                columnData[key].sort(sortByQueueNumber);
            });

            // Track current positions for animation detection
            const currentQueuePositions = new Map();
            Object.keys(columnData).forEach(key => {
                columnData[key].forEach(item => {
                    currentQueuePositions.set(item.queue_number, key);
                });
            });

            // Find maximum length to determine table rows
            const maxLength = Math.max(...Object.values(columnData).map(arr => arr.length), 0);

            // Build table rows
            let tableRows = '';
            
            for (let i = 0; i < maxLength; i++) {
                tableRows += '<tr>';
                
                columns.forEach(col => {
                    const items = columnData[col.key] || [];
                    const item = items[i];
                    
                    if (item) {
                        const queueNum = item.queue_number;
                        const prevColumn = previousQueuePositions.get(queueNum);
                        const animClass = prevColumn && prevColumn !== col.key ? 'queue-transition-in' : '';
                        tableRows += `<td class="${col.key}"><span class="queue-number ${animClass}" data-queue-num="${queueNum}">${escapeHtml(prefix)} #${queueNum}</span></td>`;
                    } else {
                        tableRows += `<td class="${col.key} empty-cell"></td>`;
                    }
                });
                
                tableRows += '</tr>';
            }

            // If no data, show empty cells for each column
            if (maxLength === 0) {
                tableRows = '<tr>';
                columns.forEach(col => {
                    tableRows += `<td class="${col.key} empty-cell"></td>`;
                });
                tableRows += '</tr>';
            }
            
            // Build header row with dynamic styling
            let headerRow = '';
            const columnWidth = (100 / columns.length).toFixed(2) + '%';
            
            columns.forEach((col, index) => {
                // Ensure column name is not empty - use key as fallback
                const columnName = col.name && col.name.trim() ? col.name.trim() : (col.key || 'Unnamed Column');
                
                // Apply color classes based on position
                let headerClass = '';
                if (index === 0) {
                    headerClass = 'style="background: linear-gradient(135deg, var(--gray-600) 0%, var(--gray-700) 100%);"';
                } else if (index === columns.length - 1) {
                    headerClass = 'style="background: linear-gradient(135deg, var(--green-500) 0%, var(--green-600) 100%); border-right: none;"';
                } else {
                    headerClass = 'style="background: linear-gradient(135deg, var(--teal-primary) 0%, var(--teal-light) 100%);"';
                }
                headerRow += `<th ${headerClass}>${escapeHtml(columnName)}</th>`;
            });
            
            container.innerHTML = `
                <div class="queue-table-container">
                    <table class="queue-table" style="table-layout: fixed; width: 100%;">
                        <colgroup>
                            ${columns.map(() => `<col style="width: ${columnWidth};">`).join('')}
                        </colgroup>
                        <thead>
                            <tr>
                                ${headerRow}
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRows}
                        </tbody>
                    </table>
                </div>
            `;

            // Update previous positions for next render
            previousQueuePositions = currentQueuePositions;

            // Remove animation class after animation completes (5 seconds)
            setTimeout(() => {
                const animatedElements = container.querySelectorAll('.queue-transition-in');
                animatedElements.forEach(el => {
                    el.classList.remove('queue-transition-in');
                });
            }, 5000);
        }

        // Initialize audio on page load
        initAudio();
        
        // Enable audio on any user interaction (required by browser security)
        document.addEventListener('click', function() {
            if (audioContext && audioContext.state === 'suspended') {
                audioContext.resume().then(() => {
                    console.log('Audio enabled by user interaction');
                });
            }
        }, { once: true });
        
        // Also enable on touch/scroll
        document.addEventListener('touchstart', function() {
            if (audioContext && audioContext.state === 'suspended') {
                audioContext.resume();
            }
        }, { once: true });
        
        // Load initial data immediately to sync display names
        loadQueueDisplay();

        // Auto-refresh every 10 seconds
        setInterval(loadQueueDisplay, 10000);
        
        // Also update display names on initial load (in case settings changed)
        // This ensures the page shows the latest names even before first data fetch
        setTimeout(() => {
            loadQueueDisplay();
        }, 500);
    </script>
</body>
</html>
