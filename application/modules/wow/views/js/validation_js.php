<script>


    /**
     * Valid username custom check
     */
    $.validator.addMethod('valid_user', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/wow/wow_ajax_call/valid_user',
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
        
    }, 'Šāds lietotājs datubāzē nav atrasts');
    
    
    /**
     * Valid username custom check
     */
    $.validator.addMethod('valid_web_user', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/wow/wow_ajax_call/valid_web_user',
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
        
    }, 'Šāds lietotājs datubāzē nav atrasts. Ielogojies vismaz vienu reizi web-portālā');

    
    /**
     * Account ban check
     */
    $.validator.addMethod('check_ban_account', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/wow/wow_ajax_call/check_ban_account',
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
        
    }, 'Šādam lietotājam bans mūsu serverī nav atrasts!');
    
    
    /**
     * Character ban check
     */
    $.validator.addMethod('check_ban_character', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/wow/wow_ajax_call/check_ban_character',
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
        
    }, 'Šādam lietotājam bans mūsu serverī nav atrasts!');
    
    
    /**
     * IP ban check
     */
    $.validator.addMethod('check_ban_ip', function(value, element){

        if(value.length >= 7)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/wow/wow_ajax_call/check_ban_ip',
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
        
    }, 'Šādam lietotājam bans mūsu serverī nav atrasts!');
    
    
    /**
     * Valid username custom check
     */
    $.validator.addMethod('check_online', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: '<?php echo site_url(); ?>/wow/wow_ajax_call/check_online',
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
        
    }, 'Lūdzu vispirms izej no spēles.');
    
    
</script>