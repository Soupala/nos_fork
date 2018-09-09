<!DOCTYPE html>
<?php
	include("securepage/nfp_password_protect.php");
	include('functions.php');
	include('mapFunctionsWC.php');
	$myMapKey=getMapKey();
	$centerLatLong=getCityLatLong();
	opendb();
	$uid=$_GET['uid'];


// return to tool used before reloading the page
	if(isset($_GET['tool']))
		$tool=$_GET['tool'];
	else $tool='unconfirmedDonors';

//do any saves necessary
	if(isset($_GET['saveEmail']) && isset($_POST['emailText']))
	{
		$dbh=openPDO();
		saveEmail($_POST['emailText'],$dbh);
	}
	if(isset($_GET['saveZoom']))
	{
		saveZoom("wholeproject", $_POST['zoomLevel'], $uid);
	}
/*	if(isset($_GET['assign']))
	{
		$dbh=openPDO();
		assignToNhood($_POST,$dbh);
	}											*/
	if(isset($_GET['savegeo']))
	{
		saveGeo($_POST);
	}
	if (isset($_GET['save']))
	{
		saveUnconfirmedData($_POST);
	}
?>


<html>
<head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />
	<script type="text/javascript"
		src="https://maps.googleapis.com/maps/api/js?key=<?php echo $myMapKey; ?>&libraries=geometry"></script>
	<script type="text/javascript"	src="js/mapFunctions.js"> </script>

	<script type="text/javascript">
		function ShowHideDivs(idOfDivToShow)
		{


			if(idOfDivToShow == "welcomeEmail")
				document.getElementById("welcomeEmail").style.display = "block";
			else
				document.getElementById("welcomeEmail").style.display = "none";

			if(idOfDivToShow == "unconfirmedDonors")
				document.getElementById("unconfirmedDonors").style.display = "block";
			else
				document.getElementById("unconfirmedDonors").style.display = "none";

			if(idOfDivToShow == "findAddress")
				document.getElementById("findAddress").style.display = "block";
			else
				document.getElementById("findAddress").style.display = "none";


			if(idOfDivToShow == "ncContactInfo")
				document.getElementById("ncContactInfo").style.display = "block";
			else
				document.getElementById("ncContactInfo").style.display = "none";

			if(idOfDivToShow == "dcContactInfo")
				document.getElementById("dcContactInfo").style.display = "block";
			else
				document.getElementById("dcContactInfo").style.display = "none";


			//if(idOfDivToShow == "mapToolsDiv")
				//document.getElementById("mapToolsDiv").style.display = "block";
			//else
				//document.getElementById("mapToolsDiv").style.display = "none";

			//if(idOfDivToShow == "selNhood")
				//document.getElementById("selNhood").style.display = "block";
			//else
				//document.getElementById("selNhood").style.display = "none";


		}

		function renderEmail()
		{
			var theDiv = document.getElementById('renderedDiv');
			var theContent = document.getElementById('emailText').value;

			theDiv.innerHTML = theContent;

			document.getElementById('emailTextDiv').style.display='none';
			document.getElementById('renderedDiv').style.display='block';
		}

		function editEmail()
		{
			// var theDiv = document.getElementById('renderedDiv');
			// var theContent = document.getElementById('emailText');

			//theDiv.innerHTML = theContent;

			document.getElementById('emailTextDiv').style.display='block';
			document.getElementById('renderedDiv').style.display='none';
		}


	</script>
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
					icon: "icons/marker_split.png",
					title: "The Center of the Map",
					visible: false
				});


		//add in the NC markers
			<?php  ncImageMarkers();//ncMarkers($ncPinColor); ?>
			<?php //dcMarkers($dcPinColor);
				unacceptedDonorMarkers();
				//unassignedDonorMarkers();
			?>

		//set the active toolPanel from before the page reloaded
			ShowHideDivs("<?php echo $tool ?>");

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

