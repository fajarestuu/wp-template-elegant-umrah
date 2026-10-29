<?php
/**
 * Title: Articles Grid
 * Slug: elegant-umrah/articles-grid
 * Categories: elegant-umrah
 * Description: A 3-column grid of recent articles with category badges, excerpts, and reading time. Insert on any page to show the latest posts.
 * Keywords: articles, blog, news, grid, posts
 * Viewport Width: 1200
 */

$eu_articles_query = new WP_Query( [
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 3,
	'orderby'        => 'date',
	'order'          => 'DESC',
] );
?>
<!-- wp:group {"align":"full","className":"eu-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base"} -->
<div class="wp-block-group alignfull eu-section has-base-background-color has-background" style="padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-top:var(--wp--preset--spacing--70)">
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"640px"},"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-group" style="margin-bottom:var(--wp--preset--spacing--50)">
		<!-- wp:paragraph {"align":"center","textColor":"secondary","style":{"typography":{"fontWeight":"600","textTransform":"uppercase","letterSpacing":"0.2em"}},"fontSize":"xs"} -->
		<p class="has-text-align-center has-secondary-color has-text-color has-xs-font-size" style="font-weight:600;letter-spacing:0.2em;text-transform:uppercase"><?php esc_html_e( 'Latest from the Blog', 'elegant-umrah' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"textAlign":"center","textColor":"primary","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
		<h2 class="wp-block-heading has-text-align-center has-primary-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--20)"><?php esc_html_e( 'News & Articles', 'elegant-umrah' ); ?></h2>
		<!-- /wp:heading -->
		<!-- wp:separator {"className":"is-style-wide"} -->
		<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>
		<!-- /wp:separator -->
		<!-- wp:paragraph {"align":"center","textColor":"muted"} -->
		<p class="has-text-align-center has-muted-color has-text-color"><?php esc_html_e( 'Insights, tips, and spiritual guidance for your Hajj and Umrah journey.', 'elegant-umrah' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:html -->
	<div class="eu-articles-container" style="max-width:1200px;margin:0 auto;">
		<div class="eu-article-grid">
			<?php
			if ( $eu_articles_query->have_posts() ) {
				while ( $eu_articles_query->have_posts() ) {
					$eu_articles_query->the_post();
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
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
									<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd M Y' ) ); ?></time>
								</span>
							</div>

							<a href="<?php the_permalink(); ?>" class="eu-article-read-link">
								<?php esc_html_e( 'Read Article', 'elegant-umrah' ); ?>
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
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
	<!-- /wp:html -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/articles/' ) ); ?>"><?php esc_html_e( 'View All Articles →', 'elegant-umrah' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
