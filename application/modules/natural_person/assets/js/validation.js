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
           /* $('input').iCheck({
                checkboxClass: 'icheckbox_minimal-grey'
            });*/
		    
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
                    },*/

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
                    /*price_id: {
                    	required: true
                    },*/
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
                    /*address_invoice: {
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
                    },*/
                    
                },

                messages: { // custom messages for radio buttons and checkboxes
                     /*account_id: {
                        required: true
                    },
                    status: {
                        required: true
                    },*/
                    birthday: {
                    	required: "Por favor indique una fecha de nacimiento"
                    },
                    personalid_type: {
                    	required: "Por favor indique un tipo de identificación"
                    },
                    personalid: {
                    	required: "Por favor indique un numero de documento"
                    },
                    personalid_expiration: {
                    	required: "Por favor indique un fecha de expiración"
                    },
                    /*price_id: {
                    	required: true
                    },*/
                    countrybirth: {
                    	required: "Por favor indique un país de nacimiento"
                    },
                    citizenship_id: {
                    	required: "Por favor indique una nacionalidad"
                    },
                    country_residence: {
                    	required: "Por favor indique un país de residencia"
                    },
                    city_residence: {
                    	required: "Por favor indique una ciudad de residencia"
                    },
                    address_residence: {
                    	required: "Por favor indique una dirección"
                    },
                    /*address_invoice: {
                    	required: true
                    },*/
                    /*phone_code_1: {
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
                    },*/
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

                /*submitHandler: function (form) {
                    //success.show();
                    //error.hide();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                	form.submit(); // submit the form
                }*/

            });
            
            $("#addbtn").click(function() {
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