<?php

$qcf_setup = qcf_get_stored_setup();
$tabs = explode( ",", $qcf_setup['alternative'] );
$firsttab = reset( $tabs );
echo '<div class="wrap">';
echo '<h1>' . esc_html__( 'Quick Contact Form Messages', 'quick-contact-form' ) . '</h1>';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No action, nonce is not required
if ( isset( $_GET['tab'] ) ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No action, nonce is not required
    $tab = sanitize_text_field( $_GET['tab'] );
    qcf_messages_admin_tabs( $tab );
} else {
    qcf_messages_admin_tabs( $firsttab );
    $tab = $firsttab;
}
qcf_show_messages( $tab );
echo '</div>';
function attach_content_type(  $filename  ) {
    $mime_types = array(
        'txt'  => 'text/plain',
        'htm'  => 'text/html',
        'html' => 'text/html',
        'php'  => 'text/html',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'swf'  => 'application/x-shockwave-flash',
        'flv'  => 'video/x-flv',
        'png'  => 'image/png',
        'jpe'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'gif'  => 'image/gif',
        'bmp'  => 'image/bmp',
        'ico'  => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif'  => 'image/tiff',
        'svg'  => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'zip'  => 'application/zip',
        'rar'  => 'application/x-rar-compressed',
        'exe'  => 'application/x-msdownload',
        'msi'  => 'application/x-msdownload',
        'cab'  => 'application/vnd.ms-cab-compressed',
        'mp3'  => 'audio/mpeg',
        'qt'   => 'video/quicktime',
        'mov'  => 'video/quicktime',
        'pdf'  => 'application/pdf',
        'psd'  => 'image/vnd.adobe.photoshop',
        'ai'   => 'application/postscript',
        'eps'  => 'application/postscript',
        'ps'   => 'application/postscript',
        'doc'  => 'application/msword',
        'rtf'  => 'application/rtf',
        'xls'  => 'application/vnd.ms-excel',
        'ppt'  => 'application/vnd.ms-powerpoint',
        'odt'  => 'application/vnd.oasis.opendocument.text',
        'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
    );
    $filename_explode = explode( '.', $filename );
    $ext = strtolower( array_pop( $filename_explode ) );
    if ( array_key_exists( $ext, $mime_types ) ) {
        return $mime_types[$ext];
    } elseif ( function_exists( 'finfo_open' ) ) {
        $finfo = finfo_open( FILEINFO_MIME );
        $mimetype = finfo_file( $finfo, $filename );
        finfo_close( $finfo );
        return $mimetype;
    } else {
        return 'application/octet-stream';
    }
}

function qcf_messages_admin_tabs(  $current = 'default'  ) {
    $qcf_setup = qcf_get_stored_setup();
    $tabs = explode( ",", $qcf_setup['alternative'] );
    array_push( $tabs, 'default' );
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $tabs as $tab ) {
        $class = ( $tab == $current ? ' nav-tab-active' : '' );
        if ( $tab ) {
            echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=quick-contact-form-messages&tab=' . esc_attr( $tab ) . '">' . wp_kses_post( $tab ) . '</a>';
        }
    }
    echo '</h2>';
}

