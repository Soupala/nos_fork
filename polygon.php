<!DOCTYPE html>
<?php 

	include("securepage/nfp_password_protect.php"); 
	if(!isset($functionsAreLoaded))
		{	include("functions.php");	}
	opendb();
	if(!isset($mapFunctionsAreLoaded))
		{	include("mapFunctions.php");	}
	//
		$myMapKey=getMapKey();
		$centerLatLong=getCityLatLong();
		
		
//first, do saves		
		if(isset($_POST['save']))
		{
			saveRegionPolygon($_POST);
		}


		
?>


<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Polygon Editor for NOS</title>
	<meta name="description" content="Polygon Editor NOS">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />
	<script src="js/libs/modernizr-2.5.3.min.js"></script>
	
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $myMapKey; ?>&sensor=false&libraries=geometry">
	</script>
	<script type="text/javascript" src="js/mapFunctions.js"> </script>
  
<!-- INITIALIZE() -->
    <script type="text/javascript">
		var editablePoly;
		var map;
		var mapCenter;
		var geocoder;
		var polyOptions;

		var districtArray={};
		var nhoodArray={};
		var membersArray={};
		var numDistricts=0;
		var numNhoods=0;
		var numMembers=0;
		var currentRegionType;
		var previousRegionType;
		
		var clickListener=false;

		
		function initialize() {
			 mapCenter=new google.maps.LatLng<?php echo $centerLatLong ?>;
			
			var myOptions = {
			  center: mapCenter,
			  zoom: 13,
			  mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			
			 map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	//create a geocoder to transform addresses into lat and Long
			geocoder= new google.maps.Geocoder();

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
				<?php //districtPolygon($dID) ?>
				 
				 
			 //add the neighborhood polygons
			 <?php 
			//	foreach ($nhoodArray as $nid => $nhoodBounds)
				//	echo NHpolygon($nhoodBounds);
			 ?>
			  
			 //add in the donor markers 

			
			
//for the click-marker connecting lines	
		  polyOptions = {
		    strokeColor: '#000000',
		    strokeOpacity: 1.0,
		    strokeWeight: 3,
			editable: true,
			map: map
		  }
			
			editablePoly = new google.maps.Polygon(polyOptions);
			//poly.setMap(map);

// Add listeners for the polygon edit events
//			google.maps.event.addListener(editablePoly.getPath(), 'insert_at', function() {updateEncodedPath(editablePoly.getPath())});
//			google.maps.event.addListener(editablePoly.getPath(), 'remove_at', function() {updateEncodedPath(editablePoly.getPath())});
//			google.maps.event.addListener(editablePoly.getPath(), 'set_at', 	function() {updateEncodedPath(editablePoly.getPath())});
//			google.maps.event.addListener(map, 'click', addPolyLatLng);






//next, load polygon to edit if so chosen
			<?php if(isset($_GET['nhToPoly'])	)
			{	
				loadNhoodPolygon($_GET['nhToPoly']);
				echo 'resetEditablePoly(nhoodPolygon'.$_GET['nhToPoly'].');	';
			}
			?>
			<?php if(isset($_GET['dToPoly']))
				{
					loadDistrictPolygon($_GET['dToPoly']);
					echo 'resetEditablePoly(dPoly'.$_GET['dToPoly'].');	';
				}
			?>


		<?php loadAllDistrictPolygons();?>	
//load the district polygons
	<?php //loadDistrictPolygon()?>
	
//load the neighborhood polygons
	<?php //loadNhoodPolygons()?>

//load the donor markers
	<?php //allDistrictsDonorMarkers() ?>

	<?php //$districtid=10001; loadAllNhoodPolygons($districtid);?>
}

	

    </script>
<!-- CHANGE EDITABLE POLYGON -->
	<script type="text/javascript">
		function resetEditablePoly(newPolyToEdit)
		{
			//reset and reassign the editable polygon
			//previousRegionType=currentRegionType;
			editablePoly.setEditable(false);
		//return old polygon to its correct color
			if(previousRegionType=="n")
			{	editablePoly.setOptions({strokeColor: "#0000FF", fillColor: "#0000FF"});	}
			if(previousRegionType=="d")
			{	editablePoly.setOptions({strokeColor: "#FF0000", fillColor: "#FF0000"});	}
		//set editablePoly to the chosen polygon and make it editable 
			if(currentRegionType=="n")
			{	editablePoly=nhoodArray[newPolyToEdit].polygon;
			}
			else if(currentRegionType=="d")
			{	editablePoly=districtArray[newPolyToEdit].polygon;
			}
			editablePoly.setEditable(true);

		// Add listeners for the polygon edit events
			google.maps.event.addListener(editablePoly.getPath(), 'insert_at', function() {updateEncodedPath(editablePoly.getPath())});
			google.maps.event.addListener(editablePoly.getPath(), 'remove_at', function() {updateEncodedPath(editablePoly.getPath())});
			google.maps.event.addListener(editablePoly.getPath(), 'set_at', 	function() {updateEncodedPath(editablePoly.getPath())});
			if(clickListener==true)
				google.maps.event.addListener(map, 'click', addPolyLatLng);
		//set the info div's data
			if(currentRegionType=="n")
			{	document.getElementById("regionType").innerHTML="Neighborhood";
				document.getElementById("currentPolyName").innerHTML=nhoodArray[newPolyToEdit].name;
				document.getElementById("currentPolyID").innerHTML=nhoodArray[newPolyToEdit].id;
			//document.getElementById("regionType").value=nhoodArray[newPolyToEdit].regionType;
			}
			if(currentRegionType=="d")
			{	document.getElementById("regionType").innerHTML="District";
				document.getElementById("currentPolyName").innerHTML=districtArray[newPolyToEdit].name;
				document.getElementById("currentPolyID").innerHTML=districtArray[newPolyToEdit].id;
			}
			updateEncodedPath(editablePoly.getPath());
		//set the new editable polygon's color
			editablePoly.setOptions({strokeColor: "#000000", fillColor: "#000000"});
		//highlight the donors in that neighborhood
	//alert("the membersArray length is: "+numMembers+"\nnewPolyToEdit: "+newPolyToEdit);
			for(var i=0; i<numMembers-1; i++)
			{
				if(membersArray[i].nhood == newPolyToEdit)
				{
					//alert("changing marker from\n "+membersArray[i].marker.getIcon()+"\nto \ngray");
					membersArray[i].marker.setIcon("icons/mapDotNC.png");
				}
				else 
				{	membersArray[i].marker.setIcon("icons/mapDotFD.png");
					//alert("NOT changing marker ");
				}
			}
			previousRegionType=currentRegionType;
		}
	</script>
	
<!-- NEW POLYGON SETUP -->
<script type="text/javascript" >
	function newPolyForRegion()
	{
		editablePoly=new google.maps.Polygon(polyOptions);
		// Add listeners for the polygon edit events
		google.maps.event.addListener(editablePoly.getPath(), 'insert_at', function() {updateEncodedPath(editablePoly.getPath())});
		google.maps.event.addListener(editablePoly.getPath(), 'remove_at', function() {updateEncodedPath(editablePoly.getPath())});
		google.maps.event.addListener(editablePoly.getPath(), 'set_at', 	function() {updateEncodedPath(editablePoly.getPath())});
		google.maps.event.addListener(map, 'click', addPolyLatLng);
	}
</script>
</head>



<body onload="initialize()" >

<div class="adminDashboardWrapper" id="adminDashboardWrapper">

<!-- UPPER LEFT TOOL/DASHBOARD NAV PANEL -->
<h1 style="color: #2f4b66; padding-left: 5px;">Polygons</h1>
<hr>

<div class="mainWrapper" id="mainWrapper">

<div class="leftWidgetWrapper" id="leftWidgetWrapper">

		<div class="leftWidget" id="chooseNhoodDiv" >
			
			<h2 style="text-align:center;">Map Polygon Editor </h2>
			
			
			<form method="post" action="polygon.php">
			
			
							<p style="text-align:center;"> - Editing  <span id="regionTypeDisplay"></span> - 
							<h3 id="currentPolyName" style="color:green; text-align:center;">No region selected.</h3> </p>
				<input type="hidden" name="currentPolyID" id="currentPolyID" />
				
				<input type="hidden" name="regionType" id="regionType" />
			<hr/>
			1) Select a neighborhood:
			
