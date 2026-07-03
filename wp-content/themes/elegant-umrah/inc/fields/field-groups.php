<?php
/**
 * Field Groups - Registers all field groups for the theme
 *
 * This is the "entry point" where all custom field groups are defined,
 * similar to how ACF field groups are registered.
 *
 * @package ElegantUmrah\Fields
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register all field groups
 * Called on 'init' so CPTs & taxonomies are available
 */
function eu_register_all_field_groups(): void {

    // =========================================================================
    // 1. UMRAH PACKAGE FIELDS
    //    Location: post_type == umrah_package
    // =========================================================================
    eu_register_field_group( [
        'id'       => 'umrah_package_fields',
        'title'    => __( '📦 Package Details', 'elegant-umrah' ),
        'context'  => 'normal',
        'priority' => 'high',
        'location' => [
            [
                [ 'param' => 'post_type', 'operator' => '==', 'value' => 'umrah_package' ],
            ],
        ],
        'fields'   => [
            [
                'type'         => 'checkbox',
                'name'         => 'eu_is_sold_out',
                'label'        => __( 'Sold Out', 'elegant-umrah' ),
                'label_on'     => __( 'Yes — Mark this package as Sold Out', 'elegant-umrah' ),
                'instructions' => __( 'Check to display a "Sold Out" badge and disable the booking button', 'elegant-umrah' ),
                'default'      => false,
                'width'        => '50',
            ],
            [
                'type'         => 'select',
                'name'         => 'eu_currency',
                'label'        => __( 'Currency', 'elegant-umrah' ),
                'instructions' => __( 'Currency used for all prices on this package', 'elegant-umrah' ),
                'allow_null'   => false,
                'default'      => 'Rp',
                'choices'      => [
                    'Rp'  => 'Rp — Indonesian Rupiah',
                    'USD' => 'USD — US Dollar',
                ],
                'required'     => false,
                'width'        => '50',
            ],
            [
                'type'         => 'text',
                'name'         => 'eu_price',
                'label'        => __( 'Price', 'elegant-umrah' ),
                'placeholder'  => 'e.g. 22.500.000',
                'instructions' => __( 'Original package price. If a Discount Price is set, this will appear struck-through.', 'elegant-umrah' ),
                'required'     => true,
                'width'        => '50',
            ],
            [
                'type'         => 'text',
                'name'         => 'eu_discount_price',
                'label'        => __( 'Discount Price', 'elegant-umrah' ),
                'placeholder'  => 'e.g. 19.500.000',
                'instructions' => __( '(Optional) Promotional price. When filled, this is shown as the active price and the original Price is struck-through.', 'elegant-umrah' ),
                'required'     => false,
                'width'        => '50',
            ],
            [
                'type'         => 'text',
                'name'         => 'eu_duration',
                'label'        => __( 'Duration', 'elegant-umrah' ),
                'placeholder'  => 'e.g. 12 Days / 11 Nights',
                'instructions' => __( 'Package duration', 'elegant-umrah' ),
                'required'     => true,
                'width'        => '50',
            ],
            [
                'type'         => 'date',
                'name'         => 'eu_departure_date',
                'label'        => __( 'Departure Date', 'elegant-umrah' ),
                'instructions' => __( 'Exact departure date (optional)', 'elegant-umrah' ),
                'required'     => false,
                'width'        => '33',
            ],
            [
                'type'         => 'month',
                'name'         => 'eu_estimate_month',
                'label'        => __( 'Estimate Month', 'elegant-umrah' ),
                'instructions' => __( 'Used if exact date is unknown', 'elegant-umrah' ),
                'required'     => false,
                'width'        => '33',
            ],
            [
                'type'         => 'text',
                'name'         => 'eu_muthawwif',
                'label'        => __( 'Muthawwif', 'elegant-umrah' ),
                'placeholder'  => 'e.g. Ustadz Fulan',
                'instructions' => __( 'Umrah Tour Leader', 'elegant-umrah' ),
                'required'     => false,
                'width'        => '33',
            ],
            [
                'type'         => 'select',
                'name'         => 'eu_package_type',
                'label'        => __( 'Package Type', 'elegant-umrah' ),
                'instructions' => __( 'Select the type of pilgrimage package', 'elegant-umrah' ),
                'allow_null'   => true,
                'null_label'   => '— Select Type —',
                'choices'      => [
                    'umrah'      => 'Umrah',
                    'hajj'       => 'Hajj',
                    'umrah_plus' => 'Umrah Plus',
                    'family'     => 'Family Package',
                    'vip'        => 'VIP Premium',
                    'honeymoon'  => 'Honeymoon Umrah',
                ],
                'width'        => '50',
            ],
            [
                'type'         => 'text',
                'name'         => 'eu_hotel_makkah',
                'label'        => __( 'Hotel Makkah', 'elegant-umrah' ),
                'placeholder'  => 'e.g. Grand Makkah Hotel',
                'instructions' => __( 'Hotel name in Makkah', 'elegant-umrah' ),
                'width'        => '50',
            ],
            [
                'type'         => 'select',
                'name'         => 'eu_hotel_makkah_rating',
                'label'        => __( 'Hotel Makkah Rating', 'elegant-umrah' ),
                'allow_null'   => true,
                'null_label'   => '— Select Rating —',
                'choices'      => [
                    '3' => '3 Stars',
                    '4' => '4 Stars',
                    '5' => '5 Stars',
                ],
                'width'        => '50',
            ],
            [
                'type'         => 'text',
                'name'         => 'eu_hotel_madinah',
                'label'        => __( 'Hotel Madinah', 'elegant-umrah' ),
                'placeholder'  => 'e.g. Anwar Al Madinah',
                'instructions' => __( 'Hotel name in Madinah', 'elegant-umrah' ),
                'width'        => '50',
            ],
            [
                'type'         => 'select',
                'name'         => 'eu_hotel_madinah_rating',
                'label'        => __( 'Hotel Madinah Rating', 'elegant-umrah' ),
                'allow_null'   => true,
                'null_label'   => '— Select Rating —',
                'choices'      => [
                    '3' => '3 Stars',
                    '4' => '4 Stars',
                    '5' => '5 Stars',
                ],
                'width'        => '50',
            ],
            [
                'type'         => 'text',
                'name'         => 'eu_airline',
                'label'        => __( 'Airline', 'elegant-umrah' ),
                'placeholder'  => 'e.g. Garuda Indonesia',
                'instructions' => __( 'Airline operating this package', 'elegant-umrah' ),
                'width'        => '50',
            ],
            [
                'type'         => 'number',
                'name'         => 'eu_seats_available',
                'label'        => __( 'Available Seats', 'elegant-umrah' ),
                'placeholder'  => '0',
                'instructions' => __( 'Number of seats remaining (0 = sold out)', 'elegant-umrah' ),
                'min'          => '0',
                'max'          => '9999',
                'step'         => '1',
                'append'       => 'seats',
                'width'        => '50',
            ],
            [
                'type'         => 'checkbox',
                'name'         => 'eu_is_featured',
                'label'        => __( 'Featured Package', 'elegant-umrah' ),
                'label_on'     => __( 'Yes — Show as Best Seller badge', 'elegant-umrah' ),
                'instructions' => __( 'Check to highlight this as a featured/best-seller package', 'elegant-umrah' ),
                'default'      => false,
                'width'        => '50',
            ],
            [
                'type'         => 'textarea',
                'name'         => 'eu_inclusions',
                'label'        => __( 'Package Inclusions', 'elegant-umrah' ),
                'placeholder'  => "✓ Round-trip airfare\n✓ Hotel accommodation\n✓ Airport transfers\n✓ Guided Manasik sessions",
                'instructions' => __( 'List inclusions, one per line', 'elegant-umrah' ),
                'rows'         => 6,
                'width'        => '50',
            ],
            [
                'type'         => 'textarea',
                'name'         => 'eu_exclusions',
                'label'        => __( 'Package Exclusions', 'elegant-umrah' ),
                'placeholder'  => "✗ Passport creation\n✗ Meningitis vaccine\n✗ Personal expenses",
                'instructions' => __( 'List exclusions, one per line', 'elegant-umrah' ),
                'rows'         => 6,
                'width'        => '50',
            ],
            [
                'type'         => 'text',
                'name'         => 'eu_additional_tour',
                'label'        => __( 'Additional Tour', 'elegant-umrah' ),
                'placeholder'  => 'e.g. Taif City Tour, Badar Tour',
                'instructions' => __( 'Any additional city tours or Ziyarah included', 'elegant-umrah' ),
                'width'        => '50',
            ],
            [
                'type'         => 'text',
                'name'         => 'eu_additional_country',
                'label'        => __( 'Additional Country', 'elegant-umrah' ),
                'placeholder'  => 'e.g. Turkey, Egypt, Jordan',
                'instructions' => __( 'For Plus packages, list additional countries visited', 'elegant-umrah' ),
                'width'        => '50',
            ],
            [
                'type'         => 'textarea',
                'name'         => 'eu_schedule_json',
                'label'        => __( 'Day-by-Day Schedule (JSON)', 'elegant-umrah' ),
                'placeholder'  => '[{"day":"Day 1","title":"Departure","description":"Board flight from Jakarta to Jeddah"}]',
                'instructions' => __( 'JSON format: [{day, title, description}, ...]. Used to render itinerary timeline.', 'elegant-umrah' ),
                'rows'         => 8,
                'width'        => '100',
            ],
        ],
    ] );

    // =========================================================================
    // 2. LANDING PAGE HERO FIELDS
    //    Location: page template == front-page
    // =========================================================================
    eu_register_field_group( [
        'id'       => 'front_page_hero_fields',
        'title'    => __( '🏠 Hero Section Settings', 'elegant-umrah' ),
        'context'  => 'normal',
        'priority' => 'high',
        'location' => [
            [
                [ 'param' => 'page_template', 'operator' => '==', 'value' => 'front-page' ],
            ],
            [
                // Also show for pages set as homepage in Reading Settings
                [ 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ],
            ],
        ],
        'fields'   => [
            [
                'type'         => 'text',
                'name'         => 'eu_hero_title',
                'label'        => __( 'Hero Title', 'elegant-umrah' ),
                'placeholder'  => 'Your Journey to the Holy Land Begins Here',
                'instructions' => __( 'Main headline for the hero section. Leave empty to use page title.', 'elegant-umrah' ),
                'width'        => '100',
            ],
            [
                'type'         => 'textarea',
                'name'         => 'eu_hero_subtitle',
                'label'        => __( 'Hero Subtitle', 'elegant-umrah' ),
                'placeholder'  => 'Trusted Umrah & Hajj travel agency with 20+ years of experience...',
                'instructions' => __( 'Supporting text shown below the hero title', 'elegant-umrah' ),
                'rows'         => 3,
                'width'        => '100',
            ],
            [
                'type'         => 'image',
                'name'         => 'eu_hero_background',
                'label'        => __( 'Hero Background Image', 'elegant-umrah' ),
                'instructions' => __( 'High-resolution image (recommended: 1920x1080px, JPG). Used as hero background.', 'elegant-umrah' ),
                'button_label' => __( 'Select Background Image', 'elegant-umrah' ),
                'width'        => '100',
            ],
            [
                'type'         => 'text',
                'name'         => 'eu_cta_text',
                'label'        => __( 'CTA Button Text', 'elegant-umrah' ),
                'placeholder'  => 'Book Umrah Now',
                'instructions' => __( 'Text for the main call-to-action button in the hero', 'elegant-umrah' ),
                'width'        => '50',
            ],
        ],
    ] );

    // =========================================================================
    // 3. ARTICLE / POST FIELDS
    //    Location: post_type == post
    // =========================================================================
    eu_register_field_group( [
        'id'       => 'article_fields',
        'title'    => __( '📖 Islamic Article Fields', 'elegant-umrah' ),
        'context'  => 'side',
        'priority' => 'default',
        'location' => [
            [
                [ 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ],
            ],
        ],
        'fields'   => [
            [
                'type'         => 'text',
                'name'         => 'eu_featured_label',
                'label'        => __( 'Featured Label', 'elegant-umrah' ),
                'placeholder'  => 'e.g. Editor\'s Pick, Trending, New',
                'instructions' => __( 'Badge text shown on the article card. Leave empty for no badge.', 'elegant-umrah' ),
                'width'        => '100',
            ],
            [
                'type'         => 'select',
                'name'         => 'eu_islamic_category_tag',
                'label'        => __( 'Islamic Category Tag', 'elegant-umrah' ),
                'instructions' => __( 'Additional Islamic categorization shown on the article', 'elegant-umrah' ),
                'allow_null'   => true,
                'null_label'   => '— No Tag —',
                'choices'      => [
                    'fiqh'       => 'Fiqh Ibadah',
                    'doa'        => 'Doa & Dzikir',
                    'manasik'    => 'Manasik',
                    'kisah'      => 'Kisah Islami',
                    'tips'       => 'Tips Umrah',
                    'motivasi'   => 'Motivasi',
                    'hikmah'     => 'Hikmah & Tadabbur',
                    'sejarah'    => 'Sejarah Islam',
                    'quran'      => 'Al-Quran & Tafsir',
                    'hadits'     => 'Hadits Nabi',
                ],
                'width'        => '100',
            ],
        ],
    ] );
}
add_action( 'init', 'eu_register_all_field_groups', 20 ); // Priority 20 so CPTs are registered first
