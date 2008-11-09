=== wp-forecast ===
Tags: weather,forecast,widget
Requires at least: 2.2
Tested up to: 2.6
Stable tag: 2.1

wp-forecast is a highly customizable plugin for wordpress, showing weather-data from accuweather.com.

== Description ==
/*
Plugin Name: wp-forecast
Plugin URI: http://www.tuxlog.de
Description:  wp-forecast is a highly customizable plugin for wordpress, 
	      showing weather-data from accuweather.com.
Version: 2.1
Author: Hans Matzen <webmaster at tuxlog dot de>
Author URI: http://www.tuxlog.de
*/

/*  Copyright 2006-2008  Hans Matzen  (email : webmaster at tuxlog dot de)

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

you are reading the readme.txt file for the wp-forecast plugin.
wp-forecast is a plugin for the famous wordpress blogging package,
showing the weather-data from accuweather.com. please also refer to
the terms of usage of accuweather.com

== credits ==
     Barbary Jany		testing a lot and bring it to valid XHTML
     Frans Lieshout		translation to dutch
     Luís Reis			translation to portugues
     Håkan Carlström		translation to swedish
     Gabriele von der Ohe	translation to german with entities 
				(for iso-8859-1 or latin1 blogs)
     Martin Loyer		translation to french
     Robert Lang		language file for en_US
     Detti Giulio		translation to italian
     Eilif Nordseth		translation to norwegian
     Michael S.R. Petersen	translation to dansk
     Jaakko Kangosjärvi		translation to finish
     Lukasz "linshi" Linhard	translation to polish
     Castmir	     		translation to spanish
 
   All the others giving feedback about missing features and bugs
   Thank you very much for your contribution to wp-forecast.


== features ==

   + Displays the weather data from AccuWeather.com at your wordpress
   pages
   + Let you choose the 
	 * location (of course)
	 * the time after the weather data is refreshed
	 * the langugage (currently only english or german are
	   available)
	 * metric or american measures
	 * windspeed unit 
	 * the forecast days 
	 * the daytime forecast for up to nine days
	 * the nighttime forecast for up to nine nights
   + support wordpress widgets, easy placement :-)
   + customize the information you want to show
   + multiple wp-forecast widget support (beta)
   + integration into your site via css (see below)


== requirements ==

   + PHP >=4.3
   + Wordpress >2.2.x


== installation ==
	
	1.  Upload to your plugins folder, usually
	    `wp-content/plugins/`, keeping the directory structure intact
	    (i.e. `wp-forecast.php` should end up in
	    `wp-content/plugins/wp-forecast/`).

	2.  Activate the plugin on the plugin screen.

	3.  Visit the configuration page (Options -> WP-forecast) to
            pick the number of widgets, data to display and to change 
	    any other option.

	4a. Visit the Themes/Widgets page to place your wp-forecat 
	    widget within your themes sidebars

	and/or
	   
	4b. Edit your template file and put the wp-forecast function
            where you want your weather data to show up. 
	    Example:
	   
	    <ul>
	     <li>
	       <?php if(function_exists(wp_forecast)) { 
	                wp_forecast( <widget_id> ); 
		} ?>
	     </li>
	    </ul>
 
	    You have to replace <widget_id> with the choosen widget id.
	    For the first widget use wp_firecast("A"), for the second 
	    wp_forecast("B") and so on.

            In some cases you must put the call into a div environment.

	5.  Optional
	    If you would like to have another set of icons download it
	    from http://accunet.accuweather.com/wx/accunet/graphics_icons.htm 
	    and put it into the wp-content/plugins/wp-forecast/icons folder

        6.  Optional
	    If you would like to change the style, just edit wp-forecast.css
	    there are three classes div.wp-forecast for outer formatting, 
	    table.wp-forecast for the middle part or iconpart and 
	    wp-forecast-details for everything below the icon


== translations ==

   wp-forecast comes with various translations, located in the directory lang.
   if you would like to add a new translation, just take the file
   wp-forecast.pot (in the wp-forecast main directory) copy it to
   <iso-code>.po and edit it to add your translations (e.g. with poedit).


   there are different translations for the german language using
   different charsets. the defaukt de_DE uses UTF-8, de_DE-iso-8859-1
   uses iso-8859-1 and de_DE-latin1 uses HTML entitites.


   to use a different as the defalult just rename the appropriate file
   to de_DE.po and de_DE.mo



== history ==
2007-01-15 v0.1	   Initial beta release 
2007-05-17 v0.2    Fixed some incorrect XHTML code
		   Fixed path settings for icons and css
		   Tested with various browsers
2007-05-18 v0.3    Integrate forecast
2007-05-31 v0.4	   -- never published
2007-06-03 v0.5	   added support for wp widgets
		   Fixed some further incorrect XHTML code
		   added selection of the firlds to show
		   added windspeed unit support (hope you like
		   it Barbara :-))
		   added german language support for admin page
2007-06-07 v0.6	   Fixed a lot of incorrect XHTML 
		   added translation for winddirection
		   changed display of low- and hightemperature in forecast
		   no decimals for windspeed
		   fixed two phrases in translation
		   added hint for dealing with german umlaute and search
		   location dialog
		   added a bit error handling to surpress long error messages
	  	   when receiving no or invalid xml from accuweather
2007-06-11 v0.7    Fixed an incompatibility with wpSEO (used same global
                   variable language which should never happen)
2007-06-18 v0.8	   added dutch language support
		   show time in wordpress format (option: time_format)
2007-06-23 v0.9	   added copyright notice
		   added date for current conditions
		   added alternative location name
2007-07-01 v0.9.1  added new field windgusts
                   fixed some incompatibility with complex themes
2007-07-17 v1.0b   added support for up to 20 widgets with different 
		   locations and settings, 
		   added portugese language support,
		   weather data is now cached in the database, 
		   no cookies needed anymore
		   default value of missing translations is now english,
		   removed configuration dialog from widgets page,
		   to avoid misunderstanding about setup
		   fixed some minor errors
2007-07-25 v1.0b2  work around for bug 4275 in wordpress 2.2
		   removed widget id from output
		   
2007-07-29 v1.0b3  fixed output of before/after widget stuff for empty forecast
		   fixed different parameters for calling wp_forecast as 
		   widget and from sidebar.php
		   added swedish translation (thx to Håkan Carlström)

2007-09-01 v1.0b4  fixed humidity / pressure checkbox
		   removed hard coded formatting, added css class
		   added support to show current time
2007-09-09 v1.0	   fixed accuweather call for us locations
		   now works with wordpress mu

2007-10-01 v1.1    fixed: setting the current time could not be disabled
		   fixed: on some servers the current date was converted to 0,
		   switched translations to gettext as recommended by wp codex

2007-11-05 v1.2	   extend error handling for serverloss, added iso8859-1 coded 
		   german translation, fixed bug with german winddirections, 
		   added a widget title, removed standard location label (this 
		   can be handled via alternate location

2007-12-26 v1.3	   added french translation, added german icon 11
		   (fog, 11_de.gif), extended css classes to support horizontal
		    view via css, removed repeating section title 

2008-01-26 v1.4	    fix loading the wright textdomain when called from outside 
		    wordpress, added a bit debug code, work around for a bug 
		    in k2rc3 theme, added italian translation, added english 
		    lanuage file, a bit of code cleanup, extend function 
		    wp-forecast to select language per widget, added functions
		    to display a set and a range of widgets at once
2008-05-12 v1.5	    fixed two dutch phrases in dutch translation
	   	    added norwegian translation (thanks to Eilif)
2008-07-11 v1.6	    removed some hardcoded css, it is now possible to 
	   	    call the widget directly outside from wp, fixed a problem 
		    with wp >2.5 and the widget dialog, removed some redundant 
		    html, when showing no current weather information, 
		    placed forecast header into own table with own css class,
		    added timeout parameter for the accuweather connections,
		    rounded humidity to integer values, fixed some typos in 
		    swedish translation and added norwegian selection 
		    (thanks to RAM_OS)  
2008-07-20 v1.7	    removed a bit of redundant html when widget title is empty,
	   	    fixed bug in output of current conditions, added option to 
		    show a link to the accuweather forecast, added dansk 
		    translation
2008-09-17 v1.8	    added css class wpf-icon to make it easier formating 
	   	    the weather icons, added autodetection for icon filetype,	
		    gif, png and jpg are supported, corrected some
		    translations
2008-10-04 v1.9	    surpress fsockopen warning messages in case of connection 
	   	    problems and output the error as html comment
2008-11-02 v2.0	    added finish translation (thanks to Jaska), fixed a problem
	   	    with overloaded textdomains (translations), since wordpress
		    does not a sanity check if a loaded domain is reloaded, 
		    we have to do it
2008-11-09 v2.1	    added language support for spanish (thanks to Castmir) and 
	   	    polish (thanks to Lukasz), fixed minor css bug 
