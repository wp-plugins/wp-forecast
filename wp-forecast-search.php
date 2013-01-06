<?php 

// include wordpress stuff
//include wp-config or wp-load.php
$root = dirname(dirname(dirname(dirname(__FILE__))));
if (file_exists($root.'/wp-load.php')) {				// since WP 2.6
	require_once($root.'/wp-load.php');
} elseif (file_exists($root.'/wp-config.php')) {		// Before 2.6
	require_once($root.'/wp-config.php');
} 
require_once("funclib.php");
require_once("func_accu.php");
require_once("func_bug.php");

$wpfcid="A";
if (isset($_GET['wpfcid'])) 
	$wpfcid=$_GET["wpfcid"];


$wpf_vars=$av=get_wpf_opts($wpfcid);
$locale = $wpf_vars['wpf_language'];

// get translations 
if(function_exists('load_plugin_textdomain')) {
  	add_filter("plugin_locale","wpf_lplug",10,2);
   	load_plugin_textdomain("wp-forecast_".$locale, false, dirname( plugin_basename( __FILE__ ) ) . "/lang/");
   	remove_filter("plugin_locale","wpf_lplug",10,2);
  }

$search_url = plugins_url("/wp-forecast-search.php",__FILE__) ;
$post_url = site_url("wp-admin/admin.php?page=wp-forecast-admin.php");

if (isset($_GET['searchterm'])) {

	echo "<h2>". __("Searchresults","wp-forecast_".$locale) . ":</h2>";
	if ($av['apikey1']=="") {
		echo "<p>" . __("No WeatherBug Partner-ID given. WeatherBug will not be searched.","wp-forecast_".$locale) . "<br/>";
		echo __("Enter a correct Partner-ID in the admin panel and save it to search WeatherBug.","wp-forecast_".$locale) . "</p>";
	}
	if (trim($_GET['searchterm']) == "") {
		echo "<p>" . __("Please enter a non empty searchstring","wp-forecast_".$locale) . "</p>";
		exit;
	} else
		echo "<p>" . __("Please select a location","wp-forecast_".$locale) . ":</p>";
	
    // search locations for accuweather
    $xml=get_loclist($av['ACCU_LOC_URI'],$_GET['searchterm']);
    $xml=utf8_encode($xml);
    accu_get_locations($xml); // modifies global array $loc
    $accu_loc = $loc;
    
    // reset $loc list of hits
    $loc=array();
    $i = 0;
    
    // search location for weather bug
    if ($av['apikey1']!="") {
    	$blu=str_replace('#apicode#',$av['apikey1'],$av['BUG_LOC_URI']);
    	$xml=get_loclist($blu,$_GET['searchterm']);
    	$xml=utf8_encode($xml);
    	bug_get_locations($xml); // modifies global array $loc
    }
	$bug_loc = $loc;
    
	// output searchresults
?>
	<table border="1" >
  	<thead>
    	<tr>
      		<th><?php _e('Accuweather Hits',"wp-forecast_".$locale); ?></th>
      		<th><?php _e('WeatherBug Hits',"wp-forecast_".$locale); ?></th>
    	</tr>
  	</thead>
  	<tbody>
<?php 
	$k = max(count($accu_loc),count($bug_loc));
	$i = 0; 
	while ($i < $k) {
		echo "<tr>";
		
		if ($i < count($accu_loc)) {			 
			echo "<td>" . '<a href="#" onclick="wpf_set_loc(\''.$wpfcid.'\',\'accu\',\''.$accu_loc[$i]['location'].'\');" >'; 
			echo $accu_loc[$i]['city'] . " ," . $accu_loc[$i]['state'] . " </a></td>";
		} else
			echo "<td>&nbsp;-&nbsp;</td>";
		
		if (count($bug_loc) > 0) {	
			echo "<td>" . '<a href="#" onclick="wpf_set_loc(\''.$wpfcid.'\',\'bug\',\''.$bug_loc[$i]['location'].'\');" >'; 
			echo $bug_loc[$i]['city'] . " ," . $bug_loc[$i]['state'] . " </td>";
		} else
			echo "<td>&nbsp;-&nbsp;</td>";
	
			echo "</tr>";
		$i++;		
	}
?>
	</tbody></table>
<?php 
	// stop here
	exit;
}
?>

<script type="text/javascript"> 

var siteuri    = document.getElementById("wpf_search_site").value; 
var posturi    = document.getElementById("wpf_post_site").value;

/* get the data for the new location */
function wpf_search()
{
    var searchterm = document.getElementById("searchloc").value;
    //var siteuri    = document.getElementById("wpf_search_site").value;
    var language   = document.getElementById("wpf_search_language").value;
    var wid        = document.getElementById("wpfcid").value;  

    jQuery.get(siteuri, 
	       { searchterm: searchterm, language: language, wpfcid: wid },
	       function(data){
		   jQuery("div#search_results").html(data);
	       });
}

function wpf_set_loc(w,p,l) { 

	params = { set_loc: 'set_loc', provider: p, new_loc: l, widgetid: w, wid: w };
	
	var form = document.createElement("form");
	form.setAttribute("method", "post");
	form.setAttribute("action", posturi);

	for(var key in params) {
		var hiddenField = document.createElement("input");
	    hiddenField.setAttribute("type", "hidden");
	    hiddenField.setAttribute("name", key);
	    hiddenField.setAttribute("value", params[key]);

	    form.appendChild(hiddenField);
	}

	document.body.appendChild(form);
	form.submit();	
}
</script>

<div class='wpf-search'>
<form action='#' onsubmit="wpf_search(); return false;">
<fieldset id="set2">
<h3><?php _e('Search location',"wp-forecast_".$locale) ?></h3><br />
     
<p><b><?php _e('Searchterm',"wp-forecast_".$locale);?>:</b>
<input id="searchloc" type="text" size="30" maxlength="30" /><br /></p>
<p><?php _e('Please replace german Umlaute ä,ö,ü with a, o, u in your searchterm.',"wp-forecast_".$locale);?></p>
<p><?php _e('Google does not support searching for locations. Only Accuweather and WeatherBug will be searched.',"wp-forecast_".$locale);?></p>
</fieldset>
<div class='submit'>
<a href="#" class='button-primary' style="color:#ffffff;" onclick='javascript:wpf_search();' id='search_loc'><?php _e('Search location',"wp-forecast_".$locale);?> »</a>
<input id='wpf_search_site' type='hidden' value='<?php echo $search_url?>' />
<input id='wpf_post_site' type='hidden' value='<?php echo $post_url?>' />
<input id='wpf_search_language' type='hidden' value='<?php echo $locale;?>' />
<input id='wpfcid' type='hidden' value='<?php echo $wpfcid;?>' />
</div>
</form>
</div>
<hr /> 
<div id="search_results"></div>