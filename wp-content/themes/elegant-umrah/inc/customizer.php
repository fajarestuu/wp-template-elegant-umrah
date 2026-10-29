<?php
/**
 * Theme Customizer — Language Settings
 *
 * Adds a "Language & Translation" section to the WordPress Customizer
 * with:
 *   - Default Language selector (dropdown)
 *   - Auto-detect from visitor location (checkbox)
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ): void {

    // ─── Section ────────────────────────────────────────────────────────────
    $wp_customize->add_section( 'eu_language_settings', [
        'title'       => __( 'Language & Translation', 'elegant-umrah' ),
        'description' => __( 'Control how the site language is determined for your visitors.', 'elegant-umrah' ),
        'priority'    => 30,
        'panel'       => '', // top-level section
    ] );

    // ─── Setting: Default Language ───────────────────────────────────────────
    $wp_customize->add_setting( 'eu_default_language', [
        'default'           => 'en',
        'sanitize_callback' => function ( $value ): string {
            return in_array( $value, array_keys( eu_supported_locales() ), true ) ? $value : 'en';
        },
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'eu_default_language', [
        'label'       => __( 'Default Language', 'elegant-umrah' ),
        'description' => __( 'The fallback language shown when no visitor preference is detected.', 'elegant-umrah' ),
        'section'     => 'eu_language_settings',
        'type'        => 'select',
        'choices'     => eu_supported_locales(),
        'priority'    => 10,
    ] );

    // ─── Setting: Auto-detect from user location ─────────────────────────────
    $wp_customize->add_setting( 'eu_lang_auto_detect', [
        'default'           => false,
        'sanitize_callback' => function ( $value ): bool {
            return (bool) $value;
        },
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'eu_lang_auto_detect', [
        'label'       => __( 'Auto-detect language from visitor location', 'elegant-umrah' ),
        'description' => __( 'When enabled, Indonesian visitors will see the Indonesian version automatically. All other visitors default to English. The result is cached per visitor — no performance overhead on repeat pages.', 'elegant-umrah' ),
        'section'     => 'eu_language_settings',
        'type'        => 'checkbox',
        'priority'    => 20,
    ] );

    // ─── Section: Contact & WhatsApp ─────────────────────────────────────────
    $wp_customize->add_section( 'eu_contact_settings', [
        'title'       => __( 'Contact & WhatsApp', 'elegant-umrah' ),
        'description' => __( 'Configure the WhatsApp number used for package booking buttons across the theme.', 'elegant-umrah' ),
        'priority'    => 35,
    ] );

    $wp_customize->add_setting( 'eu_whatsapp_number', [
        'default'           => '621234567890',
        'sanitize_callback' => function ( $value ): string {
            return preg_replace( '/\D+/', '', (string) $value ) ?: '621234567890';
        },
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'eu_whatsapp_number', [
        'label'       => __( 'WhatsApp Number (with country code)', 'elegant-umrah' ),
        'description' => __( 'Digits only, e.g. 6281234567890', 'elegant-umrah' ),
        'section'     => 'eu_contact_settings',
        'type'        => 'text',
        'priority'    => 10,
    ] );

    // ─── Section: Site Identity Enhancements ──────────────────────────────────
    // Adds custom logo URL and icon fallback into standard 'title_tagline' section
    $wp_customize->add_setting( 'eu_custom_logo_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'eu_custom_logo_url', [
        'label'       => __( 'Logo Image Link (URL)', 'elegant-umrah' ),
        'description' => __( 'Optional: Paste a direct image URL (e.g. from CDN or external hosting) if not uploading via Media Library.', 'elegant-umrah' ),
        'section'     => 'title_tagline',
        'type'        => 'url',
        'priority'    => 9,
    ] );

    $wp_customize->add_setting( 'eu_logo_icon_text', [
        'default'           => '🕌',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'eu_logo_icon_text', [
        'label'       => __( 'Default Logo Icon / Emoji', 'elegant-umrah' ),
        'description' => __( 'Shown if no custom logo image is uploaded/linked (e.g. 🕌, 🕋, ✈️).', 'elegant-umrah' ),
        'section'     => 'title_tagline',
        'type'        => 'text',
        'priority'    => 11,
    ] );

    $wp_customize->add_setting( 'eu_show_title_with_logo', [
        'default'           => true,
        'sanitize_callback' => function ( $value ): bool {
            return (bool) $value;
        },
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'eu_show_title_with_logo', [
        'label'       => __( 'Display Site Title & Tagline alongside Logo', 'elegant-umrah' ),
        'description' => __( 'Check to display site title text next to logo. Uncheck if your logo image already contains the text.', 'elegant-umrah' ),
        'section'     => 'title_tagline',
        'type'        => 'checkbox',
        'priority'    => 12,
    ] );

    // ─── Section: Hero Background & Slideshow ─────────────────────────────────
    $wp_customize->add_section( 'eu_hero_settings', [
        'title'       => __( 'Hero Background & Slideshow', 'elegant-umrah' ),
        'description' => __( 'Customize the hero background. Set 1 image for a static hero, or multiple images for an animated slideshow. You can upload images or paste direct image URLs.', 'elegant-umrah' ),
        'priority'    => 25,
    ] );

    // Bulk image URLs (one per line)
    $wp_customize->add_setting( 'eu_hero_image_urls', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'eu_hero_image_urls', [
        'label'       => __( 'Direct Image Links / CDN URLs (One per line)', 'elegant-umrah' ),
        'description' => __( 'Paste one or more image URLs (e.g. https://domain.com/photo1.jpg). If filled, these images will be used in the hero slideshow.', 'elegant-umrah' ),
        'section'     => 'eu_hero_settings',
        'type'        => 'textarea',
        'priority'    => 5,
    ] );

    // Up to 5 image slots (Media upload + URL fallback)
    for ( $i = 1; $i <= 5; $i++ ) {
        // Image Upload via Media Library
        $wp_customize->add_setting( "eu_hero_image_{$i}", [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ] );

        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "eu_hero_image_{$i}", [
            'label'       => sprintf( __( 'Hero Image %d (Media Upload)', 'elegant-umrah' ), $i ),
            'description' => $i === 1 ? __( 'Main hero background image.', 'elegant-umrah' ) : sprintf( __( 'Slide %d image (enables slideshow when 2+ images are set).', 'elegant-umrah' ), $i ),
            'section'     => 'eu_hero_settings',
            'priority'    => 10 + ( $i * 2 ),
        ] ) );

        // Direct URL alternative for this slot
        $wp_customize->add_setting( "eu_hero_image_url_{$i}", [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ] );

        $wp_customize->add_control( "eu_hero_image_url_{$i}", [
            'label'       => sprintf( __( 'Hero Image %d Direct URL (Optional)', 'elegant-umrah' ), $i ),
            'section'     => 'eu_hero_settings',
            'type'        => 'url',
            'priority'    => 11 + ( $i * 2 ),
        ] );
    }

    // Overlay darkness opacity
    $wp_customize->add_setting( 'eu_hero_overlay_opacity', [
        'default'           => '0.70',
        'sanitize_callback' => function ( $value ): string {
            $num = (float) $value;
            return (string) min( 0.95, max( 0.20, $num ) );
        },
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'eu_hero_overlay_opacity', [
        'label'       => __( 'Hero Overlay Darkness (0.20 - 0.95)', 'elegant-umrah' ),
        'description' => __( 'Controls how dark the background overlay is to ensure title and search bar legibility. Default is 0.70.', 'elegant-umrah' ),
        'section'     => 'eu_hero_settings',
        'type'        => 'number',
        'input_attrs' => [
            'min'  => '0.20',
            'max'  => '0.95',
            'step' => '0.05',
        ],
        'priority'    => 30,
    ] );

    // Slideshow interval (seconds)
    $wp_customize->add_setting( 'eu_hero_slide_interval', [
        'default'           => '5',
        'sanitize_callback' => function ( $value ): int {
            $num = absint( $value );
            return min( 15, max( 3, $num ) );
        },
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'eu_hero_slide_interval', [
        'label'       => __( 'Slideshow Interval (Seconds)', 'elegant-umrah' ),
        'description' => __( 'How many seconds each slide is displayed before transitioning to the next (3 to 15 seconds). Default is 5s.', 'elegant-umrah' ),
        'section'     => 'eu_hero_settings',
        'type'        => 'number',
        'input_attrs' => [
            'min'  => '3',
            'max'  => '15',
            'step' => '1',
        ],
        'priority'    => 35,
    ] );
} );
