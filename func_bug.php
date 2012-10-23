<?php
/* This file is part of the wp-forecast plugin for wordpress */

/*  Copyright 2006-2011  Hans Matzen  (email : webmaster at tuxlog dot de)

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

if (!function_exists('bug_xml_parser')) 
{
  global $wpf_weather,$wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber;

  //
  // build the url from the parameters and fetch the weather-data
  // return it as one long string
  //
  function bug_get_weather($uri,$apikey,$loc,$metric)
  {
    pdebug(1,"Start of bug_get_weather ()");

    $uri=str_replace('#apicode#',$apikey,$uri);
    $url=$uri . urlencode($loc) . "&UnitType=" . $metric . "&OutputType=1"; 
    $xml = fetchURL($url);

    pdebug(1,"End of bug_get_weather ()");
    
    return $xml;
  }

  // start_element() - called for every start tag
  function bug_start_element( $parser, $name, $attribute )
  {
    global $wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber,$wpf_weather;
    
    pdebug(2,"Start of start_element ()");
    
    $wpf_path_table = 
      array(
	    "/AWS:WEATHER/AWS:OB/AWS:STATION"     => "city",
	    "/AWS:WEATHER/AWS:OB/AWS:COUNTRY"     => "state",
	    "/AWS:WEATHER/AWS:INPUTLOCATIONURL"   => "servicelink",
	    "/AWS:WEATHER/AWS:OB/AWS:OB-DATE"     => "time",
	    "/AWS:WEATHER/AWS:OB/AWS:LATITUDE"    => "lat",
	    "/AWS:WEATHER/AWS:OB/AWS:LONGITUDE"   => "lon",
	    "/AWS:WEATHER/AWS:OB/AWS:PRESSURE"    => "pressure",
	    "/AWS:WEATHER/AWS:OB/AWS:TEMP"        => "temperature",
	    "/AWS:WEATHER/AWS:OB/AWS:FEELS-LIKE"  => "realfeel",
	    "/AWS:WEATHER/AWS:OB/AWS:HUMIDITY"    => "humidity",
	    "/AWS:WEATHER/AWS:OB/AWS:CURRENT-CONDITION"  => "weathertext",
	    "/AWS:WEATHER/AWS:OB/AWS:WIND-SPEED"      => "windspeed",
	    "/AWS:WEATHER/AWS:OB/AWS:WIND-DIRECTION"  => "winddirection",
	    "/AWS:WEATHER/AWS:OB/AWS:GUST-SPEED"      => "wgusts", 
	    "/AWS:WEATHER/AWS:OB/AWS:GUST-DIRECTION"  => "wgustsdirection",
	    "/AWS:WEATHER/AWS:FORECASTS/DAY/OBSDATE"  => "fc_obsdate",
	    "/AWS:WEATHER/AWS:FORECASTS/AWS:FORECAST/AWS:SHORT-PREDICTION"  => "fc_dt_short",
	    "/AWS:WEATHER/AWS:FORECASTS/AWS:FORECAST/AWS:IMAGE"  => "fc_dt_icon",
	    "/AWS:WEATHER/AWS:FORECASTS/AWS:FORECAST/AWS:HIGH"  => "fc_dt_htemp",  
	    "/AWS:WEATHER/AWS:FORECASTS/AWS:FORECAST/AWS:LOW"  => "fc_dt_ltemp",   
	    "/AWS:ERROR" => "failure"
	    );
    // path adjustment
    $wpf_pstack .= "/$name";
    
    if (isset( $wpf_path_table[ $wpf_pstack])) {
      $wpf_go_ahead = $wpf_path_table[$wpf_pstack];
      
      if ($wpf_fc_daynumber != "0" and $wpf_fc_daynumber != "")
	$wpf_go_ahead = $wpf_go_ahead . "_" . $wpf_fc_daynumber;
    }
    // for forecast days
    if ($wpf_pstack=="/AWS:WEATHER/AWS:FORECASTS/AWS:FORECAST")
      $wpf_fc_daynumber++;
    
    
    switch ( $wpf_pstack ) {
      // unit of temperature
    case "/AWS:WEATHER/AWS:OB/AWS:TEMP": 
      $wpf_weather["un_temp"]=$attribute["UNITS"];
      break;
      // unit of speed
    case "/AWS:WEATHER/AWS:OB/AWS:GUST-SPEED": 
      $wpf_weather["un_speed"]=$attribute["UNITS"];
      break; 
      // unit of pressure
    case "/AWS:WEATHER/AWS:OB/AWS:PRESSURE": 
      $wpf_weather["un_pres"]=$attribute["UNITS"];
      break;
      // get observation date
    case "/AWS:WEATHER/AWS:OB/AWS:OB-DATE/AWS:YEAR":
      $wpf_weather["time_year"]=$attribute["NUMBER"];
      break;
    case "/AWS:WEATHER/AWS:OB/AWS:OB-DATE/AWS:MONTH":
      $wpf_weather["time_month"]=$attribute["NUMBER"];
      break; 
    case "/AWS:WEATHER/AWS:OB/AWS:OB-DATE/AWS:DAY":
      $wpf_weather["time_day"]=$attribute["NUMBER"];
      break; 
    case "/AWS:WEATHER/AWS:OB/AWS:OB-DATE/AWS:HOUR":
      $wpf_weather["time_hour"]=$attribute["HOUR-24"];
      break; 
    case "/AWS:WEATHER/AWS:OB/AWS:OB-DATE/AWS:MINUTE":
      $wpf_weather["time_minute"]=$attribute["NUMBER"];
      break;
      // get sunrise
    case "/AWS:WEATHER/AWS:OB/AWS:SUNRISE/AWS:YEAR":
      $wpf_weather["sunrise_year"]=$attribute["NUMBER"];
      break;
    case "/AWS:WEATHER/AWS:OB/AWS:SUNRISE/AWS:MONTH":
      $wpf_weather["sunrise_month"]=$attribute["NUMBER"];
      break; 
    case "/AWS:WEATHER/AWS:OB/AWS:SUNRISE/AWS:DAY":
      $wpf_weather["sunrise_day"]=$attribute["NUMBER"];
      break; 
    case "/AWS:WEATHER/AWS:OB/AWS:SUNRISE/AWS:HOUR":
      $wpf_weather["sunrise_hour"]=$attribute["HOUR-24"];
      break; 
    case "/AWS:WEATHER/AWS:OB/AWS:SUNRISE/AWS:MINUTE":
      $wpf_weather["sunrise_minute"]=$attribute["NUMBER"];
      break;
      // get sunset
    case "/AWS:WEATHER/AWS:OB/AWS:SUNSET/AWS:YEAR":
      $wpf_weather["sunset_year"]=$attribute["NUMBER"];
      break;
    case "/AWS:WEATHER/AWS:OB/AWS:SUNSET/AWS:MONTH":
      $wpf_weather["sunset_month"]=$attribute["NUMBER"];
      break; 
    case "/AWS:WEATHER/AWS:OB/AWS:SUNSET/AWS:DAY":
      $wpf_weather["sunset_day"]=$attribute["NUMBER"];
      break; 
    case "/AWS:WEATHER/AWS:OB/AWS:SUNSET/AWS:HOUR":
      $wpf_weather["sunset_hour"]=$attribute["HOUR-24"];
      break; 
    case "/AWS:WEATHER/AWS:OB/AWS:SUNSET/AWS:MINUTE":
      $wpf_weather["sunset_minute"]=$attribute["NUMBER"];
      break;
    case "/AWS:WEATHER/AWS:OB/AWS:CURRENT-CONDITION":
      $wpf_weather["weathericon"] = $attribute['ICON'];
    } 
    pdebug(2,"End of bug_start_element ()");
}
  
  // end_element() - called for every end tag
  function bug_end_element( $parser, $name )
  {
    global $wpf_pstack,$wpf_fc_daynumber;
    pdebug(2,"Start of bug_end_element ()");
    // reduce xml path stack
    $wpf_pstack = substr($wpf_pstack,0, strrpos($wpf_pstack,"/"));
    pdebug(2,"End of bug_end_element ()");
  }
  
  
  // daten() - called for everey cdata 
  function bug_daten( $parser, $data )
  {
    global $wpf_weather,$wpf_go_ahead;
    pdebug(2,"Start of bug_daten ()");
    if ( strlen($wpf_go_ahead) > 0 )
      {
	$wpf_weather[$wpf_go_ahead]=$data;
	$wpf_go_ahead = '';
      }
    pdebug(2,"End of bug_daten ()");
  }

  //
  // parses the xml for the paths in path_tabelle 
  //
  
  function bug_xml_parser($xmlstring) {
    global $wpf_weather,$wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber;
    pdebug(1,"Start of bug_xml_parser ()");

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
    xml_set_element_handler( $parser,"bug_start_element","bug_end_element");
    // set handler for CDATA
    xml_set_character_data_handler( $parser,"bug_daten");
    
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
    if ($xmlerror!="")
    {
	$wpf_weather=array();
	$wpf_weather['failure'] = $xmlerror . " (" . 
	    (trim($xmlstring) !="" ? $xmlstring : __('Location does not exist') ) . ")";
    } else {
	// neutralize data from weatherbug
	// obs time/date
	$wpf_weather['time'] = $wpf_weather['time_month']."/".$wpf_weather['time_day']."/".$wpf_weather['time_year']." ".$wpf_weather['time_hour'].":".$wpf_weather['time_minute'];
	unset($wpf_weather['time_month']);
	unset($wpf_weather['time_day']);
	unset($wpf_weather['time_year']);
	unset($wpf_weather['time_hour']);
	unset($wpf_weather['time_minute']);
	// sunset time/date
	$wpf_weather['sunset'] = $wpf_weather['sunset_month']."/".$wpf_weather['sunset_day']."/".$wpf_weather['sunset_year']." ".$wpf_weather['sunset_hour'].":".$wpf_weather['sunset_minute'];
	unset($wpf_weather['sunset_month']);
	unset($wpf_weather['sunset_day']);
	unset($wpf_weather['sunset_year']);
	unset($wpf_weather['sunset_hour']);
	unset($wpf_weather['sunset_minute']);
	// sunsrise time/date
	$wpf_weather['sunrise'] = $wpf_weather['sunrise_month']."/".$wpf_weather['sunrise_day']."/".$wpf_weather['sunrise_year']." ".$wpf_weather['sunrise_hour'].":".$wpf_weather['sunrise_minute'];
	unset($wpf_weather['sunrise_month']);
	unset($wpf_weather['sunrise_day']);
	unset($wpf_weather['sunrise_year']);
	unset($wpf_weather['sunrise_hour']);
	unset($wpf_weather['sunrise_minute']);
	
    }
    pdebug(1,"End of bug_xml_parser ()");
    // and return result, empty array if error
    return $wpf_weather;
  }


  //
  // parse xml and extract locations as an array
  // for later us in the admin form
  //
  function bug_get_locations($xml)
  { 
    pdebug(1,"Start of bug_get_locations ()");
    
    // start_element() - wird vom XML-Parser bei öffnenden Tags aufgerufen
    function bug_s_element( $parser, $name, $attribute )
    {
      global $loc,$wpf_i;
      if ($name == "AWS:LOCATION") {
	$loc[$wpf_i]=array();
	$loc[$wpf_i]['city'] = $attribute['CITYNAME'];
	$loc[$wpf_i]['state'] = $attribute['COUNTRYNAME'];
	if ($attribute['CITYTYPE']==0)
	  	$loc[$wpf_i]['location'] = $attribute['ZIPCODE'];
	else
	  $loc[$wpf_i]['location'] = $attribute['CITYCODE'];
	$wpf_i++;
      }
    }
    
    // end_element() - dummy function
    function bug_e_element( $parser, $name ){}
    
    // Instanz des XML-Parsers erzeugen
    $parser = xml_parser_create();
    
    // Parameter des XML-Parsers setzen 
    xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, true ); 
    
    // Handler für Elemente ( öffnende / schließende Tags ) setzen 
    xml_set_element_handler( $parser, "bug_s_element", "bug_e_element" ); 
    
    // try to parse the xml
    if( !xml_parse( $parser, $xml,true ) )
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
    
    pdebug(1,"End of bug_get_locations ()");  
    
    // return locations
    return $loc;
  }  
}

//
// returns the widget data as an array one line per item
//
function bug_forecast_data($wpfcid="A", $language_override=null)
{
  pdebug(1,"Start of function bug_forecast_data ()");
  
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
    $weather_arr['servicelink']= $w['servicelink'];
    $weather_arr['location'] = $locname;
    $weather_arr['locname']= $w["city"]." ".$w["state"];
    
    

    $ct = time(); // this is the GMT 
    $ct = $ct + $wpf_vars['timeoffset'] * 60; // add or subtract time offset
    $weather_arr['blogdate']=date_i18n($fc_date_format, $ct);
    $weather_arr['blogtime']=date_i18n($fc_time_format, $ct);
    
    $cts = $w['time'];
    $gmtoffset=0; // wpf_get_option("gmt_offset");
    $ct = strtotime($cts) + ($gmtoffset * 3600); 
  
    $weather_arr['bugdate']=date_i18n($fc_date_format, $ct);
    $weather_arr['bugtime']=date_i18n($fc_time_format, $ct);
    
    
    $weather_arr['icon']=$w["weathericon"];
    $weather_arr['iconcode']="";
    if ( trim($w["weathertext"]) !="")
	$weather_arr['shorttext']= __($w["weathertext"],"wp-forecast_".$wpf_language);
    else
	$weather_arr['shorttext']= __("Unknown","wp-forecast");
    
    $weather_arr['temperature']=$w["temperature"]. $w['un_temp'];
    $weather_arr['realfeel']=$w["realfeel"].$w['un_temp'];
    $weather_arr['pressure']=round($w["pressure"],0)." ".$w["un_pres"];
    $weather_arr['humidity']=round($w["humidity"],0);
    $weather_arr['windspeed']=windstr($metric,$w["windspeed"],$windunit);
    $weather_arr['winddir']=translate_winddir($w["winddirection"],"wp-forecast_".$wpf_language);
    $weather_arr['windgusts']=windstr($metric,$w["wgusts"],$windunit);
    list($dummy, $weather_arr['sunrise']) = split(" ",$w['sunrise'],2);
    list($dummy, $weather_arr['sunset'] ) = split(" ",$w['sunset'] ,2);
    $weather_arr['copyright']='<a href="http://www.weatherbug.com">&copy; '.date("Y").' WeatherBug</a>';
    
    // additional info
    $weather_arr['lat']=$w['lat'];
    $weather_arr['lon']=$w['lon'];

    // calc values for forecast
    for ($i = 1; $i < 7; $i++) {
      // forecast
      $bt = strtotime($w['time']);
      $weather_arr['fc_obsdate_'.$i]= date_i18n($fc_date_format, $bt + ( $i * 3600 *24));
      $weather_arr["fc_dt_icon_".$i]=$w["fc_dt_icon_".$i];
      $weather_arr["fc_dt_iconcode_".$i]="";
      if (trim($w["fc_dt_short_".$i]) !="")
      	  $weather_arr["fc_dt_desc_".$i]= __($w["fc_dt_short_".$i],"wp-forecast_".$wpf_language);
      else
	  $weather_arr["fc_dt_desc_".$i]= __("Unknown","wp-forecast");

      $weather_arr["fc_dt_htemp_".$i]= $w["fc_dt_htemp_".$i].$w['un_temp']; 
      $weather_arr["fc_dt_ltemp_".$i]= $w["fc_dt_ltemp_".$i].$w['un_temp'];
      
    }
  }
  // fill failure anyway
  $weather_arr['failure']=( isset($w['failure']) ? $w['failure'] : '');

  pdebug(1,"End of function bug_forecast_data ()");
  
  return $weather_arr;
}
?>