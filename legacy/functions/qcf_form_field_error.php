<?php
function qcf_form_field_error( $errors, $key, $content ) {
	$required = '';
	if ( isset( $errors[ $key ] ) && $errors[ $key ] ) {
		$required = 'error';
	}
	if ( isset( $errors[ $key ] ) ) {
		$content .= $errors[ $key ];
	}

	return array( $required, $content );
}