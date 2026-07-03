<?php
/**
 * Elegant Umrah - Main Theme Functions
 *
 * @package ElegantUmrah
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// =========================================
// THEME CONSTANTS
// =========================================
define( 'EU_VERSION',    '1.0.0' );
define( 'EU_DIR',        get_template_directory() );
define( 'EU_URI',        get_template_directory_uri() );
define( 'EU_INC',        EU_DIR . '/inc' );
define( 'EU_ASSETS_URI', EU_URI . '/assets' );
define( 'EU_TEXT_DOMAIN', 'elegant-umrah' );

// =========================================
// LOAD INCLUDES
// =========================================
$eu_includes = [
    '/inc/setup.php',
    '/inc/enqueue.php',
    '/inc/post-types.php',
    '/inc/taxonomies.php',
    '/inc/helpers.php',
    '/inc/fields/field-core.php',
    '/inc/fields/field-types.php',
    '/inc/fields/field-render.php',
    '/inc/fields/field-save.php',
    '/inc/fields/field-helpers.php',
    '/inc/fields/field-groups.php',
    '/inc/admin-columns.php',        // Custom admin list columns + Quick Edit
];

foreach ( $eu_includes as $file ) {
    $path = get_template_directory() . $file;
    if ( file_exists( $path ) ) {
        require_once $path;
    }
}

// =========================================
// BLOCK PATTERNS LOADER
// =========================================
// WordPress natively auto-loads patterns from the /patterns/ directory.

// =========================================
// CUSTOM BLOCKS REGISTRATION
// =========================================
add_action( 'init', function() {
    $blocks_dir = get_template_directory() . '/blocks/';
    if ( ! is_dir( $blocks_dir ) ) return;

    foreach ( glob( $blocks_dir . '*/block.json' ) as $block_json ) {
        register_block_type( dirname( $block_json ) );
    }

    // Register custom block category
    add_filter( 'block_categories_all', function( array $categories ): array {
        return array_merge(
            [
                [
                    'slug'  => 'elegant-umrah',
                    'title' => __( 'Elegant Umrah', 'elegant-umrah' ),
                    'icon'  => 'star-filled',
                ],
            ],
            $categories
        );
    } );
} );

// =========================================
// AJAX: FILTER PACKAGES
// =========================================
add_action( 'wp_ajax_eu_filter_packages', 'eu_ajax_filter_packages' );
add_action( 'wp_ajax_nopriv_eu_filter_packages', 'eu_ajax_filter_packages' );

function eu_ajax_filter_packages(): void {
    check_ajax_referer( 'eu_nonce', 'nonce' );

    $package_type = sanitize_text_field( $_POST['package_type'] ?? 'all' );

    $args = [
        'post_type'      => 'umrah_package',
        'posts_per_page' => 9,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if ( $package_type && $package_type !== 'all' ) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'package_type',
                'field'    => 'slug',
                'terms'    => $package_type,
            ],
        ];
    }

    $query = new WP_Query( $args );

    ob_start();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            eu_render_package_card( get_the_ID() );
        }
        wp_reset_postdata();
    } else {
        echo '<div class="eu-no-results" style="text-align:center;padding:3rem;grid-column:1/-1">';
        echo '<p style="font-size:2rem">🔍</p>';
        echo '<h3>' . esc_html__( 'No packages found.', 'elegant-umrah' ) . '</h3>';
        echo '<p>' . esc_html__( 'Try a different filter or check back soon.', 'elegant-umrah' ) . '</p>';
        echo '</div>';
    }

    $html = ob_get_clean();

    wp_send_json_success( [ 'html' => $html ] );
}

// =========================================
// BLOCK EDITOR SUPPORT
// =========================================
add_action( 'enqueue_block_editor_assets', function() {
    wp_enqueue_style(
        'eu-editor-styles',
        EU_ASSETS_URI . '/css/editor.css',
        [],
        EU_VERSION
    );
} );

// =========================================
// THEME SETUP HOOK (FALLBACK)
// =========================================
if ( ! function_exists( 'eu_setup' ) ) {
    add_action( 'after_setup_theme', function() {
        // Ensure setup.php was loaded
        load_theme_textdomain( EU_TEXT_DOMAIN, EU_DIR . '/languages' );
    } );
}
