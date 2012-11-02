<script>
    
    
    /**
     * Valid username custom check
     */
    $.validator.addMethod('valid_user', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: site_url + '/muonline/muonline_ajax_call/check_valid_user',
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
        
    }, 'Šāds lietotājs datubāzē nav atrasts');
    
    
    /**
     * Check if user has name ban set
     */
    $.validator.addMethod('check_name_ban', function(value, element){

        if(value.length > 2)
        {
            $.ajax({

                url: site_url + '/muonline/muonline_ajax_call/check_name_ban',
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
        
    }, 'Šādam lietotājam bans mūsu serverī nav atrasts');
    
    
</script>