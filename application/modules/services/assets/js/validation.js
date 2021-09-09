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

            var form = $('#form_services');
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
                    form.submit(); // submit the form

                }

            });
    	}
	};
	}();
	
	if(('#form_services').length)
            FormLegal.init();
});
});
