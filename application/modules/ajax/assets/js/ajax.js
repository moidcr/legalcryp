/**
 * Conexión ajax que retorna los estados de un país específico.
 * 
 * @param {string} country_id Identificador del pais
 */
function get_states(country_id, callback) {
    $.get(base_url + "index.php/admin/content/ajax/get_states", { country_id : country_id}, function(e) {
        if(e.success) {
            callback(e.records);
        }
        else {
            callback({});
        }
    }, "JSON");
}



