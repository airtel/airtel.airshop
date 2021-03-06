<script>

    
/**
 * Check if ip is banned
 */
$.validator.addMethod('check_banned_ip', function(value, element){

    if(value.length > 7)
    {
        $.ajax({

            url: site_url + '/amx/amx_ajax_call/check_banned_ip',
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
}, 'Šādai ip adresei bans mūsu serveros nav atrasts!');


/**
 * If user exists in database check his password
 */
$.validator.addMethod('check_user', function(value, element){

    if(value.length > 2)
    {
        $.ajax({

            url: site_url + '/amx/amx_ajax_call/check_user',
            async: false,
            global: false,
            type: 'POST',
            data: {
                user_input: value,
                server_id: $('#servers').val(),
                password: $('#password').val()
            },
            dataType: 'html',
            success: function(output)
            {
                response = ( output == 'TRUE' ) ? true : false
            }
        });
        return response;
    }

}, 'Šajā serverī jau ir šads lietotājs. Lūdzu ievadi pareizu šī lietotāja paroli.');
    

</script>