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
        'label'               => __( 'Umrah Package', EU_TEXT_DOMAIN ),
        'labels'              => [
            'name'                  => __( 'Umrah Packages', EU_TEXT_DOMAIN ),
            'singular_name'         => __( 'Umrah Package', EU_TEXT_DOMAIN ),
            'menu_name'             => __( 'Umrah Packages', EU_TEXT_DOMAIN ),
            'name_admin_bar'        => __( 'Umrah Package', EU_TEXT_DOMAIN ),
            'add_new'               => __( 'Add Package', EU_TEXT_DOMAIN ),
            'add_new_item'          => __( 'Add New Package', EU_TEXT_DOMAIN ),
            'edit_item'             => __( 'Edit Package', EU_TEXT_DOMAIN ),
            'new_item'              => __( 'New Package', EU_TEXT_DOMAIN ),
            'view_item'             => __( 'View Package', EU_TEXT_DOMAIN ),
            'view_items'            => __( 'View Packages', EU_TEXT_DOMAIN ),
            'search_items'          => __( 'Search Packages', EU_TEXT_DOMAIN ),
            'not_found'             => __( 'No packages found', EU_TEXT_DOMAIN ),
            'not_found_in_trash'    => __( 'No packages in trash', EU_TEXT_DOMAIN ),
            'all_items'             => __( 'All Packages', EU_TEXT_DOMAIN ),
            'archives'              => __( 'Package Archives', EU_TEXT_DOMAIN ),
            'attributes'            => __( 'Package Attributes', EU_TEXT_DOMAIN ),
            'insert_into_item'      => __( 'Insert into package', EU_TEXT_DOMAIN ),
            'uploaded_to_this_item' => __( 'Uploaded to this package', EU_TEXT_DOMAIN ),
            'featured_image'        => __( 'Package Cover Image', EU_TEXT_DOMAIN ),
            'set_featured_image'    => __( 'Set cover image', EU_TEXT_DOMAIN ),
            'remove_featured_image' => __( 'Remove cover image', EU_TEXT_DOMAIN ),
            'use_featured_image'    => __( 'Use as cover image', EU_TEXT_DOMAIN ),
            'filter_items_list'     => __( 'Filter packages list', EU_TEXT_DOMAIN ),
            'items_list_navigation' => __( 'Packages list navigation', EU_TEXT_DOMAIN ),
            'items_list'            => __( 'Packages list', EU_TEXT_DOMAIN ),
        ],
        'description'         => __( 'Umrah & Hajj travel packages', EU_TEXT_DOMAIN ),
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
        'taxonomies'          => [ 'package_type', 'destination' ],
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
        'eu_price' => [
            'type'         => 'string',
            'description'  => 'Package price',
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
        'eu_hotel_info' => [
            'type'         => 'string',
            'description'  => 'Hotel information',
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
        'eu_package_type' => [
            'type'         => 'string',
            'description'  => 'Package type (umrah/hajj/combo)',
            'single'       => true,
            'show_in_rest' => true,
        ],
        'eu_inclusions' => [
            'type'         => 'string',
            'description'  => 'What is included (newline-separated)',
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
 * Flush rewrite rules on activation
 */
function eu_flush_rewrite_rules() {
    eu_register_post_types();
    flush_rewrite_rules();
}
register_activation_hook( EU_DIR . '/functions.php', 'eu_flush_rewrite_rules' );

/**
 * Add rewrite flush reminder
 */
add_action( 'after_switch_theme', function() {
    eu_register_post_types();
    flush_rewrite_rules();
} );
