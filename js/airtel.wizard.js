$(document).ready(function() {


    $('#validate_wizard').formwizard({ 
        formPluginEnabled: false,
        validationEnabled: true,
        focusFirstInput : false,
        validationOptions: {
            
            highlight: function(element) {
                $(element).closest('div.control-group').addClass('error f_error');
                var thisStep = $(element).closest('form').prev('ul').find('.current-step');
                thisStep.addClass('error-image');
            },

            success: function(element) {
                $(element).closest('div.control-group').removeClass('error f_error');
                if(!$(element).closest('form').find('div.error').length) {
                    var thisStep = $(element).closest('form').prev('ul').find('.current-step');
                    thisStep.removeClass('error-image');
                };

            }
        },

        disableInputFields: false,

        textSubmit: 'Apstiprin\u0101t',
        textNext: 'Uz apmaksu',
        textBack: 'Atpaka\u013c'
        
    });
    
    
    $('#validate_wizard').bind('step_shown', function(event, data){
    
        if(data.isBackNavigation || !data.isFirstStep){
            var direction = (data.isBackNavigation) ? 'back' : 'forward';
            
            if(direction === 'forward')
            {
                $('#title-step2').addClass('current-step');
                $('#title-step1').removeClass('current-step');
            }
            else
            {
                $('#title-step1').addClass('current-step');
                $('#title-step2').removeClass('current-step');
            }
            
        }
    
    });
    
    
});