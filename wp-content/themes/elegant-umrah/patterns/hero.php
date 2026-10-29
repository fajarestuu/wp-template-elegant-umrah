<?php
/**
 * Title: Hero Section
 * Slug: elegant-umrah/hero-section
 * Categories: elegant-umrah
 * Keywords: hero, banner, umrah, landing
 * Description: Full-screen hero with background, headline, subtitle, and CTA buttons.
 */

$hero_config = function_exists( 'eu_get_hero_config' ) ? eu_get_hero_config() : [ 'images' => [], 'overlay_opacity' => 0.70, 'interval' => 5 ];
$hero_images = $hero_config['images'];
$has_images  = ! empty( $hero_images );
$is_slideshow = count( $hero_images ) > 1;
?>
<!-- wp:group {"align":"full","className":"eu-hero","style":{"dimensions":{"minHeight":"100svh"},"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"primary","gradient":"hero-overlay","layout":{"type":"flex","orientation":"vertical","justifyContent":"center","verticalAlignment":"center"}} -->
<div class="wp-block-group alignfull eu-hero has-primary-background-color has-hero-overlay-gradient-background has-background" style="min-height:100svh;padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-top:var(--wp--preset--spacing--50);position:relative;overflow:hidden">
	<?php if ( $has_images ) : ?>
		<!-- Hero Media Layer (Single Image or Slideshow) -->
		<div class="eu-hero-media" data-interval="<?php echo esc_attr( (string) $hero_config['interval'] ); ?>" aria-hidden="true">
			<div class="eu-hero-slides">
				<?php foreach ( $hero_images as $index => $img_url ) : ?>
					<div class="eu-hero-slide<?php echo 0 === $index ? ' is-active' : ''; ?>" style="background-image: url('<?php echo esc_url( $img_url ); ?>')" data-index="<?php echo esc_attr( (string) $index ); ?>"></div>
				<?php endforeach; ?>
			</div>
			<div class="eu-hero-overlay-layer" style="background: rgba(11, 11, 11, <?php echo esc_attr( (string) $hero_config['overlay_opacity'] ); ?>)"></div>
			<?php if ( $is_slideshow ) : ?>
				<div class="eu-hero-dots" role="tablist" aria-label="<?php esc_attr_e( 'Hero Slideshow Pagination', 'elegant-umrah' ); ?>">
					<?php foreach ( $hero_images as $index => $img_url ) : ?>
						<button type="button" class="eu-hero-dot<?php echo 0 === $index ? ' is-active' : ''; ?>" data-slide-index="<?php echo esc_attr( (string) $index ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'elegant-umrah' ), $index + 1 ) ); ?>"></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<!-- wp:group {"layout":{"type":"constrained","contentSize":"920px"}} -->
	<div class="wp-block-group" style="position:relative;z-index:3">
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
		<div class="wp-block-buttons" style="margin-bottom:var(--wp--preset--spacing--40)">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/packages/' ) ); ?>"><?php esc_html_e( '📦 Book Umrah Now', 'elegant-umrah' ); ?></a></div>
			<!-- /wp:button -->
			<!-- wp:button {"className":"is-style-outline"} -->
			<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/packages/' ) ); ?>"><?php esc_html_e( 'View Packages', 'elegant-umrah' ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->

		<!-- Search Bar Console -->
		<div class="eu-hero-search-wrapper">
			<div class="eu-hero-search-card">
				<form method="get" action="<?php echo esc_url( home_url( '/packages/' ) ); ?>" class="eu-hero-search-form">
					<!-- Departure Month Field -->
					<div class="eu-hero-search-field">
						<label for="eu-search-month" class="eu-hero-search-label">
							<svg class="eu-hero-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
							<span><?php esc_html_e( 'Bulan Keberangkatan', 'elegant-umrah' ); ?></span>
						</label>
						<div class="eu-hero-search-select-wrap">
							<select id="eu-search-month" name="month" class="eu-hero-search-select">
								<option value=""><?php esc_html_e( 'Semua Bulan Keberangkatan', 'elegant-umrah' ); ?></option>
								<?php
								$available_months = eu_get_available_departure_months();
								foreach ( $available_months as $m_val => $m_label ) :
								?>
									<option value="<?php echo esc_attr( $m_val ); ?>"><?php echo esc_html( $m_label ); ?></option>
								<?php endforeach; ?>
							</select>
							<span class="eu-hero-select-arrow" aria-hidden="true">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
							</span>
						</div>
					</div>

					<!-- Divider -->
					<div class="eu-hero-search-divider" aria-hidden="true"></div>

					<!-- Package Type Field -->
					<div class="eu-hero-search-field">
						<label for="eu-search-type" class="eu-hero-search-label">
							<svg class="eu-hero-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
							<span><?php esc_html_e( 'Tipe Paket', 'elegant-umrah' ); ?></span>
						</label>
						<div class="eu-hero-search-select-wrap">
							<select id="eu-search-type" name="type" class="eu-hero-search-select">
								<option value="all"><?php esc_html_e( 'Semua Tipe Paket', 'elegant-umrah' ); ?></option>
								<?php
								$package_types = eu_get_package_types();
								foreach ( $package_types as $t_slug => $t_name ) :
								?>
									<option value="<?php echo esc_attr( $t_slug ); ?>"><?php echo esc_html( $t_name ); ?></option>
								<?php endforeach; ?>
							</select>
							<span class="eu-hero-select-arrow" aria-hidden="true">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
							</span>
						</div>
					</div>

					<!-- Submit Button -->
					<div class="eu-hero-search-action">
						<button type="submit" class="eu-hero-search-submit">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
							<span><?php esc_html_e( 'Cari Paket', 'elegant-umrah' ); ?></span>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
