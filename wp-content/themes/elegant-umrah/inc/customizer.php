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
} );
