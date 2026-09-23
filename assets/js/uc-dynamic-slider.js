/**
 * UC Dynamic Slider — Frontend JS
 * Initialises all sliders on the page.
 */
(function () {
    'use strict';

    var swipers = [];

    function markVideoPlaying(slide) {
        if (slide) {
            slide.classList.add('uc-video-playing');
        }
    }

    // HTML5 video event handling
    document.addEventListener('playing', function (e) {
        if (e.target && e.target.matches && e.target.matches('video.uc-slide-video')) {
            var slide = e.target.closest('.uc-slide');
            markVideoPlaying(slide);
        }
    }, true);

    // Vimeo & YouTube iframe postMessage playback event handling
    window.addEventListener('message', function (event) {
        if (!event.data) return;

        var data = event.data;
        if (typeof data === 'string') {
            try {
                data = JSON.parse(data);
            } catch (e) {
                data = {};
            }
        }

        var isVimeoPlaying = data && (data.event === 'play' || data.event === 'playing' || data.event === 'timeupdate');
        var isYouTubePlaying = data && data.event === 'infoDelivery' && data.info && data.info.playerState === 1;

        if (isVimeoPlaying || isYouTubePlaying) {
            document.querySelectorAll('.uc-slide-iframe').forEach(function (iframe) {
                if (iframe.contentWindow === event.source) {
                    var slide = iframe.closest('.uc-slide');
                    markVideoPlaying(slide);
                }
            });
        }
    });

    function handleVideoPlayback(swiper) {
        if (!swiper || !swiper.el) return;

        // Play HTML5 videos
        var allVideos = swiper.el.querySelectorAll('video.uc-slide-video');
        allVideos.forEach(function (video) {
            if (video.paused) {
                var playPromise = video.play();
                if (playPromise !== undefined) {
                    playPromise.catch(function (err) {
                        console.log('UC Slider: video play interrupted or prevented', err);
                    });
                }
            }
        });

        // Trigger iframe playback on active slide
        if (swiper.slides && swiper.slides[swiper.activeIndex]) {
            var activeSlide = swiper.slides[swiper.activeIndex];
            var iframe = activeSlide.querySelector('iframe.uc-slide-iframe');
            if (iframe && iframe.contentWindow) {
                try {
                    iframe.contentWindow.postMessage('{"method":"play"}', '*');
                    iframe.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
                } catch (e) {}
            }
        }
    }

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
                console.warn('UC Dynamic Slider: invalid swiper config', e);
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
                loopPreventsSliding: false,
                on: {
                    init: function () {
                        handleVideoPlayback(this);
                    },
                    slideChange: function () {
                        handleVideoPlayback(this);
                    },
                    slideChangeTransitionStart: function () {
                        handleVideoPlayback(this);
                    },
                    slideChangeTransitionEnd: function () {
                        handleVideoPlayback(this);
                    }
                }
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
                'frontend/element_ready/uc_dynamic_slider.default',
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
