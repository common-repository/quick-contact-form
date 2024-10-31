<?php
function qcf_loop( $id ) {
	ob_start();

	$digit1 = mt_rand( 1, 10 );
	$digit2 = mt_rand( 1, 10 );
	if ( $digit2 >= $digit1 ) {
		$values['thesum'] = "$digit1 + $digit2";
		$values['answer'] = $digit1 + $digit2;
	} else {
		$values['thesum'] = "$digit1 - $digit2";
		$values['answer'] = $digit1 - $digit2;
	}
	$qcf = qcf_get_stored_options( $id );
	for ( $i = 1; $i <= 14; $i ++ ) {
		if ( isset( $qcf['label'][ 'field' . $i ] ) ) {
			$values[ 'qcfname' . $i ] = $qcf['label'][ 'field' . $i ];
		}
	}
	if ( is_user_logged_in() && isset( $qcf['showuser'] ) && $qcf['showuser'] ) {
		$current_user       = wp_get_current_user();
		$values['qcfname1'] = $current_user->user_login;
		$values['qcfname2'] = $current_user->user_email;
	}
	$values['qcfname12'] = '';

	qcf_display_form( $values, array(), $id );

	$output_string = ob_get_contents();
	ob_end_clean();

	return $output_string;
}
