<?php
/**
 * Field Core - Custom ACF-like field engine
 *
 * Registry, registration, and retrieval core
 *
 * @package ElegantUmrah\Fields
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Global field group registry
 */
global $eu_field_groups;
$eu_field_groups = [];

/**
 * Register a field group (like ACF's register_field_group)
 *
 * @param array $group {
 *     @type string $id        Unique group identifier
 *     @type string $title     Admin label for the meta box
 *     @type array  $fields    Array of field configs
 *     @type array  $location  Location rules
 *     @type string $context   'normal', 'side', 'advanced'
 *     @type string $priority  'high', 'default', 'low'
 * }
 */
function eu_register_field_group( array $group ): void {
    global $eu_field_groups;

    if ( empty( $group['id'] ) || empty( $group['fields'] ) ) {
        return;
    }

    // Defaults
    $group = wp_parse_args( $group, [
        'id'       => '',
        'title'    => __( 'Custom Fields', 'elegant-umrah' ),
        'fields'   => [],
        'location' => [],
        'context'  => 'normal',
        'priority' => 'high',
        'style'    => 'default',
    ] );

    $eu_field_groups[ $group['id'] ] = $group;
}

/**
 * Get all registered field groups
 */
function eu_get_field_groups(): array {
    global $eu_field_groups;
    return $eu_field_groups;
}

/**
 * Get a single field group by ID
 */
function eu_get_field_group( string $id ): ?array {
    global $eu_field_groups;
    return $eu_field_groups[ $id ] ?? null;
}

/**
 * Get all fields for a specific group
 */
function eu_get_group_fields( string $group_id ): array {
    $group = eu_get_field_group( $group_id );
    return $group ? $group['fields'] : [];
}

/**
 * Check if a field group location matches the current screen
 *
 * @param array $location  Location rules from field group
 * @param int   $post_id   Current post ID
 * @return bool
 */
function eu_location_matches( array $location, int $post_id ): bool {
    if ( empty( $location ) ) return true;

    $post          = get_post( $post_id );
    $post_type     = $post ? $post->post_type : '';
    $page_template = get_page_template_slug( $post_id );

    // Location is an array of rule groups (OR between groups, AND within group)
    foreach ( $location as $rule_group ) {
        $group_match = true;

        foreach ( $rule_group as $rule ) {
            $param    = $rule['param']    ?? '';
            $operator = $rule['operator'] ?? '==';
            $value    = $rule['value']    ?? '';

            $match = match ( $param ) {
                'post_type'      => eu_compare( $post_type, $operator, $value ),
                'page_template'  => eu_compare( $page_template, $operator, $value ),
                'post_status'    => eu_compare( $post ? $post->post_status : '', $operator, $value ),
                'page'           => eu_compare( (string) $post_id, $operator, (string) $value ),
                'post_category'  => eu_location_check_taxonomy( $post_id, 'category', $operator, $value ),
                default          => false,
            };

            if ( ! $match ) {
                $group_match = false;
                break;
            }
        }

        if ( $group_match ) return true;
    }

    return false;
}

/**
 * Helper: compare two values with operator
 */
function eu_compare( string $a, string $operator, string $b ): bool {
    return match ( $operator ) {
        '=='    => $a === $b,
        '!='    => $a !== $b,
        default => $a === $b,
    };
}

/**
 * Helper: check taxonomy location rule
 */
function eu_location_check_taxonomy( int $post_id, string $taxonomy, string $operator, string $value ): bool {
    $terms = wp_get_post_terms( $post_id, $taxonomy, [ 'fields' => 'slugs' ] );
    if ( is_wp_error( $terms ) ) return false;
    $has = in_array( $value, $terms, true );
    return $operator === '==' ? $has : ! $has;
}

/**
 * Initialize all meta boxes from field groups
 * Called on 'add_meta_boxes' hook
 */
function eu_init_meta_boxes(): void {
    $groups = eu_get_field_groups();

    foreach ( $groups as $group ) {
        // Determine which post types to register for
        $post_types = eu_get_post_types_from_location( $group['location'] );

        if ( empty( $post_types ) ) {
            // If no location rules, skip
            continue;
        }

        foreach ( $post_types as $pt ) {
            add_meta_box(
                'eu_field_group_' . $group['id'],
                $group['title'],
                function( WP_Post $post ) use ( $group ) {
                    eu_render_meta_box( $post, $group );
                },
                $pt,
                $group['context'],
                $group['priority']
            );
        }
    }
}
add_action( 'add_meta_boxes', 'eu_init_meta_boxes' );

/**
 * Extract post types from location rules
 */
function eu_get_post_types_from_location( array $location ): array {
    $post_types = [];

    foreach ( $location as $rule_group ) {
        foreach ( $rule_group as $rule ) {
            if ( $rule['param'] === 'post_type' && $rule['operator'] === '==' ) {
                $post_types[] = $rule['value'];
            }
            if ( $rule['param'] === 'page_template' ) {
                $post_types[] = 'page';
            }
        }
    }

    return array_unique( $post_types );
}

/**
 * Render a meta box for a field group
 */
function eu_render_meta_box( WP_Post $post, array $group ): void {
    wp_nonce_field( 'eu_save_fields_' . $group['id'], 'eu_nonce_' . $group['id'] );

    // Check location
    if ( ! eu_location_matches( $group['location'], $post->ID ) ) {
        return;
    }

    // Allow injection of notices/content before the fields render
    do_action( 'eu_before_field_group', $group['id'], $post );

    // Call the renderer
    eu_render_field_group( $post->ID, $group );
}
