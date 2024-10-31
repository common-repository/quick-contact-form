<?php
function qcf_kses_forms( $html ) {

	$kses_defaults = wp_kses_allowed_html( 'post' );

	$svg_args = array(
		'form'     => array(
			'class'   => true,
			'method'  => true,
			'action'  => true,
			'enctype' => true,
			'id'      => true,
		),
		'select'   =>
			array(
				'class' => true,
				'name'  => true,
				'style' => true,
			),
		'option'   =>
			array(
				'class' => true,
				'value' => true,
				'style' => true,
			),
		'input'    => array(
			'id'               => true,
			'class'            => true,
			'name'             => true,
			'type'             => true,
			'value'            => true,
			'style'            => true,
			'data-default'     => true,
			'data-rangeslider' => true,
			'min'              => true,
			'max'              => true,
			'step'             => true,
			'placeholder'      => true,
			'size'             => true,
			'src'              => true,
		),
		'textarea' => array(
			'id'           => true,
			'class'        => true,
			'name'         => true,
			'type'         => true,
			'value'        => true,
			'style'        => true,
			'data-default' => true,
			'placeholder'  => true,
		),
		'output'   => array(
			'id'    => true,
			'class' => true,
			'style' => true,
		),
	);

	$allowed_tags = array_merge( $kses_defaults, $svg_args );

	return wp_kses( $html, $allowed_tags );

}
