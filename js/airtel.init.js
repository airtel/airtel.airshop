$(document).ready(function() {
    
    // Visual styling
    airtel_misc.visual();


    // Fire tooltips
    airtel_misc.tooltips();
    
    
    // Enable chosen select menu
    airtel_misc.transform_select();
    
    
    // Fire sidebar resizing
    setInterval(function(){
        airtel_sidebar.resize();
    }, 100);
    

    // Accordion change actions
    $('#side_accordion').on('hidden shown', function () {
        airtel_sidebar.make_active();
    });

    
    // Generate new SMS text on price change
    $('#prices_sms').chosen().change(function() {
        airtel_payments.sms_text();
    });
    
    
    // Open window on click and set timer
    $("#airtel_ibank_system").click(function () {
        airtel_payments.ibank_anchor(); 
    });
    
    
    $("#airtel_paypal_system").click(function () {
        airtel_payments.paypal_anchor(); 
    });
    
    
    // Clearing inputs that could been filled by user into non-active tabs, so that on submit there
    // would be only one pay-method code filled.
    $('a[data-toggle="tab"]').on('shown', function (e) {
        
        // Set value to non-active tab input to bypass validation
        $(".tab-pane:not(.active) .code").val("99999999");
        
        // Active tab input is cleared
        $(".tab-pane.active .code").val("");
    });
    
    
    // Fire toplist show on click
    $(".top-list-anchor").click(function () {
        airtel_misc.toplist_show();
    });
    
    

});


/**
 * Sidebar functions
 */
airtel_sidebar = {
    
    resize: function()
    {
        var header_height = $('#header').height();
        var wrapper_height = $('.wrapper').height();
        var push_height = (wrapper_height - header_height);
        $('.sidebar').height(push_height);
    },
    
    make_active: function()
    {
        var thisAccordion = $('#side_accordion');
        thisAccordion.find('.accordion-heading').removeClass('sdb_h_active');
        var thisHeading = thisAccordion.find('.accordion-body.in').prev('.accordion-heading');
        if(thisHeading.length) {
                thisHeading.addClass('sdb_h_active');
        }
    }

};


/**
 * Miscellaneous functions
 */
airtel_misc = {
    
    visual: function()
    {
        $('.container').hide();
        $('.container').fadeIn(400);
    },
    
    tooltips: function()
    {
        // Enable tooltips for anchor tags
        $('a[rel=tooltip]').tooltip({placement: 'bottom'});
        
        // Enable tooltips for inputs with title tag
        $('input[title]').tooltip({placement: 'top'});
    },
    
    transform_select: function()
    {
        // Pasive chosens without any additional js functionality
        $('.chosen').chosen({
            disable_search_threshold: 25
        });
        
        
        // Enable all pay method chosens
        $(pay_methods).each(function(index, element) {
            
            $('#prices_'+element).chosen({
                disable_search_threshold: 25
            });
            
        });
        
    },
    
    toplist_show: function()
    {
        var content_wrapper = $("#content-wrapper");
        var toplist_wrapper = $("#content-toplist");
        
        // Sync both div heights
        $(toplist_wrapper).height( $(content_wrapper).height() );
        
        if($(content_wrapper).is(":visible"))
        {
            content_wrapper.hide();
            //toplist_wrapper.show();
            toplist_wrapper.fadeIn(800);
            $(this).addClass('toplist-active');
        }
        else
        {
            toplist_wrapper.hide();
            //content_wrapper.show();
            content_wrapper.fadeIn(800);
            $(this).removeClass('toplist-active');
        }        
    }
    
};


/**
 * Payment functions
 */
airtel_payments = {
    
    sms_text: function()
    {
        // Get changed values
        var key = $('[name=prices_sms] option:selected').val();
        
        // Get real price
        var price = default_prices[key];
        
        // Set new price in text
        $('#price').text(price);
        
        // Set new KEY
        $('#key').text(key);
        
        // Set new link params into QR data
        var uri = 'SMSTO:' + short_number + ':' + base_keyword + key;
        
        // Generate new QR image
        $('.qrimg').attr('src', 'https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=' + encodeURI(uri));
    },
    
    ibank_anchor: function()
    {
        var key = $('[name=prices_ibank] option:selected').val();
        var href = 'http://ibank.airtel.lv/handler/index/' + key + '/LVL/';

        // Open window
        window.airtel_ibank_window = window.open(href, 'ibank_airtel', 'width=800, height=700, scrollbars=yes, status=yes, resizable=yes, screenx=200, screeny=100');
        
        // Open notification
        $('#payment-window-notification').modal({
            backdrop: 'static',
            keyboard: false
        });
        
        // Set function to check window status
        airtel_payments.ibank_check_status();
        
        // Check interval
        setTimeout('airtel_payments.ibank_check_status()', 2000);
    },

    ibank_check_status: function()
    {
        if (window.airtel_ibank_window.closed == false) {} else {
            $('#payment-window-notification').modal('hide');
        }
        setTimeout('airtel_payments.ibank_check_status()', 500);
    },

    paypal_anchor: function ()
    {
        // Get changed values
        var key = $('[name=prices_paypal] option:selected').val();
        var href = 'http://paypal.airtel.lv/handler/index/' + key + '/EUR/';
        
        // Open window
        window.airtel_paypal_window = window.open(href, 'paypal_airtel', 'width=1000, height=768, scrollbars=yes, status=yes, resizable=yes, screenx=200, screeny=100');
        
        // Open notification
        $('#payment-window-notification').modal({
            backdrop: 'static',
            keyboard: false
        });
        
        // Set function to check window status
        airtel_payments.paypal_check_status();
        
        // Check interval
        setTimeout('airtel_payments.paypal_check_status()', 2000);
    },
    
    paypal_check_status: function()
    {
        if (window.airtel_paypal_window.closed == false) {} else {
            $('#payment-window-notification').modal('hide');
        }
        setTimeout('airtel_payments.paypal_check_status()', 500);
    }

};