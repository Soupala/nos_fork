<?php
$mapFunctionsAreLoaded= true;

include ('config.php');
	//
//Define Your Food Project here: ____________
//											|
// 	function getFpCity()		{	return 'Ashland';}										// the city your Food Project serves
// 	function getFpState()		{	return 'Oregon';}										// your state (for geocode lookup purposes)
// 	function getCityLatLong()	{	return '(42.18808339413591, -122.68449783325195)';}		// the lat/long for the center of your main mapn
// 	function getMapKey()		{	return 'AIzaSyDZ5WjdEBkWjwiBi8pQaueQ3vjUpuJQxGY';}		// your google api key (get one!)

// 	$centerPinColor="FFFF00";
// 	$centerPinSize=".6";	//.5 is regular pin size. larger is larger
// 	$ncPinColor="0088FF";	// color for neighborhood coordinator pins
// 	$fdPinColor="00FF00";	// color for food donor pins
// 	$dcPinColor="FF0000";	// color for district coordinator pins





	//////////////////////////////////////////////////////////////////
	//		CHANGE THE BELOW AT YOUR OWN RISK!						//
	//////////////////////////////////////////////////////////////////

function codeAddressDiv()
{	echo '	<div style="position:absolute; width:55%; border-style:solid;">
				<input id="newAddress" type="textbox" value="Enter An Address" />
			<input type="button" value="Show on Map" onclick="codeAddress(document.getElementById(\'newAddress\').value, \'myCoords\')" />
			(latitude,longitude):		<input id="myCoords" type="textbox" />
			</div>';
}



