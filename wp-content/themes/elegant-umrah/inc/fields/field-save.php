<?php
/**
 * Field Save - Handles saving field values on post save
 *
 * @package ElegantUmrah\Fields
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Save all field groups on post save
 */
function eu_save_field_groups( int $post_id ): void {
    // Bail on autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // Bail on revisions
    if ( wp_is_post_revision( $post_id ) ) return;

    // Check for REST API (blocks editor saving)
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        eu_save_rest_fields( $post_id );
        return;
    }

    $groups = eu_get_field_groups();

    foreach ( $groups as $group ) {
        $nonce_key = 'eu_nonce_' . $group['id'];

        // Verify nonce
        if ( ! isset( $_POST[ $nonce_key ] ) ) continue;
        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_key ] ) ), 'eu_save_fields_' . $group['id'] ) ) continue;

        // Check permissions
        $capability = 'edit_post';
        if ( ! current_user_can( $capability, $post_id ) ) continue;

        // Check location match
        if ( ! eu_location_matches( $group['location'], $post_id ) ) continue;

        // Save each field
        foreach ( $group['fields'] as $field_config ) {
            $field = eu_build_field( $field_config );
            eu_save_single_field( $post_id, $field );
        }
    }
}
add_action( 'save_post', 'eu_save_field_groups' );

/**
 * Save a single field value
 *
 * @param int   $post_id  Post ID
 * @param array $field    Field config
 */
function eu_save_single_field( int $post_id, array $field ): void {
    $key  = $field['name'];
    $type = $field['type'];

    if ( ! $key ) return;

    // Get raw value from POST
    if ( $type === 'checkbox' ) {
        // Checkbox: sent as '1' or '0' via hidden
        $raw = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '0';
    } elseif ( isset( $_POST[ $key ] ) ) {
        $raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    } else {
        // Field not in POST (e.g., unchecked checkbox without hidden)
        $raw = '';
    }

    // Sanitize
    $value = eu_sanitize_field_value( $raw, $field );

    // Save or delete
    if ( $value !== '' && $value !== false && $value !== 0 ) {
        update_post_meta( $post_id, $key, $value );
    } elseif ( $type === 'checkbox' ) {
        update_post_meta( $post_id, $key, (bool) $value );
    } elseif ( $type === 'number' ) {
        update_post_meta( $post_id, $key, 0 );
    } else {
        delete_post_meta( $post_id, $key );
    }
}

/**
 * Handle REST API saves (block editor)
 * The block editor sends meta via REST separately,
 * but we add a hook here for completeness.
 */
function eu_save_rest_fields( int $post_id ): void {
    // Meta registered via register_post_meta handles REST saves automatically.
    // This function can be extended for additional logic.
    do_action( 'eu_rest_save_fields', $post_id );
}

/**
 * Handle AJAX image field save (if needed)
 */
add_action( 'wp_ajax_eu_get_image_url', function() {
    check_ajax_referer( 'eu_nonce', 'nonce' );

    $attachment_id = absint( $_POST['attachment_id'] ?? 0 );
    $size          = sanitize_text_field( $_POST['size'] ?? 'thumbnail' );

    if ( ! $attachment_id ) {
        wp_send_json_error( 'Invalid attachment ID' );
    }

    $url = wp_get_attachment_image_url( $attachment_id, $size );
    wp_send_json_success( [ 'url' => $url ] );
} );
