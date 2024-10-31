<?php
function qcf_wp_mail( $type, $qcp_email, $title, $content, $headers, $attachments = null ) {
	add_action( 'wp_mail_failed', function ( $wp_error ) {
		/**  @var $wp_error \WP_Error */
		if ( defined( 'WP_DEBUG' ) && true == WP_DEBUG && is_wp_error( $wp_error ) ) {
			trigger_error( 'QCF Email - wp_mail error msg : ' . $wp_error->get_error_message(), E_USER_WARNING );
		}
	}, 10, 1 );
	if ( defined( 'WP_DEBUG' ) && true == WP_DEBUG ) {
		trigger_error( 'QCF Email message about to send: ' . $type . ' To: ' . $qcp_email, E_USER_NOTICE );
	}
	$res = wp_mail( $qcp_email, $title, $content, $headers, $attachments );
	if ( defined( 'WP_DEBUG' ) && true == WP_DEBUG ) {
		if ( true === $res ) {
			trigger_error( 'QCF Email - wp_mail responded OK : ' . $type . ' To: ' . $qcp_email, E_USER_NOTICE );
		} else {
			trigger_error( 'QCF Email - wp_mail responded FAILED to send : ' . $type . ' To: ' . $qcp_email, E_USER_WARNING );
		}
	}
}
