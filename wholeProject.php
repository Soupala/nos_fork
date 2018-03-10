<!DOCTYPE html>
<?php
	include('securepage/nfp_password_protect.php'); 
	include('mapFunctions.php');
	$uid=$_GET['uid'];
	include('functions.php');
		opendb();
	$myMapKey=getMapKey();
	$centerLatLong=getCityLatLong();
	
// do any saves before building the page
	if(isset($_GET['savenotes']))
	{	saveWholeProjectNotes($_POST);	}
	if(isset($_GET['savecenter']))
	{	saveWholeProjectCenter($_POST);	}
	if(isset($_GET['deleteD']))
	{	deleteDistrict($_POST);	}	
	if(isset($_GET['newD']))
	{	newDistrict($_POST);	}
	if(isset($_GET['assignDC']))
	{	assignDC($_POST);	}
	if(isset($_POST['rename']))
	{// 	echo '<p style="color:crimson"> heading out to saveRegionName()<br/>
				// rename: '.$_POST['rename'].'<br/>
				// newname: '.$_POST['newname'].'<br/>
				// district: '.$_POST['Dbox'];
		saveRegionName('districts', $_POST['newname'], $_POST['Dbox']);	
	}
		
/*	$Dist=mysql_fetch_array(mysql_query("SELECT * FROM districts WHERE DistrictID=".$dID));
		// $dID=$Dist['DistrictID'];
		$dName=$Dist['DistrictName'];
		$dcid=$Dist['DCID'];
		$boundaries=$Dist['polygon'];
		if($Dist['center']=="0" || $Dist['center']==null)
			$centerLatLong=getCityLatLong();
		else $centerLatLong=$Dist['center'];
	$dc=mysql_fetch_array(mysql_query("SELECT FirstName,LastName FROM members WHERE MemberID=".$dcid));
		$dcName=$dc['FirstName'].' '.$dc['LastName'];
		$districtNotes=$Dist['notes'];
*/
//	create the list of districts	//		
	$districtTable="<tr><td colspan=2><h2>Districts:</h2></td></tr>";
	$sql=mysql_query("SELECT * FROM districts ORDER BY DistrictName");
	$districtArray=array();
	
	while ($districts=mysql_fetch_array($sql) )
		{
		
			$districtTable.="<tr ><td>	</td><td><a href='district.php?d=".$districts['DistrictID']."&uid=".$uid."'>".$districts['DistrictName']."</a></td></tr>";
			
			$districtArray[$districts['DistrictID']]=$districts['polygon'];
			
		}
	
?>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Neighborhood Organizing System- Admin Dashboard</title>
	<meta name="description" content="Main Container for the Neighborhood Food Project Database Application">
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
					  
		
		function dShowHideDivs(idOfDivToShow)
		{
			if(idOfDivToShow == "DCsDiv")
			document.getElementById("DCsDiv").style.display = "block";
		else
			document.getElementById("DCsDiv").style.display = "none";	
			
			if(idOfDivToShow == "districtsDiv")
				document.getElementById("districtsDiv").style.display = "block";
			else
				document.getElementById("districtsDiv").style.display = "none";
				
			if(idOfDivToShow == "newDistrictDiv")
				document.getElementById("newDistrictDiv").style.display = "block";
			else
				document.getElementById("newDistrictDiv").style.display = "none";
				
			if(idOfDivToShow == "deleteDistrictDiv")
				document.getElementById("deleteDistrictDiv").style.display = "block";
			else
				document.getElementById("deleteDistrictDiv").style.display = "none";
				
			if(idOfDivToShow == "assignDCDiv")
				document.getElementById("assignDCDiv").style.display = "block";
			else
				document.getElementById("assignDCDiv").style.display = "none";
				
			//if(idOfDivToShow == "changeCenterDiv")
				//document.getElementById("changeCenterDiv").style.display = "block";
			//else
				//document.getElementById("changeCenterDiv").style.display = "none";
				
			if(idOfDivToShow == "renameDistrictDiv")
				document.getElementById("renameDistrictDiv").style.display = "block";
			else
				document.getElementById("renameDistrictDiv").style.display = "none";
				
			if(idOfDivToShow == "bulkEmailDiv")
				document.getElementById("bulkEmailDiv").style.display = "block";
			else
				document.getElementById("bulkEmailDiv").style.display = "none";
			
			//if(idOfDivToShow == "Notes")
				//document.getElementById("Notes").style.display = "block";
			//else
				//document.getElementById("Notes").style.display = "none";
		}
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
						title: "The Center of the Map",
						visible: false
					});
				
				  //load the district polygons
					<?php 	loadAllDistrictPolygons();
							unassignedDonorMarkers();
					?>
				
			}
	
	    </script>
			

</head>


<body onload="initialize()">


