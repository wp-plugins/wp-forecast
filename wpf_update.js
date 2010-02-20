/*

javascript for ajax like request to update the weather location
and corresponding data on the fly

 */


/* get the data for the new location */
function wpf_update()
{
    var newloc  = document.getElementById("wpf_selector").value;
    var siteuri = document.getElementById("wpf_selector_site").value; 

    jQuery.get(siteuri + "/wp-forecast-show.php", 
	       { wpfcid: newloc, header: "0" , selector: "1" },
	       function(data){
		   /*
		   var b = data.indexOf(">");
		   var e = data.lastIndexOf("<");
		   data  = data.substring(b + 1, e - 1);
		   */
		   jQuery("div#wp-forecastA").html(data);
	       });
}

/* javascript to rebuild the onLoad event for triggering 
   the first wpf_update call */

//create onDomReady Event
window.onDomReady = initReady;

// Initialize event depending on browser
function initReady(fn)
{
    //W3C-compliant browser
    if(document.addEventListener) {
	document.addEventListener("DOMContentLoaded", fn, false);
    }
    //IE
    else {
	document.onreadystatechange = function(){readyState(fn)}
    }
      }

//IE execute function
function readyState(func)
{
    // DOM is ready
      	if(document.readyState == "interactive" || document.readyState == "complete")
      	{
	    func();
      	}
}