<script>
$(document).ready(function(){
    
    <?php if($this->uri->segment(4) != 'error'): ?>
        
    // Custom chosen hack
    $.validator.addMethod('choose_server', function(value, element){
        
        return value !== '';

    }, 'Lūdzu izvēlieties serveri no saraksta');
    
    // Hack the chosen error message
    $.validator.messages.choose_server = "";
    
    // Chosen select hack
    var settings = $.data($('#validate_wizard')[0], 'validator').settings;
    settings.ignore += ':not(.chzn-done)';

    <?php endif; ?>

});
</script>