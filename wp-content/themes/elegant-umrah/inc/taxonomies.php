<?php
/**
 * Custom Taxonomies
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register custom taxonomies
 */
function eu_register_taxonomies() {

    // ─── Destination ──────────────────────────────────────────────────────────
    register_taxonomy( 'destination', [ 'umrah_package' ], [
        'label'              => __( 'Destination', 'elegant-umrah' ),
        'labels'             => [
            'name'              => __( 'Destinations', 'elegant-umrah' ),
            'singular_name'     => __( 'Destination', 'elegant-umrah' ),
            'menu_name'         => __( 'Destinations', 'elegant-umrah' ),
            'all_items'         => __( 'All Destinations', 'elegant-umrah' ),
            'edit_item'         => __( 'Edit Destination', 'elegant-umrah' ),
            'update_item'       => __( 'Update Destination', 'elegant-umrah' ),
            'add_new_item'      => __( 'Add New Destination', 'elegant-umrah' ),
            'new_item_name'     => __( 'New Destination Name', 'elegant-umrah' ),
            'search_items'      => __( 'Search Destinations', 'elegant-umrah' ),
        ],
        'hierarchical'       => false, // Like tags
        'public'             => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => [ 'slug' => 'destination' ],
    ] );

    // ─── Package Type (Customizable by Admin) ──────────────────────────────────
    register_taxonomy( 'package_type', [ 'umrah_package' ], [
        'label'              => __( 'Package Types', 'elegant-umrah' ),
        'labels'             => [
            'name'              => __( 'Package Types', 'elegant-umrah' ),
            'singular_name'     => __( 'Package Type', 'elegant-umrah' ),
            'menu_name'         => __( 'Package Types', 'elegant-umrah' ),
            'all_items'         => __( 'All Package Types', 'elegant-umrah' ),
            'edit_item'         => __( 'Edit Package Type', 'elegant-umrah' ),
            'update_item'       => __( 'Update Package Type', 'elegant-umrah' ),
            'add_new_item'      => __( 'Add New Package Type', 'elegant-umrah' ),
            'new_item_name'     => __( 'New Package Type Name', 'elegant-umrah' ),
            'search_items'      => __( 'Search Package Types', 'elegant-umrah' ),
            'parent_item'       => __( 'Parent Type', 'elegant-umrah' ),
            'parent_item_colon' => __( 'Parent Type:', 'elegant-umrah' ),
        ],
        'hierarchical'       => true,
        'public'             => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => [ 'slug' => 'package-type' ],
    ] );

    // ─── Islamic Category (for Posts) ─────────────────────────────────────────
    register_taxonomy( 'islamic_category', [ 'post' ], [
        'label'              => __( 'Islamic Category', 'elegant-umrah' ),
        'labels'             => [
            'name'              => __( 'Islamic Categories', 'elegant-umrah' ),
            'singular_name'     => __( 'Islamic Category', 'elegant-umrah' ),
            'menu_name'         => __( 'Islamic Categories', 'elegant-umrah' ),
            'all_items'         => __( 'All Categories', 'elegant-umrah' ),
            'edit_item'         => __( 'Edit Category', 'elegant-umrah' ),
            'update_item'       => __( 'Update Category', 'elegant-umrah' ),
            'add_new_item'      => __( 'Add New Category', 'elegant-umrah' ),
            'new_item_name'     => __( 'New Category Name', 'elegant-umrah' ),
            'search_items'      => __( 'Search Categories', 'elegant-umrah' ),
            'parent_item'       => __( 'Parent Category', 'elegant-umrah' ),
            'parent_item_colon' => __( 'Parent Category:', 'elegant-umrah' ),
        ],
        'hierarchical'       => true,
        'public'             => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => [ 'slug' => 'islamic-category' ],
    ] );
}
add_action( 'init', 'eu_register_taxonomies' );

/**
 * Seed default terms for destinations and Islamic categories
 */
function eu_seed_default_terms() {
    if ( get_option( 'eu_terms_seeded' ) ) return;

    // Package Types
    $default_types = [
        'umrah'      => 'Umrah',
        'hajj'       => 'Haji',
        'umrah_plus' => 'Umrah Plus',
        'family'     => 'Paket Keluarga',
        'vip'        => 'VIP Premium',
        'honeymoon'  => 'Umrah Bulan Madu',
    ];
    foreach ( $default_types as $slug => $name ) {
        if ( ! term_exists( $slug, 'package_type' ) ) {
            wp_insert_term( $name, 'package_type', [ 'slug' => $slug ] );
        }
    }

    // Destinations
    $destinations = [ 'Makkah', 'Madinah', 'Jeddah', 'Thaif', 'Yanbu' ];
    foreach ( $destinations as $dest ) {
        if ( ! term_exists( $dest, 'destination' ) ) {
            wp_insert_term( $dest, 'destination' );
        }
    }

    // Islamic Categories for posts
    $islamic_cats = [
        'Fiqh Ibadah'  => 'Islamic jurisprudence on worship',
        'Doa & Dzikir' => 'Prayers and remembrance of Allah',
        'Manasik'      => 'Rituals of Hajj and Umrah',
        'Kisah Islami' => 'Islamic stories and parables',
        'Tips Umrah'   => 'Practical tips for Umrah travelers',
        'Motivasi'     => 'Islamic motivation and inspiration',
    ];

    foreach ( $islamic_cats as $name => $desc ) {
        if ( ! term_exists( $name, 'islamic_category' ) ) {
            wp_insert_term( $name, 'islamic_category', [ 'description' => $desc ] );
        }
    }

    update_option( 'eu_terms_seeded', true );
}
add_action( 'after_switch_theme', 'eu_seed_default_terms' );

/**
 * Keep package_type taxonomy and eu_package_type meta in sync when saving umrah_package
 */
add_action( 'save_post_umrah_package', function( int $post_id, \WP_Post $post, bool $update ): void {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_id ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['eu_package_type'] ) ) {
        $type_slug = sanitize_key( wp_unslash( $_POST['eu_package_type'] ) );
        if ( $type_slug ) {
            $term = get_term_by( 'slug', $type_slug, 'package_type' );
            if ( ! $term ) {
                $alt_slug = str_replace( '_', '-', $type_slug );
                $term = get_term_by( 'slug', $alt_slug, 'package_type' );
            }
            if ( $term ) {
                wp_set_object_terms( $post_id, [ (int) $term->term_id ], 'package_type' );
            }
        }
    } else {
        $terms = wp_get_post_terms( $post_id, 'package_type' );
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            update_post_meta( $post_id, 'eu_package_type', $terms[0]->slug );
        }
    }
}, 20, 3 );

