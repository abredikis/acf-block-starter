<?php
define( 'DEFAULT_IMAGE_ARGS', [
	'size' => 'medium'
] );

add_action( 'acf/init', 'init_lazy_constants' );
function init_lazy_constants() {
	if ( ! defined( 'DEFAULT_IMAGE' ) ) {
		if($default_image_id = get_field( 'default_image', 'options' )){
			define( 'DEFAULT_IMAGE', get_field( 'default_image', 'options' ) ?? '' );
		}
	}
}

function lazy( $image_id = false, $custom_args = [] ): string {
	$use_fallback = false;
	$args         = wp_parse_args( $custom_args, DEFAULT_IMAGE_ARGS ?? [] );


	if ( ! wp_attachment_is_image( $image_id ) && ! wp_attachment_is_image( DEFAULT_IMAGE ?? '' ) ) {
		return '';
	}

	if ( ! $image_id ) {
		$use_fallback = true;
	}

	$size = $args['size'];
	unset( $args['size'] );

	return wp_get_attachment_image( $use_fallback ? DEFAULT_IMAGE : $image_id, $size, false, $args );
}