function qcf_show_messages(  $id  ) {
    global $current_user;
    global $quick_contact_form_fs;
    $sendtoemail = false;
    if ( $id == 'default' ) {
        $id = '';
    }
    $fifty = $hundred = $all = $oldest = $newest = '';
    $title = $id;
    if ( $id == '' ) {
        $title = 'Default';
    }
    $qcf_setup = qcf_get_stored_setup();
    $qcf = qcf_get_stored_options( $id );
    qcf_generate_csv();
    if ( isset( $_POST['qcf_emaillist'] ) ) {
        if ( !isset( $_POST['_qcf_messages_download_nonce'] ) || !wp_verify_nonce( $_POST['_qcf_messages_download_nonce'], 'qcf_messages_download' ) ) {
            wp_die( esc_html__( 'Nonce validation - security failed', 'quick-contact-form' ) );
        }
        $messageoptions = qcf_get_stored_msg();
        $message = qcf_get_messages( $id );
        $content = qcf_build_message_table(
            $id,
            $message,
            $messageoptions,
            $qcf,
            999
        );
        $title = $id;
        if ( $id == '' ) {
            $title = 'Default';
        }
        $title = 'Message List for ' . $title . ' as at ' . date( 'j M Y' );
        $current_user = wp_get_current_user();
        $sendtoemail = sanitize_email( $_POST['sendtoemail'] );
        $headers = "From: <" . $sendtoemail . ">\r\n" . "MIME-Version: 1.0\r\n" . "Content-Type: text/html; charset=\"utf-8\"\r\n";
        wp_mail(
            $sendtoemail,
            $title,
            $content,
            $headers
        );
        qcf_admin_notice( 'Message list has been sent to ' . $sendtoemail . '.' );
    }
    if ( isset( $_POST['qcf_reset_message' . $id] ) ) {
        if ( !isset( $_POST['_qcf_messages_download_nonce'] ) || !wp_verify_nonce( $_POST['_qcf_messages_download_nonce'], 'qcf_messages_download' ) ) {
            wp_die( esc_html__( 'Nonce validation - security failed', 'quick-contact-form' ) );
        }
        $messageoptions = qcf_get_stored_msg();
        $message = qcf_get_messages( $id, 'all' );
        $count = count( $message );
        $message = array_filter( $message, function ( $value ) use($messageoptions) {
            return $messageoptions['messagetype'] != $value['type'];
        } );
        $count = $count - count( $message );
        update_option( 'qcf_messages' . $id, $message );
        qcf_admin_notice( $count . ' ' . __( 'Messages have been deleted', 'quick-contact-form' ) );
    }
    if ( isset( $_POST['qcf_email_selected'] ) ) {
        if ( !isset( $_POST['_qcf_messages_download_nonce'] ) || !wp_verify_nonce( $_POST['_qcf_messages_download_nonce'], 'qcf_messages_download' ) ) {
            wp_die( esc_html__( 'Nonce validation - security failed', 'quick-contact-form' ) );
        }
        $id = sanitize_text_field( $_POST['formname'] );
        $from = get_bloginfo( 'name' );
        $current_user = wp_get_current_user();
        $sendtoemail = $current_user->user_email;
        $headers = 'From: "' . $from . '" <' . $sendtoemail . '>' . "\r\n" . "MIME-Version: 1.0\r\n" . "Content-Type: text/html; charset=\"utf-8\"\r\n";
        $body = wp_kses_post( $_POST['message'] );
        $message = qcf_get_messages( $id );
        $count = count( $message );
        for ($i = 0; $i <= $count; $i++) {
            if ( $_POST[$i] == 'checked' ) {
                wp_mail(
                    $message[$i]['field2'],
                    sanitize_text_field( $_POST['subject'] ),
                    $body,
                    $headers
                );
            }
        }
        qcf_admin_notice( __( 'Messages have been sent to selected names', 'quick-contact-form' ) );
    }
    if ( isset( $_POST['qcf_delete_selected'] ) ) {
        if ( !isset( $_POST['_qcf_messages_download_nonce'] ) || !wp_verify_nonce( $_POST['_qcf_messages_download_nonce'], 'qcf_messages_download' ) ) {
            wp_die( esc_html__( 'Nonce validation - security failed', 'quick-contact-form' ) );
        }
        $id = sanitize_text_field( $_POST['formname'] );
        $messageoptions = qcf_get_stored_msg();
        $message = qcf_get_messages( $id, $messageoptions['messagetype'] );
        $count = count( $message );
        $del_count = 0;
        for ($i = 0; $i <= $count; $i++) {
            if ( $_POST[$i] == 'checked' ) {
                $del_count++;
                unset($message[$i]);
            }
        }
        $message = array_values( $message );
        update_option( 'qcf_messages' . $id, $message );
        qcf_admin_notice( $del_count . ' ' . esc_html__( 'Selected messages have been deleted.', 'quick-contact-form' ) );
    }
    if ( isset( $_POST['qcf_toggle_spam'] ) ) {
        if ( !isset( $_POST['_qcf_messages_download_nonce'] ) || !wp_verify_nonce( $_POST['_qcf_messages_download_nonce'], 'qcf_messages_download' ) ) {
            wp_die( esc_html__( 'Nonce validation - security failed', 'quick-contact-form' ) );
        }
        $id = sanitize_text_field( $_POST['formname'] );
        $messageoptions = qcf_get_stored_msg();
        $message = qcf_get_messages( $id, $messageoptions['messagetype'] );
        $all_messages = qcf_get_messages( $id, 'all' );
        $count = count( $message );
        $msg_count = 0;
        for ($i = 0; $i <= $count; $i++) {
            if ( isset( $_POST[$i] ) && $_POST[$i] == 'checked' ) {
                $msg_count++;
                if ( $message[$i]['type'] == 'spam' ) {
                    $all_messages[$message[$i]['id']]['type'] = 'notspam';
                    do_action(
                        'qcf_message_notspam',
                        $message[$i],
                        $id,
                        'notspam message'
                    );
                } else {
                    $all_messages[$message[$i]['id']]['type'] = 'spam';
                    do_action(
                        'qcf_message_spam',
                        $message[$i],
                        $id,
                        'spam message'
                    );
                }
            }
        }
        update_option( 'qcf_messages' . $id, $all_messages );
        if ( 'notspam' == $messageoptions['messagetype'] ) {
            qcf_admin_notice( $msg_count . ' ' . esc_html__( 'Selected messages have been marked as not spam.', 'quick-contact-form' ) );
        } else {
            qcf_admin_notice( $msg_count . ' ' . esc_html__( 'Selected messages have been marked as spam.', 'quick-contact-form' ) );
        }
    }
    if ( isset( $_POST['Update'] ) ) {
        if ( !isset( $_POST['_qcf_messages_update_nonce'] ) || !wp_verify_nonce( $_POST['_qcf_messages_update_nonce'], 'qcf_messages_update' ) ) {
            wp_die( esc_html__( 'Nonce validation - security failed', 'quick-contact-form' ) );
        }
        $options = array('messageqty', 'messageorder', 'messagetype');
        foreach ( $options as $item ) {
            if ( isset( $_POST[$item] ) ) {
                $messageoptions[$item] = stripslashes( sanitize_text_field( $_POST[$item] ) );
            }
        }
        update_option( 'qcf_messageoptions', $messageoptions );
        qcf_admin_notice( esc_html__( "The message options have been updated.", 'quick-contact-form' ) );
    }
    if ( !$sendtoemail ) {
        $sendtoemail = $current_user->user_email;
    }
    $new = '';
    if ( $quick_contact_form_fs->is_not_paying() ) {
        if ( $quick_contact_form_fs->is_trial() || $quick_contact_form_fs->is_trial_utilized() ) {
            $upurl = $quick_contact_form_fs->get_upgrade_url();
            $upmsg = esc_html__( 'Upgrade to Pro', 'quick-contact-form' );
        } else {
            $upurl = $quick_contact_form_fs->get_trial_url();
            $upmsg = esc_html__( 'Go Pro: Free 14 Day Trial', 'quick-contact-form' );
        }
        $new = '<div class="qpupgrade"><a href="' . $upurl . '">
    <h3>' . $upmsg . '</h3>
    <p>' . esc_html__( 'Upgrading lets you create a mailing list, send emails from your dashboard and access all form attachments.', 'quick-contact-form' ) . '</p>
    <p>' . esc_html__( 'Click here to find out more', 'quick-contact-form' ) . '</p>
    </a></div>';
    }
    $messageoptions = qcf_get_stored_msg();
    $showthismany = '9999';
    if ( $messageoptions['messageqty'] == 'fifty' ) {
        $showthismany = '50';
    }
    if ( $messageoptions['messageqty'] == 'hundred' ) {
        $showthismany = '100';
    }
    ${$messageoptions['messageorder']} = "checked";
    $message = qcf_get_messages( $id, $messageoptions['messagetype'] );
    $dashboard = '<form method="post" action="">' . wp_nonce_field(
        'qcf_messages_update',
        '_qcf_messages_update_nonce',
        true,
        false
    ) . '
	<p><b>Show</b> <input style="margin:0; padding:0; border:none;" type="radio" name="messageqty" value="fifty" ' . $fifty . ' /> 50 
	<input style="margin:0; padding:0; border:none;" type="radio" name="messageqty" value="hundred" ' . checked( $messageoptions['messageqty'], 'hundred', false ) . ' /> 100 
	<input style="margin:0; padding:0; border:none;" type="radio" name="messageqty" value="all" ' . checked( $messageoptions['messageqty'], 'all', false ) . ' /> all messages.&nbsp;&nbsp;
	<b>List</b> <input style="margin:0; padding:0; border:none;" type="radio" name="messageorder" value="oldest" ' . checked( $messageoptions['messageorder'], 'oldest', false ) . ' /> oldest first 
	<input style="margin:0; padding:0; border:none;" type="radio" name="messageorder" value="newest" ' . checked( $messageoptions['messageorder'], 'newest', false ) . ' /> newest first
	<br>
	<b>Type</b> <input style="margin:0; padding:0; border:none;" type="radio" name="messagetype" value="notspam" ' . checked( $messageoptions['messagetype'], 'notspam', false ) . ' /> Not Spam (' . count( qcf_get_messages( $id, 'notspam' ) ) . ')
	<input style="margin:0; padding:0; border:none;" type="radio" name="messagetype" value="spam" ' . checked( $messageoptions['messagetype'], 'spam', false ) . ' /> Spam (' . count( qcf_get_messages( $id, 'spam' ) ) . ')
	&nbsp;&nbsp;<input type="submit" name="Update" class="button-secondary" value="Update options" />
	</form></p>';
    $dashboard .= '<div class="wrap"><div id="qcf-widget"><form method="post" id="download_form" action="">' . wp_nonce_field(
        'qcf_messages_download',
        '_qcf_messages_download_nonce',
        true,
        false
    );
    $dashboard .= qcf_message_list_action_buttons(
        $sendtoemail,
        $id,
        $title,
        $messageoptions
    );
    $dashboard .= qcf_build_message_table(
        $id,
        $message,
        $messageoptions,
        $qcf,
        $showthismany
    );
    $dashboard .= '<p><input type="hidden" name="formname" value = "' . $id . '" />';
    $dashboard .= qcf_message_list_action_buttons(
        $sendtoemail,
        $id,
        $title,
        $messageoptions
    );
    $dashboard .= '</div></div>';
    $dashboard .= $new;
    $dashboard .= '</form>';
    echo $dashboard;
}

