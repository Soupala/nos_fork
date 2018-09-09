<!DOCTYPE html>
<?php include("securepage/nfp_password_protect.php"); ?>
	<?php
	include("config.php");
	include ("functions.php");
	include("updated.php");


	$myMapKey=getMapKey();
	//first, save any changes
		if(isset($_GET['save']))
		updateMemberEdits($_POST);

	//check if the user is editing themselves (non-self edits open in a new browser tab with a message reminding the user of that)
		// if(isset($_GET['self']))
			// $self=true;
		// else $self=false;

		//then pull up data

	$fdid=$_GET['fdid'];					// ID of the member to be edited
	$uid=$_GET['uid'];						// ID of the logged in user
	$ugroups=getUserGroups($uid);			// groups of the logged in user
	$egroups=getUserGroups($fdid);		// groups of the logged in user

	if($fdid==$uid)
		$self=true;
	else $self=false;

	if (isset($_GET["fname"]))
		$fname=$_GET["fname"];				// FirstName of the member doing the editing
	if (isset($_GET["printName"]))
		$printName=$_GET["printName"];		// PrintName of the person doing the editing
	if (isset($_GET["email"]))
		$email=$_GET["email"];				// PreferredEmail of the person doing the editing




//echo 'your role: '.$role;
	$permissionsArr=getEditPermissions($ugroups);

//uncomment to debug $permissionsArr
			// echo '<div style="position:absolute; right:50px; top:200px; width:200px; border:"3" solid red;">permissionsArr:<br/>';
			// print_r($permissionsArr);
			// echo '</div>';


	//open the database
	opendb();

	//get all member info
	$result= mysql_query("SELECT * from members where MemberID=".$fdid);
	$row=mysql_fetch_array($result);
	$NHoodID=$row['NHoodID'];

	?>
<html>
<head>
	<meta name="description" content="The Neighborhood Food Project" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Neighborhood Food Project</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script type="text/javascript" src="js/scripts.js"> </script>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $myMapKey; ?>&libraries=geometry"></script>

	<script type="text/javascript" >
		var map;
		var marker;
		var latlng = new google.maps.LatLng <?php echo $row['latLong']?>;
		var geocoder= new google.maps.Geocoder();




function initialize()
{
	//make the map


		var options = {
			zoom: 17,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById("popupDiv"), options);

		marker = new google.maps.Marker(
			{
			  position: latlng,
			  map: map,
			  draggable:true
			});


		google.maps.event.addListener(marker, "dragend", function(event)
			{
				document.getElementById("ud_latLong").value=event.latLng;
			});
}

/////////////////////////////////////////////////
function finetuneGeocode()
{
	//move map div to screen
	document.getElementById('popupDiv').style.left="25%";
}

/////////////////////////////////////////////////

		function displayPWdiv()
		{
			var theDiv=document.getElementById("changePasswordDiv");
			if(theDiv.style.display == "none")
				theDiv.style.display = "block";
			else theDiv.style.display = "none";
		}
/////////////////////////////////////////////////

		function reassignToggleDiv()
		{
			var theDiv=document.getElementById("reassignToggleDiv");
			if(theDiv.style.display == "none")
				theDiv.style.display = "block";
			else theDiv.style.display = "none";
		}
/////////////////////////////////////////////////



function resetGeo()
{
	var house=document.getElementById("ud_house").value;
	var street=document.getElementById("ud_street").value;
	var city=document.getElementById("ud_city").value;
	var state=document.getElementById("ud_state").value;

	var address=house+" "+street+" "+city+" "+state;
//alert("resetting latlong to: "+address);
	//codeAddress(address, "ud_latLong");


	geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK)
			{
			document.getElementById("ud_latLong").value=results[0].geometry.location;
			marker.setPosition(results[0].geometry.location);
			map.setCenter(marker.getPosition());
			}
		else
		{alert("Geocode was not successful for the following reason: " + status);}
	});


}

	</script>


    <link rel="stylesheet" href="https://apps.neighborhoodfoodproject.org/css/jquery-ui.css" />
     <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
     <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>


    <script>
    $(function() {
				$( "#ud_dateEntered" ).datepicker({ dateFormat: "yy-mm-dd" });
    });
    </script>


