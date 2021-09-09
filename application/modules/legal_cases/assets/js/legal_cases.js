
//if($('#lg_start_date').length)
//    $('#lg_start_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});

if($('#lg_end_date').length)
    $('#lg_end_date').datepicker({ autoclose: true, dateFormat: 'yy-mm-dd'});



/**
 * Data Table para el index
 */

var TableDatatablesAjax = function () {

    var handleRecords = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#datatable_legal_cases"),
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
                      columns: [ 1, 2, 3, 4, 5, 6, 7 ]
                    }
                  },{
                    extend:'pdf',
                    exportOptions: {
                      columns: [ 1, 2, 3, 4, 5, 6, 7 ]
                    }
                  },{
                    extend:'print',
                    exportOptions: {
                      columns: [ 1, 2, 3, 4, 5, 6, 7 ]
                    }
                  }
                ],

                "lengthMenu": [
                    [10, 15, 30, 50],
                    [10, 15, 30, 50] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": base_url + "index.php/admin/cases/legal_cases/ajax_get", // ajax source

                },
                "colReorder": {
                    order: [ 0, 1, 2, 3, 4 ]
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
                //grid.clearAjaxParams();
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
    actualizar_expediente();
    
});

$(document).on('change','.descuento_porcentaje',function(){

    if($(this).val()>100){
        $(this).val(100);
    }

});

$(document).on('change','#lg_service',function(){

    actualizar_expediente();

});

function actualizar_expediente() {
    if ($("#lg_service").val() == 'society_pty' || $("#lg_service").val() == 'society_foreign')  {

            $.ajax({ url: base_url + "index.php/admin/cases/legal_cases/get_societies",
               type: "POST",
               data: {ci_csrf_token:ci_csrf_token(),
                      lg_service: $("#lg_service").val()},
                success: function(data){

                        var response = JSON.parse(data);

                        $('#lg_file').empty();

                      /*  jQuery('<option/>', {
                        value: '0',
                        html: 'Ninguno'
                        }).appendTo('#lg_file')*/;

                        if (response != null) {


                            for(var i=0; i< response.length;i++)
                            {
                            //creates option tag
                              jQuery('<option/>', {
                                    value: response[i].soc_id,
                                    html: response[i].soc_name
                                    }).appendTo('#lg_file'); //appends to select if parent div has id dropdown
                            }

                        }

                        $('#select2-lg_file-container').val('');
                        $('#select2-lg_file-container').text('');

                        $("#lg_person_id").prop("disabled", true);
                        $('#select2-lg_person_id_id-container').val('');
                        $('#select2-lg_person_id-container').text('');

                        $("#lg_jurisdiction_id").prop("disabled", true);
                        $('#select2-lg_jurisdiction_id-container').val('');
                        $('#select2-lg_jurisdiction_id-container').text('');

                        $("#lg_file").prop("disabled", false);
                        $('#select2-lg_file-container').val('');
                        $('#select2-lg_file-container').text('');

                        $('#lg_file').val($('#lg_file_id').val());
                        $('#lg_file').trigger("change");


                    }

            })

    } else if ($("#lg_service").val() == 'escrow_pty' || $("#lg_service").val() == 'escrow_foreign' || $("#lg_service").val() == 'deposit_escrow') {

               $.ajax({ url: base_url + "index.php/admin/cases/legal_cases/get_escrows",
               type: "POST",
               data: {ci_csrf_token:ci_csrf_token(),
                      lg_service: $("#lg_service").val()},
                success: function(data){

                        var response = JSON.parse(data);

                        $('#lg_file').empty();

                        /*jQuery('<option/>', {
                          value: '0',
                          html: 'Ninguno'
                        }).appendTo('#lg_file');*/

                        if (response != null) {


                            for(var i=0; i< response.length;i++)
                            {
                            //creates option tag
                              jQuery('<option/>', {
                                    value: response[i].esc_id,
                                    html: response[i].esc_name
                                    }).appendTo('#lg_file'); //appends to select if parent div has id dropdown
                            }

                        }

                        $('#select2-lg_file-container').val('');
                        $('#select2-lg_file-container').text('');

                        $("#lg_person_id").prop("disabled", true);
                        $('#select2-lg_person_id_id-container').val('');
                        $('#select2-lg_person_id-container').text('');

                        $("#lg_jurisdiction_id").prop("disabled", true);
                        $('#select2-lg_jurisdiction_id-container').val('');
                        $('#select2-lg_jurisdiction_id-container').text('');

                        $("#lg_file").prop("disabled", false);
                        $('#select2-lg_file-container').val('');
                        $('#select2-lg_file-container').text('');


                    }

            })

    } else if ($("#lg_service").val() == 'foundations') {

               $.ajax({ url: base_url + "index.php/admin/cases/legal_cases/get_foundations",
               type: "POST",
               data: {ci_csrf_token:ci_csrf_token(),
                      lg_service: $("#lg_service").val()},
                success: function(data){

                        var response = JSON.parse(data);

                        $('#lg_file').empty();

                        /*jQuery('<option/>', {
                          value: '0',
                          html: 'Ninguno'
                        }).appendTo('#lg_file');*/

                        if (response != null) {


                            for(var i=0; i< response.length;i++)
                            {
                            //creates option tag
                              jQuery('<option/>', {
                                    value: response[i].fd_id,
                                    html: response[i].fd_name
                                    }).appendTo('#lg_file'); //appends to select if parent div has id dropdown
                            }

                        }

                        $('#select2-lg_file-container').val('');
                        $('#select2-lg_file-container').text('');

                        $("#lg_person_id").prop("disabled", true);
                        $('#select2-lg_person_id_id-container').val('');
                        $('#select2-lg_person_id-container').text('');

                        $("#lg_jurisdiction_id").prop("disabled", true);
                        $('#select2-lg_jurisdiction_id-container').val('');
                        $('#select2-lg_jurisdiction_id-container').text('');

                        $("#lg_file").prop("disabled", false);
                        $('#select2-lg_file-container').val('');
                        $('#select2-lg_file-container').text('');


                    }

            })

    }  else if ($("#lg_service").val() == 'other') {

        $("#lg_person_id").prop("disabled", false);
        $('#select2-lg_person_id_id-container').val('');
        $('#select2-lg_person_id-container').text('');

        $("#lg_jurisdiction_id").prop("disabled", false);
        $('#select2-lg_jurisdiction_id-container').val('');
        $('#select2-lg_jurisdiction_id-container').text('');

        $("#lg_file").prop("disabled", true);
        $('#select2-lg_file-container').val('');
        $('#select2-lg_file-container').text('');

    }
}

