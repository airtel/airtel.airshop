<script>
    
    
    /**
     * User suspended status custom check
     */
    $.validator.addMethod('check_suspended', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/ipb/ipb_ajax_call/check_suspended',
                async: false,
                global: false,
                type: 'POST',
                data: { user_input: value },
                dataType: 'html',
                success: function(output)
                {
                    response = ( output == 'TRUE' ) ? true : false
                }
            });
            return response;
        }
        
    }, 'Šim lietotājam Suspended status netika atrasts.');
    
    
    /**
     * Check for unique display name
     */
    $.validator.addMethod('check_displayname', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/ipb/ipb_ajax_call/check_displayname',
                async: false,
                global: false,
                type: 'POST',
                data: { user_input: value },
                dataType: 'html',
                success: function(output)
                {
                    response = ( output == 'TRUE' ) ? true : false
                }
            });
            return response;
        }
        
    }, 'Šāds display name jau eksistē! Lūdzu izvēlies citu.');
    
    
    /**
     * Display name valid characters check
     */
    $.validator.addMethod('check_valid_displayname', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/ipb/ipb_ajax_call/check_valid_displayname',
                async: false,
                global: false,
                type: 'POST',
                data: { user_input: value },
                dataType: 'html',
                success: function(output)
                {
                    response = ( output == 'TRUE' ) ? true : false
                }
            });
            return response;
        }
        
    }, 'Display name nevar saturēt ; , | [ ]');
    
    
</script>