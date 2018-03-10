<!--
***********************************
*****	mapTools
***********************************
-->
<?php
	if(isset($_GET['nh']))
	{
		$regionID=$_GET['nh'];
		$regionType="neighborhood";
		$regTyp="nh";				//abbreviated version of type for $_GET
		$eid=$ncid;
		
	}
	else if(isset($_GET['d']))
	{
		$regionID=$_GET['d'];
		$regionType="district";
		$regTyp="d";
		$eid=$dcid;
	}
	
	else
	{
		$regionID='0';
		$regionType="unconfirmed";
		$regTyp='u';
		$eid='0';
	}
	
	
?>


<!-- 	MAIN MAP FEATURE OPTIONS DIV	-->
<div style="position:relative;">

<!--	MAX DONORS	-->
<?php 
if($regionType=="neighborhood")
{	opendb();
	$sql=mysql_query("SELECT maxDonors FROM neighborhoods WHERE NHoodID=".$regionID);
	$result=mysql_fetch_array($sql);
	$maxdonors=$result['maxDonors'];
	
	
	echo '

	<div id="maxDonors" style="position:relative;">
		<form id="maxDonors" action="'. $regionType .'.php?'. $regTyp .'='.$regionID .'&id='.$eid.'&uid='.$uid.'&savemaxdonors=yes&tool=mapToolsDiv" method="post">
		
			<input type="hidden" name="regionid" value="'.$regionID.'" />
			Max donors: <input type="text" name="maxdonors" value="'.$maxdonors.'" />
			<input type="submit" value="Save" style="font-size:8pt; padding: 2 2 2 2; " />
			<br/><hr/>
		</form>
	</div>
	';
}
?>


<!-- 	ZOOM	-->
	<div style="position:relative;">		
		<a style="text-align:center;" href="javascript:toggleTools('zoom');"> Zoom </a>
	</div>	
	<div  id="zoom" style="position:relative; display:none;">
		<form id="saveZoom" action="<?php echo $regionType ?>.php?<?php echo $regTyp; ?>=<?php echo $regionID; ?>&id=<?php echo $eid; ?>&uid=<?php echo $_GET['uid']; ?>&saveZoom=yes&tool=mapToolsDiv" method="post">
	<a href="javascript:toggleTools('zoomexplaination');">Click this for further instructions +/-</a><br />
	<div id="zoomexplaination" style="display: none;">
				<p style="font-size:11pt">
				-use this option to change the default zoom level for this map.<br/>
			  -higher numbers zoom in,<br/>
				-lower numbers zoom out.
			</p>
			<br/>
	</div>	
	

			<?php $thezoom=getZoom($regionType, $regionID); ?>
				
			<input type="text" id="zoomLevelText" name="zoomLevel" value="<?php echo $thezoom ?>" onchange="updateZoom();"/>
			<input type="button" value="Save Zoom Level" onclick="submit()"/>
			<br/>
		</form>
	</div>
	<hr/>
	
	
	
	
	<!-- 	MARKERS	-->			
	<div style="position:relative; display: none;">
		<a style="text-align:center;" href="javascript:toggleTools('markers');">Markers</a>
	</div>
	<div  id="markers" style="position:relative; display:none;">
			<input type="checkbox" value="showCenter" onclick="javascript:showhideMarker(centerMarker,map);"/> Show/Hide Center Marker
			<br />
	
	</div>
	
	
	<div id="mapOptions" style="position:relative; ">	
	<a style="text-align:center;" href="javascript:toggleTools('geocodeMe');">Locate Physical Address/Save It As Map Center </a>

		<div id="geocodeMe" style="position:relative; display:none">
					<form id="saveCenter" action="<?php echo $regionType ?>.php?<?php echo $regTyp; ?>=<?php echo $regionID; ?>&id=<?php echo $eid; ?>&uid=<?php echo $_GET['uid']; ?>&savecenter=yes&tool=mapToolsDiv" method="post">	
						<a href="javascript:toggleTools('findaddressmapcenter');">Click this for further instructions +/-</a><br /><br />
						<div id="findaddressmapcenter" style="display: none;">
						  <h4>Hints for how to find, set, and save the center of your map</h4>
								<p style="font-size:11pt">
								-hover over the green dots (donor locations) on map to try and find address relatively close to the center of your area<br />
							  -copy and paste physical address from map pop-up window to the box below<br />
								-click the "Show on Map" option<br />
								-when the value of the geocode (latitude, longitude) changes, you can then proceed to "Save Geocode As Map Center"<br />
								-you can use this tool simply to find a location, and not necessarily save it as the map center
							</p>
							<br/>
						</div>	

						Enter Physical Address:
						<input id="newAddress" type="textbox" value="" />
						<input type="button" value="Show on Map" onclick="codeAddress(document.getElementById('newAddress').value, 'myCoords')" style="padding:5px; margin: 10px 5px 10px 0px;"/>
						<br />
						Geocode (latitude,longitude):		
						<input id="myCoords" name="myCoords" type="text" value="<?php echo $centerLatLong; ?>" size="40" />
						<input type="submit" value="Save as Map Center" style="padding:5px; margin: 10px 5px 10px 0px;" />
					</form>
		</div>

	
	
	</div>

	<!-- 	POLYGONS	-->
