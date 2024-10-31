<?php
function qcf_redirect_by_email( $id, $option ) {
	$qcf    = qcf_get_stored_options( $id );
	$emails = qcf_get_stored_emails( $id );
	$arr    = explode( ",", $qcf['dropdownlist'] );
	foreach ( $arr as $item ) {
		if ( $option == $item ) {
			$option = str_replace( ' ', '_', $option );

			return $emails[ $option ];
		}
	}
}
