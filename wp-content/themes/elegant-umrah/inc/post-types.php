<?php
/**
 * Custom Post Types
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register Umrah Package CPT
 */
function eu_register_post_types() {

    // ─── Umrah Package ────────────────────────────────────────────────────────
    register_post_type( 'umrah_package', [
        'label'               => __( 'Umrah Package', 'elegant-umrah' ),
        'labels'              => [
            'name'                  => __( 'Umrah Packages', 'elegant-umrah' ),
            'singular_name'         => __( 'Umrah Package', 'elegant-umrah' ),
            'menu_name'             => __( 'Umrah Packages', 'elegant-umrah' ),
            'name_admin_bar'        => __( 'Umrah Package', 'elegant-umrah' ),
            'add_new'               => __( 'Add Package', 'elegant-umrah' ),
            'add_new_item'          => __( 'Add New Package', 'elegant-umrah' ),
            'edit_item'             => __( 'Edit Package', 'elegant-umrah' ),
            'new_item'              => __( 'New Package', 'elegant-umrah' ),
            'view_item'             => __( 'View Package', 'elegant-umrah' ),
            'view_items'            => __( 'View Packages', 'elegant-umrah' ),
            'search_items'          => __( 'Search Packages', 'elegant-umrah' ),
            'not_found'             => __( 'No packages found', 'elegant-umrah' ),
            'not_found_in_trash'    => __( 'No packages in trash', 'elegant-umrah' ),
            'all_items'             => __( 'All Packages', 'elegant-umrah' ),
            'archives'              => __( 'Package Archives', 'elegant-umrah' ),
            'attributes'            => __( 'Package Attributes', 'elegant-umrah' ),
            'insert_into_item'      => __( 'Insert into package', 'elegant-umrah' ),
            'uploaded_to_this_item' => __( 'Uploaded to this package', 'elegant-umrah' ),
            'featured_image'        => __( 'Package Cover Image', 'elegant-umrah' ),
            'set_featured_image'    => __( 'Set cover image', 'elegant-umrah' ),
            'remove_featured_image' => __( 'Remove cover image', 'elegant-umrah' ),
            'use_featured_image'    => __( 'Use as cover image', 'elegant-umrah' ),
            'filter_items_list'     => __( 'Filter packages list', 'elegant-umrah' ),
            'items_list_navigation' => __( 'Packages list navigation', 'elegant-umrah' ),
            'items_list'            => __( 'Packages list', 'elegant-umrah' ),
        ],
        'description'         => __( 'Umrah & Hajj travel packages', 'elegant-umrah' ),
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => [
            'slug'       => 'packages',
            'with_front' => false,
            'feeds'      => true,
        ],
        'capability_type'     => 'post',
        'has_archive'         => 'packages',
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-star-filled',
        'supports'            => [
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'custom-fields',
            'revisions',
            'page-attributes',
        ],
        'show_in_rest'        => true,
        'rest_base'           => 'packages',
        'taxonomies'          => [ 'destination' ],
        'template'            => [
            [ 'core/paragraph', [ 'placeholder' => 'Write package description here...' ] ],
        ],
        'template_lock'       => false,
    ] );

    // Register meta fields for REST API
    eu_register_package_meta_fields();
}
add_action( 'init', 'eu_register_post_types' );

/**
 * Register meta fields for Umrah Package
 */
function eu_register_package_meta_fields() {
    $fields = [
        'eu_is_sold_out' => [
            'type'         => 'boolean',
            'description'  => 'Whether this package is sold out',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => false,
        ],
        'eu_currency' => [
            'type'         => 'string',
            'description'  => 'Package currency (Rp or USD)',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 'Rp',
        ],
        'eu_price' => [
            'type'         => 'string',
            'description'  => 'Package price',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_discount_price' => [
            'type'         => 'string',
            'description'  => 'Promotional discount price',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_duration' => [
            'type'         => 'string',
            'description'  => 'Package duration (e.g. 12 days)',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_departure_date' => [
            'type'         => 'string',
            'description'  => 'Departure date',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_estimate_month' => [
            'type'         => 'string',
            'description'  => 'Estimate departure month',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_muthawwif' => [
            'type'         => 'string',
            'description'  => 'Umrah tour leader',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_package_type' => [
            'type'         => 'string',
            'description'  => 'Package type (umrah/hajj/umrah_plus/family/vip/honeymoon)',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_hotel_makkah' => [
            'type'         => 'string',
            'description'  => 'Hotel name in Makkah',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_hotel_makkah_rating' => [
            'type'         => 'string',
            'description'  => 'Hotel rating in Makkah (3-5)',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_hotel_madinah' => [
            'type'         => 'string',
            'description'  => 'Hotel name in Madinah',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_hotel_madinah_rating' => [
            'type'         => 'string',
            'description'  => 'Hotel rating in Madinah (3-5)',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_airline' => [
            'type'         => 'string',
            'description'  => 'Airline name',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_seats_available' => [
            'type'         => 'integer',
            'description'  => 'Number of seats available',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 0,
        ],
        'eu_is_featured' => [
            'type'         => 'boolean',
            'description'  => 'Whether this is a featured package',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => false,
        ],
        'eu_inclusions' => [
            'type'         => 'string',
            'description'  => 'What is included (newline-separated)',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_exclusions' => [
            'type'         => 'string',
            'description'  => 'What is excluded (newline-separated)',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_additional_tour' => [
            'type'         => 'string',
            'description'  => 'Additional tour included',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_additional_country' => [
            'type'         => 'string',
            'description'  => 'Additional country visited',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_schedule_json' => [
            'type'         => 'string',
            'description'  => 'Schedule as JSON',
            'single'       => true,
            'show_in_rest' => true,
        ],
    ];

    foreach ( $fields as $key => $args ) {
        register_post_meta( 'umrah_package', $key, $args );
    }
}

/**
 * Register meta fields for posts
 */
function eu_register_post_meta_fields() {
    $post_fields = [
        'eu_featured_label' => [
            'type'         => 'string',
            'description'  => 'Featured label badge text',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_islamic_category_tag' => [
            'type'         => 'string',
            'description'  => 'Islamic category tag',
            'single'       => true,
            'show_in_rest' => true,
        ],
    ];

    foreach ( $post_fields as $key => $args ) {
        register_post_meta( 'post', $key, $args );
    }
}
add_action( 'init', 'eu_register_post_meta_fields' );

/**
 * Register meta fields for pages (front page fields)
 */
function eu_register_page_meta_fields() {
    $page_fields = [
        'eu_hero_title' => [
            'type'         => 'string',
            'description'  => 'Hero section title',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_hero_subtitle' => [
            'type'         => 'string',
            'description'  => 'Hero section subtitle',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_hero_background' => [
            'type'         => 'integer',
            'description'  => 'Hero background image ID',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_cta_text' => [
            'type'         => 'string',
            'description'  => 'Call-to-action button text',
            'single'       => true,
            'show_in_rest' => true,
        ],
    ];

    foreach ( $page_fields as $key => $args ) {
        register_post_meta( 'page', $key, $args );
    }
}
add_action( 'init', 'eu_register_page_meta_fields' );

/**
 * Flush rewrite rules on theme switch
 */
add_action( 'after_switch_theme', function() {
    eu_register_post_types();
    flush_rewrite_rules();
} );
