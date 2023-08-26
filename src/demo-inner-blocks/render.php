<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<?= abs_get_inner_blocks( [
		'template' => [
			[
				'core/image',
				[
					'url' => 'https://cdn.pixabay.com/photo/2023/05/21/17/48/field-8008987_960_720.jpg',
				]
			],
			[
				'core/heading',
				[
					'level'       => 1,
					'placeholder' => 'Your title here..'
				]
			],
			[
				'core/paragraph',
				[
					'placeholder' => 'Your description here..'
				]
			]
		]
	] ) ?>
</div>