<!-- CHOOSE A NEIGHBORHOOD -->	
			<p style="text-align:center;">					
				<?php echo allNhoodCombobox("nhToPoly",
					"clickListener=false; 
					currentRegionType='n'; 
					resetEditablePoly(this.value);
					document.getElementById('regionType').value='n'; 
					document.getElementById('regionTypeDisplay').innerHTML='Neighborhood';
					document.getElementById('currentPolyID').value=this.value;
					"); ?>
			</p>
		
		
<!-- CHOOSE DISTRICT -->
				Or a district:
			<p style="text-align:center;">
				<?php echo	allDistrictsCombobox("dToPoly", 
						"clickListener=false; 
						currentRegionType='d';
						resetEditablePoly(this.value);
						document.getElementById('regionType').value='d'; 
						document.getElementById('regionTypeDisplay').innerHTML='District';
						document.getElementById('currentPolyID').value=this.value;
						") ?>
			</p>

			
				<hr/>
				2) Drag the handles on the polygon to reshape it.
				<hr/>
				3) If the region (either a District or a Neighborhood) has no polygon, you can create one. <br/>
				<p style="text-align:center;"><input type="button" onclick="newPolyForRegion()" value="New Polygon" /><br/><br/>
					After selecting a region and then clicking the New Polygon button, double click on the map to add handles. Drag and drop points on perimeter to change the polygon's shape.</p>
				<hr/>
				4) Don't forget to save your work! <p style="text-align:center;"><input type="submit" name="save" value="Save" /> </p>
			<!--  	coordinates of where you clicked:
			-->		<input type="hidden" id="clickedCoords" style="width:100%" />
		
			
			
			<!--  BUTTONS	-->
			
				
				
				