</head>

<body class="Content" onload="initialize()">




	<!--	change pw	-->
	<div  id="changePasswordDiv" style="top: 90px; display:none; z-index:100; margin-right: auto; margin-left: auto; text-align:center; background-color: #A4DCED;">
		<br/><br/><br/><br/><br/><br/>
		<iframe id="pwIframe" src="resetpw.php?fdid=<?php echo $fdid ?>" name="ResetPW"		style="top: 60px; height:250px; width:300px; text-align:center; background-color: #01a7e5;">
			<p>Your browser does not support iframes.</p>
		</iframe>
		<input type="button" value="Close" onclick="displayPWdiv();"/>
	</div>


<!--	Personal data form		-->
<?php
	if($self==true)
		echo '<form id="editSelfForm" action="editMember.php?fdid='.$fdid.'&uid='.$uid.'&save=true&self=true" method="post">';
	else
		echo '<form id="editSelfForm" action="editMember.php?fdid='.$fdid.'&uid='.$uid.'&save=true" method="post">';
?>


<div class="profileNav">
	<h1 style="color: #4ba74d; padding: 5px 5px 15px 15px;">Profile</h1>
<?php if($self==false)
	echo '<div  style="position:absolute; top:5px; right:10px; height:15px;  background-color:#de040a; color:white;	border: solid 3px #bbb1a7;	-webkit-border-radius: 8px;	-moz-border-radius: 8px;	border-radius: 8px;	padding: 10px; z-index:20;">
	<p style="text-align: center; ">***This <b>Profile</b> page has opened in a separate browser tab from the home interface.  Save any changes & close this tab.***</p>
	</div>';
?>

<div class="profileFull">

<!-- THE TABLE -->
	<div class="profileLeft" style=" background-color:#4ba74d; top:32px; width:470px; margin-right: auto; margin-left: auto; border:3px solid #fafafd; webkit-border-radius:8px;	-moz-border-radius:8px;	border-radius:8px;">


		<table align="center" cellpadding="5" style="border:3px #bbb1a7 solid; webkit-border-radius:8px;	-moz-border-radius:8px;	border-radius:8px; ">

<?php


//debug:
	//echo 'debug info: '.$row['Source'];
