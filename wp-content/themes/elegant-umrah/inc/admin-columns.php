<?php
/**
 * Admin Columns & Quick Edit — Umrah Package
 *
 * Adds Sold Out, Price, and Discount Price columns to the
 * umrah_package post list and wires up Quick Edit for instant updates.
 *
 * @package ElegantUmrah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ─────────────────────────────────────────────────────────────────────────────
// 0. FEATURED IMAGE AS COVER — ensure metabox is always shown for umrah_package
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Force the "Featured Image" (post thumbnail) metabox to always be visible
 * on the umrah_package edit screen so editors cannot accidentally hide it
 * via Screen Options.
 */
// (Removed broken add_meta_box override for postimagediv that interferes with Block Editor)

/**
 * Add an instructional notice inside the Package Details meta box header
 * so editors know the Featured Image is the cover/poster for the package page.
 * Rendered via the 'eu_before_field_group' action that field-core.php fires.
 */
add_action( 'eu_before_field_group', function ( string $group_id ): void {
    if ( 'umrah_package_fields' !== $group_id ) return;
    ?>
    <div class="eu-cover-notice" style="
        background: #f0f7ff;
        border-left: 4px solid #2271b1;
        padding: 10px 14px;
        margin-bottom: 16px;
        border-radius: 0 4px 4px 0;
        font-size: 13px;
        line-height: 1.5;
        color: #1d2327;
    ">
        <strong>🖼 Package Cover Image / Promotion Poster</strong><br>
        Use the <strong>"Package Cover Image (Poster)"</strong> box on the right sidebar
        to set the image shown on the single package page and as the promotional poster.
    </div>
    <?php
} );



// ─────────────────────────────────────────────────────────────────────────────
// 1. REGISTER CUSTOM COLUMNS
// ─────────────────────────────────────────────────────────────────────────────

add_filter( 'manage_umrah_package_posts_columns', function ( array $cols ): array {
    // Insert after 'title'
    $new = [];
    foreach ( $cols as $key => $label ) {
        $new[ $key ] = $label;
        if ( 'title' === $key ) {
            $new['eu_currency']       = __( 'Currency', 'elegant-umrah' );
            $new['eu_price']          = __( 'Price', 'elegant-umrah' );
            $new['eu_discount_price'] = __( 'Discount Price', 'elegant-umrah' );
            $new['eu_sold_out']       = __( 'Sold Out', 'elegant-umrah' );
        }
    }
    return $new;
} );

// ─────────────────────────────────────────────────────────────────────────────
// 2. POPULATE COLUMN CELLS
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'manage_umrah_package_posts_custom_column', function ( string $col, int $post_id ): void {
    switch ( $col ) {

        case 'eu_currency':
            $currency = get_post_meta( $post_id, 'eu_currency', true ) ?: 'Rp';
            echo '<span class="eu-col-currency">' . esc_html( $currency ) . '</span>';
            break;

        case 'eu_price':
            $price = get_post_meta( $post_id, 'eu_price', true );
            if ( $price ) {
                echo '<span class="eu-col-price">' . esc_html( $price ) . '</span>';
            } else {
                echo '<span class="eu-col-empty">—</span>';
            }
            break;

        case 'eu_discount_price':
            $discount = get_post_meta( $post_id, 'eu_discount_price', true );
            if ( $discount ) {
                echo '<span class="eu-col-discount">' . esc_html( $discount ) . '</span>';
            } else {
                echo '<span class="eu-col-empty">—</span>';
            }
            break;

        case 'eu_sold_out':
            $sold_out = (bool) get_post_meta( $post_id, 'eu_is_sold_out', true );
            if ( $sold_out ) {
                echo '<span class="eu-col-badge eu-col-badge--sold">Sold Out</span>';
            } else {
                echo '<span class="eu-col-badge eu-col-badge--available">Available</span>';
            }
            break;
    }
}, 10, 2 );

