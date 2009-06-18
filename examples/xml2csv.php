<?php 
$xmlfile="examples/weather_bug_example.xml";

 // XML-Daten laden 
$xmlDom = new domdocument; 
$xmlDom->load($xmlfile); 
 

$location = $xmlDom->getElementsByTagName('station'); 
echo $location->item(0)->nodeValue."\n";     

$realfeel = $xmlDom->getElementsByTagName('feels-like'); 
echo $realfeel->item(0)->nodeValue."\n";     
echo $realfeel->item(0)->getAttribute("units")."\n";

$obsdate = $xmlDom->getElementsByTagName('ob-date');
$od = $obsdate->item(0)->childNodes; 
foreach ($od as $y) {
  echo $y->getAttribute("number")." ";
}
echo "\n";
?>
