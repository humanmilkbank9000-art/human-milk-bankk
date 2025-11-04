@extends('layouts.user-layout')

@section('title', 'User Dashboard')

@section('pageTitle', 'Welcome, ' . ($user->first_name ?? 'User'))

@section('content')
  @php
    use App\Helpers\UserBadgeHelper;

    // Prepare stats for badge calculation
    $userStats = [
      'totalDonations' => $totalDonations ?? 0,
      'totalVolumeDonated' => $totalVolumeDonated ?? 0,
      'infantsHelped' => $infantsHelped ?? 0,
    ];

    // Get badges
    $badges = UserBadgeHelper::getBadges($userStats);
    $motivationalMessage = UserBadgeHelper::getMotivationalMessage($userStats);

    // Get badge for each card
    $donationsBadge = UserBadgeHelper::getBadgeForCard('donations', $userStats);
    $volumeBadge = UserBadgeHelper::getBadgeForCard('volume', $userStats);
    $infantsBadge = UserBadgeHelper::getBadgeForCard('infants', $userStats);
  @endphp

  <style>
    /* Facebook Widget Container - Fixed on Desktop, Scrollable on Mobile */
    .fb-share-wrapper {
      position: fixed;
      right: 16px;
      bottom: 16px;
      z-index: 50;
      /* Below modals */
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* Mobile: Make Facebook widgets part of scrollable content */
    @media (max-width: 768px) {
      .fb-share-wrapper {
        position: static !important;
        /* Remove fixed positioning */
        margin-top: 2rem;
        margin-bottom: 1rem;
        justify-content: center;
        flex-wrap: wrap;
      }

      /* Remove body padding since widgets are now in flow */
      body {
        padding-bottom: 0 !important;
      }
    }

    /* Desktop: Keep padding for fixed widgets */
    @media (min-width: 769px) {
      body {
        padding-bottom: 80px !important;
      }
    }

  /* Dashboard Statistics Cards - Refreshed non-pink gradients to complement theme */
    .stats-container {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-bottom: 1.25rem;
    }

    .stat-card {
      background: linear-gradient(135deg, #6366f1, #4f46e5); /* indigo base */
      border-radius: 0.75rem;
      padding: 1rem;
      color: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 140px;
      position: relative;
      overflow: visible;
      /* Changed from hidden to visible for badges */
    }

    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 22px rgba(79, 70, 229, 0.18);
    }

    /* Per-card tinted hover + focus for accessibility */
    .stat-card.donations:hover { box-shadow: 0 8px 22px rgba(37, 99, 235, 0.28); }
    .stat-card.volume:hover    { box-shadow: 0 8px 22px rgba(232, 62, 140, 0.28); }
    .stat-card.infants:hover   { box-shadow: 0 8px 22px rgba(22, 163, 74, 0.28); }
    .stat-card:focus-visible   { outline: 3px solid var(--blue-400); outline-offset: 2px; }
    .stat-card.donations:focus-visible { outline-color: var(--blue-400); }
    .stat-card.volume:focus-visible    { outline-color: var(--pink-400); }
    .stat-card.infants:focus-visible   { outline-color: var(--green-400); }

  /* Align with admin palette: blue, pink, green */
  .stat-card.donations { background: var(--blue-600); } /* blue */
  .stat-card.volume    { background: var(--pink-600); } /* pink */
  .stat-card.infants   { background: var(--green-600); } /* green */

    .stat-card-icon {
      font-size: 1.8rem;
      margin-bottom: 0.25rem;
      opacity: 0.9;
    }

    .stat-card-title {
      font-size: 0.8rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 0.25rem;
      opacity: 0.95;
    }

    .stat-card-value {
      font-size: 2rem;
      font-weight: 700;
      line-height: 1;
      margin-bottom: 0.15rem;
    }

    .stat-card-subtitle {
      font-size: 0.75rem;
      opacity: 0.85;
    }

    /* Badge Styling - Fixed for Mobile */
    .badge-container {
      position: absolute;
      top: 0.5rem;
      right: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.4rem;
      background: rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(10px);
      padding: 0.35rem 0.6rem;
      border-radius: 20px;
      font-size: 0.7rem;
      font-weight: 600;
      animation: badgePulse 2s ease-in-out infinite;
      white-space: nowrap;
      max-width: calc(100% - 1rem);
      /* Prevent overflow */
    }

    .badge-icon {
      font-size: 0.9rem;
      animation: badgeSpin 3s ease-in-out infinite;
      flex-shrink: 0;
    }

    .badge-text {
      overflow: hidden;
      text-overflow: ellipsis;
    }

    @keyframes badgePulse {

      0%,
      100% {
        transform: scale(1);
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
      }

      50% {
        transform: scale(1.05);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
      }
    }

    @keyframes badgeSpin {

      0%,
      100% {
        transform: rotate(0deg);
      }

      25% {
        transform: rotate(-10deg);
      }

      75% {
        transform: rotate(10deg);
      }
    }

    /* Motivational Message - polished, subtle gradient with accent and icon pill */
    .motivational-message {
      background: linear-gradient(135deg, #f0f9ff 0%, #eef2ff 100%);
      border: 1px solid rgba(37, 99, 235, 0.12);
      border-left: 5px solid #2563eb;
      padding: 0.9rem 1.1rem;
      border-radius: 12px;
      margin-bottom: 1.25rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      box-shadow: 0 6px 16px rgba(37, 99, 235, 0.06);
    }

    .motivational-icon {
      width: 32px;
      height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 9999px;
      background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      border: 1px solid rgba(37, 99, 235, 0.15);
      color: #2563eb;
      box-shadow: 0 2px 6px rgba(37, 99, 235, 0.10);
      flex-shrink: 0;
    }

    .motivational-icon i { font-size: 1.05rem; line-height: 1; }

    .motivational-text {
      font-size: 0.98rem;
      font-weight: 600;
      color: #0f172a;
      margin: 0;
      letter-spacing: 0.2px;
    }

    /* Responsive adjustments - maintain horizontal layout */
    @media (max-width: 768px) {
      .stats-container {
        gap: 0.75rem;
      }

      .stat-card {
        min-height: 120px;
        padding: 0.85rem 0.75rem 1rem 0.75rem;
        /* More top padding for badge */
      }

      .stat-card-icon {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
      }

      .stat-card-title {
        font-size: 0.6rem;
        margin-bottom: 0.25rem;
        padding-right: 0;
        /* Remove padding to avoid badge overlap */
      }

      .stat-card-value {
        font-size: 1.5rem;
      }

      .stat-card-subtitle {
        font-size: 0.6rem;
        padding-right: 0;
      }

      /* Mobile Badge Styling - Smaller and positioned better */
      .badge-container {
        position: static;
        /* Remove absolute positioning on mobile */
        display: inline-flex;
        margin-bottom: 0.5rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.55rem;
        border-radius: 12px;
        align-self: flex-start;
      }

      .badge-icon {
        font-size: 0.7rem;
      }

      .motivational-message {
        padding: 0.75rem 1rem;
      }

      .motivational-icon {
        font-size: 1.25rem;
      }

      .motivational-text {
        font-size: 0.85rem;
      }
    }

    @media (min-width: 769px) and (max-width: 1024px) {
      .stats-container {
        gap: 1rem;
      }

      .stat-card {
        min-height: 130px;
        padding: 1.25rem;
      }

      .stat-card-icon {
        font-size: 2rem;
      }

      .stat-card-title {
        font-size: 0.75rem;
      }

      .stat-card-value {
        font-size: 2rem;
      }

      .stat-card-subtitle {
        font-size: 0.75rem;
      }
    }
  </style>

  <!-- Motivational Message -->
  <div class="motivational-message">
    <div class="motivational-icon">
      <i class="fas fa-lightbulb"></i>
    </div>
    <p class="motivational-text">{{ $motivationalMessage }}</p>
  </div>

  <!-- Dashboard Statistics Cards -->
  <div class="stats-container">
    <!-- Total Donations Card -->
    <div class="stat-card donations">
      @if($donationsBadge)
        <div class="badge-container" data-bs-toggle="tooltip" title="{{ $donationsBadge['message'] }}">
          <i class="{{ $donationsBadge['icon'] }} badge-icon"></i>
          <span class="badge-text">{{ $donationsBadge['name'] }}</span>
        </div>
      @endif
      <div>
        <div class="stat-card-icon"><i class="fas fa-hand-holding-heart"></i></div>
        <div class="stat-card-title">Total Donations</div>
      </div>
      <div>
        <div class="stat-card-value">{{ $totalDonations ?? 0 }}</div>
        <div class="stat-card-subtitle">Successful donations made</div>
      </div>
    </div>

    <!-- Total Volume Donated Card -->
    <div class="stat-card volume">
      @if($volumeBadge)
        <div class="badge-container" data-bs-toggle="tooltip" title="{{ $volumeBadge['message'] }}">
          <i class="{{ $volumeBadge['icon'] }} badge-icon"></i>
          <span class="badge-text">{{ $volumeBadge['name'] }}</span>
        </div>
      @endif
      <div>
        <div class="stat-card-icon"><i class="fas fa-tint"></i></div>
        <div class="stat-card-title">Total Volume Donated</div>
      </div>
      <div>
        <div class="stat-card-value">{{ number_format($totalVolumeDonated ?? 0, 0) }}</div>
        <div class="stat-card-subtitle">mL of life-giving milk</div>
      </div>
    </div>

    <!-- Infants Helped Card -->
    <div class="stat-card infants">
      @if($infantsBadge)
        <div class="badge-container" data-bs-toggle="tooltip" title="{{ $infantsBadge['message'] }}">
          <i class="{{ $infantsBadge['icon'] }} badge-icon"></i>
          <span class="badge-text">{{ $infantsBadge['name'] }}</span>
        </div>
      @endif
      <div>
        <div class="stat-card-icon"><i class="fas fa-baby"></i></div>
        <div class="stat-card-title">Infants Helped</div>
      </div>
      <div>
        <div class="stat-card-value">{{ $infantsHelped ?? 0 }}</div>
        <div class="stat-card-subtitle">Precious lives touched</div>
      </div>
    </div>
  </div>

  <!-- FAQ Section -->
  @include('partials.faq-section')

  <!-- Nutritional Guide & Tips Section -->
  @include('partials.nutritional-guide-section')

  <!-- Facebook support and share buttons - Fixed position on desktop, scrollable on mobile -->
  <div id="fb-share-container" aria-hidden="false" class="fb-share-wrapper">

    <!-- Follow Us link (existing) -->
    <a id="fb-support" href="https://web.facebook.com/CDOHMBLSC" target="_blank" rel="noopener noreferrer"
      style="display:flex;align-items:center;color:#222;text-decoration:none;padding:8px 10px;border-radius:10px;background:rgba(255,255,255,0.9);box-shadow:0 6px 12px rgba(99,102,241,0.06);font-size:14px;">
      <span style="color:#222;margin-right:8px;">Please follow and support us on Facebook</span>
      <!-- Inline Facebook SVG icon -->
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path
          d="M22 12.07C22 6.48 17.52 2 12 2S2 6.48 2 12.07C2 17.09 5.66 21.26 10.44 22v-7.03H7.9v-2.9h2.54V9.41c0-2.5 1.49-3.88 3.77-3.88 1.09 0 2.23.2 2.23.2v2.45h-1.25c-1.23 0-1.61.77-1.61 1.56v1.88h2.74l-.44 2.9h-2.3V22C18.34 21.26 22 17.09 22 12.07z"
          fill="#1877F2" />
      </svg>
    </a>

    <!-- Share Buttons -->
    <div id="fb-share-buttons" role="group" aria-label="Facebook share actions"
      style="display:flex;gap:8px;align-items:center;">

      <!-- Share to Timeline -->
      <button id="share-timeline" class="fb-btn fb-timeline has-tooltip" type="button"
        aria-label="Share on Facebook Timeline" data-tooltip="Share on Facebook Timeline">
        <!-- FB icon -->
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path
            d="M22 12.07C22 6.48 17.52 2 12 2S2 6.48 2 12.07C2 17.09 5.66 21.26 10.44 22v-7.03H7.9v-2.9h2.54V9.41c0-2.5 1.49-3.88 3.77-3.88 1.09 0 2.23.2 2.23.2v2.45h-1.25c-1.23 0-1.61.77-1.61 1.56v1.88h2.74l-.44 2.9h-2.3V22C18.34 21.26 22 17.09 22 12.07z"
            fill="#fff" />
        </svg>
        <span class="btn-label">Share</span>
      </button>

      <!-- Send via Messenger -->
      <button id="share-messenger" class="fb-btn fb-messenger has-tooltip" type="button"
        aria-label="Send via Facebook Messenger" data-tooltip="Send via Facebook Messenger">
        <!-- Messenger icon -->
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path
            d="M12.004 2C6.477 2 2 6.477 2 12c0 2.657 1.01 5.09 2.684 6.927V22l3.038-1.663C9.238 21.642 10.6 22 12.004 22c5.527 0 10.004-4.477 10.004-10S17.531 2 12.004 2z"
            fill="#0078FF" />
          <path d="M10.23 14.274l-2.15-2.285 4.472-4.225 2.15 2.284 2.87-2.137-4.472 4.224-2.87 2.139z" fill="#fff" />
        </svg>
        <span class="btn-label">Messenger</span>
      </button>

    </div>

  </div>

  <!-- Styles for pastel themed, accessible buttons, tooltips, hover, ripple and responsiveness -->
  <style>
    /* Base button */
    .fb-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: 0;
      padding: 8px 12px;
      border-radius: 12px;
      cursor: pointer;
      font-weight: 600;
      font-size: 14px;
      box-shadow: 0 6px 16px rgba(16, 24, 40, 0.04);
      transition: transform .14s ease, box-shadow .14s ease, opacity .14s ease;
      position: relative;
      overflow: hidden;
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.9));
      color: #111;
    }

    .fb-btn svg {
      flex: 0 0 auto
    }

    .fb-btn .btn-label {
      display: inline-block
    }

    /* Facebook style */
    .fb-timeline {
      background: linear-gradient(180deg, #E8F0FF, #DDE9FF);
      color: #0f172a
    }

    .fb-timeline svg path {
      fill: #1877F2
    }

    /* Messenger style: soft blue gradient */
    .fb-messenger {
      background: linear-gradient(180deg, #EAF4FF, #DDEBFF);
      color: #042A5A
    }

    /* Hover and focus */
    .fb-btn:hover,
    .fb-btn:focus {
      transform: translateY(-3px);
      box-shadow: 0 12px 24px rgba(99, 102, 241, 0.08);
      outline: none
    }

    /* Tooltip */
    .has-tooltip[data-tooltip] {
      position: relative
    }

    .has-tooltip[data-tooltip]::after {
      content: attr(data-tooltip);
      position: absolute;
      left: 50%;
      transform: translateX(-50%) translateY(-8px);
      bottom: calc(100% + 8px);
      background: #111;
      color: #fff;
      padding: 6px 8px;
      border-radius: 6px;
      font-size: 12px;
      white-space: nowrap;
      opacity: 0;
      pointer-events: none;
      transition: opacity .12s ease, transform .12s ease
    }

    .has-tooltip:hover::after,
    .has-tooltip:focus::after {
      opacity: 1;
      transform: translateX(-50%) translateY(-12px)
    }

    /* Ripple */
    .fb-btn .ripple {
      position: absolute;
      border-radius: 9999px;
      transform: scale(0);
      opacity: .5;
      background: rgba(255, 255, 255, 0.6);
      animation: ripple .6s linear
    }

    @keyframes ripple {
      to {
        transform: scale(6);
        opacity: 0
      }
    }
  </style>

  <!-- JS: load FB SDK gracefully, wire buttons with fallback share links, and add ripple effect -->
  <script>
    (function () {
      const pageUrl = window.location.href;
      let FB_LOADED = false;
      let FB_FAILED = false;

      function loadFacebookSDK(timeout = 3000) {
        return new Promise((resolve) => {
          if (window.FB) { FB_LOADED = true; return resolve(true); }
          const s = document.createElement('script');
          s.src = 'https://connect.facebook.net/en_US/sdk.js';
          s.async = true;
          s.onload = function () { FB_LOADED = true; resolve(true); };
          s.onerror = function () { FB_FAILED = true; resolve(false); };
          document.head.appendChild(s);
          setTimeout(() => { if (!FB_LOADED) { FB_FAILED = true; resolve(false); } }, timeout);
        });
      }

      function shareTimeline() {
        if (window.FB && typeof FB.ui === 'function') {
          try { FB.ui({ method: 'share', href: pageUrl }); return; } catch (e) { }
        }
        // fallback to sharer link
        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(pageUrl), 'fbshare', 'width=600,height=500');
      }

      function shareMessenger() {
        if (window.FB && typeof FB.ui === 'function') {
          try { FB.ui({ method: 'send', link: pageUrl }); return; } catch (e) { }
        }
        // Try messenger deep link on mobile
        const isMobile = /Mobi|Android/i.test(navigator.userAgent);
        const messengerDeep = 'fb-messenger://share?link=' + encodeURIComponent(pageUrl);
        if (isMobile) { window.location.href = messengerDeep; return; }
        // fallback: open sharer as a simple fallback so user can still share/copy
        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(pageUrl), 'fbshare', 'width=600,height=500');
      }

      function createRipple(elem, ev) {
        const rect = elem.getBoundingClientRect();
        const ripple = document.createElement('span');
        ripple.className = 'ripple';
        const size = Math.max(rect.width, rect.height) * 1.2;
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = (ev.clientX - rect.left - size / 2) + 'px';
        ripple.style.top = (ev.clientY - rect.top - size / 2) + 'px';
        elem.appendChild(ripple);
        setTimeout(() => ripple.remove(), 700);
      }

      // Wire UI
      document.addEventListener('DOMContentLoaded', function () {
        const btnTimeline = document.getElementById('share-timeline');
        const btnMessenger = document.getElementById('share-messenger');

        if (btnTimeline) {
          btnTimeline.addEventListener('click', function (ev) { createRipple(this, ev); shareTimeline(); });
        }
        if (btnMessenger) {
          btnMessenger.addEventListener('click', function (ev) { createRipple(this, ev); shareMessenger(); });
        }

        // Attempt to load SDK but don't block UI
        loadFacebookSDK().then(function (ok) {
          // if loaded, optionally initialize - we avoid forcing an appId here; FB.ui may still work for share
          if (ok && window.FB && typeof FB.init === 'function') {
            try { FB.init({ version: 'v11.0' }); } catch (e) { }
          }
        });
      });
    })();
  </script>
@endsection