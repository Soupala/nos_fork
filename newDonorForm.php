<!DOCTYPE html>
<?php 
include('securepage/nfp_password_protect.php'); 
include('functions.php');
opendb();
$poweruser=false;		//a poweruser is an ADMIN, DM, WC, OR DC

if(isset($_GET['uid']))
{	$uid=$_GET['uid'];
	$sql="SELECT NC,DC,DM,ADMIN,WC FROM groups WHERE uID=".$uid;
//echo '<p style="color:red">id='.$id.'</p><p style="color:green">$sql='.$sql.'</p>';
	$result=mysql_fetch_array(mysql_query($sql));
	if($result['NC']==1)
	{	$query="SELECT NHoodID, NHName FROM neighborhoods WHERE NCID=".$uid;
		$nhResult=mysql_fetch_array(mysql_query($query));
		$nID=$nhResult['NHoodID'];
		$nName=$nhResult['NHName'];
	}
	
	if ($result['DM']==1 || $result['ADMIN']==1 || $result['WC']==1 || $result['DC']==1)
	{	$poweruser=true;	}
}
?>

<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>New Donor Form of The Neighborhood Food Project</title>
	<meta name="description" content="NOS">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />
	<script src="js/toggletools.js"></script>
	<script src="js/swapdivs.js"></script>
	<script src="js/scripts.js"></script>
	<script src="js/libs/modernizr-2.5.3.min.js"></script>
	
	
	<script type="text/javascript">
		function getXMLHttp()
		{
		  var xmlHttp
		  try
		  {//Firefox, Opera 8.0+, Safari
				xmlHttp = new XMLHttpRequest();
		  }
		  catch(e)
		  {//Internet Explorer
			try
			{   xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");    }
			catch(e)
			{ 	try
				{   xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");      }
				catch(e)
				{  	alert("Your browser does not support AJAX!")
					return false;      }
				}
		  }
		  return xmlHttp;
		}


		function verifyDonor()
		{
			var fname=document.getElementById('FirstName').value;
			var lname=document.getElementById('LastName').value;
			var email=document.getElementById('Email').value;
			
			var xmlHttp = getXMLHttp();
			 
			xmlHttp.onreadystatechange = function()
			{
				if(xmlHttp.readyState == 4)
				{	//what to do when ajax returns
					var response=xmlHttp.responseText;
					response = response.replace(/(^\s+|\s+$)/g,'');


				//if the email address exists in the database, DO NOT SUBMIT THE FORM 
					if(response=="emailexists")
					{
						alert("That email is already in use within our system.\n Please use a different email address or contact your administrator.");
						document.getElementById("Email").style.color="red";
					}
				
				// if nothing is returned, there is nobody with that name in the database
				// so just submit the form	
					else if( response=='')
					{	//alert("nothing returned from db search -line92-");
						document.getElementById("newDonorForm").submit();
					}
				
				//if one or more similar names were found, show the confirm box	
					else					
					{	//show the confirm box
						var x =confirm(response);
						if(x==true)
							document.getElementById("newDonorForm").submit();
					}
				}
			}

			xmlHttp.open("GET", "newDonorForm-ajax.php?fname="+fname+"&lname="+lname+"&email="+email, true);
			xmlHttp.send(null);
		}

	</script>
	
	
	<script type="text/javascript" src="js/scripts.js"> </script>
</head>

<body style="background-color: white;">
	<?php if(isset($_POST['save']))
		$saved=	saveNewDonor($_POST);
		else $saved=false;
		if($saved)echo '<p style="font-size: 18px; color:green; padding-left:150px; padding-top:20px; ">***new member added successfully***</p>.';
	?>

<div id="nfp_signup_iframe" style="width: 480px; margin: 10px; padding: 15px; overflow: visible; border: solid 1pt #bbb1a7; border-radius: 8px;">
<h2>Sign Up Form</h2><br />
		<?php 
			if(isset($uid))
				echo'	<form method="post" id="newDonorForm" action="newDonorForm.php?uid='. $uid .'" >';
			else	echo'	<form method="post" id="newDonorForm" action="newDonorForm.php" >';
		?>
			<input type="hidden" name="save" value="true" />
			<table><tr><td>First Name</td><td>Last Name</td></tr>
				<tr><td><input type="text" name="FirstName" id="FirstName" maxlength="50" size="16"></td>
				<td><input type="text" name="LastName" id="LastName" maxlength="50" size="18"></td></tr>
			</table>
			<br />
			<table>
				<tr><td colspan="3"><h3>Pickup Address:</h3></td></tr>
				<tr><td>House/Street #</td><td>Street Name</td><td>Apt/Suite</td></tr>
				
				<tr><td><input type="text" name="HouseNumber" maxlength="25" size="8"></td>
					<td> <input type="text" name="street" /> </td>
					<td><input type="text" name="Apt" maxlength="25" size="6"></td>
				</tr>
				
				<tr><td>City</td><td>State</td><td>Zip Code</td></tr>
				
					<td> <input type="text" name="City" /></td>
					<td><input type="text" name="State" size="2" /></td>
					<td><input type="text" name="Zip" size="5"  /></td>
				</tr>
				</table>
				<tr><p style="font-size: 10px; color: green;">***Please do not omit City, State, or Zip Code***</p></tr>
			<br />
			<table>
			
				<tr><td colspan="3" ><h3>Primary Contact Info:</h3></td></tr>
				<tr><td colspan="2">Phone</td><td></td></tr>
				<tr><td colspan="2"><input type="text" name="Phone" maxlength="15" size="35"></td>
					<td></td>
				</tr>
				<tr><td>Email</td></tr>
					<td colspan="2"><input type="text" id="Email" name="Email" maxlength="50" size="50"></td>
				</tr>
				
			</table>
			<br />	
					
					<b>Willing to volunteer as:</b><br />
					<input type="checkbox" name="FD" value="FD" /> Food Donor<br />
					<input type="checkbox" name="NC" value="NC" /> Neighborhood Coordinator<br />
					<!-- These options are probably not relevent to the average person just signing up.
					These higher-level volunteer rolls can be added to a member via their Profile -->
						<!--
						<input type="checkbox" name="DC" value="DC" /> District Coordinator<br />
						<input type="checkbox" name="WC" value="WC" /> Welcome Committee<br />
						<input type="checkbox" name="DM" value="DM" /> Data Manager<br />
						<input type="checkbox" name="WM" value="WM" /> Web Master<br />
						<input type="checkbox" name="ADMIN" value="ADMIN" /> Administrator<br />
						-->
				
			
			<br /><p>Pickup Notes<br />
			<textarea name="AdditionalInfo" cols="50" rows="6"></textarea></p><br />
		
					 Source:<input type="text" name="source"/><br />
					 
			
			
	<?php 	//if the user is logged in, give them a chance to bypass the Welcome Committee and assign the 
			//new donor to a specific neighborhood
	if (isset($nID))
	{//echo $nID;
//	echo '	<tr><td> suggested Neighborhood </td><td>'. NeighborhoodsCombobox("NhoodsBox").'</td></tr>'; 
		echo '<hr/>';
		echo '<br/>Skip Welcome Committee and send directly to: ';
		echo '<br /><br/>My Neighborhood ('.$nName.') &nbsp;&nbsp; <input type="checkbox" name="NHoodsBox" id="myNhood" value="'.$nID.'"/> '	;
		echo ' <br /> ';
	}
	if($poweruser && isset($nID)) 
	{	echo '<p>OR</p>  ';
	}

	if($poweruser)
	{
		echo 'Another Neighborhood: '.allNhoodsByNCNameCombobox("NCbox", "document.getElementById('myNhood').checked=false;").' <br />';
	}
?>
<br /><!--	<input type="submit" name="submit" value="Submit">	-->
	<input type="button" value="Submit" onclick="verifyDonor();" />
		</form>

	</div>
           
</body>
</html>                 




<?php 
function saveNewDonor($_POST)
{
	$fname=$_POST["FirstName"];
	$lname=$_POST["LastName"];
	$house=$_POST["HouseNumber"];
	$street=$_POST["street"];
	$apt=$_POST["Apt"];
	$city=$_POST["City"];
	$state=$_POST["State"];
	$zip=$_POST["Zip"];
	//$areacode1=$_POST["AreaCode1"];
	$phone=$_POST["Phone"];
	$email=$_POST["Email"];
	$info=$_POST["AdditionalInfo"];
	$source=$_POST['source'];
	if(isset($_POST["NHoodsBox"]))
	{
		$nhoodID=$_POST["NHoodsBox"];	//NhoodID
		//echo 'NHoodsBox: '.$nhoodID.'<br/>';
	}
	else if(isset($_POST['NCbox']))
	{
		$nhoodID=$_POST['NCbox'];
		//echo 'NCbox: |'.$nhoodID.'|<br/>';
	} 

	
	$dbh=openPDO();
	
	if(isset($nhoodID) && $nhoodID!=0 && $nhoodID!='')
	{	
		$insertSQL="INSERT INTO members (FirstName, LastName, House, StreetName, Apt, City, State, Zip, PreferredPhone, PreferredEmail,  PUNotes, DateEntered,NHoodID,accepted, hasBag, source )
				VALUES (:fname,:lname,:house,:street,:apt,:city,:state,:zip,:phone,:email,:info,CURDATE(),:nhoodID, :accepted, :hasbag, :source)"	;
//echo '$nhoodID=true<br/> insertSQL='.$insertSQL;
		$insert=$dbh->prepare($insertSQL);
	}			
	else
	{	//echo 'inserting without $nhoodID (line 213)';
		$insertSQL="INSERT INTO members (FirstName, LastName, House, StreetName, Apt, City, State, Zip, PreferredPhone, PreferredEmail,  PUNotes, DateEntered, source)
			VALUES (:fname,:lname,:house,:street,:apt,:city,:state,:zip,:phone,:email,:info,CURDATE(), :source)";
		
		$insert=$dbh->prepare($insertSQL);
	}	
				
		$insert->bindParam(':fname', $fName );
		$insert->bindParam(':lname', $lName);
		$insert->bindParam(':house', $houseNum);
		$insert->bindParam(':street', $streetName);
		$insert->bindParam(':apt', $apartment);
		$insert->bindParam(':city', $theCity);
		$insert->bindParam(':state', $theState);
		$insert->bindParam(':zip', $theZip);
		$insert->bindParam(':phone', $thePhone);
		$insert->bindParam(':email', $theEmail);
		$insert->bindParam(':info', $theNotes);
		$insert->bindParam(':source',$theSource);
		if(isset($nhoodID) && $nhoodID!=0 && $nhoodID!='')
		{	$insert->bindParam(':nhoodID',$nHoodID );
			//$nHoodID=$nhoodID;
			$insert->bindParam(':accepted', $accepted);
			$insert->bindParam(':hasbag', $hasbag);
		}
		
		$fName=$fname;
		$lName=$lname;
		$houseNum=$house;
		$streetName=$street;
		$apartment=$apt;
		$theCity=$city;
		$theState=$state;
		$theZip=$zip;
		$thePhone=$phone;
		$theEmail=$email;
		$theNotes=$info;
		$theSource=$source;
	if(isset($nhoodID) && $nhoodID!=0 && $nhoodID!='')
	{//	$nHoodID=$nhoodID;
	$nHoodID=$nhoodID;
		$accepted=1;
		$hasbag=1;
	}

		
		try
		{		$insert->execute();		
		}
		catch(PDOException $e)
		{
			echo '<script type="text/javascript"> alert("updateContact() failed to UPDATE the log. \n\n Error: '.$e->getMessage().'")</script>';
			die();
		}
	
	$newID=$dbh->lastInsertId();



	

		
		
		
////////////////////////////////
//	roles
///////////////////////////////////
	//$roles[]
	//mysql_close($con);
	opendb();

	
//get the MemberID from the new member INSERT in order to perform the 'groups' table insert
	$uidSQL="SELECT MemberID FROM members 	WHERE FirstName='".$fname."'	AND LastName='".$lname."'	AND House='".$house."'	AND StreetName='".$street."'	AND PreferredPhone='".$phone."' 	AND PreferredEmail='".$email."' ORDER BY MemberID DESC";
	
//	echo '$uidSQL:		'.$uidSQL;
	
	
	$idArr=mysql_fetch_array(mysql_query($uidSQL));
	
	
// //DEBUG:	
// 		echo '<script type="text/javascript">alert("$idArr:\n'.$idArr['MemberID'].'");</script>';
	$memberID=$idArr['MemberID'];
// //DEBUG:	
// 		echo '<script type="text/javascript">alert("$idArr:\n'.$idArr.'\n\n$memberID:\n'.$memberID.'");</script>';
	if(isset($_POST['FD']))
		$isFD=1;
	else $isFD=0;
	if(isset($_POST['NC']))
		$isNC=1;
	else $isNC=0;
	if(isset($_POST['DC']))
		$isDC=1;
	else $isDC=0;
	if (isset($_POST['WC']))
		$isWC=1;
	else $isWC=0;
	if(isset($_POST['DM']))
		$isDM=1;
	else $isDM=0;
	if(isset($_POST['WM']))
		$isWM=1;
	else $isWM=0;
	if(isset($_POST['ADMIN']))
		$isADMIN=1;
	else $isADMIN=0;
	
//	echo 'memberID:'.$memberID.' isFD:'.$isFD.' isNC:'.$isNC.' isDC:'.$isDC.' isWC:'.$isWC.' isDM:'.$isDM.' isWM:'.$isWM.' isADMIN:'.$isADMIN;
	
	// $groupInsert="INSERT INTO groups (uID, FD, NC, DC, WC, DM, WM, ADMIN) VALUES(".$memberID.",".$isFD.",".$isNC.",".$isDC.",".$isWC.",".$isDM.",".$isWM.",".$isADMIN.")";
	$groupInsert="INSERT INTO groups (uID, FD, NC, DC, WC, DM, WM, ADMIN) VALUES(".$newID.",".$isFD.",".$isNC.",".$isDC.",".$isWC.",".$isDM.",".$isWM.",".$isADMIN.")";
//echo '<p style="color:green">the insert:<br/>'.$groupInsert.'</p>';
	$insertResult=mysql_query($groupInsert);

//uncomment to debug:	
//	if($insertResult)
//		echo '<p style="color:lime">Added User to Groups Table</p><br/>';
//	else echo '<p style="color:red">COULD NOT INSERT INTO GROUPS TABLE</p><hr/>'.
//			mysql_errno() . ': ' . mysql_error() . '<hr/>';
	/*
	 INSERT INTO members (FirstName, LastName, House, StreetName, Apt, City, State, Zip, PreferredPhone, PreferredEmail, SecondaryPhone, SecondaryEmail, Notes) VALUES ('ZZZ', 'DCtester', 123,'N','1ST ST','-', '0','Ashland','OR',97520,'541-555-5555','DCtester1@example.com','541-555-5555','DCtester2@example.com',' **** THIS IS A DEVELOPMENT TESTER ACCOUNT *** delete at will' )
	*/

	
	
	
	
	
	
	
	
	
	
	
	
	
	
// //LOG THE NEW SIGNUP
// 	$logSql="INSERT INTO log(changeMade, dateTime, signup) VALUES('".addslashes($sql)."',NOW(), 1)";
// 	$logResult=mysql_query($logSql);
// 	if($logResult) echo '<p style="color:lime">Log Successful</p>';
// 	else echo '<p style="color:maroon">Log Failed.<br/>logSQL:<br/>'.$logSql.'</p>';
	
	
	//////////////////////
	//	log changes 	//
		$dbh=openPDO();
		
		
		
		if(isset($nhoodID) && $nhoodID!=0 && $nhoodID!='')
		{ 	$logSQL="INSERT INTO members (FirstName, LastName, House, StreetName, Apt, City, State, Zip, PreferredPhone, PreferredEmail,  PUNotes, DateEntered,NHoodID)
				VALUES (".$fname.",".$lname.",".$house.",".$street.",".$apt.",".$city.",".$state.",".$zip.",".$phone.",".$email.",".$info.",CURDATE(),".$nhoodID.")	
				<br/>
				MemberID: ".$memberID	;
		}			
		else
		{
			$logSQL="INSERT INTO members (FirstName, LastName, House, StreetName, Apt, City, State, Zip, PreferredPhone, PreferredEmail,  PUNotes, DateEntered)
				VALUES (".$fname.",".$lname.",".$house.",".$street.",".$apt.",".$city.",".$state.",".$zip.",".$phone.",".$email.",".$info.",CURDATE())
				<br/>
				MemberID: ".$memberID	;
		}	
	
	
	
	
			$log=$dbh->prepare('INSERT INTO log (changeMade, dateTime, signup) VALUES (:logData, NOW(), 1) ');
			$log->bindParam(':logData', $logData);
			$logData=$logSQL;
			try
			{
				$log->execute();
				//echo '<p style="color:lime">Log of new member SUCCEEDED!</p>';
			}
			catch(PDOException $e)
		{
			//echo '<p style="color:maroon">Log Failed.<br/> Error: '.$e->getMessage().'")<br/>the sql:<br/>'.$logData.'</p>';
			die();
		}


		

		
		
		return true;
		
		
		
		}





?>