function jsCodeAddress()
{
	/**	transforms the address in id="address" to coordinates in id="myCoords" when
 *	Encode button is clicked */
	echo '	function codeAddress(address, latLongBox) {
			geocoder.geocode( { \'address\': address}, function(results, status) {
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
	';
}

function saveNhoodCenter($_POST, $nhID)
{
//	opendb();
	$center=$_POST['myCoords'];
//PARAMETERIZE THIS:
	$query=mysql_query("UPDATE neighborhoods SET center='".$center."' WHERE NHoodID='".$nhID."';");
	// if($query)
		// echo '<p style="color:lime">Saved New Center Point</p>';
	// else echo '<p style="color:red">Center Point Not Saved</p>';
}

function saveDistrictCenter($_POST, $dID)
{
//	opendb();
	$center=$_POST['myCoords'];
//PARAMETERIZE THIS:
	$query=mysql_query("UPDATE districts SET center='".$center."' WHERE DistrictID='".$dID."';");
	// if($query)
		// echo '<p style="color:lime">Saved New Center Point</p>';
	// else echo '<p style="color:red">Center Point Not Saved</p>';
}



function saveDistrictNotes($_POST)
{

	$notes=addslashes($_POST['notes']);
	$dID=$_POST['DistrictID'];
	//echo ' notes: '.$notes.'<br/> districtID: '.$dID.'<br/>';
//PARAMETERIZE THIS:
	mysql_query("UPDATE districts SET notes='".$notes."' WHERE DistrictID='".$dID."';");

}


// function newNhood($nhname, $ncID, $dID)
// {
	// //echo '<p style="color:red;">About to create a new Neighborhood called </p>';
	// //$nhname=$_POST['newNhoodName'];
	// //$ncID=$_POST['NCbox'];
	// $sql="INSERT INTO neighborhoods (NHName, NCID, DistrictID) VALUES ('".$nhname."', ".$ncID.", ".$dID.")";
	// $query=mysql_query($sql);
		// if(!$query)
		// //echo '<p style="color:lime">Created new Neighborhood</p>';
	 // echo '<script type="text/javascript">alert("Failed to create new Neighborhood\n\nThe error was:\n
		// '. mysql_error().'\n\n
		// the SQL was:\n'.$sql.'")</script>';

	// //logDBChange($sql);


// }


function allDonorMarkers()
{
	$query=mysql_query("Select NHoodID FROM neighborhoods ORDER BY NHName");
	while($row=mysql_fetch_array($query))
	{
		donorMarkers($row['NHoodID']);
	}
}


function donorMarkers($nhID, $image="icons/mapDotGray.png")
{
	//echo '<br/>You\'re in donorMarkers()<br/>';

	//$sql="SELECT MemberID,FirstName,LastName,latLong,House,StreetName,Apt,City,State,Zip,PreferredEmail,PreferredPhone,FD FROM members,groups WHERE NHoodID=".$nhID." AND members.MemberID=groups.uID ORDER BY routeOrder";
	$sql="SELECT * FROM members,groups WHERE NHoodID=".$nhID."  AND (Status='ACTIVE' OR Status='INACTIVE') AND members.MemberID=groups.uID ORDER BY routeOrder";
	$result=mysql_query($sql);

	// if($sql)

	while($nhoods=mysql_fetch_array($result)	)
		{

			$image="icons/mapDotGray.png";
			$zindex='2';

			if($nhoods['FD']==1 && $nhoods['accepted']==1 && $nhoods['hasBag']==1)
			{	$image="icons/mapDotGreen.png";
				$zindex='2';
			}

			// else if($nhoods['FD']==1 && ($nhoods['accepted']==1 || $nhoods['hasBag']==0))
			// {	$image="icons/mapDotYellow.png";
				// $zindex='3';
			// }

			// else if($nhoods['FD']==1 && ($nhoods['accepted']==0 || $nhoods['hasBag']==1))
			// {	$image="icons/mapDotYellow.png";
				// $zindex='3';
			// }

			else if($nhoods['FD']==1 && ($nhoods['accepted']==0 || $nhoods['hasBag']==0))
			{	$image="icons/mapDotYellow.png";
				$zindex='3';
			}

			//else
			// {
				//$image="icons/mapDotGray.png";
				//$zindex='2';
			//}
			if ($nhoods['NC'])
			{
				$image="icons/mapDotNC.png";
				$zindex='4';
			}
			if($nhoods['DC'])
			{
				$image="icons/mapDotDC.png";
				$zindex='6';
			}
			//if($nhoods['latLong'] != "0")
			if(trim($nhoods['latLong'])=="0") echo '//alert("member # '.$nhoods['MemberID'].' needs to be geocoded");
			';
			else
			{

				//$memberName=$nhoods['FirstName'].' '.$nhoods['LastName'];
				$latlong=$nhoods['latLong'];
				$markerName='point'.$nhoods['MemberID'];
				//$address=$nhoods['House'].' '.$nhoods['StreetName'].' '.$nhoods['Apt'].' '.$nhoods['City'].', '.$nhoods['State'].' '.$nhoods['Zip'];
				//$email=$nhoods['PreferredEmail'];
				//$phone=$nhoods['PreferredPhone'];
				//echo 'setMarker(new google.maps.LatLng'.$nhoods['latLong'].', "'.$tooltip.'");';

				makeMapMarkerImage($image, $latlong, $markerName);
				markerInfoWindow($nhoods['MemberID']);
				//markerInfoWindow($markerName,$memberName,$address,$email,$phone);
			}

		}//end while

}//end function


function unassignedDonorMarkers()
{//folks who haven't been assigned a neighborhood yet

	//Mica: I put ' ' where NHoodID=NULL below, and got an interesting result.  There is one donor in the database who has 0 for their NHoodID, and they show up on the map for some reason.


	$sql="SELECT * FROM members,groups WHERE (NHoodID='NULL' OR NHoodID='' OR NHoodID IS NULL) AND (Status='ACTIVE' OR Status='INACTIVE') AND members.MemberID=groups.uID ";
	$result=mysql_query($sql);

	while($nhoods=mysql_fetch_array($result)	)
		{

			//$image="icons/marker_split.png";
			$image="icons/mapDotYellow.png";
			$zindex='2';

			if(trim($nhoods['latLong'])=="0") echo '//alert("member # '.$nhoods['MemberID'].' needs to be geocoded");
			';
			else
			{
				$latlong=$nhoods['latLong'];
				$markerName='point'.$nhoods['MemberID'];

				makeMapMarkerImage($image, $latlong, $markerName);
				markerInfoWindow($nhoods['MemberID']);
				//markerInfoWindow($markerName,$memberName,$address,$email,$phone);
			}

		}//end while

}//end function


function unacceptedDonorMarkers()
{	//folks who have been assigned a neighborhood but haven't left the welcome committee page yet
	$sql="SELECT * FROM members,groups WHERE (accepted=0 OR hasBag=0) AND (Status='ACTIVE' OR Status='INACTIVE') AND members.MemberID=groups.uID ";
	$result=mysql_query($sql);

	while($nhoods=mysql_fetch_array($result)	)
		{

			//$image="icons/marker_split.png";
			$image="icons/mapDotYellow.png";
			$zindex='2';

			// if(trim($nhoods['latLong'])=="0") echo '//alert("member # '.$nhoods['MemberID'].' needs to be geocoded");
			// ';
			// else
			if(trim($nhoods['latLong'])!="0")
			{
				$latlong=$nhoods['latLong'];
				$markerName='point'.$nhoods['MemberID'];

				makeMapMarkerImage($image, $latlong, $markerName);
				markerInfoWindow($nhoods['MemberID']);
				//markerInfoWindow($markerName,$memberName,$address,$email,$phone);
			}

		}//end while

}//end function





function allMemberMarkers($nhID, $fdColor="00FF00", $ncColor="FF0000", $dcColor="0000FF")
	{
		$pinColor='';
		$pinLabel='';
		$zindex='';

		$sql=mysql_query("SELECT MemberID,FirstName,LastName,latLong,NC,DC,FD FROM members,groups WHERE NHoodID=".$nhID." AND members.MemberID=groups.uID ORDER BY routeOrder");

		while($nhoods=mysql_fetch_array($sql)	)
		{
			$pinColor="FFFFFF";
			$pinLabel='';
			$zindex='1';

			if($nhoods['FD']==1)
			{	$pinColor=$fdColor;
				$pinLabel='FD';
				$zindex='2';
			}
			if($nhoods['NC']==1)
			{	$pinColor=$ncColor;
				$pinLabel='NC';
				$zindex='3';
			}
			if($nhoods['DC']==1)
			{
				$pinColor=$dcColor;
				$pinLabel='DC';
				$zindex='4';
			}



			if(trim($nhoods['latLong']) != "0")
			{
			$tooltip=$nhoods['FirstName'].' '.$nhoods['LastName'];
			//echo 'setMarker(new google.maps.LatLng'.$nhoods['latLong'].', "'.$tooltip.'");';
			echo 'var position'.$nhoods['MemberID'].'= new google.maps.LatLng'.$nhoods['latLong'].';
			';
			makeMapMarkerImage();

			//makeMapMarker($nhoods['MemberID'], $nhoods['MemberID'], $pinLabel, $tooltip, $zindex);

			// echo 'var marker'.$nhoods['MemberID'].' = new google.maps.Marker({
					// position: position'.$nhoods['MemberID'].',
					// map: map,
					// icon: "https://chart.apis.google.com/chart?chst=d_map_spin&chld=.5|0|'.$pinColor.'|11|_|'.$pinLabel.'",
					// title: "'.$tooltip.'",
					// zIndex: '.$zindex.'
				// });
			// ';
			}
		}
	}



function makeMapMarker($markerName, $position, $pinColor="FFFFFF", $pinLabel="*", $tooltip="no data", $zindex="3")
{
	echo 'var marker'.$markerName.' = new google.maps.Marker({
					position: position'.$position.',
					map: map,
					icon: "http://chart.apis.google.com/chart?chst=d_map_spin&chld=.5|0|'.$pinColor.'|11|_|'.$pinLabel.'",
					title: "'.$tooltip.'",
					zIndex: '.$zindex.'
				});
			';
}