function qcf_message_list_action_buttons(
    $sendtoemail,
    $id,
    $title,
    $messageoptions
) {
    $dashboard = '<p>Send to this email address: <input type="text" name="sendtoemail" value="' . $sendtoemail . '">&nbsp;
    <input type="submit" name="qcf_emaillist" class="qcf-button" value="Email List" />
    <input type="submit" name="qcf_reset_message' . $id . '" class="qcf-button" value="Delete ALL Messages" onclick="return window.confirm( \'Are you sure you want to delete ALL the messages for ' . $title . '?\' );"/>
    <input type="submit" name="qcf_delete_selected" class="qcf-button" value="Delete Selected" onclick="return window.confirm( \'Are you sure you want to delete the selected messages?\' );"/>';
    $dashboard .= '<input type="submit" name="download_csv" class="qcf-button" value="Export to CSV" />';
    if ( 'notspam' == $messageoptions['messagetype'] ) {
        $msg = esc_html__( 'Spam selected', 'quick-contact-form' );
    } else {
        $msg = esc_html__( 'Unspam selected', 'quick-contact-form' );
    }
    $dashboard .= '<input type="submit" name="qcf_toggle_spam" class="qcf-button" value="' . $msg . '" />';
    $dashboard .= '</p>';
    return $dashboard;
}

