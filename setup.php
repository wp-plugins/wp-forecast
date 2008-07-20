<?php
/* This file is part of the wp-forecast plugin for wordpress */

/*  Copyright 2006,2007  Hans Matzen  (email : webmaster at tuxlog.de)

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
// setting up the default options in table wp-options during 
// plugin activation from the plugins page
//
function wp_forecast_activate()
{
  global $wpf_debug;
  
  if ($wpf_debug > 0)
    pdebug("Start of wp_forecast_activate ()");

  // add number of widgets, default: 1
  $count=get_option("wp-forecast-count");
   
  if ($count == "") {
    $count="1";
    add_option("wp-forecast-count",$count,
	       "Contains the number of wp-widgets","yes");
  };
  
  // add timeout for accuweather connections, default: 30
  $timeout=get_option("wp-forecast-timeout");
   
  if ($timeout == "") {
    $timeout="30";
    add_option("wp-forecast-timeout",$timeout,
	       "Timeout in seconds for accuweather connections","yes");
  };
  for ($i=0;$i<$count;$i++) {
    $wpfcid = get_widget_id( $i );

    // get options just in case
    $location=get_option("wp-forecast-location".$wpfcid);
    $locname=get_option("wp-forecast-locname".$wpfcid);
    $refresh=get_option("wp-forecast-refresh".$wpfcid); 
    $metric=get_option("wp-forecast-metric".$wpfcid); 
    $wpf_language=get_option("wp-forecast-language".$wpfcid);
    $daytime=get_option("wp-forecast-daytime".$wpfcid);
    $nighttime=get_option("wp-forecast-nighttime".$wpfcid);
    $dispconfig=get_option("wp-forecast-dispconfig".$wpfcid);
    $windunit = get_option("wp-forecast-windunit".$wpfcid);
    $weather = get_option("wp-forecast-cache".$wpfcid);
    $expire = get_option("wp-forecast-expire".$wpfcid);
    $currtime = get_option("wp-forecast-currtime".$wpfcid);
    $title = get_option("wp-forecast-title".$wpfcid);

    // if the options dont exists, add the defaults
    if ($location == "") {
      $location="EUR|DE|GM007|FRANKFURT AM MAIN";
      add_option("wp-forecast-location".$wpfcid,$location,
		 "Contains the location code from accuweather","yes");
    };
    
    if ($locname == "") {
      $locname="Frankfurt am Main";
      add_option("wp-forecast-location".$wpfcid,$locname,
		 "Contains the location name to show","yes");
    };
    
    if ($refresh == "") {
      $refresh="600";
      add_option("wp-forecast-refresh".$wpfcid,$refresh,
		 "Contains the intervall the local weather data is renewed",
		 "yes");
    };
    
    if ($metric == "") {
      $metric="1";
      add_option("wp-forecast-metric".$wpfcid,$metric,
		 "1 if you want to use metric scheme, else 0","yes");
    };
    
    if ($wpf_language == "") {
      $wpf_language="en_US";
      add_option("wp-forecast-language".$wpfcid,$wpf_language,
		 "The lanugage code","yes");
    };
    
    
    if ($weather == "") {
      $weather="";
      add_option("wp-forecast-cache".$wpfcid,$weather,
		 "The weather cache","yes");
    }; 
    
    if ($expire == "") {
      $expire="0";
      add_option("wp-forecast-expire".$wpfcid,$expire,
		 "when weather cache expires","yes");
    }; 
    if ($daytime == "") {
      $daytime="000000000";
      add_option("wp-forecast-daytime".$wpfcid,$daytime,
		 "Switches for Daytime forecast","yes");
    };
    
    if ($nighttime == "") {
      $nighttime="000000000";
      add_option("wp-forecast-nighttime".$wpfcid,$nighttime,
		 "Switches for Nighttime forecast","yes");
    };
    
    if ($currtime == "") {
      $currtime="1";
      add_option("wp-forecast-currtime".$wpfcid,$currtime,
		 "1 if you want to use current time, else 0","yes");
    }; 

    if ($title == "") {
      $title=__("The Weather","wp-forecast_".$wpf_language);
      add_option("wp-forecast-title".$wpfcid,$title,
		 "Contains the widget title","yes");
    };
    
    // Displayconfigurationmatrix
    //                  CC    FC Day    FC Night
    // Icon              0     10        14
    // Datum            18     -         -
    // Zeit              1     -         -
    // Shorttext         2     11        15
    // Temperatur        3     12        16
    // gef. Temp         4     -         -
    // Luftdruck         5     -         - 
    // Luftfeuchte       6     -         - 
    // Wind              7     13        17
    // Windboen         22     23        24
    // Sonnenaufgang     8     -         -
    // Sonnenuntergang   9     -         - 
    // Copyright        21     -         -
    // accuweather link 25     -         -
    //
    
    if ($dispconfig == "") {
      $dispconfig="11111111111111111111111111";
      add_option("wp-forecast-dispconfig".$wpfcid,$dispconfig,
		 "Switches for shown Information","yes");
    }
    
    if ($windunit == "") {
      $windunit="ms";
      add_option("wp-forecast-windunit".$wpfcid,$windunit,
		 "Choose between ms, kmh, mph or kts","yes");
    }
  } // end of for 

  if ($wpf_debug > 0)
    pdebug("End of wp_forecast_activate ()");
}

//
// is called when plugin is deactivated and removes all
// the  wp-forecast options from the database
//
function wp_forecast_deactivate($wpfcid) 
{ 
   global $wpf_debug;
   
   if ($wpf_debug > 0)
     pdebug("Start of wp_forecast_deactivate ()");

   $count=get_option('wp-forecast-count');
   
   for ($i=0;$i<$count;$i++) {
     $wpfcid = get_widget_id( $i );
     
     delete_option("wp-forecast-location".$wpfcid);
     delete_option("wp-forecast-locname".$wpfcid);
     delete_option("wp-forecast-refresh".$wpfcid); 
     delete_option("wp-forecast-metric".$wpfcid); 
     delete_option("wp-forecast-language".$wpfcid);
     delete_option("wp-forecast-daytime".$wpfcid);
     delete_option("wp-forecast-nighttime".$wpfcid);
     delete_option("wp-forecast-dispconfig".$wpfcid);
     delete_option("wp-forecast-windunit".$wpfcid);
     delete_option("wp-forecast-cache".$wpfcid);
     delete_option("wp-forecast-expire".$wpfcid);
     delete_option("wp-forecast-currtime".$wpfcid); 
     delete_option("wp-forecast-title".$wpfcid);
   }
   delete_option('wp-forecast-timeout');
   delete_option('wp-forecast-count');
   
   if ($wpf_debug > 0)
     pdebug("End of wp_forecast_deactivate ()");
}
?>