function makeMapMarkerImage($image, $position, $markerName)
{
	echo '//******************************************************
	';
	//echo 'var '.$markerName.'= new google.maps.LatLng'.$position.';			';

	echo "
		var ".$markerName."LatLng = new google.maps.LatLng".$position.";
		var ".$markerName." = new google.maps.Marker({
			position: ".$markerName."LatLng,
			map: map,
			icon: '".$image."'
			});

	";

	echo '//******************************************************
	';
}//end function

function ncImageMarkers($image="icons/mapDotNC.png",$nhoodid="", $showInfo=true)
	{


		if(!$nhoodid=='')
			$nhood="AND members.NHoodID=".$nhoodid;
		else $nhood="";

		$sql="SELECT MemberID,FirstName,LastName,latLong,PreferredEmail,House,StreetName,Apt,City,State,Zip,PreferredPhone FROM members,groups WHERE members.MemberID=groups.uID AND groups.NC=1 AND (Status='ACTIVE' OR Status='INACTIVE') ".$nhood;
//	echo 'alert("NCImageMarkers SQL:\n'.$sql.'")';
		$result=mysql_query($sql);
		$count=0;
		$noPinCount=0;
		if($result)
		while($ncs=mysql_fetch_array($result)	)
		{
			echo '//NCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNC
				';

			if(trim($ncs['latLong'])=="0")
			{
				$noPinCount++;
			}
			else
			{
				$position=$ncs['latLong'];
				$markerName='point'.$ncs['MemberID'];
				$memberName=addslashes($ncs['FirstName'].' '.$ncs['LastName']);
				//$lastName=$ncs['LastName'];
				$address=$ncs['House'].' '.$ncs['StreetName'].' '.$ncs['Apt'].' '.$ncs['City'].', '.$ncs['State'].' '.$ncs['Zip'];
				$email=$ncs['PreferredEmail'];
				$phone=$ncs['PreferredPhone'];

				makeMapMarkerImage($image, $position, $markerName);
				if($showInfo)
					//markerInfoWindow($markerName,$memberName,$address,$email,$phone);
					markerInfoWindow($ncs['MemberID'],true);
				$count++;
			}

			echo '//NCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNCNC
				';

		}//end while

	}//end function

function ncMarkers($ncPinColor)
	{

		$sql=mysql_query("SELECT FirstName,LastName,latLong FROM members,groups WHERE members.MemberID=groups.uID AND groups.NC=1 ORDER BY routeOrder");
		$count=0;
		$noPinCount=0;
		while($ncs=mysql_fetch_array($sql)	)
		{
			if(trim($ncs['latLong'])=="0")
			{
				$noPinCount++;
			}
			else
			{
				$tooltip=$ncs['FirstName'].' '.$ncs['LastName'];
				//echo 'setMarker(new google.maps.LatLng'.$nhoods['latLong'].', "'.$tooltip.'");';
				echo 'var position'.$count.'= new google.maps.LatLng'.$ncs['latLong'].';
				';
				echo 'var marker'.$count.' = new google.maps.Marker({
						position: position'.$count.',
						map: map,
						icon: "https://chart.apis.google.com/chart?chst=d_map_spin&chld=.5|0|'.$ncPinColor.'|11|_|NC",
						title: "'.$tooltip.'",
						zIndex: 3
					});
				';
				$count++;
			}

		}

	}


function dcImageMarkers($image="icons/mapDotDC.png", $showInfo=true)
	{


		$sql=mysql_query("SELECT MemberID,FirstName,LastName,latLong,PreferredEmail,House,StreetName,Apt,City,State,Zip,PreferredPhone FROM members,groups WHERE members.MemberID=groups.uID AND groups.DC=1 ");

		$count=0;
		$noPinCount=0;
		while($dcs=mysql_fetch_array($sql)	)
		{
			echo '//DCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDC
					//
					';

			if(trim($dcs['latLong'])=="0")
			{
				$noPinCount++;
				echo '// DC '.$dcs['FirstName'].' '.$dcs['LastName'].' HAS NO LAT/LNG
				';
			}
			else
			{
				$position=$dcs['latLong'];
				$markerName='point'.$dcs['MemberID'];
				$memberName=$dcs['FirstName'].' '.$dcs['LastName'];
				//$lastName=$ncs['LastName'];
				$address=$dcs['House'].' '.$dcs['StreetName'].' '.$dcs['Apt'].' '.$dcs['City'].', '.$dcs['State'].' '.$dcs['Zip'];
				$email=$dcs['PreferredEmail'];
				$phone=$dcs['PreferredPhone'];

				makeMapMarkerImage($image, $position, $markerName);
				if($showInfo)
					//markerInfoWindow($markerName,$memberName,$address,$email,$phone);
					markerInfoWindow($dcs['MemberID']);
				$count++;
			}

			echo '//
				//DCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDCDC
				';

		}//end while

	}//end function

function dcMarkers()
	{

		$sql=mysql_query("SELECT FirstName,LastName,latLong FROM members,groups WHERE members.MemberID=groups.uID AND groups.DC=1");
		$count=0;
		$noPinCount=0;
		while($dcs=mysql_fetch_array($sql)	)
		{
			if(trim($dcs['latLong'])=="0")
			{
				$noPinCount++;
			}
			else
			{
				$tooltip=$dcs['FirstName'].' '.$dcs['LastName'];
				//echo 'setMarker(new google.maps.LatLng'.$nhoods['latLong'].', "'.$tooltip.'");';
				echo 'var position'.$count.'= new google.maps.LatLng'.$dcs['latLong'].';
				';
				echo 'var marker'.$count.' = new google.maps.Marker({
						position: position'.$count.',
						map: map,
						icon: "https://chart.apis.google.com/chart?chst=d_map_spin&chld=.5|0|'.$dcPinColor.'|11|_|DC",
						title: "'.$tooltip.'",
						zIndex: 5
					});
				';
				$count++;
			}

		}

	}