// ─────────────────────────────────────────────────────────────────────────────
// 3. QUICK EDIT FIELDS
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'quick_edit_custom_box', function ( string $col, string $post_type ): void {
    if ( 'umrah_package' !== $post_type ) return;
    if ( ! in_array( $col, [ 'eu_currency', 'eu_price', 'eu_discount_price', 'eu_sold_out' ], true ) ) return;

    // Only output the full Quick Edit block once (keyed on the first column that fires)
    static $rendered = false;
    if ( $rendered ) return;
    $rendered = true;
    ?>
    <fieldset class="inline-edit-col-right eu-quick-edit-fields">
        <div class="inline-edit-col">
            <h4><?php esc_html_e( '📦 Package Pricing & Status', 'elegant-umrah' ); ?></h4>

            <div class="eu-qe-row">
                <!-- Currency -->
                <label>
                    <span class="title"><?php esc_html_e( 'Currency', 'elegant-umrah' ); ?></span>
                    <select name="eu_currency" id="eu-qe-currency">
                        <option value="Rp">Rp — Indonesian Rupiah</option>
                        <option value="USD">USD — US Dollar</option>
                    </select>
                </label>

                <!-- Sold Out toggle -->
                <label class="eu-qe-checkbox-label">
                    <input type="checkbox" name="eu_is_sold_out" id="eu-qe-sold-out" value="1">
                    <span class="title"><?php esc_html_e( 'Sold Out', 'elegant-umrah' ); ?></span>
                </label>
            </div>

            <div class="eu-qe-row">
                <!-- Price -->
                <label>
                    <span class="title"><?php esc_html_e( 'Price', 'elegant-umrah' ); ?></span>
                    <input type="text"
                           name="eu_price"
                           id="eu-qe-price"
                           class="eu-qe-input"
                           placeholder="e.g. 22.500.000">
                </label>

                <!-- Discount Price -->
                <label>
                    <span class="title"><?php esc_html_e( 'Discount Price', 'elegant-umrah' ); ?></span>
                    <input type="text"
                           name="eu_discount_price"
                           id="eu-qe-discount"
                           class="eu-qe-input"
                           placeholder="Optional">
                </label>
            </div>

            <?php wp_nonce_field( 'eu_quick_edit_nonce', 'eu_quick_edit_nonce' ); ?>
        </div>
    </fieldset>
    <?php
}, 10, 2 );

// ─────────────────────────────────────────────────────────────────────────────
// 4. SAVE QUICK EDIT
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'save_post_umrah_package', function ( int $post_id ): void {
    // Quick Edit fires via admin-ajax, not the bulk action path
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Only run for Quick Edit requests (nonce check)
    if ( ! isset( $_POST['eu_quick_edit_nonce'] ) ) return;
    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['eu_quick_edit_nonce'] ) ), 'eu_quick_edit_nonce' ) ) return;

    // Currency
    if ( isset( $_POST['eu_currency'] ) ) {
        $allowed_currencies = [ 'Rp', 'USD' ];
        $currency = sanitize_text_field( wp_unslash( $_POST['eu_currency'] ) );
        if ( in_array( $currency, $allowed_currencies, true ) ) {
            update_post_meta( $post_id, 'eu_currency', $currency );
        }
    }

    // Price
    if ( isset( $_POST['eu_price'] ) ) {
        update_post_meta( $post_id, 'eu_price', sanitize_text_field( wp_unslash( $_POST['eu_price'] ) ) );
    }

    // Discount Price
    if ( isset( $_POST['eu_discount_price'] ) ) {
        update_post_meta( $post_id, 'eu_discount_price', sanitize_text_field( wp_unslash( $_POST['eu_discount_price'] ) ) );
    }

    // Sold Out — checkbox: present = true, absent = false
    $sold_out = isset( $_POST['eu_is_sold_out'] ) ? '1' : '';
    update_post_meta( $post_id, 'eu_is_sold_out', $sold_out );
} );

// ─────────────────────────────────────────────────────────────────────────────
// 5. ENQUEUE ADMIN ASSETS ON LIST SCREEN
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'admin_enqueue_scripts', function ( string $hook ): void {
    global $post_type;

    // Only on the umrah_package list screen
    if ( 'edit.php' !== $hook || 'umrah_package' !== $post_type ) return;

    // Inline CSS
    wp_add_inline_style( 'wp-admin', eu_admin_columns_css() );

    // Inline JS — pre-populates Quick Edit fields with current row values
    wp_enqueue_script( 'jquery' );
    wp_add_inline_script( 'jquery', eu_admin_columns_js() );
} );

// ─────────────────────────────────────────────────────────────────────────────
// 6. HIDDEN DATA CELLS (for JS to read current values per row)
// ─────────────────────────────────────────────────────────────────────────────

