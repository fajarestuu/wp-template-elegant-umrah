<?php
/**
 * Title: Packages Grid Section
 * Slug: elegant-umrah/packages-grid
 * Categories: elegant-umrah
 * Keywords: packages, grid, section, umrah
 * Description: Full packages section with dynamic packages query and 3-column card grid.
 */

$eu_packages_query = new WP_Query( [
	'post_type'      => 'umrah_package',
	'post_status'    => 'publish',
	'posts_per_page' => 3,
	'orderby'        => 'date',
	'order'          => 'DESC',
] );
?>
<!-- wp:group {"align":"full","className":"eu-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base"} -->
<div class="wp-block-group alignfull eu-section has-base-background-color has-background" style="padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-top:var(--wp--preset--spacing--70)">
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"680px"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"align":"center","textColor":"secondary","style":{"typography":{"fontWeight":"600","textTransform":"uppercase","letterSpacing":"0.2em"}},"fontSize":"xs"} -->
		<p class="has-text-align-center has-secondary-color has-text-color has-xs-font-size" style="font-weight:600;letter-spacing:0.2em;text-transform:uppercase"><?php esc_html_e( 'Our Packages', 'elegant-umrah' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"textAlign":"center","textColor":"primary"} -->
		<h2 class="wp-block-heading has-text-align-center has-primary-color has-text-color"><?php esc_html_e( 'Premium Umrah & Hajj Packages', 'elegant-umrah' ); ?></h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<!-- wp:html -->
	<div class="eu-packages-grid" style="max-width:1200px;margin:var(--wp--preset--spacing--50) auto 0;display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:var(--wp--preset--spacing--30);">
		<?php
		if ( $eu_packages_query->have_posts() ) {
			while ( $eu_packages_query->have_posts() ) {
				$eu_packages_query->the_post();
				if ( function_exists( 'eu_render_package_card' ) ) {
					eu_render_package_card( get_the_ID() );
				}
			}
			wp_reset_postdata();
		} else {
			?>
			<div class="eu-no-results" style="grid-column:1/-1;text-align:center;padding:3rem;background:#fff;border-radius:12px;">
				<p style="font-size:2rem">🕋</p>
				<h3><?php esc_html_e( 'No packages available at the moment.', 'elegant-umrah' ); ?></h3>
			</div>
			<?php
		}
		?>
	</div>
	<!-- /wp:html -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/packages/' ) ); ?>"><?php esc_html_e( 'View All Packages →', 'elegant-umrah' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
