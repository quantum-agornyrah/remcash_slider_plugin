<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

class UC_Dynamic_Slider_Widget extends Widget_Base {

    public function get_name()        { return 'uc_dynamic_slider'; }
    public function get_title()       { return __( 'UC Dynamic Slider', 'uc-dynamic-slider' ); }
    public function get_icon()        { return 'eicon-slideshow'; }
    public function get_categories()  { return [ 'general' ]; }
    public function get_keywords()    { return [ 'slider', 'image', 'carousel', 'swiper' ]; }

    protected function register_controls() {

        /* ────────────────────────────────────────────
         *  SECTION: SLIDES
         * ──────────────────────────────────────────── */
        $this->start_controls_section( 'section_slides', [
            'label' => __( 'Slides', 'uc-dynamic-slider' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new Repeater();

        $repeater->add_control( 'slide_type', [
            'label'   => __( 'Slide Type', 'uc-dynamic-slider' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'image',
            'options' => [
                'image'    => __( 'Image / GIF', 'uc-dynamic-slider' ),
                'video'    => __( 'Self-Hosted Video (MP4/WebM)', 'uc-dynamic-slider' ),
                'external' => __( 'External Video (YouTube/Vimeo)', 'uc-dynamic-slider' ),
            ],
        ]);

        $repeater->add_control( 'slide_image', [
            'label'       => __( 'Image / Poster', 'uc-dynamic-slider' ),
            'type'        => Controls_Manager::MEDIA,
            'default'     => [ 'url' => Utils::get_placeholder_image_src() ],
            'description' => __( 'Select image/GIF, or fallback poster image for videos.', 'uc-dynamic-slider' ),
        ]);

        $repeater->add_control( 'slide_video', [
            'label'       => __( 'Video File', 'uc-dynamic-slider' ),
            'type'        => Controls_Manager::MEDIA,
            'media_types' => [ 'video' ],
            'condition'   => [ 'slide_type' => 'video' ],
            'description' => __( 'Upload or select an MP4/WebM video file.', 'uc-dynamic-slider' ),
        ]);

        $repeater->add_control( 'slide_video_url', [
            'label'       => __( 'Video URL', 'uc-dynamic-slider' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => 'https://www.youtube.com/watch?v=...',
            'label_block' => true,
            'condition'   => [ 'slide_type' => 'external' ],
            'description' => __( 'Enter YouTube or Vimeo video link.', 'uc-dynamic-slider' ),
        ]);

        $repeater->add_control( 'slide_video_loop', [
            'label'        => __( 'Loop Video', 'uc-dynamic-slider' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
            'condition'    => [ 'slide_type!' => 'image' ],
        ]);

        $repeater->add_control( 'slide_video_mute', [
            'label'        => __( 'Mute Audio', 'uc-dynamic-slider' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
            'condition'    => [ 'slide_type!' => 'image' ],
            'description'  => __( 'Required by most browsers for autoplay.', 'uc-dynamic-slider' ),
        ]);

        $repeater->add_control( 'slide_title', [
            'label'       => __( 'Title', 'uc-dynamic-slider' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Slide Title', 'uc-dynamic-slider' ),
            'label_block' => true,
        ]);

        $repeater->add_control( 'slide_description', [
            'label'   => __( 'Description', 'uc-dynamic-slider' ),
            'type'    => Controls_Manager::TEXTAREA,
            'default' => __( 'Add a short caption or description here.', 'uc-dynamic-slider' ),
            'rows'    => 3,
        ]);

        $repeater->add_control( 'slide_link', [
            'label'         => __( 'Link (URL)', 'uc-dynamic-slider' ),
            'type'          => Controls_Manager::URL,
            'placeholder'   => 'https://example.com',
            'show_external' => true,
            'default'       => [ 'url' => '' ],
        ]);

        $repeater->add_control( 'slide_link_label', [
            'label'     => __( 'Link Button Label', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Learn More', 'uc-dynamic-slider' ),
            'condition' => [ 'slide_link[url]!' => '' ],
        ]);

        $this->add_control( 'slides', [
            'label'       => __( 'Slides', 'uc-dynamic-slider' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'slide_type'        => 'image',
                    'slide_title'       => 'First Slide',
                    'slide_description' => 'A short caption for the first slide.',
                    'slide_link'        => [ 'url' => '' ],
                    'slide_image'       => [ 'url' => Utils::get_placeholder_image_src() ],
                ],
                [
                    'slide_type'        => 'image',
                    'slide_title'       => 'Second Slide',
                    'slide_description' => 'A short caption for the second slide.',
                    'slide_link'        => [ 'url' => '' ],
                    'slide_image'       => [ 'url' => Utils::get_placeholder_image_src() ],
                ],
                [
                    'slide_type'        => 'image',
                    'slide_title'       => 'Third Slide',
                    'slide_description' => 'A short caption for the third slide.',
                    'slide_link'        => [ 'url' => '' ],
                    'slide_image'       => [ 'url' => Utils::get_placeholder_image_src() ],
                ],
            ],
            'title_field' => '{{{ slide_title }}}',
        ]);

        $this->end_controls_section();

        /* ────────────────────────────────────────────
         *  SECTION: SLIDER SETTINGS
         * ──────────────────────────────────────────── */
        $this->start_controls_section( 'section_settings', [
            'label' => __( 'Slider Settings', 'uc-dynamic-slider' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_responsive_control( 'slider_height', [
            'label'      => __( 'Slider Height', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'vh' ],
            'range'      => [
                'px' => [ 'min' => 200, 'max' => 900, 'step' => 10 ],
                'vh' => [ 'min' => 20,  'max' => 100, 'step' => 1  ],
            ],
            'default'    => [ 'unit' => 'px', 'size' => 500 ],
            'selectors'  => [
                '{{WRAPPER}} .uc-slider-wrapper' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control( 'autoplay', [
            'label'        => __( 'Autoplay', 'uc-dynamic-slider' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'On', 'uc-dynamic-slider' ),
            'label_off'    => __( 'Off', 'uc-dynamic-slider' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->add_control( 'autoplay_speed', [
            'label'     => __( 'Autoplay Delay (ms)', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::NUMBER,
            'default'   => 4000,
            'min'       => 1000,
            'max'       => 10000,
            'step'      => 500,
            'condition' => [ 'autoplay' => 'yes' ],
        ]);

        $this->add_control( 'pause_on_hover', [
            'label'        => __( 'Pause on Hover', 'uc-dynamic-slider' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'uc-dynamic-slider' ),
            'label_off'    => __( 'No', 'uc-dynamic-slider' ),
            'return_value' => 'yes',
            'default'      => 'yes',
            'condition'    => [ 'autoplay' => 'yes' ],
        ]);

        $this->add_control( 'transition_speed', [
            'label'   => __( 'Transition Speed (ms)', 'uc-dynamic-slider' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 700,
            'min'     => 200,
            'max'     => 2000,
            'step'    => 100,
        ]);

        $this->add_control( 'show_arrows', [
            'label'        => __( 'Navigation Arrows', 'uc-dynamic-slider' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->add_control( 'show_dots', [
            'label'        => __( 'Dot Pagination', 'uc-dynamic-slider' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->add_control( 'loop', [
            'label'        => __( 'Infinite Loop', 'uc-dynamic-slider' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->add_control( 'keyboard_nav', [
            'label'        => __( 'Keyboard Navigation', 'uc-dynamic-slider' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'On', 'uc-dynamic-slider' ),
            'label_off'    => __( 'Off', 'uc-dynamic-slider' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->add_control( 'slide_effect', [
            'label'   => __( 'Slide Effect', 'uc-dynamic-slider' ),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'slide' => __( 'Slide', 'uc-dynamic-slider' ),
                'fade'  => __( 'Fade', 'uc-dynamic-slider' ),
            ],
            'default' => 'slide',
        ]);

        $this->add_control( 'image_fit', [
            'label'     => __( 'Image Fit', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
                'cover'   => 'Cover',
                'contain' => 'Contain',
                'fill'    => 'Fill',
            ],
            'default'   => 'cover',
            'selectors' => [
                '{{WRAPPER}} .uc-slide-bg' => 'background-size: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control( 'slide_border_radius', [
            'label'      => __( 'Image Border Radius', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [
                'px' => [ 'min' => 0, 'max' => 100 ],
                '%'  => [ 'min' => 0, 'max' => 50 ],
            ],
            'default'    => [
                'size' => 0,
                'unit' => 'px',
            ],
            'selectors'  => [
                '{{WRAPPER}} .uc-slide'       => 'border-radius: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .uc-slide-bg'     => 'border-radius: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .uc-slide-overlay' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        /* ────────────────────────────────────────────
         *  SECTION: OVERLAY STYLE
         * ──────────────────────────────────────────── */
        $this->start_controls_section( 'section_overlay_style', [
            'label' => __( 'Overlay', 'uc-dynamic-slider' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'overlay_color', [
            'label'     => __( 'Overlay Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(0,0,0,0.45)',
            'selectors' => [
                '{{WRAPPER}} .uc-slide-overlay' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control( 'caption_position', [
            'label'   => __( 'Caption Position', 'uc-dynamic-slider' ),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'center' => 'Center',
                'bottom' => 'Bottom',
                'top'    => 'Top',
            ],
            'default' => 'center',
        ]);

        $this->add_responsive_control( 'caption_align', [
            'label'     => __( 'Content Alignment', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => [ 'title' => 'Left',   'icon' => 'eicon-text-align-left'   ],
                'center' => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => 'Right',  'icon' => 'eicon-text-align-right'  ],
            ],
            'default'   => 'center',
            'selectors' => [
                '{{WRAPPER}} .uc-slide-caption' => 'text-align: {{VALUE}};',
                '{{WRAPPER}} .uc-slide-title'   => 'text-align: {{VALUE}};',
                '{{WRAPPER}} .uc-slide-desc'    => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        /* ────────────────────────────────────────────
         *  SECTION: TITLE STYLE
         * ──────────────────────────────────────────── */
        $this->start_controls_section( 'section_title_style', [
            'label' => __( 'Title', 'uc-dynamic-slider' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'title_color', [
            'label'     => __( 'Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .uc-slide-title' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'label'    => __( 'Typography', 'uc-dynamic-slider' ),
            'selector' => '{{WRAPPER}} .uc-slide-title',
        ]);

        $this->add_responsive_control( 'title_font_size', [
            'label'      => __( 'Font Size', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem', 'vw' ],
            'range'      => [
                'px'  => [ 'min' => 16, 'max' => 100 ],
                'em'  => [ 'min' => 1, 'max' => 6 ],
                'rem' => [ 'min' => 1, 'max' => 6 ],
                'vw'  => [ 'min' => 1, 'max' => 10 ],
            ],
            'default'    => [
                'size' => 32,
                'unit' => 'px',
            ],
            'selectors'  => [
                '{{WRAPPER}} .uc-slide-title' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control( 'title_spacing', [
            'label'      => __( 'Bottom Spacing', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 12 ],
            'selectors'  => [
                '{{WRAPPER}} .uc-slide-title' => 'margin-bottom: {{SIZE}}px;',
            ],
        ]);

        $this->add_responsive_control( 'caption_padding', [
            'label'      => __( 'Caption Padding', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [
                'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
            ],
            'default'    => [ 'size' => 48 ],
            'selectors'  => [
                '{{WRAPPER}} .uc-slide-caption' => 'padding: 32px {{SIZE}}px;',
            ],
        ]);

        $this->end_controls_section();

        /* ────────────────────────────────────────────
         *  SECTION: DESCRIPTION STYLE
         * ──────────────────────────────────────────── */
        $this->start_controls_section( 'section_desc_style', [
            'label' => __( 'Description', 'uc-dynamic-slider' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'desc_color', [
            'label'     => __( 'Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(255,255,255,0.85)',
            'selectors' => [
                '{{WRAPPER}} .uc-slide-desc' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'desc_typography',
            'label'    => __( 'Typography', 'uc-dynamic-slider' ),
            'selector' => '{{WRAPPER}} .uc-slide-desc',
        ]);

        $this->add_responsive_control( 'desc_font_size', [
            'label'      => __( 'Font Size', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem', 'vw' ],
            'range'      => [
                'px'  => [ 'min' => 12, 'max' => 50 ],
                'em'  => [ 'min' => 0.8, 'max' => 3 ],
                'rem' => [ 'min' => 0.8, 'max' => 3 ],
                'vw'  => [ 'min' => 1, 'max' => 5 ],
            ],
            'default'    => [
                'size' => 16,
                'unit' => 'px',
            ],
            'selectors'  => [
                '{{WRAPPER}} .uc-slide-desc' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control( 'desc_spacing', [
            'label'      => __( 'Bottom Spacing', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 20 ],
            'selectors'  => [
                '{{WRAPPER}} .uc-slide-desc' => 'margin-bottom: {{SIZE}}px;',
            ],
        ]);

        $this->end_controls_section();

        /* ────────────────────────────────────────────
         *  SECTION: BUTTON STYLE
         * ──────────────────────────────────────────── */
        $this->start_controls_section( 'section_btn_style', [
            'label' => __( 'Link Button', 'uc-dynamic-slider' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'btn_text_color', [
            'label'     => __( 'Text Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .uc-slide-btn' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control( 'btn_bg_color', [
            'label'     => __( 'Background Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(255,255,255,0.2)',
            'selectors' => [ '{{WRAPPER}} .uc-slide-btn' => 'background: {{VALUE}};' ],
        ]);

        $this->add_control( 'btn_border_color', [
            'label'     => __( 'Border Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(255,255,255,0.6)',
            'selectors' => [ '{{WRAPPER}} .uc-slide-btn' => 'border-color: {{VALUE}};' ],
        ]);

        $this->add_responsive_control( 'btn_border_radius', [
            'label'      => __( 'Border Radius', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 4 ],
            'selectors'  => [ '{{WRAPPER}} .uc-slide-btn' => 'border-radius: {{SIZE}}px;' ],
        ]);

        $this->add_responsive_control( 'btn_font_size', [
            'label'      => __( 'Font Size', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem' ],
            'range'      => [
                'px'  => [ 'min' => 10, 'max' => 30 ],
                'em'  => [ 'min' => 0.6, 'max' => 2 ],
                'rem' => [ 'min' => 0.6, 'max' => 2 ],
            ],
            'default'    => [
                'size' => 14,
                'unit' => 'px',
            ],
            'selectors'  => [
                '{{WRAPPER}} .uc-slide-btn' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control( 'btn_padding', [
            'label'      => __( 'Padding', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [
                'top'    => 10,
                'right'  => 28,
                'bottom' => 10,
                'left'   => 28,
                'unit'   => 'px',
            ],
            'selectors'  => [
                '{{WRAPPER}} .uc-slide-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control( 'btn_hover_heading', [
            'label' => __( 'Hover', 'uc-dynamic-slider' ),
            'type'  => Controls_Manager::HEADING,
        ]);

        $this->add_control( 'btn_hover_text_color', [
            'label'     => __( 'Text Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .uc-slide-btn:hover' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control( 'btn_hover_bg_color', [
            'label'     => __( 'Background Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .uc-slide-btn:hover' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'btn_hover_border_color', [
            'label'     => __( 'Border Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .uc-slide-btn:hover' => 'border-color: {{VALUE}};' ],
        ]);

        $this->end_controls_section();

        /* ────────────────────────────────────────────
         *  SECTION: ARROWS STYLE
         * ──────────────────────────────────────────── */
        $this->start_controls_section( 'section_arrows_style', [
            'label'     => __( 'Arrows', 'uc-dynamic-slider' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_arrows' => 'yes' ],
        ]);

        $this->add_control( 'arrow_color', [
            'label'     => __( 'Icon Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .uc-slider-prev svg, {{WRAPPER}} .uc-slider-next svg' => 'fill: {{VALUE}};',
            ],
        ]);

        $this->add_control( 'arrow_bg', [
            'label'     => __( 'Background', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(0,0,0,0.35)',
            'selectors' => [
                '{{WRAPPER}} .uc-slider-prev, {{WRAPPER}} .uc-slider-next' => 'background: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control( 'arrow_size', [
            'label'      => __( 'Button Size', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 44 ],
            'selectors'  => [
                '{{WRAPPER}} .uc-slider-prev, {{WRAPPER}} .uc-slider-next' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
            ],
        ]);

        $this->add_responsive_control( 'arrow_vertical_pos', [
            'label'      => __( 'Vertical Position', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ '%', 'px' ],
            'range'      => [
                '%'  => [ 'min' => 0, 'max' => 100 ],
                'px' => [ 'min' => 0, 'max' => 500 ],
            ],
            'default'    => [ 'unit' => '%', 'size' => 50 ],
            'selectors'  => [
                '{{WRAPPER}} .uc-slider-prev, {{WRAPPER}} .uc-slider-next' => 'top: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        /* ────────────────────────────────────────────
         *  SECTION: DOTS STYLE
         * ──────────────────────────────────────────── */
        $this->start_controls_section( 'section_dots_style', [
            'label'     => __( 'Dots', 'uc-dynamic-slider' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_dots' => 'yes' ],
        ]);

        $this->add_control( 'dot_color', [
            'label'     => __( 'Inactive Dot Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(255,255,255,0.4)',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet' => 'background: {{VALUE}};',
            ],
        ]);

        $this->add_control( 'dot_active_color', [
            'label'     => __( 'Active Dot Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
            ],
        ]);

        $this->add_control( 'dot_border_heading', [
            'label'     => __( 'Border', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control( 'dot_border', [
            'label'        => __( 'Show Border', 'uc-dynamic-slider' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'uc-dynamic-slider' ),
            'label_off'    => __( 'No', 'uc-dynamic-slider' ),
            'return_value' => 'yes',
            'default'      => '',
        ]);

        $this->add_control( 'dot_border_color', [
            'label'     => __( 'Inactive Border Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'condition' => [ 'dot_border' => 'yes' ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet' => 'border: 2px solid {{VALUE}};',
            ],
        ]);

        $this->add_control( 'dot_active_border_color', [
            'label'     => __( 'Active Border Color', 'uc-dynamic-slider' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'condition' => [ 'dot_border' => 'yes' ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet-active' => 'border: 2px solid {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control( 'dot_size', [
            'label'      => __( 'Dot Size', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 8 ],
            'selectors'  => [
                '{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
            ],
        ]);

        $this->add_responsive_control( 'dots_vertical_pos', [
            'label'      => __( 'Vertical Position', 'uc-dynamic-slider' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [
                'px' => [ 'min' => 0, 'max' => 100 ],
                '%'  => [ 'min' => 0, 'max' => 100 ],
            ],
            'default'    => [ 'unit' => 'px', 'size' => 16 ],
            'selectors'  => [
                '{{WRAPPER}} .uc-slider-dots' => 'bottom: {{SIZE}}{{UNIT}} !important;',
            ],
        ]);

        $this->end_controls_section();
    }

    private function get_video_embed_url( $url, $mute = true, $loop = true ) {
        if ( empty( $url ) ) return '';

        // YouTube
        if ( preg_match( '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches ) ) {
            $video_id = $matches[1];
            $params = [
                'autoplay' => 1,
                'mute'     => $mute ? 1 : 0,
                'controls' => 0,
                'rel'      => 0,
                'enablejsapi' => 1,
            ];
            if ( $loop ) {
                $params['loop'] = 1;
                $params['playlist'] = $video_id;
            }
            return add_query_arg( $params, 'https://www.youtube.com/embed/' . $video_id );
        }

        // Vimeo
        if ( preg_match( '/vimeo\.com\/(?:.*\/)?([0-9]+)/', $url, $matches ) ) {
            $video_id = $matches[1];
            $params = [
                'autoplay'  => 1,
                'muted'     => $mute ? 1 : 0,
                'loop'      => $loop ? 1 : 0,
                'autopause' => 0,
                'background'=> 1,
            ];
            return add_query_arg( $params, 'https://player.vimeo.com/video/' . $video_id );
        }

        return esc_url( $url );
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $slides   = $settings['slides'];

        if ( empty( $slides ) ) return;

        $autoplay        = $settings['autoplay'] === 'yes';
        $autoplay_speed  = (int) $settings['autoplay_speed'];
        $pause_on_hover  = $settings['pause_on_hover'] === 'yes';
        $transition_speed= (int) $settings['transition_speed'];
        $show_arrows     = $settings['show_arrows'] === 'yes';
        $show_dots       = $settings['show_dots'] === 'yes';
        $loop            = $settings['loop'] === 'yes';
        $keyboard_nav    = $settings['keyboard_nav'] === 'yes';
        $slide_effect    = $settings['slide_effect'] ?: 'slide';
        $caption_pos     = $settings['caption_position'];
        $unique_id       = 'uc-slider-' . $this->get_id();

        $autoplay_config = false;
        if ( $autoplay ) {
            $autoplay_config = [
                'delay'                => $autoplay_speed,
                'disableOnInteraction' => false,
            ];
            if ( $pause_on_hover ) {
                $autoplay_config['pauseOnMouseEnter'] = true;
            }
        }

        $swiper_config = wp_json_encode([
            'loop'            => $loop,
            'effect'          => $slide_effect,
            'speed'           => $transition_speed,
            'autoplay'        => $autoplay_config,
            'keyboard'        => $keyboard_nav ? [ 'enabled' => true ] : false,
            'navigation' => $show_arrows ? [
                'nextEl' => '#' . $unique_id . ' .uc-slider-next',
                'prevEl' => '#' . $unique_id . ' .uc-slider-prev'
            ] : false,
            'pagination' => $show_dots ? [
                'el' => '#' . $unique_id . ' .uc-slider-dots',
                'clickable' => true
            ] : false,
            'grabCursor'      => true,
        ]);
        ?>
        <div class="uc-slider-outer" id="<?php echo esc_attr( $unique_id ); ?>" data-swiper='<?php echo esc_attr( $swiper_config ); ?>'>
            <div class="uc-slider-wrapper swiper">
                <div class="swiper-wrapper">
                    <?php foreach ( $slides as $index => $slide ) :
                        $slide_type = ! empty( $slide['slide_type'] ) ? $slide['slide_type'] : 'image';
                        $img_url    = ! empty( $slide['slide_image']['url'] ) ? $slide['slide_image']['url'] : Utils::get_placeholder_image_src();
                        $video_url  = ! empty( $slide['slide_video']['url'] ) ? $slide['slide_video']['url'] : '';
                        $ext_url    = ! empty( $slide['slide_video_url'] ) ? $slide['slide_video_url'] : '';
                        $is_loop    = ( ! isset( $slide['slide_video_loop'] ) || $slide['slide_video_loop'] === 'yes' );
                        $is_mute    = ( ! isset( $slide['slide_video_mute'] ) || $slide['slide_video_mute'] === 'yes' );

                        $has_link   = ! empty( $slide['slide_link']['url'] );
                        $target     = ! empty( $slide['slide_link']['is_external'] ) ? '_blank' : '_self';
                        $norel      = ! empty( $slide['slide_link']['nofollow'] ) ? 'nofollow' : '';
                    ?>
                    <div class="swiper-slide uc-slide uc-slide-type-<?php echo esc_attr( $slide_type ); ?>">
                        <?php if ( 'video' === $slide_type && $video_url ) : ?>
                            <video class="uc-slide-video"
                                   autoplay
                                   playsinline
                                   <?php echo $is_mute ? 'muted' : ''; ?>
                                   <?php echo $is_loop ? 'loop' : ''; ?>
                                   poster="<?php echo esc_url( $img_url ); ?>">
                                <source src="<?php echo esc_url( $video_url ); ?>">
                            </video>
                        <?php elseif ( 'external' === $slide_type && $ext_url ) :
                            $embed_url = $this->get_video_embed_url( $ext_url, $is_mute, $is_loop );
                        ?>
                            <iframe class="uc-slide-iframe"
                                    src="<?php echo esc_url( $embed_url ); ?>"
                                    frameborder="0"
                                    allow="autoplay; encrypted-media; picture-in-picture"
                                    allowfullscreen></iframe>
                        <?php else : ?>
                            <div class="uc-slide-bg" style="background-image: url('<?php echo esc_url( $img_url ); ?>'); background-position: center; background-repeat: no-repeat;"></div>
                        <?php endif; ?>
                        
                        <div class="uc-slide-overlay"></div>
                        <div class="uc-slide-caption uc-caption-<?php echo esc_attr( $caption_pos ); ?>">
                            <?php if ( ! empty( $slide['slide_title'] ) ) : ?>
                                <h2 class="uc-slide-title"><?php echo wp_kses_post( $slide['slide_title'] ); ?></h2>
                            <?php endif; ?>
                            <?php if ( ! empty( $slide['slide_description'] ) ) : ?>
                                <p class="uc-slide-desc"><?php echo wp_kses_post( $slide['slide_description'] ); ?></p>
                            <?php endif; ?>
                            <?php if ( $has_link ) : ?>
                                <a class="uc-slide-btn"
                                   href="<?php echo esc_url( $slide['slide_link']['url'] ); ?>"
                                   target="<?php echo esc_attr( $target ); ?>"
                                   <?php if ( $norel ) echo 'rel="nofollow"'; ?>>
                                    <?php echo esc_html( $slide['slide_link_label'] ?: 'Learn More' ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ( $show_dots ) : ?>
                    <div class="swiper-pagination uc-slider-dots"></div>
                <?php endif; ?>

                <?php if ( $show_arrows ) : ?>
                    <button type="button" class="uc-slider-prev" aria-label="Previous slide">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                    </button>
                    <button type="button" class="uc-slider-next" aria-label="Next slide">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php
    }
}
