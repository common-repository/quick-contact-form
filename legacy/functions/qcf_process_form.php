<?php

function qcf_process_form(  $values, $id  ) {
    /**
     * @var \Freemius $quick_contact_form_fs Object for freemius.
     */
    global $quick_contact_form_fs;
    $qcf_setup = qcf_get_stored_setup();
    $qcf = qcf_get_stored_options( $id );
    $attach = qcf_get_stored_attach( $id );
    list( $attachments, $dir, $att, $gotlinks ) = qcf_handle_attachments( $attach );
    if ( !$qcf_setup['nostore'] || $values['qcfname15'] ) {
        qcf_store_message(
            $values,
            $id,
            $qcf['label']['field1'],
            $att
        );
        // now don't do anything with spam
        if ( !$qcf_setup['nostore'] && isset( $values['qcf_spam'] ) && $values['qcf_spam'] == 'spam' ) {
            return;
        }
    }
    qcf_process_confirmations(
        $id,
        $quick_contact_form_fs,
        $values,
        $qcf,
        $attach,
        $gotlinks,
        $dir,
        $attachments
    );
}

/**
 * @param $id
 * @param array $style
 * @param Freemius $quick_contact_form_fs
 * @param $values
 * @param array $reply
 * @param array $qcf
 * @param array $attach
 * @param $gotlinks
 * @param $dir
 * @param $attachments
 *
 * @return void
 * @throws RequestException
 */
