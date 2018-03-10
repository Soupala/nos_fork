<!doctype html>

<?php
		include('mapFunctions.php');
		include('functions.php');
		include('config.php');
			opendb();
			$myMapKey=getMapKey();
			$centerLatLong=getCityLatLong();
			$hideInfoWindow=true;
?>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Neighborhood Organizing System: Big Map</title>
	<meta name="description" content="Big Map for the Neighborhood Organizing System">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />
	<script src="js/libs/modernizr-2.5.3.min.js"></script>

	<script type="text/javascript"
		src="https://maps.googleapis.com/maps/api/js?key=<?php echo $myMapKey; ?>&sensor=false&libraries=geometry">
	</script>
	
	<script type="text/javascript"
		src="js/mapFunctions.js">
	</script>
	
	
	    <script type="text/javascript">
			var map;
			var geocoder;
			var mapCenter;
			
			var districtArray={};
			var nhoodArray={};
			var membersArray={};
			var numNhoods;
			var numDistricts;
			var numMembers;
	
			
		
			function initialize() {
				mapCenter=new google.maps.LatLng<?php echo $centerLatLong ?>;
				var myOptions = {
				  center: mapCenter,
				  zoom: 11,
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
						title: "The Center of the Map",
						visible: false
					});
				
				 //add in the donor markers
					//<?php  //	allDonorMarkers(); ?>		
				//add in the NC markers
					//<?php //echo ncImageMarkers(); ?>
				//add in the DC markers
					//<?php // echo dcImageMarkers(); ?>	
	
				 //add the neighborhood polygons
					//<?php 
					//	foreach ($nhoodArray as $nid => $nhoodBounds)
					//	echo NHpolygon($nhoodBounds);
					//?>
				  //load the district polygons
					<?php loadAllDistrictPolygons();
						unassignedDonorMarkers();
					?>
				
			}
	
	    </script>
			

</head>


<body onload="initialize()">
<!--	The Map	-->	
	<div style="position:absolute; height:100%; width:100%;" id="map_canvas">	
		<p style="color:purple">Map attempting to load.....if you've been waiting over 30 seconds,<br /> 
		you might check other webpages to see if your connection to the internet is working. 
		Otherwise, please contact tech support.</p>	
	</div>	
</body>
</html>