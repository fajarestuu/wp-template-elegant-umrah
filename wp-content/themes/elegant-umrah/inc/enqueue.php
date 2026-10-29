<?php
/**
 * Assets Enqueue
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Enqueue frontend styles and scripts
 */
function eu_enqueue_assets() {
    // Google Fonts - Poppins (preconnect for performance)
    wp_enqueue_style(
        'eu-google-fonts',
        'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap',
        [],
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'elegant-umrah-style',
        get_stylesheet_uri(),
        [ 'eu-google-fonts' ],
        EU_VERSION
    );

    // Main JS
    wp_enqueue_script(
        'elegant-umrah-main',
        EU_ASSETS_URI . '/js/main.js',
        [],
        EU_VERSION,
        [ 'strategy' => 'defer', 'in_footer' => true ]
    );

    // Localize script
    wp_localize_script( 'elegant-umrah-main', 'euTheme', [
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'eu_nonce' ),
        'homeUrl'   => home_url(),
        'themeUri'  => EU_URI,
        'isRtl'     => is_rtl() ? 'true' : 'false',
    ] );

    // Localize article filter data (available on blog/archive pages)
    if ( is_home() || is_archive() || is_singular( 'post' ) ) {
        wp_localize_script( 'elegant-umrah-main', 'euArticles', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'eu_nonce' ),
            'action'  => 'eu_filter_articles',
            'i18n'    => [
                'loading'  => __( 'Loading articles…', 'elegant-umrah' ),
                'error'    => __( 'Something went wrong. Please try again.', 'elegant-umrah' ),
            ],
        ] );
    }

    // Comment reply script
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    // Packages archive extra JS
    if ( is_post_type_archive( 'umrah_package' ) || is_page_template( 'templates/page-packages.html' ) ) {
        wp_enqueue_script(
            'eu-packages-filter',
            EU_ASSETS_URI . '/js/packages-filter.js',
            [ 'elegant-umrah-main' ],
            EU_VERSION,
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'eu_enqueue_assets' );


/**
 * Preconnect to Google Fonts
 */
function eu_preconnect_google_fonts( array $hints, string $relation_type ): array {
    if ( 'preconnect' === $relation_type ) {
        $hints[] = [
            'href'        => 'https://fonts.googleapis.com',
            'crossorigin' => '',
        ];
        $hints[] = [
            'href'        => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        ];
    }
    return $hints;
}
add_filter( 'wp_resource_hints', 'eu_preconnect_google_fonts', 10, 2 );

/**
 * Admin styles for meta boxes
 */
function eu_admin_enqueue_assets( string $hook ) {
    global $post_type;

    $allowed_post_types = [ 'umrah_package', 'post', 'page' ];

    if ( in_array( $hook, [ 'post.php', 'post-new.php' ] )
         && in_array( $post_type, $allowed_post_types, true ) ) {
        wp_enqueue_style(
            'eu-admin-style',
            EU_ASSETS_URI . '/css/admin.css',
            [],
            EU_VERSION
        );
        wp_enqueue_media();
        wp_enqueue_script(
            'eu-admin-js',
            EU_ASSETS_URI . '/js/admin.js',
            [ 'jquery' ],
            EU_VERSION,
            true
        );
    }
}
add_action( 'admin_enqueue_scripts', 'eu_admin_enqueue_assets' );
