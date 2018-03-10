<!DOCTYPE html>
<?php 
	include("securepage/nfp_password_protect.php"); 
	include("functions.php");
	include("mapFunctions.php");
	//
	$myMapKey=getMapKey();

//return to tool used before reloading the page
	if(isset($_GET['tool']))
		$tool=$_GET['tool'];
	else $tool='newDonorsDiv';
 
	if(isset($_GET['nh']))
	$nhID=$_GET['nh'];
	$uid=$_GET['uid'];
	opendb();
	
// do any saves before building the page
	if(isset($_GET['save']))
		saveNHdata($_POST);
	if(isset($_GET['savecenter']))
		saveNhoodCenter($_POST, $nhID);
	if(isset($_GET['savenotes']))
		saveNhoodNotes($_POST);
	if(isset($_GET['saveRoutePoly']))
	{
		$dbh=openPDO();
		saveRoutePoly($_POST['encoded-polyline'], $nhID, $dbh);
	}
	if(isset($_GET['saveroute']))
		saveRouteOrder($_POST, $nhID);
	if(isset($_GET['savemaxdonors']))
		saveMaxDonors($_POST, $nhID);
	if(isset($_GET['saveZoom']))
		saveZoom("neighborhood", $_POST['zoomLevel'], $nhID);
	if(isset($_GET['accept']))
	{	if($_GET['accept']=="true")
			acceptDonor($_POST['fdID'], $_POST['ud_latLong'], $ncid);
		if($_GET['accept']=="false")
		{	$dbh=openPDO();
			declineDonor($_POST['fdID'],$ncid, $_POST['memberNotes'], $dbh);
		}
	}
	if (isset($_GET['hasbag']))
	{	setHasBag($_POST['fdID']);
	}

	
	
//end saves section	

	
	
		$nHood=mysql_fetch_array(mysql_query("SELECT * FROM neighborhoods WHERE NHoodID=".$nhID));
		$nhName=$nHood['NHName'];
		$ncid=$nHood['NCID'];
		$districtID=$nHood['DistrictID'];
		$nhoodNotes=$nHood['notes'];
		if($nHood['center']=="0")
			$centerLatLong=getCityLatLong();
		else $centerLatLong=$nHood['center'];
		$encodedPolyline = $nHood['routePolyline'];
		
	$zoomLevel=getZoom("neighborhood", $nhID);
	$imageAcceptNew="<img src=\"icons/newDonorsNo.png\" alt=\"No new donors\" />";


