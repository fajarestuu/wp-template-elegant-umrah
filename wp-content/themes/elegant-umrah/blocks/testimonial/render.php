<?php
/**
 * Testimonial Block - Server-Side Render
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$quote       = $attributes['quote']       ?? '';
$author_name = $attributes['authorName']  ?? '';
$origin      = $attributes['authorOrigin'] ?? '';
$pkg_type    = $attributes['packageType'] ?? '';
$rating      = min( 5, max( 0, (int) ( $attributes['rating'] ?? 5 ) ) );

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'eu-testimonial-card',
] );

// Build stars
$stars = str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating );
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore ?>>

    <div class="eu-testimonial-quote" aria-hidden="true">"</div>

    <div class="eu-testimonial-stars" aria-label="<?php echo esc_attr( $rating ) . ' out of 5 stars'; ?>">
        <?php echo esc_html( $stars ); ?>
    </div>

    <?php if ( $quote ) : ?>
    <p class="eu-testimonial-text"><?php echo esc_html( $quote ); ?></p>
    <?php endif; ?>

    <div class="eu-testimonial-author">
        <div class="eu-testimonial-avatar" aria-hidden="true">
            <?php echo esc_html( mb_strtoupper( mb_substr( $author_name, 0, 1 ) ) ); ?>
        </div>
        <div class="eu-testimonial-info">
            <p class="name"><?php echo esc_html( $author_name ); ?></p>
            <?php if ( $origin || $pkg_type ) : ?>
            <p class="origin">
                <?php echo esc_html( implode( ' · ', array_filter( [ $origin, $pkg_type ] ) ) ); ?>
            </p>
            <?php endif; ?>
        </div>
    </div>

</div>
