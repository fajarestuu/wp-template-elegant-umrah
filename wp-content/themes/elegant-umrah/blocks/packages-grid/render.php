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
    'post_type'      => 'umrah_package',
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
    'orderby'        => 'date',
    'order'          => 'DESC',
];

// Check URL filters if present on archive pages
if ( ! empty( $_GET['type'] ) && 'all' !== $_GET['type'] ) {
    $clean_type = sanitize_key( wp_unslash( $_GET['type'] ) );
    $alt_slug   = strpos( $clean_type, '_' ) !== false ? str_replace( '_', '-', $clean_type ) : str_replace( '-', '_', $clean_type );
    $query_args['meta_query'] = [
        [
            'key'     => 'eu_package_type',
            'value'   => array_unique( [ $clean_type, $alt_slug ] ),
            'compare' => 'IN',
        ],
    ];
}

$packages_query = new WP_Query( $query_args );
$package_types  = function_exists( 'eu_get_package_types' ) ? eu_get_package_types() : [];
?>
<div class="eu-packages-grid-wrap">
    <?php if ( $show_filter ) : ?>
        <div class="eu-category-filter-wrap alignfull" style="background-color:#FFFFFF;border-bottom:1px solid rgba(0,0,0,0.07);padding:1.25rem 1.5rem;margin-bottom:2.5rem;">
            <div class="eu-category-filter" style="display:flex;gap:1rem;justify-content:center;overflow-x:auto;max-width:1200px;margin:0 auto">
                <button class="eu-filter-btn active" data-filter="all"><?php esc_html_e( 'All Packages', 'elegant-umrah' ); ?></button>
                <?php foreach ( $package_types as $slug => $label ) : ?>
                    <button class="eu-filter-btn" data-filter="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></button>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="eu-packages-grid" style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:var(--wp--preset--spacing--30, 2rem);">
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
