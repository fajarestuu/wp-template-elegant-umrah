<?php
/**
 * Title: Package Card (Single)
 * Slug: elegant-umrah/package-card
 * Categories: elegant-umrah
 * Keywords: package, card, umrah, hajj
 * Description: Single package card item.
 */
?>
<!-- wp:group {"className":"eu-package-card","borderColor":"base","style":{"border":{"radius":"12px","width":"1px","style":"solid"}},"backgroundColor":"white"} -->
<div class="wp-block-group eu-package-card has-base-border-color has-border-color has-white-background-color has-background" style="border-radius:12px;border-style:solid;border-width:1px">
	<!-- wp:group {"style":{"color":{"background":"#1a1a1a"},"dimensions":{"minHeight":"200px"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-group" style="background-color:#1a1a1a;min-height:200px">
		<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"2.5rem"}}} -->
		<p class="has-text-align-center" style="font-size:2.5rem">🕋</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
	<div class="wp-block-group" style="padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-top:var(--wp--preset--spacing--30)">
		<!-- wp:paragraph {"textColor":"secondary","style":{"typography":{"fontWeight":"600","textTransform":"uppercase","letterSpacing":"0.15em"},"spacing":{"margin":{"bottom":"var:preset|spacing|10"}}},"fontSize":"xs"} -->
		<p class="has-secondary-color has-text-color has-xs-font-size" style="margin-bottom:var(--wp--preset--spacing--10);font-weight:600;letter-spacing:0.15em;text-transform:uppercase"><?php esc_html_e( 'Umrah Package', 'elegant-umrah' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":3,"textColor":"primary","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
		<h3 class="wp-block-heading has-primary-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--20)"><?php esc_html_e( 'Umrah Premium 12 Days', 'elegant-umrah' ); ?></h3>
		<!-- /wp:heading -->
		<!-- wp:group {"style":{"spacing":{"blockGap":"0.25rem","margin":{"bottom":"var:preset|spacing|20"}}},"textColor":"muted","fontSize":"small"} -->
		<div class="wp-block-group has-muted-color has-text-color has-small-font-size" style="margin-bottom:var(--wp--preset--spacing--20)">
			<!-- wp:paragraph -->
			<p>⏳ <?php esc_html_e( '12 Days / 11 Nights', 'elegant-umrah' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p>📅 <?php esc_html_e( '15 March 2025', 'elegant-umrah' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p>🏨 <?php esc_html_e( 'Grand Makkah Hotel ⭐⭐⭐⭐⭐', 'elegant-umrah' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p>✈️ <?php esc_html_e( 'Garuda Indonesia', 'elegant-umrah' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:separator {"className":"is-style-wide"} -->
		<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>
		<!-- /wp:separator -->
		<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
		<div class="wp-block-group">
			<!-- wp:group {"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.05em"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"xs"} -->
				<p class="has-muted-color has-text-color has-xs-font-size" style="margin-bottom:0;margin-top:0;text-transform:uppercase;letter-spacing:0.05em"><?php esc_html_e( 'Starting from', 'elegant-umrah' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"textColor":"secondary","style":{"typography":{"lineHeight":"1","fontWeight":"800"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"xl"} -->
				<p class="has-secondary-color has-text-color has-xl-font-size" style="margin-bottom:0;margin-top:0;font-weight:800;line-height:1"><?php esc_html_e( 'USD 1,500', 'elegant-umrah' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"textColor":"muted","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"xs"} -->
				<p class="has-muted-color has-text-color has-xs-font-size" style="margin-bottom:0;margin-top:0"><?php esc_html_e( '/ person', 'elegant-umrah' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/packages"><?php esc_html_e( 'Book Now', 'elegant-umrah' ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
