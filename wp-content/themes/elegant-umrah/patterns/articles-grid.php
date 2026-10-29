<?php
/**
 * Title: Articles Grid
 * Slug: elegant-umrah/articles-grid
 * Categories: elegant-umrah
 * Description: A 3-column grid of recent articles with category badges, excerpts, and reading time. Insert on any page to show the latest posts.
 * Keywords: articles, blog, news, grid, posts
 * Viewport Width: 1200
 */
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

	<!-- wp:elegant-umrah/articles-grid {"postsPerPage":3} /-->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/articles/' ) ); ?>"><?php esc_html_e( 'View All Articles →', 'elegant-umrah' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
