document.addEventListener('DOMContentLoaded', function() {
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
                desktopInterval = setInterval(nextDesktopSlide, 5000);
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
                mobileInterval = setInterval(nextMobileSlide, 5000);
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
});