/*
  javascript function for wp-monalisa import dialog
*/
function submit_this(){
    // the fields that are to be processed
    var wprovider   = document.getElementById("wprovider").value;
    
    // ajax call to itself
    jQuery.post("../wp-content/plugins/wp-forecast/wp-forecast-check.php", {wprovider: wprovider}, function(data){jQuery("#message").html(data);});
    
    return false;
}


