<!DOCTYPE html>
<?php
	
	include("securepage/nfp_password_protect.php");
	include('mapFunctions.php');
	$dID=$_GET['d'];
	$uid=$_GET['uid'];
	include('functions.php');
	opendb();
	$myMapKey=getMapKey();
	
// return to tool used before reloading the page
	if(isset($_GET['tool']))
		$tool=$_GET['tool'];
	else $tool='nhoodsDiv';
	
	
// do any saves before building the page
	if(isset($_GET['savenotes']))
	{	saveDistrictNotes($_POST);	}
	if(isset($_GET['savecenter']))
	{	saveDistrictCenter($_POST, $dID);	}
	if(isset($_GET['deleteNH']))
	{	deleteNhood($_POST, $dID);	}	
	if(isset($_GET['newNH']))
	{	newNhood($_POST['newNhoodName'], $_POST['NCbox'], $dID);	}
	if(isset($_GET['assign']))
	{	assignNC($_POST['NHbox'], $_POST['NCbox'])	;}
	
	if(isset($_GET['saveZoom']))
	{	saveZoom("district", $_POST['zoomLevel'], $dID);	}

	if(isset($_POST['rename']))
	{	//echo '<p style="color:crimson"> heading out to saveRegionName() ';
		saveRegionName('neighborhoods', $_POST['newname'], $_POST['NHbox']);	}
	if(isset($_GET['merge']))
	{
		saveMergedNhoods($_POST['keeperBox'], $_POST['discardBox']);
	}
		
		
	$Dist=mysql_fetch_array(mysql_query("SELECT * FROM districts WHERE DistrictID=".$dID));
		$dName=$Dist['DistrictName'];
		$dcid=$Dist['DCID'];
		$boundaries=$Dist['polygon'];
		if($Dist['center']=="0" || $Dist['center']==null)
			$centerLatLong=getCityLatLong();
		else $centerLatLong=$Dist['center'];
	$dc=mysql_fetch_array(mysql_query("SELECT FirstName,LastName FROM members WHERE MemberID=".$dcid));
		$dcName=$dc['FirstName'].' '.$dc['LastName'];
		$districtNotes=$Dist['notes'];
		$zoomLevel=getZoom("district", $dID);

//	create the list of neighborhoods	//		
	$nhoodTable=" <tr><td colspan=2>Neighborhoods:</td></tr>";
	$sql=mysql_query("SELECT * FROM neighborhoods WHERE DistrictID=".$dID.' ORDER BY NHName');
	$nhoodArray=array();
	
	while ($nhoods=mysql_fetch_array($sql) )
		{
		
			$nhoodTable.="<tr><td>	</td><td><a href='neighborhood.php?nh=".$nhoods['NHoodID']."&uid=".$uid."'>".$nhoods['NHName']."</a></td></tr>";
			
			$nhoodArray[$nhoods['NHoodID']]=$nhoods['polygon'];
			
		}
	
?>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>District in the Neighborhood Organizing System</title>
	<meta name="description" content="Member DB App">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />
	<script src="js/libs/modernizr-2.5.3.min.js"></script>

	<script type="text/javascript"
		src="https://maps.googleapis.com/maps/api/js?key=<?php echo $myMapKey; ?>&sensor=false&libraries=geometry"></script>
	<script type="text/javascript"
		src="js/mapFunctions.js">
    </script>
	<script type="text/javascript">
	function dShowHideDivs(idOfDivToShow)
	{
		if(idOfDivToShow == "nhoodsDiv")
			document.getElementById("nhoodsDiv").style.display = "block";
		else
			document.getElementById("nhoodsDiv").style.display = "none";
			
		if(idOfDivToShow == "newNhoodDiv")
			document.getElementById("newNhoodDiv").style.display = "block";
		else
			document.getElementById("newNhoodDiv").style.display = "none";
			
		if(idOfDivToShow == "delNhoodDiv")
			document.getElementById("delNhoodDiv").style.display = "block";
		else
			document.getElementById("delNhoodDiv").style.display = "none";
			
		if(idOfDivToShow == "assignNCDiv")
			document.getElementById("assignNCDiv").style.display = "block";
		else
			document.getElementById("assignNCDiv").style.display = "none";
			
		if(idOfDivToShow == "mapToolsDiv")
			document.getElementById("mapToolsDiv").style.display = "block";
		else
			document.getElementById("mapToolsDiv").style.display = "none";
			
		//if(idOfDivToShow == "dNotesDiv")
			//document.getElementById("dNotesDiv").style.display = "block";
		//else
			//document.getElementById("dNotesDiv").style.display = "none";
			
			
		if(idOfDivToShow == "NCsDiv")
			document.getElementById("NCsDiv").style.display = "block";
		else
			document.getElementById("NCsDiv").style.display = "none";
			
		if(idOfDivToShow == "renameNhoodDiv")
			document.getElementById("renameNhoodDiv").style.display = "block";
		else
			document.getElementById("renameNhoodDiv").style.display = "none";
			
		if(idOfDivToShow == "mergeNhoodDiv")
			document.getElementById("mergeNhoodDiv").style.display = "block";
		else
			document.getElementById("mergeNhoodDiv").style.display = "none";
			
		if(idOfDivToShow == "bulkEmailDiv")
			document.getElementById("bulkEmailDiv").style.display = "block";
		else
			document.getElementById("bulkEmailDiv").style.display = "none";
		}
