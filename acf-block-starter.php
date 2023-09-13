<?php
/**
 * Plugin Name:       ACF Block Starter
 * Description:       Custom ACF blocks
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Alberts Bredikis
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       acf-blocks
 *
 * @package           acf-block-starter
 */

namespace ACFBlockStarter;

define( 'BLOCKS_DIR_URI', plugin_dir_url( __FILE__ ) );
define( 'BLOCKS_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'BLOCKS_NAMESPACE', 'acf-block-starter' );
define( 'BLOCKS_BUILD_URI', BLOCKS_DIR_URI . 'build/' );
define( 'BLOCKS_TEMPLATE_PATH', BLOCKS_DIR_PATH . 'templates/' );
define( 'BLOCKS_CORE_PREFIX', 'core/' );

require_once BLOCKS_DIR_PATH . 'inc/acf.php';
require_once BLOCKS_DIR_PATH . 'inc/helpers.php';

class Blocks {
	private static $instance;
	private $template_blocks = [];
	private $block_handles = [];

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'registerBlocks' ) );
		add_action( 'admin_init', array( $this, 'removeCoreBlockStyles' ) );
		add_action( 'admin_init', array( $this, 'addCoreBlockStyles' ) );
		add_filter( 'block_categories_all', array( $this, 'addBlockCategories' ) );
		add_action( 'enqueue_block_assets', array( $this, 'setTemplateBlocks' ) );
		add_action( 'enqueue_block_assets', array( $this, 'enqueueBlockAssets' ) );
	}

	public function init() {
		$this->manifest = $this->getBlockManifest();
		remove_theme_support( 'core-block-patterns' );
		add_filter( 'styles_inline_size_limit', '__return_zero' );
	}

	/**
	 *
	 * Extracts block handles from the current page template, for example index.html or single-product.html
	 *
	 * @return void
	 */
	public function setTemplateBlocks() {
		global $_wp_current_template_content;
		$template_content = $_wp_current_template_content;
		$template_blocks  = [];

		$blocks = parse_blocks( $template_content );
		foreach ( $blocks as $block ) {
			$blockNames      = $this->getBlockNames( $block );
			$template_blocks = array_unique( array_merge( $blockNames, $template_blocks ) );
		}
		$this->template_blocks = $template_blocks;
	}


	/**
	 * @param $block_name
	 *
	 * Checks if block is inside the current page content or is a block that was found inside the current page template
	 *
	 * @return bool
	 */
	public function hasBlock( $block_name ) {
		if ( has_block( $block_name ) ) {
			return true;
		}

		return in_array( $block_name, $this->template_blocks );
	}

	/**
	 * @param $blockObject
	 *
	 * Returns block names that are inside the blockObject
	 *
	 * @return array
	 */
	public function getBlockNames( $blockObject, $include_core = false ) {
		$blockNames = [];

		if ( str_starts_with( $blockObject['blockName'], $include_core ?: BLOCKS_CORE_PREFIX ) ) {
			return $blockNames;
		}

		if ( ! in_array( $blockObject['blockName'], $blockNames ) ) {
			$blockNames[] = $blockObject['blockName'];
		}
		if ( ! empty( $blockObject['innerBlocks'] ) ) {
			foreach ( $blockObject['innerBlocks'] as $innerBlock ) {
				$innerBlockNames = $this->getBlockNames( $innerBlock );
				$blockNames      = array_unique( array_merge( $blockNames, $innerBlockNames ) );
			}
		}

		return $blockNames;
	}

	/**
	 * Registers block directories located in the /build directory and registers the assets associated with each block from the webpack manifest.
	 */
	public function registerBlocks() {
		$manifest = $this->getBlockManifest();
		$entries  = $manifest['entrypoints'] ?? [];

		if ( ! empty( $entries ) && $blocks = $this->getBlocks() ):foreach ( $blocks as $block_path ):

			$block_name = basename( $block_path );

			if ( $block_name === 'scripts' || $block_name === 'styles' ) {
				continue;
			}

			$block_script_handles = [ 'app' ];
			$block_style_handles  = [ 'app' ];
			$entry_name           = $block_name . '/index';

			if ( $entries[ $entry_name ]['assets']['js'] ?? false ):foreach ( $entries[ $entry_name ]['assets']['js'] as $key => $js_file ):
				$handle = generate_block_asset_handle( BLOCKS_NAMESPACE . '/' . $block_name, 'script', $key );

				if ( $key === 0 ) {
					$handle = 'runtime';
				}

				$block_script_handles[] = $handle;

				if ( ! wp_script_is( $handle, 'registered' ) ) {
					wp_register_script( $handle, BLOCKS_BUILD_URI . $js_file, [], '1.0' );
				}
			endforeach;endif;

			if ( $entries[ $entry_name ]['assets']['css'] ?? false ):foreach ( $entries[ $entry_name ]['assets']['css'] as $key => $css_file ):
				$handle                = generate_block_asset_handle( BLOCKS_NAMESPACE . '/' . $block_name, 'style', $key );
				$block_style_handles[] = $handle;
				if ( ! wp_style_is( $handle, 'registered' ) ) {
					wp_register_style( $handle, BLOCKS_BUILD_URI . $css_file, [], '1.0' );
				}
			endforeach;endif;

			$this->setBlockHandles( $block_name, [
					'script' => $block_script_handles,
					'style'  => $block_style_handles
				]
			);

			register_block_type( __DIR__ . '/build/' . $block_name . '/block.json', [
				'style'  => json_encode( $block_style_handles ),
				'script' => json_encode( $block_script_handles )
			] );

		endforeach;endif;
	}

	/**
	 * Enqueues assets for every block present on the current page.
	 */
	public function enqueueBlockAssets() {
		$blocks = $this->getBlocks();

		if ( ! empty( $blocks ) ):foreach ( $blocks as $block_path ):
			$block_name = basename( $block_path );

			if ( ! $this->hasBlock( BLOCKS_NAMESPACE . '/' . $block_name ) ) {
				continue;
			}

			$asset_handles = $this->getBlockHandles( $block_name );


			if ( ! empty( $asset_handles ) ):

				if ( $handles = $asset_handles['script'] ?? [] ):foreach ( $handles as $handle ):
					wp_enqueue_script( $handle );
				endforeach;endif;

				if ( $handles = $asset_handles['style'] ?? [] ):foreach ( $handles as $handle ):
					wp_enqueue_style( $handle );
				endforeach;endif;
			endif;
		endforeach;endif;
	}

	/**
	 * Sets script and style handles for a specific block, which are later used when enqueuing block assets.
	 */
	public function setBlockHandles( $block_name, $handles = [] ) {
		$this->block_handles[ $block_name ] = $handles;
	}

	public function getBlockHandles( $block_name ) {
		return $this->block_handles[ $block_name ] ?? [];
	}

	/**
	 *
	 * Enqueues script that is responsible for adding custom block styles
	 *
	 * @return void
	 */
	public function addCoreBlockStyles() {
		wp_enqueue_script( 'add-block-style', plugins_url( 'inc/admin/add-block-styles.js', __FILE__ ), [
			'wp-blocks',
			'wp-edit-post'
		] );
	}

	/**
	 *
	 * Enqueues script that is responsible for removing existing block styles
	 *
	 * @return void
	 */
	public function removeCoreBlockStyles() {
		wp_enqueue_script( 'remove-block-style', plugins_url( 'inc/admin/remove-block-styles.js', __FILE__ ), [
			'wp-blocks',
			'wp-edit-post'
		] );
	}

	/**
	 *
	 * Returns names of all directories found inside the build directory (Should be only block directories), excluding styles and script directory
	 *
	 * @return array|false
	 */
	public function getBlocks() {
		$block_dir = BLOCKS_DIR_PATH . 'build/';

		return array_filter( glob( $block_dir . '*', GLOB_ONLYDIR ), function ( $directory ) {
			return ! in_array( basename( $directory ), array( 'styles', 'scripts' ) );
		} );
	}

	/**
	 *
	 * Returns an instance of the class
	 *
	 * @return self
	 */
	public static function getIntance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 *
	 * Returns the webpack manifest as an array
	 *
	 * @return array|mixed
	 */
	public function getBlockManifest() {
		return json_decode( file_get_contents( BLOCKS_DIR_PATH . 'build/manifest.json' ), true ) ?? [];
	}


	/**
	 * @param $categories
	 *
	 * Adds block categories to the site
	 *
	 * @return array|\string[][]
	 */
	public function addBlockCategories( $categories ) {
		$categories = array_merge(
			[
				[
					'slug'  => 'acf-block-starter',
					'title' => __( 'ACF Block Starter', 'acf-block-starter' ),
				]
			],
			$categories
		);

		return $categories;
	}

	public function getInnerBlocks( $options = [] ) {
		return '<InnerBlocks' . $this->generateHtmlAttributes( $options ) . ' />';
	}

	public function generateHtmlAttributes( $attributes ) {
		$html_attributes = '';

		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $key => $value ) {
				if ( is_array( $value ) ) {
					$html_attributes .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( json_encode( $value ) ) );
				} else {
					$html_attributes .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
				}
			}
		}

		return $html_attributes;
	}
}

Blocks::getIntance();
