<?php
function qcf_dropdown( $var, $list, $values, $errors, $required, $qcf, $name ) {
	$content = (isset( $errors[ $var ])  && !empty($errors[ $var ]))?'$errors[ $var ]':'';
	$content .= '<select name="' . $var . '" class="' . $required . '" ><option value="' . $qcf['label'][ $name ] . '">' . $qcf['label'][ $name ] . '</option>' . "\r\t";
	$arr     = explode( ",", $qcf[ $list ] );
	foreach ( $arr as $item ) {
		$selected = '';
		if ( $values[ $var ] == $item ) {
			$selected = 'selected';
		}
		$content .= '<option value="' . $item . '" ' . $selected . '>' . $item . '</option>' . "\r\t";
	}
	$content .= '</select>' . "\r\t";

	return $content;
}

