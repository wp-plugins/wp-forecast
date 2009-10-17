<?php
/* This file is part of the wp-forecast plugin for wordpress */

/*  Copyright 2006-2009  Hans Matzen  (email : webmaster at tuxlog dot de)

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
  pdebug(1,"Start of wp_forecast_activate ()");

  // add number of widgets, default: 1
  $count=get_option("wp-forecast-count");
   
  if ($count == "") {
    $count="1";
    add_option("wp-forecast-count",$count);
  };
  
  // add timeout for accuweather connections, default: 30
  $timeout=get_option("wp-forecast-timeout");
   
  if ($timeout == "") {
    $timeout="10";
    add_option("wp-forecast-timeout",$timeout);
  };

  // add switch to control option deletion during plugin deactivation
  $delopt=get_option("wp-forecast-delopt");
   
  if ($delopt == "") {
    $delopt="0";
    add_option("wp-forecast-delopt",$delopt);
  };

  // add preselected transport method for wp-forecast
  $pre_trans=get_option("wp-forecast-pre-transport");
   
  if ($pre_trans == "") {
    $pre_trans="default";
    add_option("wp-forecast-pre-transport",$pre_tans);
  };

  // add transport to use by wordpress only for wp-forecast
  $wp_trans=get_option("wp-forecast-wp-transport");
   
  if ($wp_trans == "") {
    $wp_trans="default";
    add_option("wp-forecast-wp-transport",$wp_trans);
  };

  for ($i=0;$i<$count;$i++) {
    $wpfcid = get_widget_id( $i );
    
    // get all widget options
    $av=get_wpf_opts($wpfcid);
    $weather = get_option("wp-forecast-cache".$wpfcid);
    $expire = get_option("wp-forecast-expire".$wpfcid);
    
    // if the options dont exists, add the defaults
    if ( empty($av['service']) or $av['service']=="" ) {
      $av=array();

      $av['service']="accu"; // specify the weatherservice to use 
      $av['apikey1']=""; // Partner ID or API Code for weatherbug or weather.com
      $av['apikey2']=""; // License Key for weather.com
      $av['location']="EUR|DE|GM007|FRANKFURT AM MAIN"; // location code
      $av['locname']="Frankfurt am Main"; // user defined location name
      $av['refresh']="1800"; // the intervall the local weather data is renewed
      $av['metric']="1"; // 1 if you want to use metric scheme, else 0
      $av['wpf_language']="en_US"; // language code for this widget
      $av['daytime']="000000000"; // Switches for Daytime forecast
      $av['nighttime']="000000000"; // Switches for Nighttime forecast
      $av['currtime']="1"; // 1 if you want to use current time, else 0
      $av['title']=__("The Weather","wp-forecast_".$av['wpf_language']); // the widget title    
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
      $av['dispconfig']="11111111111111111111111111"; 
      $av['windunit']="ms"; // Choose between ms, kmh, mph or kts
      $av['pdforecast']="0"; // pulldown forecast 0=No, 1=Yes
      $av['pdfirstday']="0"; // day to start pulldown with

      add_option( "wp-forecast-opts".$wpfcid, serialize($av) );
    }
    
    if ($weather == "") {
      $weather="";
      add_option("wp-forecast-cache".$wpfcid,$weather);
    }; 
    
    if ($expire == "") {
      $expire="0";
      add_option("wp-forecast-expire".$wpfcid, $expire );
    };    
  } // end of for 
  
  pdebug(1,"End of wp_forecast_activate ()");
}

//
// is called when plugin is deactivated and removes all
// the  wp-forecast options from the database
//
function wp_forecast_deactivate($wpfcid) 
{ 
    pdebug(1,"Start of wp_forecast_deactivate ()");
    
    global $wpf_maxwidgets;
    
    $delopt=get_option('wp-forecast-delopt');
    
    // only delete options when switch is set
    if ($delopt == 1) 
    {
	$count = $wpf_maxwidgets; //get_option('wp-forecast-count');
	
	for ($i=0;$i<$count;$i++) 
	{
	    $wpfcid = get_widget_id( $i );
       
	    delete_option("wp-forecast-opts".$wpfcid);
	    delete_option("wp-forecast-cache".$wpfcid);
	    delete_option("wp-forecast-expire".$wpfcid);
	}
	delete_option('wp-forecast-timeout');
	delete_option('wp-forecast-count');
	delete_option('wp-forecast-delopt');
	delete_option("wp-forecast-pre-transport");
	delete_option("wp-forecast-wp-transport");
    }
    
    pdebug(1,"End of wp_forecast_deactivate ()");
}
?>