//end debug

			// these member attributes appear in input areas and so can be changed by the user
				//LastName
					echo 	'<tr style="background-color:#fafde9"	name="*" ><td>	Last Name		</td><td>';
							if($permissionsArr["LastName"]=="w")
								echo '<input type="text"  name="ud_lname" rows=1 cols=36 style="background-color:transparent" value="'.$row["LastName"].'"/></td>	';
							else if ($permissionsArr["LastName"]=="r")
								echo $row["LastName"];
							//else if ($permissionsArr["LastName"]=="h")
							else	echo '<input type="hidden" name="ud_lname" value="'.$row["LastName"].'	"/>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';
				//FirstName
					echo 		'<tr style="background-color:#aef871"	name="*"><td>	First Name		</td><td>';
							if($permissionsArr["FirstName"]=="w")
								echo '<input type="text"  name="ud_fname" rows=1 cols=30 style="background-color:transparent" value="'.$row["FirstName"].'"/></td>	';
							else if ($permissionsArr["FirstName"]=="r")
								echo $row["FirstName"];
							//else if ($permissionsArr["FirstName"]=="h")
							else	echo '<input type="hidden" name="ud_fname" value="'.$row["FirstName"].'	"/>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';
					//House
					echo 	'<tr style="background-color:#fafde9"	name="*"><td>	House #	</td><td>';
							if($permissionsArr["House"]=="w")
								echo '<input type="text" id="ud_house" name="ud_house" rows=1 cols=30 style="background-color:transparent" value="'.$row["House"].'"/></td>	';
							else if ($permissionsArr["House"]=="r")
								echo $row["House"];
							//else if ($permissionsArr["House"]=="h")
							else	echo '<input type="hidden" name="ud_house" value="'.$row["House"].'	"/></td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';

				//StreetName
					echo 		'<tr style="background-color:#aef871"	name="*"><td>	Street Name		</td><td>';
							if($permissionsArr["StreetName"]=="w")
								echo '<input type="text" id="ud_street" name="ud_street" rows=1 cols=30 style="background-color:transparent" value="'.$row["StreetName"].'"/></td>	';
							else if ($permissionsArr["StreetName"]=="r")
								echo $row["StreetName"];
							//else if ($permissionsArr["StreetName"]=="h")
							else	echo '<input type="hidden" name="ud_street" value="'.$row["StreetName"].'	"/>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';
				//Apt
					echo 	'<tr style="background-color:#fafde9"	name="*"><td>	Apt/Suite/Unit				</td><td>';
							if($permissionsArr["Apt"]=="w")
								echo '<input type="text"  name="ud_apt" rows=1 cols=30 style="background-color:transparent" value="'.$row["Apt"].'"/></td>	';
							else if ($permissionsArr["Apt"]=="r")
								echo $row["Apt"];
							//else if ($permissionsArr["Apt"]=="h")
							else	echo '<input type="hidden" name="ud_apt" value="'.$row["Apt"].'	"/>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';
				//City
					echo 		'<tr style="background-color:#aef871"	name="*"><td>	City			</td><td>';
							if($permissionsArr["City"]=="w")
								echo '<input type="text" id="ud_city" name="ud_city" rows=1 cols=30 style="background-color:transparent" value="'.$row["City"].'"/></td>	';
							else if ($permissionsArr["City"]=="r")
								echo $row["City"];
							//else if ($permissionsArr["City"]=="h")
							else	echo '<input type="hidden" name="ud_city" value="'.$row["City"].'	"/>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';
				//State
					echo 	'<tr style="background-color:#fafde9"	name="*"><td>	State			</td><td>';
							if($permissionsArr["State"]=="w")
								echo '<input type="text" id="ud_state" name="ud_state" rows=1 cols=30 style="background-color:transparent" value="'.$row["State"].'"/></td>	';
							else if ($permissionsArr["State"]=="r")
								echo $row["State"];
							//else if ($permissionsArr["State"]=="h")
							else	echo '<input type="hidden" name="ud_state" value="'.$row["State"].'	"/>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';
				//Zip
					echo 		'<tr style="background-color:#aef871"	name="*"><td>	Zip				</td><td>';
							if($permissionsArr["Zip"]=="w")
								echo '<input type="text"  name="ud_zip" rows=1 cols=30 style="background-color:transparent" value="'.$row["Zip"].'"/></td>	';
							else if ($permissionsArr["Zip"]=="r")
								echo $row["Zip"];
							//else if ($permissionsArr["Zip"]=="h")
							else	echo '<input type="hidden" name="ud_zip" value="'.$row["Zip"].'	"/>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';

				//Neighborhood
					echo 		'<tr style="background-color:#fafde9"	name="*"><td>	Neighborhood			</td><td>';
						if($permissionsArr["NHoodID"]=="w")
							//	echo '<input type="text"  name="ud_nid" rows=1 cols=30 style="background-color:transparent" value="'.$row["NHoodID"].'"/></td>	';
							echo allNhoodCombobox("ud_nid", "", $row['NHoodID']);
						else if ($permissionsArr["NHoodID"]=="r")
							echo $row["NHoodID"];
						//else if ($permissionsArr["NHoodID"]=="h")
						else	echo '<input type="hidden" name="ud_nid" value="'.$row["NHoodID"].'	"/>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
						//else echo 'It appears you do not have permission to access this data.  Please contact your administrator."</td>';


				//PreferredEmail
					echo 		'<tr style="background-color:#aef871"	name="*"><td>	Primary Email</td><td>';
							if($permissionsArr["PreferredEmail"]=="w")
								echo '<input type="text"  name="ud_email1" rows=1 cols=80 style="background-color:transparent" value="'.$row["PreferredEmail"].'"/></td>	';
							else if ($permissionsArr["PreferredEmail"]=="r")
								echo $row["PreferredEmail"];
							//else if ($permissionsArr["PreferredEmail"]=="h")
							else	echo '<input type="hidden" name="ud_email1" value="'.$row["PreferredEmail"].'	"/>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';

				//SecondaryEmail
					echo 		'<tr style="background-color:#fafde9"	name="*"><td>
					More Emails	</td><td>';
							if($permissionsArr["SecondaryEmail"]=="w")
								echo '<input type="text"  name="ud_email2" rows=1 cols=80 style="background-color:transparent" value="'.$row["SecondaryEmail"].'"/></td>	';
							else if ($permissionsArr["SecondaryEmail"]=="r")
								echo $row["SecondaryEmail"];
							//else if ($permissionsArr["SecondaryEmail"]=="h")
							else	echo '<input type="hidden" name="ud_email2" value="'.$row["SecondaryEmail"].'	"/>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';

				//PreferredPhone
					echo 		'<tr style="background-color:#aef871"	name="*"><td>	Phone #s	</td><td>';
							if($permissionsArr["PreferredPhone"]=="w")
								echo '<input type="text"  name="ud_phone1" rows=1 cols=30 style="background-color:transparent" value="'.$row["PreferredPhone"].'"/></td>	';
							else if ($permissionsArr["PreferredPhone"]=="r")
								echo $row["PreferredPhone"];
							//else if ($permissionsArr["PreferredPhone"]=="h")
							else	echo '<input type="hidden" name="ud_phone1" value="'.$row["PreferredPhone"].'	"/>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							echo '		</tr>';



