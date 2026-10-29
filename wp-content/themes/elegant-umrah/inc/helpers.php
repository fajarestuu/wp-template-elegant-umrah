<?php
/**
 * Theme Helpers & Utility Functions
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ─────────────────────────────────────────────────────────────────────────────
// IMAGE HELPERS
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Get image URL by attachment ID
 */
function eu_get_image_url( int $attachment_id, string $size = 'eu-card' ): string {
    if ( ! $attachment_id ) return '';
    $src = wp_get_attachment_image_src( $attachment_id, $size );
    return $src ? $src[0] : '';
}

/**
 * Get image tag with lazy loading
 */
function eu_img( int $id, string $size = 'eu-card', string $class = '', array $attrs = [] ): string {
    if ( ! $id ) return '';
    $attr_str = '';
    foreach ( $attrs as $k => $v ) {
        $attr_str .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
    }
    return wp_get_attachment_image( $id, $size, false, array_merge( [
        'class'   => $class,
        'loading' => 'lazy',
    ], $attrs ) );
}

/**
 * Get featured image URL
 */
function eu_get_featured_image_url( ?int $post_id = null, string $size = 'eu-card' ): string {
    $post_id = $post_id ?? get_the_ID();
    $thumb_id = get_post_thumbnail_id( $post_id );
    if ( ! $thumb_id ) return '';
    return eu_get_image_url( $thumb_id, $size );
}

// ─────────────────────────────────────────────────────────────────────────────
// EXCERPT HELPERS
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Get custom excerpt with specified word count
 */
function eu_get_excerpt( ?int $post_id = null, int $words = 20, string $more = '&hellip;' ): string {
    $post_id = $post_id ?? get_the_ID();
    $post = get_post( $post_id );
    if ( ! $post ) return '';

    if ( $post->post_excerpt ) {
        return wp_trim_words( $post->post_excerpt, $words, $more );
    }
    return wp_trim_words( strip_tags( $post->post_content ), $words, $more );
}

/**
 * Echo excerpt
 */
function eu_excerpt( ?int $post_id = null, int $words = 20 ): void {
    echo wp_kses_post( eu_get_excerpt( $post_id, $words ) );
}

// ─────────────────────────────────────────────────────────────────────────────
// PACKAGE HELPERS
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Format a raw price string with currency symbol and locale-aware thousands separator.
 * Safely handles strings like "22.500.000", "22,500,000", or "1500.50".
 */
function eu_format_price( string $raw, string $currency = 'Rp' ): string {
    $raw = trim( $raw );
    if ( '' === $raw ) return '';

    // Remove everything except digits, dots, and commas
    $numeric = preg_replace( '/[^\d.,]/', '', $raw );
    if ( '' === $numeric ) return esc_html( $raw );

    $numeric = str_replace( ',', '.', $numeric );
    $parts   = explode( '.', $numeric );

    if ( count( $parts ) > 2 ) {
        // Multiple dots -> all are thousands separators (e.g. 22.500.000)
        $numeric = implode( '', $parts );
    } elseif ( count( $parts ) === 2 && strlen( $parts[1] ) === 3 ) {
        // Single dot followed by 3 digits -> thousands separator (e.g. 1.500)
        $numeric = $parts[0] . $parts[1];
    }

    $number = (float) $numeric;

    if ( 'USD' === strtoupper( $currency ) ) {
        $decimals = ( floor( $number ) != $number ) ? 2 : 0;
        return 'USD ' . number_format( $number, $decimals, '.', ',' );
    }

    $symbols = [ 'Rp' => 'Rp ', 'IDR' => 'Rp ', 'MYR' => 'RM ', 'SGD' => 'S$ ', 'SAR' => 'SAR ' ];
    $symbol  = $symbols[ $currency ] ?? $currency . ' ';

    return $symbol . number_format( $number, 0, ',', '.' );
}

/**
 * Get configured WhatsApp number (digits only) and build a wa.me URL.
 */