?>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Viewing the Neighborhood Dashboard of the Neighborhood Organizing System</title>
	<meta name="description" content="Member DB App">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />
	<script src="js/swapdivs.js"></script>
	<script src="js/scripts.js"></script>
	<script src="js/libs/modernizr-2.5.3.min.js"></script>

	<script type="text/javascript"
		src="https://maps.googleapis.com/maps/api/js?key=<?php echo $myMapKey; ?>&sensor=false&libraries=geometry">
	</script>
	<script type="text/javascript"	src="js/mapFunctions.js"></script>
    <script type="text/javascript">
		var geocoder;
		var map;
		var centerMarker;
		var tempMarker;
		var routePoly;
		var routeCoordinates;
		var zoomLevel=<?php echo $zoomLevel ?>;

		var nhoodArray={};
		var membersArray={};
		var numMembers;
		var numNhoods;
		var mapCenter;
		
		function initialize() {
			 mapCenter=new google.maps.LatLng<?php echo $centerLatLong ?>;
			var myOptions = {
			  center: new google.maps.LatLng<?php echo $centerLatLong ?>,
			  zoom: zoomLevel,
			  mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			
			map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	//create a geocoder to transform addresses into lat and Long
			geocoder= new google.maps.Geocoder();
			
	//add the 'center' marker and temp marker
			centerMarker = new google.maps.Marker({
					position: mapCenter, 
					map: map,
				//	icon: "https://chart.apis.google.com/chart?chst=d_map_spin&chld=<?php echo $centerPinSize; ?>|0|<?php echo $centerPinColor; ?>|11|_|C",
					icon: "icons/marker_split.png",
					visible:false,
					title: "The Center of the Map"
				});
			tempMarker=new google.maps.Marker({
				position:mapCenter,
				map: map,
				visible: false,
				icon: "icons/marker_split.png",
				title: ""

			});

	//add in the neighborhood polygon
	<?php loadNhoodPolygon($nhID)?>
			
	//add in the route-order polyline 
			<?php nhoodRoute($nhID,$encodedPolyline); ?>
//nhoodRoute($nhID, $routePolyline=0)			$encodedPolyline = $nHood['routePolyline'];
	//add in the donor markers
			<?php //donorMarkers($nhID); ?>//echo nhoodMarkers($nhID); ?>
			  
	//set the active Widget from before the page reloaded
		nhShowHideDivs("<?php echo $tool ?>");
}

    </script>
<!-- <script type="text/javascript">
		function showhideNewDonors()
		{
			var theDiv=document.getElementById("newDonorsDiv");
			if(theDiv.style.display=="block")
			{	
alert("neighborhood.php js:showhideNewDonors() \n has been called\n\nSETTING TO NONE");
				theDiv.style.display="none";	
			}
			else 
			{
alert("neighborhood.php js:showhideNewDonors() \n has been called\n\nSETTING TO BLOCK");
			theDiv.style.display="block";
			
			}
		}
	</script> -->
	
<script language="JavaScript" type="text/javascript">

/* 	SHOW/HIDE 	*/

//replaces one div with another for neighborhood dashboard left panel
	function nhShowHideDivs(idOfDivToShow)
	{
		if(idOfDivToShow == "donorsDiv")
			document.getElementById("donorsDiv").style.display = "block";
		else
			document.getElementById("donorsDiv").style.display = "none";
			
			
		if(idOfDivToShow == "notesDiv")
			document.getElementById("notesDiv").style.display = "block";
		else
			document.getElementById("notesDiv").style.display = "none";
			
			
		if(idOfDivToShow == "mapToolsDiv")
			document.getElementById("mapToolsDiv").style.display = "block";
		else
			document.getElementById("mapToolsDiv").style.display = "none";
			
			
		if(idOfDivToShow == "newDonorsDiv")
			document.getElementById("newDonorsDiv").style.display = "block";
		else
			document.getElementById("newDonorsDiv").style.display = "none";
			
			
		if(idOfDivToShow == "donorDbDiv")
			document.getElementById("donorDbDiv").style.display = "block";
		else
			document.getElementById("donorDbDiv").style.display = "none";

		if(idOfDivToShow == "bulkEmailDiv")
			document.getElementById("bulkEmailDiv").style.display = "block";
		else
			document.getElementById("bulkEmailDiv").style.display = "none";
		
	}

</script>

	
	
	
	
	
</head>

<body onload="initialize()">

<?php	
	//If the page has been opened in a new tab, such as from the all-members table (flatDB.php), 
	//show the neighborhood name and a message reminding the user that they are in a second tab
	//otherwise, just display the neighborhood name.
	if (isset($_GET['tab']))
		{echo '<b style="z-index: 2;font-size: 28px; color: #f33e06; padding: 10px 10px 5px 25px;">'.getNhoodNameFromNid($nhID).'</b><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=LastName" target="ContentFrame" style="padding-left: 15px;"><img src="icons/reload.png" alt="reload" /></a><div style="z-index: 1;color: red; text-align: right;">***This Neighborhood page has opened in a seperate tab from the home interface. <br /> Save any changes & close this tab.***</div>';
		echo '<br />';
		}
		else
	{	echo'<h1 style="color: #f33e06; padding: 10px 10px 5px 25px;">'. getNhoodNameFromNid($nhID) .' <a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=LastName" target="ContentFrame" style="padding-left: 15px;"><img src="icons/reload.png" alt="reload" /></a></h1>';
	echo '<br />';
	
	}
?>


<div class="gearsWidget">
			<ul id="ncNav">
			<li><a href="#" onclick="nhShowHideDivs('mapToolsDiv');">Options</a></li>
			<li><a href="#" onclick="nhShowHideDivs('notesDiv');">Notes</a></li>
			<li><a href="#" onclick="nhShowHideDivs('donorsDiv');">Route Order</a></li>
			<li><a href="#" onclick="location.href='recordTallyNH.php?uid=<?php echo $uid ?>&ncid=<?php echo $ncid ?>&nhID=<?php echo $nhID ?> '">Record Tally</a></li>
			<li><a href="#" onclick="location.href='tallysheet.php?uid=<?php echo $uid ?>&ncid=<?php echo $ncid ?>&nhid=<?php echo $nhID ?> '">Tallysheet</a></li>
			<li><a href="#" onclick="nhShowHideDivs('bulkEmailDiv');">Email List</a></li>
			<li><a href="#" onclick="nhShowHideDivs('donorDbDiv');">View Donors</a></li>
			<li><a href="#" onclick="nhShowHideDivs('newDonorsDiv');">Home</a></li>
			</ul> 
</div>
	

<!--	PREPARE THE DONORS / ROUTE DIV	-->

	<?php
	//////////////////////////////////////
	//	The  Donors / Route Div for This Nhood	//
	//////////////////////////////////////		
//set up the content of the table
	$nhoodTable=' 
		<tr>
			<th>Donors:</th>
			<td></td>
			<td><input type="submit" value="save route order" /></td>
		</tr>
		
		<tr>
		<th >Edit</th>
		<td>Donor Name</td>
		<th>Route Order</th>
		
		</tr>';
		 
	$sql="SELECT * FROM members WHERE NHoodID=".$nhID." AND accepted=1 AND hasBag=1 AND(Status='ACTIVE' OR Status='INACTIVE') ORDER BY routeOrder, StreetName, House, Apt";
	$result=mysql_query($sql);
//echo '<script type="text/javascript" />alert("sql:\n '.$sql.'")</script>';
	while ($nhoods=mysql_fetch_array($result) )
	{
		if ($nhoods['Apt'] =='')
			$theApt='';
		else $theApt=' apt:'.$nhoods['Apt'];
		
		if($nhoods['accepted']==1)

		$nhoodTable.='<tr><td> 
			<a href="editMember.php?fdid='.$nhoods["MemberID"].'&uid='.$uid.'" target="_blank" title="View/Edit this member\'s information" > 
			<img src="icons/edit.png" alt="Edit This Member" width="30px" height="30px" />
			</a>	</td>
		<td >
			'.$nhoods["FirstName"].' '.$nhoods["LastName"].'<br/> '.$nhoods['House'].' '.$nhoods['StreetName'].$theApt.' 
		</td>
		<td><input type="text" name="route'.$nhoods['MemberID'].'" value="'.$nhoods["routeOrder"].'" size="3" /></td>
		
		</tr>';
	} 
//show the table in a div
?>

			
<!--	NOTES DIV	-->
<div class="fullWidget" id="notesDiv" name="Notes" >
<h2>Neighborhood Notes</h2>
	<form id="NhoodNotesForm" action="neighborhood.php?uid=<?php echo $uid ?>&nh=<?php echo $nhID ?>&ncid=<?php echo $ncid ?>&savenotes=yes&tool=notesDiv" method="post">
		<br/>
		<textarea name="notes" id="notes" style="min-width: 600px; min-height: 300px; margin-left: auto; right: 10px;"><?php echo $nhoodNotes ?></textarea>
		<input type="hidden" name="NHoodID" id="NHoodID" value="<?php echo $nhID ?>" />
		<input type="submit" value="Save Notes" />
		
	</form>
</div>
			
<!--	THE PICKUP ROUTE DIV	-->
<div class="leftWidget" id="donorsDiv" name="nhoodInfo">
<h2>Pickup Route</h2>
<p>Click on the green dots on your map to identify your donors and save the best pickup order.  Your Tallysheet will print out in the same order.</p>
<br />
	<form id="routeOrderForm" action="neighborhood.php?uid=<?php echo $uid ?>&nh=<?php echo $nhID ?>&ncid=<?php echo $ncid ?>&saveroute=yes&tool=donorsDiv" method="post">
		<table style="background-color:transparent;">	
			<?php echo $nhoodTable; ?>
		</table>	
	</form>
</div>
	
	
<!--	MAP TOOLS DIV	-->	
<div class="leftWidget" id="mapToolsDiv" ><br />
	<a href="javascript:toggleTools('maxNumDonors');">Maximum # of Donors? +/- </a><br />
	<div id="maxNumDonors" style="display: none;">
		<p>Enter the maximum number of donors you can take on. This helps the Welcome Committee volunteer(s) determine how to assign new donors coming in via the public website, tabling, canvassing, etc.</p><br />
	</div>
	<?php include('mapTools.php'); ?>
			
</div>


<!-- DONOR TABLE FOR THIS NEIGHBORHOOD	-->
<div class="fullWidget" id="donorDbDiv">
	<?php
	$NHoodID=$nhID;
	$sql = "SELECT historySwitch FROM neighborhoods WHERE NHoodID='".$NHoodID."' " ;
	$switchResult=mysql_query($sql);
	while ($switchCheck=mysql_fetch_array($switchResult))

	{
	if ($switchCheck['historySwitch']==1)
		$NHFlatDB=include('nhDonorsDBHistoryOff.php');

	else $NHFlatDB=include('nhDonorsDBHistoryOn.php');	
	};
	
	echo $NHFlatDB;
	
	?>
</div>

<!--  BULK EMAIL LIST -->
<div class="leftWidget" id="bulkEmailDiv">
	<h2 style="text-align: center;">Email List</h2>
	<p style="padding: 11px; font-size: 11px;">Depending on which browser version you are using and how your email client is configured, the BCC: All Emails button may or may not work. An alternative method is to "triple click" on the light blue area to select it.  Then, copy to the clipboard.  Then, jump over to your email client and paste directly into the BCC field.</p>
	<p style="padding: 10px; font-size: 11px;">Some email systems require an email to be entered into the TO: field.  If that is the case, one option is to enter your own email address.</p><br />
	<h3>Separated by Commas</h3>
	<p style="background-color: #A4DCED; padding: 10px; border: 2px solid #A4DCED; border-radius: 10px;"><?php echo nhBulkEmail($nhID) ?></p><br />
	<a href="mailto:?bcc=<?php echo nhBulkEmail($nhID)?>">
		<p style="background-color:#ddd8d2; color:#2d2e2f; border: solid 3px #bbb1a7 ; border-radius:8px; text-align:center; width:100px; float:right;" title="Opens all email addresses in your email client" >BCC: All Emails</p>
	</a>
	<br /><br /><br />
	<h3>Separated by Semicolons</h3>
	<p style="background-color: #A4DCED; padding: 10px; border: 2px solid #A4DCED; border-radius: 10px;"><?php echo nhBulkEmailSemicolon($nhID) ?></p><br />
	<a href="mailto:?bcc=<?php echo nhBulkEmailSemicolon($nhID)?>">
		<p style="background-color:#ddd8d2; color:#2d2e2f; border: solid 3px #bbb1a7 ; border-radius:8px; text-align:center; width:100px; float:right;" title="Opens all email addresses in your email client" >BCC: All Emails</p>
	</a>
	<br /><br />

</div>

<!-- 	CONFIRM NEW DONORS DIV	-->
<div class="leftWidget" id="newDonorsDiv"><br />
	<h2>Confirm New Donors</h2><br />
	<a href="javascript:toggleTools('confirmNewDonors');">If no new donors? +/- </a><br />
	<div id="confirmNewDonors" style="display: none;">
		<p>If there are no donors listed below these help notes, you have no new donors in your queue!</p><br />
	</div>
		<a href="javascript:toggleTools('explainbuttons');">What do the buttons mean? +/-</a><br />
	<div id="explainbuttons" style="display: none;">
		<p>ACCEPTED: Lets the Welcome Committee volunteer know that you are taking them on as a new donor.</p><br />
		<p>CONTACTED/HAS A BAG: When this button is selected, the donor will be moved to your list (View Donors) and your Tallysheet.  The donor will also disappear from the Welcome Committee's dashboard. </p><br />
		<p>DECLINE: Click this button if you cannot take on this donor and the Welcome Committee will get an alert to re-asssign this donor to an alternate Neighborhood.
		will then know to reassign the Food Donor to another nearby neighborhood.</p>
	</div>
		<a href="javascript:toggleTools('declineSuggestion');">Decline with a suggestion? +/- </a><br />
	<div id="declineSuggestion" style="display: none;">
		<p>Suppose you're declining a donor, but you have a suggestion to send back to the Welcome Committee.  Enter your text the Member Notes field and then click 'Decline.'</p><br />
	</div>
	<hr>
		<?php		
		populateNewDonorsDiv($nhID, $uid);
		?>
</div>

<!-- END LEFT WIDGET WRAPPER -->		

<!--	START Map Area Wrapper		-->		
	<div class="mapWidgetWrapper" id="mapWidgetWrapper">

	
<!--	The Map	-->	
	<div class="mapWidget" id="map_canvas">	
		<p style="color:purple">Map attempting to load.....if you've been waiting over 
		30 seconds, you might check other webpages to see if your connection to the internet is working.
		Please contact your support team for assistance.</p>	
	</div>	

<!--	END The Map Area Wrapper	-->
	</div>
	

</body>

<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script> -->
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script src="js/plugins.js"></script>
  <script src="js/uiFunctions.js"></script>


</html>



<?php
	function nhoodMarkers($nhID)
	{
		$sql=mysql_query("SELECT FirstName,LastName,latLong FROM members WHERE NHoodID=".$nhID." ORDER BY routeOrder");
		$count=0;
		while($nhoods=mysql_fetch_array($sql)	)
		{	
			$tooltip=$nhoods['FirstName'].' '.$nhoods['LastName'];
			//echo 'setMarker(new google.maps.LatLng'.$nhoods['latLong'].', "'.$tooltip.'");';
			echo 'var position'.$count.'= new google.maps.LatLng'.$nhoods['latLong'].';
			';
			echo 'var marker'.$count.' = new google.maps.Marker({
					position: position'.$count.',
					map: map,
					title: "'.$tooltip.'"
				});
			';
			$count++;
		}
	}

	function nhoodRoute($nhID, $routePolyline=0)
	{//firstName, LastName, House,Street,City,RouteOrder,latLong

				if($routePolyline)
				{	
//	echo 'alert("found a good polyline. decoding.");';
					$encodedPolyline = $routePolyline;
					echo ' routeCoordinates =decodePath("'. $routePolyline.' ");				';
				}
				else 
				{
//	echo 'alert("did NOT find a polyline. pulling one together from donor points.");';
					$sql=mysql_query("SELECT latLong FROM members WHERE NHoodID=".$nhID." AND accepted=1 AND hasBag=1 ORDER BY RouteOrder,LastName");
					echo ' routeCoordinates = [
					';
					while ($nhoods=mysql_fetch_array($sql) )
					{	if(trim($nhoods['latLong'])!='0' && $nhoods['latLong']!="NULL")
						echo 'new google.maps.LatLng'.$nhoods['latLong'].', 
						';
					}
					echo ']; ';
				}	
				echo '
					routePoly = new google.maps.Polyline({
						path: routeCoordinates,
						strokeColor: "#FF0000",
						strokeOpacity: 1.0,
						strokeWeight: 2,
						editable: false,
						visible: false
					  });

					  routePoly.setMap(map);
					  ';
				
	}

	function saveNHoodNotes($_POST)
	{
	//save the notes
		$notes=$_POST['notes'];
		$nhID=$_POST['NHoodID'];
	//PARAMETERIZE THIS:
		mysql_query("UPDATE neighborhoods SET notes='".$notes."' WHERE NHoodID='".$nhID."';");

		
		
		
		//id="route'.$nhoods['MemberID'].'"
	}
	
	function saveRouteOrder($_POST, $nhID)
	{
			//save the routeOrder
		$nhDonors=mysql_query("SELECT MemberID FROM members WHERE NHoodID='".$nhID."';");
		while ($row=mysql_fetch_array($nhDonors))
		{
			if(isset($_POST['route'.$row['MemberID']]))
			mysql_query("UPDATE members SET routeOrder='".$_POST['route'.$row['MemberID']]."' WHERE MemberID='".$row['MemberID']."';");
		}
	}
	
		

	?>
	
