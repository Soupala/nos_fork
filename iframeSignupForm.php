<!DOCTYPE html>

<?php
include('functions.php');
include ('config.php');

if(isset($_GET['save']))
{
  if(saveNewDonor($_POST))
	header("Location: ".getAppWebsite()."/thankyou.php");
	exit;
}

?>

<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Public Signup Form of The Neighborhood Food Project</title>
	<meta name="description" content="NOS">
	<!--<meta name="viewport" content="width=device-width">-->
	<link rel="stylesheet" href="css/style.css">
	<script src="js/scripts.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>

</head>

<body>
	<?php
		//if(isset($_POST['save']))
		//$saved=	saveNewDonor($_POST);
		//else $saved=false;
		//if($saved)echo '<p style="color:green; padding-left:150px; padding-top:20px; ">***new member added successfully***</p>.';
	?>

<div class="nfp_signup_A" style="width: 550px; overflow: visible; padding: 10px;">

      <div class="nfp_signup_B" style="text-align: left; margin-left: auto; margin-right: auto; width: 470px; margin: 10px; padding: 20px; overflow: visible; border: solid 1pt #bbb1a7; border-radius: 8px;">
      <form method="post" action="iframeSignupForm.php?save=true">
      			<input type="hidden" name="save" value="true" />
      			<table><tr><td>First Name</td><td>Last Name</td></tr>
      				<tr><td><input type="text" name="FirstName" maxlength="50" size="16" <?php if($failed) echo 'value="'.$_POST['FirstName'].'"';?>></td>
      				<td><input type="text" name="LastName" maxlength="50" size="18" <?php if($failed) echo 'value="'.$_POST['LastName'].'"';?>></td></tr>
      			</table>
      			<br />
      			<table>
      				<tr><td colspan="3"><h3>Pickup Address:</h3></td></tr>
      				<tr><td>House/Street #</td><td>Street Name</td><td>Apt/Suite</td></tr>

      				<tr><td><input type="text" name="HouseNumber" maxlength="25" size="8" <?php if($failed) echo 'value="'.$_POST['HouseNumber'].'"';?>></td>
      					<td> <input type="text" name="street" <?php if($failed) echo 'value="'.$_POST['street'].'"';?> /> </td>
      					<td><input type="text" name="Apt" maxlength="25" size="6" <?php if($failed) echo 'value="'.$_POST['Apt'].'"';?>></td>
      				</tr>

      				<tr><td>City</td><td>State</td><td>Zip Code</td></tr>


      					<td> <input type="text" name="City" <?php if($failed) echo 'value="'.$_POST['City'].'"';?>/></td>
      					<td><input type="text" name="State" size="2" <?php if($failed) echo 'value="'.$_POST['State'].'"';?>/></td>
      					<td><input type="text" name="Zip" size="5" <?php if($failed) echo 'value="'.$_POST['Zip'].'"';?> /></td>
      				</tr>
      				</table>
      				<tr><p style="font-size: 10px; color: green;">***Please do not omit City, State, or Zip Code***</p></tr>
      			<br />
      			<table>

      				<tr><td colspan="3" ><h3>Primary Contact Info:</h3></td></tr>
      				<tr><td colspan="2">Phone</td><td></td></tr>
      				<tr><td colspan="2"><input type="text" name="Phone" maxlength="15" size="35" <?php if($failed) echo 'value="'.$_POST['Phone'].'"';?> /></td>
      					<td></td>
      				</tr>
      				<tr><td>Email</td></tr>
      					<td colspan="2"><input type="text" name="Email" maxlength="50" size="50" <?php if($failed) echo 'value="'.$_POST['Email'].'"';?> /></td>
      				</tr>

      			</table>
      			<br />

      					<b>Willing to volunteer as:</b><br />
      					<input type="checkbox" name="FD" value="FD" <?php if($failed) echo 'value="'.$_POST['FD'].'"';?> /> Food Donor<br />
      					<input type="checkbox" name="NC" value="NC" <?php if($failed) echo 'value="'.$_POST['NC'].'"';?>/> Neighborhood Coordinator<br />

      			<br /><p>Additional Info<br />
      			<textarea name="AdditionalInfo" cols="50" rows="6" <?php if($failed) echo 'value="'.$_POST['AdditionalInfo'].'"';?>></textarea></p><br />

						<div class="g-recaptcha" data-sitekey="6Ldy7EsUAAAAAHfb6qlMHwHRd9ULyCQigWd1btn-"></div>

      			<br /><input type="submit" name="submit" value="Submit" >
      		</form>


					<p style="text-align: center; font-size: 11px; padding-top: 10px; color: #dd050a;">
					Form not working in your browser for some reason?</br> Try another <a href="http://en.wikipedia.org/wiki/Web_browser" target="_blank">browser</a> or just send <a href="mailto:<?php echo getFPemail() ?>">an email</a> directly to the food project.
					</p>

</div>

</div>


</body>
</html>

	<script src="js/libs/modernizr-2.5.3.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"> </script>



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
	$source="website";

////UNCOMMENT TO DEBUG:
//	echo '<p style="color:fuchsia">				$fname: '.$fname.'<br/>				$lname; '.$lname.'<br/>				address: '.$house.' '.$street.' '.$apt.' '.$city.' '.$state.' '.$zip.'<br/>				phone: '.$areacode1.' .$phone1<br/>				$nhoodID: 		';
//	if(isset($nhoodID))echo $nhoodID.'</p>';
//		else echo '</p>';

	$dbh=openPDO();

	//echo 'inserting without $nhoodID';
		$insertSQL="INSERT INTO members (FirstName, LastName, House, StreetName, Apt, City, State, Zip, PreferredPhone, PreferredEmail,  PUNotes, DateEntered, source)
			VALUES (:fname,:lname,:house,:street,:apt,:city,:state,:zip,:phone,:email,:info,CURDATE(), :source)";
//echo '$nhoodID=true<br/> insertSQL:<br/>'.$insertSQL;
		$insert=$dbh->prepare($insertSQL);


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


		try
		{		$insert->execute();
			//$insert->debugDumpParams();
		}
		catch(PDOException $e)
		{
			echo '<script type="text/javascript"> alert("updateContact() failed to UPDATE the log. \n\n Error: '.$e->getMessage().'")</script>';
			die();
		}

	$newID=$dbh->lastInsertId();







// ////UNCOMMENT TO DEBUG:
	// if($result)
		// echo '<p style="color:lime">New user added successfully</p><br/>';
	// else echo '<p style="color:red">COULD NOT UPDATE MEMBERS DATABASE</p><hr/>'.
			// mysql_errno() . ': ' . mysql_error() . '<hr/>';







////////////////////////////////
//	roles
///////////////////////////////////
	//$roles[]
	//mysql_close($con);
	opendb();


//UNCOMMENT TO DEBUG:
	// echo '<p style="color:lime">	$fname: '.$fname.'<br/>
				// $lname; '.$lname.'<br/>
				// address: '.$house.' '.$street.' '.$apt.' '.$city.' '.$state.' '.$zip.'<br/>
				// phone: '.$phone.'<br/>
				// $nhoodID: 		';
	// if(isset($nhoodID))echo $nhoodID.'</p>';
		// else echo '</p>';

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

	$groupInsert="INSERT INTO groups (uID, FD, NC, DC, WC, DM, WM, ADMIN) VALUES(".$newID.",".$isFD.",".$isNC.",".$isDC.",".$isWC.",".$isDM.",".$isWM.",".$isADMIN.")";

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
