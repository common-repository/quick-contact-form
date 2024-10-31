<?php
function qcf_checklist( $var, $list, $values, $errors, $required, $qcf, $name ) {
	if ( isset( $errors[ $var ])  && !empty($errors[ $var ]) ) {
		$content = $errors[ $var ];
	} else {
		$content = '<p class="input ' . $required . '">' . $qcf['label'][ $name ] . '</p>';
	}
	$content .= '<p class="input">';
	$arr     = explode( ",", $qcf[ $list ] );
	foreach ( $arr as $item ) {
		$checked = '';
		if ( isset($values[ $var . '_' . str_replace( ' ', '', $item ) ]) && $values[ $var . '_' . str_replace( ' ', '', $item ) ] == $item ) {
			$checked = 'checked';
		}
		$content .= '<label><input type="checkbox" style="margin:0; padding: 0; border: none" name="' . $var . '_' . str_replace( ' ', '', $item ) . '" value="' . $item . '" ' . $checked . '> ' . $item . '</label><br>';
	}
	$content .= '</p>';

	return $content;
}