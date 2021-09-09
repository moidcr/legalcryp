
if($('#soc_deed_date').length)
    $('#soc_deed_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#soc_incription_date').length)
    $('#soc_incription_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#soc_pi_end_date').length)
    $('#soc_pi_end_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#soc_pdn_deed_date').length)
    $('#soc_pdn_deed_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#soc_pdn_power_due_date').length)
    $('#soc_pdn_power_due_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#soc_pdr_deed_date').length)
    $('#soc_pdr_deed_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#soc_pdr_deed_reg_date').length)
    $('#soc_pdr_deed_reg_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#soc_pdr_power_due_date').length)
    $('#soc_pdr_power_due_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

/**
 * Data Table para el index
 */

var TableDatatablesAjax = function () {

    var handleRecords = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#datatable_society_pty"),
            onSuccess: function (grid, response) {
                // grid:        grid object
                // response:    json object of server side ajax response
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            onDataLoad: function(grid) {

                // execute some code on ajax data load
            },

            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",

                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

                "buttons": [
                  {
                    extend:'excel',
                    exportOptions: {
                      columns: [ 1, 2, 3, 4, 5, 6 ]
                    }
                  },{
                    extend:'pdf',
                    exportOptions: {
                      columns: [ 1, 2, 3, 4, 5, 6 ]
                    }
                  },{
                    extend:'print',
                    exportOptions: {
                      columns: [ 1, 2, 3, 4, 5, 6 ]
                    }
                  }
                ],

                "lengthMenu": [
                    [10, 15, 30],
                    [10, 15, 30] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": base_url + "index.php/admin/files/society_pty/ajax_get", // ajax source

                },
                "colReorder": {
                    order: [ 0, 1, 2, 3 ]
                },
                "order": [
                    [1, "asc"]
                ]// set first column as a default sort by asc
                , 
		        drawCallback: function(dt) {
		          console.log("draw() callback; initializing Select2's.");
		          if($("#soc_type").length == 0)
		          {
		          	$("#selop").html('<select id = "soc_type" class="experience-jquerySelect2-tag" onchange="select_society()" style="width: 100%;"><option value="">::Tipo de Sociedad::</option><option value="Sociedad Anónima">Sociedad Anónima</option><option value="Sociedad de Responsabilidad Limitada">Sociedad de Responsabilidad Limitada</option><option value="Sociedad Civil">Sociedad Civil</option><option value="Sucursal de Sociedad Extranjera">Sucursal de Sociedad Extranjera</option><option value="Propiedad Horizontal">Propiedad Horizontal</option><option value="Naves">Naves</option></select>');
				  	$('.experience-jquerySelect2-tag').select2({tags: true, width: "100%"});
				  	$(".table-group-action-submit").trigger('click');
		          }
		        }
            }
        });

        // Evento enter
        grid.getTableWrapper().on('keyup', '.table-group-action-input', function (e) {
            var code = e.which;
            if(code==13)e.preventDefault();
            if(code==32||code==13||code==188||code==186) {
                
            }
        });
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {

            e.preventDefault();
            grid.setAjaxParam("ci_csrf_token", ci_csrf_token());
            console.log($(".table-group-action-input").val());
            var action = $(".table-group-action-input").val().replace(/[|&;$%@"<>()+,]/g, "").trim();
            console.log($(".table-group-action-input").val());
			var soct_type_ = $("#soc_type").val();
            //if (action) {
                //grid.setAjaxParam("id", grid.getSelectedRows());
                grid.setAjaxParam("filter", action);
                
                grid.setAjaxParam("soct_type_", soct_type_);

                grid.getDataTable().ajax.reload();
                //grid.clearAjaxParams();
            //} else {
                /*
                App.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Debe proporcionar un texto de búsqueda',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
                */
           // }
        });

        grid.getTableWrapper().on('click', '.table-group-action-reset', function (e) {
            $(".table-group-action-input").val("");
            e.preventDefault();
            grid.setAjaxParam("ci_csrf_token", ci_csrf_token());
            grid.getDataTable().ajax.reload();
            grid.clearAjaxParams();
        });

        grid.setAjaxParam("ci_csrf_token", ci_csrf_token());
        //grid.setAjaxParam("customActionType", "group_action");
        //grid.getDataTable().ajax.reload();
        //grid.clearAjaxParams();
    }

    return {

        //main function to initiate the module
        init: function () {

            handleRecords();
        }

    };

}();

