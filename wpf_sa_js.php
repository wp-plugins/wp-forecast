<?php
// this is the javascript code for the ckeck/uncheck all forecast boxes
?>
<script type="text/javascript">
   function check(fname)
    {
      if (fname=="day") {
	sammelvalue=document.options.alldays.checked;
	document.options.day1.checked=sammelvalue; 
	document.options.day2.checked=sammelvalue; 
	document.options.day3.checked=sammelvalue; 
	document.options.day4.checked=sammelvalue; 
	document.options.day5.checked=sammelvalue; 
	document.options.day6.checked=sammelvalue; 
	document.options.day7.checked=sammelvalue; 
	document.options.day8.checked=sammelvalue; 
	document.options.day9.checked=sammelvalue;
      } else {
	sammelvalue=document.options.allnight.checked;
	document.options.night1.checked=sammelvalue; 
	document.options.night2.checked=sammelvalue; 
	document.options.night3.checked=sammelvalue; 
	document.options.night4.checked=sammelvalue; 
	document.options.night5.checked=sammelvalue; 
	document.options.night6.checked=sammelvalue; 
	document.options.night7.checked=sammelvalue; 
	document.options.night8.checked=sammelvalue; 
	document.options.night9.checked=sammelvalue;
      }
      return !sammelvalue;
    }
<?php
 // this is the javascript code for switchgin between weather providers
?>
   function apifields(service)
    {
      if (service=="accu") {
	document.options.apikey1.disabled=true;
	// document.options.apikey2.disabled=true;
	if (typeof(document.options.allnight) != "undefined"){
	    document.options.allnight.disabled=false;
	    document.options.night1.disabled=false;
	    document.options.night2.disabled=false;
	    document.options.night3.disabled=false;
	    document.options.night4.disabled=false;
	    document.options.night5.disabled=false;
	    document.options.night6.disabled=false;
	    document.options.night7.disabled=false;
	    document.options.night8.disabled=false;
	    document.options.night9.disabled=false;
	    document.options.day8.disabled=false;
	    document.options.day9.disabled=false;
	    document.options.d_d_wind.disabled=false;
	    document.options.d_n_wind.disabled=false;	
	    document.options.d_d_wgusts.disabled=false;	
	    document.options.d_n_wgusts.disabled=false;
	}
      }
      
      if (service=="com") {
	document.options.apikey1.disabled=false;
	//document.options.apikey2.disabled=false;
      }

      if (service=="google") {
	document.woptions.apikey1.disabled=false;
	//document.woptions.apikey2.disabled=false;
	if (typeof(document.woptions.allnight) != "undefined"){
	    document.woptions.allnight.disabled=true;
	    document.woptions.night1.disabled=true;
	    document.woptions.night2.disabled=true;
	    document.woptions.night3.disabled=true;
	    document.woptions.night4.disabled=true;
	    document.woptions.night5.disabled=true;
	    document.woptions.night6.disabled=true;
	    document.woptions.night7.disabled=true;
	    document.woptions.night8.disabled=true;
	    document.woptions.night9.disabled=true;
	    document.woptions.day6.disabled=true; 
	    document.woptions.day7.disabled=true; 
	    document.woptions.day8.disabled=true; 
	    document.woptions.day9.disabled=true;
	    document.woptions.d_c_sunrise.checked=false; 
	    document.woptions.d_c_sunset.checked=false; 
	    document.woptions.d_d_wind.checked=false;
	    document.woptions.d_n_wind.checked=false;	
	    document.woptions.d_c_wgusts.checked=false;
	    document.woptions.d_d_wgusts.checked=false;	
	    document.woptions.d_n_wgusts.checked=false;
	    document.woptions.d_c_humid.checked=false;
	    document.woptions.d_c_press.checked=false;
	    document.woptions.d_c_real.checked=false;
	    document.woptions.d_c_sunrise.disabled=true;
	    document.woptions.d_c_sunset.disabled=true;
	    document.woptions.d_d_wind.disabled=true;
	    document.woptions.d_n_wind.disabled=true; 
	    document.woptions.d_c_humid.disabled=true;
	    document.woptions.d_c_press.disabled=true;
	    document.woptions.d_c_real.disabled=true;	
	    document.woptions.d_c_wgusts.disabled=true;
	    document.woptions.d_d_wgusts.disabled=true;	
	    document.woptions.d_n_wgusts.disabled=true;
	}
      }

      if (service=="bug") {
	document.options.apikey1.disabled=false;
	//document.options.apikey2.disabled=true;
	if (typeof(document.options.allnight) != "undefined"){
	    document.options.allnight.disabled=true;
	    document.options.night1.disabled=true;
	    document.options.night2.disabled=true;
	    document.options.night3.disabled=true;
	    document.options.night4.disabled=true;
	    document.options.night5.disabled=true;
	    document.options.night6.disabled=true;
	    document.options.night7.disabled=true;
	    document.options.night8.disabled=true;
	    document.options.night9.disabled=true;
	    document.options.day8.disabled=true;
	    document.options.day9.disabled=true;
	    document.options.d_d_wind.checked=false;
	    document.options.d_n_wind.checked=false;	
	    document.options.d_d_wgusts.checked=false;	
	    document.options.d_n_wgusts.checked=false;
	    document.options.d_d_wind.disabled=true;
	    document.options.d_n_wind.disabled=true;	
	    document.options.d_d_wgusts.disabled=true;	
	    document.options.d_n_wgusts.disabled=true;
	}
      }
      return 0;
    }   
<?php
// this toggles the pulldown fields
?>
     
function pdfields_update()
{
    obja=document.getElementById('pdforecast');
    objb=document.getElementById('pdfirstday');
    objb.disabled = (obja.checked == false);
}
<?php
// this toggles the new window field
?>
     
function nwfields_update()
{
    obja=document.getElementById('d_c_accuweather');
    objb=document.getElementById('d_c_aw_newwindow');
    objb.disabled = (obja.checked == false);
}
</script>

