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
 * Get formatted package price
 */
function eu_get_package_price( ?int $post_id = null, string $currency = 'USD' ): string {
    $post_id = $post_id ?? get_the_ID();
    $price   = get_post_meta( $post_id, 'eu_price', true );
    if ( ! $price ) return '';

    // If already has currency symbol, return as-is
    if ( preg_match( '/[^0-9.,]/', $price ) ) {
        return esc_html( $price );
    }

    $symbols = [ 'USD' => '$', 'IDR' => 'Rp', 'MYR' => 'RM', 'SGD' => 'S$', 'SAR' => 'SAR' ];
    $symbol  = $symbols[ $currency ] ?? $currency . ' ';

    return $symbol . number_format( (float) $price, 0, ',', '.' );
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
    $price       = eu_get_package_price( $post_id );
    $duration    = eu_get_package_duration( $post_id );
    $departure   = eu_get_departure_date( $post_id );
    $hotel       = get_post_meta( $post_id, 'eu_hotel_info', true );
    $airline     = get_post_meta( $post_id, 'eu_airline', true );
    $is_featured = eu_is_featured_package( $post_id );
    $thumb_id    = get_post_thumbnail_id( $post_id );
    
    // Package type is saved in post meta, not taxonomy
    $pkg_type_val = get_post_meta( $post_id, 'eu_package_type', true );
    $type_slug    = $pkg_type_val ?: 'umrah';
    
    // Map slug to name
    $type_labels = [
        'umrah'      => 'Umrah',
        'hajj'       => 'Hajj',
        'umrah_plus' => 'Umrah Plus',
        'family'     => 'Family Package',
        'vip'        => 'VIP Premium',
        'honeymoon'  => 'Honeymoon Umrah',
    ];
    $type_name = $type_labels[ $type_slug ] ?? ucfirst( str_replace( '_', ' ', $type_slug ) );

    ob_start();
    ?>
    <article class="eu-package-card<?php echo $is_featured ? ' is-featured' : ''; ?>" id="package-<?php echo $post_id; ?>" data-category="<?php echo esc_attr( $type_slug ); ?>">
        <?php if ( $is_featured ) : ?>
            <span class="eu-package-badge"><?php esc_html_e( 'Best Seller', EU_TEXT_DOMAIN ); ?></span>
        <?php endif; ?>

        <?php if ( $thumb_id ) : ?>
            <img src="<?php echo esc_url( eu_get_image_url( $thumb_id, 'eu-card' ) ); ?>"
                 alt="<?php echo esc_attr( $title ); ?>"
                 class="eu-package-img"
                 loading="lazy">
        <?php else : ?>
            <div class="eu-package-img-placeholder" aria-hidden="true">🕌</div>
        <?php endif; ?>

        <div class="eu-package-body">
            <div class="eu-package-type"><?php echo esc_html( $type_name ); ?></div>
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
                <?php if ( $hotel ) : ?>
                    <span class="eu-package-meta-item">
                        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <?php echo esc_html( $hotel ); ?>
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
                <?php if ( $price ) : ?>
                    <div class="eu-package-price">
                        <span class="from"><?php esc_html_e( 'Starting from', EU_TEXT_DOMAIN ); ?></span>
                        <span class="amount"><?php echo esc_html( $price ); ?></span>
                        <span class="per"><?php esc_html_e( '/ person', EU_TEXT_DOMAIN ); ?></span>
                    </div>
                <?php endif; ?>
                <a href="<?php echo esc_url( $permalink ); ?>" class="eu-package-card-btn">
                    <?php esc_html_e( 'Book Now', EU_TEXT_DOMAIN ); ?>
                </a>
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
        _n( '%d min read', '%d min read', $minutes, EU_TEXT_DOMAIN ),
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
    $desc_class  = $dark ? 'eu-section-desc' : 'eu-section-desc';
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
