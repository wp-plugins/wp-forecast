<?php

if (!function_exists('wpf_xml_parser')) {
  global $wpf_weather,$wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber,$wpf_debug;


  // start_element() - called for every start tag
  function start_element( $parser, $name, $attribute )
    {
      global $wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber;
      global $wpf_weather,$wpf_debug;

      if ($wpf_debug > 1)
	pdebug("Start of start_element ()");

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
    
      if ($wpf_debug > 1)
	pdebug("End of start_element ()");
    }
  
  // end_element() - called for every end tag
  function end_element( $parser, $name )
    {
      global $wpf_pstack,$wpf_fc_daynumber,$wpf_debug;
      
      if ($wpf_debug > 1)
	pdebug("Start of end_element ()");

      // reduce xml path
      $wpf_pstack = substr($wpf_pstack,0, strrpos($wpf_pstack,"/"));
      
      if ($name=="DAY")
	$wpf_fc_daynumber=0;

      if ($wpf_debug > 1)
	pdebug("End of end_element ()");
    }
  
  
  // daten() - called for everey cdata 
  function daten( $parser, $data )
    {
      global $wpf_weather,$wpf_go_ahead,$wpf_debug;
 
      if ($wpf_debug > 1)
	pdebug("Start of daten ()");

      if ( strlen($wpf_go_ahead) > 0 and $wpf_go_ahead != "sun" and $wpf_go_ahead != "gmtdiffdls")
	{
	  $wpf_weather[$wpf_go_ahead]=$data;
	  $wpf_go_ahead = '';
	}

      if ($wpf_debug > 1)
	pdebug("End of daten ()");
    }

  //
  // parses the xml for the paths in path_tabelle 
  //
  
  function wpf_xml_parser($xmlstring) {
    global $wpf_weather,$wpf_pstack,$wpf_go_ahead,$wpf_fc_daynumber,$wpf_debug;

    if ($wpf_debug > 0)
	pdebug("Start of wpf_xml_parser ()");

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
    xml_set_element_handler( $parser,"start_element","end_element");
    // set handler for CDATA
    xml_set_character_data_handler( $parser,"daten");
    
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

    if ($wpf_debug > 0)
      pdebug("End of wpf_xml_parser ()");

    // and return result, empty array if error
    return $wpf_weather;
  }
  
}
?>