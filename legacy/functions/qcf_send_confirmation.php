<?php
function qcf_send_confirmation( $values, $content, $id, $qcf_email ) {
	$auto = qcf_get_stored_autoresponder( $id );

	if ( empty( $auto['fromemail'] ) ) {
		$auto['fromemail'] = $qcf_email;
	}
	if ( empty( $auto['fromname'] ) ) {
		$auto['fromname'] = get_bloginfo( 'name' );
	}

	$headers = 'From: "' . $auto['fromname'] . '" <' . $auto['fromemail'] . '>' . "\r\n"
	           . "MIME-Version: 1.0\r\n"
	           . "Content-Type: text/html; charset=\"utf-8\"\r\n";
	$subject = $auto['subject'];
	$message = '<html>' . $auto['message'];
	$message = str_replace( '[name]', $values['qcfname1'], $message );
	$message = str_replace( '[date]', $values['qcfname10'], $message );
	$message = str_replace( '[option]', $values['qcfname5'], $message );

	if ( $auto['sendcopy'] ) {
		$message .= $content;
	}
	$message .= '<html>';
	qcf_wp_mail( 'Confirmation', $values['qcfname2'], $subject, $message, $headers );
}
