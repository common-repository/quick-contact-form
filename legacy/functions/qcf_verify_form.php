<?php
function qcf_verify_form( &$values, &$errors, $id ) {
	$qcf    = qcf_get_stored_options( $id );
	$error  = qcf_get_stored_error( $id );
	$attach = qcf_get_stored_attach( $id );
	$apikey = get_option( 'qcf_akismet' );
	if ( $apikey ) {
		$blogurl = get_site_url();
		$akismet = new qcf_akismet( $blogurl, $apikey );
		$akismet->setCommentAuthor( $values['qcfname1'] );
		$akismet->setCommentAuthorEmail( $values['qcfname2'] );
		$akismet->setCommentContent( $values['qcfname4'] );
		if ( $akismet->isCommentSpam() ) {
			$errors['spam'] = $error['spam'];
		}
	}
	$content_to_check = $values['qcfname1'] . ' ' .
	                    $values['qcfname4'] . ' ' .
	                    $values['qcfname8'] . ' ' .
	                    $values['qcfname9'];

	$errors = apply_filters( 'qcf_entry_is_spam', $errors, $values['qcfname1'], $values['qcfname2'], $content_to_check, $values['qcfname4'], $error['spam'] );
	// errors need to be spam types  bad spam is 'spam' - human spam for review is hspam
	// if hspam or spam then we need to change the $values message type to spam
	// but if hspam we remove the error so it can go to the forms
	$values['type'] = 'notspam';
	if ( isset( $errors['spam'] ) ) {
		$values['type'] = 'spam';
	} elseif ( isset( $errors['check_spam'] ) ) {
		$values['type'] = 'spam';
		unset( $errors['check_spam'] );
	}
	$emailcheck = $error['emailcheck'];
	if ( $qcf['required']['field2'] == 'checked' ) {
		$emailcheck = 'checked';
	}
	$qcf['required']['field12'] = 'checked';
	$phonecheck                 = $error['phonecheck'];
	if ( $qcf['required']['field3'] == 'checked' ) {
		$phonecheck = 'checked';
	}
	if ( $qcf['active_buttons']['field2'] && $emailcheck && $values['qcfname2'] !== $qcf['label']['field2'] ) {
		if ( ! filter_var( $values['qcfname2'], FILTER_VALIDATE_EMAIL ) ) {
			$errors['qcfname2'] = '<p class="qcf-input-error"><span>' . $error['email'] . '</span></p>';
		}
	}
	if ( $qcf['active_buttons']['field3'] && $phonecheck == 'checked' && $values['qcfname3'] !== $qcf['label']['field3'] ) {
		if ( preg_match( "/[^0-9()\+\.\-\s]$/", $values['qcfname3'] ) ) {
			$errors['qcfname3'] = '<p class="qcf-input-error"><span>' . $error['telephone'] . '</span></p>';
		}
	}
	if ( $qcf['fieldtype'] == 'tmail' && $qcf['active_buttons']['field11'] && $values['qcfname11'] !== $qcf['label']['field11'] ) {
		if ( ! filter_var( $values['qcfname11'], FILTER_VALIDATE_EMAIL ) ) {
			$errors['qcfname11'] = '<p class="qcf-input-error"><span>' . $error['email'] . '</span></p>';
		}
	}
	if ( $qcf['fieldtype'] == 'ttele' && $qcf['active_buttons']['field11'] && $phonecheck == 'checked' && $values['qcfname11'] !== $qcf['label']['field11'] ) {
		if ( preg_match( "/[^0-9()\+\.\-\s]$/", $values['qcfname11'] ) ) {
			$errors['qcfname11'] = '<p class="qcf-input-error"><span>' . $error['telephone'] . '</span></p>';
		}
	}
	if ( $qcf['fieldtypeb'] == 'bmail' && $qcf['active_buttons']['field13'] && $values['qcfname13'] !== $qcf['label']['field13'] ) {
		if ( ! filter_var( $values['qcfname13'], FILTER_VALIDATE_EMAIL ) ) {
			$errors['qcfname13'] = '<p class="qcf-input-error"><span>' . $error['email'] . '</span></p>';
		}
	}
	if ( $qcf['fieldtypeb'] == 'btele' && $qcf['active_buttons']['field13'] && $phonecheck == 'checked' && $values['qcfname13'] !== $qcf['label']['field13'] ) {
		if ( preg_match( "/[^0-9()\+\.\-\s]$/", $values['qcfname11'] ) ) {
			$errors['qcfname13'] = '<p class="qcf-input-error"><span>' . $error['telephone'] . '</span></p>';
		}
	}
	foreach ( explode( ',', $qcf['sort'] ) as $name ) {
		if ( $qcf['active_buttons'][ $name ] && $qcf['required'][ $name ] ) {
			switch ( $name ) {
				case 'field1':
					$values['qcfname1'] = sanitize_text_field( $values['qcfname1']);
					if ( empty( $values['qcfname1'] ) || $values['qcfname1'] == $qcf['label'][ $name ] ) {
						$errors['qcfname1'] = '<p class="qcf-input-error"><span>' . $error['field1'] . '</span></p>';
					}
					break;
				case 'field2':
					$values['qcfname2'] = sanitize_text_field( $values['qcfname2'] );
					if ( empty( $values['qcfname2'] ) || $values['qcfname2'] == $qcf['label'][ $name ] || ! strpos( $values['qcfname2'], '.' ) ) {
						$errors['qcfname2'] = '<p class="qcf-input-error"><span>' . $error['field2'] . '</span></p>';
					}
					break;
				case 'field3':
					$values['qcfname3'] = sanitize_text_field( $values['qcfname3']);
					if ( empty( $values['qcfname3'] ) || $values['qcfname3'] == $qcf['label'][ $name ] ) {
						$errors['qcfname3'] = '<p class="qcf-input-error"><span>' . $error['field3'] . '</span></p>';
					}
					break;
				case 'field4':
					$values['qcfname4'] = strip_tags( stripslashes( $values['qcfname4'] ), $qcf['htmltags'] );
					if ( empty( $values['qcfname4'] ) || $values['qcfname4'] == $qcf['label'][ $name ] ) {
						$errors['qcfname4'] = '<p class="qcf-input-error"><span>' . $error['field4'] . '</span></p>';
					}
					break;
				case 'field5':
					if ( $qcf['selectora'] == 'checkboxa' ) {
						$check = '';
						$arr   = explode( ",", $qcf['dropdownlist'] );
						foreach ( $arr as $item ) {
							$check = $check . $values[ 'qcfname5_' . str_replace( ' ', '', $item ) ];
						}
						if ( empty( $check ) ) {
							$errors['qcfname5'] = '<p class="qcf-input-error"><span>' . $error['field5'] . '</span></p>';
						}
					} else {
						$values['qcfname5'] = filter_var( $values['qcfname5'], FILTER_SANITIZE_STRING );
						if ( empty( $values['qcfname5'] ) || $values['qcfname5'] == $qcf['label'][ $name ] && $qcf['selectora'] != 'radioa' ) {
							$errors['qcfname5'] = '<p class="qcf-input-error"><span>' . $error['field5'] . '</span></p>';
						}
					}
					break;
				case 'field6':
					if ( $qcf['selectorb'] == 'checkboxb' ) {
						$check = '';
						$arr   = explode( ",", $qcf['checklist'] );
						foreach ( $arr as $item ) {
							$check = $check . $values[ 'qcfname6_' . str_replace( ' ', '', $item ) ];
						}
						if ( empty( $check ) ) {
							$errors['qcfname6'] = '<p class="qcf-input-error"><span>' . $error['field6'] . '</span></p>';
						}
					} else {
						$values['qcfname6'] = filter_var( $values['qcfname6'], FILTER_SANITIZE_STRING );
						if ( empty( $values['qcfname6'] ) || $values['qcfname6'] == $qcf['label'][ $name ] && $qcf['selectorb'] != 'radiob' ) {
							$errors['qcfname6'] = '<p class="qcf-input-error"><span>' . $error['field6'] . '</span></p>';
						}
					}
					break;
				case 'field7':
					if ( $qcf['selectorc'] == 'checkboxc' ) {
						$check = '';
						$arr   = explode( ",", $qcf['radiolist'] );
						foreach ( $arr as $item ) {
							$check = $check . $values[ 'qcfname7_' . str_replace( ' ', '', $item ) ];
						}
						if ( empty( $check ) ) {
							$errors['qcfname7'] = '<p class="qcf-input-error"><span>' . $error['field7'] . '</span></p>';
						}
					} else {
						$values['qcfname7'] = filter_var( $values['qcfname7'], FILTER_SANITIZE_STRING );
						if ( empty( $values['qcfname7'] ) || $values['qcfname7'] == $qcf['label'][ $name ] && $qcf['selectorc'] != 'radioc' ) {
							$errors['qcfname7'] = '<p class="qcf-input-error"><span>' . $error['field7'] . '</span></p>';
						}
					}
					break;
				case 'field8':
					$values['qcfname8'] = filter_var( $values['qcfname8'], FILTER_SANITIZE_STRING );
					if ( empty( $values['qcfname8'] ) || $values['qcfname8'] == $qcf['label'][ $name ] ) {
						$errors['qcfname8'] = '<p class="qcf-input-error"><span>' . $error['field8'] . '</span></p>';
					}
					break;
				case 'field9':
					$values['qcfname9'] = filter_var( $values['qcfname9'], FILTER_SANITIZE_STRING );
					if ( empty( $values['qcfname9'] ) || $values['qcfname9'] == $qcf['label'][ $name ] ) {
						$errors['qcfname9'] = '<p class="qcf-input-error"><span>' . $error['field9'] . '</span></p>';
					}
					break;
				case 'field10':
					$values['qcfname10'] = filter_var( $values['qcfname10'], FILTER_SANITIZE_STRING );
					if ( empty( $values['qcfname10'] ) || $values['qcfname10'] == $qcf['label'][ $name ] ) {
						$errors['qcfname10'] = '<p class="qcf-input-error"><span>' . $error['field10'] . '</span></p>';
					}
					break;
				case 'field11':
					$values['qcfname11'] = filter_var( $values['qcfname11'], FILTER_SANITIZE_STRING );
					if ( empty( $values['qcfname11'] ) || $values['qcfname11'] == $qcf['label'][ $name ] ) {
						$errors['qcfname11'] = '<p class="qcf-input-error"><span>' . $error['field11'] . '</span></p>';
					}
					break;
				case 'field12':
					$values['qcfname12'] = filter_var( $values['qcfname12'], FILTER_SANITIZE_STRING );
					if ( $values['qcfname12'] <> $values['answer'] ) {
						$errors['qcfname12'] = '<p class="qcf-input-error"><span>' . $error['mathsanswer'] . '</span></p>';
					}
					if ( empty( $values['qcfname12'] ) ) {
						$errors['qcfname12'] = '<p class="qcf-input-error"><span>' . $error['mathsmissing'] . '</span></p>';
					}
					break;
				case 'field13':
					$values['qcfname13'] = filter_var( $values['qcfname13'], FILTER_SANITIZE_STRING );
					if ( empty( $values['qcfname13'] ) || $values['qcfname13'] == $qcf['label'][ $name ] ) {
						$errors['qcfname13'] = '<p class="qcf-input-error"><span>' . $error['field13'] . '</span></p>';
					}
					break;
				case 'field15':
					$values['qcfname15'] = filter_var( $values['qcfname15'], FILTER_SANITIZE_STRING );
					if ( empty( $values['qcfname15'] ) ) {
						$errors['qcfname15'] = '<p class="qcf-input-error"><span>' . $error['consent'] . '</span></p>';
					}
					break;
			}
		}
	}
	for ( $i = 1; $i <= $attach['qcf_number']; $i ++ ) {
		if ( ! isset( $_FILES[ 'filename' . $i ]['name'] ) || empty( $_FILES[ 'filename' . $i ]['name'] ) ) {
			continue;
		}
		$tmp_name = $_FILES[ 'filename' . $i ]['tmp_name'];
		$name     = $_FILES[ 'filename' . $i ]['name'];
		$size     = $_FILES[ 'filename' . $i ]['size'];
		if ( file_exists( $tmp_name ) ) {
			$found = 'checked';
			if ( $size > $attach['qcf_attach_size'] ) {
				$errors[ 'filename' . $i ] = '<p class="qcf-input-error"><span>' . $attach['qcf_attach_error_size'] . '</span></p>';
			}
			$ext = strtolower( substr( strrchr( $name, '.' ), 1 ) );
			if ( strpos( $attach['qcf_attach_type'], $ext ) === false ) {
				$errors[ 'filename' . $i ] = '<p class="qcf-input-error"><span>' . $attach['qcf_attach_error_type'] . '</span></p>';
			}
		}
	}
	if ( isset( $errors['attach'] ) && $errors['attach'] && $attach['qcf_number'] > 1 ) {
		$errors['filename1'] = '<p class="qcf-input-error"><span>' . $attach['qcf_attach_error'] . '</span></p>';
	}
	if ( isset( $attach['qcf_required'] ) && $attach['qcf_required'] && ! $found ) {
		$errors['filename1'] = '<p class="qcf-input-error"><span>' . $attach['qcf_attach_error_required'] . '</span></p>';
	}

	return ( count( $errors ) == 0 );
}
