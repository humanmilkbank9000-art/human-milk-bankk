@php
    use App\Http\Controllers\NutritionalGuideController;
    $guideItems = NutritionalGuideController::getNutritionalGuideItems();
    $guideByCategory = NutritionalGuideController::getGuideByCategory();
@endphp

<!-- Nutritional Guide Section Styles -->
<style>
    /* Nutritional Guide Section Container - Matches stats-container */
    .nutritional-guide-section {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    /* Nutritional Guide Card - Blue to complement FAQ pink */
    .nutritional-guide-card {
        background: linear-gradient(135deg, var(--blue-400) 0%, var(--blue-600) 100%);
        border-radius: 0.75rem;
        padding: 1rem;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
        color: #ffffff;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .nutritional-guide-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.0);
        transition: background 0.3s ease;
        pointer-events: none;
    }

    .nutritional-guide-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
    }
    .nutritional-guide-card:focus-visible { outline: 3px solid var(--blue-400); outline-offset: 2px; }

    .nutritional-guide-card:hover::before {
        background: rgba(255, 255, 255, 0.1);
    }

    .nutritional-guide-card:active {
        transform: translateY(-2px);
    }

    .nutritional-guide-card-icon {
        font-size: 1.8rem;
        margin-bottom: 0.25rem;
        opacity: 0.95;
        color: rgba(255,255,255,0.95);
    }

    .nutritional-guide-card-title {
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
        opacity: 0.98;
        color: #ffffff;
    }

    .nutritional-guide-card-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.15rem;
        color: #ffffff;
    }

    .nutritional-guide-card-subtitle {
        font-size: 0.75rem;
        opacity: 0.95;
        color: rgba(255,255,255,0.95);
    }

    /* Clickable hint */
    .nutritional-guide-card .click-hint {
        position: absolute;
        right: 12px;
        bottom: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        color: #ffffff;
        background: rgba(255,255,255,0.16);
        padding: 6px 10px;
        border-radius: 9999px;
        border: 1px solid rgba(255,255,255,0.28);
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        user-select: none;
        pointer-events: none; /* keep click target as the whole card */
    }

    /* Modal Overlay */
    .guide-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9998;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .guide-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    /* Modal Container */
    .guide-modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        width: 90%;
        max-width: 700px;
        max-height: 85vh;
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .guide-modal.active {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, -50%) scale(1);
    }

    /* Modal Header */
    .guide-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    .guide-modal-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .guide-modal-title i {
        color: #a8edea;
    }

    .guide-modal-close {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f3f4f6;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #6b7280;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .guide-modal-close:hover {
        background: #e5e7eb;
        color: #374151;
        transform: rotate(90deg);
    }

    /* Modal Body - Swiper Container */
    .guide-modal-body {
        flex: 1;
        /* Let the modal body handle vertical scrolling */
        overflow-y: auto;
        overflow-x: hidden;
        position: relative;
        /* Allow natural touch scrolling on the modal body */
        touch-action: pan-y;
        -webkit-overflow-scrolling: touch;
    }

    .guide-swiper-container {
        width: 100%;
        height: 100%;
        /* Allow horizontal touch gestures inside the swiper area while keeping vertical pan on the modal body */
        touch-action: pan-x;
    }

    .guide-swiper-slide {
        padding: 1.5rem;
        /* Slides should not create their own vertical scroll — modal body scrolls */
        overflow: visible;
        overscroll-behavior: contain;
    }

    /* Prevent body scroll when modal is open */
    body.modal-open {
        overflow: hidden !important;
        position: fixed;
        width: 100%;
        height: 100%;
    }

    /* Guide Content */
    .guide-content-category {
        display: inline-block;
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }

    .guide-content-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .guide-content-text {
        font-size: 1rem;
        color: #374151;
        line-height: 1.7;
        margin-bottom: 1.5rem;
    }

    .guide-content-tips {
        background: linear-gradient(135deg, #f0f9ff 0%, #fef3f6 100%);
        border-left: 4px solid #a8edea;
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .guide-content-tips-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .guide-content-tips-title i {
        color: #a8edea;
    }

    .guide-content-tips-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .guide-content-tips-list li {
        font-size: 0.9rem;
        color: #4b5563;
        padding: 0.4rem 0;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .guide-content-tips-list li::before {
        content: '✓';
        color: #10b981;
        font-weight: 700;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .guide-content-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    .guide-tag {
        background: #f3f4f6;
        color: #6b7280;
        padding: 0.35rem 0.75rem;
        border-radius: 16px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    /* Navigation Arrows */
    .guide-modal-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #6b7280;
        transition: all 0.2s ease;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .guide-modal-nav:hover {
        background: #a8edea;
        border-color: #a8edea;
        color: white;
        transform: translateY(-50%) scale(1.1);
    }

    .guide-modal-nav.disabled {
        opacity: 0.3;
        pointer-events: none;
    }

    .guide-modal-prev {
        left: 1rem;
    }

    .guide-modal-next {
        right: 1rem;
    }

    /* Pagination */
    .guide-modal-pagination {
        text-align: center;
        padding: 1rem;
        border-top: 1px solid #e5e7eb;
        font-size: 0.9rem;
        color: #6b7280;
        flex-shrink: 0;
    }

    .guide-pagination-current {
        font-weight: 700;
        color: #a8edea;
    }

    /* Responsive Design - Matches stats cards */
    @media (max-width: 768px) {
        .nutritional-guide-section {
            gap: 0.75rem;
        }

        .nutritional-guide-card {
            min-height: 120px;
            padding: 0.85rem 0.75rem 1rem 0.75rem;
        }

        .nutritional-guide-card-icon {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .nutritional-guide-card-title {
            font-size: 0.6rem;
            margin-bottom: 0.25rem;
        }

        .nutritional-guide-card-value {
            font-size: 1.5rem;
        }

        .nutritional-guide-card-subtitle {
            font-size: 0.6rem;
        }

        .guide-modal {
            width: 95%;
            max-height: 90vh;
            border-radius: 12px;
        }

        .guide-modal-header {
            padding: 1rem;
        }

        .guide-modal-title {
            font-size: 1.1rem;
        }

        .guide-swiper-slide {
            padding: 1rem;
        }

        .guide-content-title {
            font-size: 1.15rem;
        }

        .guide-content-text {
            font-size: 0.95rem;
        }

        .guide-modal-nav {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }

        .guide-modal-prev {
            left: 0.5rem;
        }

        .guide-modal-next {
            right: 0.5rem;
        }
    }

    /* Accessibility */
    .nutritional-guide-card:focus,
    .guide-modal-close:focus,
    .guide-modal-nav:focus {
        outline: 3px solid #a8edea;
        outline-offset: 2px;
    }

    /* Touch gestures indicator */
    .guide-swipe-indicator {
        text-align: center;
        padding: 0.5rem;
        font-size: 0.85rem;
        color: #9ca3af;
        font-style: italic;
    }

    /* Animation for new content */
    @keyframes fadeInUpGuide {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .swiper-slide-active .guide-content-category,
    .swiper-slide-active .guide-content-title,
    .swiper-slide-active .guide-content-text {
        animation: fadeInUpGuide 0.4s ease;
    }
</style>

<!-- Nutritional Guide Section -->
<div class="nutritional-guide-section">
    <!-- Nutritional Guide Card (positioned in first column, spans all 3 columns) -->
    <div class="nutritional-guide-card" id="nutritionalGuideCard" role="button" tabindex="0"
        aria-label="Open Nutritional Guide & Tips" style="grid-column: 1 / -1;">

        <div>
            <div class="nutritional-guide-card-icon">
                <i class="fas fa-heart"></i>
            </div>
            <div class="nutritional-guide-card-title">Nutritional Guide & Tips</div>
        </div>
        <div>
            <div class="nutritional-guide-card-value">{{ count($guideItems) }}</div>
            <div class="nutritional-guide-card-subtitle">Empowering resources</div>
        </div>
        <span class="click-hint"><i class="fas fa-hand-pointer"></i> Click here</span>
    </div>
</div>

<!-- Nutritional Guide Modal -->
<div class="guide-modal-overlay" id="guideModalOverlay" role="dialog" aria-modal="true"
    aria-labelledby="guideModalTitle"></div>
<div class="guide-modal" id="guideModal">
    <div class="guide-modal-header">
        <h3 class="guide-modal-title" id="guideModalTitle">
            <i class="fas fa-heart"></i>
            <span>Nutritional Guide & Tips</span>
        </h3>
        <button class="guide-modal-close" id="guideModalClose" aria-label="Close guide modal">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="guide-modal-body">
        <!-- Swiper Container -->
        <div class="guide-swiper-container" id="guideSwiper">
            <div class="swiper-wrapper">
                @foreach($guideItems as $guide)
                    <div class="swiper-slide guide-swiper-slide" data-guide-id="{{ $guide['id'] }}">
                        <span class="guide-content-category" style="background: {{ $guide['color'] }};">
                            {{ $guide['category'] }}
                        </span>
                        <h4 class="guide-content-title">{{ $guide['title'] }}</h4>
                        <div class="guide-content-text">{{ $guide['content'] }}</div>

                        <div class="guide-content-tips">
                            <div class="guide-content-tips-title">
                                <i class="fas fa-star"></i>
                                Key Tips:
                            </div>
                            <ul class="guide-content-tips-list">
                                @foreach($guide['tips'] as $tip)
                                    <li>{{ $tip }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="guide-content-tags">
                            @foreach($guide['tags'] as $tag)
                                <span class="guide-tag">
                                    <i class="fas fa-tag" style="font-size: 0.7rem; margin-right: 0.25rem;"></i>
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                        <div class="guide-swipe-indicator">
                            <i class="fas fa-hand-point-left" style="margin-right: 0.5rem;"></i>
                            Swipe or use arrows to navigate
                            <i class="fas fa-hand-point-right" style="margin-left: 0.5rem;"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button class="guide-modal-nav guide-modal-prev" id="guidePrev" aria-label="Previous guide">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="guide-modal-nav guide-modal-next" id="guideNext" aria-label="Next guide">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <div class="guide-modal-pagination">
        <span class="guide-pagination-current" id="guideCurrentIndex">1</span> / {{ count($guideItems) }}
    </div>
</div>

<!-- Swiper.js Library (needed by nutritional guide) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Nutritional Guide Modal Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const guideCard = document.getElementById('nutritionalGuideCard');
        const guideModal = document.getElementById('guideModal');
        const guideModalOverlay = document.getElementById('guideModalOverlay');
        const guideModalClose = document.getElementById('guideModalClose');
        const guidePrev = document.getElementById('guidePrev');
        const guideNext = document.getElementById('guideNext');
        const guideCurrentIndex = document.getElementById('guideCurrentIndex');

        let guideSwiperInstance = null;

        // Initialize Swiper
        function initGuideSwiper(startIndex = 0) {
            if (guideSwiperInstance) {
                guideSwiperInstance.destroy(true, true);
            }

            guideSwiperInstance = new Swiper('#guideSwiper', {
                initialSlide: startIndex,
                slidesPerView: 1,
                spaceBetween: 0,
                speed: 400,
                effect: 'slide',
                allowTouchMove: true,
                loop: false,
                keyboard: {
                    enabled: true,
                    onlyInViewport: true,
                },
                on: {
                    slideChange: function () {
                        updateGuidePagination();
                        updateGuideNavButtons();
                    }
                }
            });
        }

        // Store scroll position
        let guideScrollPosition = 0;

        // Open modal - always start from first guide
        function openGuideModal() {
            // Save current scroll position
            guideScrollPosition = window.pageYOffset || document.documentElement.scrollTop;

            initGuideSwiper(0); // Always start from first slide

            guideModal.classList.add('active');
            guideModalOverlay.classList.add('active');

            // Lock body scroll - modern approach
            document.body.classList.add('modal-open');
            document.body.style.top = `-${guideScrollPosition}px`;

            updateGuidePagination();
            updateGuideNavButtons();

            // Focus management for accessibility
            guideModalClose.focus();
        }

        // Close modal
        function closeGuideModal() {
            guideModal.classList.remove('active');
            guideModalOverlay.classList.remove('active');

            // Restore body scroll
            document.body.classList.remove('modal-open');
            document.body.style.top = '';

            // Restore scroll position
            window.scrollTo(0, guideScrollPosition);

            if (guideSwiperInstance) {
                setTimeout(() => {
                    guideSwiperInstance.destroy(true, true);
                    guideSwiperInstance = null;
                }, 300);
            }
        }

        // Update pagination
        function updateGuidePagination() {
            if (guideSwiperInstance) {
                guideCurrentIndex.textContent = guideSwiperInstance.activeIndex + 1;
            }
        }

        // Update navigation buttons
        function updateGuideNavButtons() {
            if (guideSwiperInstance) {
                if (guideSwiperInstance.isBeginning) {
                    guidePrev.classList.add('disabled');
                } else {
                    guidePrev.classList.remove('disabled');
                }

                if (guideSwiperInstance.isEnd) {
                    guideNext.classList.add('disabled');
                } else {
                    guideNext.classList.remove('disabled');
                }
            }
        }

        // Event: Click guide card
        if (guideCard) {
            guideCard.addEventListener('click', function () {
                openGuideModal();
            });

            // Keyboard accessibility
            guideCard.addEventListener('keypress', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openGuideModal();
                }
            });
        }

        // Event: Close modal
        guideModalClose.addEventListener('click', closeGuideModal);
        guideModalOverlay.addEventListener('click', closeGuideModal);

        // Event: Navigation buttons
        guidePrev.addEventListener('click', () => {
            if (guideSwiperInstance && !guideSwiperInstance.isBeginning) {
                guideSwiperInstance.slidePrev();
            }
        });

        guideNext.addEventListener('click', () => {
            if (guideSwiperInstance && !guideSwiperInstance.isEnd) {
                guideSwiperInstance.slideNext();
            }
        });

        // Event: Escape key to close
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && guideModal.classList.contains('active')) {
                closeGuideModal();
            }
        });

        // Prevent background scroll when modal is open
        // Handle wheel events
        guideModalOverlay.addEventListener('wheel', function (e) {
            e.preventDefault();
        }, { passive: false });

        // Prevent touch move on overlay except when the touch starts inside the modal body or swiper
        guideModalOverlay.addEventListener('touchmove', function (e) {
            const startTarget = e.target;
            if (startTarget.closest && (startTarget.closest('.guide-modal-body') || startTarget.closest('.guide-swiper-container') || startTarget.closest('.swiper-wrapper'))) {
                return; // allow inner horizontal swipes
            }

            e.preventDefault();
        }, { passive: false });

        // Allow swipes and scrolling within the modal body; Swiper will handle horizontal gestures
        const guideModalBody = document.querySelector('.guide-modal-body');
        if (guideModalBody) {
            guideModalBody.addEventListener('touchstart', function (e) {
                // no-op; keep passive true to allow smooth scrolling and let Swiper manage horizontal swipes
            }, { passive: true });
        }
    });
</script>