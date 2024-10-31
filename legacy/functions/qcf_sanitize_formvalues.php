<?php
function qcf_sanitize_formvalues( $form ) {
	$form_data      = array(
		'id'        => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname1'  => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname2'  => array( 'sanitize' => 'sanitize_email', 'default' => '' ),
		'qcfname3'  => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname4'  => array( 'sanitize' => 'sanitize_textarea_field', 'default' => '' ),
		'qcfname5'  => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname6'  => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname7'  => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname8'  => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname9'  => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname10' => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname11' => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname12' => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname13' => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname14' => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'qcfname15' => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'action'    => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'thesum'    => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'answer'    => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
		'form_id'   => array( 'sanitize' => 'sanitize_text_field', 'default' => '' ),
	);
	$sanitized_form = array();
	// sanitize
	foreach ( $form as $key => $value ) {
		if ( isset( $form_data[ $key ] ) ) {
			$sanitized_form[ $key ] = call_user_func( $form_data[ $key ]['sanitize'], $value );
		} else if ( preg_match( "/^qcfname[5-7]_.+$/", $key ) ) {
			$sanitized_form[ $key ] = call_user_func( "sanitize_text_field", $value );
		}
	}
	// set default
	foreach ( $form_data as $key => $value ) {
		if ( ! isset( $sanitized_form[ $key ] ) ) {
    		$sanitized_form[ $key ] = $value['default'];
    	}
	}

	return $sanitized_form;
}