<script>
$(document).ready(function(){
    
    
    /**
     * muOnline datatable
     */
    if(active_service == 'unban') var sort = 0;
    else if(active_service == 'gmaccess') var sort = 2;
    else var sort = 1;
        
    
    $('.datatable_muonline').dataTable({
        
        "sDom": "<'row'<'searchbar pull-right'>r>t<'row'<''p>>",
        "aaSorting": [[ sort, "desc" ]],
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

    
});
</script>