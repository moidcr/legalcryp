/**
 * Validación de formularios con jquery
 * 
 * @author Dimsoft Dev Team
 * @since 9 jun 2016
 * 
 */

jQuery(document).ready(function() {
    var FormSociety = function () {

    return {
        init: function () {

            var form = $('#form_societypty');
            var error = $('.alert-danger');
//            var success = $('.alert-success', form);
            
//            $('input').iCheck({
//                checkboxClass: 'icheckbox_minimal-grey'
//            });
		    
            var validator = form.validate({
                
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                   soc_person_id: {
                        required: true
                    },
                    soc_type: {
                        required: true
                    },
                    soc_contact_id: {
                        required: true
                    },
                    soc_public_deed: {
                        required: true
                    },
                    soc_deed_date: {
                        required: true
                    },
                    soc_notary: {
                        required: true
                    },
                    soc_incription_date: {
                        required: true
                    },
                    soc_electronic_folio: {
                        required: true
                    },
                    soc_ruc: {
                        required: true
                    },
                    soc_legal_representative: {
                        required: true
                    },
                    soc_action_types: {
                        required: true
                    },
                    soc_power_type: {
                        required: true
                    }
                },

                messages: { // custom messages for radio buttons and checkboxes
                    soc_person_id: {
                        required: "Por favor seleccione un nombre"
                    },
                    soc_type: {
                        required:  "Por favor seleccione un tipo"
                    },
                    soc_contact_id: {
                        required: "Por favor seleccione un contacto"
                    },
                    soc_public_deed: {
                        required: "Por favor ingrese una escritura"
                    },
                    soc_deed_date: {
                        required: "Por favor ingrese una fecha de scritura"
                    },
                    soc_notary: {
                        required: "Por favor ingrese una notaría"
                    },
                    soc_incription_date: {
                        required: "Por favor seleccione una fecha de inscripción"
                    },
                    soc_electronic_folio: {
                        required: "Por favor ingrese un folio electrónico"
                    },
                    soc_ruc: {
                        required: "Por favor ingrese un RUC"
                    },
                    soc_legal_representative: {
                        required: "Por favor ingrese un representante legal"
                    },
                    soc_action_types: {
                        required: "Por favor ingrese un tipo de acciones"
                    },
                    soc_power_type: {
                        required: "Por favor seleccione un tipo de poder"
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
//                    success.show();
                    error.hide();
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
	
	if(('#form_societypty').length)
            FormSociety.init();
});
