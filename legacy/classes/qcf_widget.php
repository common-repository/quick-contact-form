<?php

class qcf_widget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'qcf_widget', // Base ID
			esc_html__( 'Quick Contact Form', 'quick-contact-form' ), // Name
			array(
				'description' => esc_html__( 'Add the Quick Contact Form to your sidebar', 'quick-contact-form' ),
			) // Args
		);
	}

	function form( $instance ) {
		$instance  = wp_parse_args( (array) $instance, array( 'formname' => '' ) );
		$formname  = $instance['formname'];
		$qcf_setup = qcf_get_stored_setup();
		echo 'Select Form:</ br>';
		?>
        <select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'formname' ) ); ?>">
			<?php
			$arr = explode( ",", $qcf_setup['alternative'] );
			foreach ( $arr as $item ) {
				if ( $item == '' ) {
					$showname = 'default';
					$item     = '';
				} else {
					$showname = $item;
				}
				if ( $showname == $formname || $formname == '' ) {
					$selected = 'selected';
				} else {
					$selected = '';
				}
				?>
                <option value="<?php echo esc_attr( $item ); ?>"
                        id="<?php echo esc_attr( $this->get_field_id( 'formname' ) ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $showname ); ?>
                </option>
				<?php
			}
			?>
        </select>
        <p>
			<?php
			printf(
			// translators: %1$s: link to settings page, %2$s: closing link tag
				esc_html__( 'All options for the quick contact form are changed on the plugin %1$sSettings%2$s page.', 'quick-contact-form' ),
				'<a href="' . esc_url( admin_url() ) . '/options-general.php?page=quick-contact-form">',
				'</a>'
			);
			?>
        </p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['formname'] = $new_instance['formname'];

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		$id = preg_replace( "/[^A-Za-z]/", '', $instance['formname'] );

		echo qcf_kses_forms( qcf_loop( $id ) );
	}
}
