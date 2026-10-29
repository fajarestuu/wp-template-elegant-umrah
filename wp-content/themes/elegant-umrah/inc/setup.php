<?php
/**
 * Theme Setup
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Theme setup - registers support, menus, etc.
 */
function eu_setup() {
    // Text domain
    load_theme_textdomain( 'elegant-umrah', EU_DIR . '/languages' );

    // Core supports
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', [
        'height'               => 80,
        'width'                => 300,
        'flex-height'          => true,
        'flex-width'           => true,
        'header-selector'      => '.eu-logo',
        'unlink-homepage-logo' => false,
    ] );
    add_theme_support( 'html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ] );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'post-formats', [ 'video', 'quote', 'link', 'gallery' ] );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'block-template-parts' );

    // FSE / Full Site Editing
    add_theme_support( 'block-templates' );

    // Yoast SEO Features
    add_theme_support( 'yoast-seo-breadcrumbs' );

    // Image sizes
    add_image_size( 'eu-thumb',   640, 400,  true );
    add_image_size( 'eu-card',    400, 280,  true );
    add_image_size( 'eu-hero',   1920, 1080, true );
    add_image_size( 'eu-square',  600, 600,  true );

    // Navigation Menus
    register_nav_menus( [
        'primary'    => __( 'Primary Navigation', 'elegant-umrah' ),
        'footer'     => __( 'Footer Navigation', 'elegant-umrah' ),
        'social'     => __( 'Social Links', 'elegant-umrah' ),
        'packages'   => __( 'Packages Menu', 'elegant-umrah' ),
    ] );

    // Content width
    global $content_width;
    if ( ! isset( $content_width ) ) {
        $content_width = 860;
    }

    // Editor stylesheet
    add_editor_style( 'assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'eu_setup' );

/**
 * Register widget areas
 */
function eu_register_sidebars() {
    $sidebars = [
        [
            'name'          => __( 'Blog Sidebar', 'elegant-umrah' ),
            'id'            => 'eu-blog-sidebar',
            'description'   => __( 'Widgets in the blog/post sidebar area.', 'elegant-umrah' ),
            'before_widget' => '<div id="%1$s" class="eu-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="eu-widget-title">',
            'after_title'   => '</h4>',
        ],
        [
            'name'          => __( 'Footer Column 1', 'elegant-umrah' ),
            'id'            => 'eu-footer-1',
            'description'   => __( 'Footer Widget Area 1', 'elegant-umrah' ),
            'before_widget' => '<div id="%1$s" class="eu-footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="eu-footer-widget-title">',
            'after_title'   => '</h4>',
        ],
        [
            'name'          => __( 'Footer Column 2', 'elegant-umrah' ),
            'id'            => 'eu-footer-2',
            'description'   => __( 'Footer Widget Area 2', 'elegant-umrah' ),
            'before_widget' => '<div id="%1$s" class="eu-footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="eu-footer-widget-title">',
            'after_title'   => '</h4>',
        ],
    ];

    foreach ( $sidebars as $sidebar ) {
        register_sidebar( $sidebar );
    }
}
add_action( 'widgets_init', 'eu_register_sidebars' );

/**
 * Set excerpt length
 */
add_filter( 'excerpt_length', fn() => 25 );
add_filter( 'excerpt_more', fn() => '&hellip;' );

/**
 * Body classes
 */
function eu_body_classes( array $classes ): array {
    if ( is_singular() ) {
        $classes[] = 'eu-singular';
    }
    if ( is_front_page() ) {
        $classes[] = 'eu-front-page';
    }
    if ( is_post_type_archive( 'umrah_package' ) || is_singular( 'umrah_package' ) ) {
        $classes[] = 'eu-packages-page';
    }
    return $classes;
}
add_filter( 'body_class', 'eu_body_classes' );

/**
 * Remove default WordPress styles we override
 */
function eu_clean_wp_head() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'eu_clean_wp_head' );

/**
 * Add custom image sizes to media library
 */
function eu_add_image_size_names( array $sizes ): array {
    return array_merge( $sizes, [
        'eu-thumb'  => __( 'EU Thumbnail (640x400)', 'elegant-umrah' ),
        'eu-card'   => __( 'EU Card (400x280)', 'elegant-umrah' ),
        'eu-hero'   => __( 'EU Hero (1920x1080)', 'elegant-umrah' ),
        'eu-square' => __( 'EU Square (600x600)', 'elegant-umrah' ),
    ] );
}
add_filter( 'image_size_names_choose', 'eu_add_image_size_names' );

/**
 * Override FSE template for single and archive umrah_package with PHP templates.
 *
 * FSE HTML templates cannot execute PHP, so for post types that require
 * dynamic server-side rendering (like umrah_package with postmeta), we
 * intercept the template loading here and return a PHP file instead.
 */
add_filter( 'template_include', function( string $template ): string {
    if ( is_singular( 'umrah_package' ) ) {
        $php_template = get_theme_file_path( 'single-umrah_package.php' );
        if ( file_exists( $php_template ) ) {
            return $php_template;
        }
    } elseif ( is_post_type_archive( 'umrah_package' ) ) {
        $php_template = get_theme_file_path( 'archive-umrah_package.php' );
        if ( file_exists( $php_template ) ) {
            return $php_template;
        }
    }
    return $template;
}, 99 );

