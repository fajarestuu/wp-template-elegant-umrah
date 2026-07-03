<?php
/**
 * Package Card Block - Server-Side Render
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$title       = $attributes['title']         ?? 'Umrah Package';
$pkg_type    = $attributes['packageType']   ?? 'Umrah Package';
$price       = $attributes['price']         ?? '';
$duration    = $attributes['duration']      ?? '';
$departure   = $attributes['departureDate'] ?? '';
$hotel       = $attributes['hotel']         ?? '';
$airline     = $attributes['airline']       ?? '';
$is_featured = (bool) ( $attributes['isFeatured'] ?? false );
$booking_url = $attributes['bookingUrl']    ?? '/packages';
$img_id      = absint( $attributes['imageId'] ?? 0 );
$img_url     = $attributes['imageUrl']      ?? '';

if ( $img_id && ! $img_url ) {
    $img_src = wp_get_attachment_image_src( $img_id, 'eu-card' );
    $img_url = $img_src ? $img_src[0] : '';
}

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'eu-package-card' . ( $is_featured ? ' is-featured' : '' ),
] );
?>
<article <?php echo $wrapper_attributes; // phpcs:ignore ?>>

    <?php if ( $is_featured ) : ?>
        <span class="eu-package-badge"><?php esc_html_e( 'Best Seller', 'elegant-umrah' ); ?></span>
    <?php endif; ?>

    <?php if ( $img_url ) : ?>
        <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="eu-package-img" loading="lazy">
    <?php else : ?>
        <div class="eu-package-img-placeholder" aria-hidden="true">🕌</div>
    <?php endif; ?>

    <div class="eu-package-body">
        <div class="eu-package-type"><?php echo esc_html( $pkg_type ); ?></div>
        <h3 class="eu-package-name"><a href="<?php echo esc_url( $booking_url ); ?>"><?php echo esc_html( $title ); ?></a></h3>

        <div class="eu-package-meta">
            <?php if ( $duration ) : ?>
                <span class="eu-package-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <?php echo esc_html( $duration ); ?>
                </span>
            <?php endif; ?>
            <?php if ( $departure ) : ?>
                <span class="eu-package-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="2" width="14" height="14"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?php echo esc_html( $departure ); ?>
                </span>
            <?php endif; ?>
            <?php if ( $hotel ) : ?>
                <span class="eu-package-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="2" width="14" height="14"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <?php echo esc_html( $hotel ); ?>
                </span>
            <?php endif; ?>
            <?php if ( $airline ) : ?>
                <span class="eu-package-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="2" width="14" height="14"><path d="M17.8 19.2L16 11l3.5-3.5C21 6 21 4 19.5 2.5c-1.5-1.5-3.5-1.5-5 0L11 6 2.8 4.2L1 6l6 3L4 12l-2 1 3 3 3 3 1-2 3-3 3 6 2-1.8z"/></svg>
                    <?php echo esc_html( $airline ); ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="eu-package-divider"></div>

        <div class="eu-package-price-row">
            <?php if ( $price ) : ?>
                <div class="eu-package-price">
                    <span class="from"><?php esc_html_e( 'Starting from', 'elegant-umrah' ); ?></span>
                    <span class="amount"><?php echo esc_html( $price ); ?></span>
                    <span class="per"><?php esc_html_e( '/ person', 'elegant-umrah' ); ?></span>
                </div>
            <?php endif; ?>
            <a href="<?php echo esc_url( $booking_url ); ?>" class="eu-package-card-btn">
                <?php esc_html_e( 'Book Now', 'elegant-umrah' ); ?>
            </a>
        </div>
    </div>

</article>
