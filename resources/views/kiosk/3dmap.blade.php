<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="description" content="3D Interactive Wayfinding Kiosk - Find your way around the municipal building">
  <meta name="robots" content="noindex, nofollow">

  <!-- Security Headers -->
  <meta http-equiv="Content-Security-Policy"
    content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://www.gstatic.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: blob:; connect-src 'self' {{ url('/api') }};">
  <meta http-equiv="X-Content-Type-Options" content="nosniff">
  <meta http-equiv="X-XSS-Protection" content="1; mode=block">

  <title>3D Interactive Wayfinding - {{ config('app.name', 'Click2Serve') }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Include the styles from standalone index.html -->
  <link href="{{ asset('kiosk/styles.css') }}" rel="stylesheet">

  <style>
    :root {
      --teal-700: #0e90a6;
      --teal-800: #087a93;
      --teal-900: #0a6a80;
      --card-shadow: 0 15px 35px rgba(0, 0, 0, .18);
      --inner-shadow: inset 0 0 0 2px rgba(255, 255, 255, .25), inset 0 12px 24px rgba(255, 255, 255, .06);
      --input-border: #e2e8f0;
      --input-focus: #0e90a6;
      --text-primary: #1a202c;
      --text-secondary: #4a5568;
      --text-muted: #718096;
      --form-bg: #ffffff;
      --input-bg: #f8f9fa;
      --gradient-bg: linear-gradient(180deg, #0e90a6 0%, #087a93 50%, #0a6a80 100%);
    }

    html,
    body,
    #app {
      height: 100%;
      margin: 0;
      overflow: hidden;
      font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;
      background: var(--gradient-bg);
      background-image: url('{{ asset('kiosk/assets/bg.png') }}');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
      color: var(--text-primary);
      position: relative;
      /* Kiosk optimizations */
      -webkit-touch-callout: none;
      -webkit-user-select: none;
      -khtml-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      -webkit-tap-highlight-color: transparent;
      touch-action: manipulation;
    }

    /* Background pattern */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image:
        linear-gradient(45deg, rgba(255, 255, 255, 0.1) 25%, transparent 25%),
        linear-gradient(-45deg, rgba(255, 255, 255, 0.1) 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, rgba(255, 255, 255, 0.1) 75%),
        linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, 0.1) 75%);
      background-size: 60px 60px;
      background-position: 0 0, 0 30px, 30px -30px, -30px 0px;
      opacity: 0.3;
      z-index: -1;
    }

    #ui {
      position: absolute;
      top: 15px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 10;
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      width: 1140px;
      max-width: calc(100vw - 40px);
      box-sizing: border-box;
      overflow: visible;
      pointer-events: none;
      padding: 0 10px;
    }

    #ui>* {
      pointer-events: auto;
    }

    /* Header sections matching services.css - Optimized */
    #ui .header-left {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-shrink: 0;
      min-width: auto;
      justify-content: flex-start;
      margin-right: 12px;
    }

    #ui .header-center {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 12px;
      min-width: 0;
      padding: 0 10px;
      flex-wrap: nowrap;
      overflow: hidden;
    }

    #ui .header-right {
      display: flex;
      align-items: center;
      gap: 0;
      flex-shrink: 1;
      min-width: 0;
      justify-content: flex-end;
      margin-left: 12px;
    }

    /* Modern capsule-shaped controls - Optimized */
    .nav-control {
      display: flex;
      align-items: center;
      background: var(--form-bg);
      border: 2px solid var(--input-border);
      border-radius: 25px;
      padding: 0;
      backdrop-filter: blur(10px);
      box-shadow: var(--card-shadow);
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
      flex-shrink: 1;
      min-width: 200px;
      max-width: 380px;
      width: auto;
      height: 48px;
      overflow: visible;
      /* Prevent vibration and ensure stability */
      transform: translateZ(0);
      will-change: auto;
      /* Additional stability fixes */
      -webkit-transform: translateZ(0);
      -webkit-backface-visibility: hidden;
      -webkit-perspective: 1000;
      backface-visibility: hidden;
      perspective: 1000px;
    }

    /* Responsive nav-control sizing for center section - Optimized */
    #ui .header-center .nav-control {
      min-width: 180px;
      max-width: 280px;
      flex: 1 1 auto;
    }

    /* First nav-control in center (floor selector) */
    #ui .header-center .nav-control:first-child {
      min-width: 200px;
      max-width: 280px;
      flex: 1 1 auto;
    }

    /* Second nav-control in center (zoom + destination) */
    #ui .header-center .nav-control:last-child {
      min-width: 240px;
      max-width: 340px;
      flex: 1.2 1 auto;
    }

    /* Ensure destination select in zoom-controls has enough space */
    #ui .header-center .zoom-controls .destination-select {
      min-width: 180px;
      flex: 1 1 auto;
      width: auto;
    }

    /* Search control in header-right - Optimized */
    #ui .header-right .nav-control {
      min-width: 200px;
      max-width: 300px;
      flex: 0 1 auto;
    }

    .nav-control:hover {
      border-color: var(--input-focus);
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
      transform: translateZ(0) !important;
      -webkit-transform: translateZ(0) !important;
    }

    /* Zoom controls */
    .zoom-controls {
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .zoom-btn {
      width: 32px;
      height: 32px;
      background: transparent;
      border: none;
      color: var(--teal-700);
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      border-radius: 16px;
    }

    .zoom-btn:hover {
      background: var(--input-bg);
      transform: scale(1.1);
      color: var(--teal-800);
    }

    .zoom-btn:active {
      transform: scale(0.95);
      background: var(--input-border);
    }

    /* Destination select */
    .destination-select {
      background: transparent;
      border: none;
      color: var(--text-primary);
      font-size: 14px;
      padding: 12px 15px;
      padding-right: 40px;
      outline: none;
      cursor: pointer;
      flex: 1;
      min-width: 0;
      width: 100%;
      max-width: none;
      font-family: 'Poppins', sans-serif;
      /* Kiosk touch optimizations */
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      border-radius: 0;
      min-height: 44px;
      /* Minimum touch target size */
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      /* Prevent vibration and ensure stability */
      transform: translateZ(0);
      will-change: auto;
      transition: none;
      /* Additional stability fixes */
      -webkit-transform: translateZ(0);
      -webkit-backface-visibility: hidden;
      -webkit-perspective: 1000;
      backface-visibility: hidden;
      perspective: 1000px;
      /* Custom dropdown arrow for better touch */
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230e90a6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 12px center;
      background-size: 16px;
    }

    .destination-select option:first-child {
      white-space: normal;
    }

    .destination-select option {
      background: var(--form-bg);
      color: var(--text-primary);
      padding: 10px;
    }

    .destination-select:active,
    .destination-select:focus {
      transform: translateZ(0) !important;
      -webkit-transform: translateZ(0) !important;
    }

    @media (hover: none) and (pointer: coarse) {
      .destination-select {
        -webkit-tap-highlight-color: transparent;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }
    }

    /* Back button container - Optimized */
    .back-container {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-shrink: 0;
      height: 48px;
    }

    /* Back button */
    .back-button {
      background: rgba(255, 255, 255, 0.2);
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      width: 48px;
      height: 48px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: white;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
      /* Prevent vibration and ensure stability */
      transform: translateZ(0);
      will-change: auto;
      -webkit-transform: translateZ(0);
      -webkit-backface-visibility: hidden;
      -webkit-perspective: 1000;
      backface-visibility: hidden;
      perspective: 1000px;
    }

    .back-button:hover {
      background: rgba(255, 255, 255, 0.3);
      border-color: rgba(255, 255, 255, 0.5);
      transform: translateX(-2px) translateZ(0) !important;
      -webkit-transform: translateX(-2px) translateZ(0) !important;
    }

    .back-button:active {
      transform: translateZ(0) scale(0.95) !important;
      -webkit-transform: translateZ(0) scale(0.95) !important;
    }

    .back-text {
      color: white;
      font-weight: 500;
      font-size: 16px;
      cursor: default;
      user-select: none;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      line-height: 1;
      display: flex;
      align-items: center;
      height: 48px;
    }


    /* Search control */
    .search-control {
      display: flex;
      align-items: center;
      position: relative;
      overflow: visible;
    }

    .search-input {
      flex: 1;
      background: transparent;
      border: none;
      color: var(--text-primary);
      font-size: 14px;
      padding: 12px 15px;
      outline: none;
      min-width: 0;
      font-family: 'Poppins', sans-serif;
      /* Kiosk touch optimizations */
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      border-radius: 0;
      min-height: 44px;
      /* Minimum touch target size */
      overflow: visible;
    }

    .search-input::placeholder {
      color: var(--text-muted);
    }

    .search-input:focus {
      color: var(--text-primary);
      outline: 2px solid var(--input-focus);
      outline-offset: 2px;
    }

    .destination-select:focus {
      outline: none;
      border-color: var(--input-focus);
      box-shadow: 0 0 0 3px rgba(14, 144, 166, 0.1);
      /* Prevent vibration on focus */
      transform: translateZ(0);
    }

    .nav-control:focus-within {
      border-color: var(--input-focus);
      box-shadow: 0 0 0 3px rgba(14, 144, 166, 0.1), var(--card-shadow);
      /* Prevent vibration on focus */
      transform: translateZ(0) !important;
      -webkit-transform: translateZ(0) !important;
    }

    /* Ensure consistent focus styling for all inputs */
    .destination-select:focus,
    .search-input:focus {
      outline: none !important;
      border: none !important;
    }

    /* POI List */
    #poi-list {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      max-height: 300px;
      overflow-y: auto;
      background: #ffffff;
      border: 2px solid var(--teal-700);
      border-radius: 15px;
      padding: 0;
      margin-top: 5px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      z-index: 1001;
      transform: translateZ(0);
      will-change: auto;
      display: none;
      /* Smooth scrolling for touch devices */
      -webkit-overflow-scrolling: touch;
      scroll-behavior: smooth;
      /* Better momentum scrolling */
      overscroll-behavior: contain;
    }

    #poi-list.show {
      display: block;
    }

    .poi-item {
      padding: 15px 20px;
      color: var(--text-primary);
      cursor: pointer;
      border-radius: 0;
      margin-bottom: 0;
      transition: background-color 0.2s ease;
      border: none;
      border-bottom: 1px solid #f0f0f0;
      font-weight: 600;
      font-size: 15px;
      line-height: 1.4;
      min-height: 44px;
      min-width: 44px;
      /* Improve touch responsiveness */
      -webkit-tap-highlight-color: rgba(14, 144, 166, 0.2);
      -webkit-touch-callout: none;
      -webkit-user-select: none;
      -khtml-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      /* Smooth transitions for touch feedback */
      transition: all 0.15s ease;
    }

    .poi-item:hover {
      background: #f8f9fa;
    }

    .poi-item:first-child {
      padding-top: 20px;
    }

    .poi-item:last-child {
      margin-bottom: 0;
      border-bottom: none;
      padding-bottom: 20px;
    }

    /* Canvas container */
    #canvas-container {
      width: 100%;
      height: calc(100vh - 80px);
      position: absolute;
      top: 80px;
      left: 0;
      z-index: 1;
    }

    /* Scrollbar styling */
    #poi-list::-webkit-scrollbar {
      width: 8px;
    }

    #poi-list::-webkit-scrollbar-track {
      background: var(--input-bg);
      border-radius: 4px;
    }

    #poi-list::-webkit-scrollbar-thumb {
      background: var(--teal-700);
      border-radius: 4px;
    }

    #poi-list::-webkit-scrollbar-thumb:hover {
      background: var(--teal-800);
    }

    /* Touch gesture improvements */
    body {
      /* Prevent pull-to-refresh on mobile */
      overscroll-behavior-y: contain;
      /* Better touch scrolling */
      -webkit-overflow-scrolling: touch;
    }

    /* Enhanced focus indicators for touch */
    .destination-select:focus,
    .search-input:focus {
      /* Larger focus ring for touch devices */
      outline: 3px solid var(--input-focus);
      outline-offset: 2px;
      /* Visual feedback */
      box-shadow: 0 0 0 6px rgba(14, 144, 166, 0.1);
    }
  </style>
