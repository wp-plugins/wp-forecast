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
	$res='<iframe class="wpf-iframe" src="'.plugins_url( 'wp-forecast-show.php?wpfcid='.$id.'&amp;header=1' , __FILE__ );

	// falls eine sprache angegeben wurde haengen wir sie hinten dran
	if ($lang != '')
	    $res .= '&amp;lang='.$lang;
	
	$res .= '" ';
	
	// falls eine breite angegeben wurde haengen wir sie hinten dran
	if ($width != '')
	    $res .= "width='$width' ";
	
        // falls eine sprache angegeben wurde haengen wir sie hinten dran
	if ($height != '')
	    $res .= "height='$height' ";
	
	$res .='>'.__("wp-forecast shortcode: This browser does not support iframes.","wp-forecast_".$lang).'</iframe>';

	return $res;
    }
}
// shortcode bei wp anmelden
if (function_exists('add_shortcode'))
    add_shortcode('wpforecast','wpforecast');

?>