<h1 style="color: #2f4b66; padding: 5px 5px 15px 15px;">Welcome Committee</h1>
<br />



	<!-- TITLE BAR -->
		<div class="gearsWidget" >
				<ul id="adminNav">
		<!--		<li><a href="ncNotes.php?id=<?php //echo $id?>" >Edit NC Notes</a></li>	-->
				<!-- <li><a href="#" onclick="ShowHideDivs('findAddress');">By Neighborhood</a></li> -->
				<li><a href="#" onclick="ShowHideDivs('ncContactInfo');">Contact NCs</a></li>
				<li><a href="#" onclick="ShowHideDivs('dcContactInfo');">Contact DCs</a></li>
				<li> <a href="wcPrivateNotes.php?uid=<?php echo $uid ?>" target="_blank">Private Notes</a></li>
				<li><a href="#" onclick="ShowHideDivs('findAddress');">Check Location/Get Geocode</a></li>
				<li> <a href="reassignDonorQueue.php?uid=<?php echo $_GET['uid'] ?>" target="ContentFrame" >Re-Assign Queue</a> </li>
				<li><a href="#" onclick="ShowHideDivs('welcomeEmail');">Email Templates</a></li>
				<li><a href="#" onclick="ShowHideDivs('unconfirmedDonors');">New Donor Queue</a></li>
				<!--<li><a href="#" onclick="ShowHideDivs('mapToolsDiv');">Other Map Tools</a></li> -->

				</ul>
		</div>

<!-- ALL NC EMAIL/PHONE	-->
		<div class="fullWidget" id="ncContactInfo">
		<h2>NC Contact List</h2>
			<?php echo allNcContactInfo();	?>
		</div>

<!-- ALL DC EMAIL/PHONE	-->
		<div class="fullWidget" id="dcContactInfo">
		<h2>DC Contact List</h2>
			<?php echo allDcContactInfo();	?>
		</div>
<!-- FIND AN ADDRESS	-->
		<div class="leftWidget" id="findAddress" >
		<h2>Check Location/Get Geocode</h2><br />
		<!--<input id="newAddress" type="textbox" value="Enter Physical Address" /><br />
			<input type="button" value="Show on Map" onclick="codeAddress(document.getElementById('newAddress').value, 'myCoords')" />
			Geocode(latitude,longitude):<br />
			<input id="myCoords" type="textbox" />-->
			<ol style="font-size: 14px;">
						<li><input id="newAddress" size="50" type="textbox" value="ENTER the physical address, city, state, and zip" /></li><br />
						<li><input type="button" value="Show on Map & Extract Geocode" onclick="codeAddress(document.getElementById('newAddress').value, 'myCoords')" style="padding:5px; margin: 10px 5px 10px 0px;"/></li>
						<br />
						<li><input id="myCoords" name="myCoords" type="text" value="<?php echo $centerLatLong; ?>" size="40" readonly /></li>
			</ol>
					</form>



		</div>

<!-- WELCOME EMAIL DIV -->
<div class="fullWidget" id="welcomeEmail">
		<?php include('emailWelcome.php');
		?>
</div>


<!-- UNCONFIRMED DONORS DIV -->
<div class="leftWidget" id="unconfirmedDonors">
	<h2>New Donor Queue</h2><br />
	<?php
	//	if (isset($_GET['save']))
	//		include('updated.php');
		getUnconfirmedDonors($_GET);
	?>

</div>
<!--
	<div class="leftWidget" id="editNCNotes">
		<form name="ncNotesForm" id="ncNotesForm" action="unconfirmed.php?'.$pageoptions.'tool=editNCNotes&id='.$_GET['id'].'&saveNcNotes=true" method="post">
			NCcombobox($onchange="")
			<?php
			//	$ncsql="Select NCnotes FROM neighborhoods WHERE NHoodID=".$ncid;
			// 	$ncresult=mysql_fetch_array(mysql_query($ncsql));
			?>
			<textarea  name="NCnotes" rows=10 cols=36 style="background-color:transparent" / ></textarea></td>	';

			<input type="submit" value="Save Changes" />
		</form>
	</div>
-->



<!--	The Map	-->
	<div class="mapWidget" id="map_canvas">
		<p style="color:purple">Map attempting to load.....if you've been waiting over 30 seconds,<br />
		first check other webpages to see if your connection to the internet is working. Contact your system administrator for additional assistance.</p>
	</div>


</body>
</html>

<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script> -->
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script src="js/plugins.js"></script>
  <script src="js/uiFunctions.js"></script>