jQuery(document).ready(function() {
    TableDatatablesAjax.init();
    setInterval(function() {$('.select2-container--bootstrap').css("width", "100%")}, 500);
    $('#soc_society_activities').summernote();
    $('#soc_comments').summernote();
    $('#soc_society_activities').summernote('disable');
    $('#soc_comments').summernote('disable');
});


//COMBO CONTACT
$(document).on('change','#soc_person_id',function(){

            $.ajax({ url: base_url + "index.php/admin/files/society_pty/person_contacts",
               type: "POST",
               data: {ci_csrf_token:ci_csrf_token(),
                      person_id: $("#soc_person_id").val()},
                success: function(data){

                        var response = JSON.parse(data);

                        $('#soc_contact_id').empty();

                        jQuery('<option/>', {
                        value: '',
                        html: ''
                        }).appendTo('#soc_contact_id');

                        if (response != null) {


                            for(var i=0; i< response.length;i++)
                            {
                            //creates option tag
                              jQuery('<option/>', {
                                    value: response[i].contact_id,
                                    html: response[i].fullname
                                    }).appendTo('#soc_contact_id'); //appends to select if parent div has id dropdown
                            }

                        }

                        $('#select2-soc_contact_id-container').val('');
                        $('#select2-soc_contact_id-container').text('');


                    }

            })


});




$(document).on('change','#soc_power_type',function(){

        active_power();

        $('#soc_private_instrument').attr('value', "");
        $('#soc_pi_end_date').attr('value', "");

        $('#soc_pdn_deed_number').attr('value', "");
        $('#soc_pdn_deed_date').attr('value', "");
        $('#soc_pdn_notary').attr('value', "");
        $('#soc_pdn_power_due_date').attr('value', "");

        $('#soc_pdr_deed_number').attr('value', "");
        $('#soc_pdr_deed_date').attr('value', "");
        $('#soc_pdr_notary').attr('value', "");
        $('#soc_pdr_deed_reg_date').attr('value', "");
        $('#soc_pdr_elec_folio').attr('value', "");
        $('#soc_pdr_power_due_date').attr('value', "");

});



function active_power() {

   if ( $('#soc_power_type').val() === "Instrumento Privado" )  {
        document.form_societypty.soc_private_instrument.disabled=false
        document.form_societypty.soc_pi_end_date.disabled=false

        document.form_societypty.soc_pdn_deed_number.disabled=true
        document.form_societypty.soc_pdn_deed_date.disabled=true
        document.form_societypty.soc_pdn_notary.disabled=true
        document.form_societypty.soc_pdn_power_due_date.disabled=true

        document.form_societypty.soc_pdr_deed_number.disabled=true
        document.form_societypty.soc_pdr_deed_date.disabled=true
        document.form_societypty.soc_pdr_notary.disabled=true
        document.form_societypty.soc_pdr_deed_reg_date.disabled=true
        document.form_societypty.soc_pdr_elec_folio.disabled=true
        document.form_societypty.soc_pdr_power_due_date.disabled=true

   } else if ( $('#soc_power_type').val() === "Escritura Pública No Inscrita" ){
        document.form_societypty.soc_private_instrument.disabled=true
        document.form_societypty.soc_pi_end_date.disabled=true

        document.form_societypty.soc_pdn_deed_number.disabled=false
        document.form_societypty.soc_pdn_deed_date.disabled=false
        document.form_societypty.soc_pdn_notary.disabled=false
        document.form_societypty.soc_pdn_power_due_date.disabled=false

        document.form_societypty.soc_pdr_deed_number.disabled=true
        document.form_societypty.soc_pdr_deed_date.disabled=true
        document.form_societypty.soc_pdr_notary.disabled=true
        document.form_societypty.soc_pdr_deed_reg_date.disabled=true
        document.form_societypty.soc_pdr_elec_folio.disabled=true
        document.form_societypty.soc_pdr_power_due_date.disabled=true

    } else if ( $('#soc_power_type').val() === "Escritura Pública Inscrita" ){

        document.form_societypty.soc_private_instrument.disabled=true
        document.form_societypty.soc_pi_end_date.disabled=true

        document.form_societypty.soc_pdn_deed_number.disabled=true
        document.form_societypty.soc_pdn_deed_date.disabled=true
        document.form_societypty.soc_pdn_notary.disabled=true
        document.form_societypty.soc_pdn_power_due_date.disabled=true

        document.form_societypty.soc_pdr_deed_number.disabled=false
        document.form_societypty.soc_pdr_deed_date.disabled=false
        document.form_societypty.soc_pdr_notary.disabled=false
        document.form_societypty.soc_pdr_deed_reg_date.disabled=false
        document.form_societypty.soc_pdr_elec_folio.disabled=false
        document.form_societypty.soc_pdr_power_due_date.disabled=false
    }

    $('#row_others_services div.disabled').removeClass("disabled");

    $('.form-actions').show();
    $('#btn_editar').hide();
    $('#btn_registrar_cumplimiento').show();
    $('.btn_borrar_cumplimiento').show();


    $('#btn_cancel_edit').show();
    $('.subject-detalle-edit').html("Modo edición activa");
    $('#form_societypty .form-control[readonly]').css("background-color","#fff");
    $('#soc_society_activities').summernote('enable');
    $('#soc_comments').summernote('enable');
    
}



