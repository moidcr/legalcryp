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

            var form = $('#form-person-create');
            
            var error = $('.alert-danger');
            $('input').iCheck({
                checkboxClass: 'icheckbox_minimal-grey'
            });
		    
            var validator = form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    /*account_id: {
                        required: true
                    },
                    status: {
                        required: true
                    },
                    razonsocial: {
                    	required: true
                    },
                    ruc: {
                    	required: true
                    },
                    ruc_dv: {
                    	required: true
                    },
                    price_id: {
                    	required: true
                    },*/
                    razoncomercial: {
                    	required: true
                    },
                    country_id: {
                    	required: true
                    },
                    city: {
                    	required: true
                    },
                    address_1: {
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
                },

                messages: { // custom messages for radio buttons and checkboxes
                    /*account_id: {
                        required: "Por favor seleccione un grupo"
                    },
                    status: {
                        required: "Por favor indique un Estatus"
                    },
                    
                    ruc: {
                    	required: "Por favor indique un RUC"
                    },
                    ruc_dv: {
                    	required: "Por favor indique un DV"
                    },
                    price_id: {
                    	required: "Por favor indique un tipo de precio"
                    },*/
                    razoncomercial: {
                    	required: "Por favor indique una razón comercial"
                    },
                    country_id: {
                    	required: "Por favor indique un país"
                    },
                    city: {
                    	required: "Por favor indique una ciudad"
                    },
                    address_1: {
                    	required: "Por favor indique una dirección"
                    },
                    /*phone_code_1: {
                    	required: true
                    },*/
                    phone_1: {
                    	required: "Por favor indique un número de  teléfono"
                    },
                    email_1: {
                    	required: "Por favor indique un correo",
                        email: "Por favor indique un correo válido"
                    },
                },
                
                errorElement: 'span',
			    errorPlacement: function (error, element) {
			      error.addClass('invalid-feedback');
			      element.closest('.form-group > .valmsn').append(error);
			
			    },

                highlight: function (element, errorClass, validClass) {
			      $(element).addClass('is-invalid');
			    },
			    unhighlight: function (element, errorClass, validClass) {
			      $(element).removeClass('is-invalid');
			    },

                /*errorPlacement: function (error, element) { // render error placement for each input type
                    
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
                }*/

            });
            
            $("#save").click(function() {
			     if (form.valid()) 
			        console.log("Valid!");
			      else
			      {
				      event.preventDefault();
					event.stopPropagation();
					//  console.log("test")
					var errorElements = document.querySelectorAll(".is-invalid");
					console.log(errorElements);
					for (let index = 0; index < errorElements.length; index++) {
						const element = errorElements[index];
						//  console.log(element);
						$('html, body').animate({
						scrollTop: $(errorElements[0]).focus().offset().top-100
					}, 1000);
					
			        
					return false;
					}
			        validator.focusInvalid();
			        
				  }
				  
				
			  });
    	}
	};
	}();
	
	if(('#form-person-create').length)
            FormLocation.init();
});