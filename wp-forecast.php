<?php
/*
Plugin Name: wp-forecast
Plugin URI: http://www.tuxlog.de
Description: wp-forecast is a highly customizable plugin for wordpress, showing weather-data from accuweather.com.
Version: 1.4
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
// maximal number of widgets to use
//
static $wpf_maxwidgets=20;

//
// set to 0 for no debugging information
// set to 1 for call stack
// set to 2 for call stack and variables ( not implemented yet)
//
static $wpf_debug=0;

// xml parser for accuweather xml
require_once("func_xml_parse.php");

// generic functions
require_once("funclib.php");
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
  global $wpf_debug;

  if ($wpf_debug > 0)
    pdebug("Start of function wp_forecast_init ()");
  
  $count=(int) get_option('wp-forecast-count');
  $weather=array();

  for ($i=0;$i<$count;$i++) {
    $wpfcid=get_widget_id($i);

    $wpf_vars=get_wpf_opts($wpfcid);
    $expire=get_option("wp-forecast-expire".$wpfcid);
   
    if ($expire < time()) {
      $w = get_weather($wpf_vars['BASE_URI'],$wpf_vars['location'],
		       $wpf_vars['metric']);
      $weather=wpf_xml_parser($w);
      
      // store weather to database and set expire time
      // if the current data wasnt available use old data
      if ( count($weather)>0) {
	update_option("wp-forecast-cache".$wpfcid, arr2str($weather));
	update_option("wp-forecast-expire".$wpfcid, time()+$wpf_vars['refresh']);
      }
    }
  }
  if ($wpf_debug > 0)
    pdebug("End of function wp_forecast_init ()");
}


//
// this function is called from your template
// to insert your weather data at the place you want it to be
// support to select language on a per call basis from Robert Lang
//
function wp_forecast_widget($args=array(),$wpfcid="A", $language_override=null)
{ 

  global $wpf_debug;
  
  if ($wpf_debug > 0)
    pdebug("Start of function wp_forecast_widget ()");
  
  $wpf_vars=get_wpf_opts($wpfcid);
  if (!empty($language_override)) {
    $wpf_vars['wpf_language']=$language_override;
  }
  $weather=str2arr(get_option("wp-forecast-cache".$wpfcid));
  show($wpfcid,$weather,$args,$wpf_vars);

  if ($wpf_debug > 0)
    pdebug("End of function wp_forecast_widget ()");
}

//
// this is the wrapper function for displaying from sidebar.php
// and not as a widget. since the parameters are different we need this
//
function wp_forecast($wpfcid="A", $language_override=null)
{ 
  global $wpf_debug;
  
  if ($wpf_debug > 0)
    pdebug("Start of function wp_forecast ()");
  
  wp_forecast_widget( array(), $wpfcid, $language_override);

  if ($wpf_debug > 0)
    pdebug("End of function wp_forecast ()");
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
// set the choosen number of widgets, set at the widget page
//
function wpf_widget_setup() {
  global $wpf_debug;

  if ($wpf_debug > 0)
    pdebug("Start of function wpf_widget_setup ()");
  
  $count = $newcount = get_option('wp-forecast-count');
  if ( isset($_POST['wpf-count-submit']) ) {
    $number = (int) $_POST['wpf-number'];
    if ( $number > $wpf_maxwidgets ) $number = $wpf_maxwidgets;
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

  if ($wpf_debug > 0)
    pdebug("End of function wpf_widget_setup ()");
}

//
// form snippet to set the number of wanted widgets from
// the widget page
//
function wpf_widget_page() {
  global $wpf_debug;
  
  if ($wpf_debug > 0)
    pdebug("Start of function wpf_widget_page ()");
  
  $count = $newcount = get_option('wp-forecast-count');
  
  // get locale 
  $locale = get_locale();
  if ( empty($locale) )
    $locale = 'en_US';
  // load translation
  if(function_exists('load_textdomain')) {
    load_textdomain("wp-forecast_".$locale,ABSPATH . "wp-content/plugins/wp-forecast/lang/".$locale.".mo");
  }


  $out  = "<div class='wrap'><form method='POST'>";
  $out .= "<h2>WP-Forecast Widgets</h2>";
  $out .= "<p style='line-height: 30px;'>".__('How many wp-forecast widgets would you like?',"wp-forecast_".$locale)." ";
  $out .= "<select id='wpf-number' name='wpf-number' value='".$count."'>";

  for ( $i = 1; $i <= $wpf_maxwidgets; ++$i ) {
    $out .= "<option value='$i' ";
    if ($count==$i)
      $out .= "selected='selected' ";
    $out .= ">$i</option>";
  } 
  $out .= "</select> <span class='submit'><input type='submit' name='wpf-count-submit' id='wpf-count-submit' value=".attribute_escape(__('Save'))." /></span></p></form></div>";
  echo $out;

  if ($wpf_debug > 0)
    pdebug("End of function wpf_widget_page ()");
}

function widget_wp_forecast_init()
{

  global $wp_version,$wpf_debug,$wpf_maxwidgets;

  if ($wpf_debug > 0)
    pdebug("Start of function widget_wp_forecast_init ()");
  
  $count=(int) get_option('wp-forecast-count');

  // check for widget support
  if ( !function_exists('register_sidebar_widget') )
    return;

  // add fetch weather data to init the cache before any headers are sent
  add_action('init','wp_forecast_init');
  add_action('init','wp_forecast_admin_init');
  
  // add css in header
  add_action('wp_head', 'wp_forecast_css');
  
  for ($i=0;$i<=$wpf_maxwidgets;$i++) {
    $wpfcid = get_widget_id( $i );

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
			    ,300,150,$wpfcid,1);
  } 
  // add actions for setup the count of wanted wpf widgets
  add_action('sidebar_admin_setup', 'wpf_widget_setup');
  add_action('sidebar_admin_page', 'wpf_widget_page');

  if ($wpf_debug > 0)
    pdebug("End of function widget_wp_forecast_init ()");

}



// MAIN

if ($wpf_debug > 0)
     pdebug("Start of MAIN");

// activating deactivating the plugin
register_activation_hook(__FILE__,'wp_forecast_activate');
register_deactivation_hook(__FILE__,'wp_forecast_deactivate');

// add option page 
add_action('admin_menu', 'wp_forecast_admin');

// Run our code later in case this loads prior to any required plugins.
add_action('plugins_loaded', 'widget_wp_forecast_init');

if ($wpf_debug > 0)
     pdebug("End of MAIN");
?>
