<?php

defined( 'ABSPATH' ) or die();

class ACFist_Admin_Column_Return_Formats_Truncate extends ACFist_Admin_Column_Return_Formats {

    public function get_field_types() {
        return [ 'text', 'textarea', 'email', 'url', 'password', 'wysiwyg' ];
    }

    public function get_name() {
        return 'truncate';
    }

    public function get_label() {
        return __( 'Truncate', 'acfist' );
    }

    public function add_fields( $field ) {
        $option_key = ACFist_Admin_Column::OPTION_KEY;
        $condition = [
            'field' => "{$option_key}_return_format",
            'operator' => '==',
            'value' => $this->get_name(),
        ];

        acf_render_field_setting( $field, [
            'label' => __( 'Start', 'acfist' ),
            'instructions' => __( 'Admin column', 'acfist' ),
            'placeholder' => 0,
            'default_value' => 0,
            'type' => 'number',
            'required' => true,
            'name' => "{$option_key}_{$this->get_name()}_start",
            'conditions' => $condition,
        ] );

        acf_render_field_setting( $field, [
            'label' => __( 'Width', 'acfist' ),
            'instructions' => __( 'Admin column', 'acfist' ),
            'placeholder' => 50,
            'default_value' => 50,
            'type' => 'number',
            'required' => true,
            'name' => "{$option_key}_{$this->get_name()}_width",
            'conditions' => $condition,
        ] );

        acf_render_field_setting( $field, [
            'label' => __( 'Trim Maker', 'acfist' ),
            'instructions' => __( 'Admin column', 'acfist' ),
            'placeholder' => '...',
            'default_value' => '...',
            'type' => 'text',
            'name' => "{$option_key}_{$this->get_name()}_trim_maker",
            'conditions' => $condition,
        ] );
    }

    public static function output( $value, $post_id, $field, $option_key ) {
        return mb_strimwidth(
            $value,
            $field["{$option_key}_truncate_start"],
            $field["{$option_key}_truncate_width"],
            $field["{$option_key}_truncate_trim_maker"]
        );
    }
}

new ACFist_Admin_Column_Return_Formats_Truncate();
