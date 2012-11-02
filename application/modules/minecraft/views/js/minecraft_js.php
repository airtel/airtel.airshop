<script>
$(document).ready(function(){
    
    
    /**
     * Disable validation on keyup.
     * Checks will be performed only on submit for username and factions fields
     */
    $('[name=username]').bind('keyup',function(){
       return false; 
    });
    
    $('[name=faction]').bind('keyup',function(){
       return false; 
    });    
    
    
    /**
     * Minecraft datatable
     */
    var sort = (active_service == 'unban') ? 2 : 1;
    $('.datatable_minecraft').dataTable({
        
        "sDom": "<'row'<'searchbar pull-right'>r>t<'row'<''p>>",
        "aaSorting": [[ sort, "desc" ]],
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sUrl": base_url + "lib/datatables/language/lv_LV.txt"
        }
    });
    
    
    /**
     * Enable chosen for groups 
     */
    $('#group').chosen({
        disable_search_threshold: 25
    });
    
    
    /**
     * Minecraft groups service specific js code
     */
    if(active_service == 'groups')
    {
        airtel_minecraft.rebuild_groups();

        $('#group').chosen().change(function() {
            airtel_minecraft.rebuild_groups();
        });
    }
    
});

<?php if($this->module->active_service == 'groups'): ?>
airtel_minecraft = {

    rebuild_groups: function()
    {
        var groups_pricelist = <?php echo json_encode($this->system->build_priceplan_groups()); ?>;
        var active_group = $('#group option:selected').val();

        // For each available pay method
        $(pay_methods).each(function(index, element) {

            // Remove old prices
            $('#prices_'+element).find('option').remove().end();

            // Adding new prices
            $.each(groups_pricelist[element][active_group], function(key, value) {

                // Adding data to real dropdown menu
                $('#prices_'+element).append($('<option>').text(value).attr('value', key)).trigger("liszt:updated");

            });

        });

        airtel_payments.sms_text();
    }
    
}
<?php endif; ?>
</script>