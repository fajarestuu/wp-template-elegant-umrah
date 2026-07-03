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
 * Get a custom field value (mirrors ACF's get_field)
 *
 * @param  string   $key      Meta key (field name)
 * @param  int|null $post_id  Post ID. Defaults to current post.
 * @param  mixed    $default  Default value if not set
 * @return mixed
 */
function get_field( string $key, ?int $post_id = null, $default = '' ) {
    $post_id = $post_id ?? get_the_ID();

    if ( ! $post_id ) return $default;

    $value = get_post_meta( $post_id, $key, true );

    return ( $value !== '' && $value !== null && $value !== false ) ? $value : $default;
}

/**
 * Echo a custom field value (mirrors ACF's the_field)
 *
 * @param  string   $key      Meta key
 * @param  int|null $post_id  Post ID
 * @param  string   $default  Default value
 */
function the_field( string $key, ?int $post_id = null, string $default = '' ): void {
    $value = get_field( $key, $post_id, $default );
    echo esc_html( (string) $value );
}

/**
 * Get a field and run it through wp_kses_post (for HTML values)
 *
 * @param  string   $key
 * @param  int|null $post_id
 * @param  string   $default
 * @return string
 */
function get_field_html( string $key, ?int $post_id = null, string $default = '' ): string {
    $value = get_field( $key, $post_id, $default );
    return wp_kses_post( (string) $value );
}

/**
 * Echo an HTML field value
 */
function the_field_html( string $key, ?int $post_id = null, string $default = '' ): void {
    echo get_field_html( $key, $post_id, $default ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Check if a field has a value
 *
 * @param  string   $key
 * @param  int|null $post_id
 * @return bool
 */
function has_field( string $key, ?int $post_id = null ): bool {
    $value = get_field( $key, $post_id, null );
    return ! ( $value === null || $value === '' || $value === false );
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
function get_field_image( string $key, ?int $post_id = null, string $size = 'eu-card', string $class = '' ): string {
    $attachment_id = (int) get_field( $key, $post_id, 0 );
    if ( ! $attachment_id ) return '';

    return wp_get_attachment_image( $attachment_id, $size, false, [
        'class'   => $class,
        'loading' => 'lazy',
    ] );
}

/**
 * Get image URL from an image field
 *
 * @param  string   $key
 * @param  int|null $post_id
 * @param  string   $size
 * @return string
 */
function get_field_image_url( string $key, ?int $post_id = null, string $size = 'eu-card' ): string {
    $attachment_id = (int) get_field( $key, $post_id, 0 );
    if ( ! $attachment_id ) return '';
    $src = wp_get_attachment_image_src( $attachment_id, $size );
    return $src ? $src[0] : '';
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
function get_fields( array $keys, ?int $post_id = null ): array {
    $result = [];
    foreach ( $keys as $key ) {
        $result[ $key ] = get_field( $key, $post_id );
    }
    return $result;
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
        'price'          => get_field( 'eu_price', $post_id ),
        'duration'       => get_field( 'eu_duration', $post_id ),
        'departure_date' => get_field( 'eu_departure_date', $post_id ),
        'hotel_info'     => get_field( 'eu_hotel_info', $post_id ),
        'airline'        => get_field( 'eu_airline', $post_id ),
        'seats'          => get_field( 'eu_seats_available', $post_id ),
        'is_featured'    => (bool) get_field( 'eu_is_featured', $post_id ),
        'package_type'   => get_field( 'eu_package_type', $post_id ),
        'inclusions'     => get_field( 'eu_inclusions', $post_id ),
        'schedule'       => get_field( 'eu_schedule_json', $post_id ),
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
        'title'      => get_field( 'eu_hero_title', $post_id ),
        'subtitle'   => get_field( 'eu_hero_subtitle', $post_id ),
        'background' => (int) get_field( 'eu_hero_background', $post_id ),
        'cta_text'   => get_field( 'eu_cta_text', $post_id ),
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
        'featured_label'       => get_field( 'eu_featured_label', $post_id ),
        'islamic_category_tag' => get_field( 'eu_islamic_category_tag', $post_id ),
    ];
}