<!-- UPPER LEFT TOOL/DASHBOARD NAV PANEL -->
<h1 style="color: #2f4b66; padding: 5px 5px 15px 15px;">Manage Districts</h1>
<br />


<div class="gearsWidget" style="padding-left: 10px;">
				<ul id="adminNav">
				<li><a href="#" onclick="dShowHideDivs('deleteDistrictDiv');">Delete</a></li>
				<li><a href="#" onclick="dShowHideDivs('renameDistrictDiv');">Rename</a></li>
  			<li><a href="#" onclick="dShowHideDivs('assignDCDiv');">Assign DCs</a></li>
				<li><a href="#" onclick="dShowHideDivs('newDistrictDiv');">Create</a></li>
				<li><a href="#" onclick="dShowHideDivs('bulkEmailDiv');">Email List</a></li>
				<li><a href="#" onclick="dShowHideDivs('DCsDiv');">DC List</a></li>
				<li><a href="#" onclick="dShowHideDivs('districtsDiv');">All Districts</a></li>
			  </ul>
</div>



<!-- MY DCS DIV -->
		<div class="leftWidget" id="DCsDiv">
			<?php  dcContactList(); ?>
		</div>

		<!--  BULK EMAIL LIST -->
		<div class="leftWidget" id="bulkEmailDiv">
	<h2 style="text-align: center;">District Coordinator Email List</h2>
	<p style="padding: 11px; font-size: 11px;">Depending on which browser version you are using and how your email client is configured, the BCC: All Emails button may or may not work. An alternative method is to "triple click" on the light blue area to select it.  Then, copy to the clipboard.  Then, jump over to your email client and paste directly into the BCC field.</p>
	<p style="padding: 10px; font-size: 11px;">Some email systems require an email to be entered into the TO: field.  If that is the case, one option is to enter your own email address.</p><br />
	<h3>Separated by Commas</h3>
	<p style="background-color: #A4DCED; padding: 10px; border: 2px solid #A4DCED; border-radius: 10px;"><?php echo wpBulkEmail() ?></p><br />
	<a href="mailto:?bcc=<?php echo wpBulkEmail() ?>">
		<p style="background-color:#ddd8d2; color:#2d2e2f; border: solid 3px #bbb1a7 ; border-radius:8px; text-align:center; width:100px; float:right;" title="Opens all email addresses in your email client" >BCC: All Emails</p>
	</a>
	<br /><br /><br />
	<h3>Separated by Semicolons</h3>
	<p style="background-color: #A4DCED; padding: 10px; border: 2px solid #A4DCED; border-radius: 10px;"><?php echo wpBulkEmailSemicolon() ?></p><br />
	<a href="mailto:?bcc=<?php echo wpBulkEmailSemicolon() ?>">
		<p style="background-color:#ddd8d2; color:#2d2e2f; border: solid 3px #bbb1a7 ; border-radius:8px; text-align:center; width:100px; float:right;" title="Opens all email addresses in your email client" >BCC: All Emails</p>
	</a>
	<br /><br />
			
				</div>
		

<!--	RENAME A DISTRICT	-->
		<div class="leftWidget" id="renameDistrictDiv">
		
			<form id="renameDistrictForm" name="renameDistrictForm"  action="wholeProject.php?uid=<?php echo $uid?>" method="post">
				<h2>Rename a District</h2><br />
				District to Rename:<br />
				<?php echo districtCombobox() ?> 
				<input type="hidden" name="rename" value="true"/><br />
				<input type="text" name="newname" /></br>
				<input type="submit" value="Submit" />
			</form>
</div>

<br />
<!--	CREATE A DISTRICT	-->
<div class="leftWidget" id="newDistrictDiv">

			<form id="newDistrictForm" name="newDistrictForm" action="wholeProject.php?uid=<?php echo $uid?>&newD=true" method="post">
				<h2>Create a New District</h2><br />
				Name:<input type="text" name="newDistrictName" /><br />
				Assign DC: <?php  echo DCcombobox(); ?>
				<input type="submit" value="Submit" />
			</form>
</div>
<br />		
<!--	DELETE A DISTRICT	-->
<div class="leftWidget" id="deleteDistrictDiv">

			<form id="deleteDistrictForm" name="deleteDistrictForm" onsubmit="return confirm('Are you sure you want to delete this district?');" action="wholeProject.php?uid=<?php echo $uid?>&deleteD=true" method="post">
				<h2>Delete a District</h2><br />
				Select:<?php echo districtCombobox() ?> 
				<input type="submit" value="Remove" />
			</form>
</div>

<br />		
<!--	ASSIGN A DC		-->
<div class="leftWidget" id="assignDCDiv">

			<form id="assignDCForm" name="assignDCForm" action="wholeProject.php?uid=<?php echo $uid?>&assignDC=true" method="post">
				<h2>Assign a District Coordinator</h2><br />
				<?php echo districtCombobox() ?> <br/>
				Possible DCs: 
				<?php echo DCcombobox(); ?>
				<input type="submit" value="Submit" />
			</form>
