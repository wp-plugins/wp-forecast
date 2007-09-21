/*
Plugin Name: wp-forecast
Plugin URI: http://www.tuxlog.de
Description:  wp-forecast is a highly customizable plugin for wordpress, 
	      showing weather-data from accuweather.com.
Version: 1.0 
Author: Hans Matzen <webmaster at tuxlog dot de>
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

you are reading the readme.txt file for the wp-forecast plugin.
wp-forecast is a plugin for the famous wordpress blogging package,
showing the weather-data from accuweather.com. please also refer to
the terms of usage of accuweather.com

== credits ==
     Barbary Jany		testing a lot and bring it to valid XHTML
     Frans Lieshout		translation to dutch
     Luís Reis			translation to portugues
     Håkan Carlström		translation to swedish
     
 
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

If you would like to add a new langauge to wp-forecast, just take the
few steps written below and send the result to webmaster at tuxlog dot de

    1. edit language.php copy section
      
       if ($LANGUAGE=="en") {
       ...
       
       ...
       }
       
       and place it again at the end of the file (but before ?>)

    2. change the language code en to <your language code> in the
       copied section
       
    3. translate all these words   

    4. save the file :-)

    5. edit wp-forecast-admin.php
       
       look for  "<b>Language: </b>"
       below enter one line containing
	     <option value="your-language-code" <?php if
	     ($wpf_language=="your-language-code") echo
	     "selected"?>>your-lanuage (e.g. french)</option>

    6. save the file :-)


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


== todo ==
   - add more languages (I am missing french and spanish  ;-) )
   - write a little manual for the setup
   - enhance icon management on a per widget basis




 