if ($("#lg_service").val() == 'other') {
     $("#lg_person_id").prop("disabled", false);
     $("#lg_jurisdiction_id").prop("disabled", false);
     $("#lg_file").prop("disabled", true);
} else {
     $("#lg_person_id").prop("disabled", true);
     $("#lg_jurisdiction_id").prop("disabled", true);
     $("#lg_file").prop("disabled", false);
}


$('#btn_add_item_from_lookup').click(function() {
    $('#modal-placeholder').load(base_url + "index.php/admin/cases/legal_cases/modal_item_lookups/" + Math.floor(Math.random()*1000));
});

$('#btn_add_adelanto_from_lookup').click(function() {
    $('#modal-placeholder').load(base_url + "index.php/admin/cases/legal_cases/modal_adelanto_lookups/" + $("#lg_id").val());
});


  $('input[name=lg_desc_porc]').change(function(){
    console.log("ejecutando funcion");
    subtotal = $('input[name=lg_subtotal]').val();
    desc_porc = $('input[name=lg_desc_porc]').val();
    if (subtotal) {
      descuento = (parseFloat(subtotal.replace(/,/g, '')) * parseFloat(desc_porc.replace(/,/g, '')) / 100);
      $('input[name=lg_descuento]').val(descuento);
    }
    calcularTotal();
  });

  $('input[name=lg_imp_porc]').on('change keyup paste',function(){
    subtotal = $('input[name=lg_subtotal]').val();
    imp_porc = $('input[name=lg_imp_porc]').val();
    if (subtotal) {
      impuesto = (parseFloat(subtotal.replace(/,/g, '')) * parseFloat(imp_porc.replace(/,/g, '')) / 100);
      $('input[name=lg_impuesto]').val(impuesto);
    }
    calcularTotal();
  });

  function calcularTotal() {
    subtotal = $('input[name=lg_subtotal]').val();
    descuento = $('input[name=lg_descuento]').val();
    impuesto = $('input[name=lg_impuesto]').val();
    if (subtotal) {
      total = (parseFloat(subtotal.replace(/,/g, '')) - parseFloat(descuento.replace(/,/g, '')) + parseFloat(impuesto.replace(/,/g, '')))
      $('input[name=lg_total]').val(total);
    }
  }


$(document).on('change','#lg_file',function(){


        $.ajax({ url: base_url + "index.php/admin/cases/legal_cases/get_person_file",
        type: "POST",
        data: {ci_csrf_token:ci_csrf_token(),
               lg_service: $("#lg_service").val(),
               lg_file: $("#lg_file").val()},
         success: function(data){

                 var response = JSON.parse(data);



                 if (response != null) {

                    $('#lg_person_id').val(response.client_id).change();


                 }


             }

     })

});


