<!DOCTYPE html>
<?php 
	include("securepage/nfp_password_protect.php");
	include('functions.php');
	include('mapFunctions.php');
	$centerLatLong=getCityLatLong();
	opendb();
	$id="100000";
	
	
//do any saves necessary	

	if(isset($_GET['savegeo']))
	{
		saveGeo($_POST);
	}
	
?>


<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Ungeocoded Donors of the Neighborhood Food Project</title>
	<meta name="description" content="Member DB App">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />
	<script src="js/swapdivs.js"></script>
	<script src="js/scripts.js"></script>
	<script type="text/javascript"	src="js/mapFunctions.js"> </script>
	<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"> </script>
<!-- initialize() function -->
	<script type="text/javascript">
		var poly;
		var map;
		var geocoder;
		var centerMarker;
		function initialize() 
		{
				  var myTown = new google.maps.LatLng<?php echo $centerLatLong; ?>;
				  var myOptions = {
					zoom: 13,
					center: myTown, //new google.maps.LatLng<?php echo $centerLatLong; ?>,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				  };
		//create a geocoder to transform addresses into lat and Long
				geocoder= new google.maps.Geocoder();
		//put the map in the proper div		
				  map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
	
	
		//add the 'center' marker
			centerMarker = new google.maps.Marker({
					position: myTown, //<?php echo $centerLatLong; ?>,
					map: map,
					title: "The Center of the Map"
				});
				
				
		//add in the NC markers
			<?php  ncImageMarkers(); //ncMarkers($ncPinColor); 
			?>
			
			
	
			
		}//end initialize()
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
	</script>
</head>

<body onload="initialize()"> 

<div class="adminDashboardWrapper" id="adminDashboardWrapper">

<!-- UPPER LEFT TOOL/DASHBOARD NAV PANEL -->
<h1 style="color: #2f4b66; padding: 5px 5px 15px 15px;">Un-Geocoded Donors</h1>
<br />

<div class="mainWrapper" id="mainWrapper">
<div class="leftWidgetWrapper" id="leftWidgetWrapper">
	
		
<div class="leftWidget" id="ungeocodedDonors">
	<h2>Geocode and Check for Accurate Map Location</h2>
	<p>Currently, there are <strong><?php echo getUngeoCount()?></strong> members of 
	the Food Project who need to have their addresses geocoded and saved into the database so their dot will show up on the maps.<br /></p>
	<br /><p style="font-size: 15px;"><!--If there are un-geocoded donors below, click "Show On Map" to load their geocode (lat/long) and display them on the map. If the location on the map appears to be accurate,
	then click "Save Geocode" button to save it into the system.	If the physical address is not a valid physical location or not in the 
	Google Maps database, try using this <b><a href="http://itouchmap.com/latlong.html" target="_blank">3rd party tool</a></b> to produce a lat/long for the donor. Then, visit the bottom of the donor's Profile to input the lat/long.--></p>

	<hr>
<?php

  $donorCount=0;
	$sql="SELECT * FROM members WHERE latLong='(42.938696,-122.146522)' AND (Status='ACTIVE' OR Status='INACTIVE') ORDER BY LastName  LIMIT 10";
	$result=mysql_query($sql);
	
	$donorCount++;
	if ($donorCount%2==0)
	$divColor="#dbd6d0";
	else $divColor="#c3ff91";
// 	if($result)
// 		echo '<script type="text/javascript">alert(\'unconfirmed.php->ungeocoded sql SUCCEESS\')</script>';
// 	else echo '<script type="text/javascript">alert(\'unconfirmed.php->ungeocoded sql FAILED\n ERROR:\n'.mysql_error().'\')</script>';

	while($row=mysql_fetch_array($result))		//limit the number of records displayed 
	{
	echo '<div style="position:relative; background-color:transparent; padding: 20px; border:2px solid; font-size:8pt;">'	;
		
		$memID=$row['MemberID'];
	
		//$fdID=$row['MemberID'];
		$address=$row['House'].' '.$row['StreetName'].' ' .$row['City'].' '.$row['State'];
		$latLongBox="latLong".$row['MemberID'];
		// if($row['CONFIRMED']==TRUE)
			// $confirmed='checked="checked"';
		// if($row['CONFIRMED']==FALSE)
			// $confirmed="";
			
		
			echo '<div>';
			echo '<p style="text-align:left;">
				'.  $row['FirstName'].' '.$row['LastName'].'<br/>
				'.	$row['House'].' '.$row['StreetName'].'<br/>
				'.	$row['City'].', '.$row['State'].' '.$row['Zip'].'
				</p>';
				
			echo ' 		
				
				 Email:	'.$row['PreferredEmail'].'
				<br/>
				Phone:	'.$row['PreferredPhone'];
			echo '</div>';
			echo '<div style="position:absolute; top:15px; right:15px;">';
				echo '<a href="editMember.php?fdid='.$memID.'&uid='.$id.'" target="_blank" title="View/Edit this member\'s information" style="float:right"> 
			<img src="icons/edit.png" alt="Edit This Member" width="30px" height="30px" />	</a>';
			echo '</div>';
			echo '<br/>
				<input type="hidden" name="memberID" value="'.$row["MemberID"].'	" />';

				
// DATE ENTERED
echo '			Date Entered: '.$row['DateEntered'].'	<br/>';
echo '
		<form id="ungeocodedForm'.$memID.'" action="viewUngeocoded.php?id='.$id.'&savegeo=true" method="post" >
				Lat/Long	
				<input type="text" size="30" name="ud_latLong" id="'.$latLongBox.'" value="'.trim($row['latLong']).'"/>
				<input type="hidden" name="memberID" value="'.$memID.'" />
				<br/>
				<input type="button" name="ShowOnMap" value="Show On Map" onclick="addMarkerToMap(\''.addslashes($address).'\', \''.$latLongBox.'\');" />
				';
//echo '	<input type="submit" value="Save Geocoding" />	';
	echo '<input type="button" value="Save Geocode" onclick="addMarkerToMap(\''.addslashes($address).'\', \''.$latLongBox.'\'); setTimeout(function(){document.getElementById(\'ungeocodedForm'.$memID.'\').submit();}, 1000);" />';
	echo '	</form>		';
 
	

	echo '</div>';
	echo '<br/>';
	}//end while
	
?>
</div>

<!--	END Left Widget Wrapper		-->
	</div>

<!--	START Map Panel Widget Wrapper		-->		
	<div class="mapWidgetWrapper" id="mapCanvasWrapper">
	
<!--	The Map	-->	
	<div class="mapWidget" id="map_canvas">	
		<p style="color:purple">Map attempting to load.....contact your administator for assistance.</p>	
	</div>	

<!--	END The Map Area Wrapper	-->
	</div>
	


<!-- End of Main Content Wrapper -->
</div>

<!-- END adminDashboardWrapper -->
</div>

</body>
</html>

<?php 
function saveGeo($_POST)
{
	$latlong=$_POST['ud_latLong'];
	$memID=$_POST['memberID'];
	
	$sql="UPDATE members SET latLong='".$latlong."' WHERE MemberID=".$memID;
	$result=mysql_query($sql);


//debug
	// if(!$result)
		// echo "Save failed.<br/>sql:".$sql;
	// else echo "save SUCCEEDED<br/> sql:".$sql;
}

function getUngeoCount()
{
	$sql=mysql_query('SELECT MemberID FROM `members` WHERE latLong = "(42.938696,-122.146522)" AND (Status="ACTIVE" OR Status="INACTIVE")');
	//$result=mysql_fetch_array($sql);
	$count=mysql_num_rows($sql);
	//return $result['COUNT(MemberID)'];
	return $count;
}
?>
  