<?php
/**
 * Archive Template for Umrah Packages
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

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
// Define package types for the filter (based on post meta eu_package_type)
$package_types = [
    'umrah'      => eu_t( 'Umrah',           [ 'id' => 'Umrah' ] ),
    'hajj'       => eu_t( 'Hajj',            [ 'id' => 'Haji' ] ),
    'umrah_plus' => eu_t( 'Umrah Plus',      [ 'id' => 'Umrah Plus' ] ),
    'family'     => eu_t( 'Family Package',  [ 'id' => 'Paket Keluarga' ] ),
    'vip'        => eu_t( 'VIP Premium',     [ 'id' => 'VIP Premium' ] ),
    'honeymoon'  => eu_t( 'Honeymoon Umrah', [ 'id' => 'Umrah Bulan Madu' ] ),
];
?>

<style>
/* 
 * Archive specific styles.
 * (Card and filter button styles are already in style.css)
 */
.eu-archive-hero {
    background-color: var(--eu-color-dark, #0B0B0B);
    color: var(--eu-color-white, #F8F8F8);
    padding: 6rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.eu-archive-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: radial-gradient(circle at center, rgba(212, 175, 55, 0.15) 0%, rgba(11, 11, 11, 1) 70%);
    pointer-events: none;
}

.eu-archive-hero-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 700;
    color: var(--eu-color-gold, #D4AF37);
    margin-bottom: 1rem;
    position: relative;
    z-index: 1;
}

.eu-archive-hero-subtitle {
    font-size: clamp(1rem, 2vw, 1.25rem);
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.eu-archive-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 2rem;
}

.eu-packages-filter {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 3rem;
}

/* Base styles for grid and buttons in case they are missing in style.css */
.eu-packages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
    transition: opacity 0.3s ease;
}

.eu-no-results {
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    grid-column: 1 / -1;
}

.eu-no-results h3 {
    font-size: 1.5rem;
    margin-top: 1rem;
    color: var(--eu-color-dark, #0B0B0B);
}
</style>

<div class="eu-packages-archive">
    
    <!-- Hero Section -->
    <header class="eu-archive-hero">
        <h1 class="eu-archive-hero-title"><?php esc_html_e( 'Premium Umrah & Hajj Packages', 'elegant-umrah' ); ?></h1>
        <p class="eu-archive-hero-subtitle"><?php esc_html_e( 'Discover our meticulously crafted journeys designed for your spiritual comfort and peace of mind.', 'elegant-umrah' ); ?></p>
    </header>

    <div class="eu-archive-container">
        
        <!-- Filter Navigation -->
        <?php if ( ! empty( $package_types ) ) : ?>
            <nav class="eu-packages-filter" aria-label="Package Categories">
                <button type="button" class="eu-filter-btn active" data-filter="all"><?php eu_te( 'All Packages', [ 'id' => 'Semua Paket' ] ); ?></button>
                <?php foreach ( $package_types as $slug => $name ) : ?>
                    <button type="button" class="eu-filter-btn" data-filter="<?php echo esc_attr( $slug ); ?>">
                        <?php echo esc_html( $name ); ?>
                    </button>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <!-- Packages Grid -->
        <div class="eu-packages-grid" id="eu-packages-grid">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) :
                    the_post();
                    // Render the package card using the helper function
                    eu_render_package_card( get_the_ID() );
                endwhile;
            else :
                ?>
                <div class="eu-no-results">
                    <span style="font-size: 3rem;">🕋</span>
                    <h3><?php esc_html_e( 'No packages available right now.', 'elegant-umrah' ); ?></h3>
                    <p><?php esc_html_e( 'Please check back later or contact our support team.', 'elegant-umrah' ); ?></p>
                </div>
                <?php
            endif;
            ?>
        </div>
        
        <!-- Pagination -->
        <?php
        the_posts_pagination( [
            'mid_size'  => 2,
            'prev_text' => __( '« Prev', 'elegant-umrah' ),
            'next_text' => __( 'Next »', 'elegant-umrah' ),
        ] );
        ?>
    </div>
</div>

<?php block_template_part( 'footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
