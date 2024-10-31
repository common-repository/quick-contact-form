<?php

/**
 * @var \Freemius $quick_contact_form_fs Object for freemius.
 */
global $quick_contact_form_fs;
add_action( 'wp_ajax_qcf_validate_form', 'qcf_validate_form_callback' );
add_action( 'wp_ajax_nopriv_qcf_validate_form', 'qcf_validate_form_callback' );
add_shortcode( 'qcf', 'qcf_start' );
add_filter(
    'plugin_action_links',
    'qcf_plugin_action_links',
    10,
    2
);
add_action( 'wp_enqueue_scripts', 'qcf_style_scripts', 99 );
add_action( 'widgets_init', 'add_qcf_widget' );
add_action( 'init', 'qcf_block_init' );
if ( is_admin() ) {
    require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/settings.php';
}
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/options.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/akismet.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/add_qcf_widget.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_block_init.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_checklist.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_create_user.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_display_form.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_dropdown.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_form_field_error.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_generate_css.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_get_messages.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_head_css.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_kses_forms.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_loop.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_plugin_action_links.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_process_form.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_radio.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_redirect_by_email.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_redirect_by_selection.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_sanitize_formvalues.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_send_confirmation.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_start.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_style_scripts.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_upload_dir.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_utility_functions.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_validate_form_callback.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_verify_form.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/functions/qcf_wp_mail.php';
require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'legacy/classes/qcf_widget.php';