function desactive_power() {

   if ( $('#soc_power_type').val() === "Instrumento Privado" )  {
        document.form_societypty.soc_private_instrument.disabled=true
        document.form_societypty.soc_pi_end_date.disabled=true

        document.form_societypty.soc_pdn_deed_number.disabled=false
        document.form_societypty.soc_pdn_deed_date.disabled=false
        document.form_societypty.soc_pdn_notary.disabled=false
        document.form_societypty.soc_pdn_power_due_date.disabled=false

        document.form_societypty.soc_pdr_deed_number.disabled=false
        document.form_societypty.soc_pdr_deed_date.disabled=false
        document.form_societypty.soc_pdr_notary.disabled=false
        document.form_societypty.soc_pdr_deed_reg_date.disabled=false
        document.form_societypty.soc_pdr_elec_folio.disabled=false
        document.form_societypty.soc_pdr_power_due_date.disabled=false

   } else if ( $('#soc_power_type').val() === "Escritura Pública No Inscrita" ){
        document.form_societypty.soc_private_instrument.disabled=false
        document.form_societypty.soc_pi_end_date.disabled=false

        document.form_societypty.soc_pdn_deed_number.disabled=true
        document.form_societypty.soc_pdn_deed_date.disabled=true
        document.form_societypty.soc_pdn_notary.disabled=true
        document.form_societypty.soc_pdn_power_due_date.disabled=true

        document.form_societypty.soc_pdr_deed_number.disabled=false
        document.form_societypty.soc_pdr_deed_date.disabled=false
        document.form_societypty.soc_pdr_notary.disabled=false
        document.form_societypty.soc_pdr_deed_reg_date.disabled=false
        document.form_societypty.soc_pdr_elec_folio.disabled=false
        document.form_societypty.soc_pdr_power_due_date.disabled=false

    } else if ( $('#soc_power_type').val() === "Escritura Pública Inscrita" ){

        document.form_societypty.soc_private_instrument.disabled=false
        document.form_societypty.soc_pi_end_date.disabled=false

        document.form_societypty.soc_pdn_deed_number.disabled=false
        document.form_societypty.soc_pdn_deed_date.disabled=false
        document.form_societypty.soc_pdn_notary.disabled=false
        document.form_societypty.soc_pdn_power_due_date.disabled=false

        document.form_societypty.soc_pdr_deed_number.disabled=true
        document.form_societypty.soc_pdr_deed_date.disabled=true
        document.form_societypty.soc_pdr_notary.disabled=true
        document.form_societypty.soc_pdr_deed_reg_date.disabled=true
        document.form_societypty.soc_pdr_elec_folio.disabled=true
        document.form_societypty.soc_pdr_power_due_date.disabled=true
    }

    $('#row_others_services .checker').addClass("disabled");


    $('.form-actions').hide();
    $('#btn_editar').show();
    $('#btn_registrar_cumplimiento').hide();
    $('.btn_borrar_cumplimiento').hide();


    $('#btn_cancel_edit').hide();
    $('.subject-detalle-edit').html("Detalle");
    $('#form_societypty .form-control[readonly]').css("background-color","#eee");



    

}

function select_society()
{
	$(".table-group-action-submit").trigger('click');
}



function registrar_cumplimiento(soc_id) {
    $('#cumplimiento_sin_registro').remove();

    console.log(soc_id);
    var item = `<tr>
                  <td>
                    <select class="form-control select_cumplimiento" name="cumplimiento_tipo[]">
                      <option value="Compliant">Compliant</option>
                      <option value="Parcial">Parcial</option>
                      <option value="No compliant">No compliant</option>
                    </select>
                    </td>
                    <td><input style="width:100px" class="form-control" type="text"  name="cumplimiento_ano[]"></td>
                    <td></td>
                    </tr>`;

    $('#tbody_cumplimiento').append(item);
    $('.select_cumplimiento').select2({});



}
$(document).on('click','.btn_borrar_cumplimiento',function () {
    // body...
    
    $(this).closest("tr").remove();
})
