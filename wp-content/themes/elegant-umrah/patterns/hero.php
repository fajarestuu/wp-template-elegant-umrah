<?php
/**
 * Title: Hero Section
 * Slug: elegant-umrah/hero-section
 * Categories: elegant-umrah
 * Keywords: hero, banner, umrah, landing
 * Description: Full-screen hero with headline, subtitle, CTA buttons, and search form.
 */
?>
<!-- wp:group {"align":"full","className":"eu-hero","style":{"dimensions":{"minHeight":"100svh"},"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"primary","gradient":"hero-overlay","layout":{"type":"flex","orientation":"vertical","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group alignfull eu-hero has-primary-background-color has-hero-overlay-gradient-background has-background" style="min-height:100svh;padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-top:var(--wp--preset--spacing--50)">

	<!-- wp:group {"className":"eu-hero-inner","layout":{"type":"constrained","contentSize":"920px"}} -->
	<div class="wp-block-group eu-hero-inner">
		<!-- wp:paragraph {"align":"center","textColor":"secondary","style":{"typography":{"fontWeight":"700","textTransform":"uppercase","letterSpacing":"0.15em"},"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}},"fontSize":"small"} -->
		<p class="has-text-align-center has-secondary-color has-text-color has-small-font-size" style="margin-bottom:var(--wp--preset--spacing--20);font-weight:700;letter-spacing:0.15em;text-transform:uppercase"><?php esc_html_e( '🕋 Trusted Umrah & Hajj Travel Agency 🕋', 'elegant-umrah' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"textAlign":"center","level":1,"textColor":"white","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
		<h1 class="wp-block-heading has-text-align-center has-white-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--30)"><?php esc_html_e( 'Your Sacred Journey', 'elegant-umrah' ); ?> <span class="has-secondary-color has-text-color" style="display:block"><?php esc_html_e( 'Starts Here', 'elegant-umrah' ); ?></span></h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"align":"center","textColor":"base","style":{"typography":{"lineHeight":"1.6"},"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}},"fontSize":"large"} -->
		<p class="has-text-align-center has-base-color has-text-color has-large-font-size" style="margin-bottom:var(--wp--preset--spacing--40);line-height:1.6"><?php esc_html_e( 'We make your pilgrimage comfortable, spiritually enriching, and memorable. Licensed, trusted, and dedicated to serving you.', 'elegant-umrah' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
		<div class="wp-block-buttons">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/packages/' ) ); ?>"><?php esc_html_e( '📦 Book Umrah Now', 'elegant-umrah' ); ?></a></div>
			<!-- /wp:button -->
			<!-- wp:button {"className":"is-style-outline"} -->
			<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/packages/' ) ); ?>"><?php esc_html_e( 'View Packages', 'elegant-umrah' ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->

		<!-- wp:elegant-umrah/hero-search /-->
	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->