////////////////////////////////////////////
//		get district center's Lat/Long from districtID
function getDistrictCenter($districtID)
{
	$result=mysql_query("SELECT DCID,center FROM districts WHERE DistrictID=".$districtID);
	$row=mysql_fetch_array($result);
	if($row['center'] == 0)
	{
		return getMemberLatLong($row['DCID']);
	}
	else return $row['center'];
}



////////////////////////////////////////////
//		get neighborhood center's Lat/Long from nhoodID

function getNhoodCenter($nhoodID)
{
	$result=mysql_query("SELECT NCID,center FROM neighborhoods WHERE NHoodID=".$nhoodID);
	$row=mysql_fetch_array($result);
	if($row['center'] == 0)
	{
		return getMemberLatLong($row['NCID']);
	}
	else return $row['center'];
}


////////////////////////////////////////////
//		get member Lat/Long from MemberID

function getMemberLatLong($memberID)
{
	$result=mysql_query("SELECT latLong FROM members WHERE MemberID=".$memberID);
	$row=mysql_fetch_array($result);
	return $row['latLong'];
}


////////////////////////////////////////////
//		create neighborhood polygons


function makeNhoodPolygons($strokeColor="00AA00", $strokeOpacity="0.8",$fillColor="006600", $fillOpacity="0.25")
		{
			$result=mysql_query("SELECT * FROM neighborhoods");
			$polyName='';
			while($row=mysql_fetch_array($result))
			{
				if($row['polygon']!=null)
				{
					$polyName='Polygon'.$row['NHoodID'];
					echo 'var '.$polyName.';';


					if($row['polygon'] == "0")
						//come up with some default editable polygon
						echo 'There is no polygon for this area in the database';
					else
						echo $polyName.' = new google.maps.Polygon({
							paths: decodePath("'.$row['polygon'].'"),
							strokeColor: "#'.$strokeColor.'",
							strokeOpacity: '.$strokeOpacity.',
							strokeWeight: 2,
							fillColor: "#'.$fillColor.'",
							fillOpacity: '.$fillOpacity.',
							editable: true
							});
						';


					echo $polyName.'.setMap(map);';
				}//end if
			}//end while
		}//end function



////////////////////////////////////////////
//		create district polygons


function makeDistrictPolygons($strokeColor="AA0000", $strokeOpacity="0.8",$fillColor="a9005b", $fillOpacity="0.25")
		{
			$result=mysql_query("SELECT * FROM districts");
			while($row=mysql_fetch_array($result))
			{
				if($row['polygon']!=null)
				{
					echo 'var '.$row['DistrictName'].'Polygon;
					';

					if($row['polygon'] == "0")
					{	//come up with some default editable polygon
						echo 'There is no polygon for this area in the database';
					}
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
			}
		}


function markerInfoWindow($memberID, $nhNumDonors=false)
{
	$result=mysql_fetch_array(mysql_query("SELECT MemberID,FirstName,LastName,House,StreetName,Apt,City,State,Zip,PreferredEmail,PreferredPhone,NHoodID, NC FROM members, groups WHERE members.MemberID=groups.uID AND MemberID=".$memberID));

	if ($result['NC']==1)
		$nhNumDonors=true;


	$markerName='point'.$result['MemberID'];
	$memberName=$result['FirstName'].' '.$result['LastName'];
	//$lastName=$ncs['LastName'];
	$address=$result['House'].' '.$result['StreetName'].' '.$result['Apt'].' '.$result['City'].', '.$result['State'].' '.$result['Zip'];
	if (isset($result['PreferredEmail']))
		$email=$result['PreferredEmail'];
	else $email="No email in database";
	if(isset($result['PreferredPhone'])&& $result['PreferredPhone']!='')
		$phone=$result['PreferredPhone'];
	else $phone="No Phone Number Set";

	if(isset($result['NHoodID']))
	{	$nhname=mysql_fetch_array(mysql_query("SELECT NHName FROM neighborhoods WHERE NHoodID=".$result['NHoodID']));
	}
	else $nhname['NHName']="No Neighborhood Set";

	if($nhNumDonors)
	{	$numDonors="'<b>Donors in neighborhood:</b> ".getNumNhDonors($result['NHoodID'])."<br/>'+
		";
		$nhMaxDonors="'<b>Max Donors:</b> ".getMaxNhDonors($result['NHoodID'])."<br/>'+
		";
		$dName="'<b>District: </b>".getDistrictNameFromNhoodID($result['NHoodID'])."<br/>'+
		";
		$dcName="'<b>DC: </b>".getDCNameFromNhoodID($result['NHoodID'])."<br/>'+
		";
		$privateNotes="
		";
	}
	else
	{	$numDonors='';
		$nhMaxDonors='';
		$dName='';
		$dcName='';
		$privateNotes='';
	}
	//set html to fill the info window
	echo "var ".$markerName."content = '<div>'+
		'<b>Name: </b>".addslashes($memberName)."<br/>'+
		'<b>Address: </b>".addslashes($address)."<br/>'+
		'<b>Neighborhood: </b>".addslashes($nhname['NHName'])."<br/>'+".
		$numDonors." ".
		$nhMaxDonors.
		$dName.
		$dcName.
		$privateNotes."
		'<b>Email: </b><a href=\"mailto:".$email."\">".$email."</a><br/>'+
		'<b>Phone: </b>".$phone."<br/>'+
		'</div>';
	";

////construct the info window
	echo "var ".$markerName."infowindow = new google.maps.InfoWindow({
		content: ".$markerName."content
		});
	";


////connect the info window to the marker via a 'click' listener
	echo "google.maps.event.addListener(".$markerName.", 'click', function() {
		".$markerName."infowindow.open(map,".$markerName.")
		});
	";
}//end function





