<!DOCTYPE html>
<html>
<?php

	include("securepage/nfp_password_protect.php");

	$nhoodID=$_GET['nhID'];
	$uid=$_GET['uid'];

	include('functions.php');

	opendb();

?>
<head>
	<?php
		if(isset($_GET['save']))
		{	//debug:
				// print_r($_POST);
				// echo '<hr/>';
			//end debug
			saveTally($nhoodID, $_POST, $uid);
		}
	?>


	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />


</head>

<body>
<div style="padding: 15px;">

<?php


	if(isset($nhoodID))
	{
		$sql=mysql_query("SELECT NHName FROM neighborhoods WHERE NhoodID=".$nhoodID);
		$nhname=mysql_fetch_array($sql);
		$nhName=$nhname['NHName'];

		$defaultPickupDateValues = getDefaultPickupDateValues();
		$monthCode = $defaultPickupDateValues['MONTH_CODE'];
		$monthShort = $defaultPickupDateValues['MONTH_SHORT'];

		echo "<h1>'.$monthCode.'</h1>";

		echo '<h1 style="color:green; padding-bottom: 15px;"> Record Tally for '.$nhName.' </h1> ';

		echo '<div id="tallyFormDiv">';
		//start form
			echo '
				<form action="recordTallyNH.php?uid='.$uid.'&save=true&nhID='.$nhoodID.'&tallyReturn=1" method="post" style="color: #272727;">
			';


			//	allow to pick a pickup date

			echo ' <div style="background-color:#aef871; padding: 13px; border: 2px solid #bbb1a7; border-radius: 10px; text-align: center; margin-left: auto; margin-right; auto;">

			<span style="font-size: 18px; color: red; padding: 5px; font-weight: bolder;"> Choose Nearest Month & Year </span>

				<select style="font-size: 16px; margin-left: 15px; color: red; font-weight: bolder; border: 1px solid #bbb1a7; border-radius: 5px;" name="pickupMonth">
					<option value="02">Feb </option>
					<option value="04">Apr</option>
					<option value="06">Jun </option>
					<option value="08">Aug </option>
					<option value="10">Oct</option>
					<option value="12">Dec </option>
				</select>
			';

			echo '
				<select style="font-size: 16px; color: red; font-weight: bolder; border: 1px solid #bbb1a7; border-radius: 5px;" name="pickupYear">
					<option value="2018" style="color:purple;">2018 </option>
					<option value="2012">2012 </option>
					<option value="2013">2013 </option>
					<option value="2014">2014</option>
					<option value="2015">2015 </option>
					<option value="2016">2016 </option>
					<option value="2017">2017 </option>
					<option value="2018">2018</option>
					<option value="2019">2019 </option>
					<option value="2020">2020 </option>
				</select>
			';

		echo '</div>';

			//	show all donors in the given neighborhood
			//user fills in pickups



	echo '<div>';
	$sql="SELECT FirstName,LastName,MemberID,routeOrder,PUNotes FROM members WHERE NHoodID=".$nhoodID." AND accepted=1 AND hasBag=1 AND(Status='ACTIVE' OR Status='INACTIVE') ORDER BY routeOrder,City,StreetName,House";
	$result=mysql_query($sql);

		echo '<table style="width: 100%; padding: 10px; font-size: 16px; text-align: center; border: 1px solid #bbb1a7; border-radius: 5px;" border="1" bgcolor="#aef871" cellpadding="10">';
		echo '<tr><div style="text-align: right; padding-top: 10px; padding-bottom:10px;"><input type="submit" value="Send Data" class="queuebuttons" onclick="dataSent()" /></div></tr> ';
		echo '	<tr style="font-size: 18px; font-weight: bolder;"><td style="display: none;">Count</td><td></td><td style="text-align: left; padding-left: 10px;">Name</td><td>Pickup Notes</td><td>Y</td><td>N</td><td>V</td><td>N/A</td></tr>';
		$count=1;
			while($row=mysql_fetch_array($result))
			{
				if($count%2==0)
				echo '<tr style="background-color:#aef871; border: 1px #bbb1a7;" >';
				else echo  ' <tr style="background-color:#fafde9; border: 1px #bbb1a7;" > ';

				echo '	<td style="display: none">'.$count.'</td>';
				echo '<td><a href="editMember.php?fdid='.$row['MemberID'].'&uid='.$uid.'"  title="View/Edit this member\'s information" target="_blank"> <img src="icons/edit.png" alt="Edit This Member" width="30px" height="30px" /></a></td>
							<td style="padding: 10px; text-align: left;">'.$row['FirstName'].'&nbsp;'.$row['LastName'].'</td>
							<td><textarea rows="3" cols="40"  name="pickupNotes'.$row['MemberID'].'"  / >'.$row["PUNotes"].'</textarea></td>
							<td><input type="radio" name="Pickup'.$row['MemberID'].'" value="yes" checked /></td>
							<td><input type="radio" name="Pickup'.$row['MemberID'].'" value="no" /></td>
								<td><input type="radio" name="Pickup'.$row['MemberID'].'" value="v" /></td>
							<td><input type="radio" name="Pickup'.$row['MemberID'].'" value="na" /></td>
							<input type="hidden" name="memberID'.$row['MemberID'].'" value="'.$row['MemberID'].'" />
							<input type="hidden" name="nhoodID" value="'.$nhoodID.'" />
							</tr>';
							$count=$count+1;


			}
		echo '</table> </div>';
		echo '<tr><div style="text-align: right; padding-top: 10px;"><input type="submit" value="Send Data" class="queuebuttons" onclick="dataSent()" /></div></tr><br /> ';
		echo '</form>';
	echo '</div>';

	}

	?>


