<?php

//
// display the weather-data 
//
function show($wpfcid,$w,$args,$wpfvars)
{
  extract($args);
  extract($wpfvars);

  // get translations
  $tl=array();
  $tl=set_translation($wpf_language);
    
  // ouput current conditions
  $plugin_path = get_settings('siteurl') . '/wp-content/plugins/wp-forecast';
  $out ="";
  $out .="\n<div class=\"wp-forecast\">\n";

  $out .="<b>".$tl['title']."</b>\n";
  // if error print an error message and return
  if ( count($w)<=0) {
    $out .= $tl["Sorry, no valid weather data available."]."<br />";
    $out .= $tl["Please try again later."]."</div>";
    echo $before_widget.$before_title.$after_title.$out.$after_widget;
    return false;
  }
  
  $out .= "<div>";
  if ( $locname != "" ) {
    $out .= $tl['loc'].": ".$locname."</div>\n";
  } else {
    $out .= $tl['loc'].": ".$w["city"]." ".$w["state"]."</div>\n";
  }
  $out .="<table class=\"wp-forecast\">\n";
  
  // show date / time   

  // if current time should be used calc current local time
  if ($currtime=="1") {
   
    $lt = time() - date("Z"); // this is the GMT
    $ct  = $lt + (3600 * ($w['gmtdiff'])); // local time
    if ( $w['gmtdiffdls'] == 1)
      $ct += 3600; // time with daylightsavings 
    
    $cts = strftime("%c",$ct);
  } else {
    // else take given accuweather time
    $cts = $w['fc_obsdate_1']." ".$w['time'];
  }

 if (substr($dispconfig,18,1) == "1" or substr($dispconfig,1,1) == "1") {
   $out .= "<tr align=\"center\"><td colspan=\"2\">"; 
   if (substr($dispconfig,18,1) == "1") 
     $out .= date_i18n($fc_date_format, strtotime($cts));
   else
     $out .= $tl['time'].": ";
   
   if (substr($dispconfig,18,1) == "1" and substr($dispconfig,1,1) == "1")
     $out .= ", ";
   
   if (substr($dispconfig,1,1) == "1") 
     $out .= date_i18n($fc_time_format, strtotime($cts))."<br />";
   $out .= "</td></tr>";
 }
 
  // show icon
  if (substr($dispconfig,0,1) == "1") {
    $out .= "<tr><td><img src='".$plugin_path."/icons/".$w["weathericon"].".gif' alt='".$tl[$w["weathericon"]]."' /></td>\n";
  } else {
    $out .= "<tr><td></td>\n";
  }
  
  $out .= "<td>";
  
  // show short description
  if (substr($dispconfig,2,1) == "1") 
    $out .= $tl[$w["weathericon"]]."<br />";
  
  // show temperatur
  if (substr($dispconfig,3,1) == "1") {
    $out .= $tl['tmp'].": ".$w["temperature"]."&deg;";
    $out .= $w['un_temp']."</td></tr></table>\n";
  } else {
    $out .="</td></tr></table>\n";
  }

  $out .= "<div class=\"wp-forecast-details\">";
  // show realfeel
  if (substr($dispconfig,4,1) == "1") 
    $out .= $tl['flik'].": ".$w["realfeel"]."&deg;".$w['un_temp']."<br />\n";
  // show pressure
  if (substr($dispconfig,5,1) == "1") 
    $out .= $tl['barr'].": ".$w["pressure"]." ".$w["un_pres"]."<br />\n";
  
  // show humiditiy
  if (substr($dispconfig,6,1) == "1") 
    $out .= $tl['hmid'].": ".$w["humidity"]."<br />\n";
  
  // show wind
  if (substr($dispconfig,7,1) == "1") {
    $wstr=windstr($metric,$w["windspeed"],$windunit);
    $winddir=$w["winddirection"];
    // for german language replace East with Ost or E with O
    if ($wpf_language=="de")
      $winddir=str_replace("E","O",$winddir);
    
    $out .= $tl['winds'].": ".$wstr." " . $winddir."<br />\n";
  }
  
  // show windgusts
  if (substr($dispconfig,22,1) == "1") {
    $wstr=windstr($metric,$w["wgusts"],$windunit);
    $out .= $tl['Windgusts'].": ".$wstr."<br />\n";
  }
  
  
  $sunarr = explode(" ",$w["sun"]);
  
  // show sunrise
  if (substr($dispconfig,8,1) == "1")
    $out .= $tl['sunrise'].": ".$sunarr[0]."<br />\n";
  
  // show sunset
  if (substr($dispconfig,9,1) == "1")
    $out .= $tl['sunset'].": ".$sunarr[1]."<br />\n";
  
  // show copyright
  if (substr($dispconfig,21,1) == "1")
    $out .= "<div style=\"text-align:center;\"><font size='-3'><a href=\"http://www.accuweather.com\">&copy; 2007 AccuWeather, Inc.</a></font></div>";

  $out .="</div></div>\n";
  echo $before_widget.$before_title.$after_title.$out.$after_widget;
  
  // output forecast
  $out1="";
  for ($i = 1; $i < 10; $i++) {
    
    // check active forecast for day number i
    if (substr($daytime,$i-1,1)=="1" or substr($nighttime,$i-1,1) =="1") {
      $out1 .="\n<div class=\"wp-forecast\">\n";
      $out1 .= $tl["Forecast"]." ";
      $out1 .= date_i18n($fc_date_format, strtotime($w['fc_obsdate_'.$i]))."<br />";
    }
    
    // check for daytime information
    if (substr($daytime,$i-1,1)=="1" ) {
      $out1 .="<table class=\"wp-forecast\" cellpadding='3' cellspacing='2'>\n";
      $out1 .= "<tr><td valign='top' align='center'>".$tl['day']."<br />";
      
      // show icon
      if (substr($dispconfig,10,1) == "1") {
	$out1 .= "<img src='".$plugin_path."/icons/".$w["fc_dt_icon_".$i].".gif' width='48' alt='".$tl[$w["fc_dt_icon_".$i]]."' />";
      } else {
	$out1 .= "&nbsp;";
      }
      $out1 .= "</td><td>\n";
      
      // show short description
      if (substr($dispconfig,11,1) == "1")
	$out1 .= $tl[$w["fc_dt_icon_".$i]]."<br />";
      
      // show temperature
      if (substr($dispconfig,12,1) == "1") 
	$out1 .= $w["fc_dt_htemp_".$i]."&deg;".$w['un_temp']."<br />";
      
      
      // show wind
      if (substr($dispconfig,13,1) == "1") {
	$wstr=windstr($metric,$w["fc_dt_windspeed_".$i],$windunit);
	$winddir=$w["fc_dt_winddir_".$i];
	// for german language replace East with Ost or E with O
	if ($wpf_language=="de")
	  $winddir=str_replace("E","O",$winddir);
	
	$out1 .= $tl['winds'].": ".$wstr." " . $winddir."<br />\n";
      }
      
      // show windgusts
      if (substr($dispconfig,23,1) == "1") {
	$wstr=windstr($metric,$w["fc_dt_wgusts_".$i],$windunit);
	$out1 .= $tl['Windgusts'].": ".$wstr."\n";
      }
      
      $out1 .= "</td></tr></table>\n";
    }

    // open div class for css if applicable 
    if (substr($daytime,$i-1,1)=="1" or substr($nighttime,$i-1,1) =="1") 
	$out1.="<div class=\"wp-forecast-details\">";
    
    // check for nighttime information
    if (substr($nighttime,$i-1,1)=="1" ) {
      $out1 .="<table class=\"wp-forecast\" cellpadding='3' cellspacing='2'>\n";
      $out1 .= "<tr><td valign='top' align='center'>".$tl['night']."<br />";
      if (substr($dispconfig,14,1) == "1") { 
	$out1 .= "<img src='".$plugin_path."/icons/".$w["fc_nt_icon_".$i].".gif' width='48' alt='".$tl[$w["fc_nt_icon_".$i]]."' />";
      } else {
	$out1 .= "&nbsp;";
      }
      $out1 .= "</td><td>\n";
      
      // show short description
      if (substr($dispconfig,15,1) == "1") 
	$out1 .= $tl[$w["fc_nt_icon_".$i]]."<br />";
      
      // show temperature
      if (substr($dispconfig,16,1) == "1") {
	$out1 .= $w["fc_nt_ltemp_".$i]."&deg;".$w['un_temp']."<br />";
      }
      
      // show wind
      if (substr($dispconfig,17,1) == "1") {
	$wstr=windstr($metric,$w["fc_nt_windspeed_".$i],$windunit);
	$winddir=$w["fc_nt_winddir_".$i];
	// for german language replace East with Ost or E with O
	if ($wpf_language=="de")
	  $winddir=str_replace("E","O",$winddir);
	
	$out1 .= $tl['winds'].": ".$wstr." " . $winddir."<br />\n";
      }
      
      // show windgusts
      if (substr($dispconfig,24,1) == "1") {
	$wstr=windstr($metric,$w["fc_nt_wgusts_".$i],$windunit);
	$out1 .= $tl['Windgusts'].": ".$wstr."\n";
      }
      
      $out1 .= "</td></tr></table>\n";
    }	
    // close div block
    if (substr($daytime,$i-1,1)=="1" or substr($nighttime,$i-1,1) =="1") 
      $out1 .="</div></div>\n";
  }
  // only print if there is something to show :-)
  if ($out1!="")
    echo $before_widget.$before_title.$after_title.$out1.$after_widget;
}
?>