function saveRoutePoly($polygon,$nhoodID,$dbh)
{
	//echo '<script type="text/javascript">alert("saveRoutePoly() CALLED\n	 polygon='.addslashes($polygon).'\n nhoodID='.$nhoodID.'\ndbh loaded:'.isset($dbh).'");	</script>';
	// $sql="UPDATE neighborhoods SET polygon='".$polygon."' WHERE NHoodID=".$nhoodID;
	// echo '<script type="text/javascript">alert("saveRoutePoly() SQL:\n'.$sql.'");	</script>';
	// $result=mysql_query($sql);
	// if($result)
		// echo '<script type="text/javascript">alert("route Polyline updated. \n polyline:"'.$polygon.'");	</script>';
	// else echo '<script type="text/javascript">alert("Error updating route polyline:\n'.mysql_error().'\n'.$sql.'");	</script>';

	$sql="UPDATE neighborhoods SET routePolyline=:polyline WHERE NHoodID=".$nhoodID;
	$query=$dbh->prepare($sql);
	$query->bindParam(':polyline', $polyline);

	$polyline=$polygon;

	try
	{	//echo '<script type="text/javascript">alert("About to execute saveRoutePoly query");	</script>';
		$query->execute();	}
	catch(PDOException $e)
	{
		echo '<script type="text/javascript"> alert("saveRoutePoly() failed to UPDATE. \r\n Error: '.$e->getMessage().'\n Debug info: mapFunctions.php->saveRoutePoly()")</script>';
		die();
	}

	//echo '<script type="text/javascript">alert("SaveRoutePoly didn\'t throw any exceptions");	</script>';

	//logDBChange($sql);

}



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


function getZoom($regionType, $regionID)
{
	if($regionType=="neighborhood")
	{	$sql="SELECT zoom FROM neighborhoods WHERE NHoodID='".$regionID."'";
	}
	if($regionType=="district")
	{	$sql="SELECT zoom FROM districts WHERE DistrictID=".$regionID;
	}
	if($regionType=="unconfirmed")
	{	$sql="SELECT value FROM wholeproject WHERE miscName='defaultMapZoom'";
	}
	$result=mysql_fetch_array(mysql_query($sql));
	if($regionType=="unconfirmed")
		return $result['value'];
	else
		return $result['zoom'];
}

function saveZoom($regionType, $zoomValue, $regionID=0)
{
	if($regionType=="neighborhood")
		$sql=mysql_query("UPDATE neighborhoods SET zoom=".$zoomValue." WHERE NHoodID=".$regionID);
	else if($regionType=="district")
		$sql=mysql_query("UPDATE districts SET zoom=".$zoomValue." WHERE DistrictID=".$regionID);
	else if($regionType=="unconfirmed")
		$sql=mysql_query("UPDATE wholeproject SET value=".$zoomValue." WHERE miscName='defaultMapZoom'");
	if(!$sql)
		echo '<script type="text/javascript"> alert("Zoom Level NOT saved. SQL error:'.mysql_error().'");</script>
		';

}


// function populateNewDonorsDiv($nhID, $ncid)
// {
	// $sql="SELECT * FROM members WHERE NHoodID=".$nhID." AND (accepted=0 OR hasBag=0) ";
	// $newDonors=mysql_query($sql);
		// // if($newDonors)
			// // echo '<script type="text/javascript"> alert("populateNewDonorsDiv() sql \n SUCCESSFUL");</script>	';
		// // else
			// // echo '<script type="text/javascript"> alert("populateNewDonorsDiv() sql \n UNSUCCESSFUL\n\n'.mysql_error().'\n\n The SQL query was:\n'.$sql.'");</script>	';

// //borrowed from unconfirmed.php->getUnconfirmedDonors()
	// while($row=mysql_fetch_array($newDonors) )			//limit the number of records displayed
	// {

		// $fdID=$row['MemberID'];
		// $address=$row['House'].' '.$row['StreetName'].' ' .$row['City'].' '.$row['State'];
		// $latLongBox="latLong".$row['MemberID'];
		// if($row['CONFIRMED']==TRUE)
			// $confirmed='checked="checked"';
		// if($row['CONFIRMED']==FALSE)
			// $confirmed="";



			// echo '<p style="text-align:left;">
				// <b>'.  $row['FirstName'].' '.$row['LastName'].'</b><br />
				// '.	$row['House'].' '.$row['StreetName'].'<br />
				// '.	$row['City'].', '.$row['State'].' '.$row['Zip'].'
				// </p>';

			// echo '
				// <br />
				// <b>Email:</b>	<a href="mailto: '.$row['PreferredEmail'].'">'.$row['PreferredEmail'].'</a>
				// <br />
				// <b>Phone:</b>	'.$row['PreferredPhone'].'
				// <br /><br />
				// ';

// // DATE ENTERED AND GEOLOCATION
	// echo '			Date Entered:
				// <input type="text" name="ud_date_entered" id="ud_date_entered" value="'.$row['DateEntered'].'" readonly="readonly" />
				// <br />
				// ';
	// echo	'

				// <form id="ncAcceptDonorForm" action="neighborhood.php?nh='. $nhID .'&id='. $ncid .'&accept=true&tool=newDonorsDiv" method="post" >
				// <input type="hidden" size="30" name="ud_latLong" id="'.$latLongBox.'" value="'.trim($row['latLong']).'"/>
				// <br />
				// <input type="button" name="ShowOnMap" value="Show Donor On Map" style="padding: 5px; margin-top: 5px;" onclick="addMarkerToMap(\''.$address.'\', \''.$latLongBox.'\');" />
				// <br />
				// ';

	// echo	'
				// <a style="text-align:center;" href="javascript:toggleTools(\'memberNotesDiv'.$fdID.'\');">
				// <br /> Member Notes +/-</a><br/>
				// <div id="memberNotesDiv'.$fdID.'" style="display:none">
					// <textarea rows="3" cols="50" name="ud_notes" >'.$row['Notes'].'</textarea>
				// </div>
				// <a style="text-align:center;" href="javascript:toggleTools(\'pickupNotesDiv'.$fdID.'\');">
				// Pickup Notes +/-</a><br/>
				// <div id="pickupNotesDiv'.$fdID.'" style="display:none">
					// <textarea rows="2" cols="50" name="ud_punotes" style="width:100%; ">'.$row['PUNotes'].'</textarea>
				// </div>
				// <input type="hidden" name="ud_memberID" value="'.$row["MemberID"].'	" />';


