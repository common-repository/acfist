<?php

defined( 'ABSPATH' ) or die();

class ACFist_Admin_Column {

    const OPTION_KEY = 'acfist_ac';

    private $fields = [];

    public function __construct() {
        acfist()->load_file( 'admin-column/functions' );

        $this->add_return_formats();
        $this->add_hooks();
    }

    public function add_return_formats() {
        $return_formats = [ 'base', 'image', 'selected', 'truncate', 'word-count' ];

        foreach ( $return_formats as $return_format ) {
            acfist()->load_file( "admin-column/return-formats/{$return_format}" );
        }
    }

    public function add_hooks() {
        foreach ( $this->get_field_types() as $field ) {
            add_action( "acf/render_field_settings/type={$field}", [ $this, 'extend_settings' ] );
        }

        add_action( 'load-edit.php', [ $this, 'prepare_columns' ] );
    }

    public function prepare_columns() {
        $current_screen = get_current_screen();

        if ( empty( $current_screen ) ) {
            return;
        }

        $post_type = $current_screen->post_type;

        if ( empty( $post_type ) ) {
            return;
        }

        $groups = acf_get_field_groups( [ 'post_type' => $post_type ] );
        $fields = [];

        if ( empty( $groups ) ) {
            return;
        }

        foreach ( $groups as $group ) {
            $fields = array_merge( $fields, acf_get_fields( $group['key'] ) );
        }

        foreach ( $fields as $field ) {
            if ( empty( $field[ self::OPTION_KEY ] ) ) {
                continue;
            }

            $this->fields[ $field['key'] ] = $field;
        }

        add_filter( "manage_{$post_type}_posts_columns", [ $this, 'add_columns' ] );
        add_action( "manage_{$post_type}_posts_custom_column", [ $this, 'add_columns_value' ], 10, 2 );
    }

    public function add_columns( $columns ) {
        foreach ( $this->fields as $field ) {
            $value = ! empty( $field['acfist_admin_column_label'] ) ? $field['acfist_admin_column_label'] : $field['label'];
            $columns = array_merge( $columns, [ $field['key'] => $value ] );
        }

        return $columns;
    }

    public function add_columns_value( $column_key, $post_id ) {
        $format_value = true;

        if ( ! empty( $this->fields[ $column_key ][ self::OPTION_KEY . '_return_format' ] ) ) {
            add_filter( 'acf/load_value', 'acfist_format_value', 10, 3 );
            $format_value = false;
        }

        the_field( $this->fields[ $column_key ]['key'], false, $format_value );
    }

    public function extend_settings( $field ) {
        acf_render_field_setting( $field, [
            'label' => __( 'Admin Column', 'acfist' ),
            'type' => 'true_false',
            'name' => self::OPTION_KEY,
            'ui' => 1,
        ] );

        $condition = [
            'field' => self::OPTION_KEY,
            'operator' => '==',
            'value' => 1,
        ];

        acf_render_field_setting( $field, [
            'label' => __( 'Label', 'acfist' ),
            'instructions' => __( 'Admin column', 'acfist' ),
            'placeholder' => __( 'Field Label (default)', 'acfist' ),
            'type' => 'text',
            'name' => self::OPTION_KEY . '_label',
            'conditions' => $condition,
        ] );

        acf_render_field_setting( $field, [
            'label' => __( 'Return Format', 'acfist' ),
            'instructions' => __( 'Admin column', 'acfist' ),
            'type' => 'select',
            'name' => self::OPTION_KEY . '_return_format',
            'choices' => $this->get_return_format_options( $field ),
            'conditions' => $condition,
        ] );

        do_action( 'acfist/admin_columns/fields', $field );
    }

    private function get_return_format_options( $field ) {
        $options = [
            'global' => [
                '' => __( 'Field Value (default)', 'acfist' ),
            ],
        ];

        foreach ( $this->get_field_types() as $type ) {
            $options[ $type ] = [];
        }

        $options = apply_filters( 'acfist/admin_columns/fields/return_format_options', $options, $field );

        $options = array_merge( $options['global'], $options[ $field['type'] ] );

        return $options;
    }

    private function get_field_types() {
        $types = acf_get_field_types();

        $valid_types = array_diff(
            array_keys( $types ),
            [ 'accordion', 'message', 'tab' ]
        );

        $valid_types = apply_filters( 'acfist/admin_column/field_types', $valid_types, $types );

        return $valid_types;
    }
}

new ACFist_Admin_Column();
