<?php
/**
 * Articles Grid Block - Server-Side Render
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$posts_per_page = isset( $attributes['postsPerPage'] ) ? max( 1, (int) $attributes['postsPerPage'] ) : 3;

$articles_query = new WP_Query( [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
    'orderby'        => 'date',
    'order'          => 'DESC',
] );
?>
<div class="eu-articles-container" style="max-width:1200px;margin:0 auto;">
    <div class="eu-article-grid">
        <?php
        if ( $articles_query->have_posts() ) {
            while ( $articles_query->have_posts() ) {
                $articles_query->the_post();
                $post_id = get_the_ID();
                ?>
                <article class="eu-article-card" id="post-<?php echo esc_attr( $post_id ); ?>">
                    <a href="<?php the_permalink(); ?>" class="eu-article-thumb-link" tabindex="-1" aria-hidden="true">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'large', [ 'class' => 'eu-article-thumb', 'alt' => get_the_title() ] ); ?>
                        <?php else : ?>
                            <div class="eu-article-thumb eu-article-thumb--placeholder" aria-hidden="true">
                                <span>🕌</span>
                            </div>
                        <?php endif; ?>
                    </a>

                    <div class="eu-article-body">
                        <div class="eu-article-cat">
                            <?php
                            $cats = get_the_terms( $post_id, 'islamic_category' );
                            if ( ! $cats || is_wp_error( $cats ) ) {
                                $cats = get_the_category( $post_id );
                            }
                            if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
                                $first_cat = reset( $cats );
                                echo '<span class="eu-article-cat-badge">' . esc_html( $first_cat->name ) . '</span>';
                            }
                            ?>
                        </div>

                        <h3 class="eu-article-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>

                        <p class="eu-article-excerpt">
                            <?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '...' ) ); ?>
                        </p>

                        <div class="eu-article-meta">
                            <span class="eu-article-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd M Y' ) ); ?></time>
                            </span>
                        </div>

                        <a href="<?php the_permalink(); ?>" class="eu-article-read-link">
                            <?php esc_html_e( 'Read Article', 'elegant-umrah' ); ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                    </div>
                </article>
                <?php
            }
            wp_reset_postdata();
        } else {
            ?>
            <div class="eu-no-results" style="grid-column:1/-1;text-align:center;padding:3rem;background:#fff;border-radius:12px;">
                <p style="font-size:2rem">📖</p>
                <h3><?php esc_html_e( 'No articles yet.', 'elegant-umrah' ); ?></h3>
            </div>
            <?php
        }
        ?>
    </div>
</div>