// //THE ACCEPTED BUTTON

				// echo '
				// <br />
				// <input type="hidden" name="fdID" value="'.$fdID.'" />	';
				// //echo '		<input type="button" name="accept" value="Accept donor" document.getElementById(\'ncAcceptDonorForm\').submit();" />	';
				// echo '	<input type="submit" value="Accepted" style="padding: 5px; margin: 4px;" />	';
				// echo '	</form>		';
// //THE HAS-BAG BUTTON
		// if ($row['hasBag']==0) $bagColor="red";
		// else $bagColor="green";
	// echo '
		// <form id="DonorHasBagForm'.$row['MemberID'].'" action="neighborhood.php?nh='. $nhID .'&id='. $ncid .'&hasbag=true&tool=newDonorsDiv" method="post" >';
	// echo '		<input type="hidden" name="fdid" value="'.$fdID.'" />	';
	// echo '		<input type="button" name="hasbag" value="Has A Bag" style="padding: 5px; margin: 4px; color:'.$bagColor.'" onclick="document.getElementById(\'DonorHasBagForm'.$row['MemberID'].'\').submit();" />	';
	// echo '	</form>				';

// //THE DECLINE BUTTON

	// echo '	<form id="ncDeclineDonorForm'.$row['MemberID'].'" action="neighborhood.php?nh='. $nhID .'&id='. $ncid .'&accept=false&tool=newDonorsDiv" method="post" >';
	// echo '		<input type="hidden" name="fdID" value="'.$fdID.'" />	';
	// echo ' 		<input type="hidden" name="memberNotes" value="'.$row['Notes'].'" />	';
	// echo '		<input type="button" name="decline" value="Decline" style="padding: 5px; margin: 4px;" onclick="document.getElementById(\'ncDeclineDonorForm'.$row['MemberID'].'\').submit();" />	';
	// echo '	</form>		';

		// echo '<hr/><hr/>';
	// }//end while
// }








// function acceptDonor($fdID, $latLong)
// {
	// $wcnotesArray=mysql_fetch_array(mysql_query("SELECT WCNotes FROM members WHERE MemberID=".$fdID));
		// $wcnotes= $wcnotesArray['WCNotes'];
		// $wcnotes.='
// '.getTodaysDate().':  Accepted to neighborhood';

	// $sql="UPDATE members SET accepted=1,WCNotes='".$wcnotes."',latLong='".$latLong."' WHERE MemberID=".$fdID;
	// //echo '<script type="text/javascript"> 		alert("acceptDonor():\n		You ACCEPTED a donor\n		ID: '.$fdID.'\n		sql to execute:'.$sql.'	");</script>	';

	// $result=mysql_query($sql);
	// if($result)
	// {	//echo '<script type="text/javascript"> 		alert("acceptDonor():\n result returned true	");</script>	';
	// }
	// else
		// echo '<script type="text/javascript">
		// alert("acceptDonor():\n result returned FALSE\n\n Error:\n'.mysql_error().'	\n\nSQL attempt:\n'.$sql.'");</script>	';
// }


// function declineDonor($fdID,$ncID,$newNotes,$dbh)
// {
	// //get the NC's name
	// $ncNameSql="Select FirstName,LastName FROM members WHERE MemberID=".$ncID;
	// $ncNameResult=mysql_fetch_array(mysql_query($ncNameSql));
	// $ncName=$ncNameResult['FirstName'].' '.$ncNameResult['LastName'];
	// //get the new donor's WCNotes
	// $notesSql="SELECT WCNotes FROM members WHERE MemberID=".$fdID;
	// $notesResult=mysql_fetch_array(mysql_query($notesSql));
	// $notes=$notesResult['WCNotes'];
	// //get the updated notes from $_POST

	// //if newNotes != notes, update new donor's
	// //	Notes with "Declined by SoAndSo on DateTimeStamp"
	// //if($newNotes!=$notes)
		// $newNotes=$newNotes.'
 // on '.getTodaysDate().':   Declined by '.$ncName;
	// //	set NHoodID=null
	// // // // $sql="UPDATE members SET Notes='".$newNotes."' WHERE MemberID=".$fdID;
	// // // // echo '<script type="text/javascript">
		// // // // alert("declineDonor()\n	You DECLINED a donor\n 	fdID: '.$fdID.'\n	ncID: '.$ncID.'\n\n	notes: \n '.$newNotes.'\n\n sql that would be executed:\n '.$sql.'	\n\n date:\n '.date('M d Y').'	");</script>	';

	// //UPDATE new donor's WCNotes using prepared statement
	// $query=$dbh->prepare("UPDATE members SET WCNotes=:theNotes, NHoodID=NULL WHERE memberID=".$fdID);
	// $query->bindParam(':theNotes', $theNotes);

	// $theNotes=trim($newNotes);

	// try
	// {		$query->execute();	}
	// catch(PDOException $e)
	// {
		// echo '<script type="text/javascript"> alert("declineDonor() failed to UPDATE the notes field. \r\n Error: '.$e->getMessage().'\n Debug info: mapFunctions.php->declineDonor()")</script>';
		// die();
	// }
		// //echo '<script type="text/javascript"> alert("declineDonor() succeeded in UPDATE-ing the notes field. ")</script>';
// }//end function declineDonor()

// //////////////////////
	// $query=$dbh->prepare("UPDATE wholeproject SET data=:html WHERE miscName='welcomeEmail'");
	// $query->bindParam(':html', $html);

	// $html=trim($content);

	// try
	// {
		// $query->execute();
	// }
	// catch(PDOException $e)
	// {
		// echo '<script type="text/javascript"> alert("Default welcome email has NOT been saved. \r\n Error: '.$e->getMessage().'")</script>';
		// die();
	// }
// /////////////////////



function nhoodDonorDB()
{
	echo 'this is the donor database for this neighborhood';
}





































//////////////////////////////////////////
//		DISTRICT POLYGONS				//
//////////////////////////////////////////

