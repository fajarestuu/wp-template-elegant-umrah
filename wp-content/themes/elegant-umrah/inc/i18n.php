<?php
/**
 * i18n — Language Detection & Fallback
 *
 * Handles:
 *  1. Reading the admin's Customizer settings (default lang, geo-IP toggle)
 *  2. Geo-IP country lookup on first visit → stored in a session cookie (eu_lang)
 *  3. English fallback if translation is missing
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Return the list of supported locales.
 * key = WordPress locale string, value = display label.
 */
function eu_supported_locales(): array {
    return [
        'id' => 'Bahasa Indonesia',
        'en' => 'English',
    ];
}

/**
 * Return the admin-configured default locale (from Customizer).
 * Falls back to 'en' if nothing is set.
 */
function eu_default_locale(): string {
    return get_theme_mod( 'eu_default_language', 'en' );
}

/**
 * Detect the visitor's preferred locale.
 *
 * Priority order:
 *   1. Explicit ?lang= query param (for switcher links / Polylang)
 *   2. Session cookie (set on first visit)
 *   3. Geo-IP lookup (if the admin enabled auto-detect)
 *   4. Admin's configured default language
 *   5. Hard fallback → 'en'
 *
 * @return string  Two-letter locale code, e.g. 'id' or 'en'.
 */
function eu_detect_locale(): string {
    $supported = array_keys( eu_supported_locales() );

    // 1. Explicit query param — highest priority, honours language switcher
    if ( ! empty( $_GET['lang'] ) ) {
        $lang = sanitize_key( $_GET['lang'] );
        if ( in_array( $lang, $supported, true ) ) {
            eu_set_lang_cookie( $lang );
            return $lang;
        }
    }

    // 2. Session cookie — zero latency on repeat pages
    if ( ! empty( $_COOKIE['eu_lang'] ) ) {
        $lang = sanitize_key( $_COOKIE['eu_lang'] );
        if ( in_array( $lang, $supported, true ) ) {
            return $lang;
        }
    }

    // 3. Geo-IP lookup (only if admin opted-in)
    if ( get_theme_mod( 'eu_lang_auto_detect', false ) ) {
        $lang = eu_geoip_detect_locale();
        if ( $lang && in_array( $lang, $supported, true ) ) {
            eu_set_lang_cookie( $lang );
            return $lang;
        }
    }

    // 4. Admin default
    $default = eu_default_locale();
    eu_set_lang_cookie( $default );
    return $default;
}

/**
 * Detect visitor locale from edge/server headers or Accept-Language header
 * without making blocking external HTTP calls.
 *
 * @return string|null  Two-letter locale code, or null on failure.
 */
function eu_geoip_detect_locale(): ?string {
    $country_locale_map = [
        'ID' => 'id', // Indonesia
    ];

    // 1. Check reverse-proxy / CDN country header (e.g. Cloudflare)
    if ( ! empty( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) {
        $country = strtoupper( sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) );
        if ( isset( $country_locale_map[ $country ] ) ) {
            return $country_locale_map[ $country ];
        }
    }

    // 2. Fallback to browser Accept-Language header
    if ( ! empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
        $accept = strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) );
        if ( strpos( $accept, 'id' ) === 0 || strpos( $accept, ',id' ) !== false ) {
            return 'id';
        }
    }

    return null;
}

/**
 * Initialize locale detection early on 'init' so setcookie() runs before HTTP headers are sent.
 */
add_action( 'init', function (): void {
    if ( ! is_admin() ) {
        eu_locale();
    }
}, 5 );

/**
 * Set the eu_lang cookie for 30 days (session-length for language preference).
 */
function eu_set_lang_cookie( string $lang ): void {
    if ( headers_sent() ) return;
    setcookie( 'eu_lang', $lang, [
        'expires'  => time() + 30 * DAY_IN_SECONDS,
        'path'     => COOKIEPATH ?: '/',
        'domain'   => COOKIE_DOMAIN ?: '',
        'secure'   => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax',
    ] );
}

/**
 * Get the active locale for the current request.
 * Cached in a static variable so the detection only runs once per request.
 */
function eu_locale(): string {
    static $locale = null;
    if ( null === $locale ) {
        $locale = eu_detect_locale();
    }
    return $locale;
}

/**
 * Translate a string using our locale.
 * Falls back to the $default (English) string if no translation is registered.
 *
 * Usage:
 *   eu_t( 'Book This Package', [ 'id' => 'Pesan Paket Ini' ] )
 *
 * @param  string  $default      The English (fallback) string.
 * @param  array   $translations Keyed by locale code, e.g. ['id' => '...'].
 * @return string
 */
function eu_t( string $default, array $translations = [] ): string {
    $locale = eu_locale();
    return $translations[ $locale ] ?? $translations['en'] ?? $default;
}

/**
 * Echo eu_t(), HTML-escaped.
 */
function eu_te( string $default, array $translations = [] ): void {
    echo esc_html( eu_t( $default, $translations ) );
}
