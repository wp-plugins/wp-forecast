<?php
 /* This file is part of the wp-forecast plugin for wordpress */

/*  Copyright 2009  Hans Matzen  (email : webmaster at tuxlog dot de)

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

// include wordpress stuff
//include wp-config or wp-load.php
$root = dirname(dirname(dirname(dirname(__FILE__))));
if (file_exists($root.'/wp-load.php')) {				// since WP 2.6
	require_once($root.'/wp-load.php');
} elseif (file_exists($root.'/wp-config.php')) {		// Before 2.6
	require_once($root.'/wp-config.php');
} 
require_once("funclib.php");

function checkURL($url) 
{
    $erg = array();

    // switch to wp-forecast transport
    switch_wpf_transport(true);
    
    $wprr_args = array(
	'timeout' => 30,
	'headers' => array('Connection' => 'Close','Accept' => '*/*') 
	); 
    
    // use generic wordpress function to retrieve data
    $s = time();
    $resp = wp_remote_request($url, $wprr_args);
    $e = time();
	
    // switch to wordpress transport
    switch_wpf_transport(false);

    $erg['duration'] = (int) ($e-$s);

    if ( is_wp_error($resp) ) {
	$errcode = $resp->get_error_code();
	$errmesg = $resp->get_error_message($errcode);
	
	$erg['error'] = $errcode . " " . $errmesg;
	$erg['body']  = "";
	$erg['len'] = "-1";
    } else {
	$erg['error'] = "";
	$erg['body']  = $resp['body'];
	$erg['len'] = strlen( $resp['body'] );
    }
    return $erg;
} 

function showCheckResult($erg)
{
    $out = "";
    $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if ($erg['error'] == "") {
	$out .= $space . "Test was successfull.". "<br/>";

	$out .= $space . "Fetched " . $erg['len'] . " Bytes in ";
	$out .= $erg['duration'] . " seconds."."<br/>";
    } else {
	$out .= $space . "Test ends in error.". "<br/>";
	$out .= $space . "Error-Message was " . $erg['error'] ."<br/>";
    }
    return $out;
}


$wpf_vars=get_wpf_opts("A");
$locale = $wpf_vars['wpf_language'];

// get translations
 if(function_exists('load_plugin_textdomain')) {
  	add_filter("plugin_locale","wpf_lplug",10,2);
   	load_plugin_textdomain("wp-forecast_".$locale, false, dirname( plugin_basename( __FILE__ ) ) . "/lang/");
   	remove_filter("plugin_locale","wpf_lplug",10,2);
  }
  

if (!empty($_POST)) {
    $out = "";
    $out .= "Checking for Weatherprovider " . $_POST['wprovider'] . "<br />";

    $transports = get_wp_transports();
    
    if ( $_POST['wprovider'] == "Accuweather")
	$url = $wpf_vars["ACCU_BASE_URI"] . "metric=1&location=EUR|DE|GM007|FRANKFURT%20AM%20MAIN";
    if ( $_POST['wprovider'] == "WeatherBug") {
	if ( !isset($wpf_vars['apikey1'])) {
	    $out .= "You have to set the API-Key to test WeatherBug.";
	    echo $out;
	    die();
	}
	$url = $wpf_vars["BUG_BASE_URI"] . "58738&UnitType=1&OutputType=1"; 
	$url = str_replace('#apicode#',$wpf_vars['apikey1'],$url);
    }
    
    if ( $_POST['wprovider'] == "Weather.com")
	$url="";

    
    // remember selected transport
    $wp_transport = wpf_get_option("wp-forecast-wp-transport"); 

    // checking for standard connection method
    $out .= "Checking default transport"."<br />";
    wpf_update_option("wp-forecast-wp-transport","default");
    $erg = checkURL($url);
    $out .= showCheckResult($erg);

    // checking fsockopen
    $out .= "Checking fsockopen transport"."<br />";
    wpf_update_option("wp-forecast-wp-transport","fsockopen");
    $erg = checkURL($url);
    $out .= showCheckResult($erg);

    // checking exthttp
    $out .= "Checking exthttp transport"."<br />";
    wpf_update_option("wp-forecast-wp-transport","exthttp");
    $erg = checkURL($url);
    $out .= showCheckResult($erg);


    // checking streams
    $out .= "Checking streams transport"."<br />";
    wpf_update_option("wp-forecast-wp-transport","streams");
    $erg = checkURL($url);
    $out .= showCheckResult($erg);


    // checking curl
    $out .= "Checking curl transport"."<br />";
    wpf_update_option("wp-forecast-wp-transport","curl");
    $erg = checkURL($url);
    $out .= showCheckResult($erg);


    // write back selected transport
    wpf_update_option("wp-forecast-wp-transport",$wp_transport);


    echo $out;
    // you must end here to stop the displaying of the html below
    exit (0);
}

//
// import formular aufbauen ===================================================
//
$out = "";
// add function to submit form data by adrian callaghan
$out .= '<script type="text/javascript"  src="'.plugins_url('/wp-forecast-check.js',__FILE__).'" ></script>';
// add log area style
$out .= "<style>#message {margin:20px; padding:20px; background:#cccccc; color:#cc0000;}</style>";
 
$out .= '<div id="checkorm" class="wrap" >';
$out .= '<h2>wp-forecast '.__('Connection-check',"wp-forecast_".$locale).'</h2>';
$out .= '<table class="editform" cellspacing="5" cellpadding="5">';
$out .= '<tr>';
$out .= '<th scope="row"><label for="wprovider">'.__('Select weatherprovider','wp-forecast_'.$locale).':</label></th>'."\n";
$out .= '<td><select name="wprovider" id="wprovider">'."\n";

$out .= "<option value='Accuweather'>Accuweather</option>\n";
$out .= "<option value='WeatherBug'>WeatherBug</option>\n";
//$out .= "<option value='Weather.com'>Weather.com</option>\n";
$out .= "</select></td>\n";

// add submit button to form
$href= site_url("wp-admin") . "/admin.php?page=wp-forecast-admin.php";
$out .= '<tr><td><p class="submit">';
$out .= '<input type="submit" name="startcheck" id="startcheck" value="'.
    __('Start check','wp-forecast_'.$locale).' &raquo;" onclick="submit_this(\''.plugins_url( 'wp-forecast-check.php' , __FILE__ ).'\')" />';
$out .= '<td><p class="submit">';
$out .= '<input type="submit" name="cancel" id="cancel" value="'.
    __('Close','wpml').'" onclick="tb_remove();" /></p></td>';
$out .= '</p></td></tr>'."\n";
$out .= '</table><hr />'."\n";
// div container fuer das verarbeitungs log
$out .= '<div id="message">Check log</div>';
$out .= "</div>\n";

echo $out;
?>
