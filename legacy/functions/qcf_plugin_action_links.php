<?php
function qcf_plugin_action_links( $links, $file ) {
	if ( false !== strpos( $file, '/quick-contact-form.php' ) ) {
		$qcf_links = '<a href="' . get_admin_url() . 'options-general.php?page=quick-contact-form">' . __( 'Settings' ) . '</a>';
		array_unshift( $links, $qcf_links );
	}

	return $links;
}
