<?php
/**
 * Field Helpers - ACF-compatible API surface
 *
 * Provides get_field() / the_field() equivalents
 * for use in templates and blocks
 *
 * @package ElegantUmrah\Fields
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ─────────────────────────────────────────────────────────────────────────────
// CORE API - ACF-compatible wrappers
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Get a custom field value (prefixed core function)
 *
 * @param  string   $key      Meta key (field name)
 * @param  int|null $post_id  Post ID. Defaults to current post.
 * @param  mixed    $default  Default value if not set
 * @return mixed
 */
function eu_get_field( string $key, ?int $post_id = null, $default = '' ) {
    $post_id = $post_id ?? get_the_ID();

    if ( ! $post_id ) return $default;

    $value = get_post_meta( $post_id, $key, true );

    return ( $value !== '' && $value !== null && $value !== false ) ? $value : $default;
}

if ( ! function_exists( 'get_field' ) ) {
    function get_field( string $key, ?int $post_id = null, $default = '' ) {
        return eu_get_field( $key, $post_id, $default );
    }
}

/**
 * Echo a custom field value
 *
 * @param  string   $key      Meta key
 * @param  int|null $post_id  Post ID
 * @param  string   $default  Default value
 */
function eu_the_field( string $key, ?int $post_id = null, string $default = '' ): void {
    $value = eu_get_field( $key, $post_id, $default );
    echo esc_html( (string) $value );
}

if ( ! function_exists( 'the_field' ) ) {
    function the_field( string $key, ?int $post_id = null, string $default = '' ): void {
        eu_the_field( $key, $post_id, $default );
    }
}

/**
 * Get a field and run it through wp_kses_post (for HTML values)
 *
 * @param  string   $key
 * @param  int|null $post_id
 * @param  string   $default
 * @return string
 */
function eu_get_field_html( string $key, ?int $post_id = null, string $default = '' ): string {
    $value = eu_get_field( $key, $post_id, $default );
    return wp_kses_post( (string) $value );
}

if ( ! function_exists( 'get_field_html' ) ) {
    function get_field_html( string $key, ?int $post_id = null, string $default = '' ): string {
        return eu_get_field_html( $key, $post_id, $default );
    }
}

/**
 * Echo an HTML field value
 */
