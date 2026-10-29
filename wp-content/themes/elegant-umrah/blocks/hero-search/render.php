<?php
/**
 * Hero Search Block - Server-Side Render
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$month_label       = ! empty( $attributes['monthLabel'] ) ? $attributes['monthLabel'] : __( 'Bulan Keberangkatan', 'elegant-umrah' );
$month_placeholder = ! empty( $attributes['monthPlaceholder'] ) ? $attributes['monthPlaceholder'] : __( 'Semua Bulan Keberangkatan', 'elegant-umrah' );
$type_label        = ! empty( $attributes['typeLabel'] ) ? $attributes['typeLabel'] : __( 'Tipe Paket', 'elegant-umrah' );
$type_placeholder  = ! empty( $attributes['typePlaceholder'] ) ? $attributes['typePlaceholder'] : __( 'Semua Tipe Paket', 'elegant-umrah' );
$button_text       = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : __( 'Cari Paket', 'elegant-umrah' );

$available_months = function_exists( 'eu_get_available_departure_months' ) ? eu_get_available_departure_months() : [];
$package_types    = function_exists( 'eu_get_package_types' ) ? eu_get_package_types() : [];
$action_url       = home_url( '/packages/' );
?>
<div class="eu-hero-search-wrapper">
    <div class="eu-hero-search-card">
        <form method="get" action="<?php echo esc_url( $action_url ); ?>" class="eu-hero-search-form">
            <!-- Departure Month Field -->
            <div class="eu-hero-search-field">
                <label for="eu-search-month" class="eu-hero-search-label">
                    <svg class="eu-hero-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span><?php echo esc_html( $month_label ); ?></span>
                </label>
                <div class="eu-hero-search-select-wrap">
                    <select id="eu-search-month" name="month" class="eu-hero-search-select">
                        <option value=""><?php echo esc_html( $month_placeholder ); ?></option>
                        <?php foreach ( $available_months as $m_val => $m_label ) : ?>
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
                    <span><?php echo esc_html( $type_label ); ?></span>
                </label>
                <div class="eu-hero-search-select-wrap">
                    <select id="eu-search-type" name="type" class="eu-hero-search-select">
                        <option value="all"><?php echo esc_html( $type_placeholder ); ?></option>
                        <?php foreach ( $package_types as $t_slug => $t_name ) : ?>
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
                    <span><?php echo esc_html( $button_text ); ?></span>
                </button>
            </div>
        </form>
    </div>
</div>
