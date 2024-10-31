<?php
function qcf_radio( $var, $list, $values, $errors, $required, $qcf, $name ) {
	$content = '<p class="input">' . $qcf['label'][ $name ] . '</p>';
	$arr     = explode( ",", $qcf[ $list ] );
	foreach ( $arr as $item ) {
		$checked = '';
		if ( $values[ $var ] == $item ) {
			$checked = 'checked';
		}
		if ( $item === reset( $arr ) ) {
			$content .= '<p class="input"><input type="radio" style="margin:0; padding: 0; border: none" name="' . $var . '" value="' . $item . '" id="' . $item . '" checked><label for="' . $item . '"> ' . $item . '</label><br>';
		} else {
			$content .= '<p class="input"><input type="radio" style="margin:0; padding: 0; border: none" name="' . $var . '" value="' . $item . '" id="' . $item . '" ' . $checked . '><label for="' . $item . '"> ' . $item . '</label><br>';
		}
	}
	$content .= '</p>';

	return $content;
}
