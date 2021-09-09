/**
 * Validación de formularios con jquery
 *
 * @author Dimsoft Dev Team
 * @since 9 jun 2016
 *
 */


$('#save').click(function() {
jQuery(document).ready(function() {
    var FormLegal = function () {

    return {
        init: function () {

            var form = $('#form_legal_case');
            var error = $('.alert-danger');
//            var success = $('.alert-success', form);

//            $('input').iCheck({
//                checkboxClass: 'icheckbox_minimal-grey'
//            });

            form.validate({

                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
//                    society_pty_field_spty_name: {
//                        required: true
//                    },

                },

                messages: { // custom messages for radio buttons and checkboxes

                },

                errorPlacement: function (error, element) { // render error placement for each input type

                },

                invalidHandler: function (event, validator) { //display error alert on form submit
 //                   success.hide();

//                    var errors = validator.numberOfInvalids();
//                    if (errors) {
//                      var message = errors == 1
//                        ? 'Please correct the following error:\n'
//                        : 'Please correct the following ' + errors + ' errors.\n';
//                      var errors = "";
//                      if (validator.errorList.length > 0) {
//                          for (x=0;x<validator.errorList.length;x++) {
//                              errors += "\n\u25CF " + validator.errorList[x].message;
//                          }
//                      }
//                      alert(message + errors);
//                    }


                    error.show();
                    Metronic.scrollTo(error, -200);

                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label.addClass('valid') // mark the current input as valid and display OK icon
                        .closest('.form-group').removeClass('error').addClass('has-success');
                },

                submitHandler: function (form) {
                    //success.show();
                    error.hide();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                    //form.submit(); // submit the form

                    var items = [];
                    var item_order = 1;

                    $('#item_table tr.item').each(function() {
                        var row = {};
                           $(this).find('input,select,textarea').each(function() {
                            if ($(this).is(':checkbox')) {
                                row[$(this).attr('name')] = $(this).is(':checked');
                            } else {
                                row[$(this).attr('name')] = $(this).val();
                            }
                        });
                        row['lci_item_order'] = item_order;

                        row['lci_item_discount_percent'] = parseFloat(row['lci_item_discount_percent'].replace(",",""));
                        /*if(parseFloat(row['lci_item_discount_percent'].replace(",","")) > 0){
                            let discount = parseFloat(row['lci_item_price'].replace(",","")) * (parseFloat(row['lci_item_discount_percent'].replace(",","")) / 100);
                            row['lci_item_discount_temp'] = discount;
                            console.log(row['lci_item_price'],discount);
                        }*/
                        
                        item_order++;
                        items.push(row);
                    });


                    $.post(base_url + "index.php/admin/cases/legal_cases/ajax_save", {
                        ci_csrf_token:ci_csrf_token(),
                        lg_id: $('#lg_id').val(),
                        lg_status: $('#lg_status').val(),
                        lg_name: $('#lg_name').val(),
                        lg_comments: $('#lg_comments').val(),
                        lg_service: $('#lg_service').val(),
                        lg_file: $('#lg_file').val(),
                        lg_attorney_id: $('#lg_attorney_id').val(),
                        lg_requested_by: $('#lg_requested_by').val(),
                        lg_assistant: $('#lg_assistant').val(),
                        lg_person_id: $('#lg_person_id').val(),
                        lg_jurisdiction_id: $('#lg_jurisdiction_id').val(),
                        lg_start_date: $('#lg_start_date').val(),
                        lg_end_date: $('#lg_end_date').val(),
                        lg_close_date: $('#lg_close_date').val(),
                        lg_subtotal: parseFloat($('#lg_subtotal').val().replace(/,/g, '')),
                        lg_desc_porc: $('#lg_desc_porc').val(),
                        lg_descuento: parseFloat($('#lg_descuento').val().replace(/,/g, '')),
                        lg_imp_porc: $('#lg_imp_porc').val(),
                        lg_impuesto: parseFloat($('#lg_impuesto').val().replace(/,/g, '')),
                        lg_total: parseFloat($('#lg_total').val().replace(/,/g, '')),
                        items: JSON.stringify(items)
                    },
                    function(data) {
                        var response = JSON.parse(data);

                        if (response.success == '1') {
                            window.location = base_url + "index.php/admin/cases/legal_cases/edit/" + response.last_id;
                        }
                        else {

//                                $('.control-group').removeClass('error');
//                                for (var key in response.validation_errors) {
//                                    $('#' + key).parent().parent().addClass('error');
//                                }
                        }
                    });

                }

            });
    	}
	};
	}();

	if(('#form_legal_case').length)
            FormLegal.init();
});
});
