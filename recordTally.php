<!DOCTYPE html>
<html>
<?php

	include("securepage/nfp_password_protect.php");
	if(isset($_GET['nhood']))
		$nhoodID=$_GET['nhood'];

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


</head>

<body>
<h1 style="padding-left: 15px; padding-top: 5px; color: #2f4b66;">Record Tallysheets</h1>

<div style="padding: 15px;">
	<h3>Record pickups from tallysheets here.</h3><br/>
	<h3>1) Select a Neighborhood:<br/></h3>
		<form name="chooseNhood" action="recordTally.php?uid=<?php echo $uid ?>" method="get" >
			<?php
				//allow user to select a neighborhood
				echo '<input type="hidden" name="uid" value="'.$uid.'" />';

				echo allNhoodCombobox("nhood");

				echo '<input type="submit" value="SUBMIT" style="color: green; font-weight: bolder;">';
			?>
		</form>

	</div>







<div style="padding: 15px;">

<?php

	if(isset($_GET['nhood']))
	{

		//$nhoodID=$_GET['nhood'];
		//recordTallyForm($nhoodID, $id);
		$sql=mysql_query("SELECT NHName FROM neighborhoods WHERE NhoodID=".$nhoodID);
		$nhname=mysql_fetch_array($sql);
		$nhName=$nhname['NHName'];

		$defaultPickupDateValues = getDefaultPickupDateValues();
		$monthCode = $defaultPickupDateValues['MONTH_CODE'];
		$monthShort = $defaultPickupDateValues['MONTH_SHORT'];

		echo '<h2 style="color:green;"> Recording Tallysheet for '.$nhName;

		echo '<div class="widget" id="tallyFormDiv">';
		//start form
			echo '
				<form action="recordTally.php?uid='.$uid.'&save=true&nhood='.$nhoodID.'" method="post" style="color: green; font-weight: bolder;">
			';


			//	allow to pick a pickup date
			//		(month -feb, apr, jun, aug, oct, dec )
			//		(year = 2010, 2011, 2012, 2013, 2014, 2015)

			echo '		<br/></h3><h3>2) Next, specify which pickup you\'re recording:</h3><br/>';

			echo '
				<select name="pickupMonth">
					<option value="'.$monthCode.'">'.$monthShort.' </option>
					<option value="02">Feb </option>
					<option value="04">Apr</option>
					<option value="06">Jun </option>
					<option value="08">Aug </option>
					<option value="10">Oct</option>
					<option value="12">Dec </option>
				</select>
			';

			echo '
				<select name="pickupYear">
					<option value="2019">2019 </option>
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

			//	show all donors in the given neighborhood
			// 	provide yes/no radiobuttons for each donor (default=yes, the bag WAS picked up)

			//user fills in pickups


			//user clicks save - page reloads with &save=true
			echo ' <br /><br />For each donor below, select Yes, No, N/A, or Vacation, then hit the "SAVE" button.<br /><br /><input type="submit" value="SAVE" style="color: green; font-weight: bolder;" /*style="padding: 8px; font-size: 16px; font-weight: bolder; color: #c6ff96; background-color: #777777; border: 2px outset #bbb1a7; border-radius: 10px;"*/ />
				<br /><br />
			';

			//end form
//			echo '</form>';
//		echo '</div>';





//	echo '<div class="widget" >';
	$sql="SELECT FirstName,LastName,MemberID,routeOrder,PUNotes FROM members WHERE NHoodID=".$nhoodID." AND accepted=1 AND hasBag=1 AND(Status='ACTIVE' OR Status='INACTIVE') ORDER BY routeOrder,City,StreetName,House";
	$result=mysql_query($sql);

		echo '<table border="1">';
		echo '	<tr><th>Route<br/>Order</th><th>First Name</th><th>Last Name</th><th>Pickup Notes</th><th>Yes</th><th>No</th><th>N/A</th><th>Vacation</th>';
			while($row=mysql_fetch_array($result))
			{
				echo '
				<tr onmouseover="this.style.backgroundColor=\'grey\'" onmouseout="this.style.backgroundColor=\'white\'">
							<td>'.$row['routeOrder'].'</td><td>'.$row['FirstName'].'</td><td> '.$row['LastName'].'</td>
							<td><textarea  name="pickupNotes'.$row['MemberID'].'" rows="3" cols="35" style="width:90%;"  / >'.$row["PUNotes"].'</textarea></td>
							<td><input type="radio" name="Pickup'.$row['MemberID'].'" value="yes" checked /></td>
							<td><input type="radio" name="Pickup'.$row['MemberID'].'" value="no" /></td>
							<td><input type="radio" name="Pickup'.$row['MemberID'].'" value="na" /></td>
							<td><input type="radio" name="Pickup'.$row['MemberID'].'" value="v" /></td>
							<input type="hidden" name="memberID'.$row['MemberID'].'" value="'.$row['MemberID'].'" />
							<input type="hidden" name="nhoodID" value="'.$nhoodID.'" />
							</tr>	';

			}
		echo '</table>';
		echo '</form>';
	echo '</div>';

	}

	?>


</div>




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
	$id;					//	recorded by whom

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
		{	$insert="UPDATE pickupHistory SET lastChanged=CURDATE(), changedBy=".$id.",pickedUp=".$pickedUp." WHERE memberID=".$donorID." AND pickupDate='".$pickupYear."-".$pickupMonth."-01'";
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