function loadAllDistrictPolygons()
{
	$sql=mysql_query("SELECT DistrictID FROM districts");
	while($row=mysql_fetch_array($sql))
	{
		loadDistrictPolygon($row['DistrictID']);
	}

}

function loadDistrictPolygon($did, $iseditable="false")
{
	$sql="SELECT * FROM districts where DistrictID=".$did;
	$result=mysql_query($sql);

	while ($row=mysql_fetch_array($result))
	{
		$distID=$row['DistrictID'];
		$distName=$row['DistrictName'];
		$dcID=$row['DCID'];
		$encodedPoly=stripslashes($row['polygon']);
		$regionCenter=$row['center'];

		if($encodedPoly!='')
		{
			echo ' district'.$distID.'Coords =decodePath("'. $encodedPoly.' ");		';

			echo '
			dPoly'.$distID.' = new google.maps.Polygon({
			paths: district'.$distID.'Coords,
			strokeColor: "#a9005b",
			strokeOpacity: .5,
			strokeWeight: 2,
			fillColor: "#a9005b",
			fillOpacity: 0.25,
			editable: '.$iseditable.',
			visible: true
		});
		';
			if($regionCenter=="0" || $regionCenter==null)
				echo 'var pointD'.$distID.'= mapCenter; ';
			else echo '	var pointD'.$distID.'= new google.maps.LatLng'.$regionCenter.';
			';

			echo 'var contentString=\'District: '.$distName.'\';
			var infoD'.$distID.' = new google.maps.InfoWindow({
			content: contentString,
		});
		//add the info window listener
		google.maps.event.addListener(dPoly'.$distID.', \'click\',
		function() { infoD'.$distID.'.setPosition(pointD'.$distID.'); infoD'.$distID.'.open(map); 	});

		dPoly'.$distID.'.setMap(map);
		';
		}
		else
		{
			echo 'dPoly'.$distID.' = new google.maps.Polygon();	';
		}

		echo 'districtArray['.$distID.']={"regionType":"d", "id":'.$distID.', "name": "'.$distName.'", "polygon": dPoly'.$distID.' };	';


	}//end while

	// load all nhood polygons in the district
	$query=mysql_query("SELECT NHoodID FROM neighborhoods WHERE DistrictID=".$did);
	while($row=mysql_fetch_array($query))
	{
		loadNhoodPolygon($row['NHoodID']);
	}
}


function saveDistrictPolygon($dID, $encodedPoly)
{
	$sql="UPDATE districts SET polygon=".$encodedPoly." WHERE DistrictID='".$dID;
	$result=mysql_query($sql);
	if($result)
	{
		echo '<script type="text/javascript" > alert("Save Successful") </script>';
	}
	else
		echo '<script type="text/javascript" > alert("Save FAILED\n\n SQL:\n'.$sql.'\n\nError:\n'.mysql_error().'") </script>';
}

function districtDonorMarkers($did)
{
	$nhoodArr=mysql_query("SELECT NHoodID FROM neighborhoods WHERE DistrictID=".$did);
	while($row=mysql_fetch_array($nhoodArr))
	{
		donorMarkers($row['NHoodID']);
	}

}

function allDistrictsDonorMarkers()
{
	$query=mysql_query("SELECT DistrictID FROM districts");
	while($district=mysql_fetch_array($query))
	{
		districtDonorMarkers($district['DistrictID']);
	}
}



//////////////////////////////////////////
//		NEIGHBORHOOD POLYGONS			//
//////////////////////////////////////////

function loadAllNhoodPolygons($dID)
{
	$sql="SELECT NHoodID FROM neighborhoods WHERE DistrictID=".$dID;


	$result=mysql_query($sql);
	while($row=mysql_fetch_array($result))
	{
		loadNhoodPolygon($row['NHoodID']);

	}
}

function loadNhoodPolygon($nhoodID, $iseditable="false")
{
	$sql="SELECT * FROM neighborhoods WHERE NHoodID=".$nhoodID;


	//echo 'alert("loadNhoodPolygon() is about to run this sql:\n\n'.$sql.'");	';

	$result=mysql_query($sql);

	while ($row=mysql_fetch_array($result))
	{
		$nID=$row['NHoodID'];
		$nName=$row['NHName'];
		$ncID=$row['NCID'];
		$encodedPoly=stripslashes($row['polygon']);
		$regionCenter=$row['center'];
		//if the polygon has already been defined, put it in the array
		if($encodedPoly!='')
		{
			echo ' nhood'.$nID.'Coords =decodePath("'. $encodedPoly.' ");		';


			echo '
			//make the polygon

			//then put it in the array of neighborhoods
			nhoodArray['.$nID.']={ "id":'.$nID.', "name": "'.$nName.'",
			"polygon":nhoodPolygon'.$nID.' = new google.maps.Polygon({
			paths: nhood'.$nID.'Coords,
			strokeColor: "#297ccf",
			strokeOpacity: .7,
			strokeWeight: 2,
			fillColor: "#00FFFF",
			fillOpacity: 0.35,
			editable: '.$iseditable.',
			visible: true
		}) };
		//make the info window
		';
			if($regionCenter=="0" || $regionCenter==null)
				echo 'var pointN'.$nID.'= mapCenter; ';
			else echo '	var pointN'.$nID.'= new google.maps.LatLng'.$regionCenter.';
			';

			echo 'var contentString=\'<div id="content"><div id="siteNotice">Neighborhood: '.$nName.'</div></div>\';
			var infoN'.$nID.' = new google.maps.InfoWindow({
			content: contentString,
		});
		//add the info window listener
		google.maps.event.addListener(nhoodArray['.$nID.'].polygon, \'click\',
		function() { infoN'.$nID.'.setPosition(pointN'.$nID.'); infoN'.$nID.'.open(map); 	});

		//nhoodPolygon'.$nID.'.setMap(map);
		nhoodArray['.$nID.'].polygon.setMap(map);
		';
		}
		//otherwise, put in an empty polygon
		else
		{	echo 'nhoodPolygon'.$nID.' = new google.maps.Polygon();	';
		echo 'nhoodArray['.$nID.']={"regionType":"n", "id":'.$nID.', "name": "'.$nName.'",
		"polygon": nhoodPolygon'.$nID.' = new google.maps.Polygon({
		//paths: nhood'.$nID.'Coords,
		strokeColor: "#0000FF",
		strokeOpacity: 1.0,
		strokeWeight: 2,
		fillColor: "#0000FF",
		fillOpacity: 0.35,
		editable: '.$iseditable.',
		visible: true
		}) };	';
		echo 'numNhoods++;';
		//echo 'nhoodArray['.$nID.']={"regionType":"n", "id":'.$nID.', "name": "'.$nName.'", "polygon": nhoodPolygon'.$nID.' };	';
		}
	}
	donorMarkers($nhoodID);
	//donorsArray($nhoodID);
}