<!--	<div >
		<a style="text-align:center;" href="javascript:toggleTools('polygons');"> Polygons </a>
	</div>
	<div  id="polygons" style="position:relative; display:none;">
			<input type="checkbox" value="showNPolys" /><b style="color:red"/>show/hide neighborhood polygons</b>
			<br/>
			<input type="checkbox" value="showDPolys" /><b style="color:red"/>show/hide district polygons</b>
			<br/>
	</div>
	 -->
	<!-- 	POLYLINES	-->
	<hr/>
<!--	<div style="position:relative;">
		<a style="text-align:center;" href="javascript:toggleTools('polylines');"> Polylines </a>
	</div>
	<div  id="polylines" style="position:relative; display:none;">
	
		<a href="javascript:toggleTools('showeditpolyline');">+/-</a><br />
		<div id="showeditpolyline" style="display: none;">
			<p >&nbsp;&nbsp;&nbsp;&nbsp;You can display your pickup route on the map as a polyline.<br/>
			The polyline initially connects donor pins in straight lines by the order set in the Route Order tool. To show your neighborhood's polyline, click "show/hide route polyline" below.<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;To edit your route polyline, click "set route polyline to editable" below. Then drag the boxes to set the lines on the route you take to pickup bags.<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;Finally, clicking the "Update" box will automatically encode the line in a format that can be saved, and clicking "Save Route" will set it in the database. <br/><br/></p>
		</div>
-->	
<!--		<form id="saveRoutePoly" action="<?php //echo $regionType ?>.php?<?php //echo $regTyp; ?>=<?php //echo $regionID; ?>&id=<?php// echo $uid; ?>&saveRoutePoly=yes&tool=mapToolsDiv" method="post">
		
			<input type="checkbox" value="showPolylines" onclick="showhidePoly(routePoly)"/>show/hide route polyline
			<br/>
			<input type="checkbox" value="editPolylines" onclick="setPolyEditable(routePoly);"/>set route polyline to editable
			<br/>
			polyline: <input type="text" id="encoded-polyline" name="encoded-polyline" readonly="true" />
			<input type="checkbox" value="updateRoute" onclick="updateEncodedPath(routeCoordinates); this.checked=false;"/>Update
			<br/>
			<input type="submit" value="Save Route" />
			<br/>
-->		<!--	<input type="checkbox" value="showDirections" /><span style="color:red"/>show/hide directions</span> -->
<!--			<br/>
		</form>
	</div>
-->
</div>

<!-- 	GEOCODE	-->
<!--
<div>
	<a style="text-align:center;" href="javascript:toggleTools('geocode');"> Geocode </a>
</div>
<div class="widget" id="geocode" style="position:relative; display:none;">
	<form id="saveCenter" action="<?php //echo $regionType ?>.php?<?php //echo $regTyp; ?>=<?php //echo $regionID; ?>&id=<?php //echo $uid; ?>&savecenter=yes&tool=mapToolsDiv" method="post">

		1. Enter an address to be shown on the map.<br/>
		<input id="aNewAddress" type="textbox" value="Enter An Address" /><br/>
		2. Click the "Geocode" button to compute the longitude and latitude and display a pin.<br/>
		<input id="myCoords" name="myCoords" type="text" value="<?php //echo $centerLatLong; ?>" size="40" />(lat,long)<br/>
		3. Click the "Save as Center" button to save this address as the center of the neighborhood<br/>
		
		<input type="submit" value="save as center" />
		<input type="button" value="Show on Map" onclick="codeAddress(document.getElementById('aNewAddress').value, 'myCoords')" />
	
	</form>
</div> 
-->