function qcf_build_message_table(
    $id,
    $message,
    $messageoptions,
    $qcf,
    $showthismany
) {
    global $quick_contact_form_fs;
    $attach = qcf_get_stored_attach( $id );
    $count = 0;
    $content = '';
    $report = '';
    $qcf['label']['field15'] = esc_html__( "Consent", 'quick-contact-form' );
    if ( !is_array( $message ) ) {
        $message = array();
    }
    $dashboard = '<table cellspacing="0"><tr>';
    foreach ( explode( ',', $qcf['sort'] ) as $name ) {
        if ( $qcf['active_buttons'][$name] == "on" && $name != 'field12' ) {
            $dashboard .= '<th style="text-align:left">' . $qcf['label'][$name] . '</th>';
        }
    }
    $dashboard .= '<th style="text-align:left">Date Sent</th>';
    if ( $showthismany != 999 ) {
        $dashboard .= '<th></th>';
    }
    $dashboard .= '</tr>';
    if ( $messageoptions['messageorder'] == 'newest' ) {
        $i = count( $message ) - 1;
        foreach ( array_reverse( $message ) as $value ) {
            if ( $count < $showthismany ) {
                $content .= '<tr>';
                foreach ( explode( ',', $qcf['sort'] ) as $name ) {
                    if ( $qcf['active_buttons'][$name] == "on" && $name != 'field12' ) {
                        if ( $value[$name] ) {
                            $report = 'messages';
                        }
                        $content .= '<td>' . strip_tags( $value[$name], $qcf['htmltags'] ) . '</td>';
                    }
                }
                $content .= '<td>' . $value['field0'] . '</td>';
                if ( $showthismany != 999 ) {
                    $content .= '<td><input type="checkbox" name="' . $i . '" value="checked" /></td>';
                }
                $content .= '</tr>';
                $count = $count + 1;
                $i--;
            }
        }
    } else {
        $i = 0;
        foreach ( $message as $value ) {
            if ( $count < $showthismany ) {
                $content .= '<tr>';
                foreach ( explode( ',', $qcf['sort'] ) as $name ) {
                    if ( $qcf['active_buttons'][$name] == "on" && $name != 'field12' ) {
                        if ( $value[$name] ) {
                            $report = 'messages';
                        }
                        $content .= '<td>' . strip_tags( $value[$name], $qcf['htmltags'] ) . '</td>';
                    }
                }
                $content .= '<td>' . $value['field0'] . '</td>';
                if ( $showthismany != 999 ) {
                    $content .= '<td><input type="checkbox" name="' . $i . '" value="checked" /></td>';
                }
                $content .= '</tr>';
                $count = $count + 1;
                $i++;
            }
        }
    }
    if ( $report ) {
        $dashboard .= $content . '</table>';
    } else {
        $dashboard .= '</table><p>No messages found</p>';
    }
    return $dashboard;
}

