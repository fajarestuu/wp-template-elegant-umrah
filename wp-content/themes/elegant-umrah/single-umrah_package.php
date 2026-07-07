<?php
/**
 * Single Umrah Package Template
 * Two-column layout: sticky left sidebar + scrollable right content
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Output the full HTML document structure for an FSE block theme
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Header template part -->
<?php block_template_part( 'header' ); ?>

<?php

while ( have_posts() ) :
    the_post();

    $post_id      = get_the_ID();
    $title        = get_the_title();
    $currency       = get_post_meta( $post_id, 'eu_currency', true )             ?: 'Rp';
    $price          = get_post_meta( $post_id, 'eu_price', true )                ?: '';
    $discount_price = get_post_meta( $post_id, 'eu_discount_price', true )       ?: '';
    $duration     = get_post_meta( $post_id, 'eu_duration', true )             ?: '';
    $departure    = get_post_meta( $post_id, 'eu_departure_date', true )       ?: '';
    $est_month    = get_post_meta( $post_id, 'eu_estimate_month', true )       ?: '';
    $muthawwif    = get_post_meta( $post_id, 'eu_muthawwif', true )            ?: '';
    $pkg_type     = get_post_meta( $post_id, 'eu_package_type', true )         ?: '';
    $airline      = get_post_meta( $post_id, 'eu_airline', true )              ?: '';
    $hotel_makkah = get_post_meta( $post_id, 'eu_hotel_makkah', true )         ?: '';
    $hotel_mk_rat = get_post_meta( $post_id, 'eu_hotel_makkah_rating', true )  ?: '';
    $hotel_madinah= get_post_meta( $post_id, 'eu_hotel_madinah', true )        ?: '';
    $hotel_md_rat = get_post_meta( $post_id, 'eu_hotel_madinah_rating', true ) ?: '';
    $inclusions   = get_post_meta( $post_id, 'eu_inclusions', true )           ?: '';
    $exclusions   = get_post_meta( $post_id, 'eu_exclusions', true )           ?: '';
    $add_tour     = get_post_meta( $post_id, 'eu_additional_tour', true )      ?: '';
    $add_country  = get_post_meta( $post_id, 'eu_additional_country', true )   ?: '';
    $seats        = (int) get_post_meta( $post_id, 'eu_seats_available', true );
    $is_featured  = (bool) get_post_meta( $post_id, 'eu_is_featured', true );
    $is_sold_out  = (bool) get_post_meta( $post_id, 'eu_is_sold_out', true );

    // Package type label
    $type_labels = [
        'umrah'      => 'Umrah',
        'hajj'       => 'Hajj',
        'umrah_plus' => 'Umrah Plus',
        'family'     => 'Family Package',
        'vip'        => 'VIP Premium',
        'honeymoon'  => 'Honeymoon Umrah',
    ];
    $type_label = $type_labels[ $pkg_type ] ?? ucfirst( str_replace( '_', ' ', $pkg_type ) );

    // Stars helper
    $stars = function( int $n ): string {
        return str_repeat( '★', $n ) . str_repeat( '☆', max( 0, 5 - $n ) );
    };

    // Departure formatted
    if ( $departure ) {
        $departure_fmt   = date_i18n( 'j F Y', strtotime( $departure ) );
        $departure_label = 'Departure Date';
    } elseif ( $est_month ) {
        $departure_fmt   = date_i18n( 'F Y', strtotime( $est_month . '-01' ) );
        $departure_label = 'Departure Month';
    } else {
        $departure_fmt   = '';
        $departure_label = 'Departure';
    }

    // Currency formatting helper
    // Strips any existing non-numeric chars, applies locale-aware thousands separator
    $format_price = function( string $raw, string $curr ): string {
        // Remove everything except digits and dots/commas
        $numeric = preg_replace( '/[^\d.,]/', '', $raw );
        // Normalise: treat commas as thousands sep (Indonesian style: 22.500.000 or 22,500,000)
        // Remove dots then commas used as thousands seps; keep the last dot if it's a decimal
        $numeric = str_replace( ',', '.', $numeric );
        // If there are multiple dots, keep only the last as decimal; remove the rest
        $parts = explode( '.', $numeric );
        if ( count( $parts ) > 2 ) {
            // Multiple dots → all are thousands seps, no decimal
            $numeric = implode( '', $parts );
        } else {
            $numeric = $numeric;
        }
        $number = (float) $numeric;

        if ( $curr === 'USD' ) {
            return 'USD ' . number_format( $number, 2, '.', ',' );
        }
        // Default: Rp (Indonesian Rupiah — no decimals, dot thousands sep)
        return 'Rp ' . number_format( $number, 0, ',', '.' );
    };

    $price_fmt    = $price          ? $format_price( $price, $currency )          : '';
    $discount_fmt = $discount_price ? $format_price( $discount_price, $currency ) : '';

    // Featured image (poster) — full size, no crop
    $poster_url = get_the_post_thumbnail_url( $post_id, 'full' );

    // Book URL
    $book_url = add_query_arg( 'package', $post_id, home_url( '/contact/' ) );

    // WA link
    $wa_msg  = rawurlencode( 'Assalamualaikum, saya ingin info paket: ' . $title );
    $wa_link = 'https://wa.me/621234567890?text=' . $wa_msg;
?>

<main class="eu-pkg-single" id="eu-pkg-single">

    <!-- ============================================================
         LEFT SIDEBAR — sticky on tablet+
         ============================================================ -->
    <aside class="eu-pkg-sidebar" aria-label="Package sidebar">

        <!-- Logo -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="eu-pkg-sidebar-logo">
            <div class="eu-pkg-sidebar-logo-icon" aria-hidden="true">🕌</div>
            <div class="eu-pkg-sidebar-logo-text">
                <span class="eu-pkg-sidebar-logo-primary"><?php bloginfo( 'name' ); ?></span>
                <span class="eu-pkg-sidebar-logo-secondary"><?php eu_te( 'Travel & Pilgrimage', [ 'id' => 'Perjalanan & Ziarah' ] ); ?></span>
            </div>
        </a>

        <!-- Promotion Poster -->
        <div class="eu-pkg-sidebar-poster">
            <?php if ( $poster_url ) : ?>
                <img src="<?php echo esc_url( $poster_url ); ?>"
                     alt="<?php echo esc_attr( $title ); ?>"
                     class="eu-pkg-sidebar-poster-img"
                     loading="eager">
            <?php else : ?>
                <div class="eu-pkg-sidebar-poster-placeholder" aria-hidden="true">
                    <span>🕋</span>
                    <p>Umrah &amp; Hajj<br>Premium Package</p>
                </div>
            <?php endif; ?>
            <?php if ( $is_sold_out ) : ?>
                <span class="eu-pkg-sidebar-badge eu-pkg-sidebar-badge--sold"><?php eu_te( 'Sold Out', [ 'id' => 'Habis Terjual' ] ); ?></span>
            <?php elseif ( $is_featured ) : ?>
                <span class="eu-pkg-sidebar-badge"><?php eu_te( 'Best Seller', [ 'id' => 'Terlaris' ] ); ?></span>
            <?php endif; ?>
        </div>

        <!-- Price teaser -->
        <?php if ( $price_fmt || $discount_fmt ) : ?>
        <div class="eu-pkg-sidebar-price">
            <span class="eu-pkg-sidebar-price-label">
                <?php echo $discount_fmt ? esc_html__( 'Special Price', 'elegant-umrah' ) : esc_html__( 'Starting from', 'elegant-umrah' ); ?>
            </span>
            <?php if ( $discount_fmt ) : ?>
                <span class="eu-pkg-sidebar-price-amount eu-pkg-sidebar-price-amount--discount">
                    <?php echo esc_html( $discount_fmt ); ?>
                </span>
                <span class="eu-pkg-sidebar-price-original">
                    <?php echo esc_html( $price_fmt ); ?>
                </span>
            <?php else : ?>
                <span class="eu-pkg-sidebar-price-amount">
                    <?php echo esc_html( $price_fmt ); ?>
                </span>
            <?php endif; ?>
            <span class="eu-pkg-sidebar-price-per"><?php eu_te( '/ person', [ 'id' => '/ orang' ] ); ?></span>
        </div>
        <?php endif; ?>

        <!-- CTA buttons -->
        <?php if ( $is_sold_out ) : ?>
        <div class="eu-pkg-sidebar-ctas">
            <span class="eu-pkg-sidebar-btn-soldout" aria-disabled="true">
                🚫 <?php eu_te( 'Package Sold Out', [ 'id' => 'Paket Habis' ] ); ?>
            </span>
            <a href="<?php echo esc_url( $wa_link ); ?>"
               target="_blank" rel="noopener noreferrer"
               class="eu-pkg-sidebar-btn-wa">
                💬 <?php eu_te( 'Join Waitlist via WhatsApp', [ 'id' => 'Daftar Antrian via WhatsApp' ] ); ?>
            </a>
        </div>
        <p class="eu-pkg-sidebar-seats eu-pkg-sidebar-seats--sold">
            <span aria-hidden="true">●</span> <?php eu_te( 'This package is fully booked', [ 'id' => 'Paket ini sudah penuh' ] ); ?>
        </p>
        <?php else : ?>
        <div class="eu-pkg-sidebar-ctas">
            <a href="<?php echo esc_url( $book_url ); ?>" class="eu-pkg-sidebar-btn-primary">
                🕋 <?php eu_te( 'Book This Package', [ 'id' => 'Pesan Paket Ini' ] ); ?>
            </a>
            <a href="<?php echo esc_url( $wa_link ); ?>"
               target="_blank" rel="noopener noreferrer"
               class="eu-pkg-sidebar-btn-wa">
                💬 <?php eu_te( 'WhatsApp Us', [ 'id' => 'Hubungi Kami via WhatsApp' ] ); ?>
            </a>
        </div>
        <?php if ( $seats > 0 ) : ?>
        <p class="eu-pkg-sidebar-seats">
            <span class="eu-pkg-sidebar-seats-dot" aria-hidden="true">●</span>
            <?php echo esc_html( $seats ); ?> <?php eu_te( 'seats remaining', [ 'id' => 'kursi tersisa' ] ); ?>
        </p>
        <?php endif; ?>
        <?php endif; ?>

    </aside>

    <!-- ============================================================
         RIGHT CONTENT — scrollable
         ============================================================ -->
    <div class="eu-pkg-content" id="eu-pkg-content">

        <!-- Package type pill -->
        <?php if ( $type_label ) : ?>
        <p class="eu-pkg-type-tag"><?php echo esc_html( $type_label ); ?></p>
        <?php endif; ?>

        <!-- Title -->
        <h1 class="eu-pkg-title"><?php the_title(); ?></h1>

        <!-- Additional country highlight -->
        <?php if ( $add_country ) : ?>
        <p class="eu-pkg-country-highlight">
            🌍 <?php eu_te( 'Also visiting:', [ 'id' => 'Juga mengunjungi:' ] ); ?> <strong><?php echo esc_html( $add_country ); ?></strong>
        </p>
        <?php endif; ?>

        <!-- Price -->
        <?php if ( $price_fmt || $discount_fmt ) : ?>
        <div class="eu-pkg-price-block">
            <?php if ( $discount_fmt ) : ?>
                <span class="eu-pkg-price-from"><?php eu_te( 'Special Price', [ 'id' => 'Harga Spesial' ] ); ?></span>
                <span class="eu-pkg-price-amount eu-pkg-price-amount--discount">
                    <?php echo esc_html( $discount_fmt ); ?>
                </span>
                <del class="eu-pkg-price-original" aria-label="Original price">
                    <?php echo esc_html( $price_fmt ); ?>
                </del>
                <span class="eu-pkg-price-per"><?php eu_te( '/ person', [ 'id' => '/ orang' ] ); ?></span>
            <?php else : ?>
                <span class="eu-pkg-price-from"><?php eu_te( 'Starting from', [ 'id' => 'Mulai dari' ] ); ?></span>
                <span class="eu-pkg-price-amount"><?php echo esc_html( $price_fmt ); ?></span>
                <span class="eu-pkg-price-per"><?php eu_te( '/ person', [ 'id' => '/ orang' ] ); ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <hr class="eu-pkg-divider">

        <!-- Departure · Duration · Airline -->
        <div class="eu-pkg-meta-grid">
            <?php if ( $departure_fmt ) : ?>
            <div class="eu-pkg-meta-item">
                <span class="eu-pkg-meta-icon" aria-hidden="true">📅</span>
                <div>
                    <span class="eu-pkg-meta-label"><?php echo esc_html( $departure_label ); ?></span>
                    <span class="eu-pkg-meta-value"><?php echo esc_html( $departure_fmt ); ?></span>
                </div>
            </div>
            <?php endif; ?>
            <?php if ( $muthawwif ) : ?>
            <div class="eu-pkg-meta-item">
                <span class="eu-pkg-meta-icon" aria-hidden="true">👳</span>
                <div>
                    <span class="eu-pkg-meta-label"><?php eu_te( 'Muthawwif', [ 'id' => 'Muthawwif' ] ); ?></span>
                    <span class="eu-pkg-meta-value"><?php echo esc_html( $muthawwif ); ?></span>
                </div>
            </div>
            <?php endif; ?>
            <?php if ( $duration ) : ?>
            <div class="eu-pkg-meta-item">
                <span class="eu-pkg-meta-icon" aria-hidden="true">⏱</span>
                <div>
                    <span class="eu-pkg-meta-label"><?php eu_te( 'Duration', [ 'id' => 'Durasi' ] ); ?></span>
                    <span class="eu-pkg-meta-value"><?php echo esc_html( $duration ); ?></span>
                </div>
            </div>
            <?php endif; ?>
            <?php if ( $airline ) : ?>
            <div class="eu-pkg-meta-item">
                <span class="eu-pkg-meta-icon" aria-hidden="true">✈️</span>
                <div>
                    <span class="eu-pkg-meta-label"><?php eu_te( 'Airline', [ 'id' => 'Maskapai' ] ); ?></span>
                    <span class="eu-pkg-meta-value"><?php echo esc_html( $airline ); ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <hr class="eu-pkg-divider">

        <!-- Hotels -->
        <?php if ( $hotel_makkah || $hotel_madinah ) : ?>
        <h2 class="eu-pkg-section-title"><?php eu_te( 'Hotel Information', [ 'id' => 'Informasi Hotel' ] ); ?></h2>
        <div class="eu-pkg-hotels-grid">
            <?php if ( $hotel_makkah ) : ?>
            <div class="eu-pkg-hotel-card">
                <span class="eu-pkg-hotel-city">🕋 Makkah</span>
                <span class="eu-pkg-hotel-name"><?php echo esc_html( $hotel_makkah ); ?></span>
                <?php if ( $hotel_mk_rat ) : ?>
                <span class="eu-pkg-hotel-stars" aria-label="<?php echo esc_attr( $hotel_mk_rat ); ?> stars">
                    <?php echo esc_html( $stars( (int) $hotel_mk_rat ) ); ?>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if ( $hotel_madinah ) : ?>
            <div class="eu-pkg-hotel-card">
                <span class="eu-pkg-hotel-city">🕌 Madinah</span>
                <span class="eu-pkg-hotel-name"><?php echo esc_html( $hotel_madinah ); ?></span>
                <?php if ( $hotel_md_rat ) : ?>
                <span class="eu-pkg-hotel-stars" aria-label="<?php echo esc_attr( $hotel_md_rat ); ?> stars">
                    <?php echo esc_html( $stars( (int) $hotel_md_rat ) ); ?>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <hr class="eu-pkg-divider">
        <?php endif; ?>

        <!-- Inclusions & Exclusions -->
        <?php if ( $inclusions || $exclusions ) : ?>
        <div class="eu-pkg-inclexcl-grid">
            <?php if ( $inclusions ) : ?>
            <div class="eu-pkg-inclexcl-col">
                <h2 class="eu-pkg-section-title eu-pkg-section-title--incl">✓ <?php eu_te( 'Inclusions', [ 'id' => 'Termasuk' ] ); ?></h2>
                <ul class="eu-pkg-list eu-pkg-list--incl">
                    <?php foreach ( array_filter( array_map( 'trim', explode( "\n", $inclusions ) ) ) as $line ) : ?>
                    <li><?php echo esc_html( ltrim( $line, "✓✗\-•* \t" ) ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php if ( $exclusions ) : ?>
            <div class="eu-pkg-inclexcl-col">
                <h2 class="eu-pkg-section-title eu-pkg-section-title--excl">✗ <?php eu_te( 'Exclusions', [ 'id' => 'Tidak Termasuk' ] ); ?></h2>
                <ul class="eu-pkg-list eu-pkg-list--excl">
                    <?php foreach ( array_filter( array_map( 'trim', explode( "\n", $exclusions ) ) ) as $line ) : ?>
                    <li><?php echo esc_html( ltrim( $line, "✓✗\-•* \t" ) ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Additional Tour -->
        <?php if ( $add_tour ) : ?>
        <hr class="eu-pkg-divider">
        <div class="eu-pkg-addtour">
            <h2 class="eu-pkg-section-title">🗺️ <?php eu_te( 'Additional Tour', [ 'id' => 'Tur Tambahan' ] ); ?></h2>
            <p class="eu-pkg-addtour-value"><?php echo esc_html( $add_tour ); ?></p>
        </div>
        <?php endif; ?>

        <!-- Post description / itinerary from editor -->
        <?php
        $content = get_the_content();
        if ( $content ) : ?>
        <hr class="eu-pkg-divider">
        <div class="eu-pkg-description">
            <?php the_content(); ?>
        </div>
        <?php endif; ?>

    </div><!-- /eu-pkg-content -->

</main><!-- /eu-pkg-single -->

<?php
endwhile;
?>
<?php block_template_part( 'footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
