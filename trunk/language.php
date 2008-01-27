<?php
/* This file is part of the wp-forecast plugin for wordpress */

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

if (!function_exists('set_translation')) {

//
// this function is called with the language code and returns an array
// with the corresponding translations for wp-forecast
//
function set_translation($LANGUAGE)
{
    $tl=array();
    // Translated Labels
    $tl['title'] = "Weather";
    $tl['time']  = "time";
    $tl['barr']  = "current pressure";
    $tl['tmp']   = "current temperature";
    $tl['flik']  = "real feel";
    $tl['hmid']  = "humidity";
    $tl['t']     = "forecast summary";
    $tl['windgust'] = "wind gust speed";
    $tl['winds'] = "wind speed";
    $tl['windt'] = "wind direction (text)";
    $tl['to']    = "to";
    $tl['sunrise'] = "sunrise";
    $tl['sunset'] = "sunset";
    $tl['Forecast'] = "Forecast";
    $tl['Monday'] = "Monday";
    $tl['Tuesday'] = "Tuesday";
    $tl['Wednesday'] = "Wednesday";
    $tl['Thursday'] = "Thursday";
    $tl['Friday'] = "Friday";
    $tl['Saturday'] = "Saturday";
    $tl['Sunday'] = "Sunday";
    $tl['day'] = "day";
    $tl['night'] = "night";
    
    // ::::: Translated Forecasts ::::: 
    $tl['01'] = 'Sunny';
    $tl['02'] = 'Mostly sunny';
    $tl['03'] = 'Partly sunny';
    $tl['04'] = 'Intermittent clouds';
    $tl['05'] = 'Hazy sunshine';
    $tl['06'] = 'Mostly cloudy';
    $tl['07'] = 'Cloudy';
    $tl['08'] = 'Dreary (overcast)';
    $tl['11'] = 'Fog';
    $tl['12'] = 'Showers';
    $tl['13'] = 'Mostly cloudy with showers';
    $tl['14'] = 'Partly sunny with showers';
    $tl['15'] = 'Thunderstorms';
    $tl['16'] = 'Mostly cloudy with thundershowers';
    $tl['17'] = 'Partly sunny with thundershowers';
    $tl['18'] = 'Rain';
    $tl['19'] = 'Flurries';
    $tl['20'] = 'Mostly cloudy with flurries';
    $tl['21'] = 'Partly sunny with flurries';
    $tl['22'] = 'Snow';
    $tl['23'] = 'Mostly cloudy with snow';
    $tl['24'] = 'Ice';
    $tl['25'] = 'Sleet';
    $tl['26'] = 'Freezing rain';
    $tl['29'] = 'Rain and snow';
    $tl['30'] = 'Hot';
    $tl['31'] = 'Cold';
    $tl['32'] = 'Windy';
    
    // night
    $tl['33'] = 'Clear';
    $tl['34'] = 'Mostly clear';
    $tl['35'] = 'Partly cloudy';
    $tl['36'] = 'Intermittent clouds';
    $tl['37'] = 'Hazy moonlight';
    $tl['38'] = 'Mostly cloudy';
    $tl['39'] = 'Partly cloudy with showers';
    $tl['40'] = 'Mostly cloudy with showers';
    $tl['41'] = 'Partly cloudy with thunderstorms';
    $tl['42'] = 'Mostly cloudy with thunderstorms';
    $tl['43'] = 'Mostly cloudy with flurries';
    $tl['44'] = 'Mostly cloudy with snow';
    
    // admin page
    $tl['Refresh cache after'] = 'Refresh cache after';
    $tl['secs.'] = 'secs.';
    $tl['Use metric units'] = 'Use metric units';
    $tl['Language'] = 'Language';
    $tl['Forecast'] = 'Forecast';
    $tl['Daytime'] = 'Daytime';
    $tl['Nighttime'] = 'Nighttime';
    $tl['Day'] = 'Day';
    $tl['Display Configuration'] = 'Display Configuration';
    $tl['Current Conditions'] = 'Current Conditions';
    $tl['Forecast Day'] = 'Forecast Day';
    $tl['Forecast Night'] = 'Forecast Night';
    $tl['Icon'] = 'Icon';	
    $tl['Time'] = 'Time';
    $tl['Short Description'] = 'Short Description';
    $tl['Temperature'] = 'Temperature';
    $tl['Realfeel'] = 'Realfeel';
    $tl['Pressure'] = 'Pressure';
    $tl['Humidity'] = 'Humidity';
    $tl['Wind'] = 'Wind';	
    $tl['Sunrise'] = 'Sunrise';
    $tl['Sunset'] = 'Sunset';
    $tl['n/a'] = 'n/a';
    $tl['Location'] = 'Location';
    $tl['Update options'] = 'Update options';
    $tl['Search location'] = 'Search location';
    $tl['Set location'] = 'Set location';
    $tl['Searchterm'] = 'Searchterm';
    $tl['WP-Forecast Setup'] = 'WP-Forecast Setup';
    $tl['You have to change a field to update settings.'] = 'You have to change a field to update settings.';
    $tl['Please select your city and press set location.'] = 'Please select your city and press set location.';
    $tl['Search result'] = 'Search result';
    $tl['Press Update options to save new location.'] = 'Press Update options to save new location.';
    $tl['Settings successfully updated'] = 'Settings successfully updated';
    $tl['Windspeed-Unit'] = 'Windspeed-Unit';
    $tl['Meter/Second (m/s)'] = 'Meter/Second (m/s)';
    $tl['Kilometer/Hour (km/h)'] = 'Kilometer/Hour (km/h)';
    $tl['Miles/Hour (mph)'] = 'Miles/Hour (mph)';
    $tl['Knots (kts)'] = 'Knots (kts)';
    $tl['No locations found.']="No locations found.";
    $tl['Please replace german Umlaute ä,ö,ü with a, o, u in your searchterm.'] = 'Please replace german Umlaute ä,ö,ü with a, o, u in your searchterm.';
    $tl['Sorry, no valid weather data available.'] = "Sorry, no valid weather data available.";
    $tl['Please try again later.'] = "Please try again later.";
    $tl['Date'] = "Date";
    $tl['Copyright'] = 'Copyright';
    $tl['Locationname'] = 'Locationname';
    $tl['Windgusts'] = 'Windgusts';
    $tl['Select widget']='Select widget';
    $tl['Available widgets']='Available widgets';
    $tl['How many wp-forecast widgets would you like?']='How many wp-forecast widgets would you like?';
    $tl['widget_hint']='To configure the wp-forecast widgets,<br /> goto Options / WP-Forecast.';
    $tl['Use current time']='Use current time';
    
    if ($LANGUAGE=="de"){
	// Translated Labels
	$tl['title'] = "Das Wetter";
	$tl['loc']   = "Ort";
	$tl['time']  = "Uhrzeit";
	$tl['barr']  = "Aktueller Luftdruck";
	$tl['tmp']   = "Aktuelle Temperatur";
	$tl['tmp1']  = "Temperatur";
	$tl['flik']  = "Gefühlte Temperatur";
	$tl['hmid']  = "Luftfeuchtigkeit";
	$tl['t']     = "Kurze Zusammenfassung der Vorhersage (z.B. 'sunny')";
	$tl['windgust'] = "Windböengeschwindigkeit";
	//$tl['winds'] = "Windgeschwindigkeit";
	$tl['winds'] = "Wind";
	$tl['windt'] = "Windrichtung (Text)";
	$tl['to']    = "bis";
	$tl['sunrise'] = "Sonnenaufgang";
	$tl['sunset'] = "Sonnenuntergang";
	$tl['Forecast'] = "Vorschau";
	$tl['Monday'] = "Montag";
	$tl['Tuesday'] = "Dienstag";
	$tl['Wednesday'] = "Mittwoch";
	$tl['Thursday'] = "Donnerstag";
	$tl['Friday'] = "Freitag";
	$tl['Saturday'] = "Samstag";
	$tl['Sunday'] = "Sonntag";
	$tl['day'] = "Tag";
	$tl['night'] = "Nacht";

        // ::::: Translated Forecasts ::::: 
	$tl['01'] = 'Wolkenlos';
	$tl['02'] = 'Heiter';
	$tl['03'] = 'Heiter bis wolkig';
	$tl['04'] = 'Wolkig';
	$tl['05'] = 'Dunstiger Sonnenschein';
	$tl['06'] = 'Bewölkt';
	$tl['07'] = 'Stark bewölkt';
	$tl['08'] = 'Bedeckt';
	$tl['11'] = 'Nebel';
	$tl['12'] = 'Regenschauer';
	$tl['13'] = 'Bewölkt mit Regenschauern';
	$tl['14'] = 'Wolkig mit Regenschauern';
	$tl['15'] = 'Gewitter';
	$tl['16'] = 'Bewölkt mit Gewitterschauern';
	$tl['17'] = 'Wolkig mit Gewitterschauern';
	$tl['18'] = 'Regen';
	$tl['19'] = 'Schneeschauer';
	$tl['20'] = 'Bewölkt mit Schneeschauern';
	$tl['21'] = 'Wolkig mit Schneeschauern';
	$tl['22'] = 'Schneefall';
	$tl['23'] = 'Bewölkt mit Schneefall';
	$tl['24'] = 'Glatteis';
	$tl['25'] = 'Graupelschauer';
	$tl['26'] = 'Eisregen';
	$tl['29'] = 'Schneeregen';
	$tl['30'] = 'Heiß';
	$tl['31'] = 'Kalt';
	$tl['32'] = 'Windig';

        // night
	$tl['33'] = 'Klare Nacht';
	$tl['34'] = 'Überwiegend klar';
	$tl['35'] = 'Wolkig';
	$tl['36'] = 'Zeitweise wolkig';
	$tl['37'] = 'Dunstig';
	$tl['38'] = 'Bewölkt';
	$tl['39'] = 'Wolkig mit Regenschauern';
	$tl['40'] = 'Bewölkt mit Regenschauern';
	$tl['41'] = 'Wolkig mit Gewitterschauern';
	$tl['42'] = 'Bewölkt mit Gewitterschauern';
	$tl['43'] = 'Bewölkt mit Schneeschauern';
	$tl['44'] = 'Bewölkt mit Schneefall';

	// admin page
	$tl['Refresh cache after'] = 'Cache erneuern nach';
	$tl['secs.'] = 'Sekunden';
	$tl['Use metric units'] = 'metrische Einheiten verwenden';
	$tl['Language'] = 'Sprache';
	$tl['Forecast'] = 'Vorhersage';
	$tl['Daytime'] = 'tagsüber';
	$tl['Nighttime'] = 'nachts';
	$tl['Day'] = 'Tag';
	$tl['Display Configuration'] = 'Anzeige Einstellungen';
	$tl['Current Conditions'] = 'Aktuelles Wetter';
	$tl['Forecast Day'] = 'Vorhersage tagsüber';
	$tl['Forecast Night'] = 'Vorhersage nachts';
	$tl['Icon'] = 'Symbol';	
	$tl['Time'] = 'Zeit';
	$tl['Short Description'] = 'Kurzbezeichnung';
	$tl['Temperature'] = 'Temperatur';
	$tl['Realfeel'] = 'Gefühlte Temperatur';
	$tl['Pressure'] = 'Luftdruck';
	$tl['Humidity'] = 'Luftfeuchtigkeit';
	$tl['Wind'] = 'Wind';	
	$tl['Sunrise'] = 'Sonnenaufgang';
	$tl['Sunset'] = 'Sonnenuntergang';
	$tl['n/a'] = 'n/a';
	$tl['Location'] = 'Ort';
	$tl['Update options'] = 'Einstellungen speichern';
	$tl['Search location'] = 'Ort suchen';
	$tl['Set location'] = 'Ort übernehmen';
	$tl['Searchterm'] = 'Suchbegriff';
	$tl['WP-Forecast Setup'] = 'WP-Forecast Einstellungen';
	$tl['You have to change a field to update settings.'] = 'Um die Einstellungen zu aktualisieren, müssen Sie mindestens ein Feld verändern.';
	$tl['Please select your city and press set location.'] = 'Bitte einen Ort auswählen und Ort Übernehmen klicken.';
	$tl['Search result'] = 'Suchergebnis';
	$tl['Press Update options to save new location.'] = 'Klick auf Einstellungen speichern sichert den Ort.';
	$tl['Settings successfully updated'] = 'Die Einstellungen wurden erfolgreich gespeichert.';
	$tl['Windspeed-Unit'] = 'Einheit der Windgeschwindigkeit';
	$tl['Meter/Second (m/s)'] = 'Meter/Sekunde (m/s)';
	$tl['Kilometer/Hour (km/h)'] = 'Kilometer/Stunde (km/h)';
	$tl['Miles/Hour (mph)'] = 'Meilen/Stunde (mph)';
	$tl['Knots (kts)'] = 'Knoten (kts)';
	$tl['No locations found.'] = "Keine Orte gefunden.";
    	$tl['Please replace german Umlaute ä,ö,ü with a, o, u in your searchterm.'] = 'Bitte ersetzen Sie Umlaute im Suchbegriff durch die entsprechenden Vokale (z.B. ä durch a)';
    	$tl['Sorry, no valid weather data available.'] = "Aktuell liegen keine gültigen Wetterinformationen vor.";
	$tl['Please try again later.'] = "Bitte versuchen Sie es später nochmal.";
	$tl['Date'] = "Datum";
	$tl['Copyright'] = 'Copyright';
	$tl['Locationname'] = 'Ortsname';
	$tl['Windgusts'] = 'Böen';
	$tl['Select widget']='Widget auswählen';
	$tl['Available widgets']='Verfügbare Widgets';
	$tl['How many wp-forecast widgets would you like?']='Wieviele wp-forecast widgets möchten Sie verwenden?';
	$tl['widget_hint']='Zum Konfigurieren der wp-forecast Widgets,<br /> rufen Sie bitte die WP-Forecast Admin Seite unter Einstellungen / WP-Forecast auf.'; 
	$tl['Use current time']='Verwende aktuelle Zeit';
    }




    if ($LANGUAGE=="nl") {
      // Translated Labels
      $tl['title'] = 'Weer';
      $tl['time'] = 'tijd';
      $tl['barr'] = 'huidige druk';
      $tl['tmp'] = 'Huidige temperatuur';
      $tl['flik'] = 'gevoel';
      $tl['hmid'] = 'humidity';
      $tl['t'] = 'samenvatting verwachting';
      $tl['windgust'] = 'windstoten, snelheid';
      $tl['winds'] = 'wind snelheid';
      $tl['windt'] = 'wind richting (text)';
      $tl['to'] = 'tot';
      $tl['sunrise'] = 'zon opgang';
      $tl['sunset'] = 'zon ondergang';
      $tl['Forecast'] = 'Verwachting';
      $tl['Monday'] = 'Maandag';
      $tl['Tuesday'] = 'Dinsdag';
      $tl['Wednesday'] = 'Woensdag';
      $tl['Thursday'] = 'Donderdag';
      $tl['Friday'] = 'Vrijdag';
      $tl['Saturday'] = 'Zaterdag';
      $tl['Sunday'] = 'Zondag';
      $tl['day'] = 'dag';
      $tl['night'] = 'nacht';

      // ::::: Translated Forecasts :::::
      $tl['01'] = 'Zonnig';
      $tl['02'] = 'Meestal Zonnig';
      $tl['03'] = 'Deels Zonnig';
      $tl['04'] = 'Af en toe bewolking';
      $tl['05'] = 'Nevel';
      $tl['06'] = 'Meest bewolkt';
      $tl['07'] = 'Bewolkt';
      $tl['08'] = 'Somber (betrokken lucht)';
      $tl['11'] = 'Mist';
      $tl['12'] = 'Stortbuien';
      $tl['13'] = 'Meest bewolkt met regen';
      $tl['14'] = 'Deels zonnig met buien';
      $tl['15'] = 'Onweer';
      $tl['16'] = 'Meest bewolkt met onweersbuinen';
      $tl['17'] = 'Deels zonnig met onweersbuien';
      $tl['18'] = 'Regen';
      $tl['19'] = 'Hagel en Regen';
      $tl['20'] = 'Meest bewolkt en hagel en regen';
      $tl['21'] = 'Deels zonnig met hagel en regen';
      $tl['22'] = 'Sneeuw';
      $tl['23'] = 'Meest bewolkt met sneeuw';
      $tl['24'] = 'IJs';
      $tl['25'] = 'Natte Sneeuw';
      $tl['26'] = 'Hagelbuien';
      $tl['29'] = 'Regen en sneeuw';
      $tl['30'] = 'Heet';
      $tl['31'] = 'Koud';
      $tl['32'] = 'Winderig';
      
      // night
      $tl['33'] = 'Helder';
      $tl['34'] = 'Meest helder';
      $tl['35'] = 'Deels bewolkt';
      $tl['36'] = 'Af en toe bewolking';
      $tl['37'] = 'Nevelig';
      $tl['38'] = 'Meest bewolkt';
      $tl['39'] = 'Deels bewolkt met buien';
      $tl['40'] = 'Meest bewoikt met buien';
      $tl['41'] = 'Deels bewolkt met onweer';
      $tl['42'] = 'Meest bewolkt met onweer';
      $tl['43'] = 'Meest bewolkt met hagel en regen';
      $tl['44'] = 'Meest bewolkt met sneeuw';
      
      // admin page
      $tl['Refresh cache after'] = 'Ververs geheugen na';
      $tl['secs'] = 'sec.';
      $tl['Use metric units'] = 'Gebruik metriek stelsel';
      $tl['Language'] = 'Taal';
      $tl['Forecast'] = 'Voorspelling';
      $tl['Daytime'] = 'Overdag';
      $tl['Nighttime'] = 'Nacht';
      $tl['Day'] = 'Dag';
      $tl['Display Configuration'] = 'Weergeven Configuratie';
      $tl['Current Conditions'] = 'Huidig weerbeeld';
      $tl['Forecast Day'] = 'Voorspelling Dag';
      $tl['Forecast Night'] = 'Voorspelling Nacht';
      $tl['Icon'] = 'Ikoon';
      $tl['Time'] = 'Tijd';
      $tl['Short Description'] = 'Korte Omschrijving';
      $tl['Temperature'] = 'Temperatuur';
      $tl['Realfeel'] = 'Werkelijk gevoel';
      $tl['Pressure'] = 'Druk';
      $tl['Humidity'] = 'Vochtigheid';
      $tl['Wind'] = 'Wind';
      $tl['Sunrise'] = 'Zon opgang';
      $tl['Sunset'] = 'Zon ondergang';
      $tl['n/a'] = 'n/a';
      $tl['Location'] = 'Plaats';
      $tl['Update options'] = 'Bijwerken opties';
      $tl['Search location'] = 'Zoek plaats';
      $tl['Set location'] = 'Set lokatie';
      $tl['Searchterm'] = 'Zoek term';
      $tl['WP-Forecast Setup'] = 'WP-Voorspelling Instellingen';
      $tl['You have to change a field to update settings.'] = 'U moet een veld hebben gewijzigd om te kunnen bijwerken.';
      $tl['Please select your city and press set location.'] = 'Selecteer uw stad en druk op set lokatie.';
      $tl['Search result'] = 'Zoek resultaten';
      $tl['Press Update options to save new location.'] = 'Druk op bijwerken om nieuwe lokatie te bewaren.';
      $tl['Settings successfully updated'] = 'Succesvol bijgewerkt';
      $tl['Windspeed-Unit'] = 'Windsnelheid-Unit';
      $tl['Meter/Second (m/s)'] = 'Meter/Seconde (m/s)';
      $tl['Kilometer/Hour (km/h)'] = 'Kilometer/Uur (km/h)';
      $tl['Miles/Hour (mph)'] = 'Mijl/Uur (mph)';
      $tl['Knots (kts)'] = 'Knopen (knps)';
    }

    
    if ($LANGUAGE=="pt") {
      // Translated Labels
      $tl['title'] = "Meteorologia";
      $tl['time']  = "Hora";
      $tl['barr']  = "Pressão";
      $tl['tmp']   = "Temp";
      $tl['flik']  = "Temp. sentida";
      $tl['hmid']  = "Humidade";
      $tl['t']     = "forecast summary";
      $tl['windgust'] = "Velocidade windgust";
      $tl['winds'] = "Vel. vento";
      $tl['windt'] = "Dir. vento (text)";
      $tl['to']    = "to";
      $tl['sunrise'] = "Nascer do sol";
      $tl['sunset'] = "Por do sol";
      $tl['Forecast'] = "Previsão";
      $tl['Monday'] = "Segunda";
      $tl['Tuesday'] = "Terça";
      $tl['Wednesday'] = "Quarta";
      $tl['Thursday'] = "Quinta";
      $tl['Friday'] = "Sexta";
      $tl['Saturday'] = "Sábado";
      $tl['Sunday'] = "Domingo";
      $tl['day'] = "Dia";
      $tl['night'] = "Noite";
      
      // ::::: Translated Forecasts ::::: 
      $tl['01'] = 'Sol';
      $tl['02'] = 'Muito sol';
      $tl['03'] = 'Algum sol';
      $tl['04'] = 'Céu por vezes nublado';
      $tl['05'] = 'Sol entre nuvens';
      $tl['06'] = 'Céu muito nublado';
      $tl['07'] = 'Céu nublado';
      $tl['08'] = 'Céu sombrio (Nublado)';
      $tl['11'] = 'Nevoeiro';
      $tl['12'] = 'Periodos de chuva';
      $tl['13'] = 'Muitas nuvens com periodos de chuva';
      $tl['14'] = 'Algum sol com periodos de chuva';
      $tl['15'] = 'Trovoada/Temporal';
      $tl['16'] = 'Muitas nuvens e trovoada';
      $tl['17'] = 'Algum sol com trovoada';
      $tl['18'] = 'Chuva';
      $tl['19'] = 'Rajadas de vento';
      $tl['20'] = 'Muitas nuvens e rajadas de vento';
      $tl['21'] = 'Algumas nuvens e rajadas de vento';
      $tl['22'] = 'Neve';
      $tl['23'] = 'Muitas nuvens e neve';
      $tl['24'] = 'Gelo';
      $tl['25'] = 'Granizo';
      $tl['26'] = 'Chuva e muito frio';
      $tl['29'] = 'Neve com chuva';
      $tl['30'] = 'Quente';
      $tl['31'] = 'Frio';
      $tl['32'] = 'Ventoso';
      
      // night
      $tl['33'] = 'Céu quase limpo';
      $tl['34'] = 'Céu limpo';
      $tl['35'] = 'Céu parcialmente limpo';
      $tl['36'] = 'Nuvens intermitentes';
      $tl['37'] = 'Céu com algumas nuvens';
      $tl['38'] = 'Céu muito nublado';
      $tl['39'] = 'Algumas nuvens com periodos de chuva';
      $tl['40'] = 'Muitas nuvens com periodos de chuva';
      $tl['41'] = 'Algumas nuvens com trovoada';
      $tl['42'] = 'Muitas nuvens com trovoada';
      $tl['43'] = 'Muitas nuvens com rajadas de vento';
      $tl['44'] = 'Muitas nuvens com neve';
      
      // admin page
      $tl['Refresh cache after'] = 'Refrescar a cache a cada';
      $tl['secs'] = 'segundos.';
      $tl['Use metric units'] = 'Usar unidades métricas';
      $tl['Language'] = 'Lingua';
      $tl['Forecast'] = 'Previsão';
      $tl['Daytime'] = 'Periodo do Dia';
      $tl['Nighttime'] = 'Periodo da Noite';
      $tl['Day'] = 'Dia';
      $tl['Display Configuration'] = 'Mostrar Configuração';
      $tl['Current Conditions'] = 'Condições Actuais';
      $tl['Forecast Day'] = 'Previsão para o dia';
      $tl['Forecast Night'] = 'Previsão para a noite';
      $tl['Icon'] = 'Icone';	
      $tl['Time'] = 'Hora';
      $tl['Short Description'] = 'Descrição curta';
      $tl['Temperature'] = 'Temperatura';
      $tl['Realfeel'] = 'Realfeel';
      $tl['Pressure'] = 'Pressão';
      $tl['Humidity'] = 'Humidade';
      $tl['Wind'] = 'Vento';	
      $tl['Sunrise'] = 'Nascer do Sol';
      $tl['Sunset'] = 'Por do Sol';
      $tl['n/a'] = 'n/a';
      $tl['Location'] = 'Localização';
      $tl['Update options'] = 'Actualizar Opções';
      $tl['Search location'] = 'Procurar Localização';
      $tl['Set location'] = 'Definir Localização';
      $tl['Searchterm'] = 'Termo da procura';
      $tl['WP-Forecast Setup'] = 'WP-Forecast Setup';
      $tl['You have to change a field to update settings.'] = 'Tem que modificar um campo para actualizar as definições.';
      $tl['Please select your city and press set location.'] = 'Seleccione a sua Cidade e pressione Definir Localização.';
      $tl['Search result'] = 'Resultado da Procura';
      $tl['Press Update options to save new location.'] = 'Pressione Actualizar Opções para guardar a nova Localização.';
      $tl['Settings successfully updated'] = 'Definições actualizadas com sucesso';
      $tl['Windspeed-Unit'] = 'Velocidade do Vento-Unidade';
      $tl['Meter/Second (m/s)'] = 'Metros/Segundo (m/s)';
      $tl['Kilometer/Hour (km/h)'] = 'Kilometros/Hora (km/h)';
      $tl['Miles/Hour (mph)'] = 'Milhas/Hora (mph)';
      $tl['Knots (kts)'] = 'Knots (kts)';
      $tl['No locations found.']="Não foram encontradas localizações.";
      $tl['Please replace german Umlaute ä,ö,ü with a, o, u in your searchterm.'] = 'Please replace german Umlaute ä,ö,ü with a, o, u in your searchterm.';
      $tl['Sorry, no valid weather data available.'] = "Desculpe, não existem dados metereológicos disponíveis.";
      $tl['Please try again later.'] = "Por favor tente de novo mais tarde.";
      $tl['Date'] = "Data";
      $tl['Copyright'] = 'Copyright';
      $tl['Locationname'] = 'Nome Local';
      $tl['Windgusts'] = 'Rajada';
    }

    
    if ($LANGUAGE=="se") {
      // Translated Labels
      $tl['title'] = "V&auml;der";
      $tl['time']  = "tid";
      $tl['barr']  = "Nuvarande tryck";
      $tl['tmp']   = "Nuvarande temperatur";
      $tl['flik']  = "Upplevd temperatur";
      $tl['hmid']  = "Luftfuktighet";
      $tl['t']     = "prognossammanfattning";
      $tl['windgust'] = "Vindhastighet i byar";
      $tl['winds'] = "Vindhastighet";
      $tl['windt'] = "vindriktning (text)";
      $tl['to']    = "till";
      $tl['sunrise'] = "Soluppg&aring;ng";
      $tl['sunset'] = "Solnedg&aring;ng";
      $tl['Forecast'] = "Prognos";
      $tl['Monday'] = "M&aring;ndag";
      $tl['Tuesday'] = "Tisdag";
      $tl['Wednesday'] = "Onsdag";
      $tl['Thursday'] = "Torsdag";
      $tl['Friday'] = "Fredag";
      $tl['Saturday'] = "L&ouml;rdag";
      $tl['Sunday'] = "S&ouml;ndag";
      $tl['day'] = "dag";
      $tl['night'] = "natt";
      
      // ::::: Translated Forecasts ::::: 
      $tl['01'] = 'Soligt';
      $tl['02'] = 'Mestadels soligt';
      $tl['03'] = 'Delvis soligt';
      $tl['04'] = 'Moln ibland';
      $tl['05'] = 'Disigt solsken';
      $tl['06'] = 'Mestadels molnigt';
      $tl['07'] = 'Molnigt';
      $tl['08'] = 'Mulet';
      $tl['11'] = 'Dimma';
      $tl['12'] = 'Skurar';
      $tl['13'] = 'Mestadels molnigt med skurar';
      $tl['14'] = 'Delvis molnigt med skurar';
      $tl['15'] = '&aring;skstrorm';
      $tl['16'] = 'Mestadels molnigt med &aring;skstrorm';
      $tl['17'] = 'Delvis soligt med &aring;skstrorm';
      $tl['18'] = 'Regn';
      $tl['19'] = 'Kastbyar';
      $tl['20'] = 'Mestadels molnigt med kastbyar';
      $tl['21'] = 'Delvis soligt med kastbyar';
      $tl['22'] = 'Sn&ouml';
      $tl['23'] = 'Mestadels molnigt with snow';
      $tl['24'] = 'Is';
      $tl['25'] = 'Sn&oum;blandat regn';
      $tl['26'] = 'Iskallt regn';
      $tl['29'] = 'Regn och sn&ouml;';
      $tl['30'] = 'Varmt';
      $tl['31'] = 'Kallt';
      $tl['32'] = 'Bl&aring;sigt';
      
      // night
      $tl['33'] = 'Klart';
      $tl['34'] = 'Mestadels klart';
      $tl['35'] = 'Delvis molnigt';
      $tl['36'] = 'Moln ibland';
      $tl['37'] = 'Disigt m&aring;nljus';
      $tl['38'] = 'Mestadels molnigt';
      $tl['39'] = 'Delvis molnigt med skurar';
      $tl['40'] = 'Mestadels molnigt med skurar';
      $tl['41'] = 'Delvis molnigt med &aring;skstrorm';
      $tl['42'] = 'Mestadels molnigt med &aring;skstrorm';
      $tl['43'] = 'Mestadels molnigt med kastbyar';
      $tl['44'] = 'Mestadels molnigt med sn&ouml;';
      
      // admin page
      $tl['Refresh cache after'] = 'F&ouml;rnya cache efter';
      $tl['secs'] = 'sekunder.';
      $tl['Use metric units'] = 'Anv&auml;nd metriska enheter';
      $tl['Language'] = 'Spr&aring;k';
      $tl['Forecast'] = 'Prognos';
      $tl['Daytime'] = 'Dagtid';
      $tl['Nighttime'] = 'Nattetid';
      $tl['Day'] = 'Dag';
      $tl['Display Configuration'] = 'Visningsinst&auml;llningar';
      $tl['Current Conditions'] = 'Nuvarande f&ouml;rh&aring;llande';
      $tl['Forecast Day'] = 'Prognos dag';
      $tl['Forecast Night'] = 'Prognos natt';
      $tl['Icon'] = 'Ikon';	
      $tl['Time'] = 'Tid';
      $tl['Short Description'] = 'Kort beskrivning';
      $tl['Temperature'] = 'Temperatur';
      $tl['Realfeel'] = 'Upplevd temperatur';
      $tl['Pressure'] = 'Tryck';
      $tl['Humidity'] = 'Luftfuktighet';
      $tl['Wind'] = 'Vind';	
      $tl['Sunrise'] = 'Soluppg&aring;ng';
      $tl['Sunset'] = 'Solnedg&aring;ng';
      $tl['n/a'] = 'inte tillg&auml;nglig';
      $tl['Location'] = 'Plats';
      $tl['Update options'] = 'Spara inst&auml;llningar';
      $tl['Search location'] = 'Leta efter plats';
      $tl['Set location'] = 'Spara plats';
      $tl['Searchterm'] = 'S&ouml;kterm';
      $tl['WP-Forecast Setup'] = 'WP-Forecast Inst&auml;llning';
      $tl['You have to change a field to update settings.'] = 'Du m&aring;ste &auml;ndra ett f&auml;lt f&ouml;r att spara inst&auml;llningarna.';
      $tl['Please select your city and press set location.'] = 'V&auml;lj stad och tryck spara plats.';
      $tl['Search result'] = 'S&ouml;kresultat';
      $tl['Press Update options to save new location.'] = 'V&auml;lj spara inst&auml;llningar f&ouml;r att spara ny plats.';
      $tl['Settings successfully updated'] = 'Inst&auml;llningar sparade';
      $tl['Windspeed-Unit'] = 'Enhet f&ouml;r Vindhastighet';
      $tl['Meter/Second (m/s)'] = 'Meter/Sekund (m/s)';
      $tl['Kilometer/Hour (km/h)'] = 'Kilometer/Timme (km/h)';
      $tl['Miles/Hour (mph)'] = 'Miles/Timme (mph)';
      $tl['Knots (kts)'] = 'Knop (kts)';
      $tl['No locations found.']="Ingen plats hittad.";
      $tl['Please replace german Umlaute ä,ö,ü with a, o, u in your searchterm.'] = 'Ers&auml;tt tyska/svenska tecken ä,ö,ü med a, o, u i din s&ouml;kning.';
      $tl['Sorry, no valid weather data available.'] = "Tv&auml;rr, ingen giltig v&aunl;derdata tillg&auml;lig.";
      $tl['Please try again later.'] = "F&ouml;rs&ouml;k senare.";
      $tl['Date'] = "Datum";
      $tl['Copyright'] = 'Copyright';
      $tl['Locationname'] = 'Platsnamn';
      $tl['Windgusts'] = 'Vindbyar';
      $tl['Select widget']='V&auml;lj widget';
      $tl['Available widgets']='Tillg&auml;ngliga widgets';
      $tl['How many wp-forecast widgets would you like?']='Hur m&aring;nga wp-forecast widgets vill du ha?';
      $tl['widget_hint']='F&ouml;r att konfigurera wp-forecast widgetarna,<br /> g&aring; till Inst&auml;llningar / WP-Forecast.';
    }
    
    return $tl;
}
}
?>