function qcf_process_confirmations(
    $id,
    Freemius $quick_contact_form_fs,
    $values,
    $qcf,
    $attach,
    $gotlinks,
    $dir,
    $attachments
) {
    $reply = qcf_get_stored_reply( $id );
    $style = qcf_get_stored_style( $id );
    $content = '';
    $auto = qcf_get_stored_autoresponder( $id );
    $hd = ( $style['header-type'] ? $style['header-type'] : 'h2' );
    $qcfemail = qcf_get_stored_email();
    $qcf_email = ( $qcfemail[$id] ? $qcfemail[$id] : get_bloginfo( 'admin_email' ) );
    if ( isset( $_GET["email"] ) ) {
        $qcf_email = sanitize_email( $_GET["email"] );
    }
    $values['qcfname2'] = ( $values['qcfname2'] ? $values['qcfname2'] : $qcf_email );
    if ( !empty( $reply['replytitle'] ) ) {
        $reply['replytitle'] = apply_filters( 'qcf_reply_title_h2_markup', '<' . $hd . ' class="reply-title">' ) . $reply['replytitle'] . apply_filters( 'qcf_reply_title_end_h2_markup', '</' . $hd . '>' );
    }
    if ( !empty( $reply['replyblurb'] ) ) {
        $reply['replyblurb'] = apply_filters( 'qcf_reply_blurb_p_markup', '<p class="reply-blurb">' ) . $reply['replyblurb'] . apply_filters( 'qcf_reply_blurb_end_p_markup', '</p>' );
    }
    if ( $reply['subjectoption'] == "sendername" ) {
        $addon = $values['qcfname1'];
    }
    if ( $reply['subjectoption'] == "sendersubj" ) {
        $addon = $values['c'];
    }
    if ( $reply['subjectoption'] == "sendernone" ) {
        $addon = '';
    }
    if ( empty( $reply['fromemail'] ) ) {
        $reply['fromemail'] = get_bloginfo( 'admin_email' );
    }
    $from = 'From: ' . $reply['fromemail'] . "\r\n" . 'Reply-To: "' . $values['qcfname1'] . '" <' . $values['qcfname2'] . '>' . "\r\n";
    $ip = $_SERVER['REMOTE_ADDR'];
    $url = ( isset( $_POST['url'] ) ? sanitize_text_field( $_POST['url'] ) : '' );
    // look up post id using permalink
    $post_id = url_to_postid( $url );
    if ( $post_id ) {
        $page = get_the_title( $post_id );
    } else {
        $page = 'quick contact form';
    }
    if ( $reply['subjectoption'] == "senderpage" ) {
        $addon = $page;
    }
    foreach ( explode( ',', $qcf['sort'] ) as $item ) {
        if ( $qcf['active_buttons'][$item] ) {
            switch ( $item ) {
                case 'field1':
                    if ( $values['qcfname1'] == $qcf['label'][$item] ) {
                        $values['qcfname1'] = '';
                    }
                    $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . strip_tags( stripslashes( $values['qcfname1'] ) ) . '</p>';
                    $ac_d['name'] = strip_tags( stripslashes( $values['qcfname1'] ) );
                    break;
                case 'field2':
                    if ( $values['qcfname2'] == $qcf['label'][$item] ) {
                        $values['qcfname2'] = '';
                    }
                    $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . strip_tags( stripslashes( $values['qcfname2'] ) ) . '</p>';
                    $ac_d['email'] = strip_tags( stripslashes( $values['qcfname2'] ) );
                    break;
                case 'field3':
                    if ( $values['qcfname3'] == $qcf['label'][$item] ) {
                        $values['qcfname3'] = '';
                    }
                    $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . strip_tags( stripslashes( $values['qcfname3'] ) ) . '</p>';
                    $ac_d['phone'] = strip_tags( stripslashes( $values['qcfname3'] ) );
                    break;
                case 'field4':
                    if ( $values['qcfname4'] == $qcf['label'][$item] ) {
                        $values['qcfname4'] = '';
                    }
                    $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . strip_tags( stripslashes( $values['qcfname4'] ), $qcf['htmltags'] ) . '</p>';
                    break;
                case 'field5':
                    if ( $qcf['selectora'] == 'checkboxa' ) {
                        $checks = '';
                        $arr = explode( ",", $qcf['dropdownlist'] );
                        $content .= '<p><b>' . $qcf['label'][$item] . ': </b>';
                        foreach ( $arr as $key ) {
                            if ( qcf_get_element( $values, 'qcfname5_' . str_replace( ' ', '', $key ) ) ) {
                                $checks .= $key . ', ';
                            }
                        }
                        $values['qcfname5'] = rtrim( $checks, ', ' );
                        $content .= $values['qcfname5'] . '</p>';
                    } else {
                        if ( $values['qcfname5'] == $qcf['label'][$item] ) {
                            $values['qcfname5'] = '';
                        }
                        $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . $values['qcfname5'] . '</p>';
                    }
                    break;
                case 'field6':
                    if ( $qcf['selectorb'] == 'checkboxb' ) {
                        $checks = '';
                        $arr = explode( ",", $qcf['checklist'] );
                        $content .= '<p><b>' . $qcf['label'][$item] . ': </b>';
                        foreach ( $arr as $key ) {
                            if ( qcf_get_element( $values, 'qcfname6_' . str_replace( ' ', '', $key ) ) ) {
                                $checks .= $key . ', ';
                            }
                        }
                        $values['qcfname6'] = rtrim( $checks, ', ' );
                        $content .= $values['qcfname6'] . '</p>';
                    } else {
                        if ( $values['qcfname6'] == $qcf['label'][$item] ) {
                            $values['qcfname6'] = '';
                        }
                        $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . $values['qcfname6'] . '</p>';
                    }
                    break;
                case 'field7':
                    if ( $qcf['selectorc'] == 'checkboxc' ) {
                        $checks = '';
                        $arr = explode( ",", $qcf['radiolist'] );
                        $content .= '<p><b>' . $qcf['label'][$item] . ': </b>';
                        foreach ( $arr as $key ) {
                            if ( qcf_get_element( $values, 'qcfname7_' . str_replace( ' ', '', $key ) ) ) {
                                $checks .= $key . ', ';
                            }
                        }
                        $values['qcfname7'] = rtrim( $checks, ', ' );
                        $content .= $values['qcfname7'] . '</p>';
                    } else {
                        if ( $values['qcfname7'] == $qcf['label'][$item] ) {
                            $values['qcfname7'] = '';
                        }
                        $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . $values['qcfname7'] . '</p>';
                    }
                    break;
                case 'field8':
                    if ( $values['qcfname8'] == $qcf['label'][$item] ) {
                        $values['qcfname8'] = '';
                    }
                    $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . strip_tags( stripslashes( $values['qcfname8'] ) ) . '</p>';
                    break;
                case 'field9':
                    if ( $values['qcfname9'] == $qcf['label'][$item] ) {
                        $values['qcfname9'] = '';
                    }
                    $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . strip_tags( stripslashes( $values['qcfname9'] ) ) . '</p>';
                    break;
                case 'field10':
                    if ( $values['qcfname10'] == $qcf['label'][$item] ) {
                        $values['qcfname10'] = '';
                    }
                    if ( !empty( $values['qcfname10'] ) ) {
                        $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . strip_tags( $values['qcfname10'] ) . '</p>';
                    }
                    break;
                case 'field11':
                    if ( $values['qcfname11'] == $qcf['label'][$item] ) {
                        $values['qcfname11'] = '';
                    }
                    $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . strip_tags( stripslashes( $values['qcfname11'] ) ) . '</p>';
                    break;
                case 'field13':
                    if ( $values['qcfname13'] == $qcf['label'][$item] ) {
                        $values['qcfname13'] = '';
                    }
                    $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . strip_tags( stripslashes( $values['qcfname13'] ) ) . '</p>';
                    break;
                case 'field14':
                    $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . strip_tags( stripslashes( $values['qcfname14'] ) ) . '</p>';
                    break;
                case 'field15':
                    $content .= '<p><b>' . $qcf['label'][$item] . ': </b>' . strip_tags( stripslashes( $values['qcfname15'] ) ) . '</p>';
                    break;
            }
        }
    }
    $sendcontent = "<html>" . apply_filters( 'qcf_body_head_h2_markup', '<h2 class="body-head">' ) . $reply['bodyhead'] . apply_filters( 'qcf_body_head_end_h2_markup', '</h2>' ) . $content;
    $copycontent = "<html>";
    if ( $reply['replymessage'] ) {
        $copycontent .= $reply['replymessage'];
    }
    if ( $reply['replycopy'] ) {
        $copycontent .= $content;
    }
    if ( $reply['page'] ) {
        $sendcontent .= "<p>Message was sent from: <b>" . $page . "</b></p>";
    }
    if ( $reply['tracker'] ) {
        $sendcontent .= "<p>Senders IP address: <b>" . $ip . "</b></p>";
    }
    if ( $reply['url'] ) {
        $sendcontent .= "<p>URL: <b>" . $url . "</b></p>";
    }
    $subject = "{$reply['subject']} {$addon}";
    if ( $attach['qcf_attach_link'] && $gotlinks ) {
        $sendcontent .= apply_filters( 'qcf_attach_link_h2_markup', '<h2 class="attach-link">' ) . esc_html__( 'Attachments:', 'quick-contact-form' ) . apply_filters( 'qcf_attach_link_h2_markup', '</h2>' );
        for ($i = 1; $i <= $attach['qcf_number']; $i++) {
            $filename = $_FILES['filename' . $i]['name'];
            $filename = trim( preg_replace( '/[^A-Za-z0-9. ]/', '', $filename ) );
            $filename = str_replace( ' ', '-', $filename );
            if ( $filename ) {
                $sendcontent .= '<p><a href = "' . $url . '/wp-content' . $dir . $filename . '">' . $filename . '</a><br>';
            }
        }
        $sendcontent .= '</p>';
    }
    $sendcontent .= "</html>";
    $headers = $from;
    if ( $reply['qcf_bcc'] ) {
        $headers .= "BCC: " . $qcf_email . "\r\n";
        $qcf_email = 'null';
    }
    $headers .= "MIME-Version: 1.0\r\n" . "Content-Type: text/html; charset=\"utf-8\"\r\n";
    $message = $sendcontent;
    $emails = qcf_get_stored_emails( $id );
    if ( function_exists( 'qcf_select_email' ) || $emails['emailenable'] ) {
        $email = qcf_redirect_by_email( $id, $values['qcfname5'] );
        if ( $email ) {
            $qcf_email = $email;
        }
    }
    qcf_wp_mail(
        'Admin',
        $qcf_email,
        $subject,
        $message,
        $headers,
        $attachments
    );
    if ( $auto['enable'] && $values['qcfname2'] ) {
        qcf_send_confirmation(
            $values,
            $content,
            $id,
            $qcf_email
        );
    }
    do_action( 'qcf_post_email', $values, $id );
    if ( isset( $reply['createuser'] ) && $reply['createuser'] ) {
        qcf_create_user( $values );
        do_action( 'qcf_post_user_creation', $values, $id );
    }
    $url = false;
    if ( $reply['qcf_reload'] ) {
        $_POST = array();
        $reloadinterval = ( $reply['qcf_reload_time'] ? $reply['qcf_reload_time'] : 0 );
    }
    if ( $reply['qcf_redirect'] ) {
        $wheretogo = qcf_get_stored_redirect( $id );
        if ( function_exists( 'qcf_select_redirect' ) || $wheretogo['redirectenable'] ) {
            $redirect = qcf_redirect_by_selection( $id, $values );
        }
        if ( $redirect ) {
            $location = $redirect;
        } else {
            $location = $reply['qcf_redirect_url'];
        }
        $url = ';url=' . $location;
    }
    wp_add_inline_script( 'qcf_script', 'document.getElementById("qcf_reload").scrollIntoView();' );
    if ( $id ) {
        $formstyle = $id;
    } else {
        $formstyle = 'default';
    }
    $replycontent = "<a id='qcf_reload'></a><br><div class='qcf-style " . $formstyle . "'>\r\t\n    <div id='" . $style['border'] . "'>\r\t";
    $replycontent .= $reply['replytitle'] . $reply['replyblurb'];
    if ( $reply['messages'] ) {
        $replycontent .= $content;
    }
    $replycontent .= '</div></div>';
    $redirecting = "<a id='qcf_reload'></a><br><div class='qcf-style " . $formstyle . "'>\r\t\n    <div id='" . $style['border'] . "'>\r\t";
    $redirecting .= apply_filters( 'qcf_redirecting_h2_markup', '<' . $hd . ' class="redirecting">' ) . esc_html__( 'Redirecting...', 'quick-contact-form' ) . apply_filters( 'qcf_redirecting_h2_markup', '</' . $hd . '>' );
    $redirecting .= '</div></div>';
    if ( $reply['qcf_redirect'] && $reply['qcf_reload'] ) {
        echo '<meta http-equiv="refresh" content="' . esc_attr( $reloadinterval ) . esc_attr( $url ) . '">' . wp_kses_post( $replycontent );
    } elseif ( $reply['qcf_redirect'] && !$reply['qcf_reload'] ) {
        echo '<meta http-equiv="refresh" content="0' . esc_attr( $url ) . '">' . wp_kses_post( $redirecting );
    } elseif ( !$reply['qcf_redirect'] && $reply['qcf_reload'] ) {
        echo '<meta http-equiv="refresh" content="' . esc_attr( $reloadinterval ) . '">' . wp_kses_post( $replycontent );
    } else {
        echo wp_kses_post( $replycontent );
    }
}

