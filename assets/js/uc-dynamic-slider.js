/**
 * UC Dynamic Slider — Frontend JS
 *
 * Initialises Swiper instances for every .uc-slider-outer[data-swiper] element
 * on the page, and handles HTML5 video + iframe (YouTube / Vimeo) autoplay.
 */
(function () {
    'use strict';

    // ── Video playback helpers ───────────────────────────────────────────────

    /** Marks a .uc-slide as actively playing video. */
    function markVideoPlaying( slide ) {
        if ( slide ) slide.classList.add( 'uc-video-playing' );
    }

    // Capture HTML5 video "playing" events (bubbled via capture phase).
    document.addEventListener( 'playing', function ( e ) {
        if ( e.target && e.target.matches && e.target.matches( 'video.uc-slide-video' ) ) {
            markVideoPlaying( e.target.closest( '.uc-slide' ) );
        }
    }, true );

    // Detect Vimeo / YouTube playback state via postMessage.
    window.addEventListener( 'message', function ( event ) {
        if ( ! event.data ) return;

        var data = event.data;
        if ( typeof data === 'string' ) {
            try { data = JSON.parse( data ); } catch ( e ) { data = {}; }
        }

        var isVimeoPlaying   = data && ( data.event === 'play' || data.event === 'playing' || data.event === 'timeupdate' );
        var isYouTubePlaying = data && data.event === 'infoDelivery' && data.info && data.info.playerState === 1;

        if ( isVimeoPlaying || isYouTubePlaying ) {
            document.querySelectorAll( '.uc-slide-iframe' ).forEach( function ( iframe ) {
                if ( iframe.contentWindow === event.source ) {
                    markVideoPlaying( iframe.closest( '.uc-slide' ) );
                }
            } );
        }
    } );

    // ── Swiper helpers ───────────────────────────────────────────────────────

    /**
     * Plays all HTML5 videos inside a swiper and triggers
     * iframe playback on the active slide.
     */
    function handleVideoPlayback( swiper ) {
        if ( ! swiper || ! swiper.el ) return;

        // Play every HTML5 video in the slider.
        swiper.el.querySelectorAll( 'video.uc-slide-video' ).forEach( function ( video ) {
            if ( video.paused ) {
                var p = video.play();
                if ( p !== undefined ) {
                    p.catch( function ( err ) {
                        console.warn( 'UC Slider: video autoplay prevented', err );
                    } );
                }
            }
        } );

        // Send play command to the active slide's iframe (YouTube / Vimeo).
        var activeSlide = swiper.slides && swiper.slides[ swiper.activeIndex ];
        if ( activeSlide ) {
            var iframe = activeSlide.querySelector( 'iframe.uc-slide-iframe' );
            if ( iframe && iframe.contentWindow ) {
                try {
                    iframe.contentWindow.postMessage( '{"method":"play"}', '*' );
                    iframe.contentWindow.postMessage( '{"event":"command","func":"playVideo","args":""}', '*' );
                } catch ( e ) {}
            }
        }
    }

    // ── Initialisation ───────────────────────────────────────────────────────

    function initSliders() {
        if ( typeof Swiper === 'undefined' ) {
            setTimeout( initSliders, 500 ); // Retry if Swiper hasn't loaded yet.
            return;
        }

        document.querySelectorAll( '.uc-slider-outer[data-swiper]' ).forEach( function ( el ) {
            if ( el._ucSwiper ) return; // Already initialised.

            var config;
            try {
                config = JSON.parse( el.getAttribute( 'data-swiper' ) );
            } catch ( e ) {
                console.warn( 'UC Dynamic Slider: invalid swiper config', e );
                return;
            }

            var swiperEl = el.querySelector( '.swiper' );
            if ( ! swiperEl ) return;

            // Merge in runtime options and video-playback hooks.
            var swiperConfig = Object.assign( {}, config, {
                observer:             true,
                observeParents:       true,
                loopPreventsSliding:  false,
                on: {
                    init:                       function () { handleVideoPlayback( this ); },
                    slideChange:                function () { handleVideoPlayback( this ); },
                    slideChangeTransitionStart: function () { handleVideoPlayback( this ); },
                    slideChangeTransitionEnd:   function () { handleVideoPlayback( this ); },
                },
            } );

            el._ucSwiper = new Swiper( swiperEl, swiperConfig );
        } );
    }

    function destroySlidersIn( scope ) {
        scope.querySelectorAll( '.uc-slider-outer[data-swiper]' ).forEach( function ( el ) {
            if ( el._ucSwiper ) {
                el._ucSwiper.destroy( true, true );
                el._ucSwiper = null;
            }
        } );
    }

    // ── Bootstrap ────────────────────────────────────────────────────────────

    // Standard page load.
    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initSliders );
    } else {
        initSliders();
    }

    // Safety net: also run on window load (handles lazy-loaded assets).
    window.addEventListener( 'load', initSliders );

    // Elementor editor: re-init whenever the widget re-renders.
    function registerElementorHook() {
        if ( ! ( window.elementorFrontend && window.elementorFrontend.hooks ) ) return;

        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/uc_dynamic_slider.default',
            function ( $scope ) {
                destroySlidersIn( $scope[ 0 ] );
                initSliders();
            }
        );
    }

    if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
        registerElementorHook();
    } else if ( typeof jQuery !== 'undefined' ) {
        jQuery( window ).on( 'elementor/frontend/init', registerElementorHook );
    }

}());
