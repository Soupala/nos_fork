<!DOCTYPE html>
<html>
<?php
	include("securepage/nfp_password_protect.php");
    include("config.php");	
	include('functions.php');
	if(isset($_GET['fdid']))
	$memberID=$_GET['fdid'];
	opendb();
?>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Donation History</title>		
<link rel="stylesheet" type="text/css" href="css/styles.css" />

<?php 
	$getdonorname=mysql_fetch_array(mysql_query("SELECT * FROM members WHERE MemberID=".$memberID));
		$donorfirst=$getdonorname['FirstName'];
		$donorlast=$getdonorname['LastName'];

?>

</head>

<body>
<?php

echo '<h2 style="padding-left: 2px; padding-top: 10px; padding-bottom: 10px;">UPDATE  HISTORY for '.$donorfirst.' '.$donorlast.'</h2>'; 

$sql = "SELECT * FROM pickupHistory WHERE memberID=".$memberID." ORDER BY pickupDate DESC LIMIT 6";

$result = mysql_query($sql) or die($sql."<br/><br/>".mysql_error());

$i = 0;

echo '<table width="50%" border="1px" cellpadding="15px" bgcolor="#aef871" style="color: #272727; font-size: 16px;">';
echo '<tr>';
echo '<td><b>Record ID</b></td>';
echo '<td><b>Pickup Event</b></td>';
echo '<td><b>Donated?</b></td>';
echo '</tr>';

echo "<form name='form_update' method='post' action='memberTallyupdated.php\n'";
while ($pickupHistory = mysql_fetch_array($result)) {
				if ($pickupHistory['pickedUp']==1)	{ $pu="YES"; }
				else if($pickupHistory['pickedUp']==0) { $pu="NO"; }
				else if($pickupHistory['pickedUp']==2) { $pu="VACATION/AWAY"; }
				else if($pickupHistory['pickedUp']==3) { $pu="NO DATA"; }
        echo '<tr>';
        echo "<td>{$pickupHistory['recordID']}<input type='hidden' name='recordID[$i]' value='{$pickupHistory['recordID']}' /></td>";
        echo "<td>{$pickupHistory['pickupDate']}<input type='hidden' name='pickupDate[i]' value='{$pickupHistory['pickupDate']}' /></td>";
		echo "<td>
		{$pu}<br />
					<select name='pickedUp[$i]'>
					    <option value='{$pickupHistory['pickedUp']}'>change to...</option>
						<option value='1'>yes</option>
						<option value='0'>no</option>
						<option value='2'>vacation/away</option>
						<option value='3'>no data</option>
					</select>
					</td>";
        echo '</tr>';
        ++$i;
}
echo '<tr>';
echo "<td></td>";
echo "<td></td>";
echo "<td><input type='submit' value='submit' align='center' style='padding: 5px;' /></td>";
echo '</tr>';
echo "</form>";
echo '</table>';


?>



</body>
</html>