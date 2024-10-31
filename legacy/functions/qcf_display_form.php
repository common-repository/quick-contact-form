<?php

function qcf_display_form(  $values, $errors, $id  ) {
    /**
     * @var \Freemius $quick_contact_form_fs Object for freemius.
     */
    global $quick_contact_form_fs;
    $qcf_form = qcf_get_stored_setup();
    $qcf = qcf_get_stored_options( $id );
    $error = qcf_get_stored_error( $id );
    $reply = qcf_get_stored_reply( $id );
    $attach = qcf_get_stored_attach( $id );
    $style = qcf_get_stored_style( $id );
    $qcf['required']['field12'] = 'checked';
    $hd = ( $style['header-type'] ? $style['header-type'] : 'h2' );
    $content = '';
    if ( $id ) {
        $formstyle = $id;
    } else {
        $formstyle = 'default';
    }
    if ( !empty( $qcf['title'] ) ) {
        $qcf['title'] = '<' . $hd . ' class="qcf-header">' . $qcf['title'] . '</' . $hd . '>';
    }
    if ( !empty( $qcf['blurb'] ) ) {
        $qcf['blurb'] = '<p class="qcf-blurb">' . $qcf['blurb'] . '</p>';
    }
    if ( !empty( $qcf['mathscaption'] ) ) {
        $qcf['mathscaption'] = '<p class="input">' . $qcf['mathscaption'] . '</p>';
    }
    if ( isset( $errors['spam'] ) && $errors['spam'] ) {
        $error['errorblurb'] = $errors['spam'];
    }
    if ( count( $errors ) > 0 ) {
        $content = "<a id='qcf_reload'></a>";
    }
    $content .= '<div class="qcf-main qcf-style ' . $formstyle . '"><div id="' . $style['border'] . '">';
    $content .= '<div class="qcf-state qcf-ajax-loading qcf-style ' . $formstyle . '">' . apply_filters( 'qcf_validating_h2_markup', '<' . $hd . ' class="validating">' ) . $error['validating'] . apply_filters( 'qcf_validating_end_h2_markup', '</' . $hd . '>' ) . '</div>';
    $content .= '<div class="qcf-state qcf-ajax-error qcf-style ' . $formstyle . '"><div align="center">Ouch! There was a server error.<br /><a class="qcf-retry">Retry &raquo;</a></div></div>';
    $content .= '<div class="qcf-state qcf-sending qcf-style ' . $formstyle . '">' . apply_filters( 'qcf_sending_h2_markup', '<' . $hd . ' class="sending">' ) . $error['sending'] . apply_filters( 'qcf_sending_end_h2_markup', '</' . $hd . '>' ) . '</div>';
    $content .= "<div class='qcf-state qcf-form-wrapper'>\r\t";
    //  $content .= "<div id='" . $style['border'] . "'>\r\t";
    if ( count( $errors ) > 0 ) {
        $content .= "<" . $hd . " class='error'>" . $error['errortitle'] . "</" . $hd . ">\r\t<p class='error'>" . $error['errorblurb'] . "</p>\r\t";
    } else {
        $content .= $qcf['title'] . "\r\t" . $qcf['blurb'] . "\r\t";
    }
    $name = ( empty( $id ) ? 'default' : esc_attr( $id ) );
    $content .= "<form class='qcf-form' action=\"\" method=\"POST\" enctype=\"multipart/form-data\" id=\"" . wp_unique_id( 'qfc-form-' . esc_attr( $name ) . '-' ) . "\">\r\t";
    $content .= "<input type='hidden' name='id' value='{$id}' />\r\t";
    foreach ( explode( ',', $qcf['sort'] ) as $name ) {
        $required = ( $qcf['required'][$name] ? 'required' : '' );
        if ( $qcf['active_buttons'][$name] == "on" ) {
            switch ( $name ) {
                case 'field1':
                    list( $required, $content ) = qcf_form_field_error( $errors, 'qcfname1', $content );
                    $content .= '<input type="text" id="qcf-form-field-id-' . $id . '-1" placeholder="' . $values['qcfname1'] . '" class="qcf-form-field' . $required . '" name="qcfname1" value="" >' . "\r\t";
                    break;
                case 'field2':
                    list( $required, $content ) = qcf_form_field_error( $errors, 'qcfname2', $content );
                    $content .= '<input type="email" id="qcf-form-field-id-' . $id . '-2" placeholder="' . $values['qcfname2'] . '" class="qcf-form-field' . $required . '" name="qcfname2"  value="" >' . "\r\t";
                    break;
                case 'field3':
                    list( $required, $content ) = qcf_form_field_error( $errors, 'qcfname3', $content );
                    $content .= '<input type="text" id="qcf-form-field-id-' . $id . '-3" placeholder="' . $values['qcfname3'] . '" class="qcf-form-field' . $required . '" name="qcfname3"  value="" >' . "\r\t";
                    break;
                case 'field4':
                    list( $required, $content ) = qcf_form_field_error( $errors, 'qcfname4', $content );
                    $content .= '<textarea id="qcf-form-field-id-' . $id . '-4" placeholder="' . $values['qcfname4'] . '" class="qcf-form-field' . $required . '"  rows="' . $qcf['lines'] . '" name="qcfname4"></textarea>' . "\r\t";
                    break;
                case 'field5':
                    if ( isset( $errors['qcfname5'] ) && $errors['qcfname5'] ) {
                        $required = 'error';
                    }
                    if ( $qcf['selectora'] == 'dropdowna' ) {
                        $content .= qcf_dropdown(
                            'qcfname5',
                            'dropdownlist',
                            $values,
                            $errors,
                            $required,
                            $qcf,
                            $name
                        );
                    }
                    if ( $qcf['selectora'] == 'checkboxa' ) {
                        $content .= qcf_checklist(
                            'qcfname5',
                            'dropdownlist',
                            $values,
                            $errors,
                            $required,
                            $qcf,
                            $name
                        );
                    }
                    if ( $qcf['selectora'] == 'radioa' ) {
                        $content .= qcf_radio(
                            'qcfname5',
                            'dropdownlist',
                            $values,
                            $errors,
                            $required,
                            $qcf,
                            $name
                        );
                    }
                    break;
                case 'field6':
                    if ( isset( $errors['qcfname6'] ) && $errors['qcfname6'] ) {
                        $required = 'error';
                    }
                    if ( $qcf['selectorb'] == 'dropdownb' ) {
                        $content .= qcf_dropdown(
                            'qcfname6',
                            'checklist',
                            $values,
                            $errors,
                            $required,
                            $qcf,
                            $name
                        );
                    }
                    if ( $qcf['selectorb'] == 'checkboxb' ) {
                        $content .= qcf_checklist(
                            'qcfname6',
                            'checklist',
                            $values,
                            $errors,
                            $required,
                            $qcf,
                            $name
                        );
                    }
                    if ( $qcf['selectorb'] == 'radiob' ) {
                        $content .= qcf_radio(
                            'qcfname6',
                            'checklist',
                            $values,
                            $errors,
                            $required,
                            $qcf,
                            $name
                        );
                    }
                    break;
                case 'field7':
                    if ( isset( $errors['qcfname7'] ) && $errors['qcfname7'] ) {
                        $required = 'error';
                    }
                    if ( $qcf['selectorc'] == 'dropdownc' ) {
                        $content .= qcf_dropdown(
                            'qcfname7',
                            'radiolist',
                            $values,
                            $errors,
                            $required,
                            $qcf,
                            $name
                        );
                    }
                    if ( $qcf['selectorc'] == 'checkboxc' ) {
                        $content .= qcf_checklist(
                            'qcfname7',
                            'radiolist',
                            $values,
                            $errors,
                            $required,
                            $qcf,
                            $name
                        );
                    }
                    if ( $qcf['selectorc'] == 'radioc' ) {
                        $content .= qcf_radio(
                            'qcfname7',
                            'radiolist',
                            $values,
                            $errors,
                            $required,
                            $qcf,
                            $name
                        );
                    }
                    break;
                case 'field8':
                    list( $required, $content ) = qcf_form_field_error( $errors, 'qcfname8', $content );
                    $content .= '<input type="text"  id="qcf-form-field-id-' . $id . '-8" placeholder="' . $values['qcfname8'] . '" class="qcf-form-field' . $required . '" name="qcfname8"  value=""  >' . "\r\t";
                    break;
                case 'field9':
                    list( $required, $content ) = qcf_form_field_error( $errors, 'qcfname9', $content );
                    $content .= '<input type="text" id="qcf-form-field-id-' . $id . '-9" placeholder="' . $values['qcfname9'] . '" class="qcf-form-field' . $required . '" name="qcfname9"  value="" >' . "\r\t";
                    break;
                case 'field10':
                    list( $required, $content ) = qcf_form_field_error( $errors, 'qcfname10', $content );
                    $content .= '<input type="text" id="qcf-form-field-id-' . $id . '-10" placeholder="' . $values['qcfname10'] . '" class="qcf-form-field qcfdate ' . $required . '" name="qcfname10"  value=""  />';
                    break;
                case 'field11':
                    list( $required, $content ) = qcf_form_field_error( $errors, 'qcfname11', $content );
                    if ( $qcf['fieldtype'] == 'tdate' ) {
                        $content .= '<input type="text" id="qcf-form-field-id-' . $id . '-11" placeholder="' . $values['qcfname11'] . '" class="qcf-form-field qcfdate ' . $required . '" name="qcfname11"  value=""  /></p>';
                    } else {
                        $content .= '<input type="text" id="qcf-form-field-id-' . $id . '-11" placeholder="' . $values['qcfname11'] . '" class="qcf-form-field' . $required . '" label="Multibox 1" name="qcfname11" value="" ><br>' . "\r\t";
                    }
                    break;
                case 'field13':
                    list( $required, $content ) = qcf_form_field_error( $errors, 'qcfname13', $content );
                    if ( $qcf['fieldtypeb'] == 'bdate' ) {
                        $content .= '<input type="text" id="qcf-form-field-id-' . $id . '-13" placeholder="' . $values['qcfname13'] . '" class="qcf-form-field qcfdate ' . $required . '" name="qcfname13"  value="" >';
                    } else {
                        $content .= '<input type="text" id="qcf-form-field-id-' . $id . '-13" placeholder="' . $values['qcfname13'] . '" class="qcf-form-field' . $required . '" name="qcfname13" value="" >' . "\r\t";
                    }
                    break;
                case 'field12':
                    if ( isset( $errors['qcfname12'] ) && $errors['qcfname12'] ) {
                        $required = 'error';
                    }
                    if ( isset( $errors['qcfname12'] ) && $errors['qcfname12'] ) {
                        $content .= $errors['qcfname12'];
                    } else {
                        $content .= '<p>' . $qcf['label']['field12'] . '</p>';
                    }
                    $content .= '<p>' . strip_tags( $values['thesum'] ) . ' = <input type="text" class="' . $required . '" style="width:3em;font-size:inherit;" name="qcfname12"  value="' . strip_tags( $values['qcfname12'] ) . '"></p> 
                <input type="hidden" name="answer" value="' . strip_tags( $values['answer'] ) . '" />
                <input type="hidden" name="thesum" value="' . strip_tags( $values['thesum'] ) . '" />';
                    break;
                case 'field14':
                    $content .= '<p>' . $qcf['label']['field14'] . '</p>
                <input type="range" class="qcf-range-slider-control" name="qcfname14" min="' . $qcf['min'] . '" max="' . $qcf['max'] . '" value="' . $qcf['initial'] . '" step="' . $qcf['step'] . '" data-rangeslider>
                <div class="qcf-slideroutput">';
                    if ( $qcf['output-values'] ) {
                        $content .= '<span class="qcf-sliderleft">' . $qcf['min'] . '</span>
                    <span class="qcf-slidercenter"><output></output></span>
                    <span class="qcf-sliderright">' . $qcf['max'] . '</span>';
                    } else {
                        $content .= '<span class="qcf-outputcenter"><output></output></span>';
                    }
                    $content .= '</div><div style="clear: both;"></div>';
                    break;
                case 'field15':
                    list( $required, $content ) = qcf_form_field_error( $errors, 'qcfname15', $content );
                    $content .= '<input type="checkbox" name="qcfname15"  value="checked"  />&nbsp;' . $qcf['label']['field15'] . "\r\t";
                    break;
            }
        }
    }
    if ( $attach['qcf_attach'] == "checked" ) {
        /*
        	@Change
        	@Add <div> around file inputs
        */
        $content .= '<div>';
        /*
        	@Change
        	@Add <script> block with a simple file info object
        */
        $qfc_file_info = (object) array(
            'required'       => (int) (( $attach['qcf_required'] == "checked" ? 1 : 0 )),
            'types'          => explode( ',', $attach['qcf_attach_type'] ),
            'max_size'       => (int) $attach['qcf_attach_size'],
            'error'          => $attach['qcf_attach_error'],
            'error_size'     => $attach['qcf_attach_error_size'],
            'error_type'     => $attach['qcf_attach_error_type'],
            'error_required' => $attach['qcf_attach_error_required'],
        );
        $script = 'if (typeof qfc_file_info == "undefined") { var qfc_file_info = ' . json_encode( $qfc_file_info ) . ';}';
        wp_add_inline_script( 'qcf_script', $script, 'before' );
        if ( isset( $errors['attach'] ) && !empty( $errors['attach'] ) ) {
            $content .= $errors['attach'];
        } else {
            $content .= '<p class="input">' . $attach['qcf_attach_label'] . '</p>' . "\r\t" . '<p>';
        }
        $size = $attach['qcf_attach_width'];
        $content .= '<div name="attach">';
        for ($i = 1; $i < $attach['qcf_number']; $i++) {
            $content .= '<input type="file" size="' . $size . '" name="filename' . $i . '" class="qcf_filename_input qcf_filename' . $i . '"/><br>';
        }
        $content .= '<input type="file" size="' . $size . '" name="filename' . $attach['qcf_number'] . '" class="qcf_filename_input qcf_filename' . $i . '"/></p>';
        $content .= '</div></div>';
    }
    $caption = $qcf['send'];
    if ( $style['submit-button'] ) {
        $content .= '<p><input type="image" value="' . $caption . '" src="' . $style['submit-button'] . '" id="submit" name="qcfsubmit' . $id . '" /></p>';
    } else {
        $content .= '<p><input type="submit" value="' . $caption . '" id="submit" name="qcfsubmit' . $id . '" /></p>';
    }
    $content .= '<input type="hidden" name="form_id" value="' . uniqid() . wp_unique_id() . '" />';
    $content .= '</form></div>' . "\r\t" . '<div style="clear:both;"></div></div>' . "\r\t" . '</div>' . "\r\t";
    // close
    if ( count( $errors ) > 0 ) {
        wp_add_inline_script( 'qcf_script', 'document.getElementById("qcf_reload").scrollIntoView();' );
    }
    echo qcf_kses_forms( $content );
}
