<?php include("securepage/nfp_password_protect.php"); ?>
<html>
<body>

<?php
include("functions.php");
opendb();

$fname=$_POST["FirstName"];
$lname=$_POST["LastName"];
$house=$_POST["HouseNumber"];
$street=$_POST["street"];
$apt=$_POST["Apt"];
$city=$_POST["City"];
$state=$_POST["state"];
$zip=$_POST["zip"];
$areacode1=$_POST["AreaCode1"];
$phone1=$_POST["Phone1"];
$email1=$_POST["Email1"];
$areacode2=$_POST["AreaCode2"];
$phone2=$_POST["Phone2"];
$email2=$_POST["Email2"];
$info=$_POST["AdditionalInfo"];
if(isset($_POST['NhoodsBox']))
{	$nhoodID=$_POST["NhoodsBox"];	//NhoodID
}
 if (isset($_POST['NCbox']))
{	$nhoodID=$_POST['NCbox'];	}
//$roles=$_POST["role"];

echo 'passed-in street name: '.$street.'<br/>';

	$query=mysql_query("SELECT * FROM ashlandstreets WHERE  FullStreetName='".$street."'" );
	$queryResult=mysql_fetch_array($query);
	
$preDir=$queryResult['PRE_DIRECTION'];
$streetName=$queryResult['STREET_NAME'];
$streetType=$queryResult['STREET_TYPE_CODE'];
$postDir=$queryResult['POST_DIRECTION'];
if (!$postDir)
	$postDir="-";

//test
echo '<p style="color:blue">   	';
	
echo '<h3>You entered:</h3><br/>
		fname: '.$fname.'<br/>
		lname: '.$lname.'<br/>
		house: '.$house.'<br/>
		
		street selected from drop-down: '.$street.'<br/>	';
		
	echo 'preDir: '.$preDir.'<br/>
		streetName: '.$streetName.'<br/>
		streetType: '.$streetType.'<br/>
		postDir: '.$postDir.'<br/>
	';
	
	echo 'apt: '.$apt.'<br/>
		city: '.$city.'<br/>
		state: '.$state.'<br/>
		zip: '.$zip.'<br/>
		areacode 1: '.$areacode1.'<br/>
		phone 1: '.$phone1.'<br/>
		email 1: '.$email1.'<br/>
		areacode 2: '.$areacode2.'<br/>
		phone 2: '.$phone2.'<br/>
		email 2: '.$email2.'<br/>
		info:: '.$info.'<br/>
	';
	if(isset($nhoodID))
		echo 'nhoodID: '.$nhoodID.'<br/>';
echo '</p>	';

if (isset($nhoodID))
{
$sql= "INSERT INTO members (FirstName, LastName, House, StreetName, PostDirection, Apt, City, State, Zip, PreferredPhone, PreferredEmail, SecondaryPhone, SecondaryEmail, Notes, DateEntered,NHoodID)
	VALUES ('".$fname."','".$lname."',".$house.",'".$preDir."','".$streetName." ".$streetType."','".$postDir."','".$apt."','".$city."','".$state."',".$zip.",'".$areacode1."-".$phone1."','".$email1."','".$areacode2."-".$phone2."','".$email2."','".$info."',CURDATE(), ".$nhoodID."
	)";
}
else
{
$sql= "INSERT INTO members (FirstName, LastName, House,  StreetName, PostDirection, Apt, City, State, Zip, PreferredPhone, PreferredEmail, SecondaryPhone, SecondaryEmail, Notes, DateEntered,)
	VALUES ('".$fname."','".$lname."',".$house.",'".$preDir."','".$streetName." ".$streetType."','".$postDir."','".$apt."','".$city."','".$state."',".$zip.",'".$areacode1."-".$phone1."','".$email1."','".$areacode2."-".$phone2."','".$email2."','".$info."',CURDATE()
	)";
}	
	
	
$result=mysql_query($sql);

if($result)
	echo '<p style="color:lime">New user added successfully</p><br/>';
else echo '<p style="color:red">COULD NOT UPDATE DATABASE</p><hr/>'.
	mysql_errno($con) . ': ' . mysql_error($con) . '<hr/>';
//mysql_close($con);


echo '<hr/><br/>';
//if ($result)


//testing the sql
echo "you attempted to insert with the following query:<br/>

INSERT INTO members (FirstName, LastName, House, StreetName, Apt, City, State, Zip, PreferredPhone, PreferredEmail, SecondaryPhone, SecondaryEmail, Notes) VALUES ('".$fname."','".$lname."',".$house.",'".$preDir."','".$streetName." ".$streetType."','".$postDir."','".$apt."','".$city."','".$state."',".$zip.",'".$areacode1."-".$phone1."','".$email1."','".$areacode2."-".$phone2."','".$email2."','".$info."'
	)";

	
	
	echo '<hr/>';
////////////////////////////////
//	roles
///////////////////////////////////
//$roles[]
//mysql_close($con);
opendb();
echo 'email1:'.$email1.' firstName:'.$fname.' lastName:'.$lname.'<br/>';
//$NHood=mysql_fetch_array(mysql_query("	Select NHoodID,NHName from neighborhoods where NCID=".$NCid));
$idArr=mysql_fetch_array(mysql_query("SELECT MemberID FROM members WHERE PreferredEmail='".$email1."'"));

$memberID=$idArr['MemberID'];
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

	echo 'memberID:'.$memberID.' isFD:'.$isFD.' isNC:'.$isNC.' isDC:'.$isDC.' isWC:'.$isWC.' isDM:'.$isDM.' isWM:'.$isWM.' isADMIN:'.$isADMIN;

	$groupInsert="INSERT INTO groups (uID, FD, NC, DC, WC, DM, WM, ADMIN) VALUES(".$memberID.",".$isFD.",".$isNC.",".$isDC.",".$isWC.",".$isDM.",".$isWM.",".$isADMIN.")";
echo 'the insert:<br/>'.$groupInsert.'<br/>';
$insertResult=mysql_query($groupInsert);

if($insertResult)
	echo '<p style="color:lime">Added User to Groups Table</p><br/>';
else echo '<p style="color:red">COULD NOT INSERT INTO GROUPS TABLE</p><hr/>'.
	mysql_errno($con) . ': ' . mysql_error($con) . '<hr/>';
/*
INSERT INTO members (FirstName, LastName, House, StreetName, PostDirection, Apt, City, State, Zip, PreferredPhone, PreferredEmail, SecondaryPhone, SecondaryEmail, Notes) VALUES ('ZZZ', 'DCtester', 123,'N','1ST ST','-', '0','Ashland','OR',97520,'541-555-5555','DCtester1@example.com','541-555-5555','DCtester2@example.com',' **** THIS IS A DEVELOPMENT TESTER ACCOUNT *** delete at will' ) 
*/
?>

</body>
</html>