<?php
function getUnconfirmedDonors($_GET)
{
	opendb();
	$pageoptions='';
	$where='';

//order by user given parameters (by neighborhood, by date entered ascending,
//	by date entered descending, etc)
	if (isset($_GET['byNH']))
	{	$where=" WHERE NHoodID=".$_GET['byNH'];
		$pageoptions="byNH=".$_GET['byNH']."&";
	}
	else if(isset($_GET['ncGroup']))
	{
		$where=",groups WHERE members.MemberID=groups.uID AND groups.NC=1 ";
		$pageoptions="ncGroup=true&";
	}
	else if(isset($_GET['dcGroup']))
	{
		$where=",groups WHERE members.MemberID=groups.uID AND groups.DC=1 ";
		$pageoptions="dcGroup=true&";
	}
	else
	{	$where=" WHERE (accepted=0 OR hasBag=0)";
		$pageoptions="";
	}

	$maxnum=200;
	$numSql="SELECT MemberID from members ".$where." AND Status='ACTIVE'";
	$numResult=mysql_query($numSql);
	$num=mysql_num_rows($numResult);


	//$sql='SELECT * FROM members'.$where.' ORDER BY MemberID DESC LIMIT 50';

	$sql='SELECT * FROM members, groups WHERE (accepted=0 OR hasBag=0) AND Status="ACTIVE" AND MemberID=uID ORDER BY MemberID DESC LIMIT '.$maxnum;


	$newDonors=mysql_query($sql);

	//	echo '<script type="text/javascript" >alert("The SQL to order the New Donors\'s panel is:\n'.$sql.'")</script>';

	if($num<$maxnum)
		echo '<p style="text-align:center">*** Showing 1 - '.$num.' new sign-ups ***</p>';
	else echo '<p style="text-align:center">*** Showing first '.$maxnum.' of '.$num.' new sign-ups ***</p>';

	$donorCount=0;

	while($row=mysql_fetch_array($newDonors) )			//limit the number of records displayed
	{
		$donorCount++;

		if ($row['NC']==1)
			$divColor="#f9c962"; // orange

		else if ($donorCount%2==0)
			$divColor="#71aae4"; // blue

		else $divColor="#c3ff91"; // green

		$address=$row['House'].' '.$row['StreetName'].' ' .$row['City'].' '.$row['State'];
		$latLongBox="latLong".$row['MemberID'];
		if($row['accepted']==1)
			$confirmed='checked="checked"';
		if($row['accepted']==0)
			$confirmed="";
		echo '<div id="div'.$row['MemberID'].'" style="position:relative; color: #2d2e2f; font-size: 16px; border: 4px solid #bbb1a7; border-radius: 8px; solid; background-color:'.$divColor.'; padding:15px;">';
		echo '	<form id="unconfirmedForm'.$row['MemberID'].'" action="unconfirmed.php?'.$pageoptions.'tool=unconfirmedDonors&uid='.$_GET['uid'].'&save=true#div'.$row['MemberID'].'" method="post" >';

		  	echo '
					<div style="border: solid grey 3px; webkit-border-radius: 20px; moz-border-radius: 20px; border-radius: 20px;padding:0 10px 0 10px; float:right; font-size:20px; font-weight:bold; color:#fafafb; background-color:#2f4b66;">'.$row['MemberID'].'</div>
					<a href="editMember.php?fdid='.$row["MemberID"].'&uid='.$_GET['uid'].'" target="_blank" title="View/Edit this member\'s information" style="float:right">
			<img src="icons/edit.png" alt="Edit This Member" width="30px" height="30px" />	</a>
					'.$row['FirstName'].' '.$row['LastName'].'
					<br />
					'.$row['House'].' '.$row['StreetName'].' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '.$row['Apt'].'
					<br/>
					'.$row['City'].' '.$row['State'].' '.$row['Zip'].'
					<br />
					<b></b> '.$row['PreferredPhone'].'
					<br />
					<b></b> <a href="mailto:'.$row['PreferredEmail'].'">'.$row['PreferredEmail'].'</a>
					<br />
					<p style="background-color: #dbe5dd; color: #272727; padding: 5px 10px 5px 10px; margin-top: 15px; border: 2px solid #304b66; border-radius: 10px;"><b>Donor Source:</b> '.$row['Source'].'</p><br />
					';

  		echo '<div>'.$row['WCNotes'].'</div>
  		<input type="hidden" name="ud_memberID" value="'.$row["MemberID"].'	" />
  		';

//	welcome email sent?
if($row['WCEmail'])
	$emailsent='checked="checked"';
else
	$emailsent='';
	 echo ' <br />';
	echo '<input type="checkbox" name="wcemail" '.$emailsent.' /> Welcome Email Sent';


//	NC contacted?
if($row['NCEmail'])
	$emailsent='checked="checked"';
else
	$emailsent='';
  echo ' <br />';
	echo '<input type="checkbox" name="ncemail" '.$emailsent.' /> NC Contacted<br /><br/>';

//echo ' <input type="button" name="ShowOnMap" value="Geolocate Donor/Find Nearby NC" onclick="codeAddress(\''.$address.'\', \''.$latLongBox.'\'); " /><br /><br /> ';

echo '<a style="margin-bottom: 5px;" class="queuebuttons"  onclick="codeAddress(\''.$address.'\', \''.$latLongBox.'\'); " >Geocode/Locate on Map</a><br/>';

//LATLONG
	//echo	'		Lat/Long	';
	echo '<input type="hidden" size="40" name="ud_latLong" id="'.$latLongBox.'" value="'.$row['latLong'].'"/><br />	';
	if ($row['latLong']=="(42.938696,-122.146522)")
		echo '<p style="color:red; text-align:left ;">***donor needs to be geocoded***</p>';
		//echo '<img id="geoImg" src="images/needsGeocoding.png" alt="needs Geocoding" style="color:red;"/>';
	else echo '<p style="color:green; text-align:left;">**donor has been geocoded**</p>';//echo '<img id="geoImg" src="images/hasGeocoding.png" alt="has Geocoding" style="color:green;"/>';
	echo '<br />';

//				Neighborhood:	<input type="text" name="ud_nid" value="'.$row['NHoodID'].'"/>
//				<br />';



// DATE ENTERED
echo 'Date Entered: <input type="text" name="ud_date_entered" id="ud_date_entered" value="'.$row['DateEntered'].'" />';

			echo '<br />	<a href="javascript:toggleTools(\'memberNotesDiv'.$row['MemberID'].'\');">General (Member) Notes +/-	</a><br/>
				<div id="memberNotesDiv'.$row['MemberID'].'" style="display:none"><textarea rows="2" cols="47" name="ud_notes" style="background-color:transparent">'.$row['Notes'].'</textarea></div>
				<a href="javascript:toggleTools(\'puNotesDiv'.$row['MemberID'].'\');">Pickup Notes +/-</a><br/>
				<div id="puNotesDiv'.$row['MemberID'].'" style="display:none;"><textarea rows="2" cols="47" name="ud_punotes" style="background-color:transparent">'.$row['PUNotes'].'</textarea></div>
				<br />';

//				Neighborhood:	<input type="text" name="ud_nid" value="'.$row['NHoodID'].'"/>
//				<br />';

// NEIGHBORHOOD COMBOBOX
echo '			Neighborhood:'.allNhoodCombobox("NHbox", "", $row['NHoodID']).'<br /> ';

echo '	<a style="margin-top: 10px;" class="queuebuttons"  onclick="document.getElementById(\'unconfirmedForm'.$row['MemberID'].'\').submit();">Save Changes and Assign to NCs Queue</a>';

		echo '	</form>';
		echo '</div>';
		echo '<br />';


	}//end while
}//end getUnconfirmedDonors()

