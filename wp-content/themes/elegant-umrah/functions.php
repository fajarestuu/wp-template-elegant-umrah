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
define( 'EU_VERSION',    '1.0.1' );
define( 'EU_DIR',        get_template_directory() );
define( 'EU_URI',        get_template_directory_uri() );
define( 'EU_INC',        EU_DIR . '/inc' );
define( 'EU_ASSETS_URI', EU_URI . '/assets' );
define( 'EU_TEXT_DOMAIN', 'elegant-umrah' );

// Automatically flush WordPress block pattern cache when theme version changes
add_action( 'init', function() {
    $cached_ver = get_option( 'eu_installed_theme_version', '' );
    if ( $cached_ver !== EU_VERSION ) {
        $theme = wp_get_theme();
        if ( method_exists( $theme, 'delete_pattern_cache' ) ) {
            $theme->delete_pattern_cache();
        }
        update_option( 'eu_installed_theme_version', EU_VERSION );
    }
} );

// =========================================
// LOAD INCLUDES
// =========================================
$eu_includes = [
    '/inc/setup.php',
    '/inc/enqueue.php',
    '/inc/i18n.php',             // Language detection, geo-IP, cookie, eu_t() helpers
    '/inc/customizer.php',       // Customizer: default language + auto-detect setting
    '/inc/post-types.php',
    '/inc/taxonomies.php',
    '/inc/helpers.php',
    '/inc/articles.php',         // Articles: AJAX filter, category badge, related posts
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
    $month        = sanitize_text_field( $_POST['month'] ?? '' );

    $args = [
        'post_type'      => 'umrah_package',
        'posts_per_page' => 9,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    $meta_query = [];

    if ( $package_type && 'all' !== $package_type ) {
        $alt_slug = strpos( $package_type, '_' ) !== false ? str_replace( '_', '-', $package_type ) : str_replace( '-', '_', $package_type );
        $type_slugs = array_unique( [ $package_type, $alt_slug ] );

        $meta_query[] = [
            'key'     => 'eu_package_type',
            'value'   => $type_slugs,
            'compare' => 'IN',
        ];
    }

    if ( ! empty( $month ) ) {
        $clean_month = sanitize_text_field( $month ); // e.g. "2026-07"
        $meta_query[] = [
            'relation' => 'OR',
            [
                'key'     => 'eu_departure_date',
                'value'   => '^' . preg_quote( $clean_month, '/' ),
                'compare' => 'REGEXP',
            ],
            [
                'key'     => 'eu_estimate_month',
                'value'   => $clean_month,
                'compare' => '=',
            ],
        ];
    }

    if ( ! empty( $meta_query ) ) {
        if ( count( $meta_query ) > 1 ) {
            $meta_query['relation'] = 'AND';
        }
        $args['meta_query'] = $meta_query;
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
        echo '<h3>' . esc_html__( 'No packages found for this selection.', 'elegant-umrah' ) . '</h3>';
        echo '<p>' . esc_html__( 'Try selecting a different departure month or package type.', 'elegant-umrah' ) . '</p>';
        echo '</div>';
    }

    $html = ob_get_clean();

    wp_send_json_success( [ 'html' => $html ] );
}

// =========================================
// ARCHIVE QUERY FILTER (?type=... & ?month=...)
// =========================================
add_action( 'pre_get_posts', function( WP_Query $query ): void {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( $query->is_post_type_archive( 'umrah_package' ) ) {
        $meta_query = $query->get( 'meta_query' ) ?: [];

        // Filter by package type
        if ( ! empty( $_GET['type'] ) ) {
            $type = sanitize_key( wp_unslash( $_GET['type'] ) );
            if ( $type && 'all' !== $type ) {
                $alt_slug = strpos( $type, '_' ) !== false ? str_replace( '_', '-', $type ) : str_replace( '-', '_', $type );
                $type_slugs = array_unique( [ $type, $alt_slug ] );
                $meta_query[] = [
                    'key'     => 'eu_package_type',
                    'value'   => $type_slugs,
                    'compare' => 'IN',
                ];
            }
        }

        // Filter by departure month (YYYY-MM)
        if ( ! empty( $_GET['month'] ) ) {
            $month = sanitize_text_field( wp_unslash( $_GET['month'] ) );
            if ( preg_match( '/^\d{4}-\d{2}$/', $month ) ) {
                $meta_query[] = [
                    'relation' => 'OR',
                    [
                        'key'     => 'eu_departure_date',
                        'value'   => '^' . preg_quote( $month, '/' ),
                        'compare' => 'REGEXP',
                    ],
                    [
                        'key'     => 'eu_estimate_month',
                        'value'   => $month,
                        'compare' => '=',
                    ],
                ];
            }
        }

        if ( ! empty( $meta_query ) ) {
            if ( count( $meta_query ) > 1 ) {
                $meta_query['relation'] = 'AND';
            }
            $query->set( 'meta_query', $meta_query );
        }
    }
} );

// =========================================
// SUBDIRECTORY ROOT-RELATIVE LINK REWRITER
// Rewrites href="/..." into home_url('/...') in rendered blocks & template parts
// =========================================
add_filter( 'render_block', function( string $block_content, array $block ): string {
    $home_path = wp_parse_url( home_url(), PHP_URL_PATH );
    if ( ! empty( $home_path ) && '/' !== $home_path ) {
        $home_path = rtrim( $home_path, '/' );
        // Replace href="/packages..." with href="{home_path}/packages..." if not already prefixed
        $pattern = '#href=(["\'])\/(?!\/|wp-content|wp-includes|wp-admin|wp-json|index\.php|' . preg_quote( ltrim( $home_path, '/' ), '#' ) . ')([^"\']*)\1#i';
        $block_content = preg_replace( $pattern, 'href=$1' . $home_path . '/$2$1', $block_content );
    }
    return $block_content;
}, 10, 2 );

// =========================================
// DYNAMIC SITE IDENTITY & BLOCK ENHANCEMENTS
// =========================================
add_filter( 'render_block', function( string $block_content, array $block ): string {
    // 1. Header Brand (.eu-logo) on live frontend
    if ( ! is_admin() && ! empty( $block['attrs']['className'] ) && strpos( $block['attrs']['className'], 'eu-logo' ) !== false && function_exists( 'eu_get_header_brand_html' ) ) {
        return eu_get_header_brand_html();
    }

    // 2. Mobile Nav Toggle & Drawer on frontend
    if ( ! is_admin() && ! empty( $block['attrs']['className'] ) && strpos( $block['attrs']['className'], 'eu-nav' ) !== false && function_exists( 'eu_get_mobile_nav_html' ) ) {
        $block_content .= eu_get_mobile_nav_html();
    }

    // 3. Hero Slideshow Media Layer on frontend (only outer .eu-hero, never .eu-hero-inner)
    if ( ! is_admin() && ! empty( $block['attrs']['className'] ) && preg_match( '/\beu-hero\b/', $block['attrs']['className'] ) && ! preg_match( '/\beu-hero-inner\b/', $block['attrs']['className'] ) && function_exists( 'eu_get_hero_media_html' ) ) {
        $media_html = eu_get_hero_media_html();
        if ( $media_html ) {
            $block_content = preg_replace( '#(<div\s+[^>]*class=["\'][^"\']*\beu-hero\b(?!\-)[^"\']*["\'][^>]*>)#i', '$1' . $media_html, $block_content, 1 );
        }
    }

    // 4. Footer Brand replacement if still using fallback
    if ( strpos( $block_content, 'eu-footer-brand' ) !== false && function_exists( 'eu_get_footer_brand_html' ) ) {
        $pattern = '#<div\s+style=["\'][^"\']*display:flex[^"\']*margin-bottom:0\.5rem[^"\']*["\']>.*?<\/div>\s*<p\s+style=["\'][^"\']*letter-spacing:0\.1em[^"\']*["\']>.*?<\/p>#si';
        $block_content = preg_replace( $pattern, eu_get_footer_brand_html(), $block_content, 1 );
    }

    // 5. Footer Copyright site title
    if ( strpos( $block_content, 'eu-footer-bottom' ) !== false ) {
        $site_name = get_bloginfo( 'name' );
        if ( ! $site_name || 'Local Development' === $site_name ) {
            $site_name = 'Elegant Umrah';
        }
        $block_content = preg_replace(
            '#(©\s*<span id="eu-year">\d+<\/span>\s*)(Elegant Umrah)(\s*&amp;\s*Hajj Travel)#si',
            '$1' . esc_html( $site_name ) . '$3',
            $block_content
        );
    }

    return $block_content;
}, 20, 2 );

// Scroll to top button in footer
add_action( 'wp_footer', function() {
    echo '<button id="eu-scroll-top" aria-label="' . esc_attr__( 'Scroll to top', 'elegant-umrah' ) . '" title="' . esc_attr__( 'Scroll to top', 'elegant-umrah' ) . '">↑</button>';
} );

// =========================================
// YOAST SEO COMPATIBILITY & ENHANCEMENTS
// =========================================

// Fallback OpenGraph / Meta Description for Umrah Packages
add_filter( 'wpseo_opengraph_desc', function( string $desc ): string {
    if ( is_singular( 'umrah_package' ) && empty( $desc ) ) {
        $post_id   = get_the_ID();
        $title     = get_the_title( $post_id );
        $price     = get_post_meta( $post_id, 'eu_price', true );
        $currency  = get_post_meta( $post_id, 'eu_currency', true ) ?: 'Rp';
        $duration  = get_post_meta( $post_id, 'eu_duration', true );
        $departure = get_post_meta( $post_id, 'eu_departure_date', true );

        $parts = [];
        if ( $duration ) {
            $parts[] = is_numeric( trim( (string) $duration ) ) ? trim( (string) $duration ) . ' Hari' : trim( (string) $duration );
        }
        if ( $departure ) {
            $parts[] = 'Keberangkatan ' . date_i18n( 'j F Y', strtotime( $departure ) );
        }
        if ( $price && function_exists( 'eu_format_price' ) ) {
            $parts[] = 'Mulai ' . eu_format_price( (string) $price, (string) $currency );
        }

        if ( ! empty( $parts ) ) {
            return sprintf( '%s - %s. Dapatkan informasi lengkap dan pesan sekarang.', $title, implode( ' | ', $parts ) );
        }
    }
    return $desc;
} );

// Fallback OpenGraph image for Umrah Packages
add_filter( 'wpseo_opengraph_image', function( string $img ): string {
    if ( is_singular( 'umrah_package' ) && empty( $img ) ) {
        $post_id = get_the_ID();
        $thumb   = get_the_post_thumbnail_url( $post_id, 'full' );
        if ( $thumb ) {
            return $thumb;
        }
    }
    return $img;
} );

// =========================================
// THEME SETUP HOOK (FALLBACK)
// =========================================
if ( ! function_exists( 'eu_setup' ) ) {
    add_action( 'after_setup_theme', function() {
        // Ensure setup.php was loaded
        load_theme_textdomain( 'elegant-umrah', EU_DIR . '/languages' );
    } );
}