function eu_get_whatsapp_url( string $message = '' ): string {
    $phone = get_theme_mod( 'eu_whatsapp_number', '621234567890' );
    $phone = preg_replace( '/\D+/', '', (string) $phone ) ?: '621234567890';
    $url   = 'https://wa.me/' . $phone;
    if ( '' !== $message ) {
        $url .= '?text=' . rawurlencode( $message );
    }
    return $url;
}

/**
 * Get formatted package price
 */
function eu_get_package_price( ?int $post_id = null, string $currency = '' ): string {
    $post_id  = $post_id ?? get_the_ID();
    $price    = (string) get_post_meta( $post_id, 'eu_price', true );
    if ( ! $price ) return '';

    if ( ! $currency ) {
        $currency = (string) ( get_post_meta( $post_id, 'eu_currency', true ) ?: 'Rp' );
    }

    return eu_format_price( $price, $currency );
}

/**
 * Get package duration
 */
function eu_get_package_duration( ?int $post_id = null ): string {
    $post_id = $post_id ?? get_the_ID();
    return esc_html( get_post_meta( $post_id, 'eu_duration', true ) ?? '' );
}

/**
 * Get formatted departure date or estimated month
 */
function eu_get_departure_date( ?int $post_id = null, string $format = 'd M Y' ): string {
    $post_id = $post_id ?? get_the_ID();
    $date    = get_post_meta( $post_id, 'eu_departure_date', true );
    
    if ( $date ) {
        $timestamp = strtotime( $date );
        return $timestamp ? date_i18n( $format, $timestamp ) : esc_html( $date );
    }
    
    // Fallback to Estimate Month
    $month = get_post_meta( $post_id, 'eu_estimate_month', true );
    if ( $month ) {
        // Month is typically saved as YYYY-MM
        $timestamp = strtotime( $month . '-01' );
        return $timestamp ? date_i18n( 'M Y', $timestamp ) : esc_html( $month );
    }
    
    return '';
}

/**
 * Get package schedule as array
 */
function eu_get_package_schedule( ?int $post_id = null ): array {
    $post_id = $post_id ?? get_the_ID();
    $json    = get_post_meta( $post_id, 'eu_schedule_json', true );
    if ( ! $json ) return [];
    $decoded = json_decode( $json, true );
    return is_array( $decoded ) ? $decoded : [];
}

/**
 * Get package inclusions as array
 */
function eu_get_package_inclusions( ?int $post_id = null ): array {
    $post_id  = $post_id ?? get_the_ID();
    $raw      = get_post_meta( $post_id, 'eu_inclusions', true );
    if ( ! $raw ) return [];
    return array_filter( array_map( 'trim', explode( "\n", $raw ) ) );
}

/**
 * Get package types array (dynamically fetched from package_type taxonomy, fallback to default)
 */
function eu_get_package_types(): array {
    $types = [];

    // Check taxonomy terms
    $terms = get_terms( [
        'taxonomy'   => 'package_type',
        'hide_empty' => false,
        'orderby'    => 'term_id',
        'order'      => 'ASC',
    ] );

    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        foreach ( $terms as $term ) {
            $types[ $term->slug ] = $term->name;
        }
    }

    // Default fallback if no terms exist
    if ( empty( $types ) ) {
        $types = [
            'umrah'      => eu_t( 'Umrah',           [ 'id' => 'Umrah' ] ),
            'hajj'       => eu_t( 'Hajj',            [ 'id' => 'Haji' ] ),
            'umrah_plus' => eu_t( 'Umrah Plus',      [ 'id' => 'Umrah Plus' ] ),
            'family'     => eu_t( 'Family Package',  [ 'id' => 'Paket Keluarga' ] ),
            'vip'        => eu_t( 'VIP Premium',     [ 'id' => 'VIP Premium' ] ),
            'honeymoon'  => eu_t( 'Honeymoon Umrah', [ 'id' => 'Umrah Bulan Madu' ] ),
        ];
    }

    return $types;
}

/**
 * Get package type label for a specific post or slug
 */