</head>

<body>
  <div id="app">
    <div id="ui">
      <!-- Header Left: Back Button -->
      <div class="header-left">
        <div class="back-container">
          <button class="back-button" onclick="goBack()" title="Back to Map">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
            </svg>
          </button>
          <span class="back-text">Map</span>
        </div>
      </div>

      <!-- Header Center: Floor Selection and Navigation Controls -->
      <div class="header-center">
        <!-- Floor Selection Control -->
        <div class="nav-control">
          <select id="floor-select" class="destination-select">
            <option value="">Loading floors...</option>
          </select>
        </div>

        <!-- Navigation Control with Zoom and Destination -->
        <div class="nav-control zoom-controls">
          <button class="zoom-btn" id="zoom-in" title="Zoom In">+</button>
          <button class="zoom-btn" id="zoom-out" title="Zoom Out">−</button>
          <select id="location-select" class="destination-select">
            <option value="">Input Destination</option>
          </select>
        </div>
      </div>

      <!-- Header Right: Search Control -->
      <div class="header-right">
        <div class="nav-control search-control">
          <input id="search" class="search-input" placeholder="Search POIs..." autocomplete="off" />

          <!-- POI List -->
          <div id="poi-list"></div>
        </div>
      </div>
    </div>
    <div id="canvas-container"></div>
  </div>

  <!-- Three.js Import Map -->
  <script type="importmap">
  {
    "imports": {
      "three": "https://unpkg.com/three@0.149.0/build/three.module.js"
    }
  }
  </script>
  <script type="module">
    import * as THREE_MOD from 'three';
    import { GLTFLoader } from 'https://unpkg.com/three@0.149.0/examples/jsm/loaders/GLTFLoader.js';
    import { DRACOLoader } from 'https://unpkg.com/three@0.149.0/examples/jsm/loaders/DRACOLoader.js';
    import { OrbitControls } from 'https://unpkg.com/three@0.149.0/examples/jsm/controls/OrbitControls.js';
    window.THREE = window.THREE || THREE_MOD; // expose module build for loaders
    window.GLTFLoader = GLTFLoader;
    window.DRACOLoader = DRACOLoader;
    window.OrbitControls = OrbitControls;
  </script>
  <script>
    // Kiosk GLB debug/override flags
    // window.KIOSK_GLB_PATH = '{{ asset('kiosk/assets/map.glb') }}'; // Commented out to allow floor-specific GLB files
    window.KIOSK_FORCE_GLB = true;
    window.KIOSK_SHOW_BBOX = false;
    window.KIOSK_GLB_SCALE = 1.0; // adjust if model is too big/small
    window.KIOSK_TARGET_WIDTH = 2000;  // auto-scale model to ~2000 units wide
    window.KIOSK_TARGET_HEIGHT = 2000; // and ~2000 units tall

    // Back button functionality
    function goBack() {
      window.location.href = '/';
    }
  </script>
  <script src="{{ asset('kiosk/main.js') }}"></script>
</body>

</html>