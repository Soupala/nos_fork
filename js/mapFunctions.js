function killWhiteSpace(aStr)
{
	var mystr=aStr.replace(/\s/g, '');
	return mystr;
}


function addMarkerToMap(address, latLongBox)
{
	//alert("latLongBox value:"+latLongBox);
	if(document.getElementById(latLongBox).value=="0" || document.getElementById(latLongBox).value==null)
	{	
		//alert("latLongBox value is 0 or Null. \ngeocoding address now\n address: \n"+address);
		codeAddress(address, latLongBox);
	}
	else
	{	
		//alert("latLongBox value is NOT 0 or NULL. \ncreating marker now from stored lat/long: "+document.getElementById(latLongBox).value);
		// var mylatlong= document.getElementById(latLongBox).value;
		// var mylength=mylatlong.length;

		// //mylatlong=mylatlong.substring(1, mylength-1);
// alert("addMarkerToMap() sees \n mylatlong:"+mylatlong+"\n string length: "+mylength);
		// var theSpot= new google.maps.LatLng(mylatlong);
		
		// var newDonorMarker = new google.maps.Marker({
			// map: map,
			// position: theSpot
		// });
		
		var latlongStr= document.getElementById(latLongBox).value;
		var minusParens=latlongStr.substring(1, latlongStr.length-1);
		var split=minusParens.split(",",2);
		var lat=parseFloat(split[0]);
		var lng=parseFloat(split[1]);
		
		
//alert("addMarkerToMap() sees \n position:"+position);
		var newDonorLatLong = new google.maps.LatLng(lat,lng);
		var newDonorMarker = new google.maps.Marker({
			position: newDonorLatLong,
			map: map,
			icon: 'icons/marker_split.png'
			});
		
		
	}
}


/**	transforms the address in id="address" to coordinates in id="myCoords" when 	
 *	Encode button is clicked */
		function codeAddress(address, latLongBox) {
			geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) 
					{
						map.setCenter(results[0].geometry.location);
						var marker = new google.maps.Marker({
							map: map,
							position: results[0].geometry.location
						});
						document.getElementById(latLongBox).value=results[0].geometry.location;
					} 
				else 
				{alert("Geocode was not successful for the following reason: " + status);}
			});
		}
		
function addslashes(str)
{
	str=str.replace(/\\/g,'\\\\');
	str=str.replace(/\'/g,'\\\'');
	str=str.replace(/\"/g,'\\"');
	str=str.replace(/\0/g,'\\0');
	return str;
}	
function stripslashes(str)
{
	str=str.replace(/\\'/g,'\'');
	str=str.replace(/\\"/g,'"');
	str=str.replace(/\\0/g,'\0');
	str=str.replace(/\\\\/g,'\\');
}
		
     function addLatLng(event) {
        var path = newPoly.getPath();
        // Because path is an MVCArray, we can simply append a new coordinate
        // and it will automatically appear
        path.push(event.latLng);

		updateEncodedPath(path);
      }
	  
	  function moveLatLng(event)
	  {
		var path=newPoly.getPath();
		
		updateEncodedPath(path);
	  }
	  
// Update the text field to display the polyline encodings
	function updateEncodedPath(path)
	{
        var encodeString = google.maps.geometry.encoding.encodePath(path);
        if (encodeString) {
          document.getElementById('encoded-polyline').value = encodeString;
        }
	}
	
	
// show the encoded path in html area where <... id="displayID" ...>
	 function showEncoding(displayID) 
	 {
			var encodeString = google.maps.geometry.encoding.encodePath(latlngs);
			document.getElementById(displayID).value = encodeString;
	}

	function decodePath(encodedPoly)
	{
		return google.maps.geometry.encoding.decodePath(encodedPoly);
	}
	
	
//toggles independant of other divs in the group (for maptools workpanel)
	function toggleTools(tooldiv)
	{
		var theDiv=document.getElementById(tooldiv);
		if(theDiv.style.display == "block") theDiv.style.display="none";
		else theDiv.style.display="block";
	}

	//
	function showhideMarker(marker,map)
	{
				if(marker.getMap()== null)
					marker.setMap(map);
				else marker.setMap(null);
	}
	
	
	function updateCurrentPoly(newCurrentPoly, newCurrentName)
	{
		//currentPoly = newCurrentPoly;
		if(typeof(newCurrentPoly)==='undefined')
			newCurrentPoly=newPoly;
		if(typeof(newCurrentName)==='undefined')
			newCurrentName="newPoly";
		
		alert("you're about to update the currentPolygon to:\n"
		+"name: "+ newCurrentName);
	}
	
	
	function setPolyEditable(poly)
	{
		poly.setVisible(true);
		if(poly.getEditable()==true)
			poly.setEditable(false);
		else poly.setEditable(true);
	}
	
	function showhidePoly(poly)
	{
	
		if(poly.getVisible()==true)
			poly.setVisible(false);
		else poly.setVisible(true);
	}
	
	
	function updateZoom()
	{
		newZoom=document.getElementById('zoomLevelText').value;
		map.setZoom(newZoom);
	}