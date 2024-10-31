<?php
function qcf_start( $atts ) {
	$atts = shortcode_atts( array( 'id' => '', 'form' => '' ), $atts );
	if ( ! empty( $atts['id'] ) ) {
		$id = $atts['id'];
	} else {
		$id = $atts['form'];
	}
	$id = preg_replace( "/[^A-Za-z]/", '', $id );

	return qcf_loop( $id );
}
