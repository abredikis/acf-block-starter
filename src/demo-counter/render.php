<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<div class="wp-block-acf-block-starter-demo-counter__counter">
		<p>Count: <span id="count">0</span></p>
		<button id="incrementBtn">Increment</button>
	</div>
</div>