//////////////////////////////////////////////////////
//	MEMBER ROLE AND STATUS VARIABLES				//
//////////////////////////////////////////////////////

					echo 		'<tr style="background-color:#fafde9"	name="*"><td>	Role			</td><td style="text-align:left">	';
	//
	// Role
					$isFD=$egroups["FD"];
						if ($isFD==0)
							$isFD="";
						else $isFD="checked";
					$isNC=$egroups["NC"];
						if ($isNC==0)
							$isNC="";
						else $isNC="checked";
					$isDC=$egroups["DC"];
						if ($isDC==0)
							$isDC="";
						else $isDC="checked";
					$isWC=$egroups["WC"];
						if ($isWC==0)
							$isWC="";
						else $isWC="checked";
					$isWM=$egroups["WM"];
						if ($isWM==0)
							$isWM="";
						else $isWM="checked";
					$isDM=$egroups["DM"];
						if ($isDM==0)
							$isDM="";
						else $isDM="checked";
					$isADMIN=$egroups["ADMIN"];
						if ($isADMIN==0)
							$isADMIN="";
						else $isADMIN="checked";
					$isVC=$egroups["VC"];
						if ($isVC==0)
							$isVC="";
						else $isVC="checked";


							if ($permissionsArr['FD'] == "w")
								echo 	'<input type="checkbox"  name="isFD"  style="background-color:transparent" value="isFD" '.$isFD.' />Food Donor<br/>';
							else if ($permissionsArr['FD'] == "r")
								echo 	'<input type="checkbox"  name="isFD"  style="background-color:transparent" value="isFD" '.$isFD.' disabled="disabled" />Food Donor<br/>';
							//else if($permissionsArr['FD'] =="h")
							else	echo 	'<input type="hidden"  name="isFD"  style="background-color:transparent" value="isFD" '.$isFD.' />Food Donor<br/>';

							if ($permissionsArr['NC'] == "w")
								echo 	'<input type="checkbox"  name="isNC"  style="background-color:transparent" value="isNC" '.$isNC.' />Neighborhood Coordinator<br/>';
							else if ($permissionsArr['NC'] == "r")
								echo 	'<input type="checkbox"  name="isNC"  style="background-color:transparent" value="isNC" '.$isNC.' disabled="disabled" />Neighborhood Coordinator<br/>';
							//else if($permissionsArr['NC'] =="h")
							else	echo 	'<input type="hidden"  name="isNC"  style="background-color:transparent" value="isNC" '.$isNC.' />Neighborhood Coordinator<br/>';

							if ($permissionsArr['DC'] == "w")
								echo 	'<input type="checkbox"  name="isDC"  style="background-color:transparent" value="isDC" '.$isDC.' />District Coordinator<br/>';
							else if ($permissionsArr['DC'] == "r")
								echo 	'<input type="checkbox"  name="isDC"  style="background-color:transparent" value="isDC" '.$isDC.' disabled="disabled" />District Coordinator<br/>';
							//else if($permissionsArr['DC'] =="h")
							else	echo 	'<input type="hidden"  name="isDC"  style="background-color:transparent" value="isDC" '.$isDC.' />District Coordinator<br/>';

							if ($permissionsArr['WC'] == "w")
								echo 	'<input type="checkbox"  name="isWC"  style="background-color:transparent" value="isWC" '.$isWC.' />Welcome Committee<br/>';
							else if ($permissionsArr['WC'] == "r")
								echo 	'<input type="checkbox"  name="isWC"  style="background-color:transparent" value="isWC" '.$isWC.' disabled="disabled" />Welcome Committee<br/>';
							//else if($permissionsArr['WC'] =="h")
							else		echo 	'<input type="hidden"  name="isWC"  style="background-color:transparent" value="isWC" '.$isWC.' />Welcome Committee<br/>';


							if ($permissionsArr['WM'] == "w")
								echo 	'<input type="checkbox"  name="isWM"  style="background-color:transparent" value="isWM" '.$isWM.' />Web Master<br/>';
							else if ($permissionsArr['WM'] == "r")
								echo 	'<input type="checkbox"  name="isWM"  style="background-color:transparent" value="isWM" '.$isWM.' disabled="disabled" />Web Master<br/>';
							//else if($permissionsArr['WM'] =="h")
							else		echo 	'<input type="hidden"  name="isWM"  style="background-color:transparent" value="isWM" '.$isWM.' />Web Master<br/>';


							if ($permissionsArr['DM'] == "w")
								echo 	'<input type="checkbox"  name="isDM"  style="background-color:transparent" value="isDM" '.$isDM.' />Data Manager<br/>';
							else if ($permissionsArr['DM'] == "r")
								echo 	'<input type="checkbox"  name="isDM"  style="background-color:transparent" value="isDM" '.$isDM.' disabled="disabled" />Data Manager<br/>';
							//else if($permissionsArr['DM'] =="h")
							else	echo 	'<input type="hidden"  name="isDM"  style="background-color:transparent" value="isDM" '.$isDM.' />Data Manager<br/>';


							if ($permissionsArr['ADMIN'] == "w")
								echo 	'<input type="checkbox"  name="isADMIN"  style="background-color:transparent" value="isADMIN" '.$isADMIN.' />Administrator<br/>';
							else if ($permissionsArr['ADMIN'] == "r")
								echo 	'<input type="checkbox"  name="isADMIN"  style="background-color:transparent" value="isADMIN" '.$isADMIN.' disabled="disabled" />Administrator<br/>';
							//else if($permissionsArr['ADMIN'] =="h")
							else	echo 	'<input type="hidden"  name="isADMIN"  style="background-color:transparent" value="isADMIN" '.$isADMIN.' />Administrator<br/>';


							if ($permissionsArr['VC'] == "w")
								echo 	'<input type="checkbox"  name="isVC"  style="background-color:transparent" value="isVC" '.$isVC.' />Volunteer Coordinator';
							else if ($permissionsArr['VC'] == "r")
								echo 	'<input type="checkbox"  name="isVC"  style="background-color:transparent" value="isVC" '.$isVC.' disabled="disabled" />Volunteer Coordinator';
							//else if($permissionsArr['VC'] =="h")
							else	echo 	'<input type="hidden"  name="isVC"  style="background-color:transparent" value="isVC" '.$isVC.' />Volunteer Coordinator';



	// STATUS

					if($permissionsArr["Status"]=="w")
						echo '<tr style="background-color:#aef871"><td> Status</td><td>'.statusMenu($row['Status']).'</td></tr>';
					else if ($permissionsArr["Status"]=="r")
						echo '<tr style="background-color:#aef871"><td> Status</td><td>'.$row['Status'].'<input type="hidden" name="ud_status" value="'.$row["Status"].'	"\></td></tr>';
					else
						echo '<tr style="background-color:#aef871"><td> Status</td><td><input type="hidden" name="ud_status" value="'.$row["Status"].'	"\></td></tr>';
		?>

