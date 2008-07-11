<?php

/*  Copyright 2006,2007,2008  Hans Matzen  (email : webmaster at tuxlog.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

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

// if called directly, get parameters from GET and output the forecast html
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
  require_once("funclib.php");
  require_once( dirname(__FILE__) . '/../../../wp-config.php');
  
  $wpfcid = attribute_escape($_GET['wpfcid']);
  $language_override = attribute_escape($_GET['language_override']);
  $args=array();
  
  $wpf_vars=get_wpf_opts($wpfcid);
  if (!empty($language_override)) {
    $wpf_vars['wpf_language']=$language_override;
  }
  $weather=str2arr(get_option("wp-forecast-cache".$wpfcid));
  
  wp_forecast_css_nowp($wpfcid);
  show($wpfcid,$weather,$args,$wpf_vars);
 }

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

  // current conditions nur ausgeben, wenn mindestens ein feld aktiv ist
  if ( strpos(substr($dispconfig,0,9),"1") > 0 or substr($dispconfig,18,1) == "1" or substr($dispconfig,21,1) == "1" or substr($dispconfig,22,1) == "1" ) {
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
    
    // ortsnamen ausgeben
    if ( $locname == "" ) 
      $out .= "<div>" . $w["city"]." ".$w["state"]."</div>\n";
    else if ( trim($locname) !="" and $locname != "&nbsp;")
      $out .= "<div>" . $locname."</div>\n";
    
    
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
      $out .= "<tr><td colspan=\"2\">"; 
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
      $out .= $w['un_temp']."</td></tr>\n";
    } else {
      $out .="</td></tr>\n";
    }
    
    $out .= "</table>\n";
    
    
    $out .= "<div class=\"wp-forecast-curr-details\">";
    // show realfeel
    if (substr($dispconfig,4,1) == "1") 
      $out .= __('flik',"wp-forecast_".$wpf_language).": ".$w["realfeel"]."&deg;".$w['un_temp']."<br />\n";
    // show pressure
    if (substr($dispconfig,5,1) == "1") 
      $out .= __('barr',"wp-forecast_".$wpf_language).": ".$w["pressure"]." ".$w["un_pres"]."<br />\n";
    
    // show humiditiy
    if (substr($dispconfig,6,1) == "1") 
      //
      // you can change the decimals of humditiy by switching 
      // the 0 to whatever you need
      //
      $out .= __('hmid',"wp-forecast_".$wpf_language).": ".round($w["humidity"],0)."%<br />\n";
    
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
  }
 
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
      $out1 .="<table class=\"wp-forecast-fc-head\" cellpadding='3' cellspacing='2'><tr><td>\n";
      $out1 .= __("Forecast","wp-forecast_".$wpf_language)." ";
      $out1 .= date_i18n($fc_date_format, strtotime($w['fc_obsdate_'.$i]))."</td></tr></table>";
    }
    
    // check for daytime information
    if (substr($daytime,$i-1,1)=="1" ) {
      $out1 .="<table class=\"wp-forecast-fc\" cellpadding='3' cellspacing='2'>\n";
      $out1 .= "<tr><td>".__('day',"wp-forecast_".$wpf_language)."<br />";
      
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
      $out1 .= "<tr><td>".__('night',"wp-forecast_".$wpf_language)."<br />";
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