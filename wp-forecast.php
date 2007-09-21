<?php
/*
Plugin Name: wp-forecast
Plugin URI: http://www.tuxlog.de
Description: wp-forecast is a highly customizable plugin for wordpress, showing weather-data from accuweather.com.
Version: 1.0
Author: Hans Matzen <webmaster at tuxlog.de>
Author URI: http://www.tuxlog.de
*/

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
// string of ids for multi widget support
//
static $wpf_idstr="ABCDEFGHIJKLMNOPQRSTUVWXYZ";


// xml parser for accuweather xml
require_once("func_xml_parse.php");
// include translations
require_once("language.php");
// generic functions
require_once("functions.php");
// include setup functions
require_once("setup.php");
// include admin options page
require_once("wp-forecast-admin.php");
// display functions
require_once("wp-forecast-show.php");


//
// set cache with weather data for current parameters
// a wrapper function called via the init hook
//

function wp_forecast_init() 
{
  global $wpf_idstr;
  
  $count=(int) get_option('wp-forecast-count');
  $weather=array();

  for ($i=0;$i<$count;$i++) {
    $wpfcid=substr($wpf_idstr,$i,1);

    $wpf_vars=get_wpf_opts($wpfcid);
    $expire=get_option("wp-forecast-expire".$wpfcid);
   
    if ($expire < time()) {
      $w = get_weather($wpf_vars['BASE_URI'],$wpf_vars['location'],
		       $wpf_vars['metric']);
      $weather=wpf_xml_parser($w);
      
      // store weather to database and set expire time
      if ( count($weather)>0) {
	update_option("wp-forecast-cache".$wpfcid, arr2str($weather));
	update_option("wp-forecast-expire".$wpfcid, time()+$wpf_vars['refresh']);
      }
    }
  }
}

// 
// this function is called from your template
// to insert your weather data at the place you want it to be
//
function wp_forecast_widget($args=array(),$wpfcid="A")
{
  $wpf_vars=get_wpf_opts($wpfcid);
  $weather=str2arr(get_option("wp-forecast-cache".$wpfcid));
  show($wpfcid,$weather,$args,$wpf_vars);
}

//
// this is the wrapper function for displaying from sidebar.php
// and not as a widget. since the parameters are different we need this
//
function wp_forecast($wpfcid="A")
{
  wp_forecast_widget( array(), $wpfcid);
}


//
// set the choosen number of widgets, set at the widget page
//
function wpf_widget_setup() {
  $count = $newcount = get_option('wp-forecast-count');
  if ( isset($_POST['wpf-count-submit']) ) {
    $number = (int) $_POST['wpf-number'];
    if ( $number > 20 ) $number = 20;
    if ( $number < 1 ) $number = 1;
    $newcount = $number;
  }
  if ( $count != $newcount ) {
    $count = $newcount;
    update_option('wp-forecast-count', $count);
    // add missing option to database
    wp_forecast_activate();
    // init the new number of widgets
    widget_wp_forecast_init($count);
  }
}

//
// form snippet to set the number of wanted widgets from
// the widget page
//
function wpf_widget_page() {
  $count = $newcount = get_option('wp-forecast-count');

  // get translations
  $wpf_language=get_option("wp-forecast-languageA");
  $tl=array();
  $tl=set_translation($wpf_language);
  
  $out  = "<div class='wrap'><form method='POST'>";
  $out .= "<h2>WP-Forecast Widgets</h2>";
  $out .= "<p style='line-height: 30px;'>".$tl['How many wp-forecast widgets would you like?']." ";
  $out .= "<select id='wpf-number' name='wpf-number' value='".$count."'>";

  for ( $i = 1; $i < 30; ++$i ) {
    $out .= "<option value='$i' ";
    if ($count==$i)
      $out .= "selected='selected' ";
    $out .= ">$i</option>";
  } 
  $out .= "</select> <span class='submit'><input type='submit' name='wpf-count-submit' id='wpf-count-submit' value=".attribute_escape(__('Save'))." /></span></p></form></div>";
  echo $out;
}

function widget_wp_forecast_init()
{

  global $wpf_idstr,$wp_version;

  $count=(int) get_option('wp-forecast-count');

  // check for widget support
  if ( !function_exists('register_sidebar_widget') )
    return;

  // add fetch weather data to init the cache before any headers are sent
  add_action('init','wp_forecast_init');
  add_action('init','wp_forecast_admin_init');
  
  // add css in header
  add_action('wp_head', 'wp_forecast_css');
  
  for ($i=0;$i<30;$i++) {
    $wpfcid=substr($wpf_idstr,$i,1);

    // register our widget and add a control
    $name = sprintf(__('wp-forecast %s'), $wpfcid);
    $id = "wp-forecast$wpfcid"; 
    
    // register / unregister widget and control form
    // the first part is to work around bug 4275 which was
    // corrected with v2.2.1
    if ($wp_version < "2.2.1") 
      register_sidebar_widget(array($id,$name),
			      $i < $count ? 'wp_forecast_widget' : '','',$wpfcid);
    else
      register_sidebar_widget(array($id,$name),
			      $i < $count ? 'wp_forecast_widget' : '',$wpfcid);

    register_widget_control(array($id,$name),
			    $i < $count ? 'wpf_admin_hint' : ''
			    ,300,100,$wpfcid,1);
  } 
  // add actions for setup the count of wanted wpf widgets
  add_action('sidebar_admin_setup', 'wpf_widget_setup');
  add_action('sidebar_admin_page', 'wpf_widget_page');
}



// MAIN

// activating deactivating the plugin
register_activation_hook(__FILE__,'wp_forecast_activate');
register_deactivation_hook(__FILE__,'wp_forecast_deactivate');

// add option page 
add_action('admin_menu', 'wp_forecast_admin');

// Run our code later in case this loads prior to any required plugins.
add_action('plugins_loaded', 'widget_wp_forecast_init');

?>