function eu_the_field_html( string $key, ?int $post_id = null, string $default = '' ): void {
    echo eu_get_field_html( $key, $post_id, $default ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

if ( ! function_exists( 'the_field_html' ) ) {
    function the_field_html( string $key, ?int $post_id = null, string $default = '' ): void {
        eu_the_field_html( $key, $post_id, $default );
    }
}

/**
 * Check if a field has a value
 *
 * @param  string   $key
 * @param  int|null $post_id
 * @return bool
 */
function eu_has_field( string $key, ?int $post_id = null ): bool {
    $value = eu_get_field( $key, $post_id, null );
    return ! ( $value === null || $value === '' || $value === false );
}

if ( ! function_exists( 'has_field' ) ) {
    function has_field( string $key, ?int $post_id = null ): bool {
        return eu_has_field( $key, $post_id );
    }
}

/**
 * Get an image field and return full image tag
 *
 * @param  string   $key          Meta key storing attachment ID
 * @param  int|null $post_id
 * @param  string   $size         Image size
 * @param  string   $class        CSS class
 * @return string                 HTML <img> tag or empty string
 */
function eu_get_field_image( string $key, ?int $post_id = null, string $size = 'eu-card', string $class = '' ): string {
    $attachment_id = (int) eu_get_field( $key, $post_id, 0 );
    if ( ! $attachment_id ) return '';

    return wp_get_attachment_image( $attachment_id, $size, false, [
        'class'   => $class,
        'loading' => 'lazy',
    ] );
}

if ( ! function_exists( 'get_field_image' ) ) {
    function get_field_image( string $key, ?int $post_id = null, string $size = 'eu-card', string $class = '' ): string {
        return eu_get_field_image( $key, $post_id, $size, $class );
    }
}

/**
 * Get image URL from an image field
 *
 * @param  string   $key
 * @param  int|null $post_id
 * @param  string   $size
 * @return string
 */
function eu_get_field_image_url( string $key, ?int $post_id = null, string $size = 'eu-card' ): string {
    $attachment_id = (int) eu_get_field( $key, $post_id, 0 );
    if ( ! $attachment_id ) return '';
    $src = wp_get_attachment_image_src( $attachment_id, $size );
    return $src ? $src[0] : '';
}

if ( ! function_exists( 'get_field_image_url' ) ) {
    function get_field_image_url( string $key, ?int $post_id = null, string $size = 'eu-card' ): string {
        return eu_get_field_image_url( $key, $post_id, $size );
    }
}

/**
 * Get all meta for a post as an associative array
 *
 * @param int $post_id
 * @return array
 */
function eu_get_all_fields( int $post_id ): array {
    $meta = get_post_meta( $post_id );
    $result = [];
    foreach ( $meta as $key => $values ) {
        // Filter to eu_ prefixed keys
        if ( strpos( $key, 'eu_' ) === 0 ) {
            $result[ $key ] = maybe_unserialize( $values[0] );
        }
    }
    return $result;
}

/**
 * Get multiple fields at once
 *
 * @param  array    $keys     Array of meta keys
 * @param  int|null $post_id
 * @return array              Associative array of key => value
 */
function eu_get_fields( array $keys, ?int $post_id = null ): array {
    $result = [];
    foreach ( $keys as $key ) {
        $result[ $key ] = eu_get_field( $key, $post_id );
    }
    return $result;
}

if ( ! function_exists( 'get_fields' ) ) {
    function get_fields( array $keys, ?int $post_id = null ): array {
        return eu_get_fields( $keys, $post_id );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// OPTION HELPERS (for theme options stored as options, not post meta)
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Get a theme option field
 *
 * @param  string $key
 * @param  mixed  $default
 * @return mixed
 */
function eu_get_option( string $key, $default = '' ) {
    return get_option( 'eu_' . $key, $default );
}

/**
 * Set a theme option
 */
function eu_update_option( string $key, $value ): bool {
    return update_option( 'eu_' . $key, $value );
}

// ─────────────────────────────────────────────────────────────────────────────
// PACKAGE-SPECIFIC CONVENIENCE WRAPPERS
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Get package fields as a single array
 *
 * @param int|null $post_id
 * @return array
 */
function eu_get_package_fields( ?int $post_id = null ): array {
    $post_id = $post_id ?? get_the_ID();

    return [
        'currency'             => eu_get_field( 'eu_currency', $post_id, 'Rp' ),
        'price'                => eu_get_field( 'eu_price', $post_id ),
        'discount_price'       => eu_get_field( 'eu_discount_price', $post_id ),
        'duration'             => eu_get_field( 'eu_duration', $post_id ),
        'departure_date'       => eu_get_field( 'eu_departure_date', $post_id ),
        'estimate_month'       => eu_get_field( 'eu_estimate_month', $post_id ),
        'muthawwif'            => eu_get_field( 'eu_muthawwif', $post_id ),
        'hotel_makkah'         => eu_get_field( 'eu_hotel_makkah', $post_id ),
        'hotel_makkah_rating'  => eu_get_field( 'eu_hotel_makkah_rating', $post_id ),
        'hotel_madinah'        => eu_get_field( 'eu_hotel_madinah', $post_id ),
        'hotel_madinah_rating' => eu_get_field( 'eu_hotel_madinah_rating', $post_id ),
        'airline'              => eu_get_field( 'eu_airline', $post_id ),
        'seats'                => eu_get_field( 'eu_seats_available', $post_id ),
        'is_featured'          => (bool) eu_get_field( 'eu_is_featured', $post_id ),
        'is_sold_out'          => (bool) eu_get_field( 'eu_is_sold_out', $post_id ),
        'package_type'         => eu_get_field( 'eu_package_type', $post_id ),
        'inclusions'           => eu_get_field( 'eu_inclusions', $post_id ),
        'exclusions'           => eu_get_field( 'eu_exclusions', $post_id ),
        'additional_tour'      => eu_get_field( 'eu_additional_tour', $post_id ),
        'additional_country'   => eu_get_field( 'eu_additional_country', $post_id ),
        'schedule'             => eu_get_field( 'eu_schedule_json', $post_id ),
    ];
}

/**
 * Get landing page hero fields
 *
 * @param int|null $post_id
 * @return array
 */
function eu_get_hero_fields( ?int $post_id = null ): array {
    $post_id = $post_id ?? get_the_ID();

    return [
        'title'      => eu_get_field( 'eu_hero_title', $post_id ),
        'subtitle'   => eu_get_field( 'eu_hero_subtitle', $post_id ),
        'background' => (int) eu_get_field( 'eu_hero_background', $post_id ),
        'cta_text'   => eu_get_field( 'eu_cta_text', $post_id ),
    ];
}

/**
 * Get article/post custom fields
 *
 * @param int|null $post_id
 * @return array
 */
function eu_get_article_fields( ?int $post_id = null ): array {
    $post_id = $post_id ?? get_the_ID();

    return [
        'featured_label'       => eu_get_field( 'eu_featured_label', $post_id ),
        'islamic_category_tag' => eu_get_field( 'eu_islamic_category_tag', $post_id ),
    ];
}
