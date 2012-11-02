<script>
$(document).ready(function(){
    
    
    /**
     * Disable validation on keyup.
     * Checks will be performed only on submit for username and factions fields
     */
    $('input[name=expression], input[name=character]').bind('keyup', function(){
        return false; 
    });
    
    
    // Without search and with pagination
    $('.datatable_c').dataTable({
        "sDom": "<'row'<'searchbar pull-right'>r>t<'row'<''p>>",
        "aaSorting": [[ 1, "desc" ]],
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sUrl": base_url + "lib/datatables/language/lv_LV.txt"
        }
    });
    
    
    
});
</script>