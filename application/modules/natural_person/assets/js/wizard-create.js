/**
 * Wizard para registro de persona juridica y contacto.
 * @author Dimsoft Dev Team
 * @since 11 jun 2016
 */

var FormWizard = function () {

    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }
            

            var form = $('#form-person-create');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            
            // Definición de mascaras.
    
            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    account_id: {
                        required: true
                    },
                    status: {
                        required: true
                    },
                    apellative:{
                        required:true
                    },
                    firstname: {
                    	required: true
                    },
                    lastname: {
                    	required: true
                    },
                    secondlastname: {
                    	required: true
                    },
                    birthday: {
                    	required: true
                    },
                    personalid_type: {
                    	required: true
                    },
                    personalid: {
                    	required: true
                    },
                    personalid_expiration: {
                    	required: true
                    },
                    price_id: {
                    	required: true
                    },
                    countrybirth: {
                    	required: true
                    },
                    citizenship_id: {
                    	required: true
                    },
                    country_residence: {
                    	required: true
                    },
                    city_residence: {
                    	required: true
                    },
                    address_residence: {
                    	required: true
                    },
                    address_invoice: {
                    	required: true
                    },
                    phone_code_1: {
                    	required: true
                    },
                    phone_1: {
                    	required: true
                    },
                    email_1: {
                    	required: true,
                        email: true
                    },
                    description_activity: {
                    	required: true
                    },
                    contact_firstname: {
                        required: true
                    },
                    contact_lastname: {
                    	required:true
                    },
                    contact_surname: {
                    	required: true
                    },
                    contact_birthday: {
                    	required: true
                    },
                    contact_marital_status: {
                    	required: true
                    },
                    contact_personalid_type: {
                    	required: true
                    },
                    contact_personalid: {
                    	required: true
                    },
                    contact_personalid_expiration: {
                    	required: true
                    },
                    contact_profession_activity: {
                    	required: true
                    },
                    contact_address_1: {
                    	required: true
                    },
                    contact_phone_code_1: {
                    	required: true
                    },
                    contact_phone_1: {
                    	required: true
                    },
                    contact_phone_code_2: {
                    	required: false
                    },
                    contact_phone_2: {
                    	required: false
                    },
                    contact_email_1: {
                    	required: true,
                        email: true
                    },
                },

                messages: { // custom messages for radio buttons and checkboxes
                    
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    
                },

                invalidHandler: function (event, validator) { //display error alert on form submit   
                    success.hide();
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
                        .closest('.form-group').removeClass('has-error').addClass('has-success');
                },

                submitHandler: function (form) {
                    
                    success.show();
                    error.hide();
                    
                    if (form.valid() == false) {
                        
                        return false;
                    }
                    else {
                       form.submit();
                       
                       return true;
                    }
                   
                    
                }

            });

            var displayConfirm = function() {
                $('#tab4 .form-control-static', form).each(function(){
                    var input = $('[name="'+$(this).attr("data-display")+'"]', form);
                    
                    if (input.is(":radio")) {
                        input = $('[name="'+$(this).attr("data-display")+'"]:checked', form);
                    }
                    if (input.is(":checkbox")) {
                        input = $('[name="'+$(this).attr("data-display")+'"]:checked', form);
                    }
                    
                   //alert($(this).attr("data-display"));
                    if (input.is(":text") || input.is("textarea") || input.is("hidden")) {
                        $(this).html(input.val());
                    } else if (input.is("select")) {
                        $(this).html(input.find('option:selected').text());
                    } else if (input.is(":radio") && input.is(":checked")) {
                        $(this).html(input.attr("data-title"));
                    }
                    
                    if (input.is(":checkbox") && input.is(":checked")) {
                    	 $(this).html(input.attr('data-label'));

                    }
                    
                });
            };

            var handleTitle = function(tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                // set wizard title
                $('.step-title', $('#wizard-form')).text('Paso ' + (index + 1) + ' de ' + total);
                // set done steps
                jQuery('li', $('#wizard-form')).removeClass("done");
                var li_list = navigation.find('li');
                for (var i = 0; i < index; i++) {
                    jQuery(li_list[i]).addClass("done");
                }

                if (current == 1) {
                    $('#wizard-form').find('.button-previous').hide();
                } else {
                    $('#wizard-form').find('.button-previous').show();
                }

                if (current >= total) {
                    $('#wizard-form').find('.button-next').hide();
                    $('#wizard-form').find('.button-submit').show();
                    //displayConfirm();
                } else {
                    $('#wizard-form').find('.button-next').show();
                    $('#wizard-form').find('.button-submit').hide();
                }
                
                Metronic.scrollTo($('.page-title'));
            };

            // default form wizard
            $('#wizard-form').bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function (tab, navigation, index, clickedIndex) {
                    return false;
                    /*
                    success.hide();
                    error.hide();
                    if (form.valid() == false) {
                        return false;
                    }
                    handleTitle(tab, navigation, clickedIndex);
                    */
                },
                onNext: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    if (form.valid() == false) {
                        return false;
                    }
                    
                        
                    handleTitle(tab, navigation, index);
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    handleTitle(tab, navigation, index);
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    $('#wizard-form').find('.progress-bar').css({
                        width: $percent + '%'
                    });
                }
            });

            $('#wizard-form').find('.button-previous').hide();
            $('#save').hide();
            
            /*
            $('#save').click(function () {
                if (form.valid() == false) {
                    
                    alert("error")
                    return false;
                }
                else {
                    success.hide();
                    error.hide();
                    
                    form.submit();
                    
                    
                    return true;
                }
            });
               */
        }

    };

}();

FormWizard.init();