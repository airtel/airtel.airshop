<script>
$(document).ready(function(){
    
    
    $('[name=username]').bind('keyup',function(){
       return false; 
    });
    
    
    /**
     * War3 datatable
     */
    $('.datatable_war').dataTable({
        
        "sDom": "<'row'<'searchbar pull-right'>r>t<'row'<''p>>",
        "aaSorting": [[ 1, "desc" ]],
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sUrl": base_url + "lib/datatables/language/lv_LV.txt"
        }
    });
    
    
});
</script>