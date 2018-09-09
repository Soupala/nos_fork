<?php
	include("securepage/ashland_password_protect.php");


	$dID="10001";



	if(!isset($functionsAreLoaded))
		{	include("functions.php");	}
	opendb();
	if(!isset($mapFunctionsAreLoaded))
		{	include("mapFunctions.php");	}
	//
		$myMapKey=getMapKey();
		$centerLatLong=getCityLatLong();


//	create the list of neighborhoods	//
	$nhoodTable=" <tr><td colspan=2>Neighborhoods:</td></tr>";
	$sql=mysql_query("SELECT * FROM neighborhoods ORDER BY DistrictID,NHoodID");
	$nhoodArray=array();

	while ($nhoods=mysql_fetch_array($sql) )
		{

			$nhoodTable.="<tr><td>	</td><td><a href='neighborhood.php?nh=".$nhoods['NHoodID']."&id=".$nhoods['NCID']."'>".$nhoods['NHName']."</a></td></tr>";

			$nhoodArray[$nhoods['NHoodID']]=$nhoods['polygon'];

		}

?>


<html>
<head>
	<link rel="icon" type="image/x-ico" href="images/AFPfavicon.ico" />
	<link rel="shortcut icon" type="image/x-icon" href="images/AFPfavicon.ico" />
	<link rel="stylesheet" type="text/css" href="memberStyles.css" />
	<script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=<?php echo $myMapKey; ?>&libraries=geometry">
    </script>
	<script type="text/javascript" src="mapFunctions.js"> </script>
    <script type="text/javascript">
		var poly;
		var map;
		var geocoder;

		function initialize() {
			var mapCenter=new google.maps.LatLng<?php echo $centerLatLong ?>;

			var myOptions = {
			  center: mapCenter,
			  zoom: 13,
			  mapTypeId: google.maps.MapTypeId.ROADMAP
			};

			 map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	//create a geocoder to transform addresses into lat and Long
			geocoder= new google.maps.Geocoder();
	// Add a listener for the click event
			google.maps.event.addListener(map, 'click', addPolyLatLng);
	//add the 'center' marker
			var centerMarker = new google.maps.Marker({
					position: mapCenter,
					map: map,
					icon: "http://chart.apis.google.com/chart?chst=d_map_spin&chld=<?php echo $centerPinSize; ?>|0|<?php echo $centerPinColor; ?>|11|_|C",
					title: "The Center of the Map"
				});

			//add in the NC markers
			  <?php //echo ncMarkers($ncPinColor); ?>
			 //add in the DC markers
				 <?php //echo dcMarkers($dcPinColor); ?>


			//add the district polygon
				<?php districtPolygon($dID) ?>


			 //add the neighborhood polygons
			 <?php
			//	foreach ($nhoodArray as $nid => $nhoodBounds)
				//	echo NHpolygon($nhoodBounds);
			 ?>

			 //add in the donor markers
			 <?php
/*
				foreach($nhoodArray as $nhoodID=>$polygon)
				{
					echo '
					//going to make markers for nhood: '.$nhoodID.'
					';
					 //echo donorMarkers($nhoodID);
					 echo donorMarkers($nhoodID, $fdPinColor, $ncPinColor, $dcPinColor);
				}
*/
			?>


//for the click-marker connecting lines
		  var polyOptions = {
		    strokeColor: '#000000',
		    strokeOpacity: 1.0,
		    strokeWeight: 3,
			editable: true
		  }

			poly = new google.maps.Polyline(polyOptions);
			poly.setMap(map);
}



    </script>

</head>

<body onload="initialize()">

<!--
//////////////////////////////////////////
//	The Neighborhoods 					//
//////////////////////////////////////////	-->
		<div class="widget" name="districtInfo" style="position:absolute; left:20px; width:150px; height:95%; overflow:auto;">
			<form id="editDistrictForm" action="updated.php" method="post">
				<table>
					<?php	echo $nhoodTable;	?>
				</table>
			</form>
		</div>

<!-- the latitude and longitude of the place you clicked get put in this div: -->
		<div class="widget" id="coordsDiv" style="position:absolute; overflow:auto; width:200px;  left:170px; height:45%; top:2%" >
			<br/>
			coordinates of where you clicked<hr/>
			<textarea id="clickedCoords" style="height:100%; width:100%;"> </textarea>
		</div>


<!-- the encoded path gets put in this div: -->
		<div class="widget" id="coordsDiv" style="position:absolute; overflow:auto; width:200px;  left:170px; height:45%; bottom:2%;" >
			<br/>
			the encoded polyline:<hr/>
			<textarea id="encodedPolyline" style="height:100%; width:100%;"> </textarea>
		</div>
<!--
//////////////////////////
//	The Map				//
//////////////////////////	-->
	<div class="widget" id="map_canvas" style="position:absolute; right:2%;  width:60%; height:95%; top:2%">
		<p style="color:lime"> Polygon Maps Coming Soon </p>
	</div>




</body>
<script type="text/javascript">
	/**
	 * Handles click events on a map, and adds a new point to the Polyline.
	 * @param {MouseEvent} mouseEvent
	 */
	function addPolyLatLng(event) {

		var path = poly.getPath();

	  // Because path is an MVCArray, we can simply append a new coordinate
	  // and it will automatically appear
		path.push(event.latLng);

	  // Add a new marker at the new plotted point on the polyline.
		var marker = new google.maps.Marker({
			position: event.latLng,
			title: '#' + path.getLength(),
			map: map,
			draggable: true
		});

	//add the coordinates to the coordinates div
		 document.getElementById("clickedCoords").innerHTML+=event.latLng+",\n";
	//update the text field to display the polyline encodings
		var encodeString = google.maps.geometry.encoding.encodePath(path);
		if(encodeString)
		{	document.getElementByID('encodedPolyline').innerHTML = encodeString; }

	}

</script>
</html>

<?php

//////////////////////////////////////////
//	constructs a javascript polygon for google map

function districtPolygon($districtID, $strokeColor="00AA00", $strokeOpacity="0.8",$fillColor="006600", $fillOpacity="0.35")
		{
			$row=mysql_fetch_array(mysql_query("SELECT DistrictName,DCID,center,polygon FROM districts WHERE DistrictID=".$districtID));

			echo 'var '.$row['DistrictName'].'Polygon;
			';

			if($row['polygon'] == "0")
				//come up with some default editable polygon
				echo 'There is no polygon for this area in the database';
			else
				echo 'var '.$row['DistrictName'].'Coords = [
					'.$row['polygon'].'
					];
				';




			echo $row['DistrictName'].'Polygon = new google.maps.Polygon({
					paths: '.$row['DistrictName'].'Coords,
					strokeColor: "#'.$strokeColor.'",
					strokeOpacity: '.$strokeOpacity.',
					strokeWeight: 2,
					fillColor: "#'.$fillColor.'",
					fillOpacity: '.$fillOpacity.',
					editable: true
				});
			';


			echo $row['DistrictName'].'Polygon.setMap(map);';
		}






?>
