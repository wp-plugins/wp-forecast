<?php
/* This file is part of the wp-forecast plugin for wordpress */

/*  Copyright 2006-2009  Hans Matzen  (email : webmaster at tuxlog dot de)

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


// location array and counter init
$loc=array();
$i=0;

// generic functions
require_once("funclib.php");
require_once("func_accu.php");
require_once("func_bug.php");

//
// delete cache if parameters are changed, to make sure
// current data will be available with next call
//
function wp_forecast_admin_init() 
{
  pdebug(1,"Start of wp_forecast_admin_init ()");
  
  $count = get_option('wp-forecast-count');

  if ( ($_SERVER['QUERY_STRING']=="page=wp-forecast-admin.php") && 
       (isset($_POST['info_update']) ))
    {
      for ($i=0;$i<$count;$i++) {
	$wpfcid = get_widget_id( $i );

	// delete cache for old location
	update_option("wp-forecast-expire".$wpfcid,"0");
	update_option("wp-forecast-cache".$wpfcid,"");
      }
    }
 
  // add thickbox and jquery for checklist 
  wp_enqueue_script( 'thickbox' );
  wp_enqueue_style ( 'thickbox' );

  pdebug(1,"End of wp_forecast_admin_init ()");
}

//
// add menuitem for options menu
//
function wp_forecast_admin() 
{
  pdebug(1,"Start of wp_forecast_admin ()");
  
  add_menu_page('wp-Forecast', 'wp-Forecast', 6, 
		basename(__FILE__), 'wpf_admin_form',
		site_url("/wp-content/plugins/wp-forecast") . '/wpf.png');

  pdebug(1,"End of wp_forecast_admin ()");
} 

//
// print out hint for the widget control
//
function wpf_admin_hint($args = null) 
{
  pdebug(1,"Start of wp_admin_hint ()");

  $wpfcid = $args;

  // get translation 
  $locale = get_locale();
  if ( empty($locale) )
    $locale = 'en_US';
  if(function_exists('load_textdomain')) 
    load_textdomain("wp-forecast_".$locale,ABSPATH . "wp-content/plugins/wp-forecast/lang/".$locale.".mo");
  
  // code for widget title form 
  $av=get_wpf_opts($wpfcid);
  $av['title'] = $newtitle = $av['title'];
 
  if ( $_POST["wpf-submit-title".$wpfcid] ) 
    $newtitle = strip_tags(stripslashes($_POST["wpf-title-".$wpfcid]));

  if ( $av['title'] != $newtitle ) {
    $av['title'] = $newtitle;
    update_option('wp-forecast-opts'.$wpfcid, serialize($av));
  }

  echo __("Title:","wp-forecast_".$locale);
  echo " <input style='width: 250px;' id='wpf-title-". $wpfcid ."' name='wpf-title-" . $wpfcid . "' type='text' value='". $av['title'] . "' />";
  echo "<input type='hidden' id='wpf-submit-title" . $wpfcid . "' name='wpf-submit-title".$wpfcid."' value='1' />";
  echo "<p>".__('widget_hint',"wp-forecast_".$locale)."</p>";

  pdebug(1,"End of wp_admin_hint ()");
}

// 
// get the locationlist and return it in one long string
// 
function get_loclist($uri,$loc)
{
  pdebug(1,"Start of get_loclist ()");

  $url=$uri . urlencode($loc);
  $xml = fetchURL($url);

  pdebug(1,"End of get_loclist ()");

  return $xml;
}


//
// form handler for the widgets
//
function wpf_admin_form($wpfcid='A',$widgetcall=0) 
{
    global $wpf_maxwidgets;

  pdebug(1,"Start of wpf_admin_form ()");  

  $count = get_option('wp-forecast-count');
  $wpf_timeout = get_option("wp-forecast-timeout");
  $wpf_delopt = get_option("wp-forecast-delopt");

  // get locale 
  $locale = get_locale();
  if ( empty($locale) )
    $locale = 'en_US';

  // called via the options menu not from widgets
  if ($widgetcall==0) {
    // load translation
    if(function_exists('load_textdomain')) {
      load_textdomain("wp-forecast_".$locale,ABSPATH . "wp-content/plugins/wp-forecast/lang/".$locale.".mo");
    }

    // if this is a post call, number of widgets 
    if ( isset($_POST['wpf-count-submit']) ) {
      $number = (int) $_POST['wpf-number'];
      if ( $number > $wpf_maxwidgets ) $number = $wpf_maxwidgets;
      if ( $number < 1 ) $number = 1;
      $newcount = $number;

      if ( $count != $newcount ) {
	$count = $newcount;
	update_option('wp-forecast-count', $count);
	// add missing option to database
	wp_forecast_activate();
	// init the new number of widgets
	widget_wp_forecast_init($count);
      }
    } 

    // if this is a post call, timeout
    if ( isset($_POST['wpf-timeout-submit']) ) {
      $timeout = (int) $_POST['wpf-timeout'];
      if ( $timeout < 0 ) $timeout = 1;
      
      if ( $wpf_timeout != $timeout ) {
	$wpf_timeout = $timeout;
	update_option('wp-forecast-timeout', $wpf_timeout);
      }
    } 
  
    // if this is a post call, delopt 
    if ( isset($_POST['wpf-delopt-submit']) ) 
    {
	if ( empty($_POST['wpf-delopt']) )
	     $_POST['wpf-delopt'] = 'off';
	$delopt = (int) ($_POST['wpf-delopt'] == "on");
	if ( $wpf_delopt != $delopt ) {
	    $wpf_delopt = $delopt;
	    update_option('wp-forecast-delopt', $wpf_delopt);
	}
    } 


    // if this is a post call, pre-transport
    if ( isset($_POST['wpf-pre-transport-submit']) ) 
    {
	$pre_trans = get_option('wp-forecast-pre-transport');
	if ($_POST['wpf-pre-transport'] != $pre_trans)
	    update_option('wp-forecast-pre-transport', $_POST['wpf-pre-transport']);
    } 


    // print out number of widgets selection
    $out  = "<div class='wrap'>";
    $out .= "<h2>WP-Forecast Widgets</h2>";
    $out .= "<form name='options' id='options' method='post' action=''>";
    $out .= "<table><tr><td width=\"60%\">".__('How many wp-forecast widgets would you like?',"wp-forecast_".$locale)."</td>";
    $out .= "<td width=\"20%\"><select id='wpf-number' name='wpf-number'>";
    
    for ( $i = 1; $i <= $wpf_maxwidgets; ++$i ) {
      $out .= "<option value='$i' ";
      if ($count==$i)
	$out .= "selected='selected' ";
      $out .= ">$i</option>";
    } 
    $out .= "</select></td><td><span class='submit'><input class='button' type='submit' name='wpf-count-submit' id='wpf-count-submit' value='".attribute_escape(__('Save'),"wp-forecast_".$locale)."' /></span></td></tr>";

    // print out widget selection form
    $out .="<tr><td>".__('Available widgets',"wp-forecast_".$locale).": </td>";
    $out .="<td><select name='widgetid' size='1' >";
    for ($i=0;$i<$count;$i++) {
      $id = get_widget_id( $i );
      $out .="<option value='".$id."' ";
      if ( (array_key_exists('widgetid', $_POST) and 
	    $id==$_POST['widgetid'] and isset($_POST['set_widget'])) or
	   (isset($_POST['info_update']) and  $id==$_POST['wid']) or
	   (isset($_POST['search_loc']) and  $id==$_POST['wid']) or
	   (isset($_POST['set_loc']) and  $id==$_POST['wid']))
	$out .="selected";
      $out .=">".$id."</option>";
    }
    $out .= "</select></td>";
    
    $out .='<td><span class="submit"><input class="button" type="submit" name="set_widget" value="' ; 
    $out .=__('Select widget',"wp-forecast_".$locale)." »\" /></span></td></tr>\n";
   

    // print out timeout input field for transport
    // (timeout for data connection)
    $out .= "<tr><td>".__('Timeout for weatherprovider connections (secs.)?',"wp-forecast_".$locale)."</td>";
    $out .= "<td><input id='wpf-timeout' name='wpf-timeout' type='text' size='3' maxlength='3' value='".$wpf_timeout. "' />";
    $out .= "</td><td><span class='submit'><input class='button' type='submit' name='wpf-timeout-submit' id='wpf-timeout-submit' value='".attribute_escape(__('Save'),"wp-forecast_".$locale)."' /></span></td></tr>";

    
    // show transport method selection 
    $out .= "<tr><td>".__('Preselect wordpress transfer method',"wp-forecast_".$locale)." :</td>";
    $out .= "<td><select name='wpf-pre-transport' id='wpf-pre-transport' size='1' >";
    $out .= "<option value='default'>". __("default","wp-forecast".$locale)."</option>";

    // get wordpress default transports
    $pre_trans = get_option("wp-forecast-pre-transport");
    $tlist = get_wp_transports();
    foreach($tlist as $t)
    {
	$out .= "<option value='$t'" . ($t == $pre_trans ? 'selected="selected"':'')  . ">$t</option>";
    }
    $out .= "</select></td>";
    $out .= "<td><span class='submit'><input class='button' type='submit' name='wpf-pre-transport-submit' id='wpf-pre-transport-submit' value='".attribute_escape(__('Save'),"wp-forecast_".$locale)."' /></span></td></tr>";


    // print out option deletion switch
    $out .= "<tr><td>".__('Delete options during plugin deactivation?',"wp-forecast_".$locale)."</td>";
    $out .= "<td><input id='wpf-delopt' name='wpf-delopt' type='checkbox' ";
    if ($wpf_delopt)
      $out .= 'checked="checked"';
    $out .= " />";
    $out .= "</td><td><span class='submit'><input class='button' type='submit' name='wpf-delopt-submit' id='wpf-delopt-submit' value='".attribute_escape(__('Save'),"wp-forecast_".$locale)."' /></span></td></tr></table></form></div>\n"; 
    
    // add link to checklist dialog
    $out .= '<div style="text-align:right"><a href="../wp-content/plugins/wp-forecast/wp-forecast-check.php?height=600&amp;width=800" class="thickbox" Title="">'.__("Check connection to Weatherprovider","wp-forecast_".$locale).'</a></div>'."\n";

    echo $out;
  }
 
  // if this is a post call, select widget
  if (isset($_POST['set_widget']) and  $widgetcall==0)
    $wpfcid = $_POST['widgetid'];

  // if this is any other post call
  if ( (isset($_POST['info_update']) and  $widgetcall==0) or
       (isset($_POST['set_loc']) and $widgetcall==0) or
       (isset($_POST['search_loc']) and $widgetcall==0) )
    $wpfcid = $_POST['wid'];

  // default is the first widget
  if ($wpfcid=="") 
    $wpfcid="A";

  // call sub form
  wpf_sub_admin_form($wpfcid,$widgetcall);
  
  pdebug(1,"End of wpf_admin_form ()");  
}
 
//
// form to modify wp-forecast setup
// the form also has a search function to search the wright location
//
function wpf_sub_admin_form($wpfcid,$widgetcall) {
  global $loc;

  pdebug(1,"Start of wpf_sub_admin_form ()");  

  // get parameters
  $av=get_wpf_opts($wpfcid);

  // get translation 
  $locale = get_locale();
  if ( empty($locale) )
    $locale = 'en_US';
  if(function_exists('load_textdomain')) 
    load_textdomain("wp-forecast_".$locale,ABSPATH . "wp-content/plugins/wp-forecast/lang/".$locale.".mo");
  
  
  // if this is a POST call, save new values
  if (isset($_POST['info_update'])) {
    $upflag=false;

    if ($av['service'] != $_POST["service"]) {
      $av['service'] =  $_POST["service"];
      $upflag=true;
    } 

    if ($av['apikey1'] != $_POST["apikey1"]) {
      $av['apikey1'] =  $_POST["apikey1"];
      $upflag=true;
    }

    if ($av['apikey2'] != $_POST["apikey2"]) {
      $av['apikey2'] =  $_POST["apikey2"];
      $upflag=true;
    }
    
    if ($av['location'] != $_POST["location"]) {
      $av['location'] =  $_POST["location"];
      $upflag=true;
    }
    
    if ($av['locname'] != $_POST["locname"]) {
      $av['locname'] =  $_POST["locname"];
      $upflag=true;
    }	
    
    if ($av['refresh'] != $_POST["refresh"]) {
      $av['refresh'] =  $_POST["refresh"];
      $upflag=true;
    }
    
    if ($av['metric'] != $_POST["metric"]) {
      $av['metric'] =  $_POST["metric"];
      if ($av['metric']=="") $av['metric']="0";
      $upflag=true;
    }
    
    if ($av['windunit'] != $_POST["windunit"]) {
      $av['windunit'] =  $_POST["windunit"];
      $upflag=true;
    }
    
    if ($av['wpf_language'] != $_POST["language"]) {
      $av['wpf_language'] =  $_POST["language"];
      $upflag=true;
    }
   
    if ($av['currtime'] != $_POST["currtime"]) {
      $av['currtime'] =  $_POST["currtime"];
      if ($av['currtime']=="") $av['currtime']="0";
      $upflag=true;
    } 

    if ($av['pdfirstday'] != $_POST["pdfirstday"]) {
      $av['pdfirstday'] =  $_POST["pdfirstday"];
      if ($av['pdfirstday']=="") $av['pdfirstday']="0";
      $upflag=true;
    }

    if ($av['pdforecast'] != $_POST["pdforecast"]) {
	$av['pdforecast'] =  $_POST["pdforecast"];
      if ($av['pdforecast']=="") $av['pdforecast']="0";
      $upflag=true;
    }

    // set checkbox value to zero if not set
    // for forecast options
    $nd = array('day1','day2','day3','day4','day5','day6','day7','day8','day9','night1','night2','night3','night4','night5','night6','night7','night8','night9');
    foreach ($nd as $i) {
      if ($_POST[$i]=="")
	$_POST["$i"]="0";
    }
    
    // set empty checkboxes to 0
    $do = array('d_c_icon','d_c_time','d_c_short','d_c_temp','d_c_real','d_c_press','d_c_humid','d_c_wind','d_c_sunrise','d_c_sunset','d_d_icon','d_d_short','d_d_temp','d_d_wind','d_n_icon','d_n_short','d_n_temp','d_n_wind','d_c_date','d_d_date','d_n_date','d_c_copyright','d_c_wgusts','d_d_wgusts','d_n_wgusts','d_c_accuweather');
    foreach ($do as $i) {
      if ($_POST[$i]=="")
	$_POST["$i"]="0";
    }
    
    // build config string for dispconfig and update if necessary
    $newdispconfig="";
    foreach ($do as $i) 
      $newdispconfig.=$_POST[$i];
    
    if (strcmp($av['dispconfig'],$newdispconfig) != 0) {
      $av['dispconfig'] =  $newdispconfig;
      $upflag=true;
    }
    
    
    // build config string for forecast and update if necessary
    $newdaytime=$_POST["day1"].$_POST["day2"].$_POST["day3"].$_POST["day4"].$_POST["day5"].$_POST["day6"].$_POST["day7"].$_POST["day8"].$_POST["day9"];
    
    if ($av['daytime'] != $newdaytime) {
      $av['daytime'] =  $newdaytime;
      $upflag=true;
    }
    // build config string for forecast and update if necessary
    $newnighttime=$_POST["night1"].$_POST["night2"].$_POST["night3"].$_POST["night4"].$_POST["night5"].$_POST["night6"].$_POST["night7"].$_POST["night8"].$_POST["night9"];
    
    if ($av['nighttime'] != $newnighttime) {
      $av['nighttime'] =  $newnighttime;
      $upflag=true;
    }	
    // put message after update
    echo"<div class='updated'><p><strong>";
    if ($upflag) {
      update_option("wp-forecast-opts".$wpfcid,serialize($av));
      update_option("wp-forecast-expire".$wpfcid,"0");
      echo __('Settings successfully updated',"wp-forecast_".$locale);
    }  else
      echo __('You have to change a field to update settings.',"wp-forecast_".$locale);
    echo "</strong></p></div>";
  } 
  
  // if this is a POST call, search locations
  if (isset($_POST['search_loc'])) {
    // search location for accuweather
    if ($av['service'] == "accu") {
      $xml=get_loclist($av['ACCU_LOC_URI'],$_POST["searchloc"]);
      $xml=utf8_encode($xml);
      accu_get_locations($xml); // modifies global array $loc
    }

    // search location for weather bug
    if ($av['service'] == "bug") {
      $blu=str_replace('#apicode#',$av['apikey1'],$av['BUG_LOC_URI']);
      $xml=get_loclist($blu,$_POST["searchloc"]);
      $xml=utf8_encode($xml);

     
      bug_get_locations($xml); // modifies global array $loc
    }
    // search location for weather.com
    if ($av['service'] == "com") {
      //$xml=get_loclist($COM_LOC_URI,$_POST["searchloc"]);
      //$xml=utf8_encode($xml);
      //if ( !function_exists('com_get_locations') )
      //require_once("func_com.php");
      //com_get_locations($xml); // modifies global array $loc
    }
  }
  
  // if this is a POST call, set location
  if (isset($_POST['set_loc'])) {
    $av['location']=$_POST["newloc"];
  }
?>

	 <?php if ($widgetcall == 0): ?><div class="wrap">
         <!-- add javascript for field control -->
         <?php 
	     include("wp-forecast-js.php"); 
         ?>
	 <form method="post" name='woptions' action=''>
         <?php endif; ?>
	 <input name='wid' type='hidden' value='<?php echo $wpfcid; ?>'/>   
	 <h2><?php echo __('WP-Forecast Setup',"wp-forecast_".$locale)." (Widget ".$wpfcid.") ";?></h2>
	  <?php if ($widgetcall == 0): ?><fieldset id="set1"><?php endif; ?>
	 <div style="float: left; width: 49%">
         <p><b><?php echo __('Weatherservice',"wp-forecast_".$locale)?>:</b>
         <select name="service" size="1" onchange="apifields(document.woptions.service.value);">
	   <option value="accu" <?php if ($av['service']=="accu") echo "selected=\"selected\""?>><?php echo __('AccuWeather',"wp-forecast_".$locale)?></option>
           <option value="bug" <?php if ($av['service']=="bug") echo "selected=\"selected\""?>><?php echo __('WeatherBug',"wp-forecast_".$locale)?></option>
           <!--
           <option value="com" <?php if ($av['service']=="com") echo "selected=\"selected\""?>><?php echo __('Weather.com',"wp-forecast_".$locale)?></option> -->
	 </select></p>

         <p><?php echo __('Partner ID',"wp-forecast_".$locale)?>:
         <input name="apikey1" type="text" size="30" maxlength="80" value="<?php echo $av['apikey1'] ?>" /></p>
         <!--
         <p><?php echo __('Licensekey',"wp-forecast_".$locale)?>:
         <input name="apikey2" type="text" size="30" maxlength="80" value="<?php echo $av['apikey2'] ?>" /></p>
         -->
         <script type="text/javascript">apifields(document.woptions.service.value);</script>

	 <p><b><?php echo __('Location',"wp-forecast_".$locale)?>:</b>
	 <input name="location" type="text" size="30" maxlength="80" value="<?php echo $av['location'] ?>"<?php if ($widgetcall==1) echo "readonly" ?> />
	 <?php if (isset($_POST['set_loc'])) { ?>
	       <p><b><?php echo __('Press Update options to save new location.',"wp-forecast_".$locale)?></b></p>
         <?php } ?>
         
         <p><b><?php echo __('Locationname',"wp-forecast_".$locale)?>:</b>
         <input name="locname" type="text" size="30" maxlength="80" value="<?php echo $av['locname'] ?>" /></p>
	 <p><b><?php echo __('Refresh cache after',"wp-forecast_".$locale)?></b>
         <input name="refresh" type="text" size="10" maxlength="6" value="<?php echo $av['refresh'] ?>"/>
         <b><?php echo __('secs.',"wp-forecast_".$locale)?></b><br /></p>
	 <p><input type="checkbox" name="metric" value="1" <?php if ($av['metric']=="1") echo "checked=\"checked\""?> /> <b><?php echo __('Use metric units',"wp-forecast_".$locale)?></b>
	 </p>

	 <p><input type="checkbox" name="currtime" value="1" <?php if ($av['currtime']=="1") echo "checked=\"checked\""?> /> <b><?php echo __('Use current time',"wp-forecast_".$locale)?></b>
         </p>

         <p><b><?php echo __('Windspeed-Unit',"wp-forecast_".$locale)?>: </b><select name="windunit" size="1">
	      <option value="ms" <?php if ($av['windunit']=="ms") echo "selected=\"selected\""?>><?php echo __('Meter/Second (m/s)',"wp-forecast_".$locale)?></option>
              <option value="kmh" <?php if ($av['windunit']=="kmh") echo "selected=\"selected\""?>><?php echo __('Kilometer/Hour (km/h)',"wp-forecast_".$locale)?></option>
              <option value="mph" <?php if ($av['windunit']=="mph") echo "selected=\"selected\""?>><?php echo __('Miles/Hour (mph)',"wp-forecast_".$locale)?></option>
              <option value="kts" <?php if ($av['windunit']=="kts") echo "selected=\"selected\""?>><?php echo __('Knots (kts)',"wp-forecast_".$locale)?></option>
	 </select></p>


        

	 <p>
         <b><?php echo __('Language',"wp-forecast_".$locale)?>: </b><select name="language" size="1">
	    <option value="en_US" <?php if ($av['wpf_language']=="en_US") echo "selected=\"selected\""?>>english</option>
	    <option value="de_DE" <?php if ($av['wpf_language']=="de_DE") echo "selected=\"selected\""?>>deutsch</option>
            <option value="da_DK" <?php if ($av['wpf_language']=="da_DK") echo "selected=\"selected\""?>>dansk</option>
	    <option value="nl_NL" <?php if ($av['wpf_language']=="nl_NL") echo "selected=\"selected\""?>>dutch</option>
            <option value="fi_FI" <?php if ($av['wpf_language']=="fi_FI") echo "selected=\"selected\""?>>finnish</option>
            <option value="fr_FR" <?php if ($av['wpf_language']=="fr_FR") echo "selected=\"selected\""?>>french</option>
            <option value="hu_HU" <?php if ($av['wpf_language']=="hu_HU") echo "selected=\"selected\""?>>hungarian</option>
            <option value="it_IT" <?php if ($av['wpf_language']=="it_IT") echo "selected=\"selected\""?>>italian</option>
	    <option value="pl_PL" <?php if ($av['wpf_language']=="pl_PL") echo "selected=\"selected\""?>>polish</option>
            <option value="pt_PT" <?php if ($av['wpf_language']=="pt_PT") echo "selected=\"selected\""?>>portugu&#234;s</option>
            <option value="ro_RO" <?php if ($av['wpf_language']=="ro_RO") echo "selected=\"selected\""?>>romanian</option>
            <option value="ru_RU" <?php if ($av['wpf_language']=="ru_RU") echo "selected=\"selected\""?>>russian</option> 
            <option value="nb_NO" <?php if ($av['wpf_language']=="nb_NO") echo "selected=\"selected\""?>>norwegian</option>
	    <option value="es_ES" <?php if ($av['wpf_language']=="es_ES") echo "selected=\"selected\""?>>spanish</option>
            <option value="sv_SE" <?php if ($av['wpf_language']=="sv_SE") echo "selected=\"selected\""?>>swedish</option>
	    
         </select></p>
 
         <p><input type="checkbox" id="pdforecast" name="pdforecast" value="1" <?php if ($av['pdforecast']=="1") echo "checked=\"checked\""?> onchange="pdfields_update();" /> <b><?php echo __('Show forecast as ajax pull-down',"wp-forecast_".$locale)?></b>
         </p>

         <p>
         <b><?php echo __('First day in pull-down',"wp-forecast_".$locale)?>: </b><select id="pdfirstday" name="pdfirstday" size="1">
   <option value="1" <?php if ($av['pdfirstday']=="1") echo "selected=\"selected\""?>>1</option>
   <option value="2" <?php if ($av['pdfirstday']=="2") echo "selected=\"selected\""?>>2</option>
   <option value="3" <?php if ($av['pdfirstday']=="3") echo "selected=\"selected\""?>>3</option>
   <option value="4" <?php if ($av['pdfirstday']=="4") echo "selected=\"selected\""?>>4</option>
   <option value="5" <?php if ($av['pdfirstday']=="5") echo "selected=\"selected\""?>>5</option>
   <option value="6" <?php if ($av['pdfirstday']=="6") echo "selected=\"selected\""?>>6</option>
   <option value="7" <?php if ($av['pdfirstday']=="7") echo "selected=\"selected\""?>>7</option>
   <option value="8" <?php if ($av['pdfirstday']=="8") echo "selected=\"selected\""?>>8</option>
   <option value="9" <?php if ($av['pdfirstday']=="9") echo "selected=\"selected\""?>>9</option>
   </select></p>
   <script type="text/javascript">pdfields_update();</script>

       </div>
       <!-- start of right column -->
       <div  style="padding-left: 2%; float: left; width: 49%;">
       <b><?php echo __('Display Configuration',"wp-forecast_".$locale)?></b>
        <table>
	<tr>
         <td>&nbsp;</td>
         <td><?php echo __('Current Conditions',"wp-forecast_".$locale)?></td>
         <td><?php echo __('Forecast Day',"wp-forecast_".$locale)?></td>
         <td><?php echo __('Forecast Night',"wp-forecast_".$locale)?></td>
        </tr>
        <tr>
        <td><?php echo __('Icon',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_icon" value="1" 
		 <?php if (substr($av['dispconfig'],0,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'><input type="checkbox" name="d_d_icon" value="1" 
		 <?php if (substr($av['dispconfig'],10,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'><input type="checkbox" name="d_n_icon" value="1" 
		 <?php if (substr($av['dispconfig'],14,1)=="1") echo "checked=\"checked\""?> /></td>
         </tr>
          <tr>
         <td><?php echo __('Date',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_date" value="1" 
		 <?php if (substr($av['dispconfig'],18,1)=="1") echo "checked=\"checked\""?> /></td>
         <td align='center'>&nbsp;</td>
         <td align='center'>&nbsp;</td>
         </tr>
	 <tr>
         <td><?php echo __('Time',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_time" value="1" 
		 <?php if (substr($av['dispconfig'],1,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'>&nbsp;</td>
        <td align='center'>&nbsp;</td>
        </tr> 
        <tr>
        <td><?php echo __('Short Description',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_short" value="1" 
	     <?php if (substr($av['dispconfig'],2,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'><input type="checkbox" name="d_d_short" value="1" 
	     <?php if (substr($av['dispconfig'],11,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'><input type="checkbox" name="d_n_short" value="1" 
	     <?php if (substr($av['dispconfig'],15,1)=="1") echo "checked=\"checked\""?> /></td>
        </tr> 
        <tr>
        <td><?php echo __('Temperature',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_temp" value="1" 
	     <?php if (substr($av['dispconfig'],3,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'><input type="checkbox" name="d_d_temp" value="1" 
	     <?php if (substr($av['dispconfig'],12,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'><input type="checkbox" name="d_n_temp" value="1" 
	     <?php if (substr($av['dispconfig'],16,1)=="1") echo "checked=\"checked\""?> /></td>
        </tr> 
        <tr>
        <td><?php echo __('Realfeel',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_real" value="1" 
	     <?php if (substr($av['dispconfig'],4,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'>&nbsp;</td>
        <td align='center'>&nbsp;</td>
        </tr> 
        <tr>
        <td><?php echo __('Pressure',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_press" value="1" 
	     <?php if (substr($av['dispconfig'],5,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'>&nbsp;</td>
        <td align='center'>&nbsp;</td>
        </tr> 
        <tr>
        <td><?php echo __('Humidity',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_humid" value="1" 
	     <?php if (substr($av['dispconfig'],6,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'>&nbsp;</td>
        <td align='center'>&nbsp;</td>
        </tr> 
        <tr>
        <td><?php echo __('Wind',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_wind" value="1" 
	     <?php if (substr($av['dispconfig'],7,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'><input type="checkbox" name="d_d_wind" value="1" 
	     <?php if (substr($av['dispconfig'],13,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'><input type="checkbox" name="d_n_wind" value="1" 
  	    <?php if (substr($av['dispconfig'],17,1)=="1") echo "checked=\"checked\""?> /></td>
        </tr> 
	<tr>
        <td><?php echo __('Windgusts',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_wgusts" value="1" 
	     <?php if (substr($av['dispconfig'],22,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'><input type="checkbox" name="d_d_wgusts" value="1" 
	     <?php if (substr($av['dispconfig'],23,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'><input type="checkbox" name="d_n_wgusts" value="1" 
  	     <?php if (substr($av['dispconfig'],24,1)=="1") echo "checked=\"checked\""?> /></td>
        </tr>         
	<tr>
        <td><?php echo __('Sunrise',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_sunrise" value="1" 
		 <?php if (substr($av['dispconfig'],8,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'>&nbsp;</td>
        <td align='center'>&nbsp;</td>
        </tr> 
        <tr>
        <td><?php echo __('Sunset',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_sunset" value="1" 
		 <?php if (substr($av['dispconfig'],9,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'>&nbsp;</td>
        <td align='center'>&nbsp;</td>
        </tr>
        <tr>
        <td><?php echo __('Copyright',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_copyright" value="1" 
		 <?php if (substr($av['dispconfig'],21,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'>&nbsp;</td>
        <td align='center'>&nbsp;</td>
        </tr> 
	<tr>
        <td><?php echo __('Link to Weatherprovider',"wp-forecast_".$locale)?></td>
        <td align='center'><input type="checkbox" name="d_c_accuweather" value="1" 
		 <?php if (substr($av['dispconfig'],25,1)=="1") echo "checked=\"checked\""?> /></td>
        <td align='center'>&nbsp;</td>
        <td align='center'>&nbsp;</td>
        </tr> 
        </table>
	<br /> 

<b><?php echo __('Forecast',"wp-forecast_".$locale)?></b>
         					      
         <table>
         <tr>
             <td>&nbsp;</td>
             <td><?php echo __('All',"wp-forecast_".$locale)?></td>
             <td><?php echo __('Day',"wp-forecast_".$locale)?> 1</td>
             <td><?php echo __('Day',"wp-forecast_".$locale)?> 2</td>
             <td><?php echo __('Day',"wp-forecast_".$locale)?> 3</td>
             <td><?php echo __('Day',"wp-forecast_".$locale)?> 4</td>
             <td><?php echo __('Day',"wp-forecast_".$locale)?> 5</td>
             <td><?php echo __('Day',"wp-forecast_".$locale)?> 6</td>
             <td><?php echo __('Day',"wp-forecast_".$locale)?> 7</td>
             <td><?php echo __('Day',"wp-forecast_".$locale)?> 8</td>
             <td><?php echo __('Day',"wp-forecast_".$locale)?> 9</td>
         </tr>
         <tr><td><?php echo __('Daytime',"wp-forecast_".$locale)?></td>
             <td><input type="checkbox" name="alldays" onClick="this.value=check('day')" /></td>
             <td><input type="checkbox" name="day1" value="1" 
		   <?php if (substr($av['daytime'],0,1)=="1") echo "checked=\"checked\""?> /></td>
             <td><input type="checkbox" name="day2" value="1" 
		   <?php if (substr($av['daytime'],1,1)=="1") echo "checked=\"checked\""?> /></td> 
             <td><input type="checkbox" name="day3" value="1" 
		   <?php if (substr($av['daytime'],2,1)=="1") echo "checked=\"checked\""?> /></td> 
	     <td><input type="checkbox" name="day4" value="1" 
		   <?php if (substr($av['daytime'],3,1)=="1") echo "checked=\"checked\""?> /></td> 
             <td><input type="checkbox" name="day5" value="1" 
		   <?php if (substr($av['daytime'],4,1)=="1") echo "checked=\"checked\""?> /></td> 
             <td><input type="checkbox" name="day6" value="1" 
		   <?php if (substr($av['daytime'],5,1)=="1") echo "checked=\"checked\""?> /></td> 
             <td><input type="checkbox" name="day7" value="1" 
		   <?php if (substr($av['daytime'],6,1)=="1") echo "checked=\"checked\""?> /></td> 
             <td><input type="checkbox" name="day8" value="1" 
		   <?php if (substr($av['daytime'],7,1)=="1") echo "checked=\"checked\""?> /></td> 
             <td><input type="checkbox" name="day9" value="1" 
		   <?php if (substr($av['daytime'],8,1)=="1") echo "checked=\"checked\""?> /></td>
         </tr>
         <tr><td><?php echo __('Nighttime',"wp-forecast_".$locale)?></td>
             <td><input type="checkbox" name="allnight" onClick="this.value=check('night')" /></td>
             <td><input type="checkbox" name="night1" value="1" 
		 <?php if (substr($av['nighttime'],0,1)=="1") echo "checked=\"checked\""?> /></td>
             <td><input type="checkbox" name="night2" value="1" 
		 <?php if (substr($av['nighttime'],1,1)=="1") echo "checked=\"checked\""?> /></td> 
             <td><input type="checkbox" name="night3" value="1" 
		 <?php if (substr($av['nighttime'],2,1)=="1") echo "checked=\"checked\""?> /></td> 
	     <td><input type="checkbox" name="night4" value="1" 
		 <?php if (substr($av['nighttime'],3,1)=="1") echo "checked=\"checked\""?> /></td> 
             <td><input type="checkbox" name="night5" value="1" 
		 <?php if (substr($av['nighttime'],4,1)=="1") echo "checked=\"checked\""?> /></td> 
             <td><input type="checkbox" name="night6" value="1" 
		 <?php if (substr($av['nighttime'],5,1)=="1") echo "checked=\"checked\""?> /></td> 
             <td><input type="checkbox" name="night7" value="1" 
		 <?php if (substr($av['nighttime'],6,1)=="1") echo "checked=\"checked\""?> /></td> 
             <td><input type="checkbox" name="night8" value="1" 
		 <?php if (substr($av['nighttime'],7,1)=="1") echo "checked=\"checked\""?> /></td> 
             <td><input type="checkbox" name="night9" value="1" 
		 <?php if (substr($av['nighttime'],8,1)=="1") echo "checked=\"checked\""?> /></td>
         </tr>
       </table>
       </div>
       <!-- finally update field attributes -->		   
       <script type="text/javascript">
	       apifields(document.woptions.service.value);
       </script>
       <?php if ($widgetcall==0): ?></fieldset><?php endif; ?>
<?php
   if ($widgetcall ==0) 
     echo "<div class='submit'><input class='button-primary' type='submit' name='info_update' value='".__('Update options',"wp-forecast_".$locale)." »' /></div>";
   else
     echo "<input type='hidden' name='info_update' value='1' />";
   
   //  suchformular fuer locations
   if ($widgetcall==0) 
     {
       echo "<hr /><fieldset id=\"set2\"><legend>".__('Search location',"wp-forecast_".$locale)."</legend><br />";
     
       if (count($loc)<=0 ) 
	 { 	 
	   echo "<p><b>".__('Searchterm',"wp-forecast_".$locale).":</b>\n";
	   echo "<input name=\"searchloc\" type=\"text\" size=\"30\" maxlength=\"30\" /><br /></p>\n";
	   if (isset($_POST['search_loc'])) 
	     { 
	       echo "<p>".__('No locations found.',"wp-forecast_".$locale)."</p>";
	     } 
	   else 
	     {
	       echo "<p>".__('Please replace german Umlaute ä,ö,ü with a, o, u in your searchterm.',"wp-forecast_".$locale)."</p>";
	     }
	   echo "</fieldset>\n";
	   echo "<div class='submit'>\n";
	   echo "<input class='button-primary' type='submit' name='search_loc' value='" ;
	   echo __('Search location',"wp-forecast_".$locale);
	   echo " »' />\n";
	 } 
       else 
	 {
	   echo "<b>".__('Search result',"wp-forecast_".$locale).": </b><select name=\"newloc\" size=\"1\">\n";
	   foreach ($loc as $l) 
	     {
	       echo "<option value=\"".$l['location']."\">";
	       echo $l['city']."/".$l['state'];
	       echo "</option>\n";
	     }
	   echo "</select><br /><p>".__('Please select your city and press set location.',"wp-forecast_".$locale)."</p>\n";
	   echo "</fieldset>\n";
	   echo "<div class='submit'>\n";
	   echo "<input class='button-primary' type='submit' name='set_loc' value='" ;
	   echo  __('Set location',"wp-forecast_".$locale);
	   echo " »' />\n";
	 }
       echo "</div></form></div>";
     }
   
   pdebug(1,"End of wpf_sub_admin_form ()");
}
?>
