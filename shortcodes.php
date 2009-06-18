<?php
/**
 * @author Hans Matzen
 * @copyright 2009
 * @since 2.4
 * @description wp-forecast using wordpress shortcode for more features
 * @Docs http://codex.wordpress.org/Shortcode_API
 */

if (!function_exists('wpforecast')) 
{
    function wpforecast($atts)
    {
	// parameter einlesen
	if ( is_array($atts) )
	    extract($atts);

	if ($id == '')
	    $id='A';
	
	// iframe tag zusammen bauen
	$res='<iframe src="'.get_settings('siteurl').
	    '/wp-content/plugins/wp-forecast/wp-forecast-show.php?wpfcid='.
	    $id.'&amp;header=1';

	// falls eine sprache angegeben wurde haengen wir sie hinten dran
	if ($lang != '')
	    $res .= '&amp;lang='.$lang;
	
	$res .='">'.__("wp-forecast shortcode: This browser does not support iframes.","wp-forecast_".$lang).'</iframe>';

	return $res;
    }
}
// shortcode bei wp anmelden
if (function_exists('add_shortcode'))
    add_shortcode('wpforecast','wpforecast');

?>