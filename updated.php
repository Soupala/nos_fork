<?php
function updateMemberEdits($_POST)
{
$debug=false;

$lname=addslashes(trim($_POST['ud_lname']));
$fname=addslashes(trim($_POST['ud_fname']));
$house=trim($_POST['ud_house']);
$street=trim($_POST['ud_street']);
$Apt=trim($_POST['ud_apt']);
$city=trim($_POST['ud_city']);
$state=trim($_POST['ud_state']);
$zip=trim($_POST['ud_zip']);
$Nid=trim($_POST['ud_nid']);
$email1=trim($_POST['ud_email1']);
$email2=trim($_POST['ud_email2']);
$phone1=trim($_POST['ud_phone1']);
//$phone2=trim($_POST['ud_phone2']);
$notes=trim($_POST['ud_notes']);
$punotes=trim($_POST['ud_punotes']);
$memberID=trim($_POST['ud_memberID']);
$status=strtoupper($_POST['statusMenu']);
$latLong=$_POST['ud_latLong'];
// if (isset($_POST['confirmed']) && $_POST['confirmed']=="YES")
	// $confirmed=1;
	// else $confirmed=0;
$dateentered=trim($_POST['ud_dateEntered']);	
$source=trim($_POST['ud_source']);

	//Prepare roles values
if(isset($_POST['isFD']))
	$isFD=1;
else $isFD=0;
if(isset($_POST['isNC']))
	$isNC=1;
else $isNC=0;
if(isset($_POST['isDC']))
	$isDC=1;
else $isDC=0;
if(isset($_POST['isWC']))
	$isWC=1;
else $isWC=0;
if(isset($_POST['isWM']))
	$isWM=1;
else $isWM=0;
if(isset($_POST['isDM']))
	$isDM=1;
else $isDM=0;
if(isset($_POST['isVC']))
	$isVC=1;
else $isVC=0;
if(isset($_POST['isADMIN']))
	$isADMIN=1;
else $isADMIN=0;
	
	
	
	
	
	
	
	
	// //causes problems:
// if (!isset($functionsAreLoaded))
	// include ("functions.php");
opendb();
//log the member's info before updating the record
$preLogSql="SELECT * FROM members WHERE MemberID=".$memberID;
$preLogResult=mysql_fetch_array(mysql_query($preLogSql));

$changedFrom= "CHANGED FROM<br/>
		***MEMBERS TABLE*** <br/>
		FirstName: ".$preLogResult['FirstName'].", LastName: ".$preLogResult['LastName'].", House: ".$preLogResult['House'].", StreetName: ".$preLogResult['StreetName'].", Apt: ".$preLogResult['Apt'].", City: ".$preLogResult['City'].", State: ".$preLogResult['State'].", Zip: ".$preLogResult['Zip'].", NHoodID: ".$preLogResult['NHoodID'].", PreferredEmail: ".$preLogResult['PreferredEmail'].", SecondaryEmail: ".$preLogResult['SecondaryEmail'].", PreferredPhone: ".$preLogResult['PreferredPhone'].", Status: ".$preLogResult['Status'].", Notes: ".$preLogResult['Notes'].", PUNotes: ".$preLogResult['PUNotes'].", latLong: ".$preLogResult['latLong'].", DateEntered: ".$preLogResult['DateEntered'].", Source: ".$preLogResult['Source'];







//save to members table
$sql="UPDATE members SET LastName = '".addslashes($lname)."', FirstName='".addslashes($fname)."', House='".addslashes($house)."', StreetName='".addslashes($street)."', Apt='".addslashes($Apt)."',City='".addslashes($city)."',State='".$state."',Zip='".addslashes($zip)."',NHoodID='".$Nid."',PreferredEmail='".addslashes($email1)."',SecondaryEmail='".addslashes($email2)."', PreferredPhone='".$phone1."', Notes='".addslashes($notes)."', PUNotes='".addslashes($punotes)."', latLong='".$latLong."',Status='".$status."', DateEntered='".$dateentered."', Source='".addslashes($source)."'  WHERE MemberID='".$memberID."'";

$result=mysql_query($sql);

// if ($result)
	// echo '<p style="color:lime">member update successful</p>';
// else echo '<p style="color:red">COULD NOT UPDATE member DATABASE</p><hr/>'.
	// mysql_errno($con) . ': ' . mysql_error($con) . '<hr/>';
if ($result)
	$popup= '<p style="color:lime">member update successful</p>';
else $popup= '<p style="color:red">(90) COULD NOT UPDATE member DATABASE</p>'. mysql_error() . '<hr/>$sql:'.$sql.'<hr/>';	

	echo $popup;
	
//GROUPS
//first, set up to log the pre-change state	
	$preLogSql="SELECT * FROM groups WHERE uID=".$memberID;
	$preLogResult=mysql_fetch_array(mysql_query($preLogSql));
	
	$changedFrom.="<br/>***GROUPS TABLE***<br/>
			FD ".$preLogResult['FD'].", NC ".$preLogResult['NC'].", DC ".$preLogResult['DC'].", WC ".$preLogResult['WC'].", WM ".$preLogResult['WM'].",DM ".$preLogResult['DM'].", ADMIN ".$preLogResult['ADMIN'].", VC ".$preLogResult['VC'] ;
	
	
	
//save to groups table
$query="UPDATE groups SET FD=".$isFD.", NC=".$isNC.", DC=".$isDC.", WC=".$isWC.", WM=".$isWM.", DM=".$isDM.", VC=".$isVC.", ADMIN=".$isADMIN." WHERE uID=".$memberID;

$answer=mysql_query($query);
// if($answer)
	// echo '<p style="color:lime">groups update successful</p>';
// else echo '<p style="color:red">COULD NOT UPDATE group DATABASE</p><hr/>'.
	// mysql_errno($con) . ': ' . mysql_error($con) . '<hr/>';
if($answer)
	$popup.= '<p style="color:lime">groups update successful</p>';
else $popup.= '<p style="color:red">COULD NOT UPDATE group DATABASE</p><hr/>'; //.
//	mysql_errno($con) . ': ' . mysql_error($con) . '<hr/>';
	
	
//mysql_close($con);

//to debug the queries, uncomment this:
if($debug) echo $popup;








//echo '<p style="color:crimson">About to log the changes</p>';

//WRITE TO THE LOG
$changedTo="<br/>CHANGED TO:<br/>
			***MEMBERS TABLE***<br/>
			".$sql." <br/>
			***GROUPS TABLE***<br/>
			".$query;

$logSql="INSERT INTO log (changeMade, dateTime, memberTable) VALUES (\"".$changedFrom." <br/>".$changedTo."\", NOW(), 1) ";

$logresult=mysql_query($logSql);
// if($logresult) echo '<p style="color:green">LOGGED SUCCESSFULLY!</p>';
// else echo '<p style="color:red">FAILED TO LOG!<br/>
		// $logSql:<br/>
		// '.$logSql.'<br/>
		// mysql_error():<br/>
		// '.mysql_error().'</p>';






 //check that all values came through
/*echo ' <p style="color:blue">
		contactID: '.$memberID.'<br/>
		lname: '.$lname.'<br/>
		fname: '.$fname.'<br/>
		house: '.$house.'<br/>
		street: '.$street.'<br/>
		Apt: '.$Apt.'<br/>
		city: '.$city.'<br/>
		state: '.$state.'<br/>
		zip: '.$zip.'<br/>
		ncid: '.$Nid.'<br/>
		email1: '.$email1.'<br/>
		email2: '.$email2.'<br/>
		phone1: '.$phone1.'<br/>
		phone2: '.$phone2.'<br/>
		notes: '.$notes.'<br/>
		punotes: '.$punotes.'<br/>
		latLong: '.$latLong.'<br/>
		confirmed: '.$confirmed.'<br/>
		</p>
		';
*/
/*
echo ' <script type="text/javascript">alert("
		contactID: '.$memberID.'\n
		lname: '.$lname.'\n
		fname: '.$fname.'\n
		house: '.$house.'\n
		street: '.$street.'\n
		Apt: '.$Apt.'\n
		city: '.$city.'\n
		state: '.$state.'\n
		zip: '.$zip.'\n
		ncid: '.$Nid.'\n
		email1: '.$email1.'\n
		email2: '.$email2.'\n
		phone1: '.$phone1.'\n
		phone2: '.$phone2.'\n
		notes: '.$notes.'\n
		punotes: '.$punotes.'\n
		latLong: '.$latLong.'\n
		confirmed: '.$confirmed.'\n
		")</script>
		
		';
*/
}
?>