function swapDivs(newContentId)
{	
	var theDiv
	
	if (newContentId =="homeDiv")
	{	theDiv=document.getElementById("homeDiv");
		theDiv.style.display="block";
	}
	else 
	{
		theDiv = document.getElementById("homeDiv");
		theDiv.style.display="none";
	}
	
	if (newContentId =="volunteerDiv")
	{
		theDiv=document.getElementById("volunteerDiv");
		theDiv.style.display="block";
	}
	else 
	{	theDiv=document.getElementById("volunteerDiv");
		theDiv.style.display="none";
	}
	
	if (newContentId =="shareDiv")
	{	theDiv=document.getElementById("shareDiv");
		theDiv.style.display="block";
	}
	else 
	{	theDiv=	document.getElementById("shareDiv");
		theDiv.style.display="none";
	}
	if (newContentId =="tshirtDiv")
	{theDiv=document.getElementById("tshirtDiv");
		theDiv.style.display="block";
	}
	else 
	{	theDiv=document.getElementById("tshirtDiv");
		theDiv.style.display="none";
	}
	
	if (newContentId =="contactUsDiv")
	{theDiv=document.getElementById("contactUsDiv");
		theDiv.style.display="block";
	}
	else 
	{	theDiv=document.getElementById("contactUsDiv");
		theDiv.style.display="none";
	}
	
	if (newContentId =="aboutUsDiv")
	{	theDiv=document.getElementById("aboutUsDiv");
		theDiv.style.display="block";
	}
	else 
	{	theDiv=document.getElementById("aboutUsDiv");
		theDiv.style.display="none";
	}

	if (newContentId =="coordinatorsDiv")
	{	theDiv=document.getElementById("coordinatorsDiv");
		theDiv.style.display="block";
	}
	else 
	{	theDiv=document.getElementById("coordinatorsDiv");
		theDiv.style.display="none";
	}
	
	if (newContentId =="faqDiv")
	{	theDiv=document.getElementById("faqDiv");
		theDiv.style.display="block";
	}
	else 
	{	theDiv=document.getElementById("faqDiv");
		theDiv.style.display="none";
	}
	
	if (newContentId =="Volunteer-Full")
	{	theDiv=document.getElementById("Volunteer-Full");
		theDiv.style.display="block";
	}
	else 
	{	theDiv=document.getElementById("Volunteer-Full");
		theDiv.style.display="none";
	}
	
	if (newContentId =="DCpageDiv")
	{	theDiv=document.getElementById("DCpageDiv");
		theDiv.style.display="block";
	}
	else 
	{	theDiv=document.getElementById("DCpageDiv");
		theDiv.style.display="none";
	}
	if (newContentId =="DCtalentDiv")
	{	theDiv=document.getElementById("DCtalentDiv");
		theDiv.style.display="block";
	}
	else 
	{	theDiv=document.getElementById("DCtalentDiv");
		theDiv.style.display="none";
	}
	
	if (newContentId =="tablingDiv")
	{	theDiv=document.getElementById("tablingDiv");
		theDiv.style.display="block";
	}
	else 
	{	theDiv=document.getElementById("tablingDiv");
		theDiv.style.display="none";
	}
	
	
}




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
