
if($('#fd_deed_date').length)
    $('#fd_deed_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#fd_incription_date').length)
    $('#fd_incription_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#fd_pi_end_date').length)
    $('#fd_pi_end_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#fd_pdn_deed_date').length)
    $('#fd_pdn_deed_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#fd_pdn_power_due_date').length)
    $('#fd_pdn_power_due_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#fd_pdr_deed_date').length)
    $('#fd_pdr_deed_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#fd_pdr_deed_reg_date').length)
    $('#fd_pdr_deed_reg_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#fd_pdr_power_due_date').length)
    $('#fd_pdr_power_due_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

/**
 * Data Table para el index
 */

var TableDatatablesAjax = function () {

    var handleRecords = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#datatable_foundation"),
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
                      columns: [ 1, 2, 3, 4, 5 ]
                    }
                  },{
                    extend:'pdf',
                    exportOptions: {
                      columns: [ 1, 2, 3, 4, 5 ]
                    }
                  },{
                    extend:'print',
                    exportOptions: {
                      columns: [ 1, 2, 3, 4, 5 ]
                    }
                  }
                ],

                "lengthMenu": [
                    [10, 15, 30],
                    [10, 15, 30] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": base_url + "index.php/admin/files/foundation/ajax_get", // ajax source

                },
                "colReorder": {
                    order: [ 0, 1, 2, 3 ]
                },
                "order": [
                    [1, "asc"]
                ]// set first column as a default sort by asc
            }
        });

        // Evento enter
        grid.getTableWrapper().on('keyup', '.table-group-action-input', function (e) {
            var code = e.which;
            if(code==13)e.preventDefault();
            if(code==32||code==13||code==188||code==186) {
                $(".table-group-action-submit").trigger('click');
            }
        });
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {

            e.preventDefault();
            grid.setAjaxParam("ci_csrf_token", ci_csrf_token());

            var action = $(".table-group-action-input").val().replace(/[|&;$%@"<>(  )+,]/g, "").trim();

            if (action) {
                //grid.setAjaxParam("id", grid.getSelectedRows());
                grid.setAjaxParam("filter", action);

                grid.getDataTable().ajax.reload();
                grid.clearAjaxParams();
            } else {
                /*
                App.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Debe proporcionar un texto de búsqueda',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
                */
            }
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
});


//COMBO CONTACT
$(document).on('change','#fd_person_id',function(){

            $.ajax({ url: base_url + "index.php/admin/files/foundation/person_contacts",
               type: "POST",
               data: {ci_csrf_token:ci_csrf_token(),
                      person_id: $("#fd_person_id").val()},
                success: function(data){

                        var response = JSON.parse(data);


                        $('#fd_contact_id').empty();

                        jQuery('<option/>', {
                        value: '',
                        html: ''
                        }).appendTo('#fd_contact_id');

                        if (response != null) {


                            for(var i=0; i< response.length;i++)
                            {
                            //creates option tag
                              jQuery('<option/>', {
                                    value: response[i].contact_id,
                                    html: response[i].fullname
                                    }).appendTo('#fd_contact_id'); //appends to select if parent div has id dropdown
                            }

                        }

                        $('#select2-fd_contact_id-container').val('');
                        $('#select2-fd_contact_id-container').text('');


                    }

            })


});




$(document).on('change','#fd_power_type',function(){

        active_power();

        $('#fd_private_instrument').attr('value', "");
        $('#fd_pi_end_date').attr('value', "");

        $('#fd_pdn_deed_number').attr('value', "");
        $('#fd_pdn_deed_date').attr('value', "");
        $('#fd_pdn_notary').attr('value', "");
        $('#fd_pdn_power_due_date').attr('value', "");

        $('#fd_pdr_deed_number').attr('value', "");
        $('#fd_pdr_deed_date').attr('value', "");
        $('#fd_pdr_notary').attr('value', "");
        $('#fd_pdr_deed_reg_date').attr('value', "");
        $('#fd_pdr_elec_folio').attr('value', "");
        $('#fd_pdr_power_due_date').attr('value', "");

});



function active_power() {

   if ( $('#fd_power_type').val() === "Instrumento Privado" )  {
        document.form_foundation.fd_private_instrument.disabled=false
        document.form_foundation.fd_pi_end_date.disabled=false

        document.form_foundation.fd_pdn_deed_number.disabled=true
        document.form_foundation.fd_pdn_deed_date.disabled=true
        document.form_foundation.fd_pdn_notary.disabled=true
        document.form_foundation.fd_pdn_power_due_date.disabled=true

        document.form_foundation.fd_pdr_deed_number.disabled=true
        document.form_foundation.fd_pdr_deed_date.disabled=true
        document.form_foundation.fd_pdr_notary.disabled=true
        document.form_foundation.fd_pdr_deed_reg_date.disabled=true
        document.form_foundation.fd_pdr_elec_folio.disabled=true
        document.form_foundation.fd_pdr_power_due_date.disabled=true

   } else if ( $('#fd_power_type').val() === "Escritura Pública No Inscrita" ){
        document.form_foundation.fd_private_instrument.disabled=true
        document.form_foundation.fd_pi_end_date.disabled=true

        document.form_foundation.fd_pdn_deed_number.disabled=false
        document.form_foundation.fd_pdn_deed_date.disabled=false
        document.form_foundation.fd_pdn_notary.disabled=false
        document.form_foundation.fd_pdn_power_due_date.disabled=false

        document.form_foundation.fd_pdr_deed_number.disabled=true
        document.form_foundation.fd_pdr_deed_date.disabled=true
        document.form_foundation.fd_pdr_notary.disabled=true
        document.form_foundation.fd_pdr_deed_reg_date.disabled=true
        document.form_foundation.fd_pdr_elec_folio.disabled=true
        document.form_foundation.fd_pdr_power_due_date.disabled=true

    } else if ( $('#fd_power_type').val() === "Escritura Pública Inscrita" ){

        document.form_foundation.fd_private_instrument.disabled=true
        document.form_foundation.fd_pi_end_date.disabled=true

        document.form_foundation.fd_pdn_deed_number.disabled=true
        document.form_foundation.fd_pdn_deed_date.disabled=true
        document.form_foundation.fd_pdn_notary.disabled=true
        document.form_foundation.fd_pdn_power_due_date.disabled=true

        document.form_foundation.fd_pdr_deed_number.disabled=false
        document.form_foundation.fd_pdr_deed_date.disabled=false
        document.form_foundation.fd_pdr_notary.disabled=false
        document.form_foundation.fd_pdr_deed_reg_date.disabled=false
        document.form_foundation.fd_pdr_elec_folio.disabled=false
        document.form_foundation.fd_pdr_power_due_date.disabled=false
    }



    $('.form-actions').show();
    $('#btn_editar').hide();
    $('#btn_cancel_edit').show();
    $('.subject-detalle-edit').html("Modo edición activa");
    $('.form-control[readonly]').css("background-color","#fff");


}


function desactive_power() {

   if ( $('#fd_power_type').val() === "Instrumento Privado" )  {
        document.form_foundation.fd_private_instrument.disabled=true
        document.form_foundation.fd_pi_end_date.disabled=true

        document.form_foundation.fd_pdn_deed_number.disabled=false
        document.form_foundation.fd_pdn_deed_date.disabled=false
        document.form_foundation.fd_pdn_notary.disabled=false
        document.form_foundation.fd_pdn_power_due_date.disabled=false

        document.form_foundation.fd_pdr_deed_number.disabled=false
        document.form_foundation.fd_pdr_deed_date.disabled=false
        document.form_foundation.fd_pdr_notary.disabled=false
        document.form_foundation.fd_pdr_deed_reg_date.disabled=false
        document.form_foundation.fd_pdr_elec_folio.disabled=false
        document.form_foundation.fd_pdr_power_due_date.disabled=false

   } else if ( $('#fd_power_type').val() === "Escritura Pública No Inscrita" ){
        document.form_foundation.fd_private_instrument.disabled=false
        document.form_foundation.fd_pi_end_date.disabled=false

        document.form_foundation.fd_pdn_deed_number.disabled=true
        document.form_foundation.fd_pdn_deed_date.disabled=true
        document.form_foundation.fd_pdn_notary.disabled=true
        document.form_foundation.fd_pdn_power_due_date.disabled=true

        document.form_foundation.fd_pdr_deed_number.disabled=false
        document.form_foundation.fd_pdr_deed_date.disabled=false
        document.form_foundation.fd_pdr_notary.disabled=false
        document.form_foundation.fd_pdr_deed_reg_date.disabled=false
        document.form_foundation.fd_pdr_elec_folio.disabled=false
        document.form_foundation.fd_pdr_power_due_date.disabled=false

    } else if ( $('#fd_power_type').val() === "Escritura Pública Inscrita" ){

        document.form_foundation.fd_private_instrument.disabled=false
        document.form_foundation.fd_pi_end_date.disabled=false

        document.form_foundation.fd_pdn_deed_number.disabled=false
        document.form_foundation.fd_pdn_deed_date.disabled=false
        document.form_foundation.fd_pdn_notary.disabled=false
        document.form_foundation.fd_pdn_power_due_date.disabled=false

        document.form_foundation.fd_pdr_deed_number.disabled=true
        document.form_foundation.fd_pdr_deed_date.disabled=true
        document.form_foundation.fd_pdr_notary.disabled=true
        document.form_foundation.fd_pdr_deed_reg_date.disabled=true
        document.form_foundation.fd_pdr_elec_folio.disabled=true
        document.form_foundation.fd_pdr_power_due_date.disabled=true
    }


    
    $('.form-actions').hide();
    $('#btn_editar').show();
    $('#btn_cancel_edit').hide();
    $('.subject-detalle-edit').html("Detalle");
    $('.form-control[readonly]').css("background-color","#eee");


}