function eu_get_package_type_label( ?int $post_id = null, string $fallback_slug = '' ): string {
    $types = eu_get_package_types();

    if ( $post_id ) {
        // First check term assignment
        $terms = get_the_terms( $post_id, 'package_type' );
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            return $terms[0]->name;
        }

        // Second check meta
        $slug = (string) get_post_meta( $post_id, 'eu_package_type', true );
        if ( $slug && isset( $types[ $slug ] ) ) {
            return $types[ $slug ];
        }
    }

    if ( $fallback_slug && isset( $types[ $fallback_slug ] ) ) {
        return $types[ $fallback_slug ];
    }

    return $fallback_slug ? ucfirst( str_replace( [ '-', '_' ], ' ', $fallback_slug ) ) : '';
}

/**
 * Get distinct departure months available from packages
 * Returns array of [ 'YYYY-MM' => 'Month YYYY' ]
 */
function eu_get_available_departure_months(): array {
    global $wpdb;

    $dates = $wpdb->get_col( "
        SELECT DISTINCT meta_value 
        FROM {$wpdb->postmeta} pm
        JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE p.post_type = 'umrah_package' 
          AND p.post_status = 'publish'
          AND pm.meta_key IN ('eu_departure_date', 'eu_estimate_month') 
          AND pm.meta_value != ''
    " );

    $months = [];

    if ( ! empty( $dates ) ) {
        foreach ( $dates as $d ) {
            $d = trim( $d );
            $ym = substr( $d, 0, 7 ); // 'YYYY-MM'
            if ( preg_match( '/^\d{4}-\d{2}$/', $ym ) ) {
                $time = strtotime( $ym . '-01' );
                if ( $time ) {
                    $months[ $ym ] = date_i18n( 'F Y', $time );
                }
            }
        }
        ksort( $months );
    }

    // If no dates exist in DB yet, generate next 6 upcoming months as graceful preview
    if ( empty( $months ) ) {
        for ( $i = 0; $i < 6; $i++ ) {
            $time = strtotime( "+{$i} month" );
            $ym   = date( 'Y-m', $time );
            $months[ $ym ] = date_i18n( 'F Y', $time );
        }
    }

    return $months;
}

/**
 * Check if package is featured
 */
function eu_is_featured_package( ?int $post_id = null ): bool {
    $post_id = $post_id ?? get_the_ID();
    return (bool) get_post_meta( $post_id, 'eu_is_featured', true );
}

/**
 * Render a package card
 */
function eu_render_package_card( ?int $post_id = null, bool $echo = true ): string {
    $post_id     = $post_id ?? get_the_ID();
    $title       = get_the_title( $post_id );
    $permalink   = get_permalink( $post_id );
    $duration    = eu_get_package_duration( $post_id );
    if ( $duration && is_numeric( trim( $duration ) ) ) {
        $duration = trim( $duration ) . ' ' . eu_t( 'Days', [ 'id' => 'Hari' ] );
    }
    $departure   = eu_get_departure_date( $post_id );
    $airline     = get_post_meta( $post_id, 'eu_airline', true );
    $is_featured = eu_is_featured_package( $post_id );
    $is_sold_out = (bool) get_post_meta( $post_id, 'eu_is_sold_out', true );
    $thumb_id    = get_post_thumbnail_id( $post_id );

    // Currency & price from post meta
    $currency      = (string) ( get_post_meta( $post_id, 'eu_currency', true ) ?: 'Rp' );
    $raw_price     = (string) get_post_meta( $post_id, 'eu_price', true );
    $raw_discount  = (string) get_post_meta( $post_id, 'eu_discount_price', true );
    $fmt_price     = $raw_price    ? eu_format_price( $raw_price, $currency )    : '';
    $fmt_discount  = $raw_discount ? eu_format_price( $raw_discount, $currency ) : '';

    // Hotel star rating — show highest of Makkah / Madinah
    $mk_rat  = (int) get_post_meta( $post_id, 'eu_hotel_makkah_rating',  true );
    $md_rat  = (int) get_post_meta( $post_id, 'eu_hotel_madinah_rating', true );
    $max_rat = max( $mk_rat, $md_rat );  // 0 if both empty
    
    // Package type: check taxonomy terms first, fallback to post meta
    $terms = get_the_terms( $post_id, 'package_type' );
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        $type_slug = $terms[0]->slug;
        $type_name = $terms[0]->name;
    } else {
        $pkg_type_val = get_post_meta( $post_id, 'eu_package_type', true );
        $type_slug    = $pkg_type_val ?: 'umrah';
        $type_name    = eu_get_package_type_label( $post_id, $type_slug );
    }

    ob_start();
    ?>
    <article class="eu-package-card<?php echo $is_featured ? ' is-featured' : ''; ?>" id="package-<?php echo (int) $post_id; ?>" data-category="<?php echo esc_attr( $type_slug ); ?>">
        <?php if ( $is_sold_out ) : ?>
            <span class="eu-package-badge eu-package-badge--sold"><?php eu_te( 'Sold Out', [ 'id' => 'Habis Terjual' ] ); ?></span>
        <?php elseif ( $is_featured ) : ?>
            <span class="eu-package-badge"><?php eu_te( 'Best Seller', [ 'id' => 'Terlaris' ] ); ?></span>
        <?php endif; ?>

        <?php if ( $thumb_id ) : ?>
            <img src="<?php echo esc_url( wp_get_attachment_image_url( $thumb_id, 'eu-thumb' ) ); ?>"
                 alt="<?php echo esc_attr( $title ); ?>"
                 class="eu-package-img"
                 loading="lazy">
        <?php else : ?>
            <div class="eu-package-img-placeholder" aria-hidden="true">🕋</div>
        <?php endif; ?>

        <div class="eu-package-body">
            <div class="eu-package-type"><?php echo esc_html( $type_name ); ?></div>
            <?php if ( $max_rat > 0 ) : ?>
            <div class="eu-package-stars" aria-label="<?php echo esc_attr( $max_rat ); ?> stars">
                <?php
                for ( $i = 1; $i <= 5; $i++ ) {
                    echo '<span class="eu-star' . ( $i <= $max_rat ? ' eu-star--filled' : '' ) . '" aria-hidden="true">★</span>';
                }
                ?>
            </div>
            <?php endif; ?>
            <h3 class="eu-package-name">
                <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
            </h3>

            <div class="eu-package-meta">
                <?php if ( $duration ) : ?>
                    <span class="eu-package-meta-item">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <?php echo esc_html( $duration ); ?>
                    </span>
                <?php endif; ?>
                <?php if ( $departure ) : ?>
                    <span class="eu-package-meta-item">
                        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?php echo esc_html( $departure ); ?>
                    </span>
                <?php endif; ?>
                <?php if ( $airline ) : ?>
                    <span class="eu-package-meta-item">
                        <svg viewBox="0 0 24 24"><path d="M17.8 19.2L16 11l3.5-3.5C21 6 21 4 19.5 2.5c-1.5-1.5-3.5-1.5-5 0L11 6 2.8 4.2L1 6l6 3L4 12l-2 1 3 3 3 3 1-2 3-3 3 6 2-1.8z"/></svg>
                        <?php echo esc_html( $airline ); ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="eu-package-divider"></div>

            <div class="eu-package-price-row">
                <?php if ( $fmt_price || $fmt_discount ) : ?>
                    <div class="eu-package-price">
                        <span class="from"><?php eu_te( 'Starting from', [ 'id' => 'Mulai dari' ] ); ?></span>
                        <?php if ( $fmt_discount ) : ?>
                            <del class="amount amount--original"><?php echo esc_html( $fmt_price ); ?></del>
                            <span class="amount amount--discount"><?php echo esc_html( $fmt_discount ); ?></span>
                        <?php else : ?>
                            <span class="amount"><?php echo esc_html( $fmt_price ); ?></span>
                        <?php endif; ?>
                        <span class="per"><?php eu_te( '/ person', [ 'id' => '/ orang' ] ); ?></span>
                    </div>
                <?php else : ?>
                    <div class="eu-package-price">
                        <span class="amount" style="font-size:1.05rem;color:var(--wp--preset--color--secondary)"><?php eu_te( 'Contact Us', [ 'id' => 'Hubungi Kami' ] ); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ( $is_sold_out ) : ?>
                    <span class="eu-package-card-btn eu-package-card-btn--sold">
                        <?php eu_te( 'Sold Out', [ 'id' => 'Habis Terjual' ] ); ?>
                    </span>
                <?php else : ?>
                    <a href="<?php echo esc_url( $permalink ); ?>" class="eu-package-card-btn">
                        <?php eu_te( 'Book Now', [ 'id' => 'Pesan Sekarang' ] ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </article>
    <?php
    $html = ob_get_clean();
    if ( $echo ) {
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    return $html;
}

// ─────────────────────────────────────────────────────────────────────────────
// NAVIGATION HELPERS
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Get primary nav menu HTML
 */
function eu_get_primary_nav( string $menu_class = 'eu-nav' ): string {
    if ( ! has_nav_menu( 'primary' ) ) return '';

    return wp_nav_menu( [
        'theme_location'  => 'primary',
        'menu_class'      => $menu_class,
        'container'       => false,
        'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        'link_before'     => '',
        'link_after'      => '',
        'walker'          => new EU_Nav_Walker(),
        'echo'            => false,
    ] );
}

/**
 * Custom nav walker for adding CTA class to last item
 */
class EU_Nav_Walker extends Walker_Nav_Menu {
    public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
        $classes   = empty( $data_object->classes ) ? [] : (array) $data_object->classes;
        $is_cta    = in_array( 'eu-nav-cta', $classes, true );
        $class_str = implode( ' ', array_filter( $classes ) );

        $output .= '<li class="' . esc_attr( $class_str ) . '">';

        $atts           = [];
        $atts['title']  = ! empty( $data_object->attr_title ) ? $data_object->attr_title : '';
        $atts['target'] = ! empty( $data_object->target ) ? $data_object->target : '';
        $atts['rel']    = ! empty( $data_object->xfn ) ? $data_object->xfn : '';
        $atts['href']   = ! empty( $data_object->url ) ? $data_object->url : '#';
        if ( $atts['target'] === '_blank' ) {
            $atts['rel'] = 'noopener noreferrer';
        }

        $attr_str = '';
        foreach ( $atts as $attr => $value ) {
            if ( $value ) {
                $attr_str .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
            }
        }

        $output .= '<a' . $attr_str . '>' . esc_html( $data_object->title ) . '</a>';
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// POST META HELPERS
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Get reading time estimate
 */
function eu_get_reading_time( ?int $post_id = null, int $wpm = 200 ): string {
    $post_id  = $post_id ?? get_the_ID();
    $content  = get_post_field( 'post_content', $post_id );
    $count    = str_word_count( strip_tags( $content ) );
    $minutes  = max( 1, (int) ceil( $count / $wpm ) );
    return sprintf(
        _n( '%d min read', '%d min read', $minutes, 'elegant-umrah' ),
        $minutes
    );
}

/**
 * Get post category links
 */
function eu_get_category_links( ?int $post_id = null, string $sep = ', ' ): string {
    $post_id = $post_id ?? get_the_ID();
    $cats    = get_the_category( $post_id );
    if ( ! $cats ) return '';

    $links = [];
    foreach ( $cats as $cat ) {
        $links[] = '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '" class="eu-blog-cat">' . esc_html( $cat->name ) . '</a>';
    }
    return implode( $sep, $links );
}

/**
 * Get post Islamic category
 */
function eu_get_islamic_category( ?int $post_id = null ): string {
    $post_id    = $post_id ?? get_the_ID();
    $terms      = get_the_terms( $post_id, 'islamic_category' );
    if ( ! $terms || is_wp_error( $terms ) ) return '';
    return esc_html( $terms[0]->name );
}

// ─────────────────────────────────────────────────────────────────────────────
// TEMPLATE HELPERS
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Render SVG icon (inline)
 */
function eu_icon( string $name ): string {
    $icons = [
        'clock'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'calendar'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        'map-pin'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'phone'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.36 2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
        'mail'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
        'star'      => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
        'check'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>',
        'user'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        'plane'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17.8 19.2L16 11l3.5-3.5C21 6 21 4 19.5 2.5c-1.5-1.5-3.5-1.5-5 0L11 6 2.8 4.2L1 6l6 3L4 12l-2 1 3 3 3 3 1-2 3-3 3 6 2-1.8z"/></svg>',
        'arrow-up'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>',
        'globe'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
        'home'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
    ];

    return $icons[ $name ] ?? '';
}

/**
 * Output section header markup
 */
function eu_section_header( string $label, string $title, string $desc = '', bool $dark = false ): void {
    $title_class = $dark ? 'eu-section-title text-white' : 'eu-section-title';
    $desc_class  = 'eu-section-desc';
    ?>
    <header class="eu-section-header">
        <span class="eu-section-label"><?php echo esc_html( $label ); ?></span>
        <h2 class="<?php echo esc_attr( $title_class ); ?>"><?php echo esc_html( $title ); ?></h2>
        <div class="eu-separator" aria-hidden="true"></div>
        <?php if ( $desc ) : ?>
            <p class="<?php echo esc_attr( $desc_class ); ?>"><?php echo esc_html( $desc ); ?></p>
        <?php endif; ?>
    </header>
    <?php
}

/**
 * Render star rating
 */
function eu_stars( int $rating = 5 ): string {
    $stars = '';
    for ( $i = 0; $i < 5; $i++ ) {
        $stars .= $i < $rating ? '★' : '☆';
    }
    return '<span class="eu-stars" aria-label="' . $rating . ' out of 5 stars">' . $stars . '</span>';
}

/**
 * Get dynamic Header brand HTML (Logo, Site Title, Tagline)
 */
function eu_get_header_brand_html(): string {
    $title = get_bloginfo( 'name' );
    if ( ! $title || 'Local Development' === $title ) {
        $title = 'Elegant Umrah';
    }
    $tagline = get_bloginfo( 'description' );
    if ( ! $tagline || 'Just another WordPress site' === $tagline ) {
        $tagline = 'Travel & Pilgrimage';
    }

    $logo_url = esc_url( (string) get_theme_mod( 'eu_custom_logo_url', '' ) );
    if ( ! $logo_url && has_custom_logo() ) {
        $logo_data = wp_get_attachment_image_src( (int) get_theme_mod( 'custom_logo' ), 'full' );
        if ( ! empty( $logo_data[0] ) ) {
            $logo_url = esc_url( $logo_data[0] );
        }
    }

    $icon_emoji = (string) get_theme_mod( 'eu_logo_icon_text', '🕌' );
    if ( ! $icon_emoji ) {
        $icon_emoji = '🕌';
    }

    $show_text = (bool) get_theme_mod( 'eu_show_title_with_logo', true );
    $home_url  = esc_url( home_url( '/' ) );

    $html = '<a href="' . $home_url . '" class="eu-logo" style="display:flex;align-items:center;gap:0.75rem;text-decoration:none">';

    if ( $logo_url ) {
        $html .= '<div class="eu-logo-img-wrap" style="display:flex;align-items:center;flex-shrink:0">';
        $html .= '<img src="' . $logo_url . '" alt="' . esc_attr( $title ) . '" class="eu-custom-logo" style="max-height:48px;width:auto;object-fit:contain" />';
        $html .= '</div>';
    } else {
        $html .= '<div class="eu-logo-icon" style="width:42px;height:42px;background:linear-gradient(135deg,#D4AF37,#B8960C);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0">';
        $html .= '<span style="line-height:1">' . esc_html( $icon_emoji ) . '</span>';
        $html .= '</div>';
    }

    if ( $show_text || ! $logo_url ) {
        $html .= '<div class="eu-logo-text" style="display:flex;flex-direction:column">';
        $html .= '<span style="font-size:1.1rem;font-weight:700;color:#FFFFFF;line-height:1.1">' . esc_html( $title ) . '</span>';
        if ( $tagline ) {
            $html .= '<span class="secondary-text" style="font-size:0.65rem;letter-spacing:0.1em;text-transform:uppercase;color:#D4AF37">' . esc_html( $tagline ) . '</span>';
        }
        $html .= '</div>';
    }

    $html .= '</a>';
    return $html;
}

/**
 * Get dynamic Mobile Nav HTML (Toggle button + Drawer)
 */
function eu_get_mobile_nav_html(): string {
    $home_url     = home_url( '/' );
    $packages_url = home_url( '/packages/' );
    $articles_url = home_url( '/articles/' );
    $about_url    = home_url( '/about/' );
    $contact_url  = home_url( '/contact/' );

    $html  = '<button class="eu-menu-toggle" id="eu-menu-toggle" aria-label="Toggle Navigation Menu" aria-expanded="false" aria-controls="eu-mobile-nav">';
    $html .= '<span></span><span></span><span></span>';
    $html .= '</button>';

    $html .= '<nav class="eu-mobile-nav" id="eu-mobile-nav" aria-label="Mobile Navigation" role="navigation">';
    $html .= '<ul>';
    $html .= '<li><a href="' . esc_url( $home_url ) . '">Home</a></li>';
    $html .= '<li class="menu-item-has-children">';
    $html .= '<a href="' . esc_url( $packages_url ) . '">Packages</a>';
    $html .= '<ul class="sub-menu">';
    $html .= '<li><a href="' . esc_url( $packages_url . '?type=umrah' ) . '">Umrah Packages</a></li>';
    $html .= '<li><a href="' . esc_url( $packages_url . '?type=hajj' ) . '">Hajj Packages</a></li>';
    $html .= '<li><a href="' . esc_url( $packages_url . '?type=vip' ) . '">VIP Packages</a></li>';
    $html .= '<li><a href="' . esc_url( $packages_url . '?type=family' ) . '">Family Packages</a></li>';
    $html .= '</ul>';
    $html .= '</li>';
    $html .= '<li><a href="' . esc_url( $articles_url ) . '">Articles</a></li>';
    $html .= '<li><a href="' . esc_url( $about_url ) . '">About Us</a></li>';
    $html .= '<li class="eu-nav-cta"><a href="' . esc_url( $contact_url ) . '">Contact Us</a></li>';
    $html .= '</ul>';
    $html .= '</nav>';

    return $html;
}

/**
 * Get dynamic Hero Media / Slideshow HTML
 */
function eu_get_hero_media_html(): string {
    $hero_config = function_exists( 'eu_get_hero_config' ) ? eu_get_hero_config() : [ 'images' => [], 'overlay_opacity' => 0.70, 'interval' => 5 ];
    $hero_images = $hero_config['images'] ?? [];
    if ( empty( $hero_images ) ) {
        return '';
    }

    $is_slideshow = count( $hero_images ) > 1;
    $interval     = (string) ( $hero_config['interval'] ?? 5 );
    $opacity      = (string) ( $hero_config['overlay_opacity'] ?? 0.70 );

    ob_start();
    ?>
    <div class="eu-hero-media" data-interval="<?php echo esc_attr( $interval ); ?>" aria-hidden="true">
        <div class="eu-hero-slides">
            <?php foreach ( $hero_images as $index => $img_url ) : ?>
                <div class="eu-hero-slide<?php echo 0 === $index ? ' is-active' : ''; ?>" style="background-image: url('<?php echo esc_url( $img_url ); ?>')" data-index="<?php echo esc_attr( (string) $index ); ?>"></div>
            <?php endforeach; ?>
        </div>
        <div class="eu-hero-overlay-layer" style="background: rgba(11, 11, 11, <?php echo esc_attr( $opacity ); ?>)"></div>
        <?php if ( $is_slideshow ) : ?>
            <div class="eu-hero-dots" role="tablist" aria-label="<?php esc_attr_e( 'Hero Slideshow Pagination', 'elegant-umrah' ); ?>">
                <?php foreach ( $hero_images as $index => $img_url ) : ?>
                    <button type="button" class="eu-hero-dot<?php echo 0 === $index ? ' is-active' : ''; ?>" data-slide-index="<?php echo esc_attr( (string) $index ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'elegant-umrah' ), $index + 1 ) ); ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Get dynamic Footer brand HTML (Logo, Site Title, Tagline)
 */
function eu_get_footer_brand_html(): string {
    $title = get_bloginfo( 'name' );
    if ( ! $title || 'Local Development' === $title ) {
        $title = 'Elegant Umrah';
    }
    $tagline = get_bloginfo( 'description' );
    if ( ! $tagline || 'Just another WordPress site' === $tagline ) {
        $tagline = 'Premium Umrah & Hajj Travel';
    }

    $logo_url = esc_url( (string) get_theme_mod( 'eu_custom_logo_url', '' ) );
    if ( ! $logo_url && has_custom_logo() ) {
        $logo_data = wp_get_attachment_image_src( (int) get_theme_mod( 'custom_logo' ), 'full' );
        if ( ! empty( $logo_data[0] ) ) {
            $logo_url = esc_url( $logo_data[0] );
        }
    }

    $icon_emoji = (string) get_theme_mod( 'eu_logo_icon_text', '🕌' );
    if ( ! $icon_emoji ) {
        $icon_emoji = '🕌';
    }

    $html = '<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem">';
    if ( $logo_url ) {
        $html .= '<img src="' . $logo_url . '" alt="' . esc_attr( $title ) . '" class="eu-footer-custom-logo" style="max-height:44px;width:auto;object-fit:contain" />';
    } else {
        $html .= '<p style="font-size:1.5rem;margin:0">' . esc_html( $icon_emoji ) . '</p>';
    }
    $html .= '<span style="font-size:1.3rem;font-weight:700;color:#FFFFFF">' . esc_html( $title ) . '</span>';
    $html .= '</div>';

    if ( $tagline ) {
        $html .= '<p style="font-size:0.75rem;letter-spacing:0.1em;text-transform:uppercase;color:#D4AF37;margin-bottom:1.25rem">' . esc_html( $tagline ) . '</p>';
    }

    return $html;
}

/**
 * Get Hero configuration (images, overlay opacity, slide interval)
 */
function eu_get_hero_config(): array {
    $images = [];

    // 1. Bulk image URLs from textarea
    $bulk = (string) get_theme_mod( 'eu_hero_image_urls', '' );
    if ( $bulk ) {
        $lines = preg_split( '/\r\n|\r|\n/', $bulk );
        foreach ( $lines as $line ) {
            $clean_url = esc_url_raw( trim( $line ) );
            if ( $clean_url && filter_var( $clean_url, FILTER_VALIDATE_URL ) ) {
                $images[] = $clean_url;
            }
        }
    }

    // 2. Individual slots (1 to 5)
    for ( $i = 1; $i <= 5; $i++ ) {
        $img = esc_url_raw( (string) get_theme_mod( "eu_hero_image_{$i}", '' ) );
        if ( ! $img ) {
            $img = esc_url_raw( (string) get_theme_mod( "eu_hero_image_url_{$i}", '' ) );
        }
        if ( $img && filter_var( $img, FILTER_VALIDATE_URL ) ) {
            $images[] = $img;
        }
    }

    $images = array_values( array_unique( $images ) );

    $overlay_opacity = (float) get_theme_mod( 'eu_hero_overlay_opacity', '0.70' );
    $overlay_opacity = min( 0.95, max( 0.20, $overlay_opacity ) );

    $interval = (int) get_theme_mod( 'eu_hero_slide_interval', 5 );
    $interval = min( 15, max( 3, $interval ) );

    return [
        'images'          => $images,
        'overlay_opacity' => $overlay_opacity,
        'interval'        => $interval,
    ];
}

/**
 * Render Breadcrumbs (Supports Yoast SEO Breadcrumbs with native fallback)
 */
function eu_render_breadcrumbs(): void {
    if ( function_exists( 'yoast_breadcrumb' ) ) {
        yoast_breadcrumb( '<nav class="eu-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'elegant-umrah' ) . '">', '</nav>' );
    }
}
