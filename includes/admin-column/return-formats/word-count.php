<?php

defined( 'ABSPATH' ) or die();

class ACFist_Admin_Column_Return_Formats_Word_Count extends ACFist_Admin_Column_Return_Formats {

    private static $instance;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get_field_types() {
        return [ 'text', 'textarea', 'wysiwyg' ];
    }

    public function get_name() {
        return 'word_count';
    }

    public function get_label() {
        return __( 'Word Count', 'acfist' );
    }

    public static function output( $value, $post_id, $field, $option_key ) {
        return str_word_count( $value );
    }
}

new ACFist_Admin_Column_Return_Formats_Word_Count();
