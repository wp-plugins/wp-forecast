/*
  javascript function for wp-forecast import dialog
*/
function submit_this(uri){
    // the fields that are to be processed
    var wprovider   = document.getElementById("wprovider").value;
    
    // ajax call to itself
    jQuery.post(uri, {wprovider: wprovider}, function(data){jQuery("#message").html(data);});
    
    return false;
}


