<?php
function qcf_get_element( $array, $keys, $default = '' ) {
	if ( ! is_array( $array ) ) {
		return $array;
	}
	if ( ! is_array( $keys ) ) {
		if ( array_key_exists( $keys, $array ) ) {
			return $array[ $keys ];
		}
	} else {
		$result = $array;
		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, $result ) ) {
				$result = $result[ $key ];
			} else {
				return $default;
			}
		}
		if ( ! is_array( $result ) ) {
			return $result;
		}

	}

	return $default;
}