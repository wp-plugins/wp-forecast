<?php
/* This file is part of the wp-forecast plugin for wordpress */

/*  Copyright 2010-2011  Hans Matzen  (email : webmaster at tuxlog dot de)

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

if (!function_exists('google_xml_parser')) {
    global $wpf_weather,$wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber;
    
    //
    // build the url from the parameters and fetch the weather-data
    // return it as one long string
    //
    function google_get_weather($uri,$loc,$lang)
    {
	pdebug(1,"Start of google_get_weather ()");

	$url=$uri . "weather=" . urlencode($loc) . "&hl=" . $lang; 

	$xml = fetchURL($url);

	pdebug(1,"End of google_get_weather ()");
    
    return $xml;
  }

  // start_element() - called for every start tag
  function google_start_element( $parser, $name, $attribute )
    {
      global $wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber,$wpf_weather;

      pdebug(2,"Start of start_element ()");

      $wpf_path_table = 
	  array(
	      "/XML_API_REPLY/WEATHER/FORECAST_INFORMATION/UNIT_SYSTEM"  => "un_system",
	      "/XML_API_REPLY/WEATHER/FORECAST_INFORMATION/CITY"  => "city",
	      "/XML_API_REPLY/WEATHER/FORECAST_INFORMATION/LATITUDE_E6"   => "lat",
	      "/XML_API_REPLY/WEATHER/FORECAST_INFORMATION/LONGITUDE_E6"   => "lon",
	      "/XML_API_REPLY/WEATHER/CURRENT_CONDITIONS/TEMP_C"  => "temperature",
	      "/XML_API_REPLY/WEATHER/CURRENT_CONDITIONS/HUMIDITY"  => "humidity",
	      "/XML_API_REPLY/WEATHER/CURRENT_CONDITIONS/CONDITION"  => "weathertext",
	      "/XML_API_REPLY/WEATHER/CURRENT_CONDITIONS/ICON"  => "weathericon",
	      "/XML_API_REPLY/WEATHER/CURRENT_CONDITIONS/WIND_CONDITION"  => "wind",
	      "/XML_API_REPLY/WEATHER/FORECAST_INFORMATION/CURRENT_DATE_TIME"  => "fc_obsdate",
	      "/XML_API_REPLY/WEATHER/FORECAST_CONDITIONS/CONDITION"  => "fc_dt_short",
	      "/XML_API_REPLY/WEATHER/FORECAST_CONDITIONS/ICON"  => "fc_dt_icon",
	      "/XML_API_REPLY/WEATHER/FORECAST_CONDITIONS/HIGH"  => "fc_dt_htemp",  
	      "/XML_API_REPLY/WEATHER/FORECAST_CONDITIONS/LOW"  => "fc_dt_ltemp",   
	      "/XML_API_REPLY/WEATHER/PROBLEM_CAUSE" => "failure"
	      );

      $wpf_pstack .= "/$name";

      if (isset( $wpf_path_table[ $wpf_pstack ])) {
	  $wpf_go_ahead = $wpf_path_table[$wpf_pstack];

	  if (strpos( $wpf_pstack, "XML_API_REPLY/WEATHER/FORECAST_CONDITIONS") > 0 ) {
	      $wpf_weather[$wpf_path_table[ $wpf_pstack ] .  "_" . $wpf_fc_daynumber ] = $attribute['DATA'];
	  } else
	      $wpf_weather[$wpf_path_table[ $wpf_pstack ]] = $attribute['DATA'];
      }
      
      pdebug(2,"End of start_element ()");
    }
  
  // end_element() - called for every end tag
  function google_end_element( $parser, $name )
    {
      global $wpf_pstack,$wpf_fc_daynumber;
      
      pdebug(2,"Start of end_element ()");

      // reduce xml path
      $wpf_pstack = substr($wpf_pstack,0, strrpos($wpf_pstack,"/"));
      
      if ($name == "FORECAST_CONDITIONS")
	  $wpf_fc_daynumber +=1;

      pdebug(2,"End of end_element ()");
    }
  
  
  // daten() - called for everey cdata 
  function google_daten( $parser, $data )
    {
      global $wpf_weather,$wpf_go_ahead;
 
      pdebug(2,"Start of daten ()");

      // nothing to do here since google uses attributes

      pdebug(2,"End of daten ()");
    }

  //
  // parses the xml for the paths in path_tabelle 
  //
  
  function google_xml_parser($xmlstring) 
  {
      global $wpf_weather,$wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber;
      
      pdebug(1,"Start of google_xml_parser ()");
      
      $wpf_weather=array();
      $xmlerror="";
      
      $wpf_pstack="";
      $wpf_go_ahead = "";
      $wpf_fc_daynumber="0";
      
      // create an xml parser object
      $parser = xml_parser_create();
      
      // set parameters for xml parser
      xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, true ); 
      
      // set handler for start and end-tags 
      xml_set_element_handler( $parser,"google_start_element","google_end_element");
      // set handler for CDATA
      xml_set_character_data_handler( $parser,"google_daten");
      
      // try to parse the xml
      if( !xml_parse( $parser, $xmlstring, true ) )
      {
	  // Error --> stop execution
	  $xmlerror="XML Fehler: " . 
	      xml_error_string( xml_get_error_code( $parser ) ) . " in Zeile " .
	      xml_get_current_line_number( $parser )
	      ;
      }
      
      // Vom XML-Parser belegten Speicher freigeben
      xml_parser_free( $parser );
      
      // check for error
      if ($xmlerror!="") {
	  $wpf_weather=array();
      }
      
      pdebug(1,"End of wpf_xml_parser ()");
      
      // and return result, empty array if error
      return $wpf_weather;
  }
  

  
//
// returns the widget data as an array one line per item
//
function google_forecast_data($wpfcid="A", $language_override=null)
{
  pdebug(1,"Start of function google_forecast_data ()");
  
  $wpf_vars=get_wpf_opts($wpfcid);
  if (!empty($language_override)) {
    $wpf_vars['wpf_language']=$language_override;
  } 

  extract($wpf_vars);
  $w=maybe_unserialize(wpf_get_option("wp-forecast-cache".$wpfcid));

  // get translations
 if(function_exists('load_plugin_textdomain')) {
  	add_filter("plugin_locale","wpf_lplug",10,2);
   	load_plugin_textdomain("wp-forecast_".$wpf_language, false, dirname( plugin_basename( __FILE__ ) ) . "/lang/");
   	remove_filter("plugin_locale","wpf_lplug",10,2);
  }
  
  
  $weather_arr=array();

  // --------------------------------------------------------------
  // calc values for current conditions
  if ( ! isset($w['failure'])) {
    $weather_arr['servicelink']= 'http://www.google.de/#q=google+weather+' . $location;
    $weather_arr['location'] = $locname;
    $weather_arr['locname']= $w["city"];
    
    $lt = time() - date("Z"); // this is the GMT
    $ct  = $lt + (3600 * ($w['gmtdiff'])); // local time
    $ct =  strtotime(current_time('mysql',false));
    
    if ( $w['gmtdiffdls'] == 1)
      $ct += 3600; // time with daylightsavings 

    
    $ct = $ct + $wpf_vars['timeoffset'] * 60; // add or subtract time offset
    
    $weather_arr['blogdate']=date_i18n($fc_date_format, $ct);
    $weather_arr['blogtime']=date_i18n($fc_time_format, $ct);
    
    $cts = $w['fc_obsdate'];
    $ct = strtotime($cts);
    //$ct = $ct + $wpf_vars['timeoffset'] * 60; // add or subtract time offset
    $weather_arr['googledate']=date_i18n($fc_date_format, $ct);
    $weather_arr['googletime']=date_i18n($fc_time_format, $ct);
    
    
    $iconfile="http://www.google.de" . $w['weathericon'];
    $weather_arr['icon'] = $iconfile;
    $weather_arr['iconcode'] = $w['weathericon'];

    $weather_arr['shorttext']= $w["weathertext"];
    
    $weather_arr['temperature']=$w["temperature"]. "&deg;".$w['un_temp'];
    $weather_arr['humidity'] = trim(substr($w["humidity"], strpos($w["humidity"], " "),3));
    $wdirarr = explode(" ", $w['wind']);
    $weather_arr['windspeed']=windstr($metric,$wdirarr[3] / 3.6,"kmh");
    $weather_arr['winddir']=translate_winddir($wdirarr[1],"wp-forecast_".$wpf_language);
    $weather_arr['copyright']='<a href="http://www.google.com">&copy; '.date("Y").' GoogleWeather</a>';
    
    
    // calc values for forecast
    for ($i = 0; $i < 4; $i++) {
	// daytime forecast
	$weather_arr['fc_obsdate_'.$i] = date_i18n($fc_date_format, strtotime($w['fc_obsdate'])+($i * 3600 *24));
	$weather_arr["fc_dt_icon_".$i] = "http://www.google.de" . $w['fc_dt_icon_' . $i];
	$weather_arr["fc_dt_iconcode_".$i] = $w['fc_dt_icon_' . $i];
	$weather_arr["fc_dt_desc_".$i]= $w["fc_dt_short_".$i];
	$weather_arr["fc_dt_htemp_".$i]= $w["fc_dt_htemp_".$i]."&deg;".$w['un_temp'];
	$weather_arr["fc_dt_ltemp_".$i]= $w["fc_dt_ltemp_".$i]."&deg;".$w['un_temp'];
    } 
	// additional info
	$weather_arr['lat']=$w['lat'];
	$weather_arr['lon']=$w['lon'];
    
  }
  // fill failure anyway
  $weather_arr['failure']=( isset($w['failure']) ? $w['failure'] : '');

  pdebug(1,"End of function google_forecast_data ()");

  return $weather_arr;
}
}
?>