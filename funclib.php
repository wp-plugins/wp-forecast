<?php
/* This file is part of the wp-forecast plugin for wordpress */

/*  Copyright 2006,2007,2008  Hans Matzen (email : webmaster at tuxlog.de)

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

if (!function_exists('fetchURL')) {

//
// this function fetches an url an returns it as one whole string
//
function fetchURL($url) 
{
  global $wpf_debug;
    
  if ($wpf_debug > 0)
    pdebug("Start of fetchURL ()");

  // get timeout parameter
  $timeout = get_option("wp-forecast-timeout");
  if ( $timeout =="")
    $timeout = 30;

  $url_parsed = parse_url($url);
    $host = $url_parsed["host"];
    if (!isset($url_parsed["port"])) {
	$port = 80;
    }
    else {
	$port = $url_parsed["port"];
    }
    $path = $url_parsed["path"];
    if ($url_parsed["query"] != "") $path .= "?" . $url_parsed["query"];
    $out = "GET $path HTTP/1.0\r\nHost: $host\r\n\r\n";
    // open connection
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
   
    $in = "";
    if ($fp) {
      // set timeout for reading 
      stream_set_timeout($fp, $timeout);
      // send request
      fwrite($fp, $out);
      $body = false;
      // read answer
      while (!feof($fp)) {
	$s = fgets($fp, 1024);
	if ($body) $in .= $s;
        if ($s == "\r\n") $body = true;
      }
      // close connection
      fclose($fp);
    } else {
      // error handling
      echo '<!-- Connection-Error, Error-No.: ' . $errno . ' >> ' . $errstr . "-->\n";
    }  

    if ($wpf_debug > 0)
      pdebug("End of fetchURL ()");
    
    return $in;
}

//
// converts an array to an cookie like string
// e.g. a=5&b=6&c=7
function arr2str($a)
{
  global $wpf_debug;
    
  if ($wpf_debug > 0)
    pdebug("Start of arr2str ()");

  $s="";
  foreach ($a as $name => $value){
    $s=$s . $name . "=" . $value . "&";
  }

  if ($wpf_debug > 0)
    pdebug("End of arr2str ()");

  return $s;
}

//
// converts a cookie like string (see above) to an array
//
function str2arr($s)
{
  global $wpf_debug;
  
  if ($wpf_debug > 0)
    pdebug("Start of str2arr ()");

  $a=array();
  parse_str($s,$a);

  if ($wpf_debug > 0)
    pdebug("End of str2arr ()");

  return $a;
}

//
// converts the given wind parameters into a suitable windstring
//
function windstr($metric,$wspeed,$windunit) 
{
  global $wpf_debug;
  
  if ($wpf_debug > 0)
    pdebug("Start of windstr ()");

  // if its mph convert it to m/s
  if ($metric != 1)
    $wspeed = round($wspeed * 0.44704,0);
	
  // convert it to selected unit
  switch ($windunit) {
  case "ms":
    $wunit="m/s";
    break;
  case "kmh":
    $wspeed=round($wspeed*3.6,0);
    $wunit="km/h";
    break;
  case "mph":
    $wspeed=round($wspeed*2.23694,0);
    $wunit="mph";
    break;
  case "kts":
    $wspeed=round($wspeed*1.9438,0);
    $wunit="kts";
    break;
  }
  
  if ($wpf_debug > 0)
    pdebug("End of windstr ()");
  
  return $wspeed." ".$wunit;
}


function get_wpf_opts($wpfcid) 
{
  global $wpf_debug;
  
  if ($wpf_debug > 0)
    pdebug("Start of get_wpf_opts ()");

  $av=array();
  
  // get options from database
  $av['location']=get_option("wp-forecast-location".$wpfcid);
  $av['locname']=get_option("wp-forecast-locname".$wpfcid);
  $av['refresh']=get_option("wp-forecast-refresh".$wpfcid); 
  $av['metric']=get_option("wp-forecast-metric".$wpfcid); 
  $av['wpf_language']=get_option("wp-forecast-language".$wpfcid);
  $av['daytime']=get_option("wp-forecast-daytime".$wpfcid);
  $av['nighttime']=get_option("wp-forecast-nighttime".$wpfcid);
  $av['fc_date_format']=get_option("date_format");
  $av['fc_time_format']=get_option("time_format");
  $av['dispconfig']=get_option("wp-forecast-dispconfig".$wpfcid);
  $av['windunit']=get_option("wp-forecast-windunit".$wpfcid);
  $av['currtime']=get_option("wp-forecast-currtime".$wpfcid);
  $av['title'] = get_option("wp-forecast-title".$wpfcid);
  $av['xmlerror']="";

  // set accuweather uri
  $av['BASE_URI']="http://forecastfox.accuweather.com/adcbin/forecastfox/weather_data.asp?";

  if ($wpf_debug > 0)
    pdebug("End of get_wpf_opts ()");  

  return $av;
}


//
// build the url from the parameters and fetch the weather-data
// return it as one long string
//
function get_weather($uri,$loc,$metric)
{
  global $wpf_debug;
  
  if ($wpf_debug > 0)
    pdebug("Start of get_weather ()");

  $url=$uri . "location=" . urlencode($loc) . "&metric=" . 
    $metric;// . "&partner=forecastfox";
  
  $xml = fetchURL($url);

  if ($wpf_debug > 0)
    pdebug("End of get_weather ()");
  
  return $xml;
}

//
// just return the css link
// this function is called via the wp_head hook
//
function wp_forecast_css($wpfcid="A") {

  global $wpf_debug;
  
  if ($wpf_debug > 0)
    pdebug("Start of function wp_forecast_css ()");

  $plugin_path = get_settings('siteurl') . '/wp-content/plugins/wp-forecast';
  echo "<link rel=\"stylesheet\" href=\"". $plugin_path. "/wp-forecast.css\" type=\"text/css\" media=\"screen\" />\n";

  
  if ($wpf_debug > 0)
    pdebug("End of function wp_forecast_css ()");
}


//
// just return the css link when not using wordpress
// this function is called when showing widget directly via wp-forecast-show.php
//
function wp_forecast_css_nowp($wpfcid="A") {
  
  global $wpf_debug;
  
  if ($wpf_debug > 0)
    pdebug("Start of function wp_forecast_css_nowp ()");
  
  $plugin_path = get_settings('siteurl') . '/wp-content/plugins/wp-forecast';
  echo "<link rel=\"stylesheet\" href=\"". $plugin_path. "/wp-forecast-nowp.css\" type=\"text/css\" media=\"screen\" />\n";
  
  
  if ($wpf_debug > 0)
    pdebug("End of function wp_forecast_css ()");
}


//
// little debug output routine
//
function pdebug($dstr)
{
  echo $dstr."<br />\n";
}

//
// returns the number's widget id used with wp-forecast
// maximum is 999999 :-)
//
function get_widget_id($number)
{
  // if negative take the first id
  if ($number < 0 )
    return "A";

  // the first widgets use chars above we go with 0 padded numbers
  if ( $number <= 25 ) 
    return substr("ABCDEFGHIJKLMNOPQRSTUVWXYZ",$number,1);
  else
    return str_pad($number, 6, "0", STR_PAD_LEFT);
}

//
// function tries to determine the icon path for icon number ino
//
function find_icon($ino) 
{
  $path = ABSPATH . "wp-content/plugins/wp-forecast/icons/".$ino;
  $ext=".gif";
  
  if ( file_exists($path.".gif") )
    $ext= ".gif";
  else if ( file_exists($path.".png") )
    $ext= ".png";
  else  if ( file_exists($path.".jpg") )
    $ext= ".jpg";
  else if ( file_exists($path.".GIF") )
    $ext= ".GIF";
  else if ( file_exists($path.".PNG") )
    $ext= ".PNG";
  else  if ( file_exists($path.".JPG") )
    $ext= ".JPG";
  else  if ( file_exists($path.".jpeg") )
    $ext= ".jpeg"; 
  else  if ( file_exists($path.".JPEG") )
    $ext= ".JPEG";
  return $ino . $ext;
}

function translate_winddir($wdir,$tdom)
{
  // translate winddir char by char
  $winddir="";
  for ($i=0;$i<strlen($wdir);$i++)
    $winddir=$winddir . __($wdir{$i},$tdom);
  return $winddir;
}

}
?>