// We piggyback on the title column action to inject hidden spans
add_filter( 'post_row_actions', function ( array $actions, \WP_Post $post ): array {
    if ( 'umrah_package' !== $post->post_type ) return $actions;

    $currency = esc_attr( get_post_meta( $post->ID, 'eu_currency', true ) ?: 'Rp' );
    $price    = esc_attr( get_post_meta( $post->ID, 'eu_price', true ) ?: '' );
    $discount = esc_attr( get_post_meta( $post->ID, 'eu_discount_price', true ) ?: '' );
    $sold_out = get_post_meta( $post->ID, 'eu_is_sold_out', true ) ? '1' : '0';

    // Inject hidden data attributes into the row via a hidden span in row actions
    // (WordPress does not have a built-in row data hook, this is the standard pattern)
    $actions['_eu_data'] = sprintf(
        '<span class="eu-row-data hidden"
            data-currency="%s"
            data-price="%s"
            data-discount="%s"
            data-sold-out="%s"
            aria-hidden="true"></span>',
        $currency, $price, $discount, $sold_out
    );

    return $actions;
}, 10, 2 );

// ─────────────────────────────────────────────────────────────────────────────
// 7. CSS
// ─────────────────────────────────────────────────────────────────────────────

function eu_admin_columns_css(): string {
    return <<<CSS
    /* ── Custom columns ─────────────────────────────── */
    .column-eu_currency    { width: 80px; }
    .column-eu_price       { width: 130px; }
    .column-eu_discount_price { width: 130px; }
    .column-eu_sold_out    { width: 90px; }

    .eu-col-price   { font-weight: 600; color: #1d6b1d; }
    .eu-col-discount { font-weight: 600; color: #c0392b; }
    .eu-col-currency { font-weight: 600; color: #555; }
    .eu-col-empty   { color: #bbb; }

    .eu-col-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 10px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .eu-col-badge--sold      { background: #fde8e8; color: #b91c1c; border: 1px solid #fca5a5; }
    .eu-col-badge--available { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }

    /* Hide the _eu_data action from display */
    .row-actions ._eu_data { display: none !important; }
    .eu-row-data { display: none; }

    /* ── Quick Edit panel ───────────────────────────── */
    .eu-quick-edit-fields h4 {
        margin: 10px 0 8px;
        font-size: 13px;
        font-weight: 600;
        color: #1d2327;
        border-bottom: 1px solid #dcdcde;
        padding-bottom: 6px;
    }
    .eu-qe-row {
        display: flex;
        gap: 16px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    .eu-qe-row label {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        min-width: 160px;
    }
    .eu-qe-row .title {
        font-size: 12px;
        font-weight: 600;
        color: #50575e;
    }
    .eu-qe-input, .eu-qe-row select {
        width: 100%;
        padding: 5px 8px;
        border: 1px solid #8c8f94;
        border-radius: 3px;
        font-size: 13px;
        box-sizing: border-box;
    }
    .eu-qe-checkbox-label {
        flex-direction: row !important;
        align-items: center;
        gap: 8px !important;
        margin-top: 18px;
    }
    .eu-qe-checkbox-label input { width: auto; margin: 0; }
    .eu-qe-checkbox-label .title { margin: 0; }
CSS;
}

// ─────────────────────────────────────────────────────────────────────────────
// 8. JAVASCRIPT — populate Quick Edit from row data
// ─────────────────────────────────────────────────────────────────────────────

function eu_admin_columns_js(): string {
    return <<<JS
    jQuery(function($) {
        var \$inlineEditPost = inlineEditPost;

        // Store reference to original edit function
        var origEdit = \$inlineEditPost.edit;

        // Override the Quick Edit 'edit' method
        \$inlineEditPost.edit = function(id) {
            // Call the original first to render the row
            origEdit.apply(this, arguments);

            // Resolve post ID
            var postId = (typeof id === 'object') ? parseInt(this.getId(id)) : parseInt(id);
            if (!postId) return;

            // Find the hidden data span in this row
            var \$row   = $('#post-' + postId);
            var \$data  = \$row.find('.eu-row-data');

            if (!\$data.length) return;

            var currency = \$data.data('currency') || 'Rp';
            var price    = \$data.data('price')    || '';
            var discount = \$data.data('discount') || '';
            var soldOut  = \$data.data('sold-out') === '1' || \$data.data('sold-out') === 1;

            // Populate Quick Edit fields
            var \$editRow = $('#edit-' + postId);
            \$editRow.find('#eu-qe-currency').val(currency);
            \$editRow.find('#eu-qe-price').val(price);
            \$editRow.find('#eu-qe-discount').val(discount);
            \$editRow.find('#eu-qe-sold-out').prop('checked', soldOut);
        };
    });
JS;
}
