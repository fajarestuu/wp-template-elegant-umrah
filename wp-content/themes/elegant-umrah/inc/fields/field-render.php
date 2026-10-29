<?php
/**
 * Field Render - HTML rendering for all field types
 *
 * @package ElegantUmrah\Fields
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Render all fields in a field group for a given post
 *
 * @param int   $post_id  Post ID
 * @param array $group    Field group config
 */
function eu_render_field_group( int $post_id, array $group ): void {
    echo '<div class="eu-meta-box-wrap">';

    foreach ( $group['fields'] as $field_config ) {
        $field = eu_build_field( $field_config );
        eu_render_field( $post_id, $field );
    }

    echo '</div>';
}

/**
 * Route rendering to the correct field type renderer
 *
 * @param int   $post_id  Post ID
 * @param array $field    Field config (already built with defaults)
 */
function eu_render_field( int $post_id, array $field ): void {
    $type      = $field['type'];
    $name      = $field['name'];
    $meta_key  = $name; // meta key = field name
    $value     = get_post_meta( $post_id, $meta_key, true );
    $width_class = ( isset( $field['width'] ) && $field['width'] === '100' ) ? ' full-width' : '';

    echo '<div class="eu-meta-field' . esc_attr( $width_class ) . '">';

    // Label
    if ( $type !== 'hidden' ) {
        eu_render_field_label( $field, $meta_key );
    }

    // Field
    match ( $type ) {
        'text', 'url', 'email', 'date', 'month', 'color' => eu_render_input( $field, $meta_key, $value ),
        'number'                                 => eu_render_number_input( $field, $meta_key, $value ),
        'textarea'                               => eu_render_textarea( $field, $meta_key, $value ),
        'select'                                 => eu_render_select( $field, $meta_key, $value ),
        'radio'                                  => eu_render_radio( $field, $meta_key, $value ),
        'checkbox'                               => eu_render_checkbox( $field, $meta_key, $value ),
        'image'                                  => eu_render_image_field( $field, $meta_key, $value ),
        'hidden'                                 => eu_render_hidden( $field, $meta_key, $value ),
        default                                  => eu_render_input( $field, $meta_key, $value ),
    };

    // Instructions
    if ( ! empty( $field['instructions'] ) ) {
        echo '<p class="description">' . esc_html( $field['instructions'] ) . '</p>';
    }

    echo '</div>';
}

/**
 * Render a <label> element
 */
