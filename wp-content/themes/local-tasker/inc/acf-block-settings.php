<?php
function generate_block_settings_classnames(){
    $block_style = $class_name = '';
    // Spacing Top and Bottom
    $spacing_top = get_field( 'spacing_top' );
    $spacing_bottom = get_field( 'spacing_bottom' );


    switch( $spacing_top ){
        case 'small':
            $class_name .= ' padding-top-sm';
            break;
        case 'medium':
            $class_name .= ' padding-top-md';
            break;
        case 'large':
            $class_name .= ' padding-top-lg';
            break;
        default:
            break;
    }

    switch( $spacing_bottom ){
        case 'small':
            $class_name .= ' padding-bottom-sm';
            break;
        case 'medium':
            $class_name .= ' padding-bottom-md';
            break;
        case 'large':
            $class_name .= ' padding-bottom-lg';
            break;
        default:
            break;
    }

    return array(
        'class_name' => $class_name,
        'block_style' => $block_style
    );
}