<?php
/**
 * Single Post Template (Articles)
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
<body <?php body_class( 'eu-single-post-page' ); ?>>
<?php wp_body_open(); ?>

<!-- Reading Progress Bar -->
<div class="eu-read-progress-wrap" aria-hidden="true">
    <div class="eu-read-progress" id="eu-read-progress"></div>
</div>

<?php echo $eu_header_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<main class="eu-main-content" id="main-content">

<?php while ( have_posts() ) : the_post(); ?>

    <?php
    $post_id   = get_the_ID();
    $prev_post = get_previous_post();
    $next_post = get_next_post();
    $share_url = rawurlencode( get_permalink() );
    $share_title = rawurlencode( get_the_title() );
    ?>

    <!-- ============================================================
         FEATURED IMAGE HERO
         ============================================================ -->
    <div class="eu-post-image-hero alignfull">
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="eu-post-hero-img-wrap">
                <?php the_post_thumbnail( 'eu-hero', [
                    'class' => 'eu-post-hero-img',
                    'alt'   => esc_attr( get_the_title() ),
                ] ); ?>
            </div>
        <?php else : ?>
            <div class="eu-post-hero-placeholder"></div>
        <?php endif; ?>
        <div class="eu-post-hero-overlay" aria-hidden="true"></div>
    </div>

    <!-- ============================================================
         POST HEADER
         ============================================================ -->
    <div class="eu-single-post" style="padding:3rem 1.5rem 2rem">

        <?php eu_render_breadcrumbs(); ?>

        <div class="eu-post-header" style="margin-bottom:2.5rem;text-align:center">

            <!-- Islamic Category Badge -->
            <div class="eu-post-header-badges">
                <?php echo eu_get_islamic_category_badge( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>

            <!-- Title -->
            <h1 class="eu-post-title"><?php the_title(); ?></h1>

            <!-- Meta bar: author · date · reading time -->
            <div class="eu-post-meta-bar">
                <span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="width:14px;height:14px"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <?php the_author(); ?>
                </span>
                <span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="width:14px;height:14px"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?php echo esc_html( get_the_date( 'd F Y' ) ); ?>
                </span>
                <span class="eu-post-read-time">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="width:14px;height:14px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <?php echo esc_html( eu_get_reading_time( $post_id ) ); ?>
                </span>
            </div>

            <hr class="eu-separator" style="margin:1.5rem auto;max-width:60px;border:none;border-top:2px solid #D4AF37">
        </div>

        <!-- ============================================================
             POST CONTENT
             ============================================================ -->
        <div class="eu-post-content">
            <?php the_content(); ?>
        </div>

        <!-- ============================================================
             POST FOOTER: TAGS + SHARE + NAV
             ============================================================ -->
        <div class="eu-post-footer" style="margin-top:3rem;padding-top:2rem;border-top:1px solid rgba(0,0,0,0.08)">

            <!-- Tags & Islamic Category -->
            <div style="margin-bottom:1rem">
                <?php
                $tags = get_the_tags();
                if ( $tags ) {
                    echo '<div class="eu-post-tags">Tags: ';
                    $tag_links = [];
                    foreach ( $tags as $tag ) {
                        $tag_links[] = '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a>';
                    }
                    echo implode( ', ', $tag_links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Share + Navigation row -->
            <div class="eu-post-nav-row">
                <!-- Share buttons -->
                <div class="eu-post-share">
                    <span class="eu-post-share-label"><?php esc_html_e( 'Share:', 'elegant-umrah' ); ?></span>
                    <a href="https://wa.me/?text=<?php echo $share_title; ?>%20<?php echo $share_url; ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="eu-post-share-btn eu-post-share-wa"
                       aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'elegant-umrah' ); ?>">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.136.562 4.139 1.535 5.875L.057 23.943l6.204-1.483A11.947 11.947 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.8 9.8 0 0 1-5.021-1.377l-.361-.214-3.712.888.916-3.62-.236-.373A9.782 9.782 0 0 1 2.182 12c0-5.421 4.397-9.818 9.818-9.818 5.421 0 9.818 4.397 9.818 9.818 0 5.421-4.397 9.818-9.818 9.818z"/></svg>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="eu-post-share-btn eu-post-share-fb"
                       aria-label="<?php esc_attr_e( 'Share on Facebook', 'elegant-umrah' ); ?>">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <button class="eu-post-share-btn eu-post-share-copy"
                            id="eu-copy-link"
                            aria-label="<?php esc_attr_e( 'Copy link', 'elegant-umrah' ); ?>"
                            data-url="<?php echo esc_attr( get_permalink() ); ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    </button>
                </div>

                <!-- Prev / Next navigation -->
                <div class="eu-post-nav-links">
                    <?php if ( $prev_post ) : ?>
                        <a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>"
                           class="eu-post-nav-link eu-post-nav-prev">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="width:14px;height:14px"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                            <span><?php echo esc_html( wp_trim_words( get_the_title( $prev_post->ID ), 5 ) ); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if ( $next_post ) : ?>
                        <a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>"
                           class="eu-post-nav-link eu-post-nav-next">
                            <span><?php echo esc_html( wp_trim_words( get_the_title( $next_post->ID ), 5 ) ); ?></span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="width:14px;height:14px"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ============================================================
             COMMENTS
             ============================================================ -->
        <?php if ( comments_open() || get_comments_number() ) : ?>
            <div class="eu-comments-area" style="margin-top:3rem">
                <?php comments_template(); ?>
            </div>
        <?php endif; ?>

    </div>
    <!-- /eu-single-post -->

<?php endwhile; ?>

    <!-- ============================================================
         RELATED ARTICLES
         ============================================================ -->
    <div class="eu-related-articles-wrapper alignfull" style="background-color:#F8F8F8;padding:clamp(3rem,7vw,5rem) 1.5rem">
        <?php eu_render_related_articles( $post_id ); ?>
    </div>

    <!-- ============================================================
         BACK TO ARTICLES
         ============================================================ -->
    <?php
    $posts_page_id  = (int) get_option( 'page_for_posts' );
    $articles_url   = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/articles/' );
    ?>
    <div class="eu-back-to-articles alignfull" style="background-color:#0B0B0B;padding:2rem 1.5rem;text-align:center">
        <a href="<?php echo esc_url( $articles_url ); ?>" class="eu-btn-ghost-gold"
           style="display:inline-flex;align-items:center;gap:0.5rem;color:#D4AF37;border:1px solid rgba(212,175,55,0.4);padding:0.75rem 2rem;border-radius:4px;font-weight:600;font-size:0.9rem;text-decoration:none;transition:all 0.25s ease">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="width:16px;height:16px"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            <?php esc_html_e( 'Back to All Articles', 'elegant-umrah' ); ?>
        </a>
    </div>

</main>

<?php echo $eu_footer_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<?php wp_footer(); ?>
</body>
</html>
