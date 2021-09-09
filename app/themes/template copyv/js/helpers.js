/* Nofication Close Buttons */
$('.notification a.close').click(function(e){
	e.preventDefault();

	$(this).parent('.notification').fadeOut();
});

/*
	Check All Feature
*/
$(".check-all").click(function(){
	$("table input[type=checkbox]").attr('checked', $(this).is(':checked'));
});

/*
	Dropdowns
*/
$('.dropdown-toggle').dropdown();

/*
	Set focus on the first form field
*/
$(":input:visible:first").focus();

/*
	Responsive Navigation
*/
$('.collapse').collapse();

/*
 Prevent elements classed with "no-link" from linking
*/
//$(".no-link").click(function(e){ e.preventDefault();	});

$.fn.select2.defaults.set("theme", "bootstrap");
$("select").select2({
    /* allowClear: true */
});
/*
if($("#state").length) {
    var cities = [];
    $('#country_id').on('change', function() {
        
        get_states(this.value, function(records) {
           if(records.length) {
               $("#state").empty().append($("<option/>"));
               
               $.each(records, function(index, item){
                   $("#state").append($("<option/>").val(item.state_id).text(item.state_name).attr("city", item.state_city));
                   cities[item.state_id] = item.state_city;
               });
           }
           $("#state").select2("val", '');
        
        });
      
        
        if($("#city").length) {
            //$("#city").val("");
        }
    });
    
    $('#state').on('change', function() {
        $("#city").val(cities[$(this).val()]);
    });
}

*/

// Crear los datepicker
//$('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});

// Buscar contactos
$("#search-contact").on("click keyup", function() {
    $('#modal-placeholder').load(base_url + "index.php/admin/content/ajax/modal_search_contact", {
        object_id: $("#account_id").val(),
        ci_csrf_token : ci_csrf_token()
    });
});

// Mascaras
if($("#ruc_dv").length) {
    $("#ruc_dv").inputmask('9{1,2}', {
        rightAlignNumerics: false
    });
}
if($("#ruc").length) {
    $("#ruc").inputmask("Regex", {
        regex: "[0-9-]{15}"
    });
}
if($("#phone_1").length) {
    $("#phone_1").inputmask("Regex", {
        regex: "[0-9-]{15}"
      });
}
if($("#phone_2").length) {
    $("#phone_2").inputmask("Regex", {
        regex: "[0-9-]{15}"
      });
}
if($("#contact_phone_1").length) {
    $("#contact_phone_1").inputmask("Regex", {
        regex: "[0-9-]{15}"
      });
}
if($("#contact_phone_2").length) {
    $("#contact_phone_2").inputmask("Regex", {
        regex: "[0-9-]{15}"
      });
}

if($("#contact_personalid").length) {
    $("#contact_personalid").inputmask("Regex", {
        regex: "[A-Za-z0-9-]*"
      });
}

// desactivar campos
function disable_form(element) {
    
    $(element + " input").attr("disabled", "disabled");
    $(element + " textarea").attr("disabled", "disabled");
    $(element + " select").attr("disabled", "disabled");
}

function active_form(element) {
    $(element + " input").removeAttr("disabled");
    $(element + " textarea").removeAttr("disabled");
    $(element + " select").removeAttr("disabled");
}

if($(".disable_form").length) {
    disable_form('.disable_form');
}