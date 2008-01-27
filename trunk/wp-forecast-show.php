<?php

//
// display the weather-data 
//
// the following structure will be used:
//
// <div class=wp-forecast">
//    <div class="wp-forecast-curr">
//      <div class="wp-forecast-curr-details">
//      </div>
//      <div class="wp-forecast-copyright">
//      </div>
//    </div>
//    <div class="wp-forecast-fc">
//      <div class="wp-forecast-fc-details">
//      </div>
//    </div>
// </div>
//
//
//
function show($wpfcid,$w,$args,$wpfvars)
{
  global $wpf_debug;

  if ($wpf_debug > 0)
    pdebug("Start of show ()");

  // check how we are called as a widget or from sidebar
  if (sizeof($args)==0)
    $show_from_widget=0;
  else
    $show_from_widget=1;


  extract($args);
  extract($wpfvars);

  $plugin_path = get_settings('siteurl') . '/wp-content/plugins/wp-forecast';

  // get translations
  if(function_exists('load_textdomain')) 
     load_textdomain("wp-forecast_".$wpf_language, ABSPATH . "wp-content/plugins/wp-forecast/lang/".$wpf_language.".mo");


  // ouput current conditions
  $out ="";
  $out .="\n<div class=\"wp-forecast-curr\">\n";

  
  // if error print an error message and return
  if ( count($w)<=0) {
    $out .= __("Sorry, no valid weather data available.","wp-forecast_".$wpf_language)."<br /></div>";
    $out .= __("Please try again later.","wp-forecast_".$wpf_language)."</div>";
    // print it
    if ( $show_from_widget == 1 )
      echo $before_widget . $before_title . $title . $after_title . $out . $after_widget;
    else
      echo $out;

    return false;
  }
  
  $out .= "<div>";

  if ( $locname != "" ) 
    $out .= $locname."</div>\n";
  else 
    $out .= $w["city"]." ".$w["state"]."</div>\n";
  
  $out .="<table class=\"wp-forecast-curr\">\n";
  
  // show date / time   

  // if current time should be used calc current local time
  if ($currtime=="1") {
   
    $lt = time() - date("Z"); // this is the GMT
    $ct  = $lt + (3600 * ($w['gmtdiff'])); // local time
    if ( $w['gmtdiffdls'] == 1)
      $ct += 3600; // time with daylightsavings 
  } else {
    // else take given accuweather time
    $cts = $w['fc_obsdate_1']." ".$w['time'];
    $ct = strtotime($cts);
  }

 if (substr($dispconfig,18,1) == "1" or substr($dispconfig,1,1) == "1") {
   $out .= "<tr align=\"center\"><td colspan=\"2\">"; 
   if (substr($dispconfig,18,1) == "1") 
     $out .= date_i18n($fc_date_format, $ct);
   else
     $out .= __('time',"wp-forecast_".$wpf_language).": ";
   
   if (substr($dispconfig,18,1) == "1" and substr($dispconfig,1,1) == "1")
     $out .= ", ";
   
   if (substr($dispconfig,1,1) == "1") 
     $out .= date_i18n($fc_time_format, $ct)."<br />";
   $out .= "</td></tr>";
 }
 
  // show icon
  if (substr($dispconfig,0,1) == "1") {
    $out .= "<tr><td><img src='".$plugin_path."/icons/".$w["weathericon"].".gif' alt='".__($w["weathericon"],"wp-forecast_".$wpf_language)."' /></td>\n";
  } else {
    $out .= "<tr><td></td>\n";
  }
  
  $out .= "<td>";
  
  // show short description
  if (substr($dispconfig,2,1) == "1") 
    $out .= __($w["weathericon"],"wp-forecast_".$wpf_language)."<br />";
  
  // show temperatur
  if (substr($dispconfig,3,1) == "1") {
    $out .= __('tmp',"wp-forecast_".$wpf_language).": ".$w["temperature"]."&deg;";
    $out .= $w['un_temp']."</td></tr></table>\n";
  } else {
    $out .="</td></tr></table>\n";
  }

  $out .= "<div class=\"wp-forecast-curr-details\">";
  // show realfeel
  if (substr($dispconfig,4,1) == "1") 
    $out .= __('flik',"wp-forecast_".$wpf_language).": ".$w["realfeel"]."&deg;".$w['un_temp']."<br />\n";
  // show pressure
  if (substr($dispconfig,5,1) == "1") 
    $out .= __('barr',"wp-forecast_".$wpf_language).": ".$w["pressure"]." ".$w["un_pres"]."<br />\n";
  
  // show humiditiy
  if (substr($dispconfig,6,1) == "1") 
    $out .= __('hmid',"wp-forecast_".$wpf_language).": ".$w["humidity"]."<br />\n";
  
  // show wind
  if (substr($dispconfig,7,1) == "1") {
    $wstr=windstr($metric,$w["windspeed"],$windunit);
    $winddir=$w["winddirection"];
    // for german language replace East with Ost or E with O
    if ($wpf_language=="de_DE")
      $winddir=str_replace("E","O",$winddir);
    
    $out .= __('winds',"wp-forecast_".$wpf_language).": ".$wstr." " . $winddir."<br />\n";
  }
  
  // show windgusts
  if (substr($dispconfig,22,1) == "1") {
    $wstr=windstr($metric,$w["wgusts"],$windunit);
    $out .= __('Windgusts',"wp-forecast_".$wpf_language).": ".$wstr."<br />\n";
  }
  
  
  $sunarr = explode(" ",$w["sun"]);
  
  // show sunrise
  if (substr($dispconfig,8,1) == "1")
    $out .= __('sunrise',"wp-forecast_".$wpf_language).": ".$sunarr[0]."<br />\n";
  
  // show sunset
  if (substr($dispconfig,9,1) == "1")
    $out .= __('sunset',"wp-forecast_".$wpf_language).": ".$sunarr[1]."<br />\n";
  
  // show copyright
  if (substr($dispconfig,21,1) == "1")
    $out .= "<div class=\"wp-forecast-copyright\"><a href=\"http://www.accuweather.com\">&copy; 2007 AccuWeather, Inc.</a></div>";

  $out .="</div></div>\n";

 
  // ------------------
  // 
  // output forecast
  //
  // -------------------
  $out1="";
  for ($i = 1; $i < 10; $i++) {
    
    // check active forecast for day number i
    if (substr($daytime,$i-1,1)=="1" or substr($nighttime,$i-1,1) =="1") {
      $out1 .="\n<div class=\"wp-forecast-fc\">\n";
      $out1 .= __("Forecast","wp-forecast_".$wpf_language)." ";
      $out1 .= date_i18n($fc_date_format, strtotime($w['fc_obsdate_'.$i]))."<br />";
    }
    
    // check for daytime information
    if (substr($daytime,$i-1,1)=="1" ) {
      $out1 .="<table class=\"wp-forecast-fc\" cellpadding='3' cellspacing='2'>\n";
      $out1 .= "<tr><td valign='top' align='center'>".__('day',"wp-forecast_".$wpf_language)."<br />";
      
      // show icon
      if (substr($dispconfig,10,1) == "1") {
	$out1 .= "<img src='".$plugin_path."/icons/".$w["fc_dt_icon_".$i].".gif' width='48' alt='".__($w["fc_dt_icon_".$i],"wp-forecast_".$wpf_language)."' />";
      } else {
	$out1 .= "&nbsp;";
      }
      $out1 .= "</td><td>\n";
      
      // show short description
      if (substr($dispconfig,11,1) == "1")
	$out1 .= __($w["fc_dt_icon_".$i],"wp-forecast_".$wpf_language)."<br />";
      
      // show temperature
      if (substr($dispconfig,12,1) == "1") 
	$out1 .= $w["fc_dt_htemp_".$i]."&deg;".$w['un_temp']."<br />";
      
      
      // show wind
      if (substr($dispconfig,13,1) == "1") {
	$wstr=windstr($metric,$w["fc_dt_windspeed_".$i],$windunit);
	$winddir=$w["fc_dt_winddir_".$i];
	// for german language replace East with Ost or E with O
	if ($wpf_language=="de_DE")
	  $winddir=str_replace("E","O",$winddir);
	
	$out1 .= __('winds',"wp-forecast_".$wpf_language).": ".$wstr." " . $winddir."<br />\n";
      }
      
      // show windgusts
      if (substr($dispconfig,23,1) == "1") {
	$wstr=windstr($metric,$w["fc_dt_wgusts_".$i],$windunit);
	$out1 .= __('Windgusts',"wp-forecast_".$wpf_language).": ".$wstr."\n";
      }
      
      $out1 .= "</td></tr></table>\n";
    }

    // open div class for css if applicable 
    if (substr($daytime,$i-1,1)=="1" or substr($nighttime,$i-1,1) =="1") 
	$out1.="<div class=\"wp-forecast-fc-details\">";
    
    // check for nighttime information
    if (substr($nighttime,$i-1,1)=="1" ) {
      $out1 .="<table class=\"wp-forecast-fc\" cellpadding='3' cellspacing='2'>\n";
      $out1 .= "<tr><td valign='top' align='center'>".__('night',"wp-forecast_".$wpf_language)."<br />";
      if (substr($dispconfig,14,1) == "1") { 
	$out1 .= "<img src='".$plugin_path."/icons/".$w["fc_nt_icon_".$i].".gif' width='48' alt='".__($w["fc_nt_icon_".$i],"wp-forecast_".$wpf_language)."' />";
      } else {
	$out1 .= "&nbsp;";
      }
      $out1 .= "</td><td>\n";
      
      // show short description
      if (substr($dispconfig,15,1) == "1") 
	$out1 .= __($w["fc_nt_icon_".$i],"wp-forecast_".$wpf_language)."<br />";
      
      // show temperature
      if (substr($dispconfig,16,1) == "1") {
	$out1 .= $w["fc_nt_ltemp_".$i]."&deg;".$w['un_temp']."<br />";
      }
      
      // show wind
      if (substr($dispconfig,17,1) == "1") {
	$wstr=windstr($metric,$w["fc_nt_windspeed_".$i],$windunit);
	$winddir=$w["fc_nt_winddir_".$i];
	// for german language replace East with Ost or E with O
	if ($wpf_language=="de_DE")
	  $winddir=str_replace("E","O",$winddir);
	
	$out1 .= __('winds',"wp-forecast_".$wpf_language).": ".$wstr." " . $winddir."<br />\n";
      }
      
      // show windgusts
      if (substr($dispconfig,24,1) == "1") {
	$wstr=windstr($metric,$w["fc_nt_wgusts_".$i],$windunit);
	$out1 .= __('Windgusts',"wp-forecast_".$wpf_language).": ".$wstr."\n";
      }
      
      $out1 .= "</td></tr></table>\n";
    }	
    // close div block
    if (substr($daytime,$i-1,1)=="1" or substr($nighttime,$i-1,1) =="1") 
      $out1 .="</div></div>\n";
  }

  
  // print it
  if ( $show_from_widget == 1 )
    echo $before_widget . $before_title . $title . $after_title;
  
  echo "<div class=\"wp-forecast\">" . $out . $out1 . "</div><br />";;
  
  if ( $show_from_widget == 1 )
    echo $after_widget;
  
  if ($wpf_debug > 0)
    pdebug("End of show ()");    
  
}
?>