</div>

<script>
function dataSent()
{
alert("Click OK to continue.  The data you are submitting will replace any previous data you entered for the same month & year. This form does not show pickup history you've already recorded. Check your Tallysheet or View Donors list.  Also, at the moment, there IS NOT a form for entering just one person at a time.  However, after the pickup has been recorded a particular date, you can go modify individual donor history via their Profile.")
}
</script>




</body>



</html>





<?php

function saveTally($nhoodID, $_POST, $uid)
{
// 	echo 'from inside saveTally():<br/>';
// 	foreach($_POST as $key=>$entry) echo $key.'---'.$entry.'<br/>';
// 	echo 'pickupMonth:'.$_POST['pickupMonth'];
// 	echo 'pickupYear:'.$_POST['pickupYear'];


	//debug:
		//print_r($_POST);
	//end debug

	date_default_timezone_set('UTC');
	opendb();
	//things to save for each member in the nhood:
	$pickupMonth = $_POST['pickupMonth'];
	$pickupYear = $_POST['pickupYear'];			//	date of pickup

	$pickedUp;				//	yes or no
	//$now = CURDATE();					//	date recorded
	$uid;					//	recorded by whom

	$sql=mysql_query("SELECT MemberID from members WHERE NHoodID=".$nhoodID." AND accepted=1 AND hasBag=1 AND(Status='ACTIVE' OR Status='INACTIVE') ORDER BY routeOrder,City,StreetName,House");
	//WHERE NHoodID=".$nhoodID."there AND accepted=1 AND hasBag=1 AND(Status='ACTIVE' OR Status='INACTIVE') ORDER BY routeOrder,City,StreetName,House
	while($row=mysql_fetch_array($sql))
	{


		$donorID=$row['MemberID'];
		 if($_POST['Pickup'.$donorID] == "yes")
		 $pickedUp=1;
		 else if($_POST['Pickup'.$donorID] == "no")
			$pickedUp=0;
		else if($_POST['Pickup'.$donorID] == "v")
			$pickedUp=2;
		else if($_POST['Pickup'.$donorID] == "na")
			$pickedUp=3;
		else $pickedUp=null;

		 //check if there's already a record. If yes, update it. If no, insert a new record
		$recordCheck=mysql_query("SELECT * FROM pickupHistory WHERE	memberID=".$donorID." and pickupMonth=".$pickupMonth." and pickupYear=".$pickupYear);
		$numrows=mysql_num_rows($recordCheck);
		if ($numrows>0)
		{	$insert="UPDATE pickupHistory SET lastChanged=CURDATE(), changedBy=".$uid.",pickedUp=".$pickedUp." WHERE memberID=".$donorID." AND pickupDate='".$pickupYear."-".$pickupMonth."-01'";
			//debug:
				//echo 'UPDATE MEMBER '.$donorID.'<br/>$insert: '.$insert.'<hr/>';
			//end debug
		 }
		 else
	{	$insert="INSERT INTO pickupHistory (memberID, pickupMonth, pickupYear, pickupDate, lastChanged, changedBy, pickedUp)
						VALUES (".$donorID.",' ".$pickupMonth."', ".$pickupYear.", '".$pickupYear."-".$pickupMonth."-01', CURDATE(), ".$uid.", ".$pickedUp.")";
		//debug:
			//echo 'INSERT MEMBER '.$donorID.'<br/>$insert: '.$insert.'<hr/>';
		//end debug
		}
		//echo '<!-- '.$insert.'
		//';	-->

		$result=mysql_query($insert);
		if($result)
			echo '<!--	 SUCCESS ON MEMBER'.$donorID.'	-->
		';
		else echo '<!--	 FAILED ON MEMBER '.$donorID.'
				ERROR: '.mysql_error().'
				THE SQL: '.$insert.'-->
		';

		$dbh=openPDO();
		$update="UPDATE members SET PUNotes=:punotes WHERE MemberID=".$donorID;
		$pickupNotesUpdate=$dbh->prepare($update);
		$pickupNotesUpdate->bindParam(':punotes', $pickupnotes );
		$pickupnotes=$_POST['pickupNotes'.$donorID];

			try
		{		$pickupNotesUpdate->execute();		}
		catch(PDOException $e)
		{
			echo '<script type="text/javascript"> alert("updateContact() failed to UPDATE memberID '.$donorID.'\'s pickup notes. \n\n Error: '.$e->getMessage().'")</script>';
			die();
		}



	}//end while

}






?>