</div>
<br />	

<!--	ALL DISTRICTS in the Food Project	-->
<div class="leftWidget" id="districtsDiv">
			<form id="editFPForm" action="updated.php" method="post">
			<table style="background-color:transparent; color: #2f4b66;">		
				<?php	echo  $districtTable;	?>
				</table>	
			</form>
			
</div>
		
		
<!--	END Left Widget Wrapper		-->


<!--	START Map Panel Widget Wrapper		-->		
<div class="mapWidgetWrapper" id="mapCanvasWrapper">
	
<!--	The Map	-->	
	<div class="mapWidget" id="map_canvas">	
		<p style="color:purple">Map attempting to load.....if you've been waiting over 30 seconds,<br /> 
		you might check other webpages to see if your connection to the internet is working.</p>	
	</div>	

<!--	END The Map Area Wrapper	-->
	</div>
	

<div style="z-index: 100000000;">
<script type="text/javascript" src="//assets.zendesk.com/external/zenbox/v2.5/zenbox.js"></script>
<style type="text/css" media="screen, projection">
  @import url(//assets.zendesk.com/external/zenbox/v2.5/zenbox.css);
</style>
<script type="text/javascript">
  if (typeof(Zenbox) !== "undefined") {
    Zenbox.init({
      dropboxID:   "20081413",
      url:         "https://nfp.zendesk.com",
      tabID:       "Support",
      tabColor:    "purple",
      tabPosition: "Right"
    });
  }
</script>
</div>
		
</body>


<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script> -->
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script src="js/plugins.js"></script>
  <script src="js/uiFunctions.js"></script>


</html>



<?php

function deleteDistrict($_POST)
{
	// echo '<script type="text/javascript">
			// alert("You would now be deleting the district");
		// </script>';
	$did=$_POST['Dbox'];
//move all neighborhoods from this district to the at-large district
		
	//get the DistrictID for the atlarge district
		$row=mysql_fetch_array(mysql_query("SELECT DistrictID FROM districts WHERE DistrictName LIKE '%atlarge%'"));
	$atlargeID=$row['DistrictID'];
	//update neighborhoods where districtID=$did SET districtID=$at-largeID
		$nhoodsMoved=mysql_query("UPDATE neighborhoods SET DistrictID=".$atlargeID." WHERE DistrictID=".$did);
	//delete the district
		//DELETE FROM districts WHERE districtID=".$did
		$districtDeleted=mysql_query("DELETE FROM districts WHERE DistrictID='".$did."'");
		
		
} 

function newDistrict($_POST)
{
	//echo '<p style="color:red;">About to create a new District called </p>';
	$dname=$_POST['newDistrictName'];
	$dID=$_POST['DCsBox'];
//create district
	$sql="INSERT INTO districts (DistrictName, DCID) VALUES ('".$dname."', ".$dID.")";
	$query=mysql_query($sql);
}


function assignDC($_POST)
{
	
		$dcidFromBox=$_POST['DCsBox'];
		$districtID=$_POST['Dbox'];
	$result=mysql_query("UPDATE districts SET DCID=".$dcidFromBox." WHERE DistrictID=".$districtID);
	if($result)
		echo '<script type="text/javascript">
		alert("You have just assigned a new DC to a district.");
		</script>';
	else echo '<script type="text/javascript">
		alert("Assignation failed.");
		</script>';
}

// function saveDistrictNotes($_POST)
// {
	
	// $notes=$_POST['notes'];
	// $dID=$_POST['DistrictID'];
	// //echo ' notes: '.$notes.'<br/> districtID: '.$dID.'<br/>';
// //PARAMETERIZE THIS:
	// mysql_query("UPDATE districts SET notes='".$notes."' WHERE DistrictID='".$dID."';");
	
// }
// function saveDistrictCenter($_POST, $dID)
// {
// //	opendb();
	// $center=$_POST['myCoords'];
// //PARAMETERIZE THIS:
	// $query=mysql_query("UPDATE districts SET center='".$center."' WHERE DistrictID='".$dID."';");
	// // if($query)
		// // echo '<p style="color:lime">Saved New Center Point</p>';
	// // else echo '<p style="color:red">Center Point Not Saved</p>';
// }

function Dpolygon($districtBounds)
{
	echo 'var thisPolygon = new google.maps.Polygon({
				paths: '.$nhoodBounds.',
				strokeColor:"#66cc33",
				strokeOpacity: 0.8,
				strokeWeight: 2,
				fillColor: "#663300",
				fillOpacity: 0.35,
				editable: true
			});
			thisPolygon.setMap(map);
		';

}






?>
