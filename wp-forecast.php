<?php
/*
Plugin Name: wp-forecast
Plugin URI: http://www.tuxlog.de
Description: wp-forecast is a highly customizable plugin for wordpress, showing weather-data from accuweather.com.
Version: 4.5
Author: Hans Matzen
Author URI: http://www.tuxlog.de
*/

/*  
    Copyright 2006-2013  Hans Matzen 

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
// only use this in case of severe problems accessing the admin dialog
//
// preselected transport method for fetching the weather data
// valid values are
//       curl      - uses libcurl
//       fsockopen - uses fsockopen
//       streams   - uses fopen with streams
//       exthttp   - uses pecl http extension
// this will override every setting from the admin dialog
// you have to assure that the chosen transport is supported by the
// wordpress class WP_Http;
//
static $wp_forecast_pre_transport="";

//
// maximal number of widgets to use
//
$wpf_maxwidgets=20;

//
// set to 0 for no debugging information
// set to 1 for call stack
// set to 2 for call stack including xml parser
//
static $wpf_debug=0;


/* ---------- no parameters to change after this point -------------------- */
// define path to wp-forecast plugin
define( 'WPF_PATH', plugin_dir_path(__FILE__) );
// accuweather data functions
require_once("func_accu.php");
// weatherbug data functions
require_once("func_bug.php");
// google data functions
require_once("func_google.php");

// generic functions
require_once("funclib.php");
// include setup functions
require_once("wpf_setup.php");
// include admin options page
require_once("wp-forecast-admin.php");
// display functions
require_once("wp-forecast-show.php");
// shortcodes
require_once("shortcodes.php");
// support for wordpress autoupdate
require_once("wpf_autoupdate.php");
// super admin dialog
require_once("wpf_sa_admin.php");


global $blog_id;

//
// set cache with weather data for current parameters
// a wrapper function called via the init hook
//

function wp_forecast_init() 
{
    pdebug(1,"Start of function wp_forecast_init ()");

    // first of all check if we have to set a hard given
    // transport method
    if (isset($wp_forecast_pre_transport) && 
	wpf_get_option("wp-forecast-pre-transport") != $wp_forecast_pre_transport )
    {
	pdebug(1,"Setting hard coded transport method to $wp_forecast_pre_transport");
	wpf_update_option("wp-forecast-pre-transport",$wp_forecast_pre_transport);
    }

    $count=(int) wpf_get_option('wp-forecast-count');
    
    $weather=array();
    
    for ($i=0;$i<$count;$i++) 
    {
	$wpfcid=get_widget_id($i);
	
	$wpf_vars=get_wpf_opts($wpfcid);

	if ($wpf_vars['expire'] < time()) 
	{
	    switch ($wpf_vars['service']) 
	    {
	    case "accu":
		$w = accu_get_weather($wpf_vars['ACCU_BASE_URI'],
				      $wpf_vars['location'],
				      $wpf_vars['metric']);
		// next line is beta for non utf8 charactes in weatherdata
		$weather=accu_xml_parser(utf8_encode($w));
		//$weather=accu_xml_parser($w);
		break;
	 
	    case "bug":
		$w1 = bug_get_weather($wpf_vars['BUG_BASE_URI'],$wpf_vars['apikey1'],
				      $wpf_vars['location'],$wpf_vars['metric']);
		$weather1=bug_xml_parser($w1);
		$w2 = bug_get_weather($wpf_vars['BUG_FORC_URI'],$wpf_vars['apikey1'],
				      $wpf_vars['location'],$wpf_vars['metric']);
		$weather2=bug_xml_parser($w2);
		$weather = array_merge($weather2,$weather1);
		break;

	    case "com":
		// to be done
		break;
		
	    case "google":
		$w = google_get_weather($wpf_vars['GOOGLE_BASE_URI'],
					$wpf_vars['location'],
					substr($wpf_vars['wpf_language'],0,2));
		$weather=google_xml_parser(utf8_encode($w));
		break;
	    }

	    pdebug(1,"Fetched xml was:\n".$w);

	    // store weather to database and set expire time
	    // if the current data wasnt available use old data
	    if ( count($weather)>0) 
	    {
		wpf_update_option("wp-forecast-cache".$wpfcid, serialize($weather));
		if ( empty($weather['failure']) or $weather['failure'] == "" )
		    wpf_update_option("wp-forecast-expire".$wpfcid, time()+$wpf_vars['refresh']);
		else
		    wpf_update_option("wp-forecast-expire".$wpfcid, 0); 
	    }
	}
    }
    
    // javascript hinzufügen fuer ajax widget
   if (! is_admin())
   	 wp_enqueue_script('wpf_update', plugins_url('wpf_update.js', __FILE__),  array('jquery'),"9999");

    
    pdebug(1,"End of function wp_forecast_init ()");
}


