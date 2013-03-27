<?php
/* This file is part of the wp-forecast plugin for wordpress */

/*  Copyright 2010-2013 Hans Matzen

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


// generic functions
require_once("funclib.php");


//
// add menuitem for options menu
//
function wpmu_forecast_admin() 
{
  pdebug(1,"Start of wpmu_forecast_admin ()");

   $cssurl  = WP_PLUGIN_URL . '/wp-forecast/wpf_sa_admin.css';
   $cssfile = WP_PLUGIN_DIR . '/wp-forecast/wpf_sa_admin.css';
   if ( file_exists($cssfile) ) {
       wp_register_style('wpf_sa_admin', $cssurl);
       wp_enqueue_style( 'wpf_sa_admin');
   }

   // check if we are superadmin and may see the super admin dialog
   if ( function_exists("is_multisite") && is_multisite() && is_super_admin() )
       add_submenu_page("options-general.php", 'wp-forecast', 'wp-forecast', 'edit_plugins', 
			basename(__FILE__), 'wpf_wpmu_admin_form',
			plugins_url('/wpf_sa.png',__FILE__));

  pdebug(1,"End of wpmu_forecast_admin ()");
} 

//
// form handler for the widgets
//
function wpf_wpmu_admin_form($wpfcid='A',$widgetcall=0) 
{
    pdebug(1,"Start of wpf_wpmu_admin_form ()");  
    
    // check for wp-forecast
    $all_plugins = get_plugins();
    $found = false;
    foreach ( (array)$all_plugins as $plugin_file => $plugin_data) {
	if ( $plugin_data['Name'] == "wp-forecast" &&   is_plugin_active($plugin_file) ) 
	    $found = true;
    }
    if ( ! is_plugin_active("wp-forecast/wp-forecast.php") )
	$out .= '<div class="error"><p>'.__("wp-forecast plugin not found. Please install and activate wp-forecast.").'</p></div>';


    // if this is a post call delete_sa_opts
    if ( isset( $_POST['del_sa_opts'] ) && $_POST['delete_sa_opts']==1 ) {
	delete_option("wpf_sa_defaults");
	delete_option("wpf_sa_allowed");
    }

    // get opts
    $defaults = maybe_unserialize(get_option("wpf_sa_defaults"));
    $allowed  = maybe_unserialize(get_option("wpf_sa_allowed"));
    if ($defaults == "") $defaults=array();
    if ($allowed  == "") $allowed=array();
    
    extract($allowed);
    extract($defaults);

    $av=$defaults;

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
    
    

    // if this is a post call
    if ( isset($_POST['info_update']) ) {
	$allowed['ue_wp-forecast-count']         = $_POST['ue_wp-forecast-count'];	
	$allowed['ue_wp-forecast-timeout']       = $_POST['ue_wp-forecast-timeout'];
	$allowed['ue_wp-forecast-pre-transport'] = $_POST['ue_wp-forecast-pre-transport'];
	$allowed['ue_wp-forecast-delopt']        = $_POST['ue_wp-forecast-delopt'];	

	$allowed['ue_service']    = $_POST['ue_service'];
	$allowed['ue_apikey1']    = $_POST['ue_apikey1'];
	$allowed['ue_location']   = $_POST['ue_location'];
	$allowed['ue_locname']    = $_POST['ue_locname'];
	$allowed['ue_refresh']    = $_POST['ue_refresh'];
	$allowed['ue_metric']     = $_POST['ue_metric'];
	$allowed['ue_currtime']   = $_POST['ue_currtime'];
	$allowed['ue_timeoffset'] = $_POST['ue_currtime'];
	$allowed['ue_windunit']   = $_POST['ue_windunit'];
	$allowed['ue_wpf_language'] = $_POST['ue_wpf_language'];
	$allowed['ue_pdforecast'] = $_POST['ue_pdforecast'];	
	$allowed['ue_pdfirstday'] = $_POST['ue_pdfirstday'];	
	$allowed['ue_dispconfig'] = $_POST['ue_dispconfig'];
	$allowed['ue_forecast']   = $_POST['ue_forecast'];
	if ( $allowed['ue_forecast'] == '1' ) {
	    $allowed['ue_daytime']='1';
	    $allowed['ue_nighttime']='1';
	} else {
	    $allowed['ue_daytime']='';
	    $allowed['ue_nighttime']='';
	}

	$defaults['wp-forecast-count'] = $_POST['wp-forecast-count'];	
	$defaults['wp-forecast-timeout']       = $_POST['wp-forecast-timeout'];	
	$defaults['wp-forecast-pre-transport'] = $_POST['wp-forecast-pre-transport'];
	$defaults['wp-forecast-delopt']= $_POST['wp-forecast-delopt'];

	$defaults['service']       = $_POST['service'];
	$defaults['location']      = $_POST['location'];	
	$defaults['locname']       = $_POST['locname'];	
	$defaults['refresh']       = $_POST['refresh'];
	$defaults['apikey1']       = $_POST['apikey1'];
	$defaults['metric']        = $_POST['metric'];
	$defaults['currtime']      = $_POST['currtime'];	
	$defaults['timeoffset']    = $_POST['timeoffset'];	
	$defaults['windunit']      = $_POST['windunit'];
	$defaults['wpf_language']  = $_POST['wpf_language'];
	$defaults['pdforecast']    = $_POST['pdforecast'];
	$defaults['pdfirstday']    = $_POST['pdfirstday'];
	

        // set empty checkboxes to 0
	$do = array('d_c_icon','d_c_time','d_c_short','d_c_temp','d_c_real','d_c_press',
		    'd_c_humid','d_c_wind','d_c_sunrise','d_c_sunset','d_d_icon','d_d_short',
		    'd_d_temp','d_d_wind','d_n_icon','d_n_short','d_n_temp','d_n_wind','d_c_date',
		    'd_d_date','d_n_date','d_c_copyright','d_c_wgusts','d_d_wgusts','d_n_wgusts',
		    'd_c_accuweather','d_c_aw_newwindow');

	foreach ($do as $i) {
	    if ($_POST[$i]=="")
		$_POST["$i"]="0";
	}
	
    

	// build config string for dispconfig and update if necessary
	$newdispconfig="";
	foreach ($do as $i) 
	    $newdispconfig.=$_POST[$i];
	
	$defaults['dispconfig'] =  $newdispconfig;
	
	// for forecast options
	$nd = array('day1','day2','day3','day4','day5','day6','day7','day8','day9',
		    'night1','night2','night3','night4','night5','night6','night7','night8','night9');
	foreach ($nd as $i) {
	    if ($_POST[$i]=="")
		$_POST["$i"]="0";
	}

	
	// build config string for forecast and update if necessary
	$newdaytime=$_POST["day1"].$_POST["day2"].$_POST["day3"].$_POST["day4"].$_POST["day5"].$_POST["day6"].
	    $_POST["day7"].$_POST["day8"].$_POST["day9"];
	
	$defaults['daytime'] = $newdaytime;
	
	// build config string for forecast and update if necessary
	$newnighttime=$_POST["night1"].$_POST["night2"].$_POST["night3"].$_POST["night4"].$_POST["night5"].
	    $_POST["night6"].$_POST["night7"].$_POST["night8"].$_POST["night9"];
	
	$defaults['nighttime'] = $newnighttime; 
	
	// put message after 'pdate
	echo"<div class='updated'><p><strong>";
	update_option("wpf_sa_defaults",serialize($defaults));
	update_option("wpf_sa_allowed",serialize($allowed));
	extract($allowed);
	extract($defaults);
	$av=$defaults;
	echo __('Settings successfully updated',"wp-forecast_".$locale);
	echo "</strong></p></div>";
    }
  
  
    
    // --- form starts here ----------------------------------------------------------------------
    
    // print out number of widgets selection
    $out = "";
    $out .= "<div class='wrap'>";
    $out .= "<h2>WPMU-Admin-Settings for WP-Forecast</h2>";
    
    $out .= "<span class='wpfbcg'><b>" . __("Check the colored checkboxes to user enable the options","wp-forecast_".$locale) . "</b></span>";
    $out .= "<form name='options' id='options' method='post' action='#'>";
    
    $out .= "<table><tr><td><span class='wpfbcg'><input type='checkbox' name='ue_wp-forecast-count' value='1'";
    if ($allowed['ue_wp-forecast-count']=="1") $out .= "checked='checked'";
    $out .= "/></span>".__('Maximal number of widgets per user',"wp-forecast_".$locale)."</td>";
    $out .= "<td><input id='wp-forecast-count' name='wp-forecast-count' type='text' size='2' maxlength='2' value='".$defaults['wp-forecast-count']. "' /></td></tr>";
    
    
    // print out timeout input field for transport
    // (timeout for data connection)
    $out .= "<tr><td><span class='wpfbcg'><input type='checkbox' name='ue_wp-forecast-timeout' value='1'";
    if ($allowed['ue_wp-forecast-timeout'] == "1") $out .= "checked='checked'";
    $out .= "/></span>".__('Timeout for weatherprovider connections (secs.)?',"wp-forecast_".$locale)."</td>";
    $out .= "<td><input id='wp-forecast-timeout' name='wp-forecast-timeout' type='text' size='3' maxlength='3' value='".$defaults['wp-forecast-timeout']. "' />";
    $out .= "</td></tr>";
    
    
    // show transport method selection 
    $out .= "<tr><td><span class='wpfbcg'><input type='checkbox' name='ue_wp-forecast-pre-transport' value='1'";
    if ($allowed['ue_wp-forecast-pre-transport'] == "1") $out .= "checked='checked'";
    $out .= "/></span>".__('Preselect wordpress transfer method',"wp-forecast_".$locale)." :</td>";
    $out .= "<td><select name='wp-forecast-pre-transport' id='wp-forecast-pre-transport' size='1' >";
    $out .= "<option value='default'>". __("default","wp-forecast".$locale)."</option>";
    
    // get wordpress default transports
    $tlist = get_wp_transports();
    foreach($tlist as $t)
    {
	$out .= "<option value='$t'" . ($t == $defaults['wp-forecast-pre-transport'] ? 'selected="selected"':'')  . ">$t</option>";
    }
    $out .= "</select></td></tr>";
    
    // print out option deletion switch
    $out .= "<tr><td ><span class='wpfbcg'><input type='checkbox' name='ue_wp-forecast-delopt' value='1'";
    if ($allowed['ue_wp-forecast-delopt'] == "1") $out .= "checked='checked'";
    $out .= "/></span>".__('Delete options during plugin deactivation?',"wp-forecast_".$locale)."</td>";
    $out .= "<td><input id='wp-forecast-delopt' name='wp-forecast-delopt' type='checkbox' ";
    if ($defaults['wp-forecast-delopt'])
	$out .= 'checked="checked"';
    $out .= " />";
    $out .= "</td></tr></table>\n"; 

    // button to delete superadmin options
    $out .= '<div style="text-align:right;"><label for="delete_sa_opts">'.__('Delete SuperAdmin options',"wp-forecast_".$locale).':</label><input name="delete_sa_opts" id="delete_sa_opts" type="checkbox" value="1"  />';
    $out .= '&nbsp;&nbsp;&nbsp;<input type="submit" name="del_sa_opts" value="'.__('Delete SA-Options','wp-forecast_'.$locale).' &raquo;" /></div>'."\n";

    
    echo $out;
	echo '<div class="wrap">';
    ?>
    <!-- add javascript for field control -->
    <?php include("wpf_sa_js.php"); ?>
	 <input name='wid' type='hidden' value='<?php echo $wpfcid; ?>'/>   
	 <h2><?php echo __('Widget-Setup',"wp-forecast_".$locale);?></h2>
	  <?php if ($widgetcall == 0): ?><fieldset id="set1"><?php endif; ?>
	 <div style="float: left; width: 49%">
	 <p><b><span class='wpfbcg'><input type='checkbox' name='ue_service' value='1' <?php echo ($ue_service=="1" ? "checked='checked'":""); ?> /></span><?php echo __('Weatherservice',"wp-forecast_".$locale)?>:</b>
         <select name="service" size="1" onchange="apifields(document.options.service.value);">
	   <option value="accu" <?php if ($av['service']=="accu") echo "selected=\"selected\""?>><?php echo __('AccuWeather',"wp-forecast_".$locale)?></option>
           <option value="bug" <?php if ($av['service']=="bug") echo "selected=\"selected\""?>><?php echo __('WeatherBug',"wp-forecast_".$locale)?></option>
	 </select></p>

         <p><b><span class='wpfbcg'><input type='checkbox' name='ue_apikey1' value='1' <?php echo ($ue_apikey1=="1" ? "checked='checked'":""); ?> /></span><?php echo __('Partner-ID',"wp-forecast_".$locale)?>:</b>
         <input name="apikey1" type="text" size="30" maxlength="80" value="<?php echo $av['apikey1'] ?>" /></p>
         <!--
         <p><?php echo __('Licensekey',"wp-forecast_".$locale)?>:
         <input name="apikey2" type="text" size="30" maxlength="80" value="<?php echo $av['apikey2'] ?>" /></p>
         -->
         <script type="text/javascript">apifields(document.options.service.value);</script>

	 <p><b><span class='wpfbcg'><input type='checkbox' name='ue_location' value='1' <?php echo ($ue_location=="1" ? "checked='checked'":""); ?> /></span><?php echo __('Location',"wp-forecast_".$locale)?>:</b>
	 <input name="location" type="text" size="30" maxlength="80" value="<?php echo $av['location'] ?>"<?php if ($widgetcall==1) echo "readonly" ?> /></p>
	 <?php if (isset($_POST['set_loc'])) { ?>
	       <p><b><?php echo __('Press Update options to save new location.',"wp-forecast_".$locale)?></b></p>
         <?php } ?>
         
         <p><b><span class='wpfbcg'><input type='checkbox' name='ue_locname' value='1' <?php echo ($ue_locname=="1" ? "checked='checked'":""); ?>/></span><?php echo __('Locationname',"wp-forecast_".$locale)?>:</b>
         <input name="locname" type="text" size="30" maxlength="80" value="<?php echo $av['locname'] ?>" /></p>

	 <p><b><span class='wpfbcg'><input type='checkbox' name='ue_refresh' value='1' <?php echo ($ue_refresh=="1" ? "checked='checked'":""); ?>/></span><?php echo __('Refresh cache after',"wp-forecast_".$locale)?></b>
         <input name="refresh" type="text" size="10" maxlength="6" value="<?php echo $av['refresh'] ?>"/>
         <b><?php echo __('secs.',"wp-forecast_".$locale)?></b><br /></p>
	 <p><b><span class='wpfbcg'><input type='checkbox' name='ue_metric' value='1' <?php echo ($ue_metric=="1" ? "checked='checked'":""); ?>/></span><?php echo __('Use metric units',"wp-forecast_".$locale)?>: </b><input type="checkbox" name="metric" value="1" <?php if ($av['metric']=="1") echo "checked=\"checked\""?> /> 
	 </p>

	 <p><b><span class='wpfbcg'><input type='checkbox' name='ue_currtime' value='1' <?php echo ($ue_currtime=="1" ? "checked='checked'":""); ?>/></span><?php echo __('Use current time',"wp-forecast_".$locale)?>: </b><input type="checkbox" name="currtime" value="1" <?php if ($av['currtime']=="1") echo "checked=\"checked\""?> /> 
												 / <b><?php echo __('Time-Offset',"wp-forecast_".$locale)?> :</b> <input type="text" name="timeoffset" size="5" maxlength="5" value="<?php echo $av['timeoffset'] ?>" /> <b><?php echo __('minutes',"wp-forecast_".$locale);?></b> 
         </p>

         <p><b><span class='wpfbcg'><input type='checkbox' name='ue_windunit' value='1' <?php echo ($ue_windunit=="1" ? "checked='checked'":""); ?>/></span><?php echo __('Windspeed-Unit',"wp-forecast_".$locale)?>: </b><select name="windunit" size="1">
	      <option value="ms" <?php if ($av['windunit']=="ms") echo "selected=\"selected\""?>><?php echo __('Meter/Second (m/s)',"wp-forecast_".$locale)?></option>
              <option value="kmh" <?php if ($av['windunit']=="kmh") echo "selected=\"selected\""?>><?php echo __('Kilometer/Hour (km/h)',"wp-forecast_".$locale)?></option>
              <option value="mph" <?php if ($av['windunit']=="mph") echo "selected=\"selected\""?>><?php echo __('Miles/Hour (mph)',"wp-forecast_".$locale)?></option>
              <option value="kts" <?php if ($av['windunit']=="kts") echo "selected=\"selected\""?>><?php echo __('Knots (kts)',"wp-forecast_".$locale)?></option>
              <option value="bft" <?php if ($av['windunit']=="bft") echo "selected=\"selected\""?>><?php echo __('Beaufort (bft)',"wp-forecast_".$locale)?></option>
	 </select></p>


        

	 <p>
         <b><span class='wpfbcg'><input type='checkbox' name='ue_wpf_language' value='1' <?php echo ($ue_wpf_language=="1" ? "checked='checked'":""); ?>/></span><?php echo __('Language',"wp-forecast_".$locale)?>: </b><select name="wpf_language" size="1">
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
 
	 <p><b><span class='wpfbcg'><input type='checkbox' name='ue_pdforecast' value='1' <?php echo ($ue_pdforecast=="1" ? "checked='checked'":""); ?>/></span><?php echo __('Show forecast as ajax pull-down',"wp-forecast_".$locale)?>: </b><input type="checkbox" id="pdforecast" name="pdforecast" value="1" <?php if ($av['pdforecast']=="1") echo "checked=\"checked\""?> onchange="pdfields_update();" /> 
         </p>

         <p>
         <b><span class='wpfbcg'><input type='checkbox' name='ue_pdfirstday' value='1' <?php echo ($ue_pdfirstday=="1" ? "checked='checked'":""); ?>/></span><?php echo __('First day in pull-down',"wp-forecast_".$locale)?>: </b><select id="pdfirstday" name="pdfirstday" size="1">
   <option value="0" <?php if ($av['pdfirstday']=="0") echo "selected=\"selected\""?>>0</option>
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
       <b><span class='wpfbcg'><input type='checkbox' name='ue_dispconfig' value='1' <?php echo ($ue_dispconfig=="1" ? "checked='checked'":""); ?>/></span>
        <?php echo __('Display Configuration',"wp-forecast_".$locale)?></b>
        <table>
	<tr>
         <td>&nbsp;</td>
         <td><?php echo __('Current Conditions',"wp-forecast_".$locale)?></td>
         <td><?php echo __('Forecast Day',"wp-forecast_".$locale)?></td>
         <td><?php echo __('Forecast Night',"wp-forecast_".$locale)?></td>
        </tr>
        <tr>
        <td><?php echo __('Icon',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_icon" value="1" 
		 <?php if (substr($av['dispconfig'],0,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'><input type="checkbox" name="d_d_icon" value="1" 
		 <?php if (substr($av['dispconfig'],10,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'><input type="checkbox" name="d_n_icon" value="1" 
		 <?php if (substr($av['dispconfig'],14,1)=="1") echo "checked=\"checked\""?> /></td>
         </tr>
          <tr>
         <td><?php echo __('Date',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_date" value="1" 
		 <?php if (substr($av['dispconfig'],18,1)=="1") echo "checked=\"checked\""?> /></td>
         <td class='td-center'>&nbsp;</td>
         <td class='td-center'>&nbsp;</td>
         </tr>
	 <tr>
         <td><?php echo __('Time',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_time" value="1" 
		 <?php if (substr($av['dispconfig'],1,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'>&nbsp;</td>
        <td class='td-center'>&nbsp;</td>
        </tr> 
        <tr>
        <td><?php echo __('Short Description',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_short" value="1" 
	     <?php if (substr($av['dispconfig'],2,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'><input type="checkbox" name="d_d_short" value="1" 
	     <?php if (substr($av['dispconfig'],11,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'><input type="checkbox" name="d_n_short" value="1" 
	     <?php if (substr($av['dispconfig'],15,1)=="1") echo "checked=\"checked\""?> /></td>
        </tr> 
        <tr>
        <td><?php echo __('Temperature',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_temp" value="1" 
	     <?php if (substr($av['dispconfig'],3,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'><input type="checkbox" name="d_d_temp" value="1" 
	     <?php if (substr($av['dispconfig'],12,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'><input type="checkbox" name="d_n_temp" value="1" 
	     <?php if (substr($av['dispconfig'],16,1)=="1") echo "checked=\"checked\""?> /></td>
        </tr> 
        <tr>
        <td><?php echo __('Realfeel',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_real" value="1" 
	     <?php if (substr($av['dispconfig'],4,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'>&nbsp;</td>
        <td class='td-center'>&nbsp;</td>
        </tr> 
        <tr>
        <td><?php echo __('Pressure',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_press" value="1" 
	     <?php if (substr($av['dispconfig'],5,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'>&nbsp;</td>
        <td class='td-center'>&nbsp;</td>
        </tr> 
        <tr>
        <td><?php echo __('Humidity',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_humid" value="1" 
	     <?php if (substr($av['dispconfig'],6,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'>&nbsp;</td>
        <td class='td-center'>&nbsp;</td>
        </tr> 
        <tr>
        <td><?php echo __('Wind',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_wind" value="1" 
	     <?php if (substr($av['dispconfig'],7,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'><input type="checkbox" name="d_d_wind" value="1" 
	     <?php if (substr($av['dispconfig'],13,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'><input type="checkbox" name="d_n_wind" value="1" 
  	    <?php if (substr($av['dispconfig'],17,1)=="1") echo "checked=\"checked\""?> /></td>
        </tr> 
	<tr>
        <td><?php echo __('Windgusts',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_wgusts" value="1" 
	     <?php if (substr($av['dispconfig'],22,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'><input type="checkbox" name="d_d_wgusts" value="1" 
	     <?php if (substr($av['dispconfig'],23,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'><input type="checkbox" name="d_n_wgusts" value="1" 
  	     <?php if (substr($av['dispconfig'],24,1)=="1") echo "checked=\"checked\""?> /></td>
        </tr>         
	<tr>
        <td><?php echo __('Sunrise',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_sunrise" value="1" 
		 <?php if (substr($av['dispconfig'],8,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'>&nbsp;</td>
        <td class='td-center'>&nbsp;</td>
        </tr> 
        <tr>
        <td><?php echo __('Sunset',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_sunset" value="1" 
		 <?php if (substr($av['dispconfig'],9,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'>&nbsp;</td>
        <td class='td-center'>&nbsp;</td>
        </tr>
        <tr>
        <td><?php echo __('Copyright',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_copyright" value="1" 
		 <?php if (substr($av['dispconfig'],21,1)=="1") echo "checked=\"checked\""?> /></td>
        <td class='td-center'>&nbsp;</td>
        <td class='td-center'>&nbsp;</td>
        </tr> 
	<tr>
        <td><?php echo __('Link to Weatherprovider',"wp-forecast_".$locale)?></td>
        <td class='td-center'><input type="checkbox" name="d_c_accuweather" id="d_c_accuweather" value="1" 
	    <?php if (substr($av['dispconfig'],25,1)=="1") echo "checked=\"checked\""?> onchange="nwfields_update();" /></td>
            <td colspan="2" >(<?php echo __('Open in new Window',"wp-forecast_".$locale)?>: 
            <input type="checkbox" name="d_c_aw_newwindow" id="d_c_aw_newwindow" value="1" 
 	    <?php if (substr($av['dispconfig'],26,1)=="1") echo "checked=\"checked\""?> />)</td>
        </tr> 
        </table>
	<br /> 
        <script type="text/javascript">nwfields_update();</script>

<b><span class='wpfbcg'><input type='checkbox' name='ue_forecast' value='1' <?php echo ($ue_forecast=="1" ? "checked='checked'":""); ?>/></span><?php echo __('Forecast',"wp-forecast_".$locale)?></b>
         					      
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
             <td><input type="checkbox" name="alldays" onclick="this.value=check('day')" /></td>
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
             <td><input type="checkbox" name="allnight" onclick="this.value=check('night')" /></td>
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
	       apifields(document.options.service.value); 
       </script>
       </fieldset>
<?php
     echo "<div class='submit'><input class='button-primary' type='submit' name='info_update' value='".__('Update options',"wp-forecast_".$locale)." »' /></div>";
     echo "</div></form></div>";
    pdebug(1,"End of wpf_sub_admin_form ()");
}
?>