function saveNhoodPolygon($nhid, $encodedPoly)
{
	$sql="UPDATE neighborhoods SET polygon=".$encodedPoly."WHERE NHoodID=".$nhid;
	$result = mysql_query($sql);
	if($result)
	{

	}
	else
	{

	}
}


function donorsArray($nhoodID)
{
	$sql=mysql_query("SELECT MemberID,FirstName,LastName,latLong,House,StreetName,Apt,City,State,Zip,PreferredEmail,PreferredPhone,FD,NC,DC FROM members,groups WHERE NHoodID=".$nhoodID." AND members.MemberID=groups.uID ORDER BY routeOrder");
	$markerName='';
	$latlong='';
	$image='';

	// if($sql) echo '<script type="text/javascript">alert("We gotts $sql in mapFunctions.php line 1110!")</script>';
	// else echo '<script type="text/javascript">alert("$sql is EMPTY in mapFunctions.php line 1111")</script>';
	if ($sql)
	while($nhoods=mysql_fetch_array($sql)	)
	{
		if($nhoods['FD']==1)
		{
			//$image="icons/mapDotGreen.png";
			$image="icons/mapDotFD.png";
			$zindex='3';
		}
		if($nhoods['NC']==1)
		{
			$image="icons/mapDotNC.png";
			$zindex='5';
		}
		if($nhoods['DC']==1)
		{
			$image="icons/mapDotDC.png";
			$zindex='7';
		}
		if($nhoods['FD']==0 && $nhoods['NC']==0 &&$nhoods['DC']==0)
		{	$image="icons/mapDotGray.png";
		$zindex='10';
		}

		if(trim($nhoods['latLong']) == "0")
		{
			$markerName='point'.$nhoods['MemberID'];
			echo ' var '.$markerName.' = new google.maps.Marker();	';
		}
		else
		{
			$latlong=$nhoods['latLong'];
			$markerName='point'.$nhoods['MemberID'];

			//makeMapMarkerImage($image, $latlong, $markerName);
			echo "
			var ".$markerName."LatLng = new google.maps.LatLng".$nhoods['latLong'].";
			var ".$markerName." = new google.maps.Marker({
			position: ".$markerName."LatLng,
			map: map,
			icon: '".$image."'
		});

		";

			//markerInfoWindow($nhoods['MemberID']);
		}//end else

		//set it in the array
		echo 'membersArray[numMembers-1]={"id":'.$nhoods['MemberID'].', "name": "'.$nhoods['FirstName'].' '.$nhoods['LastName'].'", "marker": '.$markerName.', "nhood":'.$nhoodID.' };	';
		echo 'numMembers++;';
	}//end while
}


function saveRegionPolygon($_POST)
{
	if($_POST['regionType']=="n")
	{


		$dbh=openPDO();
		$query=$dbh->prepare("UPDATE neighborhoods SET polygon=:polygon WHERE NHoodID=".$_POST['currentPolyID']);
		$query->bindParam(':polygon', $polygon);

		$polygon=addslashes($_POST['encoded-polyline']);

		try
		{	//echo '<script type="text/javascript">alert("About to execute saveRoutePoly query");	</script>';
			$query->execute();
		}
		catch(PDOException $e)
		{
			echo '<script type="text/javascript"> alert("saveRegionPolygon() failed to UPDATE. \n\n Error: '.$e->getMessage().'\n Debug info: mapFunctions.php->declineDonor()")</script>';
			die();
		}


		// 		$sql="UPDATE neighborhoods SET polygon=".$_POST['encoded-polyline']."WHERE NHoodID=".$_POST['currentPolyID'];
		// 		$result=mysql_query($sql);
		// 		if($result) echo '<script type="text/javascript"> alert("region saved. the sql was:\n '.$sql.'");</script>';
		// 		else echo '<script type="text/javascript"> alert("region NOT saved. the sql was:\n '.$sql.'");</script>';

	}
	else if($_POST['regionType']=="d")
	{
		$dbh=openPDO();
		$query=$dbh->prepare("UPDATE districts SET polygon=:polygon WHERE DistrictID=".$_POST['currentPolyID']);
		$query->bindParam(':polygon', $polygon);

		$polygon=addslashes($_POST['encoded-polyline']);

		try
		{	//echo '<script type="text/javascript">alert("About to execute saveRoutePoly query");	</script>';
			$query->execute();
		}
		catch(PDOException $e)
		{
			echo '<script type="text/javascript"> alert("saveRegionPolygon() failed to UPDATE. \n\n Error: '.$e->getMessage().'\n Debug info: mapFunctions.php->declineDonor()")</script>';
			die();
		}


		// 		$sql="UPDATE districts SET polygon=".$_POST['encoded-polyline']."WHERE DistrictID=".$_POST['currentPolyID'];
		// 		$result=mysql_query($sql);
		// 		if($result) echo '<script type="text/javascript"> alert("region saved. the sql was:\n '.$sql.'");</script>';
		// 		else echo '<script type="text/javascript"> alert("region NOT saved. the sql was:\n '.$sql.'");</script>';

	}

}


		?>
