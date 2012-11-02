// Check format of ip address
$.validator.addMethod('valid_ip', function(value) {
    var ip = /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/;
    return value.match(ip);
}, 'Ievadiet IP adresi pareizajā formātā');


$.validator.addMethod('valid_authid', function(value) {
    var authid = /^STEAM_0:(0|1):[1-9]{1}[0-9]{0,19}$/;
    return value.match(authid);
}, 'Ievadiet STEAM:ID pareizajā formātā');