function solicitar_descuento(){
    var items = [];
    var item_order = 1;

    $('#item_table tr.item').each(function() {
        var row = {};
           $(this).find('input,select,textarea').each(function() {
            if ($(this).is(':checkbox')) {
                row[$(this).attr('name')] = $(this).is(':checked');
            } else {
                row[$(this).attr('name')] = $(this).val();
            }
        });
        row['lci_item_order'] = item_order;
        row['lci_item_discount_percent'] = parseFloat(row['lci_item_discount_percent'].replace(",",""));
        
        //let new_price = row['lci_item_price'] * (row['lci_item_discount_percent'] / 100);
        //console.log(new_price);

        item_order++;
        items.push(row);
    });


    $.post(base_url + "index.php/admin/cases/legal_cases/ajax_save", {
        ci_csrf_token:ci_csrf_token(),
        lg_id: $('#lg_id').val(),
        lg_status: $('#lg_status').val(),
        lg_name: $('#lg_name').val(),
        lg_comments: $('#lg_comments').val(),
        lg_service: $('#lg_service').val(),
        lg_file: $('#lg_file').val(),
        lg_attorney_id: $('#lg_attorney_id').val(),
        lg_requested_by: $('#lg_requested_by').val(),
        lg_assistant: $('#lg_assistant').val(),
        lg_person_id: $('#lg_person_id').val(),
        lg_jurisdiction_id: $('#lg_jurisdiction_id').val(),
        lg_start_date: $('#lg_start_date').val(),
        lg_end_date: $('#lg_end_date').val(),
        lg_close_date: $('#lg_close_date').val(),
        lg_subtotal: parseFloat($('#lg_subtotal').val().replace(/,/g, '')),
        lg_desc_porc: $('#lg_desc_porc').val(),
        lg_descuento: parseFloat($('#lg_descuento').val().replace(/,/g, '')),
        lg_imp_porc: $('#lg_imp_porc').val(),
        lg_impuesto: parseFloat($('#lg_impuesto').val().replace(/,/g, '')),
        lg_total: parseFloat($('#lg_total').val().replace(/,/g, '')),
        items: JSON.stringify(items),
        solicitud_descuento: true
    },
    function(data) {
        var response = JSON.parse(data);

        if (response.success == '1') {
            window.location = base_url + "index.php/admin/cases/legal_cases/edit/" + response.last_id;
        }
       
    });
}

function aprobar_descuento(){
    var items = [];
    var item_order = 1;

    $('#item_table tr.item').each(function() {
        var row = {};
           $(this).find('input,select,textarea').each(function() {
            if ($(this).is(':checkbox')) {
                row[$(this).attr('name')] = $(this).is(':checked');
            } else {
                row[$(this).attr('name')] = $(this).val();
            }
        });
        row['lci_item_order'] = item_order;
        row['lci_item_discount_percent'] = parseFloat(row['lci_item_discount_percent'].replace(",",""));

        item_order++;
        items.push(row);
    });


    $.post(base_url + "index.php/admin/cases/legal_cases/ajax_save", {
        ci_csrf_token:ci_csrf_token(),
        lg_id: $('#lg_id').val(),
        lg_status: $('#lg_status').val(),
        lg_name: $('#lg_name').val(),
        lg_comments: $('#lg_comments').val(),
        lg_service: $('#lg_service').val(),
        lg_file: $('#lg_file').val(),
        lg_attorney_id: $('#lg_attorney_id').val(),
        lg_requested_by: $('#lg_requested_by').val(),
        lg_assistant: $('#lg_assistant').val(),
        lg_person_id: $('#lg_person_id').val(),
        lg_jurisdiction_id: $('#lg_jurisdiction_id').val(),
        lg_start_date: $('#lg_start_date').val(),
        lg_end_date: $('#lg_end_date').val(),
        lg_close_date: $('#lg_close_date').val(),
        lg_subtotal: parseFloat($('#lg_subtotal').val().replace(/,/g, '')),
        lg_desc_porc: $('#lg_desc_porc').val(),
        lg_descuento: parseFloat($('#lg_descuento').val().replace(/,/g, '')),
        lg_imp_porc: $('#lg_imp_porc').val(),
        lg_impuesto: parseFloat($('#lg_impuesto').val().replace(/,/g, '')),
        lg_total: parseFloat($('#lg_total').val().replace(/,/g, '')),
        items: JSON.stringify(items),
        aprobar_descuento: true
    },
    function(data) {
        var response = JSON.parse(data);

        if (response.success == '1') {
            window.location = base_url + "index.php/admin/cases/legal_cases/edit/" + response.last_id;
        }
       
    });
}
