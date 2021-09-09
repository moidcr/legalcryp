/**
 * Validación de formularios con jquery
 * 
 * @author Dimsoft Dev Team
 * @since 9 jun 2016
 * 
 */

jQuery(document).ready(function() {
    var FormLocation = function () {

    return {
        init: function () {

            var form = $('#form-contact');
            
            var error = $('.alert-danger');
            $('input').iCheck({
                checkboxClass: 'icheckbox_minimal-grey'
            });
		    
            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                //errorElement: 'span', //default input error message container
                //errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    contact_apellative: {
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
                    	required: false
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
                    contact_email_1: {
                    	required: true,
                        email: true
                    },
                    contact_phone_code_1: {
                    	required: true
                    },
                    contact_phone_1: {
                    	required: true
                    }
                },

                messages: { // custom messages for radio buttons and checkboxes
                    
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    
                },

                invalidHandler: function (event, validator) { //display error alert on form submit   
                    //success.hide();
                    //error.show();
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
                    //error.hide();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                	form.submit(); // submit the form
                }

            });
    	}
	};
	}();
	
	if(('#form-contact').length)
            FormLocation.init();
});
