$(document).ready(function() {
    
    if(iframe_mode)
    {
        setInterval(function(){
            parent.$("iframe").height($("#container").height());
            parent.$("iframe").width( $("#container").width());
        }, 100);
    }
    
});