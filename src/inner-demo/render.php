<?php

use ACFBlockStarter\Blocks;

$instance = Blocks::getIntance();
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'inner-demo' ] ); ?>>
	<div class="inner-demo-wrapper">
		<?= $instance->getInnerBlocks( [
			'template' => [
				[
					'core/cover',
					[
						'url' => 'https://cdn.pixabay.com/photo/2023/05/21/17/48/field-8008987_960_720.jpg',
						'dimRatio' => 50
					],
					[
						[
							'core/heading',
							[
								'level'   => 1,
								'content' => 'Inner Demo'
							]
						],
					]
				],
				[
					'core/group',
					[],
					[
						[
							'core/paragraph',
							[
								'placeholder' => 'Your description here..'
							]
						]
					]
				]
			]
		] ) ?>
	</div>
</section>

