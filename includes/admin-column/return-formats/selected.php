<?php

defined( 'ABSPATH' ) or die();

class ACFist_Admin_Column_Return_Formats_Selected extends ACFist_Admin_Column_Return_Formats {

    public function get_field_types() {
        return [ 'select', 'checkbox', 'radio', 'button_group', 'true_false' ];
    }

    public function get_name() {
        return 'selected';
    }

    public function get_label() {
        return __( 'Selected', 'acfist' );
    }

    public function add_fields( $field ) {
        $option_key = ACFist_Admin_Column::OPTION_KEY;
        $condition = [
            'field' => "{$option_key}_return_format",
            'operator' => '==',
            'value' => $this->get_name(),
        ];

        acf_render_field_setting( $field, [
            'label' => __( 'Type', 'acfist' ),
            'instructions' => __( 'Admin column', 'acfist' ),
            'name' => "{$option_key}_{$this->get_name()}_type",
            'type' => 'radio',
            'choices' => [
                'value' => __( 'Value', 'acfist' ),
                'label' => __( 'Label', 'acfist' ),
            ],
            'default_value' => 'label',
            'layout' => 'horizontal',
            'conditions' => $condition,
        ] );

        if ( ! in_array( $field['type'], [ 'select', 'checkbox' ], true ) ) {
            $condition['value'] = 'acfist-undefined';
        }

        acf_render_field_setting( $field, [
            'label' => __( 'Separator', 'acfist' ),
            'instructions' => __( 'Admin column', 'acfist' ),
            'name' => "{$option_key}_{$this->get_name()}_separator",
            'type' => 'text',
            'default_value' => ', &nbsp;',
            'conditions' => $condition,
        ] );
    }

    public static function output( $value, $post_id, $field, $option_key ) {
        $output = [];
        $values = (array) $value;

        if ( 'true_false' === $field['type'] ) {
            return self::get_true_false_output( $option_key, $value, $field );
        }

        foreach ( $values as $value ) {
            if ( 'label' === $field["{$option_key}_selected_type"] ) {
                $output[] = $field['choices'][$value];
                continue;
            }

            $output[] = $value;
        }

        return implode( $field["{$option_key}_selected_separator"], $output );
    }

    private static function get_true_false_output( $option_key, $value, $field ) {
        $output = $value;

        if ( 'value' === $field["{$option_key}_selected_type"] ) {
            return $output;
        }

        if ( 1 === $field['ui'] && 1 == $value ) {
            $output = ! empty( $field['ui_on_text'] ) ? $field['ui_on_text'] : __( 'Yes', 'acfist' );
        }

        if ( 1 === $field['ui'] && 0 == $value ) {
            $output = ! empty( $field['ui_off_text'] ) ? $field['ui_off_text'] : __( 'No', 'acfist' );
        }

        return $output;
    }
}

new ACFist_Admin_Column_Return_Formats_Selected();
