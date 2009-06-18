<?php

// start_element() - called for every start tag
function bug_start_element( $parser, $name, $attribute )
{
  global $wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber,$wpf_weather;

  $wpf_path_table = 
    array(
	  "/AWS:WEATHER/AWS:OB/AWS:STATION"     => "city",
	  "/AWS:WEATHER/AWS:OB/AWS:COUNTRY"     => "state",
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
}
  
// end_element() - called for every end tag
function bug_end_element( $parser, $name )
{
  global $wpf_pstack,$wpf_fc_daynumber;
  
  // reduce xml path stack
  $wpf_pstack = substr($wpf_pstack,0, strrpos($wpf_pstack,"/"));
  
}


// daten() - called for everey cdata 
function bug_daten( $parser, $data )
{
  global $wpf_weather,$wpf_go_ahead;
  
  if ( strlen($wpf_go_ahead) > 0 )
    {
      $wpf_weather[$wpf_go_ahead]=$data;
      $wpf_go_ahead = '';
    }
}

//
// parses the xml for the paths in path_tabelle 
//

function bug_xml_parser($xmlstring) {
  global $wpf_weather,$wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber;
  
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
    $wpf_weather=array();
  else {
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
  // and return result, empty array if error
  return $wpf_weather;
}


// liest den Inhalt einer Datei in einen String
$filename = "examples/weather_bug_example.xml";
$handle = fopen ($filename, "r");
$contents1 = fread ($handle, filesize ($filename));
$filename = "examples/weather_bug_fc_example.xml";
$handle = fopen ($filename, "r");
$contents2 = fread ($handle, filesize ($filename));
fclose ($handle);

$erg1=bug_xml_parser($contents1);
$erg2=bug_xml_parser($contents2);
$erg = array_merge($erg2,$erg1);
//print_r($erg);



?>