<!--  LATLONG	-->
					<tr style="background-color:#fafde9"	>
						<td>Pickup Location</td>
						<td style="text-align: left; font-size: 13px; padding-top:25px; padding-right: 25px; padding-bottom: 15px;"><p style="background-color: #A4DCED; padding: 10px; border-radius: 10px;">If this member's address has not been geocoded yet, the default map location will be Wizard Island in the middle of Crater Lake National Park. You should: 1) Re-geocode  2) Save Changes.</p><br /><br />If the member moves across town, enter the new address information (#,Street,City,State,Zip) and then re-geocode. If the geolocation needs to be refined, select-and-drag the marker on the map below. Release the marker at the the correct location. Save Changes.
						</td>
					</tr>

						<tr style="background-color:#fafde9">
						<td></td>
						<td><input type="hidden" id="ud_latLong" name="ud_latLong" value="<?php echo $row['latLong'] ?>	" readonly />
							<input class="queuebuttons" style="padding: 10px;" type="button" id="geocode" name="geocode" value="Re-geocode" onclick="resetGeo()" />
						</td>
						</tr>
						<tr style="background-color: #fafde9">
						<td></td><td></td>
						</tr>


			</table>
								<!-- THE POPUP DIV - PUT THE FINE-TUNING MAP IN HERE	-->
							<div id="popupDiv" style="width:464px; height:400px; border-top: solid 0px #bbb1a7; border-left: solid 3px #bbb1a7; border-right: solid 3px #bbb1a7; border-bottom: solid 3px #bbb1a7; z-index:5; padding-bottom: 25px;" >
							You should be seeing a map here.  If not, please contact tech support for assistance.
							</div>