//
// this function is called from your template
// to insert your weather data at the place you want it to be
// support to select language on a per call basis from Robert Lang
//
function wp_forecast_widget($args=array(),$wpfcid="A", $language_override=null)
{ 

  pdebug(1,"Start of function wp_forecast_widget (".$wpfcid.")");
  
  if ($wpfcid == "?")
      $wpf_vars=get_wpf_opts("A");
  else
      $wpf_vars=get_wpf_opts($wpfcid);

  if (!empty($language_override)) {
    $wpf_vars['wpf_language']=$language_override;
  }

  if ($wpfcid == "?")
      $weather=maybe_unserialize(wpf_get_option("wp-forecast-cacheA"));
  else
      $weather=maybe_unserialize(wpf_get_option("wp-forecast-cache".$wpfcid));

  show($wpfcid,$args,$wpf_vars);

  pdebug(1,"End of function wp_forecast_widget ()");
}

//
// this is the wrapper function for displaying from sidebar.php
// and not as a widget. since the parameters are different we need this
//
function wp_forecast($wpfcid="A", $language_override=null)
{ 
  pdebug(1,"Start of function wp_forecast ()");
  
  wp_forecast_widget( array(), $wpfcid, $language_override);

  pdebug(1,"End of function wp_forecast ()");
}

//
// a function to show a range of widgets at once
//
function wp_forecast_range($from=0, $to=0, $numpercol=1, $language_override=null)
{
  global $wpf_maxwidgets;
  $wcount=1;

  // check min and max limit
  if ($from < 0)
    $from = 0;
  
  if ($to > $wpf_maxwidgets)
    $to = $wpf_maxwidgets;
  
  // output table header
  echo "<table><tr>";

  // out put widgets in a table
  for ($i=$from;$i<=$to;$i++) {

    if ( $wcount % $numpercol == 1)
      echo "<tr>";

    echo "<td>";
    wp_forecast( get_widget_id($i), $language_override);
    echo "</td>";

    if ( ($wcount % $numpercol == 0) and ($i< $to))
      echo "</tr>";

    $wcount += 1;
  }
  
  // output table footer
  echo "</tr></table>";
}

//
// a function to show a set of widgets at once
//
function wp_forecast_set($wset, $numpercol=1, $language_override=null)
{
  global $wpf_maxwidgets;
  $wcount=1;
  $wset_max= count($wset)-1;

  // output table header
  echo "<table><tr>";

  // out put widgets in a table
  for ($i=0;$i<=$wset_max;$i++) {

    if ( $wcount % $numpercol == 1)
      echo "<tr>";

    echo "<td>";
    wp_forecast( $wset[$i], $language_override);
    echo "</td>";

    if ( ($wcount % $numpercol == 0) and ($i< $wset_max))
      echo "</tr>";

    $wcount += 1;
  }
  
  // output table footer
  echo "</tr></table>";
}

//
// returns the widget data as an array 
//
function wp_forecast_data($wpfcid="A", $language_override=null)
{
  pdebug(1,"Start of function wp_forecast_data ()");
  
  $wpf_vars=get_wpf_opts($wpfcid);

  if (!empty($language_override)) {
    $wpf_vars['wpf_language']=$language_override;
  } 

  extract($wpf_vars);
  $w=maybe_unserialize(wpf_get_option("wp-forecast-cache".$wpfcid));

  $weather_arr=array();

  // read service dependent weather data
  switch ($wpf_vars['service']) {
  case "accu":
    $weather_arr= accu_forecast_data($wpfcid,$language_override);
    break;
  case "bug":
    $weather_arr= bug_forecast_data($wpfcid,$language_override);
    break;
  case "com":
    // to be done
    break; 
  case "google":
      $weather_arr= google_forecast_data($wpfcid,$language_override);
      break;
  }

  return $weather_arr;
  
  pdebug(1,"End of function wp_forecast_data ()");
  
}


//
// set the choosen number of widgets, set at the widget page
//
function wpf_widget_setup() {
  global $wpf_maxwidgets;

  pdebug(1,"Start of function wpf_widget_setup ()");
  
  $count = $newcount = wpf_get_option('wp-forecast-count');
  if ( isset($_POST['wpf-count-submit']) ) {
    $number = (int) $_POST['wp-forecast-count'];
    if ( $number > $wpf_maxwidgets ) $number = $wpf_maxwidgets;
    if ( $number < 1 ) $number = 1;
    $newcount = $number;
  }
  if ( $count != $newcount ) {
    $count = $newcount;
    wpf_update_option('wp-forecast-count', $count);
    // add missing option to database
    wp_forecast_activate();
    // init the new number of widgets
    widget_wp_forecast_init($count);
  }

  pdebug(1,"End of function wpf_widget_setup ()");
}

