<?php
/**
 * Articles — Helpers & AJAX Handlers
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ─────────────────────────────────────────────────────────────────────────────
// BADGE HELPERS
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Return styled Islamic-category badge HTML for a post.
 */
function eu_get_islamic_category_badge( ?int $post_id = null ): string {
    $post_id = $post_id ?? get_the_ID();
    $terms   = get_the_terms( $post_id, 'islamic_category' );
    if ( ! $terms || is_wp_error( $terms ) ) {
        // Fall back to standard category
        $cats = get_the_category( $post_id );
        if ( ! $cats ) return '';
        $name = esc_html( $cats[0]->name );
        $url  = esc_url( get_category_link( $cats[0]->term_id ) );
    } else {
        $name = esc_html( $terms[0]->name );
        $url  = esc_url( get_term_link( $terms[0] ) );
    }
    return '<a href="' . $url . '" class="eu-article-cat-badge">' . $name . '</a>';
}

/**
 * Render a single article card (used by AJAX filter & template).
 */
function eu_render_article_card( ?int $post_id = null, bool $echo = true ): string {
    $post_id   = $post_id ?? get_the_ID();
    $title     = get_the_title( $post_id );
    $permalink = get_permalink( $post_id );
    $excerpt   = eu_get_excerpt( $post_id, 18 );
    $date      = get_the_date( 'd M Y', $post_id );
    $read_time = eu_get_reading_time( $post_id );
    $thumb_id  = get_post_thumbnail_id( $post_id );
    $thumb_url = $thumb_id ? esc_url( wp_get_attachment_image_url( $thumb_id, 'eu-thumb' ) ) : '';
    $badge     = eu_get_islamic_category_badge( $post_id );

    // Collect islamic_category slug for data-category attr
    $terms   = get_the_terms( $post_id, 'islamic_category' );
    $cat_slug = ( $terms && ! is_wp_error( $terms ) ) ? esc_attr( $terms[0]->slug ) : 'uncategorized';

    ob_start();
    ?>
    <article class="eu-article-card" data-category="<?php echo $cat_slug; ?>" id="article-<?php echo $post_id; ?>">
        <a href="<?php echo esc_url( $permalink ); ?>" class="eu-article-thumb-link" tabindex="-1" aria-hidden="true">
            <?php if ( $thumb_url ) : ?>
                <div class="eu-article-thumb" style="background-image:url('<?php echo $thumb_url; ?>')"></div>
            <?php else : ?>
                <div class="eu-article-thumb eu-article-thumb--placeholder">
                    <span aria-hidden="true">📖</span>
                </div>
            <?php endif; ?>
        </a>
        <div class="eu-article-body">
            <?php if ( $badge ) : ?>
                <div class="eu-article-cat"><?php echo $badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
            <?php endif; ?>
            <h3 class="eu-article-title">
                <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
            </h3>
            <?php if ( $excerpt ) : ?>
                <p class="eu-article-excerpt"><?php echo esc_html( $excerpt ); ?></p>
            <?php endif; ?>
            <div class="eu-article-meta">
                <span class="eu-article-date">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?php echo esc_html( $date ); ?>
                </span>
                <span class="eu-article-read-time">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <?php echo esc_html( $read_time ); ?>
                </span>
            </div>
            <a href="<?php echo esc_url( $permalink ); ?>" class="eu-article-read-link" aria-label="<?php echo esc_attr( sprintf( __( 'Read: %s', 'elegant-umrah' ), $title ) ); ?>">
                <?php esc_html_e( 'Read Article', 'elegant-umrah' ); ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </article>
    <?php
    $html = ob_get_clean();
    if ( $echo ) {
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    return $html;
}

/**
 * Render the featured (hero) article card — large horizontal layout.
 */
function eu_render_featured_article( ?int $post_id = null ): void {
    $post_id   = $post_id ?? get_the_ID();
    $title     = get_the_title( $post_id );
    $permalink = get_permalink( $post_id );
    $excerpt   = eu_get_excerpt( $post_id, 30 );
    $date      = get_the_date( 'd F Y', $post_id );
    $read_time = eu_get_reading_time( $post_id );
    $thumb_id  = get_post_thumbnail_id( $post_id );
    $thumb_url = $thumb_id ? esc_url( wp_get_attachment_image_url( $thumb_id, 'eu-hero' ) ) : '';
    $badge     = eu_get_islamic_category_badge( $post_id );
    $author    = get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) );
    ?>
    <article class="eu-article-featured" id="featured-article-<?php echo $post_id; ?>">
        <a href="<?php echo esc_url( $permalink ); ?>" class="eu-article-featured-thumb-link" tabindex="-1" aria-hidden="true">
            <?php if ( $thumb_url ) : ?>
                <div class="eu-article-featured-thumb" style="background-image:url('<?php echo $thumb_url; ?>')"></div>
            <?php else : ?>
                <div class="eu-article-featured-thumb eu-article-featured-thumb--placeholder"></div>
            <?php endif; ?>
        </a>
        <div class="eu-article-featured-body">
            <div class="eu-article-featured-top">
                <span class="eu-article-featured-label">✦ Featured Article</span>
                <?php if ( $badge ) echo $badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
            <h2 class="eu-article-featured-title">
                <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
            </h2>
            <?php if ( $excerpt ) : ?>
                <p class="eu-article-featured-excerpt"><?php echo esc_html( $excerpt ); ?></p>
            <?php endif; ?>
            <div class="eu-article-featured-meta">
                <span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <?php echo esc_html( $author ); ?>
                </span>
                <span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?php echo esc_html( $date ); ?>
                </span>
                <span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <?php echo esc_html( $read_time ); ?>
                </span>
            </div>
            <a href="<?php echo esc_url( $permalink ); ?>" class="eu-btn eu-btn-cta eu-article-featured-cta">
                <?php esc_html_e( 'Read Full Article', 'elegant-umrah' ); ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </article>
    <?php
}

