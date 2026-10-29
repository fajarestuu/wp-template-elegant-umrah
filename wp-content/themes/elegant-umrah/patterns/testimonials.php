<?php
/**
 * Title: Testimonials Section
 * Slug: elegant-umrah/testimonials
 * Categories: elegant-umrah
 * Keywords: testimonials, reviews, pilgrims
 * Description: Dark testimonials section with 3 pilgrim reviews.
 */
?>
<!-- wp:group {"align":"full","className":"eu-section eu-section-dark","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"primary"} -->
<div class="wp-block-group alignfull eu-section eu-section-dark has-primary-background-color has-background" style="padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-top:var(--wp--preset--spacing--70)">
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"680px"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"align":"center","textColor":"secondary","style":{"typography":{"fontWeight":"600","textTransform":"uppercase","letterSpacing":"0.2em"}},"fontSize":"xs"} -->
		<p class="has-text-align-center has-secondary-color has-text-color has-xs-font-size" style="font-weight:600;letter-spacing:0.2em;text-transform:uppercase"><?php esc_html_e( 'Testimonials', 'elegant-umrah' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"textAlign":"center","textColor":"white","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} -->
		<h2 class="wp-block-heading has-text-align-center has-white-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--50)"><?php esc_html_e( 'What Our Pilgrims Say', 'elegant-umrah' ); ?></h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<!-- wp:html -->
	<div class="eu-testimonials-wrapper" style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:var(--wp--preset--spacing--30);">
		<div class="eu-testimonial-card">
			<div class="eu-testimonial-quote">“</div>
			<p class="eu-testimonial-text"><?php esc_html_e( 'Alhamdulillah, the most beautiful experience of my life. The team was incredibly professional and caring. I will definitely return for Hajj inshallah!', 'elegant-umrah' ); ?></p>
			<div class="eu-testimonial-stars">★★★★★</div>
			<div class="eu-testimonial-author">
				<div class="eu-testimonial-avatar">AF</div>
				<div class="eu-testimonial-info">
					<div class="name">Bapak Ahmad Fauzi</div>
					<div class="origin">Jakarta · Umrah 2024</div>
				</div>
			</div>
		</div>

		<div class="eu-testimonial-card">
			<div class="eu-testimonial-quote">“</div>
			<p class="eu-testimonial-text"><?php esc_html_e( 'We went as a family of 7 including elderly parents. The team arranged everything perfectly. Such a heartwarming and flawless journey. Jazakallah khairan!', 'elegant-umrah' ); ?></p>
			<div class="eu-testimonial-stars">★★★★★</div>
			<div class="eu-testimonial-author">
				<div class="eu-testimonial-avatar">SR</div>
				<div class="eu-testimonial-info">
					<div class="name">Ibu Siti Rahayu</div>
					<div class="origin">Surabaya · Hajj 2024</div>
				</div>
			</div>
		</div>

		<div class="eu-testimonial-card">
			<div class="eu-testimonial-quote">“</div>
			<p class="eu-testimonial-text"><?php esc_html_e( 'Third time with Elegant Umrah. The VIP package was absolutely worth every dirham. Private guide, direct flights, and the most spiritual experience I have ever had!', 'elegant-umrah' ); ?></p>
			<div class="eu-testimonial-stars">★★★★★</div>
			<div class="eu-testimonial-author">
				<div class="eu-testimonial-avatar">HW</div>
				<div class="eu-testimonial-info">
					<div class="name">Dr. Hendra Wijaya</div>
					<div class="origin">Bandung · VIP Umrah 2025</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
