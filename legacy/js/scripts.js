function retryValidation(link) {
    var main = jQuery(link).closest('.qcf-main');
    main.find('.qcf-state').hide();
    main.find('.qcf-form-wrapper').fadeIn('fast');
}


(function ($) {
    $(function () {

        /*
            Add jQuery ajax for form validation.
        */

        $('.qcf-form').on("submit", function (x) {
            x.preventDefault();

            // Contain parent form in a variable
            var f = $(this);


            // Intercept request and handle with AJAX
            console.log($(this).serialize());

            // var fd = $(this).serialize();
            var fp = f.closest('.qcf-main');


            var executes = 0;
            $('html, body').animate({
                    scrollTop: Math.max(fp.offset().top - 100, 0),
                }, 200, null, function () {

                    executes++;

                    if (executes >= 2) return false;

                    fp.find('.qcf-state').hide();
                    fp.find('.qcf-ajax-loading').show();

                    var form_data = new FormData(x.target);
                    //loop through all the input fields and get the file data incrementing the names
                    var i = 1;
                    fp.find('.qcf_filename_input').each(function () {
                        var file_data = $(this).prop('files')[0];
                        form_data.append('filename' + i, file_data);
                        i++;
                    });
                    form_data.append('action', 'qcf_validate_form');
                    form_data.append('url', window.location.href);

                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        contentType: false,
                        processData: false,
                        data: form_data,
                    }).done(qcf_ajax_success);

                    function qcf_ajax_success(e) {
                        const data = JSON.parse(e)
                        if (data.success !== undefined) {


                            /*
                                Quick validate file fields
                            */
                            var has_file_error = false;


                            if (data.errors.length) { // errors found

                                /* Remove all prior errors */
                                f.find('.qcf-input-error').remove();
                                f.find('.error').removeClass('error');

                                // Display error header
                                fp.find('.qcf-header').addClass('error').html(data.display);

                                fp.find('.qcf-blurb').addClass('error').html(data.blurb);

                                for (i = 0; i < data.errors.length; i++) {
                                    const error = data.errors[i];
                                    if (error.name == 'spam') {
                                        fp.find('.qcf-blurb').addClass('error').html(error.error);
                                        break;
                                    } else {
                                        element = f.find('[name=' + error.name + ']');
                                        element.addClass('error');
                                        if (error.name == 'qcfname12') {
                                            element.parent().prepend(error.error);
                                        } else {
                                            element.before(error.error);
                                        }

                                    }
                                }

                                fp.find('.qcf-state').hide();
                                fp.find('.qcf-form-wrapper').fadeIn('fast');
                            } else {
                                fp.find('.qcf-state').html(data.display);


                            }
                        } else {
                            // assume error so just show the form again.
                            fp.find('.qcf-state').hide();
                            $('.qcf-ajax-error').fadeIn('fast');
                        }

                        return false;
                    }
                    ;

                }
            )
            ;
            return false;
        });

        $('.qcfdate').datepicker({dateFormat: 'dd M yy'});

        $('body').on('click', '.qcf-retry', function () {
            retryValidation(this);
        });

        $('body').on('click', '.qcf-confirm', function () {
            return window.confirm($(this).data('confirm'));
        });


        $('body').on('focus', '.has-default', function () {
            if ($(this).val() == $(this).data('default')) {
                $(this).val(" ");
            }
            $(this).blur(function () {
                if ($.trim($(this).val()).length == 0) {
                    $(this).val($(this).data('default'));
                }
            });
        });

    });
})(jQuery);