/**
 * Store the message in the database
 *
 * @param $values
 * @param $id
 * @param $field
 * @param $att
 *
 */
function qcf_store_message(
    $values,
    $id,
    $field,
    $att
) {
    $qcf_messages = qcf_get_messages( $id );
    $sentdate = date_i18n( 'd M Y' );
    $message = array(
        'field0'      => $sentdate,
        'type'        => $values['type'],
        'attachments' => $att,
    );
    for ($i = 1; $i <= 15; $i++) {
        $key = 'qcfname' . $i;
        $message['field' . $i] = $values[$key] ?? '';
        // If the field is 5, 6 or 7, check for any additional '_anystring' fields
        if ( $i >= 5 && $i <= 7 ) {
            foreach ( $values as $key => $value ) {
                if ( preg_match( "/^qcfname{$i}_.+\$/", $key ) ) {
                    // Store these fields in 'field'.$i.'_anystring'
                    $message['field' . $i] = $value;
                }
            }
        }
    }
    $qcf_messages[] = $message;
    update_option( 'qcf_messages' . $id, $qcf_messages );
}

/**
 * @param array $attach
 *
 * @return array
 */
function qcf_handle_attachments(  array $attach  ) {
    $attachments = array();
    if ( !function_exists( 'wp_handle_upload' ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    add_filter( 'upload_dir', 'qcf_upload_dir' );
    $dir = ( realpath( WP_CONTENT_DIR . '/uploads/qcf/' ) ? '/uploads/qcf/' : '/uploads/' );
    $url = get_site_url();
    $att = $b = array();
    $upload_dir = wp_upload_dir();
    $upload = $upload_dir['basedir'];
    $gotlinks = false;
    for ($i = 1; $i <= $attach['qcf_number']; $i++) {
        if ( isset( $_FILES['filename' . $i] ) ) {
            $filename = $_FILES['filename' . $i]['tmp_name'];
            if ( file_exists( $filename ) ) {
                $name = $_FILES['filename' . $i]['name'];
                $name = trim( preg_replace( '/[^A-Za-z0-9. ]/', '', $name ) );
                $name = str_replace( ' ', '-', $name );
                if ( file_exists( $upload . '/qcf/' . $name ) ) {
                    $name = 'x' . $name;
                }
                $_FILES['filename' . $i]['name'] = $name;
                $uploadedfile = $_FILES['filename' . $i];
                $upload_overrides = array(
                    'test_form' => false,
                );
                $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
                if ( !$attach['qcf_attach_link'] ) {
                    array_push( $attachments, WP_CONTENT_DIR . $dir . $name );
                    $b['url'] = $url . '/wp-content' . $dir . $name;
                    $b['file'] = $name;
                    array_push( $att, $b );
                }
                $gotlinks = true;
            }
        }
    }
    remove_filter( 'upload_dir', 'qcf_upload_dir' );
    return array(
        $attachments,
        $dir,
        $att,
        $gotlinks
    );
}
