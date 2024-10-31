<?php
function qcf_block_init() {

	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	wp_register_script(
		'qcf_block',
		QUICK_CONTACT_FORM_PLUGIN_URL . 'legacy/js/block.js',
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' )
	);

	register_block_type(
		'quick-contact-form/block', array(
			'editor_script'   => 'qcf_block',
			'render_callback' => function( $attributes, $content, $block ) {
				return qcf_loop( '' );
			},
		)
	);
}