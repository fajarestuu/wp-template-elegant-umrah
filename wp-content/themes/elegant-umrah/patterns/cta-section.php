<?php
/**
 * Title: CTA Section
 * Slug: elegant-umrah/cta-section
 * Categories: elegant-umrah, call-to-action
 * Keywords: cta, call to action, book, contact
 * Description: Dark call-to-action section with headline and buttons.
 */
?>
<!-- wp:group {"align":"full","className":"eu-cta-section eu-section-dark","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"primary"} -->
<div class="wp-block-group alignfull eu-cta-section eu-section-dark has-primary-background-color has-background" style="padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-top:var(--wp--preset--spacing--70)">
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"680px"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"align":"center","textColor":"secondary","style":{"typography":{"fontWeight":"600","textTransform":"uppercase","letterSpacing":"0.2em"}},"fontSize":"xs"} -->
		<p class="has-text-align-center has-secondary-color has-text-color has-xs-font-size" style="font-weight:600;letter-spacing:0.2em;text-transform:uppercase"><?php esc_html_e( 'Limited Seats Available', 'elegant-umrah' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"textAlign":"center","textColor":"white","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
		<h2 class="wp-block-heading has-text-align-center has-white-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--30)"><?php esc_html_e( 'Begin Your Sacred Journey Today', 'elegant-umrah' ); ?></h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"align":"center","textColor":"base","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} -->
		<p class="has-text-align-center has-base-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--40)"><?php esc_html_e( 'Seats fill up quickly. Book your Umrah or Hajj package now and secure your spot on this life-changing pilgrimage to the Holy Land.', 'elegant-umrah' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
		<div class="wp-block-buttons">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/packages/' ) ); ?>"><?php esc_html_e( '📦 Book Now', 'elegant-umrah' ); ?></a></div>
			<!-- /wp:button -->
			<!-- wp:button {"className":"is-style-outline"} -->
			<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( eu_get_whatsapp_url( __( 'Assalamualaikum, saya ingin info pemesanan paket umrah.', 'elegant-umrah' ) ) ); ?>"><?php esc_html_e( 'Contact Us', 'elegant-umrah' ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
