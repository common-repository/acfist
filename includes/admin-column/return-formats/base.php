<?php

defined( 'ABSPATH' ) or die();

abstract class ACFist_Admin_Column_Return_Formats {

    public function __construct() {
        $this->add_hooks();
    }

    public function add_hooks() {
        add_action( 'acfist/admin_columns/fields', [ $this, 'add_fields' ] );
        add_filter( 'acfist/admin_columns/fields/return_format_options', [ $this, 'add_return_format' ] );
    }

    public function get_field_types() {}

    public function get_name() {}

    public function get_label() {}

    public function add_fields( $field ) {}

    public function add_return_format( $options ) {
        foreach ( $this->get_field_types() as $field_type ) {
            $options[ $field_type ] = array_merge(
                $options[ $field_type ],
                [ $this->get_name() => $this->get_label() ]
            );
        }

        return $options;
    }

    public static function render( $value, $post_id, $field ) {
        $option_key = ACFist_Admin_Column::OPTION_KEY;
        $hook = "acfist/admin_column/return_formats/{$field[ $option_key . '_return_format']}/output";
        $output = static::output( $value, $post_id, $field, $option_key );

        $output = apply_filters( $hook, $output, $value, $post_id, $field );

        do_action( $hook, $output, $value, $post_id, $field );

        return $output;
    }

    public static function output( $value, $post_id, $field, $option_key ) {}
}

