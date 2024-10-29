<?php

defined( 'ABSPATH' ) or die();

class ACFist_Admin_Column_Return_Formats_Image extends ACFist_Admin_Column_Return_Formats {

    public function get_field_types() {
        return [ 'image' ];
    }

    public function get_name() {
        return 'image';
    }

    public function get_label() {
        return __( 'Image', 'acfist' );
    }

    public static function output( $value, $post_id, $field, $option_key ) {
        $output = '';
        $src = wp_get_attachment_url( $value );

        if ( ! empty( $src ) ) {
            $output = "<img src='{$src}' style='max-width: 100px;' />";
        }

        return $output;
    }
}

new ACFist_Admin_Column_Return_Formats_Image();
