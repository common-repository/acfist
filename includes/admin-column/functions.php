<?php

function acfist_format_value( $value, $post_id, $field ) {
    $classname = 'ACFist_Admin_Column_Return_Formats_' . ucfirst( $field['acfist_ac_return_format'] );

    if ( class_exists( $classname ) ) {
        $value = $classname::render( $value, $post_id, $field );
    }

    return $value;
}