function saveUnconfirmedData($_POST)
{
	$dbh=openPDO();
//check if the donor has been accepted yet
	$sql=mysql_query("SELECT accepted, WCNotes FROM members WHERE MemberID=".$_POST['ud_memberID']);
	$accepted=mysql_fetch_array($sql);

	//echo 'saveUnconfirmedData() (at the bottom of unconfirmed.php) has been called!<br/>';

			$query=$dbh->prepare("UPDATE members SET NHoodID=:nhName, Notes=:notes, PUNotes=:punotes,WCNotes=:wcnotes, latLong=:latlong, WCEmail=:wcEmail, NCEmail=:ncEmail WHERE MemberID=:uID");
		$query->bindParam(':nhName', $nhName);
		$query->bindParam(':notes', $notes);
		$query->bindParam(':punotes', $punotes);
		$query->bindParam(':wcnotes', $wcnotes);
		$query->bindParam(':uID', $uID);
		$query->bindParam(':latlong', $latLong);
		$query->bindParam(':wcEmail', $wcEmail);
		$query->bindParam(':ncEmail', $ncEmail);

		$nhName= $_POST['NHbox'];
		$notes= $_POST['ud_notes'];
		$punotes=$_POST['ud_punotes'];

		//$wcnotes=$_POST['welcomeNotes'].'
		//'.getTodaysDate().':  Assigned to '.getNCNameFromNhoodID($_POST['NHbox']) .' for "'.getNHNameFromNhoodID($nhName).'" neighborhood';

//if they've been accepted already, don't replace the status field
		if($accepted['accepted']==1)
			$wcnotes=$accepted['WCNotes'];
		else
			$wcnotes='<p style="width: 360px; background-color: green; color: white; font-size: 16px; font-weight: bolder; padding: 15px; border: 1px solid green; border-radius: 10px;">
			'.getTodaysDate().':  Assigned to NH '.getNHNameFromNhoodID($nhName).' (NC: '.getNCNameFromNhoodID($_POST['NHbox']).'  )</p>';
		// Email: <a href="mailto:'.getNCEmailFromNhoodID($_POST['Email']).'">'.getNCEmailFromNhoodID($_POST['Email']).'</a>

		$uID=$_POST['ud_memberID'];
		$latLong=$_POST['ud_latLong'];

		if(isset($_POST['wcemail']))
			$wcEmail=1;
		else $wcEmail=0;

		if(isset($_POST['ncemail']))
			$ncEmail=1;
		else $ncEmail=0;

		try
		{
			$query->execute();
//			echo '<script type="text/javascript"> alert("query executed")</script>';
		}
		catch(PDOException $e)
		{
			echo '<script type="text/javascript"> alert("saveUncomfirmedData() has NOT saved. \r\n Error: '.$e->getMessage().'")</script>';
			die();
		}
}




