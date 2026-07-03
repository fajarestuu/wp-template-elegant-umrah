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

    // ─── Package Type ─────────────────────────────────────────────────────────
    register_taxonomy( 'package_type', [ 'umrah_package' ], [
        'label'              => __( 'Package Type', EU_TEXT_DOMAIN ),
        'labels'             => [
            'name'              => __( 'Package Types', EU_TEXT_DOMAIN ),
            'singular_name'     => __( 'Package Type', EU_TEXT_DOMAIN ),
            'menu_name'         => __( 'Package Types', EU_TEXT_DOMAIN ),
            'all_items'         => __( 'All Types', EU_TEXT_DOMAIN ),
            'edit_item'         => __( 'Edit Type', EU_TEXT_DOMAIN ),
            'view_item'         => __( 'View Type', EU_TEXT_DOMAIN ),
            'update_item'       => __( 'Update Type', EU_TEXT_DOMAIN ),
            'add_new_item'      => __( 'Add New Type', EU_TEXT_DOMAIN ),
            'new_item_name'     => __( 'New Type Name', EU_TEXT_DOMAIN ),
            'search_items'      => __( 'Search Types', EU_TEXT_DOMAIN ),
            'not_found'         => __( 'No types found', EU_TEXT_DOMAIN ),
        ],
        'hierarchical'       => true, // Like categories
        'public'             => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => [ 'slug' => 'package-type' ],
    ] );

    // ─── Destination ──────────────────────────────────────────────────────────
    register_taxonomy( 'destination', [ 'umrah_package' ], [
        'label'              => __( 'Destination', EU_TEXT_DOMAIN ),
        'labels'             => [
            'name'              => __( 'Destinations', EU_TEXT_DOMAIN ),
            'singular_name'     => __( 'Destination', EU_TEXT_DOMAIN ),
            'menu_name'         => __( 'Destinations', EU_TEXT_DOMAIN ),
            'all_items'         => __( 'All Destinations', EU_TEXT_DOMAIN ),
            'edit_item'         => __( 'Edit Destination', EU_TEXT_DOMAIN ),
            'update_item'       => __( 'Update Destination', EU_TEXT_DOMAIN ),
            'add_new_item'      => __( 'Add New Destination', EU_TEXT_DOMAIN ),
            'new_item_name'     => __( 'New Destination Name', EU_TEXT_DOMAIN ),
            'search_items'      => __( 'Search Destinations', EU_TEXT_DOMAIN ),
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

    // ─── Islamic Category (for Posts) ─────────────────────────────────────────
    register_taxonomy( 'islamic_category', [ 'post' ], [
        'label'              => __( 'Islamic Category', EU_TEXT_DOMAIN ),
        'labels'             => [
            'name'              => __( 'Islamic Categories', EU_TEXT_DOMAIN ),
            'singular_name'     => __( 'Islamic Category', EU_TEXT_DOMAIN ),
            'menu_name'         => __( 'Islamic Categories', EU_TEXT_DOMAIN ),
            'all_items'         => __( 'All Categories', EU_TEXT_DOMAIN ),
            'edit_item'         => __( 'Edit Category', EU_TEXT_DOMAIN ),
            'update_item'       => __( 'Update Category', EU_TEXT_DOMAIN ),
            'add_new_item'      => __( 'Add New Category', EU_TEXT_DOMAIN ),
            'new_item_name'     => __( 'New Category Name', EU_TEXT_DOMAIN ),
            'search_items'      => __( 'Search Categories', EU_TEXT_DOMAIN ),
            'parent_item'       => __( 'Parent Category', EU_TEXT_DOMAIN ),
            'parent_item_colon' => __( 'Parent Category:', EU_TEXT_DOMAIN ),
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
 * Seed default terms for package types
 */
function eu_seed_default_terms() {
    if ( get_option( 'eu_terms_seeded' ) ) return;

    // Package Types
    $package_types = [
        'Umrah'      => 'Umrah package for individuals and groups',
        'Hajj'       => 'Hajj package - full pilgrimage',
        'Umrah Plus' => 'Umrah with added destinations and experiences',
        'Honeymoon'  => 'Special Umrah packages for newly married couples',
        'Family'     => 'Family-friendly Umrah packages',
        'VIP'        => 'Premium VIP Umrah experiences',
    ];

    foreach ( $package_types as $name => $desc ) {
        if ( ! term_exists( $name, 'package_type' ) ) {
            wp_insert_term( $name, 'package_type', [ 'description' => $desc ] );
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
