<?php
function qcf_redirect_by_selection( $id, $values ) {
	$qcf          = qcf_get_stored_options( $id );
	$qcf_redirect = qcf_get_stored_redirect( $id );
	if ( $qcf_redirect['whichlist'] == 'dropdownlist' ) {
		$choice = $values['qcfname5'];
	}
	if ( $qcf_redirect['whichlist'] == 'radiolist' ) {
		$choice = $values['qcfname7'];
	}
	$arr = explode( ",", $qcf[ $qcf_redirect['whichlist'] ] );
	foreach ( $arr as $item ) {
		if ( $choice == $item ) {
			$choice = str_replace( ' ', '_', $choice );

			return $qcf_redirect[ $choice ];
		}
	}
}
