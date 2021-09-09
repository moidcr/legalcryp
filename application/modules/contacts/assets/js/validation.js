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
		    
            var validator = form.validate({
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
	                
	                contact_apellative: {
                        required: "Por favor seleccione un apelativo"
                    },
                    contact_firstname: {
                        required: "Por favor ingrese un nombre"
                    },
                    contact_lastname: {
                    	required: "Por favor ingrese un apellido"
                    },
                    contact_surname: {
                    	required: "Por favor ingrese un segundo apellido"
                    },
                    contact_birthday: {
                    	required: "Por favor ingrese una fecha de nacimiento"
                    },
                    contact_personalid_type: {
                    	required: "Por favor ingrese un tipo de documento"
                    },
                    contact_personalid: {
                    	required: "Por favor ingrese una identificación"
                    },
                    contact_personalid_expiration: {
                    	required: "Por favor ingrese una fecha de vencimiento"
                    },
                    contact_profession_activity: {
                    	required: "Por favor ingrese una profesión"
                    },
                    contact_email_1: {
                    	required: "Por favor ingrese un correo",
                        email: "Por favor ingrese un correo válido"
                    },
                    contact_phone_code_1: {
                    	required: "Por favor ingrese un código de teléfono"
                    },
                    contact_phone_1: {
                    	required: "Por favor ingrese un número de teléfono"
                    }
                    
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
	
	if(('#form-contact').length)
            FormLocation.init();
});
