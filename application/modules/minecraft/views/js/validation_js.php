<script>


    /**
     * Valid username custom check
     */
    $.validator.addMethod('valid_user', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/minecraft/minecraft_ajax_call/check_valid_user',
                async: false,
                global: false,
                type: 'POST',
                data: { user_input: value },
                dataType: 'html',
                success: function(output)
                {
                    response = ( output === 'TRUE' ) ? true : false;
                }
            });
            return response;
        }
        
    }, "Šāds lietotājs datubāzē nav atrasts");

    
    /**
     * Check if user has name ban set
     */
    $.validator.addMethod('check_name_ban', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/minecraft/minecraft_ajax_call/check_name_ban',
                async: false,
                global: false,
                type: 'POST',
                data: { user_input: value },
                dataType: 'html',
                success: function(output)
                {
                    response = ( output === 'TRUE' ) ? true : false;
                }
            });
            return response;
        }
        
    }, "Šādam lietotājam bans mūsu serverī nav atrasts");
    
    
    /**
     * Check if user has name ban set
     */
    $.validator.addMethod('check_prisoner', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/minecraft/minecraft_ajax_call/check_prisoner',
                async: false,
                global: false,
                type: 'POST',
                data: { user_input: value },
                dataType: 'html',
                success: function(output)
                {
                    response = ( output === 'TRUE' ) ? true : false;
                }
            });
            return response;
        }
        
    }, 'Šāds lietotājs cietumā neuzturas');
    
    
    /**
     * Check if user has name ban set
     */
    $.validator.addMethod('check_faction', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/minecraft/minecraft_ajax_call/check_faction',
                async: false,
                global: false,
                type: 'POST',
                data: { user_input: value },
                dataType: 'html',
                success: function(output)
                {
                    response = ( output === 'TRUE' ) ? true : false;
                }
            });
            return response;
        }
        
    }, 'Neesam atraduši frakciju ar šādu nosaukumu');
    
    
    /**
     * Check if player is in game
     */
    $.validator.addMethod('check_online', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/minecraft/minecraft_ajax_call/check_online',
                async: false,
                global: false,
                type: 'POST',
                data: { user_input: value },
                dataType: 'html',
                success: function(output)
                {
                    response = ( output === 'TRUE' ) ? true : false;
                }
            });
            return response;
        }
        
    }, 'Tev jaatrodas spēlē lai pasūtītu šo pakalpojumu!');


</script>