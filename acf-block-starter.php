<?php
/**
 * Plugin Name:       Acf Block Starter
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       acf-block-starter
 *
 * @package           acf-block-starter
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function abs_block_init() {
	register_block_type( __DIR__ . '/build/demo-counter/block.json' );
	register_block_type( __DIR__ . '/build/demo-inner-blocks/block.json' );
}

add_action( 'init', 'abs_block_init' );


/**
 * Register a custom block category for our blocks.
 *
 * @link https://developer.wordpress.org/reference/hooks/block_categories_all/
 *
 * @param array $block_categories Existing block categories
 *
 * @return array Block categories
 */
function abs_categories( $block_categories ) {

	$block_categories = array_merge(
		[
			[
				'slug'  => 'acf-block-starter',
				'title' => __( 'ACF Blocks', 'acf-block-starter' ),
			]
		],
		$block_categories
	);

	return $block_categories;
}

add_filter( 'block_categories_all', 'abs_categories' );

function abs_get_inner_blocks( $options = [] ) {
	return '<InnerBlocks' . abs_generate_html_attributes( $options ) . ' />';
}

function abs_generate_html_attributes( $attributes ) {
	$html_attributes = '';

	foreach ( $attributes as $key => $value ) {
		if ( is_array( $value ) ) {
			$html_attributes .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( json_encode( $value ) ) );
		} else {
			$html_attributes .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}
	}

	return $html_attributes;
}
