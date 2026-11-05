<?php
/**
 * Packages Grid Block - Server-Side Render
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$posts_per_page = isset( $attributes['postsPerPage'] ) ? max( 1, (int) $attributes['postsPerPage'] ) : 3;
$show_filter    = ! empty( $attributes['showFilter'] );

$query_args = [
    'post_type'        => 'umrah_package',
    'post_status'      => 'publish',
    'posts_per_page'   => $posts_per_page,
    'eu_sort_packages' => true,
];

$meta_query = [];

// Check URL filters if present on archive / search pages
if ( ! empty( $_GET['type'] ) && 'all' !== $_GET['type'] ) {
    $clean_type = sanitize_key( wp_unslash( $_GET['type'] ) );
    $alt_slug   = strpos( $clean_type, '_' ) !== false ? str_replace( '_', '-', $clean_type ) : str_replace( '-', '_', $clean_type );
    $meta_query[] = [
        'key'     => 'eu_package_type',
        'value'   => array_unique( [ $clean_type, $alt_slug ] ),
        'compare' => 'IN',
    ];
}

$active_month = '';
if ( ! empty( $_GET['month'] ) ) {
    $clean_month = sanitize_text_field( wp_unslash( $_GET['month'] ) );
    if ( preg_match( '/^\d{4}-\d{2}$/', $clean_month ) ) {
        $active_month = $clean_month;
        $meta_query[] = [
            'relation' => 'OR',
            [
                'key'     => 'eu_departure_date',
                'value'   => '^' . preg_quote( $clean_month, '/' ),
                'compare' => 'REGEXP',
            ],
            [
                'key'     => 'eu_estimate_month',
                'value'   => $clean_month,
                'compare' => '=',
            ],
        ];
    }
}

if ( ! empty( $meta_query ) ) {
    if ( count( $meta_query ) > 1 ) {
        $meta_query['relation'] = 'AND';
    }
    $query_args['meta_query'] = $meta_query;
}

$packages_query = new WP_Query( $query_args );
$package_types  = function_exists( 'eu_get_package_types' ) ? eu_get_package_types() : [];
$active_filter  = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : 'all';
?>
<div class="eu-packages-grid-wrap">
    <?php if ( ! empty( $active_month ) ) : 
        $all_months = function_exists( 'eu_get_available_departure_months' ) ? eu_get_available_departure_months() : [];
        $month_label = $all_months[ $active_month ] ?? date_i18n( 'F Y', strtotime( $active_month . '-01' ) );
    ?>
        <div class="eu-archive-active-filters" style="margin-bottom: 2rem;">
            <div class="eu-filter-badge">
                <span>📅 <?php function_exists( 'eu_te' ) ? eu_te( 'Departure:', [ 'id' => 'Keberangkatan:' ] ) : esc_html_e( 'Departure:', 'elegant-umrah' ); ?> <strong><?php echo esc_html( $month_label ); ?></strong></span>
                <a href="<?php echo esc_url( remove_query_arg( 'month' ) ); ?>" class="eu-filter-badge-remove" title="<?php function_exists( 'eu_te' ) ? eu_te( 'Clear month filter', [ 'id' => 'Hapus filter bulan' ] ) : esc_attr_e( 'Clear month filter', 'elegant-umrah' ); ?>" aria-label="<?php function_exists( 'eu_te' ) ? eu_te( 'Clear month filter', [ 'id' => 'Hapus filter bulan' ] ) : esc_attr_e( 'Clear month filter', 'elegant-umrah' ); ?>">&times;</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if ( $show_filter ) : ?>
        <div class="eu-category-filter-wrap alignfull" style="background-color:#FFFFFF;border-bottom:1px solid rgba(0,0,0,0.07);padding:1.25rem 1.5rem;margin-bottom:2.5rem;">
            <div class="eu-category-filter" style="display:flex;gap:1rem;justify-content:center;overflow-x:auto;max-width:1200px;margin:0 auto">
                <button class="eu-filter-btn<?php echo 'all' === $active_filter ? ' active' : ''; ?>" data-filter="all"><?php esc_html_e( 'All Packages', 'elegant-umrah' ); ?></button>
                <?php foreach ( $package_types as $slug => $label ) : ?>
                    <button class="eu-filter-btn<?php echo $slug === $active_filter ? ' active' : ''; ?>" data-filter="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></button>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="eu-packages-grid">
        <?php
        if ( $packages_query->have_posts() ) {
            while ( $packages_query->have_posts() ) {
                $packages_query->the_post();
                if ( function_exists( 'eu_render_package_card' ) ) {
                    eu_render_package_card( get_the_ID() );
                }
            }
            wp_reset_postdata();
        } else {
            ?>
            <div class="eu-no-results" style="grid-column:1/-1;text-align:center;padding:3rem;background:#fff;border-radius:12px;">
                <p style="font-size:2rem">🕋</p>
                <h3><?php esc_html_e( 'No packages available at the moment.', 'elegant-umrah' ); ?></h3>
            </div>
            <?php
        }
        ?>
    </div>
</div>
