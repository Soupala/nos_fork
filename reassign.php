<!DOCTYPE html>
<html>
<?php
	include("securepage/nfp_password_protect.php");
    include("config.php");	
	include("functions.php");
	if(isset($_GET['fdid']))
	$fdid=$_GET['fdid'];
	if(isset($_GET['uid']));
	$uid=$_GET['uid'];
	opendb();
	
	$sqlReassign="SELECT * FROM members WHERE members.MemberID=".$fdid." ";
	$resultsReassign=mysql_query($sqlReassign);
	$makeReassign=mysql_fetch_array($resultsReassign);
	$ReassignNotes=$makeReassign['ReassignNotes'];
	$FirstNameReassign=$makeReassign['FirstName'];
	$LastNameReassign=$makeReassign['LastName'];
	$reassignNHoodID=$makeReassign['NHoodID'];
	
	if ($reassignNHoodID!=NULL)
	{
	$sqlReassignNH="SELECT * FROM neighborhoods WHERE NHoodID=".$reassignNHoodID." ";
	$resultsReassignNH=mysql_query($sqlReassignNH);
	$makeReassignNH=mysql_fetch_array($resultsReassignNH);
	$requestFromNHName=$makeReassignNH['NHName'];
	}
	
	else
	{
	$requestFromNHName="Opps. Something very rare has occurred. This Profile is not currently assigned to a any Neighborhood.  They were already sent to the Re-Assign Queue.  This can happen if the member is sent to the Re-Assign Queue more than once." ; 
	}
	
	
?>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Donation History</title>		
<link rel="stylesheet" type="text/css" href="css/styles.css" />


</head>

<body>

<div style="width: 500px; padding-left: 10px;">

<?php 
$printThis="<p style='font-size: 18px; color: red; background-color: yellow; padding: 10px; border: 2px solid #bbb1a7; border-radius: 10px;'>If you are the NC of the Neighborhood you are assigned to, make sure you re-assign your donors first, so they're not left dangling in outerspace without an NC.</p>" ;
if ($uid==$fdid)
echo $printThis;
?>  


	<form method="post" action="reassignment-post.php">
		
	<?php
					echo '<h1 style="color: green;">Send '.$FirstNameReassign.' '.$LastNameReassign.' to the <br />Welcome Committee <br />Re-Assign Queue</h1>
					    <input type="hidden" name="fdid" value="'.$fdid.'" />
						<input type="hidden" name="reassignNHoodID" value="'.$reassignNHoodID.'" /><br />
						<input type="hidden" name="requestfrom" value="Re-assignment request from '.$requestFromNHName.'"/><br /><h3 style="color: green;">Reasons/Notes for Re-Assignment:</h3><br />
						<textarea  name="ReassignNotes" rows="10" cols="36" style="background-color:white; border: 1px solid #bbb1a7; border-radius: 1px;" / >'.$ReassignNotes.'&#46;&nbsp;</textarea><br />
						';
					echo ' <input type="submit" value="Send Request" align="center" style="padding: 5px;" /> ';
	echo '</form>	';
?>

</form>
		
		
</div>


</body>
</html>
