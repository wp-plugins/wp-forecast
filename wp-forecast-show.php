<?php

/*  Copyright 2006-2013  Hans Matzen  (email : webmaster at tuxlog dot de)

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
// display the weather-data
//
// the following structure will be used:
/*
 <div class="wp-forecast">
<div class="wp-forecast-curr">
<div class="wp-forecast-curr-head">
</div>
<div class="wp-forecast-curr-block">
<div class='wp-forecast-curr-left'>
</div>
<div class='wp-forecast-curr-right'>
</div>
</div>
<div class="wp-forecast-curr-details">
<div class="wp-forecast-copyright">
</div>
</div>
</div>
<div class="wp-forecast-fc">

<div class="wp-forecast-fc-oneday">
<div class="wp-forecast-fc-head">
</div>
<div class="wp-forecast-fc-block">
<div class="wp-forecast-fc-left">
</div>
<div class='wp-forecast-fc-right'>
</div>
</div>
<div class="wp-forecast-fc-block">
<div class="wp-forecast-fc-left">
</div>
<div class='wp-forecast-fc-right'>
</div>
</div>
</div>
... repetead for everey forecast day ...
</div>
</div>
*/

// if called directly, get parameters from GET and output the forecast html
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	require_once("funclib.php");

	//include wp-config or wp-load.php
	$root = dirname(dirname(dirname(dirname(__FILE__))));
	if (file_exists($root.'/wp-load.php')) {			// since WP 2.6
		require_once($root.'/wp-load.php');
	} elseif (file_exists($root.'/wp-config.php')) {	// Before 2.6
		require_once($root.'/wp-config.php');
	}

	$wpfcid = esc_attr($_GET['wpfcid']);
	$language_override = (isset($_GET['language_override'])?esc_attr($_GET['language_override']):"");
	$header = esc_attr($_GET['header']);
	$selector = (isset($_GET['selector'])?esc_attr($_GET['selector']):"");
	if ($selector == "1")
		$selector = "?";
	$args=array();

	$wpf_vars=get_wpf_opts($wpfcid);
	if (!empty($language_override)) {
		$wpf_vars['wpf_language']=$language_override;
	}
	$weather=maybe_unserialize(wpf_get_option("wp-forecast-cache".$wpfcid));


	if ($header) {
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
		echo '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="'. str_replace("_","-",$wpf_vars['wpf_language']) . '">'."\n";
		echo "<head><title>wp-forecast iframe</title>\n";
		echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />'."\n";
	}

	if ($header)
	{
		wp_forecast_css_nowp($wpfcid);
		echo "</head>\n<body>\n";
	}

	show($selector . $wpfcid,$args,$wpf_vars);

	if ($header)
		echo "</body></html>\n";
}

