document.addEventListener('DOMContentLoaded', function() {
    if (window.__warafyProductGalleryInitialized) {
        return;
    }

    window.__warafyProductGalleryInitialized = true;

    const ROTATION_INTERVAL_MS = 6000;

    // Desktop Carousel
    const desktopMainCarousel = document.getElementById('desktop-product-carousel-main');
    const desktopThumbsContainer = document.getElementById('desktop-product-carousel-thumbs');
    const desktopPrevBtn = document.querySelector('.desktop-carousel-prev');
    const desktopNextBtn = document.querySelector('.desktop-carousel-next');

    if (desktopMainCarousel && desktopThumbsContainer) {
        const desktopCarouselItems = desktopMainCarousel.querySelectorAll('.desktop-carousel-item');
        const desktopCarouselThumbs = desktopThumbsContainer.querySelectorAll('.desktop-carousel-thumb');
        let currentDesktopIndex = 0;
        let desktopInterval;

        function updateDesktopCarousel() {
            desktopCarouselItems.forEach((item, index) => {
                if (index === currentDesktopIndex) {
                    item.classList.remove('opacity-0');
                    item.classList.add('opacity-100');
                } else {
                    item.classList.remove('opacity-100');
                    item.classList.add('opacity-0');
                }
            });

            desktopCarouselThumbs.forEach((thumb, index) => {
                if (index === currentDesktopIndex) {
                    thumb.classList.add('border-primary');
                } else {
                    thumb.classList.remove('border-primary');
                }
            });
        }

        function goToDesktopSlide(index) {
            currentDesktopIndex = index;
            updateDesktopCarousel();
            resetDesktopInterval();
        }

        function nextDesktopSlide() {
            currentDesktopIndex = (currentDesktopIndex + 1) % desktopCarouselItems.length;
            updateDesktopCarousel();
            resetDesktopInterval();
        }

        function prevDesktopSlide() {
            currentDesktopIndex = (currentDesktopIndex - 1 + desktopCarouselItems.length) % desktopCarouselItems.length;
            updateDesktopCarousel();
            resetDesktopInterval();
        }

        function startDesktopInterval() {
            if (desktopCarouselItems.length > 1) {
                desktopInterval = setInterval(nextDesktopSlide, ROTATION_INTERVAL_MS);
            }
        }

        function resetDesktopInterval() {
            clearInterval(desktopInterval);
            startDesktopInterval();
        }

        desktopCarouselThumbs.forEach(thumb => {
            thumb.addEventListener('click', () => {
                const index = parseInt(thumb.dataset.imageIndex);
                goToDesktopSlide(index);
            });
        });

        if (desktopPrevBtn) {
            desktopPrevBtn.addEventListener('click', prevDesktopSlide);
        }
        if (desktopNextBtn) {
            desktopNextBtn.addEventListener('click', nextDesktopSlide);
        }

        startDesktopInterval(); // Start auto-sliding for desktop
    }

    // Mobile Carousel
    const mobileCarousel = document.getElementById('mobile-product-carousel');

    if (mobileCarousel) {
        const mobileCarouselTrack = mobileCarousel.querySelector('.mobile-carousel-track');
        const mobileCarouselItems = mobileCarousel.querySelectorAll('.mobile-carousel-item');
        const mobileCarouselDotsContainer = mobileCarousel.querySelector('.mobile-carousel-dots');
        const mobileCarouselDots = mobileCarouselDotsContainer ? mobileCarouselDotsContainer.querySelectorAll('.mobile-carousel-dot') : [];
        let currentMobileIndex = 0;
        let mobileInterval;

        let startX = 0;
        let endX = 0;

        function updateMobileCarousel() {
            const itemWidth = mobileCarouselItems[0].clientWidth;
            mobileCarouselTrack.style.transform = `translateX(${-currentMobileIndex * itemWidth}px)`;

            mobileCarouselDots.forEach((dot, index) => {
                if (index === currentMobileIndex) {
                    dot.classList.add('bg-primary', 'dark:bg-primary-light');
                    dot.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                } else {
                    dot.classList.remove('bg-primary', 'dark:bg-primary-light');
                    dot.classList.add('bg-gray-300', 'dark:bg-gray-600');
                }
            });
        }

        function goToMobileSlide(index) {
            currentMobileIndex = index;
            updateMobileCarousel();
            resetMobileInterval();
        }

        function nextMobileSlide() {
            currentMobileIndex = (currentMobileIndex + 1) % mobileCarouselItems.length;
            updateMobileCarousel();
            resetMobileInterval();
        }

        function startMobileInterval() {
            if (mobileCarouselItems.length > 1) {
                mobileInterval = setInterval(nextMobileSlide, ROTATION_INTERVAL_MS);
            }
        }

        function resetMobileInterval() {
            clearInterval(mobileInterval);
            startMobileInterval();
        }

        mobileCarouselDots.forEach(dot => {
            dot.addEventListener('click', () => {
                const index = parseInt(dot.dataset.imageIndex);
                goToMobileSlide(index);
            });
        });

        mobileCarousel.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });

        mobileCarousel.addEventListener('touchmove', (e) => {
            endX = e.touches[0].clientX;
        });

        mobileCarousel.addEventListener('touchend', () => {
            const sensitivity = 50; // Minimum swipe distance
            if (startX - endX > sensitivity) {
                // Swipe left (next)
                currentMobileIndex = (currentMobileIndex + 1) % mobileCarouselItems.length;
            } else if (endX - startX > sensitivity) {
                // Swipe right (prev)
                currentMobileIndex = (currentMobileIndex - 1 + mobileCarouselItems.length) % mobileCarouselItems.length;
            }
            updateMobileCarousel();
            resetMobileInterval();
        });

        // Update carousel on window resize to ensure correct positioning
        window.addEventListener('resize', () => {
            updateMobileCarousel();
        });
        
        startMobileInterval(); // Start auto-sliding for mobile
        updateMobileCarousel(); // Initial update
    }

    // Product Image Modal
    const modal = document.getElementById('warafy-product-image-modal');
    const modalPanel = document.getElementById('warafy-product-image-modal-panel');
    const modalImage = document.getElementById('warafy-product-image-modal-image');
    const modalClose = document.getElementById('warafy-product-image-modal-close');

    if (modal && modalPanel && modalImage && modalClose) {
        let currentModalImageIndex = 0;
        const modalStage = document.getElementById('warafy-product-image-modal-stage');
        let modalZoom = 1;
        let modalPanX = 0;
        let modalPanY = 0;
        let isDragging = false;
        let dragStartX = 0;
        let dragStartY = 0;
        let dragPanStartX = 0;
        let dragPanStartY = 0;
        let pinchStartDistance = 0;
        let pinchStartZoom = 1;

        const clampZoom = (value) => Math.min(4, Math.max(1, value));

        function applyModalZoom() {
            modalImage.style.transformOrigin = 'center center';
            modalImage.style.transform = `translate(${modalPanX}px, ${modalPanY}px) scale(${modalZoom})`;
            modalImage.style.cursor = modalZoom > 1 ? (isDragging ? 'grabbing' : 'grab') : 'zoom-in';
        }

        function resetModalZoom() {
            modalZoom = 1;
            modalPanX = 0;
            modalPanY = 0;
            isDragging = false;
            applyModalZoom();
        }

        function getTouchDistance(touches) {
            if (touches.length < 2) return 0;
            const dx = touches[0].clientX - touches[1].clientX;
            const dy = touches[0].clientY - touches[1].clientY;
            return Math.sqrt((dx * dx) + (dy * dy));
        }

        function openModal(imageUrl, imageAlt, imageIndex = 0) {
            modalImage.src = imageUrl;
            modalImage.alt = imageAlt;
            currentModalImageIndex = imageIndex;
            resetModalZoom();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            resetModalZoom();
        }

        // Open modal on image click
        document.addEventListener('click', (e) => {
            const imageItem = e.target.closest('.desktop-carousel-item, .mobile-carousel-item');
            if (imageItem && imageItem.dataset.imageUrl) {
                const imageUrl = imageItem.dataset.imageUrl;
                const imageAlt = imageItem.dataset.imageAlt || '';
                const imageIndex = parseInt(imageItem.dataset.imageIndex) || 0;
                openModal(imageUrl, imageAlt, imageIndex);
            }
        });

        // Close modal on close button click
        modalClose.addEventListener('click', closeModal);

        // Close modal on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });

        if (modalStage) {
            modalStage.addEventListener('wheel', (e) => {
                if (modal.classList.contains('hidden')) return;
                e.preventDefault();
                modalZoom = clampZoom(modalZoom + (e.deltaY < 0 ? 0.15 : -0.15));
                applyModalZoom();
            }, { passive: false });

            modalStage.addEventListener('mousedown', (e) => {
                if (modalZoom <= 1) return;
                isDragging = true;
                dragStartX = e.clientX;
                dragStartY = e.clientY;
                dragPanStartX = modalPanX;
                dragPanStartY = modalPanY;
                applyModalZoom();
            });

            window.addEventListener('mousemove', (e) => {
                if (!isDragging || modal.classList.contains('hidden')) return;
                modalPanX = dragPanStartX + (e.clientX - dragStartX);
                modalPanY = dragPanStartY + (e.clientY - dragStartY);
                applyModalZoom();
            });

            window.addEventListener('mouseup', () => {
                isDragging = false;
                applyModalZoom();
            });

            modalStage.addEventListener('touchstart', (e) => {
                if (e.touches.length === 1 && modalZoom > 1) {
                    isDragging = true;
                    dragStartX = e.touches[0].clientX;
                    dragStartY = e.touches[0].clientY;
                    dragPanStartX = modalPanX;
                    dragPanStartY = modalPanY;
                } else if (e.touches.length === 2) {
                    isDragging = false;
                    pinchStartDistance = getTouchDistance(e.touches);
                    pinchStartZoom = modalZoom;
                }
            }, { passive: true });

            modalStage.addEventListener('touchmove', (e) => {
                if (modal.classList.contains('hidden')) return;
                if (e.touches.length === 1 && isDragging) {
                    e.preventDefault();
                    modalPanX = dragPanStartX + (e.touches[0].clientX - dragStartX);
                    modalPanY = dragPanStartY + (e.touches[0].clientY - dragStartY);
                    applyModalZoom();
                    return;
                }
                if (e.touches.length === 2 && pinchStartDistance) {
                    e.preventDefault();
                    const distance = getTouchDistance(e.touches);
                    modalZoom = clampZoom(pinchStartZoom * (distance / pinchStartDistance));
                    applyModalZoom();
                }
            }, { passive: false });

            modalStage.addEventListener('touchend', () => {
                pinchStartDistance = 0;
                isDragging = false;
            });

            modalStage.addEventListener('touchcancel', () => {
                pinchStartDistance = 0;
                isDragging = false;
            });

            modalStage.addEventListener('dblclick', () => {
                if (modalZoom > 1) {
                    modalZoom = 1;
                    modalPanX = 0;
                    modalPanY = 0;
                } else {
                    modalZoom = 2;
                }
                applyModalZoom();
            });

            applyModalZoom();
        }

        // Close modal on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    }
});
