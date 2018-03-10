<!DOCTYPE html>
<?php 
	include("securepage/nfp_password_protect.php"); 
//fix this for long term:	
	$dID="10001";
	
	if(isset($_GET['save']))
		savePolygon();
	
	if(!isset($functionsAreLoaded))
		{	include("functions.php");	}
	opendb();
	if(!isset($mapFunctionsAreLoaded))
		{	include("mapFunctions.php");	}
	if(isset($_GET['nhood']))
		{									}
	
		$myMapKey=getMapKey();
		$centerLatLong=getCityLatLong();
?>
<html>
  <head>
    <title>Google Maps JavaScript API v3 Example: Encoded Polylines</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
    <link href="memberStyles.css" rel="stylesheet" type="text/css">
    <script type="text/javascript"
		src="https://maps.googleapis.com/maps/api/js?key=<?php echo $myMapKey; ?>&sensor=false&libraries=geometry"></script>
	<script type="text/javascript" src="mapFunctions.js"></script>
    <script type="text/javascript">
		var currentPoly;
		var newPoly;
		var map;
		var nhIsVisible=true;
		var dIsVisible=true;
		//var neighborhoods=array();
		//var districts[];
		
		function initialize() {
					var mapCenter=new google.maps.LatLng<?php echo $centerLatLong ?>;
			
					var myOptions = {
					  center: mapCenter,
					  zoom: 13,
					  mapTypeId: google.maps.MapTypeId.ROADMAP
					};

					var map = new google.maps.Map(document.getElementById('map_canvas'),
					  myOptions);
//add the 'center' marker
					var centerMarker = new google.maps.Marker({
							position: mapCenter, 
							map: map,
							icon: "http://chart.apis.google.com/chart?chst=d_map_spin&chld=<?php echo $centerPinSize; ?>|0|<?php echo $centerPinColor; ?>|11|_|CENTER",
							title: "The Center of the Map"
					});
					  
					var polyOptions = {
					  strokeColor: '#000000',
					  strokeOpacity: 1.0,
					  strokeWeight: 3,
					  map: map,
					  editable: true
					};
					newPoly = new google.maps.Polygon(polyOptions);

// Add a listener for the click event 
//parameters:(*the map canvas* , *the mouse event* , *function to send listener to*)
					google.maps.event.addListener(map, 'click', addLatLng);
/* // Add a listener for the polygon edit event
					google.maps.event.addListener(newPoly, event, moveLatLng);
*/


//make the neighborhood and district polygons
				<?php makeNhoodPolygons(); ?>
				<?php //makeDistrictPolygons(); ?>
//show all donors in the neighborhood
				<?php donorMarkers(10015); ?>
		}	//end initialize()


      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</head>
<body>
<!-- THE REGION SELECT TOOLS DIV -->
	<div class="widget" name="districtInfo" style="position:absolute; left:20px; width:30%; height:95%; overflow:auto;">	
		<div>
			<em>Click map to draw a polyline</em>
		</div>
<hr/>the form<hr/>
		<form id="savePolygonForm" action="newPolygon.php?save=true" method="post">
			
			<input type="button" name="showNeighborhoods" value="Show Neighborhoods" />
			<br/>
			Region Name
			<input type="text" id="currentPolygonName" name="currentPolygonName" value="currentPolygonName" title="The name of the region you're editing"/>
			<br/>
			Region ID
			<input type="text" id="currentRegionID" name="currentRegionID" value="currentRegionID" title="The database ID number of the current region"/>
			<br/>
			Region Type
			<input type="text" id="NorD" name="NorD" value="is Nhood or District" title="Neighborhoods are stored separately from Districts, so we need to know which one to save to."/>
			
			<div>
				Encoding: 
				
			</div>
			<textarea id="encoded-polyline" name="encoded-polyline" rows="1" style="width:100%;" title="This is the encoded form of the current region's boundaries. You probably don't care about this.">
			</textarea>
			<input type="submit" value="Save the polygon" />
		</form>
<hr/>end form<hr/>
		<input type="button" onclick="updateEncodedPath(newPoly.getPath());" />
					See PolyCode
		
		<br/>
		Change the current region to:
		<br/>
		<input type="button" value="new" onclick="submit()" />
		<br/>
		
		<form id="nhoodPolygonForm" action="newPolygon.php?nhood=true" method="post">
		<?php 
			$onchange="submit()";
			echo allNhoodCombobox($onchange); 
		?>
	</div>
	
	



	
	
	
<!-- THE MAP DIV -->
    <div class="widget" id="map_canvas" style="height:95%; width:60%; right:2%; top:2%;">
	<p style="color:red">For some reason, the map isn't loading.</p>
	</div>
	
	
</body>
</html>



<?php
	function savePolygon()
	{	
/*		if($_GET['save']!="n")
		{
*/
			$nhoodID=$_POST['NHbox'];
			$polygon=$_POST['encoded-polyline'];
			$result=mysql_query("UPDATE neighborhoods SET polygon=\"".$polygon."\" WHERE NHoodID=".$nhoodID);
/*		}
		if($_GET['save']=="d")
		{
			$dID=$_POST[''];
			$polygon=$_POST['encoded-polyline'];
			$result=mysql_query("UPDATE districts SET polygon=\"".$polygon."\" WHERE DistrictID=".$dID);
		}
*/
	}

?>
