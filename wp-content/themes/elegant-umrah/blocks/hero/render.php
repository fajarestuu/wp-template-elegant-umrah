<?php
/**
 * Hero Block - Server-Side Render
 *
 * @package ElegantUmrah
 * @param   array  $attributes  Block attributes
 * @param   string $content     Inner block content
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$title           = $attributes['title']          ?? 'Your Journey to the Holy Land Begins Here';
$highlight       = $attributes['highlight']      ?? '';
$subtitle        = $attributes['subtitle']       ?? '';
$badge           = $attributes['badge']          ?? '';
$cta_primary     = $attributes['ctaPrimary']     ?? 'Book Umrah Now';
$cta_primary_url = $attributes['ctaPrimaryUrl']  ?? '/packages';
$cta_secondary   = $attributes['ctaSecondary']   ?? 'View Packages';
$cta_sec_url     = $attributes['ctaSecondaryUrl'] ?? '/packages';
$bg_id           = absint( $attributes['backgroundId'] ?? 0 );
$bg_url          = $attributes['backgroundUrl']  ?? '';
$overlay         = min( 1, max( 0, (float) ( $attributes['overlayOpacity'] ?? 0.75 ) ) );
$min_height      = esc_attr( $attributes['minHeight'] ?? '100svh' );

// Get background image URL
if ( $bg_id && ! $bg_url ) {
    $bg_src = wp_get_attachment_image_src( $bg_id, 'eu-hero' );
    $bg_url = $bg_src ? $bg_src[0] : '';
}

$bg_style = $bg_url
    ? 'background-image:url(' . esc_url( $bg_url ) . ');background-size:cover;background-position:center;background-repeat:no-repeat;'
    : 'background:linear-gradient(135deg,#0B0B0B 0%,#1a1a1a 100%);';

// Overlay style
$overlay_style = 'position:absolute;inset:0;background:rgba(11,11,11,' . $overlay . ');z-index:1;';

// Build title HTML
$title_html = esc_html( $title );
if ( $highlight ) {
    $title_html = str_replace(
        esc_html( $highlight ),
        '<span class="highlight" style="color:#D4AF37;display:block">' . esc_html( $highlight ) . '</span>',
        $title_html
    );
}

// Wrapper attributes
$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'eu-hero',
    'style' => 'min-height:' . $min_height . ';position:relative;display:flex;align-items:center;justify-content:center;overflow:hidden;background-color:#0B0B0B;',
] );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

    <!-- Background -->
    <?php if ( $bg_url ) : ?>
    <div class="eu-hero-bg" style="<?php echo esc_attr( $bg_style ); ?>position:absolute;inset:0;" aria-hidden="true"></div>
    <?php endif; ?>

    <!-- Overlay -->
    <div class="eu-hero-overlay" style="<?php echo esc_attr( $overlay_style ); ?>" aria-hidden="true"></div>

    <!-- Content -->
    <div class="eu-hero-content" style="position:relative;z-index:2;text-align:center;max-width:800px;padding:2rem 1.5rem;width:100%;animation:heroFadeIn 1s ease forwards;">

        <?php if ( $badge ) : ?>
        <div class="eu-hero-badge" style="display:inline-block;padding:0.35rem 1rem;border:1px solid #D4AF37;border-radius:30px;color:#D4AF37;font-size:0.75rem;font-weight:600;letter-spacing:0.15em;text-transform:uppercase;margin-bottom:1.5rem;">
            <?php echo esc_html( $badge ); ?>
        </div>
        <?php endif; ?>

        <h1 class="eu-hero-heading" style="color:#FFFFFF;font-size:clamp(2rem,6vw,4rem);font-weight:800;line-height:1.15;margin-bottom:1.25rem;text-shadow:0 2px 20px rgba(0,0,0,0.5);">
            <?php echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </h1>

        <?php if ( $subtitle ) : ?>
        <p class="eu-hero-subtitle" style="color:rgba(255,255,255,0.8);font-size:clamp(0.95rem,2.5vw,1.15rem);max-width:580px;margin:0 auto 2.5rem;line-height:1.75;">
            <?php echo esc_html( $subtitle ); ?>
        </p>
        <?php endif; ?>

        <div class="eu-hero-actions" style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:center;align-items:center;">
            <a href="<?php echo esc_url( $cta_primary_url ); ?>" class="eu-btn eu-btn-cta">
                <?php echo esc_html( $cta_primary ); ?>
            </a>
            <?php if ( $cta_secondary ) : ?>
            <a href="<?php echo esc_url( $cta_sec_url ); ?>" class="eu-btn" style="background:transparent;color:#D4AF37;border:2px solid rgba(212,175,55,0.5);padding:0.85rem 2rem;border-radius:4px;font-weight:600;font-size:0.9rem;letter-spacing:0.05em;text-transform:uppercase;transition:all 0.3s ease;">
                <?php echo esc_html( $cta_secondary ); ?>
            </a>
            <?php endif; ?>
        </div>

    </div>

    <!-- Scroll indicator -->
    <div class="eu-hero-scroll" aria-hidden="true" style="position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:0.5rem;color:rgba(255,255,255,0.5);font-size:0.7rem;letter-spacing:0.1em;text-transform:uppercase;animation:bounce 2s infinite;">
        <svg viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="2" width="20" height="20"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
        <span>Scroll</span>
    </div>

</section>