function qcf_message_thumbs(  $value  ) {
    $content = '<td>';
    if ( $value['attachments'] ) {
        foreach ( $value['attachments'] as $item ) {
            $mime = attach_content_type( $item['url'] );
            $filename = pathinfo( $item['url'], PATHINFO_FILENAME );
            $content .= '<a href="' . $item['url'] . '"><img style="width:auto;height:40px;margin-right:6px;" ';
            if ( strpos( $item['file'], '.pdf' ) ) {
                $content .= 'src="' . QUICK_CONTACT_FORM_PLUGIN_URL . 'legacy/images/pdf.png"';
            } elseif ( strpos( $item['file'], '.xls' ) ) {
                $content .= 'src="' . QUICK_CONTACT_FORM_PLUGIN_URL . 'legacy/images/xls.png"';
            } elseif ( strpos( $item['file'], '.doc' ) ) {
                $content .= 'src="' . QUICK_CONTACT_FORM_PLUGIN_URL . 'legacy/images/doc.png"';
            } elseif ( strstr( $mime, "image/" ) ) {
                $content .= 'src="' . $item['url'] . '"';
            } else {
                $content .= 'src="' . QUICK_CONTACT_FORM_PLUGIN_URL . 'legacy/images/files.png"';
            }
            $content .= ' alt="' . $filename . '" title="' . $filename . '" /></a>';
        }
    }
    $content .= '</td>';
    return $content;
}
