<?php
/**
 * Plugin Name: UC Dynamic Slider
 * Description: A custom Elementor widget — dynamic slider supporting Images, Animated GIFs, MP4/WebM Videos, and YouTube/Vimeo embeds with captions, autoplay, navigation, and custom buttons.
 * Version: 1.1.0
 * Author: Agornyrah Eric
 * Text Domain: uc-dynamic-slider
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UC_DYNAMIC_SLIDER_PATH', plugin_dir_path( __FILE__ ) );
define( 'UC_DYNAMIC_SLIDER_URL',  plugin_dir_url( __FILE__ ) );

/**
 * Check Elementor is active before doing anything.
 */
add_action( 'plugins_loaded', function() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-warning"><p><strong>UC Dynamic Slider</strong> requires Elementor to be installed and activated.</p></div>';
        });
        return;
    }
    add_action( 'elementor/widgets/register', function( $manager ) {
        require_once UC_DYNAMIC_SLIDER_PATH . 'widgets/class-uc-dynamic-slider-widget.php';
        $manager->register( new \UC_Dynamic_Slider_Widget() );
    });
});

/**
 * Enqueue Swiper + our assets on the frontend.
 */
add_action( 'wp_enqueue_scripts', function() {
    // Only load on frontend, not in admin
    if ( is_admin() ) return;

    wp_enqueue_style(
        'swiper-css',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        [],
        '11'
    );
    wp_enqueue_script(
        'swiper-js',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        [],
        '11',
        true
    );
    wp_enqueue_style(
        'uc-dynamic-slider-css',
        UC_DYNAMIC_SLIDER_URL . 'assets/css/uc-dynamic-slider.css',
        [ 'swiper-css' ],
        filemtime( UC_DYNAMIC_SLIDER_PATH . 'assets/css/uc-dynamic-slider.css' )
    );
    wp_enqueue_script(
        'uc-dynamic-slider-js',
        UC_DYNAMIC_SLIDER_URL . 'assets/js/uc-dynamic-slider.js',
        [ 'swiper-js', 'elementor-frontend' ],
        filemtime( UC_DYNAMIC_SLIDER_PATH . 'assets/js/uc-dynamic-slider.js' ),
        true
    );
}, 20); // Load after Elementor's scripts
