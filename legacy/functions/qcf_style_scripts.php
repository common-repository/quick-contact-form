<?php
function qcf_style_scripts() {

	$qcf_form = qcf_get_stored_setup();
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( "jquery-effects-core" );
	wp_enqueue_script( 'qcf_script', QUICK_CONTACT_FORM_PLUGIN_URL . 'legacy/js/scripts.js', array(
		'jquery',
		'jquery-ui-datepicker',
		'jquery-effects-core'
	), null, true );
	wp_add_inline_script( 'qcf_script', 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";' );
	wp_enqueue_script( 'qcf_slider', QUICK_CONTACT_FORM_PLUGIN_URL . 'legacy/js/slider.js', array( 'jquery' ), null, true );
	if ( ! $qcf_form['nostyling'] ) {
		wp_enqueue_style( 'qcf_style', QUICK_CONTACT_FORM_PLUGIN_URL . 'legacy/css/styles.css' );

		wp_add_inline_style( 'qcf_style', qcf_generate_css() );

	}
	if ( ! $qcf_form['noui'] ) {
		wp_enqueue_style( 'jquery-style', QUICK_CONTACT_FORM_PLUGIN_URL . 'ui/user/css/jquery/jquery-ui.min.css' );
	}
}