<!--  			<input type="radio" name="showD" /> Show Districts<br/>
				<input type="radio" name="showN" /> Show Neighborhoods<br/>
				<input type="radio" name="showM" /> Show Donors<br/>
-->		

				<textarea id="encoded-polyline" rows="1" readonly="readonly" name="encoded-polyline" style=" width:96%; left:2%; display:none;"> </textarea>
		
					
				
				
			</form>
		</div>
		
		<br/>


<!--	END Left Widget Wrapper		-->
	</div>

<!--	START Map Panel Widget Wrapper		-->		
	<div class="mapWidgetWrapper" id="mapCanvasWrapper">
	
<!--	The Map	-->	
	<div class="mapWidget" id="map_canvas">	
		<p style="color:purple">Map attempting to load.....if problem persists, please contact technical support.</p>	
	</div>	

<!--	END The Map Area Wrapper	-->
	</div>
	


<!-- End of Main Content Wrapper -->
</div>

<!-- END adminDashboardWrapper -->
</div>
	
	
</body>

<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script> -->
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script src="js/plugins.js"></script>
  <script src="js/uiFunctions.js"></script>


<!--  Handles click events on a map, and adds a new point to the Polyline.	-->
<script type="text/javascript">

	function addPolyLatLng(event) {

		var path = editablePoly.getPath();

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
		 document.getElementById("clickedCoords").value=event.latLng;
	//update the text field to display the polyline encodings
		var encodeString = google.maps.geometry.encoding.encodePath(path);
		if(encodeString)
		{	document.getElementByID('encoded-polyline').innerHTML = encodeString; }

		updateEncodedPath(path);
	}

</script>



</html>



<!--  Handles click events on a map, and adds a new point to the Polyline.	-->
<script type="text/javascript">

	function addPolyLatLng(event) {

		var path = editablePoly.getPath();

	  // Because path is an MVCArray, we can simply append a new coordinate
	  // and it will automatically appear
		path.push(event.latLng);

	  // Add a new marker at the new plotted point on the polyline.
//		var marker = new google.maps.Marker({
//			position: event.latLng,
//			title: '#' + path.getLength(),
//			map: map,
//			draggable: true
//		});

	//add the coordinates to the coordinates div
		 document.getElementById("clickedCoords").value=event.latLng;
	//update the text field to display the polyline encodings
//		var encodeString = google.maps.geometry.encoding.encodePath(path);
//		if(encodeString)
//		{	document.getElementByID('encoded-polyline').innerHTML = encodeString; }

		updateEncodedPath(path);
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







// again, for all districts, takes combobox name and onsubmit
function allDistrictsCombobox($comboboxName, $onchange)
{
	$query= "SELECT * FROM 	districts ORDER BY DistrictName";
	$result = mysql_query($query);
	$districtsBox='<select name="'.$comboboxName.'" onchange="'.$onchange.';">';
	$districtsBox.='<option value="" >District Name (DC Name)</option>';
	while ($row = mysql_fetch_array($result))
	{
		$DistrictName=$row['DistrictName'];
		$DistrictID=$row['DistrictID'];
		$DCID=$row['DCID'];
		$thisDC=mysql_fetch_array(mysql_query("SELECT PrintName FROM members WHERE  MemberID=".$DCID));
		$DCName=$thisDC['PrintName'];
		$districtsBox.='<option value='.$DistrictID.'> '.$DistrictName.'(DC: '.$DCName.') </option>';
	}
	$districtsBox.='</select>';

	return $districtsBox;
}

?>


<?php 



?>
