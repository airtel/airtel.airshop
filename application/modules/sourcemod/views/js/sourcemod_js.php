<script>
$(document).ready(function(){


    // Disable validation on keyup
    $('[name=ipaddress]').bind('keyup',function(){
        return false; 
    });
    $('[name=authid]').bind('keyup',function(){
        return false; 
    });
    $('[name=username]').bind('keyup',function(){
        return false; 
    });
    
    
});
</script>