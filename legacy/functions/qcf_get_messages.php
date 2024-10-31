<?php
function qcf_get_messages( $id, $type = 'all' ) {
	$data = qcf_get_messages_data( $id );
	if ( 'all' === $type ) {
		// add id to each row
		foreach ( $data as $key => $value ) {
			$data[ $key ]['id'] = $key;
		}
		return $data;
	} else {
		return qcf_get_messages_by_type( $data, $type );
	}
}

function qcf_get_messages_by_type( $data, $type ) {
	$messages = array();
	foreach ( $data as $key => $value ) {
		if ( $value['type'] === $type ) {
			$value['id'] = $key;
			$messages[] = $value;
		}
	}
	return $messages;
}

function qcf_get_messages_data( $id ) {
	$message = get_option( 'qcf_messages' . $id, array() );
	// update messages if there isn't a type and set to not spam as an upgrade path
	// only update if there were changes
	$updated = false;
	foreach ( $message as $key => $value ) {
		if ( ! isset( $value['type'] ) ) {
			$message[ $key ]['type'] = 'notspam';
			$updated                 = true;
		}
	}
	if ( $updated ) {
		update_option( 'qcf_messages' . $id, $message );
	}



	return $message;
}