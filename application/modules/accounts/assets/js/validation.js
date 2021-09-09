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

            var form = $('#form-account');
            
            var error = $('.alert-danger');
            
            $('input').iCheck({
                checkboxClass: 'icheckbox_minimal-grey'
            });
		    
            form.validate({
                doNotHideMessage: true, 
                focusInvalid: false,
                rules: {
                    //account
                    account_name: {
                        required: true
                    }
                },

                messages: {
                    
                },

                errorPlacement: function (error, element) {
                    
                },

                invalidHandler: function (event, validator) {
                    Metronic.scrollTo(error, -200);
                },

                highlight: function (element) {
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
                    form.submit(); // submit the form
                }

            });
    	}
	};
	}();
	
	if(('#form-contact').length)
            FormLocation.init();
});