/**
 * Render related articles for single post (3 posts from same islamic_category).
 */
function eu_render_related_articles( ?int $post_id = null ): void {
    $post_id = $post_id ?? get_the_ID();
    $terms   = get_the_terms( $post_id, 'islamic_category' );

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'post__not_in'   => [ $post_id ],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if ( $terms && ! is_wp_error( $terms ) ) {
        $args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            [
                'taxonomy' => 'islamic_category',
                'field'    => 'term_id',
                'terms'    => wp_list_pluck( $terms, 'term_id' ),
            ],
        ];
    }

    $related = new WP_Query( $args );

    if ( ! $related->have_posts() ) return;
    ?>
    <section class="eu-related-articles" aria-label="<?php esc_attr_e( 'Related Articles', 'elegant-umrah' ); ?>">
        <div class="eu-related-articles-inner">
            <header class="eu-section-header" style="text-align:center;margin-bottom:2.5rem">
                <span class="eu-section-label"><?php esc_html_e( 'Continue Reading', 'elegant-umrah' ); ?></span>
                <h2 class="eu-section-title" style="font-size:clamp(1.4rem,3vw,2rem)"><?php esc_html_e( 'Related Articles', 'elegant-umrah' ); ?></h2>
                <div class="eu-separator" aria-hidden="true"></div>
            </header>
            <div class="eu-related-grid">
                <?php
                while ( $related->have_posts() ) {
                    $related->the_post();
                    eu_render_article_card( get_the_ID() );
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>
    <?php
}

// Shortcode wrapper so FSE templates can call it via wp:shortcode
add_shortcode( 'eu_related_articles', function() {
    ob_start();
    eu_render_related_articles();
    return ob_get_clean();
} );

// ─────────────────────────────────────────────────────────────────────────────
// AJAX: FILTER ARTICLES BY ISLAMIC CATEGORY
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'wp_ajax_eu_filter_articles',        'eu_ajax_filter_articles' );
add_action( 'wp_ajax_nopriv_eu_filter_articles', 'eu_ajax_filter_articles' );

function eu_ajax_filter_articles(): void {
    check_ajax_referer( 'eu_nonce', 'nonce' );

    $cat_slug = sanitize_text_field( $_POST['category'] ?? 'all' );
    $paged    = max( 1, (int) ( $_POST['paged'] ?? 1 ) );

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 9,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if ( $cat_slug && 'all' !== $cat_slug ) {
        $args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            [
                'taxonomy' => 'islamic_category',
                'field'    => 'slug',
                'terms'    => $cat_slug,
            ],
        ];
    }

    $query = new WP_Query( $args );

    ob_start();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            eu_render_article_card( get_the_ID() );
        }
        wp_reset_postdata();
    } else {
        ?>
        <div class="eu-no-results" style="grid-column:1/-1;text-align:center;padding:4rem 0">
            <p style="font-size:2.5rem;margin-bottom:1rem">🔍</p>
            <h3 style="color:#0B0B0B;font-size:1.4rem;font-weight:700;margin-bottom:0.5rem">
                <?php esc_html_e( 'No articles found.', 'elegant-umrah' ); ?>
            </h3>
            <p style="color:#666"><?php esc_html_e( 'Try selecting a different category.', 'elegant-umrah' ); ?></p>
        </div>
        <?php
    }

    $html       = ob_get_clean();
    $max_pages  = (int) $query->max_num_pages;

    wp_send_json_success( [
        'html'      => $html,
        'max_pages' => $max_pages,
        'paged'     => $paged,
    ] );
}
