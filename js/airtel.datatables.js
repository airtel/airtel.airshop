$(document).ready(function(){
    
    // With search and pagination
    $('.datatable_a').dataTable({
        "sDom": "<'row'<'searchbar pull-right'f>r>t<'row'<''p>>",
        "aaSorting": [[ 0, "desc" ]],
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sUrl": base_url + "lib/datatables/language/lv_LV.txt"
        }
    });
    
    // Without search and with pagination
    $('.datatable_b').dataTable({
        "sDom": "<'row'<'searchbar pull-right'>r>t<'row'<''p>>",
        "aaSorting": [[ 2, "desc" ]],
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sUrl": base_url + "lib/datatables/language/lv_LV.txt"
        }
    });
    
});