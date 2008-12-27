<?php
// this is the javascript code for the ckeck/uncheck all forecast boxes
?>
<script type="text/javascript">
   function check(fname)
    {
      if (fname=="day") {
	sammelvalue=document.woptions.alldays.checked;
	document.woptions.day1.checked=sammelvalue; 
	document.woptions.day2.checked=sammelvalue; 
	document.woptions.day3.checked=sammelvalue; 
	document.woptions.day4.checked=sammelvalue; 
	document.woptions.day5.checked=sammelvalue; 
	document.woptions.day6.checked=sammelvalue; 
	document.woptions.day7.checked=sammelvalue; 
	document.woptions.day8.checked=sammelvalue; 
	document.woptions.day9.checked=sammelvalue;
      } else {
	sammelvalue=document.woptions.allnight.checked;
	document.woptions.night1.checked=sammelvalue; 
	document.woptions.night2.checked=sammelvalue; 
	document.woptions.night3.checked=sammelvalue; 
	document.woptions.night4.checked=sammelvalue; 
	document.woptions.night5.checked=sammelvalue; 
	document.woptions.night6.checked=sammelvalue; 
	document.woptions.night7.checked=sammelvalue; 
	document.woptions.night8.checked=sammelvalue; 
	document.woptions.night9.checked=sammelvalue;
      }
      return !sammelvalue;
    }    
</script>

