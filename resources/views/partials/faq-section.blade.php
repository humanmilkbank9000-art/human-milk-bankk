@php
    use App\Http\Controllers\FaqController;
    $faqItems = FaqController::getFaqItems();
    $faqByCategory = FaqController::getFaqByCategory();
@endphp

<!-- FAQ Section Styles -->
<style>
    /* FAQ Section Container - Matches stats-container */
    .faq-section {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    /* Single Consolidated FAQ Card - Exact match to stat-card */
    .faq-main-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 0.75rem;
        padding: 1rem;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
        color: white;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .faq-main-card::before {
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

    .faq-main-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
    }

    .faq-main-card:hover::before {
        background: rgba(255, 255, 255, 0.1);
    }

    .faq-main-card:active {
        transform: translateY(-2px);
    }

    .faq-main-card-icon {
        font-size: 1.8rem;
        margin-bottom: 0.25rem;
        opacity: 0.9;
    }

    .faq-main-card-title {
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
        opacity: 0.95;
    }

    .faq-main-card-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.15rem;
    }

    .faq-main-card-subtitle {
        font-size: 0.75rem;
        opacity: 0.85;
    }



    /* Modal Overlay */
    .faq-modal-overlay {
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

    .faq-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    /* Modal Container */
    .faq-modal {
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

    .faq-modal.active {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, -50%) scale(1);
    }

    /* Modal Header */
    .faq-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    .faq-modal-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .faq-modal-title i {
        color: #667eea;
    }

    .faq-modal-close {
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

    .faq-modal-close:hover {
        background: #e5e7eb;
        color: #374151;
        transform: rotate(90deg);
    }

    /* Modal Body - Swiper Container */
    .faq-modal-body {
        flex: 1;
        /* Let the modal body handle vertical scrolling */
        overflow-y: auto;
        overflow-x: hidden;
        position: relative;
        /* Allow natural touch vertical pan; prevent horizontal pan conflicts */
        touch-action: pan-y;
        -webkit-overflow-scrolling: touch;
    }

    .swiper-container {
        width: 100%;
        height: 100%;
        /* Allow horizontal touch gestures inside the swiper area while keeping vertical pan on the modal body */
        touch-action: pan-x;
    }

    .swiper-slide {
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

    /* FAQ Content */
    .faq-content-category {
        display: inline-block;
        background: #667eea;
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }

    .faq-content-question {
        font-size: 1.35rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .faq-content-answer {
        font-size: 1rem;
        color: #374151;
        line-height: 1.7;
        margin-bottom: 1.5rem;
    }

    .faq-content-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    .faq-tag {
        background: #f3f4f6;
        color: #6b7280;
        padding: 0.35rem 0.75rem;
        border-radius: 16px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    /* Navigation Arrows */
    .faq-modal-nav {
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

    .faq-modal-nav:hover {
        background: #667eea;
        border-color: #667eea;
        color: white;
        transform: translateY(-50%) scale(1.1);
    }

    .faq-modal-nav.disabled {
        opacity: 0.3;
        pointer-events: none;
    }

    .faq-modal-prev {
        left: 1rem;
    }

    .faq-modal-next {
        right: 1rem;
    }

    /* Pagination */
    .faq-modal-pagination {
        text-align: center;
        padding: 1rem;
        border-top: 1px solid #e5e7eb;
        font-size: 0.9rem;
        color: #6b7280;
        flex-shrink: 0;
    }

    .faq-pagination-current {
        font-weight: 700;
        color: #667eea;
    }

    /* Responsive Design - Matches stats cards */
    @media (max-width: 768px) {
        .faq-section {
            gap: 0.75rem;
        }

        .faq-main-card {
            min-height: 120px;
            padding: 0.85rem 0.75rem 1rem 0.75rem;
        }

        .faq-main-card-icon {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .faq-main-card-title {
            font-size: 0.6rem;
            margin-bottom: 0.25rem;
        }

        .faq-main-card-value {
            font-size: 1.5rem;
        }

        .faq-main-card-subtitle {
            font-size: 0.6rem;
        }

        .faq-modal {
            width: 95%;
            max-height: 90vh;
            border-radius: 12px;
        }

        .faq-modal-header {
            padding: 1rem;
        }

        .faq-modal-title {
            font-size: 1.1rem;
        }

        .swiper-slide {
            padding: 1rem;
        }

        .faq-content-question {
            font-size: 1.15rem;
        }

        .faq-content-answer {
            font-size: 0.95rem;
        }

        .faq-modal-nav {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }

        .faq-modal-prev {
            left: 0.5rem;
        }

        .faq-modal-next {
            right: 0.5rem;
        }
    }

    /* Accessibility */
    .faq-card:focus,
    .faq-modal-close:focus,
    .faq-modal-nav:focus {
        outline: 3px solid #667eea;
        outline-offset: 2px;
    }

    /* Touch gestures indicator */
    .swipe-indicator {
        text-align: center;
        padding: 0.5rem;
        font-size: 0.85rem;
        color: #9ca3af;
        font-style: italic;
    }

    /* Animation for new content */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .swiper-slide-active .faq-content-category,
    .swiper-slide-active .faq-content-question,
    .swiper-slide-active .faq-content-answer {
        animation: fadeInUp 0.4s ease;
    }
</style>

<!-- FAQ Section -->
<div class="faq-section">
    <!-- FAQ Card (positioned in first column, spans all 3 columns) -->
    <div class="faq-main-card" id="faqMainCard" role="button" tabindex="0" aria-label="Open Frequently Asked Questions"
        style="grid-column: 1 / -1;">

        <div>
            <div class="faq-main-card-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="faq-main-card-title">Frequently Asked Questions</div>
        </div>
        <div>
            <div class="faq-main-card-value">{{ count($faqItems) }}</div>
            <div class="faq-main-card-subtitle">Topics available</div>
        </div>
    </div>
</div>

<!-- FAQ Modal -->
<div class="faq-modal-overlay" id="faqModalOverlay" role="dialog" aria-modal="true" aria-labelledby="faqModalTitle">
</div>
<div class="faq-modal" id="faqModal">
    <div class="faq-modal-header">
        <h3 class="faq-modal-title" id="faqModalTitle">
            <i class="fas fa-question-circle"></i>
            <span>Frequently Asked Questions</span>
        </h3>
        <button class="faq-modal-close" id="faqModalClose" aria-label="Close FAQ modal">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="faq-modal-body">
        <!-- Swiper Container -->
        <div class="swiper-container" id="faqSwiper">
            <div class="swiper-wrapper">
                @foreach($faqItems as $faq)
                    <div class="swiper-slide" data-faq-id="{{ $faq['id'] }}">
                        <span class="faq-content-category" style="background: {{ $faq['color'] }};">
                            {{ $faq['category'] }}
                        </span>
                        <h4 class="faq-content-question">{{ $faq['question'] }}</h4>
                        <div class="faq-content-answer">{{ $faq['answer'] }}</div>
                        <div class="faq-content-tags">
                            @foreach($faq['tags'] as $tag)
                                <span class="faq-tag">
                                    <i class="fas fa-tag" style="font-size: 0.7rem; margin-right: 0.25rem;"></i>
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                        <div class="swipe-indicator">
                            <i class="fas fa-hand-point-left" style="margin-right: 0.5rem;"></i>
                            Swipe or use arrows to navigate
                            <i class="fas fa-hand-point-right" style="margin-left: 0.5rem;"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button class="faq-modal-nav faq-modal-prev" id="faqPrev" aria-label="Previous FAQ">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="faq-modal-nav faq-modal-next" id="faqNext" aria-label="Next FAQ">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <div class="faq-modal-pagination">
        <span class="faq-pagination-current" id="faqCurrentIndex">1</span> / {{ count($faqItems) }}
    </div>
</div>

<!-- Swiper.js Library -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- FAQ Modal Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const faqMainCard = document.getElementById('faqMainCard');
        const faqModal = document.getElementById('faqModal');
        const faqModalOverlay = document.getElementById('faqModalOverlay');
        const faqModalClose = document.getElementById('faqModalClose');
        const faqPrev = document.getElementById('faqPrev');
        const faqNext = document.getElementById('faqNext');
        const faqCurrentIndex = document.getElementById('faqCurrentIndex');

        let swiperInstance = null;

        // Initialize Swiper
        function initSwiper(startIndex = 0) {
            if (swiperInstance) {
                swiperInstance.destroy(true, true);
            }

            swiperInstance = new Swiper('#faqSwiper', {
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
                        updatePagination();
                        updateNavButtons();
                    }
                }
            });
        }

        // Store scroll position
        let scrollPosition = 0;

        // Open modal - always start from first FAQ
        function openModal() {
            // Save current scroll position
            scrollPosition = window.pageYOffset || document.documentElement.scrollTop;

            initSwiper(0); // Always start from first slide

            faqModal.classList.add('active');
            faqModalOverlay.classList.add('active');

            // Lock body scroll - modern approach
            document.body.classList.add('modal-open');
            document.body.style.top = `-${scrollPosition}px`;

            updatePagination();
            updateNavButtons();

            // Focus management for accessibility
            faqModalClose.focus();
        }

        // Close modal
        function closeModal() {
            faqModal.classList.remove('active');
            faqModalOverlay.classList.remove('active');

            // Restore body scroll
            document.body.classList.remove('modal-open');
            document.body.style.top = '';

            // Restore scroll position
            window.scrollTo(0, scrollPosition);

            if (swiperInstance) {
                setTimeout(() => {
                    swiperInstance.destroy(true, true);
                    swiperInstance = null;
                }, 300);
            }
        }

        // Update pagination
        function updatePagination() {
            if (swiperInstance) {
                faqCurrentIndex.textContent = swiperInstance.activeIndex + 1;
            }
        }

        // Update navigation buttons
        function updateNavButtons() {
            if (swiperInstance) {
                if (swiperInstance.isBeginning) {
                    faqPrev.classList.add('disabled');
                } else {
                    faqPrev.classList.remove('disabled');
                }

                if (swiperInstance.isEnd) {
                    faqNext.classList.add('disabled');
                } else {
                    faqNext.classList.remove('disabled');
                }
            }
        }

        // Event: Click main FAQ card
        if (faqMainCard) {
            faqMainCard.addEventListener('click', function () {
                openModal();
            });

            // Keyboard accessibility
            faqMainCard.addEventListener('keypress', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openModal();
                }
            });
        }

        // Event: Close modal
        faqModalClose.addEventListener('click', closeModal);
        faqModalOverlay.addEventListener('click', closeModal);

        // Event: Navigation buttons
        faqPrev.addEventListener('click', () => {
            if (swiperInstance && !swiperInstance.isBeginning) {
                swiperInstance.slidePrev();
            }
        });

        faqNext.addEventListener('click', () => {
            if (swiperInstance && !swiperInstance.isEnd) {
                swiperInstance.slideNext();
            }
        });

        // Event: Escape key to close
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && faqModal.classList.contains('active')) {
                closeModal();
            }
        });

        // Prevent background scroll when modal is open
        // Handle wheel events
        faqModalOverlay.addEventListener('wheel', function (e) {
            e.preventDefault();
        }, { passive: false });

        // Prevent touch move on overlay except when the touch starts inside the modal body or swiper
        faqModalOverlay.addEventListener('touchmove', function (e) {
            // If the touch originated from within the modal body or swiper, don't prevent it
            const startTarget = e.target;
            if (startTarget.closest && (startTarget.closest('.faq-modal-body') || startTarget.closest('.swiper-container') || startTarget.closest('.swiper-wrapper'))) {
                return; // allow inner horizontal swipes
            }

            e.preventDefault();
        }, { passive: false });

        // Allow scroll and swipe within modal body (let Swiper handle horizontal swipes)
        const modalBody = document.querySelector('.faq-modal-body');
        if (modalBody) {
            modalBody.addEventListener('touchstart', function (e) {
                // no-op here; we rely on Swiper's touch handling for horizontal gestures
            }, { passive: true });
        }
    });
</script>