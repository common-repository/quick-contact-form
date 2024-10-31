<?php
function qcf_create_user( $values ) {
	$user_name  = $values['qcfname1'];
	$user_email = $values['qcfname2'];
	$user_id    = username_exists( $user_name );
	if ( ! $user_id and email_exists( $user_email ) == false and $user_name and $user_email ) {
		$password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
		$user_id  = wp_create_user( $user_name, $password, $user_email );
		wp_update_user( array( 'ID' => $user_id, 'role' => 'subscriber' ) );
		wp_new_user_notification( $user_id, $notify = 'both' );
	}
}