</script>
<!-- INITIALIZE() -->
    <script type="text/javascript">
		var map;
		var geocoder;
		var mapCenter;
		var centerMarker;
		var zoomLevel=<?php echo $zoomLevel ?>;

		var districtArray={};
		var nhoodArray={};
		var membersArray={};
		var numNhoods;
		var numMembers;
			
		function initialize() {
			var mapCenter=new google.maps.LatLng<?php echo $centerLatLong ?>;
			var myOptions = {
			  center: mapCenter,
			  zoom: zoomLevel,
			  mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			
			 map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	//create a geocoder to transform addresses into lat and Long
			geocoder= new google.maps.Geocoder();
	//add the 'center' marker
			 centerMarker = new google.maps.Marker({
					position: mapCenter, 
					map: map,
					//icon: "http://chart.apis.google.com/chart?chst=d_map_spin&chld=<?php echo $centerPinSize; ?>|0|<?php echo $centerPinColor; ?>|11|_|C",
					icon: "icons/marker_split.png",
					title: "The Center of the Map", 
					visible: false
				});
				
						 //add in the donor markers
			 <?php 
				
// 				foreach($nhoodArray as $key=>$value)
// 				{ 
// 					echo '
// 					//going to make markers for nhood: '.$key.'
// 					';
// 					 //echo donorMarkers($key); 	
// 					 echo donorMarkers($key);
// 				}
 			?>
			
			//add in the NC markers
			  <?php //ncImageMarkers(); ?>

			 //add the neighborhood polygons
			 <?php 
// 			//	foreach ($nhoodArray as $nid => $nhoodBounds)
// 				//	echo NHpolygon($nhoodBounds);
// 			 ?>
			  
			 //add in the donor markers
			 <?php 
				
// 				foreach($nhoodArray as $key=>$value)
// 				{ 
// 					echo '
// 					//going to make markers for nhood: '.$key.'
// 					';
// 					 //echo donorMarkers($key); 	
// 					 echo donorMarkers($key);
// 				}
			?>



		//load the district polygon
		<?php loadDistrictPolygon($_GET['d']);?>

			
	//set the active toolPanel from before the page reloaded
			dShowHideDivs("<?php echo $tool ?>");
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
    </script>

</head>


<body onload="initialize()">


<!--<div class="adminDashboardWrapper" id="adminDashboardWrapper">-->

<!-- UPPER LEFT TOOL/DASHBOARD NAV PANEL -->
<?php 	if (isset($_GET['tab']))
	{echo '<b style="font-size:32px; color: #a9005b; padding-left:25px; padding-top:30px;">'.getDistNameFromDid($dID).'</b><div style=" color: red; float: right; padding: 25px; top:25px;">***This page has opened in a separate tab from the home interface.  <br/>Save any changes & close this tab.***</div>';
	}
	else
{	echo'<h1 style="color: #a9005b; padding-left: 15px;">'. getDistNameFromDid($dID).'</h1>';
	echo '<br />';
}
?>


<!--<div class="mainWrapper" id="mainWrapper">-->

<!--<div class="leftWidgetWrapper" id="leftWidgetWrapper" >-->

<div class="gearsWidget">
				<ul id="dcNav">
				<!--<li><a href="#" onclick="dShowHideDivs('dNotesDiv');">District Notes</a></li> -->
				<li><a href="#" onclick="dShowHideDivs('mapToolsDiv');">Map Tools</a></li>
				<li><a href="#" onclick="dShowHideDivs('renameNhoodDiv')">Rename</a></li>
				<li><a href="#" onclick="dShowHideDivs('delNhoodDiv');">Delete</a></li>
				<li><a href="#" onclick="dShowHideDivs('mergeNhoodDiv');">Merge</a></li>
				<li><a href="#" onclick="dShowHideDivs('assignNCDiv');">Assign</a></li>
				<li><a href="#" onclick="dShowHideDivs('newNhoodDiv');">Create New</a></li>
				<li><a href="#" onclick="dShowHideDivs('bulkEmailDiv');">Email List</a></li>	
				<li><a href="#" onclick="dShowHideDivs('NCsDiv');">NC List</a></li>				
				<li><a href="#" onclick="dShowHideDivs('nhoodsDiv');">Neighborhoods</a></li>
				
				</ul>
</div>
		
		<!--  BULK EMAIL LIST -->
		<div class="leftWidget" id="bulkEmailDiv">
	<h2 style="text-align: center;">Your NC Email List</h2>
	<p style="padding: 11px; font-size: 11px;">Depending on which browser version you are using and how your email client is configured, the BCC: All Emails button may or may not work. An alternative method is to "triple click" on the light blue area to select it.  Then, copy to the clipboard.  Then, jump over to your email client and paste directly into the BCC field.</p>
	<p style="padding: 10px; font-size: 11px;">Some email systems require an email to be entered into the TO: field.  If that is the case, one option is to enter your own email address.</p><br />
	<h3>Separated by Commas</h3>
	<p style="background-color: #A4DCED; padding: 10px; border: 2px solid #A4DCED; border-radius: 10px;"><?php echo districtBulkEmail($dID) ?></p><br />
	<a href="mailto:?bcc=<?php echo districtBulkEmail($dID) ?>">
		<p style="background-color:#ddd8d2; color:#2d2e2f; border: solid 3px #bbb1a7 ; border-radius:8px; text-align:center; width:100px; float:right;" title="Opens all email addresses in your email client" >BCC: All Emails</p>
	</a>
	<br /><br /><br />
	<h3>Separated by Semicolons</h3>
	<p style="background-color: #A4DCED; padding: 10px; border: 2px solid #A4DCED; border-radius: 10px;"><?php echo districtBulkEmailSemicolon($dID) ?></p><br />
	<a href="mailto:?bcc=<?php echo districtBulkEmailSemicolon($dID) ?>">
		<p style="background-color:#ddd8d2; color:#2d2e2f; border: solid 3px #bbb1a7 ; border-radius:8px; text-align:center; width:100px; float:right;" title="Opens all email addresses in your email client" >BCC: All Emails</p>
	</a>
	<br /><br />
	</div>
		
<!-- 	MERGE TWO NEIGHBORHOODS INTO ONE -->
		<div class="leftWidget" id="mergeNhoodDiv" name="mergeNhoodDiv" >
			<form id="mergeNhoodForm" name="mergeNhoodForm" action="district.php?uid=<?php echo $uid ?>&d=<?php echo $dID;?>&tool=mergeNhoodDiv&merge=true" method="post">
				Use this tool to transfer all members from one neighborhood to another and delete the freshly emptied neighborhood from the database
				<br/>
				<br/>
				Neighborhood to keep:<br/>
				<?php echo allNhoodsByNCNameCombobox('keeperBox');?>
				<br/><br/>
				Neighborhood to delete:<br/>
				<?php echo allNhoodsByNCNameCombobox('discardBox');?>
				<br/><br/>
				<input type="submit" />
			</form>
		</div>
<!--	RENAME A NEIGHBORHOOD	-->
		<div class="leftWidget" id="renameNhoodDiv" name="renameNhoodDiv" >	
			<form id="renameNhoodForm" name="renameNhoodForm" action="district.php?uid=<?php echo $uid ?>&d=<?php echo $dID;?>&tool=renameNhoodDiv" method="post">
				<h2>Rename a Neighborhood</h2>
				<br />
				Neighborhood:
				<br />
				<?php echo NhoodCombobox($dID) ?> 
				<br/><br/>
				<input type="hidden" name="rename" value="true" />
				<input type="text" name="newname" /> 
				<input type="submit" value="Submit" />
		
			</form>
		</div>
		
<!-- MY NCS DIV -->
<div class="leftWidget" id="NCsDiv">
	<?php  ncContactList($dID); ?>
</div>
<br />		

<!--	CREATE A NEIGHBORHOOD	-->
		<div class="leftWidget" id="newNhoodDiv" name="createNhood" >	
			<form id="newNhoodForm" name="newNhoodForm" action="district.php?uid=<?php echo $uid ?>&newNH=true&d=<?php echo $dID;?>&tool=newNhoodDiv" method="post">
				<h2>Creating A New Neighborhood</h2>
				<br />
				<p><b>Step 1</b><br />Visit the NCs Profile to make sure he/she has the NC box checked under Rolls. If not, check the NC box, save changes, and then return here.</p><br />
				<p><b>Step 2</b></p><br />
				<b>Give this Neighborhood A Name:</b> 
				<br/><input type="text" name="newNhoodName" id="newNhName"/><br /><br />
				<b>Attach the NC to it:</b> <br/><?php  echo NCcombobox(); ?><br /><br />
				<!-- for debugging: 
				<input type="button" value="debug" onclick="alert('nhName: ' + document.getElementById('newNhName') + '\nNC: ' + document.getElementByID('NCbox'));" />-->
				<input class="queuebuttons" style="padding:10px;" type="submit" value="submit" /><br /><br />
				
			</form>
			    <p><b>Step 3</b><br /> Return to the NC's Profile and switch them to this Neighborhood you just created.</p>
			
		</div>
		
<!--	DELETE A NEIGHBORHOOD	-->
		<div class="leftWidget" id="delNhoodDiv" name="deleteNhood" >	
			<form id="deleteNhoodForm" name="deleteNhoodForm" onsubmit="return confirm('Are you sure you want to delete this neighborhood? \nAny donors remaining assigned to this neighborhood will be left dangling.');" action="district.php?uid=<?php echo $uid ?>&d=<?php echo $dID;?>&deleteNH=true&tool=delNhoodDiv" method="post">
				<h2>Delete a Neighborhood</h2>
				<br/>
				Neighborhood to remove from your district:
				<br /><br />
				<?php echo NhoodCombobox($dID) ?> 
				<br /><br />
				<input type="submit" value="Delete" />
		
			</form>
		</div>
		
<!--	ASSIGN AN NC	-->
		
		<div class="leftWidget" id="assignNCDiv" >		
			<form id="assignNCForm" name="assignNCForm" action="district.php?uid=<?php echo $uid ?>&assign=true&d=<?php echo $dID;?>&tool=assignNCDiv" method="post">
				<h2>Assign an NC</h2> 
				<br />
				Neighborhood: <?php echo NhoodCombobox($dID) ?> <br/>
				Possible NCs: <?php echo NCcombobox(); ?>
				<input type="submit" />
			</form>
		</div>
	

<!--  MAP TOOLS-->	
		<div class="leftWidget" id="mapToolsDiv">	
			<?php include('mapTools.php'); ?>
		</div>

<!--	The Neighborhoods In the District	-->
		<div class="leftWidget" id="nhoodsDiv" name="districtInfo" >	
				<form id="editDistrictForm" action="updated.php?uid=<?php echo $uid ?>&tool=leftWidget" method="post">
					<table style="background-color:transparent;">	
						<?php	echo $nhoodTable;	?>
					</table>	
				</form>
			
		</div>		
		
		
<!--	END LEFT WIDGET WRAPPER		-->
	<!--</div>-->

<!--	START Map Widget Wrapper		-->		
	<div class="mapWidgetWrapper">

	
<!--	The Map	-->	
	<div class="mapWidget" id="map_canvas">	
		<p style="color:purple">Map attempting to load.....if you've been waiting over 30 seconds,<br /> 
		you might check other webpages to see if your connection to the internet is working.</p>	
	</div>	

<!--	END The Map Area Wrapper	-->
</div>

	
<!-- End of Main Content Wrapper -->
<!--</div>-->


<!-- END adminDashboardWrapper -->
<!--</div> -->

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


function deleteNhood($_POST, $dID)
{
//	echo '<script type="text/javascript">	alert("You would now be deleting the neighborhood");		</script>';
	$nhid=$_POST['NHbox'];
//move all donors from this neighborhood to the district's at-large neighborhood
		
	//get the NHoodID for the atlarge neighborhood for this district
		$row=mysql_fetch_array(mysql_query("SELECT NHoodID FROM neighborhoods WHERE DistrictID=".$dID." AND NHName LIKE '%atlarge%'"));
	$atlargeID=$row['NHoodID'];
	//update members where nhoodID=$nhid SET NHoodID=$at-largeID
		$donorsMoved=mysql_query("UPDATE members SET NHoodID=".$atlargeID." WHERE NHoodID=".$nhid);
	//delete the neighborhood
		//DELETE FROM neighborhoods WHERE NHoodID=".$nhid
		$nhoodDeleted=mysql_query("DELETE FROM neighborhoods WHERE NHoodID='".$nhid."'");
		
		
}


function assignNC($nhoodID, $newNCID)
{
	$sql="UPDATE neighborhoods SET NCID=".$newNCID." WHERE NHoodID=".$nhoodID;
	$result=mysql_query($sql);
	if($result)
		echo '<script type="text/javascript">alert("New NC has been assigned");</script>';
	else 
		echo '<script type="text/javascript">alert("ERROR ASSIGNING AN NC\n\n the SQL is:\n'.$sql.'\n\nthe error was:'.mysql_error().'");</script>';
	
}

// function newNhood($_POST, $dID)
// {
	// echo '<p style="color:red;">About to create a new Neighborhood called </p>';
	// $nhname=$_POST['newNhoodName'];
	// $ncID=$_POST['NCbox'];
	// $sql="INSERT INTO neighborhoods (NHName, NCID, DistrictID) VALUES ('".$nhname."', ".$ncID.", ".$dID.")";
	// $query=mysql_query($sql);
		// if($query)
		// echo '<p style="color:lime">Created new Neighborhood</p>';
	// else echo '<p style="color:red">Failed to create new Neighborhood<br/>
		// '. mysql_error().'</p>
		// the SQL was:'.$sql;
// }
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

function NHpolygon($nhoodBounds)
{
	echo 'var thisPolygon = new google.maps.Polygon({
				paths: '.$nhoodBounds.',
				strokeColor:"#FF0000",
				strokeOpacity: 0.8,
				strokeWeight: 2,
				fillColor: "#FF0000",
				fillOpacity: 0.35,
				editable: true
			});
			thisPolygon.setMap(map);
		';

}

	// function donorMarkers($nhID)
	// {
		// $sql=mysql_query("SELECT MemberID,FirstName,LastName,latLong FROM members WHERE NHoodID=".$nhID." ORDER BY routeOrder");
		
		// while($nhoods=mysql_fetch_array($sql)	)
		// {	
		
			// if(!$nhoods['latLong']==0)
			// {
			// $tooltip=$nhoods['FirstName'].' '.$nhoods['LastName'];
			// //echo 'setMarker(new google.maps.LatLng'.$nhoods['latLong'].', "'.$tooltip.'");';
			// echo 'var position'.$nhoods['MemberID'].'= new google.maps.LatLng'.$nhoods['latLong'].';
			// ';
			// echo 'var marker'.$nhoods['MemberID'].' = new google.maps.Marker({
					// position: position'.$nhoods['MemberID'].',
					// map: map,
					// icon: "http://chart.apis.google.com/chart?chst=d_map_spin&chld=.5|0|0000FF|11|_|FD",
					// title: "'.$tooltip.'"
				// });
			// ';
			// }
		// }
	// }




?>
