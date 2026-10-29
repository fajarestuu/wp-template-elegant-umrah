<?php
/**
 * Blog Home / Articles Archive Template
 *
 * Used when WordPress "Posts page" is set in Settings → Reading.
 * URL: /articles (or wherever the Posts page is assigned)
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Pre-render block template parts before wp_head() so block styles enqueue properly
$eu_header_html = do_blocks( '<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->' );
$eu_footer_html = do_blocks( '<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->' );

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'eu-articles-page' ); ?>>
<?php wp_body_open(); ?>

<?php echo $eu_header_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<main class="eu-main-content" id="main-content">

    <!-- ============================================================
         ARTICLES HERO HEADER
         ============================================================ -->
    <div class="eu-articles-hero alignfull">
        <div class="eu-articles-hero-inner">
            <div class="eu-articles-hero-pattern" aria-hidden="true"></div>
            <div class="eu-articles-hero-content">
                <span class="eu-section-label" style="color:#D4AF37;font-size:0.75rem;font-weight:600;letter-spacing:0.2em;text-transform:uppercase">
                    ✦ <?php eu_te( 'Knowledge & Inspiration', [ 'id' => 'Ilmu & Inspirasi' ] ); ?>
                </span>
                <h1 class="eu-articles-hero-title"><?php eu_te( 'News & Articles', [ 'id' => 'Berita & Artikel' ] ); ?></h1>
                <p class="eu-articles-hero-desc">
                    <?php eu_te( 'Deepen your understanding of Hajj and Umrah. Tips, guides, and spiritual insights curated for the modern Muslim traveller.', [ 'id' => 'Perdalam pemahaman Anda tentang Haji dan Umrah. Tips, panduan, dan wawasan spiritual pilihan untuk jamaah masa kini.' ] ); ?>
                </p>
                <div class="eu-articles-hero-tags">
                    <span class="eu-articles-hero-tag">🕌 Manasik</span>
                    <span class="eu-articles-hero-tag">📖 Fiqh Ibadah</span>
                    <span class="eu-articles-hero-tag">🤲 Doa &amp; Dzikir</span>
                    <span class="eu-articles-hero-tag">💡 Tips Umrah</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         CATEGORY FILTER BAR
         ============================================================ -->
    <div class="eu-article-filter-wrap alignfull" id="eu-article-filter-wrap">
        <div class="eu-article-filter" role="tablist" aria-label="<?php esc_attr_e( 'Filter articles by category', 'elegant-umrah' ); ?>">
            <button class="eu-article-filter-btn active" data-category="all" role="tab" aria-selected="true">
                <?php eu_te( 'All Articles', [ 'id' => 'Semua Artikel' ] ); ?>
            </button>
            <?php
            // Dynamically render islamic_category terms
            $islamic_cats = get_terms( [
                'taxonomy'   => 'islamic_category',
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ] );

            $cat_icons = [
                'tips-umrah'   => '💡',
                'manasik'      => '🕌',
                'fiqh-ibadah'  => '📖',
                'doa-dzikir'   => '🤲',
                'kisah-islami' => '📜',
                'motivasi'     => '🌟',
            ];

            if ( ! is_wp_error( $islamic_cats ) && ! empty( $islamic_cats ) ) :
                foreach ( $islamic_cats as $cat ) :
                    $icon = $cat_icons[ $cat->slug ] ?? '📄';
                    ?>
                    <button class="eu-article-filter-btn"
                            data-category="<?php echo esc_attr( $cat->slug ); ?>"
                            role="tab"
                            aria-selected="false">
                        <?php echo esc_html( $icon . ' ' . $cat->name ); ?>
                    </button>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>

    <!-- ============================================================
         ARTICLES GRID SECTION
         ============================================================ -->
    <div class="eu-section eu-articles-section alignfull" style="background-color:#F8F8F8;padding:clamp(2.5rem,6vw,4rem) 1.5rem clamp(3rem,7vw,5rem)">
        <div class="eu-articles-container" id="eu-articles-container">

            <?php
            // ── Featured Article (most recent post) ─────────────────────
            $featured_query = new WP_Query( [
                'post_type'      => 'post',
                'posts_per_page' => 1,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ] );

            if ( $featured_query->have_posts() ) :
                $featured_query->the_post();
                eu_render_featured_article( get_the_ID() );
                wp_reset_postdata();
            endif;

            // ── Article Grid (posts 2–10) ─────────────────────────────
            $paged          = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
            $articles_query = new WP_Query( [
                'post_type'      => 'post',
                'posts_per_page' => 9,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'paged'          => $paged,
                'offset'         => ( $paged === 1 ) ? 1 : 0, // skip featured on page 1
            ] );
            ?>

            <div class="eu-article-grid" id="eu-article-grid" role="list">
                <?php
                if ( $articles_query->have_posts() ) :
                    while ( $articles_query->have_posts() ) :
                        $articles_query->the_post();
                        eu_render_article_card( get_the_ID() );
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p style="grid-column:1/-1;text-align:center;color:#666;padding:3rem 0">'
                        . esc_html__( 'No articles yet. Check back soon!', 'elegant-umrah' )
                        . '</p>';
                endif;
                ?>
            </div>

            <!-- Load More / Pagination -->
            <div class="eu-articles-pagination" id="eu-articles-pagination">
                <?php if ( $articles_query->max_num_pages > 1 ) : ?>
                    <button class="eu-articles-load-more"
                            id="eu-articles-load-more"
                            data-paged="2"
                            data-max-pages="<?php echo (int) $articles_query->max_num_pages; ?>">
                        <?php esc_html_e( 'Load More Articles', 'elegant-umrah' ); ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="width:16px;height:16px"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                    </button>
                <?php endif; ?>
            </div>

        </div>
        <!-- /eu-articles-container -->

        <!-- Loading overlay -->
        <div class="eu-articles-loading" id="eu-articles-loading" aria-live="polite" aria-label="<?php esc_attr_e( 'Loading', 'elegant-umrah' ); ?>" style="display:none">
            <div class="eu-articles-loading-spinner"></div>
            <span><?php esc_html_e( 'Loading articles…', 'elegant-umrah' ); ?></span>
        </div>
    </div>

    <!-- ============================================================
         NEWSLETTER / SUBSCRIPTION CTA
         ============================================================ -->
    <div class="eu-articles-cta alignfull">
        <div class="eu-articles-cta-inner">
            <div class="eu-articles-cta-icon" aria-hidden="true">📬</div>
            <h2 class="eu-articles-cta-title"><?php esc_html_e( 'Stay Updated with Spiritual Insights', 'elegant-umrah' ); ?></h2>
            <p class="eu-articles-cta-desc">
                <?php esc_html_e( 'Get the latest articles on Hajj, Umrah, and Islamic guidance delivered straight to your inbox.', 'elegant-umrah' ); ?>
            </p>
            <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="eu-btn eu-btn-cta eu-articles-cta-btn">
                <?php esc_html_e( 'Subscribe to Newsletter', 'elegant-umrah' ); ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>

</main>

<?php echo $eu_footer_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<?php wp_footer(); ?>
</body>
</html>
