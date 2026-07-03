<?php
/**
 * Field Types Definition
 *
 * Defines the schema for each supported field type
 *
 * @package ElegantUmrah\Fields
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Supported field types and their defaults
 */
function eu_get_field_type_defaults( string $type ): array {
    $defaults = [
        // Text input
        'text' => [
            'type'         => 'text',
            'label'        => '',
            'name'         => '',
            'placeholder'  => '',
            'default'      => '',
            'instructions' => '',
            'required'     => false,
            'maxlength'    => '',
            'prepend'      => '',
            'append'       => '',
            'class'        => '',
            'width'        => '50',
        ],

        // Textarea
        'textarea' => [
            'type'         => 'textarea',
            'label'        => '',
            'name'         => '',
            'placeholder'  => '',
            'default'      => '',
            'instructions' => '',
            'required'     => false,
            'rows'         => 4,
            'class'        => '',
            'width'        => '100',
        ],

        // Number
        'number' => [
            'type'         => 'number',
            'label'        => '',
            'name'         => '',
            'placeholder'  => '',
            'default'      => '',
            'instructions' => '',
            'required'     => false,
            'min'          => '',
            'max'          => '',
            'step'         => '1',
            'prepend'      => '',
            'append'       => '',
            'width'        => '50',
        ],

        // Date
        'date' => [
            'type'         => 'date',
            'label'        => '',
            'name'         => '',
            'default'      => '',
            'instructions' => '',
            'required'     => false,
            'min'          => '',
            'max'          => '',
            'width'        => '50',
        ],

        // URL
        'url' => [
            'type'         => 'url',
            'label'        => '',
            'name'         => '',
            'placeholder'  => 'https://',
            'default'      => '',
            'instructions' => '',
            'required'     => false,
            'width'        => '50',
        ],

        // Email
        'email' => [
            'type'         => 'email',
            'label'        => '',
            'name'         => '',
            'placeholder'  => '',
            'default'      => '',
            'instructions' => '',
            'required'     => false,
            'width'        => '50',
        ],

        // Select/Dropdown
        'select' => [
            'type'          => 'select',
            'label'         => '',
            'name'          => '',
            'default'       => '',
            'instructions'  => '',
            'required'      => false,
            'choices'       => [],   // [ 'value' => 'Label' ]
            'allow_null'    => true,
            'null_label'    => '— Select —',
            'multiple'      => false,
            'class'         => '',
            'width'         => '50',
        ],

        // Radio buttons
        'radio' => [
            'type'         => 'radio',
            'label'        => '',
            'name'         => '',
            'default'      => '',
            'instructions' => '',
            'required'     => false,
            'choices'      => [],
            'layout'       => 'horizontal', // horizontal|vertical
            'width'        => '50',
        ],

        // Checkbox
        'checkbox' => [
            'type'         => 'checkbox',
            'label'        => '',
            'name'         => '',
            'default'      => false,
            'instructions' => '',
            'label_on'     => __( 'Yes', 'elegant-umrah' ),
            'label_off'    => __( 'No', 'elegant-umrah' ),
            'width'        => '50',
        ],

        // Image (attachment ID)
        'image' => [
            'type'          => 'image',
            'label'         => '',
            'name'          => '',
            'default'       => '',
            'instructions'  => '',
            'required'      => false,
            'return_format' => 'id', // id|url|array
            'preview_size'  => 'thumbnail',
            'button_label'  => __( 'Select Image', 'elegant-umrah' ),
            'width'         => '100',
        ],

        // Color Picker
        'color' => [
            'type'         => 'color',
            'label'        => '',
            'name'         => '',
            'default'      => '#D4AF37',
            'instructions' => '',
            'required'     => false,
            'width'        => '25',
        ],

        // Hidden (internal use)
        'hidden' => [
            'type'  => 'hidden',
            'label' => '',
            'name'  => '',
        ],
    ];

    return $defaults[ $type ] ?? $defaults['text'];
}

/**
 * Build a field config with defaults filled in
 *
 * @param array $field  Partial field config
 * @return array        Complete field config
 */
function eu_build_field( array $field ): array {
    $type     = $field['type'] ?? 'text';
    $defaults = eu_get_field_type_defaults( $type );

    return wp_parse_args( $field, $defaults );
}

/**
 * Sanitize a field value before saving
 *
 * @param  mixed  $value  Raw value from $_POST
 * @param  array  $field  Field config
 * @return mixed          Sanitized value
 */
function eu_sanitize_field_value( $value, array $field ) {
    $type = $field['type'] ?? 'text';

    return match ( $type ) {
        'text', 'url', 'email', 'color' => sanitize_text_field( $value ),
        'textarea'                       => sanitize_textarea_field( $value ),
        'number'                         => is_numeric( $value ) ? (float) $value : 0,
        'date'                           => sanitize_text_field( $value ),
        'select', 'radio'                => sanitize_text_field( $value ),
        'checkbox'                       => (bool) $value,
        'image'                          => absint( $value ),
        'hidden'                         => sanitize_text_field( $value ),
        default                          => sanitize_text_field( $value ),
    };
}
