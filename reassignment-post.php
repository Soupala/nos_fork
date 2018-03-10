<!DOCTYPE html>
<html>
<?php

	include("securepage/nfp_password_protect.php"); 
	include('functions.php');
	include("config.php");
	opendb();
	
	
?>
<head>
	
	
	<link rel="stylesheet" type="text/css" href="css/styles.css" />
	
	
</head>

<body>
<?php

	$fdid=$_POST['fdid'];
	$reassignerNHoodID=$_POST['reassignNHoodID'];
	$requestfrom=trim($_POST['requestfrom']);
	$ReassignNotes=trim($_POST['ReassignNotes']);
	$ReassignNotesAndNHName="".$requestfrom."".$ReassignNotes."";
			$sqlUpdateReassign= "UPDATE members SET NHoodID=NULL, accepted=3, hideFromReassignerQueue=".$reassignerNHoodID.", ReassignNotes='".addslashes($ReassignNotesAndNHName)."', WCnotes=NULL, NCEmail=0 WHERE MemberID=".$fdid."";

	mysql_query($sqlUpdateReassign) or die ("Error in query: $sqlUpdateReassign");
        echo ' <h1 style="color: green; padding:15px;">Request Sent Succesfully!</h1> ';
?>

</body>
</html>