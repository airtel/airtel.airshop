<script>
    
    
    /**
     * Valid username custom check
     */
    $.validator.addMethod('valid_exp_user', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: site_url + '/war/war_ajax_call/check_valid_exp_user/' + active_service + '/',
                async: false,
                global: false,
                type: 'POST',
                data: { 
                    user_input: value,
                    race_id: $('#races').val()
                },
                dataType: 'html',
                success: function(output)
                {
                    response = ( output == 'TRUE' ) ? true : false
                }
            });
            return response;
        }
        
    }, 'Šādas rases spēlētajs nav atrasts. Pārbaudi vai spēlē ilgāk!');
    
    
    /**
     * Valid username custom check
     */
    $.validator.addMethod('valid_ammo_user', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: site_url + '/war/war_ajax_call/check_ammo_user/' + active_service + '/',
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
        
    }, 'Šāds spēlētajs nav atrasts. Pārbaudi vai spēlē ilgāk!');
    
    
    
</script>