function saveEmail($content,$dbh)
{
	// if(!isset($functionsAreLoaded))
	// {
		// echo 'functionsAre - NOT - Loaded';
		// include('functions.php');
	// }
	// else echo 'functionsAreLoaded';
	//$sql="INSERT INTO wholeproject data='".mysql_real_escape_string($content)."' WHERE miscName=welcomeEmail";
	// openPDO();

		$query=$dbh->prepare("UPDATE wholeproject SET data=:html WHERE miscName='welcomeEmail'");
		$query->bindParam(':html', $html);

		$html=trim($content);

		try
		{
			$query->execute();
		}
		catch(PDOException $e)
		{
			echo '<script type="text/javascript"> alert("Default welcome email has NOT been saved. \r\n Error: '.$e->getMessage().'")</script>';
			die();
		}

	// if(!$query->execute())
		// echo '<script type="text/javascript"> alert("Default welcome email has NOT been saved. \r\n Error: '.mysql_error().'")</script>';
	// else echo '<script type="text/javascript"> alert("Default welcome email has been saved")</script>';
}


function assignToNhood($_POST, $dbh)
{/*
// echo '<script type="text/javascript"> alert("assignToNhood() was called")</script>';

	// $sql="UPDATE members SET NHoodID=".$_POST['NHbox'].", Notes='".$_POST['ud_notes']."' WHERE MemberID=".$_POST['ud_memberID'];


// echo '<script type="text/javascript"> alert("The SQL is:\n'.$sql.'")</script>';

			$query=$dbh->prepare("UPDATE members SET NHoodID=:nhName, Notes=:notes, latLong=:latlong WHERE MemberID=:uID");
		$query->bindParam(':nhName', $nhName);
		$query->bindParam(':notes', $notes);
		$query->bindParam(':uID', $uID);
		$query->bindParam(':latlong', $latLong);

		$nhName= $_POST['NHbox'];
		$notes= $_POST['ud_notes'];
		$uID=$_POST['ud_memberID'];
		$latLong=$_POST['ud_latLong'];

//echo '<script type="text/javascript"> alert("Going to try to save nhname, memberNotes, and latLong by memberid")</script>';

		try
		{
			$query->execute();
//			echo '<script type="text/javascript"> alert("query executed")</script>';
		}
		catch(PDOException $e)
		{
			echo '<script type="text/javascript"> alert("assignToNhood() has NOT saved. \r\n Error: '.$e->getMessage().'")</script>';
			die();
		}
*/
echo 'assignToNhood() was called';
}


?>