function eu_render_field_label( array $field, string $for ): void {
    if ( empty( $field['label'] ) ) return;
    $required = ! empty( $field['required'] ) ? ' <span style="color:red">*</span>' : '';
    printf(
        '<label for="%s">%s%s</label>',
        esc_attr( 'eu_field_' . $for ),
        esc_html( $field['label'] ),
        $required // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    );
}

/**
 * Render text / url / email / date / color input
 */
function eu_render_input( array $field, string $key, $value ): void {
    $type        = $field['type'];
    $placeholder = $field['placeholder'] ?? '';
    $maxlength   = $field['maxlength'] ?? '';
    $class       = $field['class'] ?? '';

    $attrs = '';
    if ( $placeholder ) $attrs .= ' placeholder="' . esc_attr( $placeholder ) . '"';
    if ( $maxlength )   $attrs .= ' maxlength="' . absint( $maxlength ) . '"';
    if ( ! empty( $field['required'] ) ) $attrs .= ' required';

    $input_value = is_array( $value ) ? '' : esc_attr( (string) $value );

    echo '<input type="' . esc_attr( $type ) . '"'
        . ' id="eu_field_' . esc_attr( $key ) . '"'
        . ' name="' . esc_attr( $key ) . '"'
        . ' value="' . $input_value . '"'
        . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' )
        . $attrs // phpcs:ignore
        . '>';
}

/**
 * Render number input
 */
function eu_render_number_input( array $field, string $key, $value ): void {
    $min  = $field['min']  ?? '';
    $max  = $field['max']  ?? '';
    $step = $field['step'] ?? '1';

    $attrs = '';
    if ( $min !== '' )  $attrs .= ' min="' . esc_attr( $min ) . '"';
    if ( $max !== '' )  $attrs .= ' max="' . esc_attr( $max ) . '"';
    if ( $step !== '' ) $attrs .= ' step="' . esc_attr( $step ) . '"';

    if ( ! empty( $field['prepend'] ) || ! empty( $field['append'] ) ) {
        echo '<div style="display:flex;gap:4px;align-items:center;">';
        if ( ! empty( $field['prepend'] ) ) {
            echo '<span style="padding:0 8px;background:#f5f5f5;border:1px solid #ddd;border-radius:4px 0 0 4px;height:32px;display:flex;align-items:center">'
                . esc_html( $field['prepend'] ) . '</span>';
        }
    }

    echo '<input type="number"'
        . ' id="eu_field_' . esc_attr( $key ) . '"'
        . ' name="' . esc_attr( $key ) . '"'
        . ' value="' . esc_attr( (string) $value ) . '"'
        . $attrs // phpcs:ignore
        . ( ! empty( $field['required'] ) ? ' required' : '' )
        . '>';

    if ( ! empty( $field['prepend'] ) || ! empty( $field['append'] ) ) {
        if ( ! empty( $field['append'] ) ) {
            echo '<span style="padding:0 8px;background:#f5f5f5;border:1px solid #ddd;border-radius:0 4px 4px 0;height:32px;display:flex;align-items:center">'
                . esc_html( $field['append'] ) . '</span>';
        }
        echo '</div>';
    }
}

/**
 * Render textarea
 */
function eu_render_textarea( array $field, string $key, $value ): void {
    $rows        = $field['rows'] ?? 4;
    $placeholder = $field['placeholder'] ?? '';
    $class       = $field['class'] ?? '';

    echo '<textarea'
        . ' id="eu_field_' . esc_attr( $key ) . '"'
        . ' name="' . esc_attr( $key ) . '"'
        . ' rows="' . absint( $rows ) . '"'
        . ( $placeholder ? ' placeholder="' . esc_attr( $placeholder ) . '"' : '' )
        . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' )
        . ( ! empty( $field['required'] ) ? ' required' : '' )
        . '>' . esc_textarea( (string) $value ) . '</textarea>';
}

/**
 * Render select dropdown
 */
function eu_render_select( array $field, string $key, $value ): void {
    $choices    = $field['choices']    ?? [];
    $allow_null = $field['allow_null'] ?? true;
    $null_label = $field['null_label'] ?? '— Select —';
    $multiple   = ! empty( $field['multiple'] );
    $class      = $field['class'] ?? '';

    echo '<select'
        . ' id="eu_field_' . esc_attr( $key ) . '"'
        . ' name="' . esc_attr( $key ) . ( $multiple ? '[]' : '' ) . '"'
        . ( $multiple ? ' multiple' : '' )
        . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' )
        . ( ! empty( $field['required'] ) ? ' required' : '' )
        . '>';

    if ( $allow_null ) {
        echo '<option value="">' . esc_html( $null_label ) . '</option>';
    }

    foreach ( $choices as $choice_val => $choice_label ) {
        $selected = $multiple
            ? ( is_array( $value ) && in_array( $choice_val, $value, true ) ? ' selected' : '' )
            : selected( $value, $choice_val, false );
        echo '<option value="' . esc_attr( $choice_val ) . '"' . $selected . '>'
            . esc_html( $choice_label ) . '</option>';
    }

    echo '</select>';
}

/**
 * Render radio buttons
 */
function eu_render_radio( array $field, string $key, $value ): void {
    $choices = $field['choices'] ?? [];
    $layout  = $field['layout']  ?? 'horizontal';
    $flex    = $layout === 'horizontal' ? 'row' : 'column';

    echo '<div style="display:flex;flex-direction:' . esc_attr( $flex ) . ';flex-wrap:wrap;gap:0.75rem">';
    foreach ( $choices as $choice_val => $choice_label ) {
        $id      = 'eu_field_' . $key . '_' . $choice_val;
        $checked = checked( $value, $choice_val, false );
        echo '<label for="' . esc_attr( $id ) . '" style="display:flex;align-items:center;gap:0.3rem;cursor:pointer;font-weight:normal">'
            . '<input type="radio" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $choice_val ) . '"' . $checked . '>'
            . esc_html( $choice_label )
            . '</label>';
    }
    echo '</div>';
}

/**
 * Render checkbox (single toggle)
 */
function eu_render_checkbox( array $field, string $key, $value ): void {
    $label_on = $field['label_on']  ?? __( 'Yes', 'elegant-umrah' );
    $label    = $field['label']     ?? '';
    $id       = 'eu_field_' . $key;
    $checked  = checked( (bool) $value, true, false );

    // Hidden input to send 0 when unchecked
    echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="0">';
    echo '<label for="' . esc_attr( $id ) . '" style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-weight:normal">'
        . '<input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="1"' . $checked . '>'
        . esc_html( $label_on )
        . '</label>';
}

/**
 * Render image field (media uploader)
 */
function eu_render_image_field( array $field, string $key, $value ): void {
    $attachment_id = absint( $value );
    $img_url       = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'thumbnail' ) : '';
    $btn_label     = $field['button_label'] ?? __( 'Select Image', 'elegant-umrah' );
    $preview_style = $img_url ? '' : ' style="display:none"';

    echo '<div class="eu-image-field-wrap" data-key="' . esc_attr( $key ) . '">';

    echo '<input type="hidden"'
        . ' id="eu_field_' . esc_attr( $key ) . '"'
        . ' name="' . esc_attr( $key ) . '"'
        . ' class="eu-image-id"'
        . ' value="' . esc_attr( (string) $attachment_id ) . '">';

    echo '<div class="eu-image-preview-wrap"' . $preview_style . '>';
    echo '<img src="' . esc_url( $img_url ) . '" class="eu-image-preview" alt="" style="max-width:200px;border-radius:6px;margin-bottom:8px">';
    echo '<br>';
    echo '<button type="button" class="button eu-remove-image">' . esc_html__( 'Remove Image', 'elegant-umrah' ) . '</button>';
    echo '</div>';

    echo '<button type="button" class="button button-primary eu-select-image"' . ( $img_url ? ' style="display:none"' : '' ) . '>'
        . esc_html( $btn_label ) . '</button>';

    echo '</div>';
}

/**
 * Render hidden field
 */
function eu_render_hidden( array $field, string $key, $value ): void {
    echo '<input type="hidden"'
        . ' id="eu_field_' . esc_attr( $key ) . '"'
        . ' name="' . esc_attr( $key ) . '"'
        . ' value="' . esc_attr( (string) $value ) . '">';
}
