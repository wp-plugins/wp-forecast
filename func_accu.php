<?php
/* This file is part of the wp-forecast plugin for wordpress */

/*  Copyright 2006-2012  Hans Matzen  (email : webmaster at tuxlog dot de)

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

if (!function_exists('accu_xml_parser')) {
  global $wpf_weather,$wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber;

  //
  // build the url from the parameters and fetch the weather-data
  // return it as one long string
  //
  function accu_get_weather($uri,$loc,$metric)
  {
    pdebug(1,"Start of accu_get_weather ()");
    
    $url=$uri . "location=" . urlencode($loc) . "&metric=" . 
      $metric; 
    
    $xml = fetchURL($url);
    
    pdebug(1,"End of accu_get_weather ()");
    
    return $xml;
  }

  // start_element() - called for every start tag
  function accu_start_element( $parser, $name, $attribute )
    {
      global $wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber,$wpf_weather;

      pdebug(2,"Start of start_element ()");

      $wpf_path_table = 
	array(
	      "/ADC_DATABASE/UNITS/TEMP"  => "un_temp",
	      "/ADC_DATABASE/UNITS/DIST"  => "un_dist",
	      "/ADC_DATABASE/UNITS/SPEED" => "un_speed",
	      "/ADC_DATABASE/UNITS/PRES"  => "un_pres",
	      "/ADC_DATABASE/UNITS/PREC"  => "un_prec",
	      "/ADC_DATABASE/LOCAL/CITY"  => "city",
	      "/ADC_DATABASE/LOCAL/STATE" => "state",
	      "/ADC_DATABASE/LOCAL/TIME"  => "time",
	      "/ADC_DATABASE/LOCAL/LAT"   => "lat",
	      "/ADC_DATABASE/LOCAL/LON"   => "lon",
	      "/ADC_DATABASE/LOCAL/GMTDIFF"  => "gmtdiff",
	      "/ADC_DATABASE/CURRENTCONDITIONS/PRESSURE"  => "pressure",
	      "/ADC_DATABASE/CURRENTCONDITIONS/TEMPERATURE"  => "temperature",
	      "/ADC_DATABASE/CURRENTCONDITIONS/REALFEEL"  => "realfeel",
	      "/ADC_DATABASE/CURRENTCONDITIONS/HUMIDITY"  => "humidity",
	      "/ADC_DATABASE/CURRENTCONDITIONS/WEATHERTEXT"  => "weathertext",
	      "/ADC_DATABASE/CURRENTCONDITIONS/WEATHERICON"  => "weathericon",
	      "/ADC_DATABASE/CURRENTCONDITIONS/WINDSPEED"  => "windspeed",
	      "/ADC_DATABASE/CURRENTCONDITIONS/WINDDIRECTION"  => "winddirection",
	      "/ADC_DATABASE/CURRENTCONDITIONS/WINDGUSTS"  => "wgusts",
	      "/ADC_DATABASE/PLANETS/SUN" => "sun",
	      "/ADC_DATABASE/FORECAST/DAY/OBSDATE"  => "fc_obsdate",
	      "/ADC_DATABASE/FORECAST/DAY/DAYTIME/TXTSHORT"  => "fc_dt_short",
	      "/ADC_DATABASE/FORECAST/DAY/DAYTIME/WEATHERICON"  => "fc_dt_icon",
	      "/ADC_DATABASE/FORECAST/DAY/DAYTIME/HIGHTEMPERATURE"  => "fc_dt_htemp",  
	      "/ADC_DATABASE/FORECAST/DAY/DAYTIME/LOWTEMPERATURE"  => "fc_dt_ltemp",   
	      "/ADC_DATABASE/FORECAST/DAY/DAYTIME/WINDSPEED"  => "fc_dt_windspeed",
	      "/ADC_DATABASE/FORECAST/DAY/DAYTIME/WINDDIRECTION"  => "fc_dt_winddir",
	      "/ADC_DATABASE/FORECAST/DAY/NIGHTTIME/WEATHERICON"  => "fc_nt_icon",
	      "/ADC_DATABASE/FORECAST/DAY/NIGHTTIME/HIGHTEMPERATURE"  => "fc_nt_htemp",  
	      "/ADC_DATABASE/FORECAST/DAY/NIGHTTIME/LOWTEMPERATURE"  => "fc_nt_ltemp",   
	      "/ADC_DATABASE/FORECAST/DAY/NIGHTTIME/WINDSPEED"  => "fc_nt_windspeed",
	      "/ADC_DATABASE/FORECAST/DAY/NIGHTTIME/WINDDIRECTION"  => "fc_nt_winddir",
	      "/ADC_DATABASE/FORECAST/DAY/DAYTIME/WINDGUST"  => "fc_dt_wgusts",
	      "/ADC_DATABASE/FORECAST/DAY/NIGHTTIME/WINDGUST"  => "fc_nt_wgusts",
	      "/ADC_DATABASE/FAILURE" => "failure"
	      );

      $wpf_pstack .= "/$name";

      if (isset( $wpf_path_table[ $wpf_pstack])) {
	$wpf_go_ahead = $wpf_path_table[$wpf_pstack];

	if ($wpf_fc_daynumber != "0" and $wpf_fc_daynumber != "")
	  $wpf_go_ahead = $wpf_go_ahead . "_" . $wpf_fc_daynumber;
      }
      
      // for forecast days
      if ($wpf_pstack=="/ADC_DATABASE/FORECAST/DAY")
	$wpf_fc_daynumber=$attribute["NUMBER"];
      
      // for current sun rise/set
      if ($wpf_pstack=="/ADC_DATABASE/PLANETS/SUN") 
	$wpf_weather["sun"]=$attribute["RISE"]." ".$attribute["SET"];

      // for current time daylightsavings
      if ($wpf_pstack== "/ADC_DATABASE/LOCAL/GMTDIFF") {
	 if ( $attribute["DAYLIGHTSAVINGS"] !="")
	  $wpf_weather['gmtdiffdls'] = $attribute["DAYLIGHTSAVINGS"];
	else
	  $wpf_weather['gmtdiffdls'] = 0;
      }
    
      pdebug(2,"End of start_element ()");
    }
  
  // end_element() - called for every end tag
  function accu_end_element( $parser, $name )
    {
      global $wpf_pstack,$wpf_fc_daynumber;
      
      pdebug(2,"Start of end_element ()");

      // reduce xml path
      $wpf_pstack = substr($wpf_pstack,0, strrpos($wpf_pstack,"/"));
      
      if ($name=="DAY")
	$wpf_fc_daynumber=0;

      pdebug(2,"End of end_element ()");
    }
  
  
  // daten() - called for everey cdata 
  function accu_daten( $parser, $data )
    {
      global $wpf_weather,$wpf_go_ahead;
 
      pdebug(2,"Start of daten ()");

      if ( strlen($wpf_go_ahead) > 0 and $wpf_go_ahead != "sun" and $wpf_go_ahead != "gmtdiffdls")
	{
	  $wpf_weather[$wpf_go_ahead]=$data;
	  $wpf_go_ahead = '';
	}

      pdebug(2,"End of daten ()");
    }

  //
  // parses the xml for the paths in path_tabelle 
  //
  
  function accu_xml_parser($xmlstring) {
    global $wpf_weather,$wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber;

    pdebug(1,"Start of accu_xml_parser ()");

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
    xml_set_element_handler( $parser,"accu_start_element","accu_end_element");
    // set handler for CDATA
    xml_set_character_data_handler( $parser,"accu_daten");
    
    // try to parse the xml
    if( !xml_parse( $parser, $xmlstring,true ) )
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
  // parse xml and extract locations as an array
  // for later use in the admin form
  //
  function accu_get_locations($xml)
  { 
    pdebug(1,"Start of get_locations ()");

    global $loc,$wpf_i;
    $wpf_i=0;
    $loc=array();
    
    // start_element() - wird vom XML-Parser bei öffnenden Tags aufgerufen
    function s_element( $parser, $name, $attribute )
    {
      global $loc,$wpf_i;
    
      if ($name == "LOCATION") {
		$loc[$wpf_i]=array();
		$loc[$wpf_i]['city'] = $attribute['CITY'];
		$loc[$wpf_i]['state'] = $attribute['STATE'];
		$loc[$wpf_i]['location'] = $attribute['LOCATION'];
		$wpf_i++;
      }
    }
    
    // end_element() - dummy function
    function e_element( $parser, $name ){}
    
    // Instanz des XML-Parsers erzeugen
    $parser = xml_parser_create();
    
    // Parameter des XML-Parsers setzen 
    xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, true );  
    
    // Handler für Elemente ( öffnende / schließende Tags ) setzen 
    xml_set_element_handler( $parser, "s_element", "e_element" ); 
    
    // try to parse the xml
    if( !xml_parse( $parser, $xml, true ) )
      {
	// Fehler -> Ausführung abbrechen
	die(  "XML Fehler: " . 
	      xml_error_string( xml_get_error_code( $parser ) ) . 
	      " in Zeile " .
	      xml_get_current_line_number( $parser )
	      );
      }
    
    // Vom XML-Parser belegten Speicher freigeben
    xml_parser_free( $parser );
    
    pdebug(1,"End of get_locations ()");  
    
  }  
}


//
// returns the widget data as an array one line per item
//
function accu_forecast_data($wpfcid="A", $language_override=null)
{
  pdebug(1,"Start of function accu_forecast_data ()");
  
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

    $weather_arr['servicelink']= 'http://www.accuweather.com/quick-look.aspx?partner=accuweather&amp;loc=' . $location . '&amp;metric=' . $metric;
    // next line is for compatibility
    $weather_arr['acculink']=$weather_arr['servicelink'];
    $weather_arr['location'] = $locname;
    $weather_arr['locname']= $w["city"]." ".$w["state"];
    
    
    $lt = time() - date("Z"); // this is the GMT
    $ct  = $lt + (3600 * ($w['gmtdiff'])); // local time
    
    
    if ( $w['gmtdiffdls'] == 1)
      $ct += 3600; // time with daylightsavings 

    
    $ct = $ct + $wpf_vars['timeoffset'] * 60; // add or subtract time offset
    
    $weather_arr['blogdate']=date_i18n($fc_date_format, $ct);
    $weather_arr['blogtime']=date_i18n($fc_time_format, $ct);
    
    $cts = $w['fc_obsdate_1']." ".$w['time'];
    $ct = strtotime($cts);
    //$ct = $ct + $wpf_vars['timeoffset'] * 60; // add or subtract time offset
    $weather_arr['accudate']=date_i18n($fc_date_format, $ct);
    $weather_arr['accutime']=date_i18n($fc_time_format, $ct);
    
    
    $iconfile=find_icon($w["weathericon"]);
    $weather_arr['icon']="icons/".$iconfile;
    $weather_arr['iconcode']=$w["weathericon"];

    $weather_arr['shorttext']= __($w["weathericon"],"wp-forecast_".$wpf_language);
    
    $weather_arr['temperature']=$w["temperature"]. "&deg;".$w['un_temp'];
    $weather_arr['realfeel']=$w["realfeel"]."&deg;".$w['un_temp'];
	// workaround different pressure values returned by accuweather
    $press = round($w["pressure"],0);
    if (strlen($press)==3 and substr($press,0,1)=="1")
    	$press = $press * 10;
    $weather_arr['pressure'] = $press . " " . $w["un_pres"];
    $weather_arr['humidity']=round($w["humidity"],0);
    $weather_arr['windspeed']=windstr($metric,$w["windspeed"],$windunit);
    $weather_arr['winddir']=translate_winddir($w["winddirection"],"wp-forecast_".$wpf_language);
    $weather_arr['windgusts']=windstr($metric,$w["wgusts"],$windunit);
    $sunarr = explode(" ",$w["sun"]);
    $weather_arr['sunrise']= date_i18n($fc_time_format,strtotime($sunarr[0]));
    $weather_arr['sunset'] = date_i18n($fc_time_format, strtotime($sunarr[1]));
    $weather_arr['copyright']='<a href="http://www.accuweather.com">&copy; '.date("Y").' AccuWeather, Inc.</a>';
    
    
    // calc values for forecast
    for ($i = 1; $i < 10; $i++) {
      // daytime forecast
      $weather_arr['fc_obsdate_'.$i]= date_i18n($fc_date_format, strtotime($w['fc_obsdate_'.$i]));
      $iconfile=find_icon($w["fc_dt_icon_".$i]);
      $weather_arr["fc_dt_icon_".$i]="icons/".$iconfile;
      $weather_arr["fc_dt_iconcode_".$i]=$w["fc_dt_icon_".$i];
      $weather_arr["fc_dt_desc_".$i]= __($w["fc_dt_icon_".$i],"wp-forecast_".$wpf_language);
      $weather_arr["fc_dt_htemp_".$i]= $w["fc_dt_htemp_".$i]."&deg;".$w['un_temp'];
      $wstr=windstr($metric,$w["fc_dt_windspeed_".$i],$windunit);
      $weather_arr["fc_dt_windspeed_".$i]= $wstr;
      $weather_arr["fc_dt_winddir_".$i]=translate_winddir($w["fc_dt_winddir_".$i],"wp-forecast_".$wpf_language);
      $weather_arr["fc_dt_wgusts_".$i] = windstr($metric,$w["fc_dt_wgusts_".$i],$windunit);
      
      // nighttime forecast
      $iconfile=find_icon($w["fc_nt_icon_".$i]);
      $weather_arr["fc_nt_icon_".$i]="icons/".$iconfile;
      $weather_arr["fc_nt_iconcode_".$i]=$w["fc_nt_icon_".$i];
      $weather_arr["fc_nt_desc_".$i]= __($w["fc_nt_icon_".$i],"wp-forecast_".$wpf_language);
      $weather_arr["fc_nt_ltemp_".$i]= $w["fc_nt_ltemp_".$i]."&deg;".$w['un_temp'];
      $wstr=windstr($metric,$w["fc_nt_windspeed_".$i],$windunit);
      $weather_arr["fc_nt_windspeed_".$i]= $wstr;
      $weather_arr["fc_nt_winddir_".$i]=translate_winddir($w["fc_nt_winddir_".$i],"wp-forecast_".$wpf_language);
      $weather_arr["fc_nt_wgusts_".$i] = windstr($metric,$w["fc_nt_wgusts_".$i],$windunit);
      
      // additional info
      $weather_arr['lat']=$w['lat'];
      $weather_arr['lon']=$w['lon'];
    }
  }
  // fill failure anyway
  $weather_arr['failure']=( isset($w['failure']) ? $w['failure'] : '');

  pdebug(1,"End of function accu_forecast_data ()");
  
  return $weather_arr;
}
?>