function show($wpfcid,$args,$wpfvars)
{
	pdebug(1,"Start of show ()");

	// check how we are called as a widget or from sidebar
	if (sizeof($args)==0)
		$show_from_widget=0;
	else
		$show_from_widget=1;

	// order is important to override old title in wpfvars with new in args
	extract($wpfvars);
	extract($args);

	// get translations
	if(function_exists('load_plugin_textdomain')) {
		add_filter("plugin_locale","wpf_lplug",10,2);
		load_plugin_textdomain("wp-forecast_".$wpf_language, false, dirname( plugin_basename( __FILE__ ) ) . "/lang/");
		remove_filter("plugin_locale","wpf_lplug",10,2);
	}

	$plugin_path = plugins_url("",__FILE__);

	// show location selection dialog and handle it ajax like
	$fout="";
	$selector = substr($wpfcid,0,1);
	if ($selector == "?")
	{
		$wpfcid = substr($wpfcid,1);
		if ($wpfcid == "")
			$wpfcid = "A";

		$count = wpf_get_option('wp-forecast-count');
		$fout .= "<div class='wpf-selector'><form action='#'>" .
				__("Locations:","wp-forecast_".$wpf_language);
		$fout .= "<select id='wpf_selector' size='1' onchange='wpf_update();' >";
		for ($i=0;$i<$count;$i++) {
	  $id = get_widget_id( $i );
	  $v  = get_wpf_opts($id);
	  $fout .="<option value='".$id."' ";
	  if ($id == $wpfcid)
	  	$fout .= "selected='selected' ";
	  $fout .=">".$v['locname']."</option>";
		}
		$fout .= "</select>";
		$fout .="<input id='wpf_selector_site' type='hidden' value='" .
				$plugin_path."' />";
		$fout .="<input id='wpf_language' type='hidden' value='" . $wpf_language ."' />";
		$fout .= "</form>";
		$fout .='<script type="text/javascript">window.onDomReady(wpf_load);</script>';
		$fout .= "</div>";
			
	}

	$w = wp_forecast_data($wpfcid, $wpf_language);

	// current conditions nur ausgeben, wenn mindestens ein feld aktiv ist
	if ( strpos(substr($dispconfig,0,9),"1") >= 0 or
			substr($dispconfig,18,1) == "1" or
			substr($dispconfig,21,1) == "1" or
			substr($dispconfig,22,1) == "1" )
	{
		// ouput current conditions
		$out ="";
		$out .="\n<div class=\"wp-forecast-curr\">\n";

		// if the provider sends us a failure notice print it and return
		if ( $w['failure'] != "" )
		{
	  $out .= __("Failure notice from provider","wp-forecast_".$wpf_language).":<br />";
	  $out .= $w['failure']."</div>";

	  // print it
	  if ( $show_from_widget == 1 )
	  	echo $before_widget . $before_title . $title . $after_title
	  	. $out . $after_widget;
	  else
	  	echo $out;

	  return false;
		}


		// if error print an error message and return
		if ( count($w)<=0)
		{
	  $out .= __("Sorry, no valid weather data available.","wp-forecast_".$wpf_language)."<br />";
	  $out .= __("Please try again later.","wp-forecast_".$wpf_language)."</div>";
	  // print it
	  if ( $show_from_widget == 1 )
	  	echo $before_widget . $before_title . $title . $after_title .
	  	$out . $after_widget;
	  else
	  	echo $out;

	  return false;
		}

		// ortsnamen ausgeben parameter fuer open in new window berücksichtigen
		$servicelink="";
		$servicelink_end="";
		if (substr($dispconfig,25,1) == "1") {
			$servicelink= '<a href="'.$w['servicelink'].'"';
			$servicelink_end="</a>";

			if (substr($dispconfig,26,1) == "1")
				$servicelink= $servicelink . ' target="_blank" >';
	  else
	  	$servicelink= $servicelink.' >';
		}


		$out .= '<div class="wp-forecast-curr-head">';
		if ( $w['location'] == "" )
			$out .= "<div>".$servicelink.$w['locname']. $servicelink_end."</div>\n";
		else if ( trim($w['location']) !="" and $w['location'] != "&nbsp;")
			$out .= "<div>".$servicelink.$w['location'].$servicelink_end."</div>\n";

		// show date / time
		// if current time should be used
		if ($currtime=="1")
		{
			$cd = $w['blogdate'];
			$ct = $w['blogtime'];
		} else  if ($service=="accu")
		{
			// else take given accuweather time
			$cd = $w['accudate'];
			$ct = $w['accutime'];
		} else  if ($service=="bug")
		{
			// else take given weatherbug time
			$cd = $w['bugdate'];
			$ct = $w['bugtime'];
		} else  if ($service=="google")
		{
			// else take given weatherbug time
			$cd = $w['googledate'];
			$ct = $w['googletime'];
		}

		if (substr($dispconfig,18,1) == "1" or substr($dispconfig,1,1) == "1") {
			$out .= "<div>";
			if (substr($dispconfig,18,1) == "1")
				$out .= $cd;
			else
				$out .= __('time',"wp-forecast_".$wpf_language).": ";

			if (substr($dispconfig,18,1) == "1" and substr($dispconfig,1,1) == "1")
				$out .= ", ";

			if (substr($dispconfig,1,1) == "1")
				$out .= $ct;
			$out .= "</div>\n";
		}
		$out .= "</div>\n";

		$out .= '<div class="wp-forecast-curr-block">';

		// show icon
		if (substr($dispconfig,0,1) == "1") {
			if ($service=="accu") {
				$breite=0; $hoehe=0;
				$isize=getimagesize(plugin_dir_path(__FILE__). "/" . $w['icon'] );
				if ($isize != false) {
					$breite=$isize[0];
					$hoehe=$isize[1];
				}
				$out .= "<div class='wp-forecast-curr-left'><img class='wp-forecast-curr-left' src='" . $plugin_path . "/" . $w['icon']."' alt='".$w['shorttext']. "' width='".$breite."' height='".$hoehe."' /></div>\n";
			}
			if ($service=="bug")
				$out .= "<div class='wp-forecast-curr-left'><img class='wp-forecast-curr-left' src='" . $w['icon']."' alt='".$w['shorttext']."' /></div>\n";

			if ($service=="google")
				$out .= "<div class='wp-forecast-curr-left'><img class='wp-forecast-curr-left' src='" . $w['icon']."' alt='".$w['shorttext']."' /></div>\n";
		}

		$out .= "<div class='wp-forecast-curr-right'>";

		// show short description
		if (substr($dispconfig,2,1) == "1")
			$out .= "<div>". $w["shorttext"]."</div>";

		// show temperatur
		if (substr($dispconfig,3,1) == "1")
			$out .= $w["temperature"];
		//$out .= __('tmp',"wp-forecast_".$wpf_language).": ".$w["temperature"];
		$out .= "</div>\n"; // end of right
		$out .= "</div>\n";  // end of block

		$out .= "<div class=\"wp-forecast-curr-details\">";
		// show realfeel
		if (substr($dispconfig,4,1) == "1")
			$out .= "<div>".__('flik',"wp-forecast_".$wpf_language).": ".$w["realfeel"]."</div>\n";
		// show pressure
		if (substr($dispconfig,5,1) == "1")
			$out .= "<div>".__('barr',"wp-forecast_".$wpf_language).": ".$w["pressure"]."</div>\n";

		// show humiditiy
		if (substr($dispconfig,6,1) == "1")
			//
			// you can change the decimals of humditiy by switching
			// the 0 to whatever you need
		//
		$out .= "<div>".__('hmid',"wp-forecast_".$wpf_language).": ".$w["humidity"]."%</div>\n";

		// show wind
		if (substr($dispconfig,7,1) == "1")
			$out .= "<div>".__('winds',"wp-forecast_".$wpf_language).": ".$w['windspeed']." " . $w['winddir']."</div>\n";


		// show windgusts
		if (substr($dispconfig,22,1) == "1")
			$out .= "<div>".__('Windgusts',"wp-forecast_".$wpf_language).": ".$w['windgusts']."</div>\n";


		// show sunrise
		if (substr($dispconfig,8,1) == "1")
			$out .="<div>". __('sunrise',"wp-forecast_".$wpf_language).": ".$w['sunrise']."</div>\n";

		// show sunset
		if (substr($dispconfig,9,1) == "1")
			$out .= "<div>".__('sunset',"wp-forecast_".$wpf_language).": ".$w['sunset']."</div>\n";

		// show copyright
		if (substr($dispconfig,21,1) == "1")
			$out .= "<div class=\"wp-forecast-copyright\">".$w['copyright']."</div>";

		$out .="</div>\n"; // end of details
		$out .="</div>\n"; // end of curr


	}


	// ------------------
	//
	// output forecast
	//
	// -------------------
	// calc max forecast days depending on provider
	switch ($service)
	{
		case "accu":
			$maxdays=9; break;
		case "bug":
			$maxdays=6;  break;
		case "com":
			// to be done
		case "google":
			$maxdays=4; break;
	}

	$out1 = "<div class=\"wp-forecast-fc\">\n";
	$out2 = "";
	for ($i = 1; $i <= $maxdays; $i++)
	{
		// check active forecast for day number i
		if (substr($daytime,$i-1,1)=="1" or substr($nighttime,$i-1,1) =="1")
		{
	  $out1 .="<div class=\"wp-forecast-fc-oneday\">\n";

	  $out1 .="<div class=\"wp-forecast-fc-head\">";
	  $out1 .= __("Forecast","wp-forecast_".$wpf_language)." ";
	  $out1 .= $w['fc_obsdate_'.$i]."</div>\n";
		}

		// check for daytime information
		if (substr($daytime,$i-1,1)=="1" )
		{
	  $out1 .="<div class=\"wp-forecast-fc-block\">\n";
	  $out1 .="<div class=\"wp-forecast-fc-left\">\n";
	  $out1 .= "<div>".__('day',"wp-forecast_".$wpf_language)."</div>\n";

	  // show icon
	  if (substr($dispconfig,10,1) == "1")
	  {
	  	if ($service=="accu") {
	  		$breite=0; $hoehe=0;
	  		$isize=getimagesize(plugin_dir_path(__FILE__). "/" . $w['fc_dt_icon_'.$i] );
	  		if ($isize != false) {
	  			$breite=$isize[0];
	  			$hoehe=$isize[1];
	  		}
	  		$out1 .= "<img class='wp-forecast-fc-left' src='".$plugin_path."/". $w['fc_dt_icon_'.$i]."' alt='".
	 	  		__($w["fc_dt_iconcode_".$i],"wp-forecast_".$wpf_language). "' width='$breite' height='$hoehe' />";
	  	}
		  if ($service=="bug")
		  	$out1 .= "<img class='wp-forecast-fc-left' src='".$w['fc_dt_icon_'.$i].
		  	"' alt='".$w["fc_dt_desc_".$i]."' />";

		  if ($service=="google")
		  	$out1 .= "<img class='wp-forecast-fc-left' src='".$w['fc_dt_icon_'.$i].
		  	"' alt='".$w["fc_dt_desc_".$i]."' />";
	  }
	  else
	  {
	  	$out1 .= "&nbsp;";
	  }
	  $out1 .= "\n</div>\n"; // end of wp-forecast-fc-left
	  $out1 .= "<div class='wp-forecast-fc-right'>";

	  // show short description
	  if (substr($dispconfig,11,1) == "1")
	  	$out1 .= "<div>".$w["fc_dt_desc_".$i]."</div>";

	  // show temperature
	  if (substr($dispconfig,12,1) == "1")
	  {
	  	$out1 .= "<div>";
	  	$out1 .= ($service=="bug" ? $w["fc_dt_ltemp_".$i]." - ":"");
	  	$out1 .= $w["fc_dt_htemp_".$i]. "</div>";
	  }

	  // show wind
	  if (substr($dispconfig,13,1) == "1")
	  	$out1 .= "<div>".__('winds',"wp-forecast_".$wpf_language).": ".
	  	$w["fc_dt_windspeed_".$i]." ".$w["fc_dt_winddir_".$i]."</div>";

	  // show windgusts
	  if (substr($dispconfig,23,1) == "1")
	  	$out1 .= "<div>".__('Windgusts',"wp-forecast_".$wpf_language).": ".
	  	$w["fc_dt_wgusts_".$i]."</div>\n";

	  $out1 .= "</div></div>\n"; // end of wp-forecast-fc-right / block
		}


		// check for nighttime information
		if (substr($nighttime,$i-1,1)=="1" and $service != "bug")
		{
			$out1 .="<div class=\"wp-forecast-fc-block\">\n";
			$out1 .="<div class=\"wp-forecast-fc-left\">\n";
			$out1 .= "<div>". __('night',"wp-forecast_".$wpf_language)."</div>\n";
			if (substr($dispconfig,14,1) == "1")
	  {
	  	$iconfile=find_icon($w["fc_nt_icon_".$i]);
	  	$breite=64; $hoehe=40;
	  	$isize=getimagesize(plugin_dir_path(__FILE__). "/" . $w['fc_nt_icon_'.$i] );
	  	if ($isize != false) {
	  		$breite=$isize[0];
	  		$hoehe=$isize[1];
	  	}
	  	$out1 .= "<img class='wp-forecast-fc-left' src='" .$plugin_path."/".$w['fc_nt_icon_'.$i]."' alt='"
	  	.__($w["fc_nt_iconcode_".$i],"wp-forecast_".$wpf_language)."' width='$breite' height='$hoehe' />";
	  }
	  else
	  {
	  	$out1 .= "&nbsp;";
	  }
	  $out1 .= "\n</div>\n<div class='wp-forecast-fc-right'>";

	  // show short description
	  if (substr($dispconfig,15,1) == "1")
	  	$out1 .= "<div>".$w["fc_nt_desc_".$i]."</div>";

	  // show temperature
	  if (substr($dispconfig,16,1) == "1")
	  	$out1 .= "<div>".$w["fc_nt_ltemp_".$i]."</div>";


	  // show wind
	  if (substr($dispconfig,17,1) == "1")
	  	$out1 .= "<div>".__('winds',"wp-forecast_".$wpf_language)
	  	.": ".$w["fc_nt_windspeed_".$i]." "
	  	. $w["fc_nt_winddir_".$i]  ."</div>";


	  // show windgusts
	  if (substr($dispconfig,24,1) == "1")
	  	$out1 .= "<div>".__('Windgusts',"wp-forecast_".$wpf_language)
	  	.": ".$w["fc_nt_wgusts_".$i]."</div>\n";


	  $out1 .= "</div></div>\n"; // end of wp-forecast-fc-right / block
		}



		// close div block
		if (substr($daytime,$i-1,1)=="1" or substr($nighttime,$i-1,1) =="1")
			$out1 .="</div>\n";

		// store first shown forecast in case pulldown is active
		if ( $pdforecast == 1 and $pdfirstday == $i ) {
			$out2 = $out1 . "</div>\n";
		}
	}

	$out1 .= "</div>\n"; // end of wp-forecast-fc

	//
	// wrap a div around for pulldown and switch off complete forecast
	//
	// mark ids: wpfbl wpfc1 wpfc2 wpfbm with widget id have disjunct ods when using more than one pulldown widget
	if ( $pdforecast == 1 ) {
		$out1 .= "<div class='wpff_nav' id='wpfbl1".$wpfcid."' onclick=\"document.getElementById('wpfc1".$wpfcid."').style.display='none';document.getElementById('wpfc2".$wpfcid."').style.display='block';return false;\">" . __("Less forecast...","wp-forecast_" . $wpf_language) . "</div>\n";
		$out1 = "<div class='wpff_nav' id='wpfbl2".$wpfcid."' onclick=\"document.getElementById('wpfc1".$wpfcid."').style.display='none';document.getElementById('wpfc2".$wpfcid."').style.display='block';return false;\">" . __("Less forecast...","wp-forecast_" . $wpf_language) . "</div>\n" . $out1;
		$out2 .= "<div class='wpff_nav' id='wpfbm".$wpfcid."' onclick=\"document.getElementById('wpfc2".$wpfcid."').style.display='none';document.getElementById('wpfc1".$wpfcid."').style.display='block';return false;\">" . __("More forecast...","wp-forecast_" . $wpf_language) . "</div>\n";

		$out1 = '<div id="wpfc1'.$wpfcid.'"  style="display:none;">' . $out1 . "</div>\n";
		$out2 = '<div id="wpfc2'.$wpfcid.'"  style="display:block;">' . $out2 . "</div>\n";
	}


	// print it
	if ( $show_from_widget == 1 )
		echo $before_widget . $before_title . $title . $after_title;

	echo '<div id="wp-forecast'.$wpfcid.'" class="wp-forecast">' . $fout . $out . $out1 . $out2 . '</div>'."\n";
	// to come back to theme floating status
	echo '<div style="clear:inherit;">&nbsp;</div>';

	if ( $show_from_widget == 1 )
		echo $after_widget;

	pdebug(1,"End of show ()");

}
?>