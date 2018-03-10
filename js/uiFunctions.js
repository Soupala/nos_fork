function toggleTools(tooldiv)

{
	var theDiv=document.getElementById(tooldiv);
	
	if(theDiv.style.display == "block") theDiv.style.display="none";
	
	else theDiv.style.display="block";
}


//Not sure where this is used, but thought it would make more sense in this file, rather than the toggleTools.js file.  I'll methodically strip out all the toggleTools.js pointeres as I work through the code. -Mica
function updateClock ( )
{
  var currentTime = new Date ( );

  var currentHours = currentTime.getHours ( );
  var currentMinutes = currentTime.getMinutes ( );
  var currentSeconds = currentTime.getSeconds ( );

  // Pad the minutes and seconds with leading zeros, if required
  currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
  currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;

  // Choose either "AM" or "PM" as appropriate
  var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";

  // Convert the hours component to 12-hour format if needed
  currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;

  // Convert an hours component of "0" to "12"
  currentHours = ( currentHours == 0 ) ? 12 : currentHours;

  // Compose the string for display
  var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;

  // Update the time display
  document.getElementById("clock").firstChild.nodeValue = currentTimeString;
}


//The $ is currently the symbol for jQuery functions, although it may conflict with other js libraries that we use in the future.  
//Note that the jQuery libraries are also installed under js/libs/..
//It might be cleaner and faster to switch back over to Google's AJAX library (be sure to use HTTPS version), which includes jQuery.  That might allow our servers to focus on interacting with SQL


//HELP TOGGLE 
$(document).ready(function(){
  $("button.helpiconWidget").click(function(){
    $("div.helpWidget").toggle(1000);
  });
});


//SWAP MAIN CONTENT CONTAINERS FOR FULL PAGE DISPLAY
$(document).ready(function(){
  $("button.helpiconWidget").click(function(){
    $("div.helpWidget").toggle(1000);
  });
});