</div>

	<!--	Notes and PUnotes	-->
		<div class="rightWidget" style="background-color:#4ba74d; top:32px; border:3px solid #fafafd; height: 720px;">
			<table style="border:3px #bbb1a7 solid;">

					<tr style="background-color:#fafde9"	name="*"><td>Member Notes</td><td>
<?php
							if($permissionsArr["Notes"]=="w")
							//	echo '<input type="text"  name="ud_notes" rows=2 cols=100 style="background-color:transparent" value="'.$row["Notes"].'"/ ></td>	';
								echo '<textarea  name="ud_notes" rows=10 cols=36 style="background-color:transparent" / >'.$row["Notes"].'</textarea></td>	';
							else if ($permissionsArr["Notes"]=="r")
								echo $row["Notes"];
							//else if ($permissionsArr["Notes"]=="h")
							else	echo '<input type="hidden" name="ud_notes" value="'.$row["Notes"].'	"\>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';
?>
					<tr style="background-color:#aef871" name="*"><td>Pickup Notes</td><td>
<?php
							if($permissionsArr["PUNotes"]=="w")
								echo '<textarea  name="ud_punotes" rows=10 cols=36 style="background-color:transparent; width:100%;"/>'.$row["PUNotes"].'</textarea></td>	';
							else if ($permissionsArr["PUNotes"]=="r")
								echo $row["PUNotes"];
							//else if ($permissionsArr["PUNotes"]=="h")
							else	echo '<input type="hidden" name="ud_punotes" value="'.$row["PUNotes"].'	"\>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."
						echo '	</td>';

						// DATE ENTERED

					echo 		'<tr style="background-color:#fafde9"	name="*"><td>	Date Entered		</td><td>		';
							if($permissionsArr["DateEntered"]=="w")
								echo '<input type="text"  id="ud_dateEntered" name="ud_dateEntered" rows=1 cols=30 style="background-color:transparent" value="'.$row["DateEntered"].'"/ >
								</td>	';
							else if ($permissionsArr["DateEntered"]=="r")
								echo $row["DateEntered"];
							//else if ($permissionsArr["DateEntered"]=="h")
							else	echo '<input type="hidden" id="ud_dateEntered" name="ud_dateEntered" value="'.$row["DateEntered"].'	"\></td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';
	//
	//	Source

					echo 		'<tr style="background-color:#aef871"	name="*"><td>	Source		</td><td>		';
							if($permissionsArr["Source"]=="w")
								echo '<input type="text"  name="ud_source" rows=1 cols=30 style="background-color:transparent" value="'.$row["Source"].'"/ ></td>	';
							else if ($permissionsArr["Source"]=="r")
								echo $row["Source"];
							//else if ($permissionsArr["Source"]=="h")
							else	echo '<input type="hidden" name="ud_source" value="'.$row["Source"].'	"\>Oops.  It appears you do not have permission to access this data.  Please contact your administrator.</td>	';
							//else echo 'Oops.  It appears you do not have permission to access this data.  Please contact your administrator."</td>';
							echo '		</tr>';

							// MEMBER ID
								echo '<tr style="background-color:#fafde9"	><td>	<input type="hidden" name="ud_memberID" value="'.$row["MemberID"].'	"/>	MemberID		</td><td>';
								echo $row["MemberID"].'</td>';
								echo '</tr>';



								/////////////	PICKUP HISTORY	///////////////////
								echo '<tr style="background-color:#aef871"><td> Donor History</td><td><span>&nbsp;&nbsp;</span>';
									$numDates=6;
									$dates=getRecentPickupDates($numDates);
									if ($dates !='')
										echo getDonorHistoryTable($row["MemberID"], $dates, $numDates);
									else echo 'No Pickup History Has Been Recorded';
								  echo ' </td></tr> ';

								?>
								<tr style="background-color: #aef871"><td> </td><td><a href="memberTally.php?uid=<?php echo $uid ?>&fdid=<?php echo $fdid ?>" target="ContentFrame">change history</a></td>
								</tr>
								<tr style="background-color:#aef871"><td></td><td></td></tr>


								<tr style="background-color:#fafde9"><td></td><td></td></tr>
								<tr style="background-color:#fafde9">
								<td>Re-Assign</td>
								<td>

								<a class="queuebuttons"style="padding: 10px; color: #272727;" href="reassign.php?fdid=<?php echo $fdid ?>&uid=<?php echo $uid ?>" target="ContentFrame">Initiate Request</a><br />
								</td>
								</tr>
								<tr style="background-color:#fafde9"><td></td><td></td></tr>


					</tr>
			</table>

		</div>
	 </form>


	</div>

</div>


			<div style="position: fixed; bottom: 0px; width: 100%; background-color: #272727; z-index: 6; padding: 0px 0px 10px 0px; border-top: solid 3px #bbb1a7;" >
			<div style="margin-left: 35%; margin-right: 30%;">
					<ul id="ncNav">
			<li><a style="background-color: #fafafb; color: #272727" href="#" onclick="document.getElementById('editSelfForm').submit();">Save Changes</a></li>
			<li><a style="background-color: #fafafb; color: #272727" href="#" onclick="displayPWdiv();">Set / Reset Password</a></li>
			</ul>
			</div>
			<div>








<?php
	mysql_close($con);
?>

</body>
</html>


<?php
function getlatFromLatLong($latlong)
	{
		return	 strtok($latlong, '(,') ;
	}


	function getlongFromLatLong($latlong)
	{
		strtok($latlong, '(,');
		return	 strtok(' )') ;
	}


?>
