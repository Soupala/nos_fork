<?php

?>


	<script type="text/javascript" >
		function submitOnEnter(e, form)
		{
			var key=e.keyCode || e.which;
			if (key==13)
				{form.submit();}
		}
	</script>
	<style type="text/css">
		
		tr, td, th {padding:10px 10px 10px 10px; text-align:center; border:1px solid #bbb1a7;}
		table {color:#2d2e2f }
		
	</style>




	<?php
	$uid=$_GET['uid'];

/* to get records/page and to advance from page to page:
	need a MaxEntriesPerPage
	

*/	
////////////////////////////////////////////////////////////////////////////////
//		Construct the SQL query from the search boxes on top
//			- Each box that has a search string in it gets AND-ed to the LIKE
//			clause of the SQL query (the first gets the WHERE)
//			- When the search is initiated, each of text area is POST-ed to this same page
////////////////////////////////////////////////////////////////////////////////
$like="WHERE NHoodID=".$nhID;
	//search db WHERE $field LIKE $searchterm
	if (isset($_POST["LastNameBox"]))
	{
		if ($_POST["LastNameBox"] !="")
			if ($like=="")
				$like='WHERE NHoodID='.$nhID.' AND LastName LIKE "'.$_POST["LastNameBox"].'%" ';
			else $like.=' AND LastName LIKE "'.$_POST["LastNameBox"].'%" ';
		if ($_POST["FirstNameBox"] !="")
			if ($like=="")
				$like='WHERE  NHoodID='.$nhID.' AND FirstName LIKE "'.$_POST["FirstNameBox"].'%" ';
			else $like.='  AND FirstName LIKE "'.$_POST["FirstNameBox"].'%" ';
		else if ($_POST["StreetBox"] !="")
			if ($like == "")
				$like = 'WHERE  NHoodID='.$nhID.' AND StreetName LIKE "'.$_POST["StreetBox"].'%" ';
			else $like.='  AND StreetName LIKE "'.$_POST["StreetBox"].'%" ';
		else if ($_POST["AptBox"] !="")
			if ($like == "")
				$like = 'WHERE  NHoodID='.$nhID.' AND Apt LIKE "'.$_POST["AptBox"].'%" ';
			else $like.='  AND Apt LIKE "'.$_POST["AptBox"].'%" ';
		else if ($_POST["CityBox"]!="")
			if ($like == "")
				$like = 'WHERE  NHoodID='.$nhID.' AND City LIKE "'.$_POST["CityBox"].'%" ';
			else $like.='  AND City LIKE "'.$_POST["CityBox"].'%" ';
		else if ($_POST["ZipBox"]!="")
			if ($like == "")
				$like = 'WHERE  NHoodID='.$nhID.' AND Zip LIKE "'.$_POST["ZipBox"].'%" ';
			else $like.='  AND  Zip LIKE "'.$_POST["ZipBox"].'%" ';
			
		else if ($_POST["PrefEmailBox"]!="")
			if ($like == "")
				$like = 'WHERE  NHoodID='.$nhID.' AND PreferredEmail LIKE "'.$_POST["PrefEmailBox"].'%" ';
			else $like.='  AND PreferredEmail LIKE "'.$_POST["PrefEmailBox"].'%" ';
		
		else if ($_POST["SecondEmailBox"]!="")
			if ($like == "")
				$like = 'WHERE  NHoodID='.$nhID.' AND SecondaryEmail LIKE "'.$_POST["SecondEmailBox"].'%" ';
			else $like.='  AND PreferredEmail LIKE "'.$_POST["PrefEmailBox"].'%" ';			
			
		else if ($_POST["PrefPhoneBox"]!="")
			if ($like == "")
				$like = 'WHERE  NHoodID='.$nhID.' AND PreferredPhone LIKE "'.$_POST["PrefPhoneBox"].'%" ';
			else $like.='  AND PreferredPhone LIKE "'.$_POST["PrefPhoneBox"].'%" ';
			
		else if ($_POST["DateEnteredBox"]!="")
			if ($like == "")
				$like = 'WHERE  NHoodID='.$nhID.' AND DateEntered LIKE "'.$_POST["DateEnteredBox"].'%" ';
			else $like.='  AND DateEntered LIKE "'.$_POST["DateEnteredBox"].'%" ';

		else if ($_POST["SourceBox"]!="")
			if ($like == "")
				$like = 'WHERE  NHoodID='.$nhID.' AND SourceBox LIKE "'.$_POST["SourceBox"].'%" ';
			else $like.='  AND Source LIKE "'.$_POST["SourceBox"].'%" ';				

	else if ($_POST["HouseBox"]!="")
		if ($like == "")
			$like = 'WHERE  NHoodID='.$nhID.' AND House='.$_POST["HouseBox"];
		else $like.='  AND House='.$_POST["HouseBox"];		
		
	}

//////////////////////////////////////////////////////////////////////////////
	//orderBy:
		if (isset($_GET["orderBy"]))
			$orderBy=mysql_real_escape_string($_GET["orderBy"]);	
		else $orderBy="LastName";
		
		
	
	
	$maxRows=100;			//maximum number of rows to display at once
	$role="ADMIN";				// Role of the member doing the editing

	
//echo 'your role: '.$role;
//	$permissionsArr=getEditPermissions($role);

	 
	

	//open the database
	opendb();
	
	//get member info
	$donordbsql="SELECT * FROM members ".$like." AND accepted=1 AND hasBag=1 AND (Status='ACTIVE' OR Status='INACTIVE') ORDER BY ".$orderBy;
	$result= mysql_query($donordbsql);
	$MemberID=$result['MemberID'];
//echo '<script type="text/javascript"> alert("nhDonorsDB.php line 113\nthe sql:\n\n'.$sql.'")</script>';
	 //echo $donordbsql;
	

// TABLE LAYOUT DIV FOR UI
echo ' <div style="margin-bottom: 10px; margin-right: 60px; margin-left: 10px;"> ';

	
//////////////////////////////
//	Member data table		//	
//////////////////////////////


	echo '	<form id="flatDBForm" action="neighborhood.php?uid='.$uid.'&nh='. $nhID .'&ncid='.$ncid .'&tool=donorDbDiv" method="post" onkeypress="submitOnEnter(event, this)">';

			echo '<table border="1" style="font-size:10pt; border: #bbb1a7;" cellpadding="2">  ';

//////////////////////////////////////////////////////////////
// The column headers. user clicks on one to order by that column
			echo '<tr style="font-size: 14px;">
				<th>Edit</th>
				<th width=10>Pickup History</th>
				<th width=10>Row</th>
				<th width=20><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=LastName">	Last Name</a> </th>
				<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=FirstName">First Name </a>	</th>
				<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=StreetName,House">House	</a></th>
				<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=StreetName">Street Name	</a></th>
				<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=Apt">Apt	</a></th>
				<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=City">City </a></th>
				<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=State">State	</a></th>
				<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=Zip">Zip Code	</a></th>
				<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=PreferredEmail">Email	</a></th>
				<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=SecondaryEmail">2nd Email	</a></th>
				<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=PreferredPhone">Phone	</a></th>
				<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=DateEntered">Date Entered	</a></th>
				<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=Source">Source	</a></th>
<!--		<th><a href="neighborhood.php?uid='.$uid.'&nh='.$nhID.'&ncid='.$ncid.'&tool=donorDbDiv&orderBy=PUNotes">Pickup Notes</th>-->
				<th>Pickup Notes</th>


								
			</tr>
			';
//
//
//	end column headers
///////////////////////////////////


/////////////////////////////////
//the search boxes:
//
//	
	if (isset($_POST["LastNameBox"]))
			echo '
			<tr>
				
				<td colspan="3"><input type="submit" value="search"  /></td>
				
				<td><input type="text"	size="5" name="LastNameBox" value="'.$_POST["LastNameBox"].'" style="width:90%" /></td>
				<td><input type="text"	name="FirstNameBox" value="'.$_POST["FirstNameBox"].'" style="width:90%" /></td>
				<td><input type="text"	name="HouseBox" value="'.$_POST["HouseBox"].'" style="width:90%" /></td>
				<td><input type="text"	name="StreetBox" value="'.$_POST["StreetBox"].'" style="width:90%" /></td>
				<td><input type="text"	name="AptBox" value="'.$_POST["AptBox"].'" style="width:90%" /></td>
				<td><input type="text"	name="CityBox" value="'.$_POST["CityBox"].'" style="width:90%" /></td>
				<td><input type="text"	name="StateBox" value="'.$_POST["StateBox"].'" style="width:90%" /></td>
				<td><input type="text"	name="ZipBox" value="'.$_POST["ZipBox"].'" style="width:90%" /></td>
				
				
				<td><input type="text"	name="PrefEmailBox" value="'.$_POST["PrefEmailBox"].'" style="width:90%" /></td>
				<td><input type="text"	name="SecondEmailBox" value="'.$_POST["SecondEmailBox"].'" style="width:90%" /></td>
				<td><input type="text"	name="PrefPhoneBox" value="'.$_POST["PrefPhoneBox"].'" style="width:90%" /></td>
				<td><input type="text"	name="DateEnteredBox" value="'.$_POST["DateEnteredBox"].'" style="width:90%" /></td>
				<td><input type="text"	name="SourceBox" value="'.$_POST["SourceBox"].'" style="width:90%" /></td>
			  <td></td>
				

				
			</tr>';
	else
		echo '
			<tr style="color: #aef871;">
				
				<td colspan="3"><input type="submit" value="search"  /></td>
				<td><input type="text" name="LastNameBox"  style="width:90%" /></td>
				<td><input type="text" name="FirstNameBox" style="width:90%" /></td>
				<td><input type="text" name="HouseBox" style="width:90%" /></td>
				<td><input type="text" name="StreetBox" style="width:90%" /></td>
				<td><input type="text" name="AptBox" style="width:90%" /></td>
				<td><input type="text" name="CityBox" style="width:90%" /></td>
				<td><input type="text" name="StateBox" style="width:90%" /></td>
				<td><input type="text" name="ZipBox" style="width:90%" /></td>
				
		
				<td><input type="text" name="PrefEmailBox" style="width:90%" /></td>
				<td><input type="text" name="SecondEmailBox" style="width:90%" /></td>
				<td><input type="text" name="PrefPhoneBox" style="width:90%" /></td>
				<td><input type="text" name="DateEnteredBox" style="width:90%" /></td>
				<td><input type="text" name="SourceBox" style="width:90%" /></td>
			  <td></td>
			</tr>';
//	
// end search boxes
////////////////////////////////
	
///////////////////////////////////////
// Populate the table from the database	
	$count=1;
	while ($row=mysql_fetch_array($result) )
		{
			if($count%2==0 && $row['Status']=="ACTIVE")
				echo '<tr style="background-color: #fafde9;">';
			else if($row['Status']=="ACTIVE")
				echo  '<tr style="background-color: #aef871;">';
			else echo '<tr style="background-color:gray;" title="This Donor is on break, on vacation, or otherwise temporarily not to be picked up">';
					
				echo '<td><a href="editMember.php?fdid='.$row["MemberID"].'&uid='.$uid.'"  title="View/Edit this member\'s information" target="_blank"> <img src="icons/edit.png" alt="Edit This Member" width="30px" height="30px" /></a></td>';
				//pickup History
					$numDates=6;
					$dates=getRecentPickupDates($numDates);
					if($dates == '') echo '<td></td>';
						else echo '<td>'.getDonorHistoryTable($row["MemberID"], $dates, $numDates).'</td>';
				//row
					echo '<td>'.$count.'</td> ';
				//LastName
					echo '<td>'.$row['LastName'].'</td>	';		
				//FirstName
					echo '<td>'.$row['FirstName'].'</td>	';		
				//House
					echo '<td>'.$row['House'].'</td>	';		
				//StreetName
					echo '<td>'.$row['StreetName'].'</td>	';	
				//Apt
					echo '<td>'.$row['Apt'].'</td>	';		
				//City
					echo '<td>'.$row['City'].'</td>	';		
				//State
					echo '<td>'.$row['State'].'</td>	';		
				//Zip
					echo '<td>'.$row['Zip'].'</td>	';		
				//PreferredEmail
					echo '<td><a href="mailto:'.$row['PreferredEmail'].'">'.$row["PreferredEmail"].'</a></td>	';		
				//SecondaryEmail
					echo '<td><a href="mailto:'.$row['SecondaryEmail'].'">'.$row["SecondaryEmail"].'</a></td>	';		
				//PreferredPhone
					echo '<td>'.$row['PreferredPhone'].'</td>	';		

				//Date Entered
					echo '<td>'.$row['DateEntered'].'</td>	';	
				//Source
					echo '<td>'.$row['Source'].'</td>	';	
				//Pickup Notes
					echo '<td>'.$row['PUNotes'].'</td>	';	
			echo '</tr>'; 
		$count=($count+1);
		
		}

			echo'</table>	';
			
	
		
	//	Close Form	//	
	echo' </form>';
	

// CLOSING TABLE LAYOUT DIV FOR UI
echo ' </div> ';	

	
//	mysql_close($con);

	
	?>

