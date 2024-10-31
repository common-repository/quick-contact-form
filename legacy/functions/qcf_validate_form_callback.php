<?php
function qcf_validate_form_callback() {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- user submitted form anti spam measure may be employed
	$formvalues = qcf_sanitize_formvalues( $_POST );
	$formerrors = array();
	$json       = (object) array(
		'success' => false,
		'errors'  => array(),
		'display' => '',
	);
	if ( isset( $formvalues['id'] ) ) {
		$id = $formvalues['id'];
	} else {
		echo wp_json_encode( $json );
	}
	if ( ! qcf_verify_form( $formvalues, $formerrors, $id ) ) {
		$error         = qcf_get_stored_error( $id );
		$json->display = $error['errortitle'];
		$json->blurb   = $error['errorblurb'];
		/* Format Form Errors */
		foreach ( $formerrors as $k => $v ) {
			$json->errors[] = (object) array(
				'name'  => $k,
				'error' => $v,
			);
		}
	} else {
		$json->success = true;
		ob_start();
		qcf_process_form( $formvalues, $id );
		$json->display = ob_get_clean();
	}
	echo wp_json_encode( $json );
	wp_die();
}
