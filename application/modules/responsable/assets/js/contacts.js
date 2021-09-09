$('#contact_personalid_expiration').datepicker({dateFormat: 'yy-mm-dd'});
$('#contact_birthday').datepicker({dateFormat: 'yy-mm-dd',  startView: "-15y", endDate: "-15y"});

/**
 * Data Table para el index
 */

var TableDatatablesAjax = function () {

    var handleRecords = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#datatable_contacts"),
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
            loadingMessage: 'Loading...',
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
                    "url": base_url + "index.php/admin/content/contacts/ajax_get", // ajax source

                },
                "colReorder": {
                    order: [ 1, 2, 3, 4, 5 ]
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

            var action = $(".table-group-action-input").val().replace(/[|&;$%@"<>(  )+,]/g, "");

            //if (action) {
                //grid.setAjaxParam("id", grid.getSelectedRows());
                grid.setAjaxParam("filter", action);

                grid.getDataTable().ajax.reload();
                grid.clearAjaxParams();
           // } else {
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
});
