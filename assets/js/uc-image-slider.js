/**
 * UC Image Slider — Frontend JS
 * Initialises all sliders on the page.
 */
(function () {
    'use strict';

    var swipers = [];

    function initSliders() {
        console.log('UC Slider: initSliders called');
        
        // Check if Swiper is available
        if (typeof Swiper === 'undefined') {
            console.warn('UC Slider: Swiper library not loaded yet, retrying in 500ms...');
            setTimeout(initSliders, 500);
            return;
        }
        
        document.querySelectorAll('.uc-slider-outer[data-swiper]').forEach(function (el) {
            // Skip if already initialized
            if (el._ucSwiper) {
                console.log('UC Slider: already initialized', el.id);
                return;
            }

            var config;
            try {
                config = JSON.parse(el.getAttribute('data-swiper'));
                console.log('UC Slider: config', config);
            } catch (e) {
                console.warn('UC Image Slider: invalid swiper config', e);
                return;
            }

            var swiperEl = el.querySelector('.swiper');
            if (!swiperEl) {
                console.warn('UC Slider: no .swiper element found');
                return;
            }

            // Check for navigation elements
            var prevEl = swiperEl.querySelector('.uc-slider-prev');
            var nextEl = swiperEl.querySelector('.uc-slider-next');
            var dotsEl = swiperEl.querySelector('.uc-slider-dots');
            console.log('UC Slider: nav elements', { prev: prevEl, next: nextEl, dots: dotsEl });

            // Store reference for cleanup
            var swiperConfig = Object.assign({}, config, {
                observer: true,
                observeParents: true,
            });
            
            el._ucSwiper = new Swiper(swiperEl, swiperConfig);
            swipers.push(el._ucSwiper);
            console.log('UC Slider: initialized', el.id);
        });
    }

    function destroySlidersIn(scope) {
        scope.querySelectorAll('.uc-slider-outer[data-swiper]').forEach(function (el) {
            if (el._ucSwiper) {
                el._ucSwiper.destroy(true, true);
                el._ucSwiper = null;
            }
        });
    }

    /* Standard page load */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSliders);
    } else {
        initSliders();
    }

    /* Elementor editor — re-init after widget renders/updates */
    function registerElementorHook() {
        if (window.elementorFrontend && window.elementorFrontend.hooks) {
            window.elementorFrontend.hooks.addAction(
                'frontend/element_ready/uc_image_slider.default',
                function ($scope) {
                    // Destroy existing slider in this scope
                    destroySlidersIn($scope[0]);
                    // Re-init all sliders
                    initSliders();
                }
            );
        }
    }

    if (window.elementorFrontend && window.elementorFrontend.hooks) {
        registerElementorHook();
    } else if (typeof jQuery !== 'undefined') {
        jQuery(window).on('elementor/frontend/init', registerElementorHook);
    }

    /* Also handle window load for safety */
    window.addEventListener('load', initSliders);
})();
