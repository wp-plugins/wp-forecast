<?php
// this is the javascript code for the ckeck/uncheck all forecast boxes
?>
<script type="text/javascript">
   function check(fname)
    {
      if (fname=="day") {
	sammelvalue=document.woptions.alldays.checked;
	document.woptions.day1.checked=sammelvalue; 
	document.woptions.day2.checked=sammelvalue; 
	document.woptions.day3.checked=sammelvalue; 
	document.woptions.day4.checked=sammelvalue; 
	document.woptions.day5.checked=sammelvalue; 
	document.woptions.day6.checked=sammelvalue; 
	document.woptions.day7.checked=sammelvalue; 
	document.woptions.day8.checked=sammelvalue; 
	document.woptions.day9.checked=sammelvalue;
      } else {
	sammelvalue=document.woptions.allnight.checked;
	document.woptions.night1.checked=sammelvalue; 
	document.woptions.night2.checked=sammelvalue; 
	document.woptions.night3.checked=sammelvalue; 
	document.woptions.night4.checked=sammelvalue; 
	document.woptions.night5.checked=sammelvalue; 
	document.woptions.night6.checked=sammelvalue; 
	document.woptions.night7.checked=sammelvalue; 
	document.woptions.night8.checked=sammelvalue; 
	document.woptions.night9.checked=sammelvalue;
      }
      return !sammelvalue;
    }
<?php
 // this is the javascript code for switchgin between weather providers
?>
   function apifields(service)
    {
      if (service=="accu") {
	document.woptions.apikey1.disabled=true;
	// document.woptions.apikey2.disabled=true;
	if (typeof(document.woptions.allnight) != "undefined"){
	    document.woptions.allnight.disabled=false;
	    document.woptions.night1.disabled=false;
	    document.woptions.night2.disabled=false;
	    document.woptions.night3.disabled=false;
	    document.woptions.night4.disabled=false;
	    document.woptions.night5.disabled=false;
	    document.woptions.night6.disabled=false;
	    document.woptions.night7.disabled=false;
	    document.woptions.night8.disabled=false;
	    document.woptions.night9.disabled=false;
	    document.woptions.day8.disabled=false;
	    document.woptions.day9.disabled=false;
	    document.woptions.d_d_wind.disabled=false;
	    document.woptions.d_n_wind.disabled=false;	
	    document.woptions.d_d_wgusts.disabled=false;	
	    document.woptions.d_n_wgusts.disabled=false;
	    document.woptions.day6.disabled=false; 
	    document.woptions.day7.disabled=false; 
	    document.woptions.day8.disabled=false; 
	    document.woptions.day9.disabled=false;
	    document.woptions.d_c_sunrise.disabled=false;
	    document.woptions.d_c_sunset.disabled=false;
	    document.woptions.d_d_wind.disabled=false;
	    document.woptions.d_n_wind.disabled=false; 
	    document.woptions.d_c_humid.disabled=false;
	    document.woptions.d_c_press.disabled=false;
	    document.woptions.d_c_real.disabled=false;	
	    document.woptions.d_c_wgusts.disabled=false;
	    document.woptions.d_d_wgusts.disabled=false;	
	    document.woptions.d_n_wgusts.disabled=false;
	    //document.woptions.searchloc.disabled=false;
	    //document.woptions.search_loc.disabled=false;
	}
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

	    //document.woptions.searchloc.disabled=true;
	    //document.woptions.search_loc.disabled=true;
	}
      }

      if (service=="bug") {
	document.woptions.apikey1.disabled=false;
	//document.woptions.apikey2.disabled=true;
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
	    document.woptions.day8.disabled=true;
	    document.woptions.day9.disabled=true;
	    document.woptions.d_d_wind.checked=false;
	    document.woptions.d_n_wind.checked=false;	
	    document.woptions.d_d_wgusts.checked=false;	
	    document.woptions.d_n_wgusts.checked=false;
	    document.woptions.d_d_wind.disabled=true;
	    document.woptions.d_n_wind.disabled=true;	
	    document.woptions.d_d_wgusts.disabled=true;	
	    document.woptions.d_n_wgusts.disabled=true;

	    //document.woptions.searchloc.disabled=false;
	    //document.woptions.search_loc.disabled=false;

	   
	}
      }
      nwfields_update();
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


<?php
// the part for wpmu disabled fields by admin
global $blog_id;
if ( function_exists("is_multisite") && is_multisite() && $blog_id!=1)
{
    echo "function wpf_wpmu_disable_fields()\n{\n";
    // read defaults and allowed fields
    $allowed  = maybe_unserialize(wpf_get_option("wpf_sa_allowed"));
    
    foreach($allowed as $f => $fswitch)
    {
	$fname = substr($f,3); // strip ue_ prefix
	
	if ( $fswitch != "1" )
	{
	    if ($fname != "dispconfig" and $fname != "forecast") {
		// replace value in av with forced default
		echo "document.getElementById('$fname').disabled=true;\n";
	    }
	    else if ($fname == "dispconfig") {
		$do = array('d_c_icon','d_c_time','d_c_short','d_c_temp','d_c_real','d_c_press',
			    'd_c_humid','d_c_wind','d_c_sunrise','d_c_sunset','d_d_icon','d_d_short',
			    'd_d_temp','d_d_wind','d_n_icon','d_n_short','d_n_temp','d_n_wind','d_c_date',
			    'd_c_copyright','d_c_wgusts','d_d_wgusts','d_n_wgusts',
			    'd_c_accuweather','d_c_aw_newwindow','d_c_copyright','d_c_accuweather');
		
		foreach ($do as $i) {
		    echo "document.getElementById('$i').disabled=true;\n";
		}
	    } else {
		// for forecast options
		$nd = array('day1','day2','day3','day4','day5','day6','day7','day8','day9',
			    'night1','night2','night3','night4','night5','night6','night7','night8','night9');
		foreach ($nd as $i) {
		    echo "document.getElementById('$i').disabled=true;\n"; 
		}
	    } 
	}
    }
    echo "}\n";   
}
?>
</script>