//
// form snippet to set the number of wanted widgets from
// the widget page
//
function wpf_widget_page() {
  global $wpf_maxwidgets;
  
  pdebug(1,"Start of function wpf_widget_page ()");
  
  $count = $newcount = wpf_get_option('wp-forecast-count');
  
  // get locale 
  $locale = get_locale();
  if ( empty($locale) )
    $locale = 'en_US';
  // load translation 
  if(function_exists('load_plugin_textdomain')) {
  	add_filter("plugin_locale","wpf_lplug",10,2);
   	load_plugin_textdomain("wp-forecast_".$locale, false, dirname( plugin_basename( __FILE__ ) ) . "/lang/");
   	remove_filter("plugin_locale","wpf_lplug",10,2);
  }
  

  $out  = "<div class='wrap'><form method='POST' action='#'>";
  $out .= "<h2>WP-Forecast Widgets</h2>";
  $out .= "<p style='line-height: 30px;'>".__('How many wp-forecast widgets would you like?',"wp-forecast_".$locale)." ";
  $out .= "<select id='wp-forecast-count' name='wp-forecast-count'>";

  for ( $i = 1; $i <= $wpf_maxwidgets; ++$i ) {
    $out .= "<option value='$i' ";
    if ($count==$i)
      $out .= "selected='selected' ";
    $out .= ">$i</option>";
  } 
  $out .= "</select> <span class='submit'><input type='submit' name='wpf-count-submit' id='wpf-count-submit' value=".esc_attr(__('Save'))." /></span></p></form></div>";
  echo $out;

  pdebug(1,"End of function wpf_widget_page ()");
}

function widget_wp_forecast_init()
{

  global $wp_version,$wpf_maxwidgets;

  pdebug(1,"Start of function widget_wp_forecast_init ()");
  
  $count=(int) wpf_get_option('wp-forecast-count');

  // check for widget support
  if ( !function_exists('register_sidebar_widget') )
    return;

  // add fetch weather data to init the cache before any headers are sent
  add_action('init','wp_forecast_init');
  add_action('admin_init','wp_forecast_admin_init');
  
  // add css in header
  add_action('wp_head', 'wp_forecast_css');
 
  

  for ($i=0;$i<=$wpf_maxwidgets;$i++) {
    $wpfcid = get_widget_id( $i );

    // register our widget and add a control
    $name = sprintf(__('wp-forecast %s'), $wpfcid);
    $id = "wp-forecast-$wpfcid"; 
    
    
    // include widget class (new widget api)
    require_once("class-wpf_widget.php");
    // register class
    add_action('widgets_init', create_function('', 'return register_widget("wpf_widget");')); 
    
    wp_unregister_sidebar_widget($i >= $count ? 'wp_forecast_widget'.$wpfcid:'');
    
    wp_register_widget_control($id, $name, $i < $count ? 'wpf_admin_hint' : '',
			       array('width' => 300, 'height' => 150));
    
    wp_unregister_widget_control($i >= $count ? 'wpf_admin_hint'.$wpfcid : '');
  } 

  // add actions for setup the count of wanted wpf widgets
  add_action('sidebar_admin_setup', 'wpf_widget_setup');
  add_action('sidebar_admin_page', 'wpf_widget_page');

  // add filters for transport method check
  add_filter('use_fsockopen_transport','wpf_check_fsockopen');
  add_filter('use_fopen_transport','wpf_check_fopen');
  add_filter('use_streams_transport','wpf_check_streams');
  add_filter('use_http_extension_transport','wpf_check_exthttp');
  add_filter('use_curl_transport','wpf_check_curl');

  pdebug(1,"End of function widget_wp_forecast_init ()");

}



// MAIN

pdebug(1,"Start of MAIN");

// activating deactivating the plugin
register_activation_hook(__FILE__,'wp_forecast_activate');
register_deactivation_hook(__FILE__,'wp_forecast_deactivate');

// add option page 
add_action('admin_menu', 'wp_forecast_admin');

// add super admin options page (check for super admin is done inside)
add_action('admin_menu', 'wpmu_forecast_admin');

// Run our code later in case this loads prior to any required plugins.
add_action('plugins_loaded', 'widget_wp_forecast_init');


pdebug(1,"End of MAIN");
?>
