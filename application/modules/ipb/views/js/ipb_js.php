<script>
$(document).ready(function(){
    
    
    // IPB datatable
    $('.datatable_ipb').dataTable({
        "sDom": "<'row'<'searchbar pull-right'f>r>t<'row'<''p>>",
        "aaSorting": [[ 1, "desc" ]],
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sUrl": base_url + "lib/datatables/language/lv_LV.txt"
        }        
    });
    
    
    /**
     * Disable validation on keyup.
     * Checks will be performed only on submit for username and factions fields
     */
    $('[name=username]').bind('keyup',function(){
       return false; 
    });
    
    $('[name=displayname]').bind('keyup',function(){
       return false; 
    });
    
    
});
</script>