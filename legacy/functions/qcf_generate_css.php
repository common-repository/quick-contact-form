<?php
function qcf_generate_css() {
	$qcf_form = qcf_get_stored_setup();
	$arr      = explode( ",", $qcf_form['alternative'] );
	$code     = '';
	foreach ( $arr as $item ) {
		$handle       = $header = $font = $inputfont = $submitfont = $fontoutput = $border = '';
		$headercolour = $headersize = $corners = $input = $background = $submitwidth = $paragraph = $submitbutton = $submit = '';
		$style        = qcf_get_stored_style( $item );
		$hd           = ( $style['header-type'] ? $style['header-type'] : 'h2' );
		if ( $item != '' ) {
			$id = '.' . $item;
		} else {
			$id = '.default';
		}
		if ( $style['font'] == 'plugin' ) {
			$font       = "font-family: " . $style['text-font-family'] . "; font-size: " . $style['text-font-size'] . ";color: " . $style['text-font-colour'] . ";height:auto;";
			$inputfont  = "font-family: " . $style['font-family'] . "; font-size: " . $style['font-size'] . "; color: " . $style['font-colour'] . ";";
			$submitfont = "font-family: " . $style['font-family'];
			if ( $style['header-size'] ) {
				$headersize = "font-size: " . $style['header-size'] . ";";
			}
			if ( $style['header-colour'] ) {
				$headercolour = "color: " . $style['header-colour'] . ";";
			}
			$header = ".qcf-style" . $id . " " . $hd . " {" . $headercolour . $headersize . ";height:auto;}";
		}
		$input     = ".qcf-style" . $id . " input[type=text], .qcf-style" . $id . " input[type=email], .qcf-style" . $id . " textarea, .qcf-style" . $id . " select {border: " . $style['input-border'] . ";background:" . $style['inputbackground'] . ";" . $inputfont . ";line-height:normal;height:auto; " . $style['line_margin'] . "}\r\n";
		$input     .= ".qcf-style" . $id . " .qcfcontainer input + label, .qcf-style" . $id . " .qcfcontainer textarea + label {" . $inputfont . ";}\r\n";
		$focus     = ".qcf-style" . $id . " input:focus, .qcf-style" . $id . " textarea:focus {background:" . $style['inputfocus'] . ";}\r\n";
		$paragraph = ".qcf-style" . $id . " p, .qcf-style" . $id . " select{" . $font . "line-height:normal;height:auto;}\r\n";
		$required  = ".qcf-style" . $id . " input[type=text].required, .qcf-style" . $id . " input[type=email].required, .qcf-style" . $id . " select.required, .qcf-style" . $id . " textarea.required {border: " . $style['input-required'] . ";}\r\n";
		$error     = ".qcf-style" . $id . " p span, .qcf-style" . $id . " .error {color:" . $style['error-font-colour'] . ";clear:both;}\r\n
.qcf-style" . $id . " input[type=text].error, .qcf-style" . $id . " input[type=email].error,.qcf-style" . $id . " select.error, .qcf-style" . $id . " textarea.error {border:" . $style['error-border'] . ";}\r\n";
		if ( $style['submitwidth'] == 'submitpercent' ) {
			$submitwidth = 'width:100%;';
		}
		if ( $style['submitwidth'] == 'submitrandom' ) {
			$submitwidth = 'width:auto;';
		}
		if ( $style['submitwidth'] == 'submitpixel' ) {
			$submitwidth = 'width:' . $style['submitwidthset'] . ';';
		}
		if ( $style['submitposition'] == 'submitleft' ) {
			$submitposition = 'float:left;';
		} else {
			$submitposition = 'float:right;';
		}
		if ( ! $style['submit-button'] ) {
			$submit      = "color:" . $style['submit-colour'] . ";background:" . $style['submit-background'] . ";border:" . $style['submit-border'] . ";" . $submitfont . ";font-size: inherit;";
			$submithover = "background:" . $style['submit-hover-background'] . ";";
		} else {
			$submit = 'border:none;padding:none;height:auto;overflow:hidden;';
		}
		$submitbutton = ".qcf-style" . $id . " #submit {" . $submitposition . $submitwidth . $submit . "}\r\n";
		$submitbutton .= ".qcf-style" . $id . " #submit:hover{" . $submithover . "}\r\n";
		if ( $style['border'] <> 'none' ) {
			$border = ".qcf-style" . $id . " #" . $style['border'] . " {border:" . $style['form-border'] . ";}\r\n";
		}
		if ( $style['background'] == 'white' ) {
			$background = ".qcf-style" . $id . " div {background:#FFF;}\r\n";
		}
		if ( $style['background'] == 'color' ) {
			$background = ".qcf-style" . $id . " div {background:" . $style['backgroundhex'] . ";}\r\n";
		}
		if ( $style['backgroundimage'] ) {
			$background = ".qcf-style" . $id . " div {background: url('" . $style['backgroundimage'] . "');}\r\n";
		}
		$formwidth = preg_split( '#(?<=\d)(?=[a-z%])#i', $style['width'] );
		if ( ! isset( $formwidth[1] ) || empty( $formwidth[1] ) ) {
			$formwidth[1] = 'px';
		}
		if ( $style['widthtype'] == 'pixel' ) {
			$width = $formwidth[0] . $formwidth[1];
		} else {
			$width = '100%';
		}
		if ( $style['corners'] == 'round' ) {
			$corner = '5px';
		} else {
			$corner = '0';
		}
		$corners = ".qcf-style" . $id . " input[type=text], .qcf-style" . $id . " input[type=email],.qcf-style" . $id . " textarea, .qcf-style" . $id . " select, .qcf-style" . $id . " #submit {border-radius:" . $corner . ";}\r\n";
		if ( $style['corners'] == 'theme' ) {
			$corners = '';
		}
		if ( ! isset( $style['slider-thickness'] ) ) {
			$style['slider-thickness'] = 1;
		}
		$handle = (int) $style['slider-thickness'] + 1;
		$slider = '.qcf-style' . $id . ' div.rangeslider, .qcf-style' . $id . ' div.rangeslider__fill {height: ' . $style['slider-thickness'] . 'em;background: ' . $style['slider-background'] . ';}
.qcf-style' . $id . ' div.rangeslider__fill {background: ' . $style['slider-revealed'] . ';}
.qcf-style' . $id . ' div.rangeslider__handle {background: ' . $style['handle-background'] . ';border: 1px solid ' . $style['handle-border'] . ';width: ' . $handle . 'em;height: ' . $handle . 'em;position: absolute;top: -0.5em;-webkit-border-radius:' . $style['handle-colours'] . '%;-moz-border-radius:' . $style['handle-corners'] . '%;-ms-border-radius:' . $style['handle-corners'] . '%;-o-border-radius:' . $style['handle-corners'] . '%;border-radius:' . $style['handle-corners'] . '%;}
.qcf-style' . $id . ' div.qcf-slideroutput{font-size:' . $style['output-size'] . ';color:' . $style['output-colour'] . ';}';
		$code   .= ".qcf-style" . $id . " {max-width:100%;overflow:hidden;width:" . $width . ";}\r\n" . $border . $corners . $header . $paragraph . $slider . $input . $focus . $required . $error . $background . $submitbutton;
	}

	return $code;
}
