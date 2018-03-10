<?php 


//////////////////////////////////////////////////
//	Set up variables for the possible buttons	//	
//////////////////////////////////////////////////
	opendb($dbhost,$dbuser,$dbpw,$db);

	$groups=mysql_fetch_array(mysql_query('SELECT * FROM groups WHERE uID='.$uid.';'));
	$member=mysql_fetch_array(mysql_query('SELECT * FROM members WHERE MemberID='.$uid));

// TRYING CSS MENUS	
echo ' <div style="width: 100%;"> ';

	echo '<ul id="nav">	';
	
// LOGGED IN AS
	echo '
		<li>
			<a href="#" style="background-color: #3c3d3f;">Logged-in as '.$member['FirstName'].' '.$member['LastName'].' </a>
			<ul>
				<li> <a href="securepage/nfp_password_protect.php?logout=1">Logout</a> </li>
			</ul>
		</li>
	';	
	
	
// User
	echo '
		<li>
			<a href="#" style="background-color: #7c0008;">Other</a>
			<ul>
			<li><a href="editMember.php?fdid='.$fdid.'&uid='.$uid.'&self=true" target="ContentFrame">My Profile</a></li>
			</ul>
		</li>
	';


	// MY DISTRICT
		if($groups['DC'])
		{
				echo '	<li>
		
					<a href="#" style="background-color: #a9005b;">My District</a>
					<ul>
					';
						//if $id is in districts as a DCID, make a menu item for each district
						$dcidQuery=mysql_query('SELECT * FROM districts where DCID='.$uid);
						while($row=mysql_fetch_array($dcidQuery))
						{
							echo '<li><a href="district.php?d='.$row['DistrictID'].'&uid='.$uid.'" target="ContentFrame" title="View and edit the district \''.$row['DistrictName'].'" > '.$row['DistrictName'].' </a></li>
							<li> <a href="BigMap.php" target="_blank">Big Map</a></li>
							<li> <a href="allMembers.php?uid='.$uid.'" target="ContentFrame" >All Members</a> </li>
							';
						}
					
				echo '		</ul>
						</li> ';
				
		}

		
	// MY NEIGHBORHOOD	-->
	if($groups['NC'])
	{
		echo '	
			<li>
			<a href="#" style="background-color: #f33e06;">My Neighborhood</a>
			<ul>
			';
		//if $uid is in neighborhoods as an NCID, print a button for each neighborhood
		$ncidQuery=mysql_query('SELECT * FROM neighborhoods where NCID='.$uid);
		while($row=mysql_fetch_array($ncidQuery))
		{
			//BUTTON//
			echo '	<li><a href="neighborhood.php?nh='.$row['NHoodID'].'&uid='.$uid.'" target="ContentFrame" title="View and edit the neighborhood \''.$row['NHName'].'" > '.$row['NHName'].' </a> </li>
			';
		}
		echo '<li> <a href="BigMapNC.php" target="_blank">Big Map</a></li> ';
		echo '	</ul>
			</li> ';
	}


	// ADMIN TOOLS
	//		goes to Welcome Committee members, Data Managers, and ADMINs
		if ($groups['WC'] || $groups['DM'] || $groups['ADMIN'])
		{
			echo '
				<li>
					<a href="#" style="background-color: #2f4b66;">Admin</a>
					<ul>
						<li> <a href="wholeProject.php?uid='.$uid.'" target="ContentFrame">Manage Districts</a> </li>
						<li> <a href="allMembers.php?uid='.$uid.'" target="ContentFrame" >All Members</a> </li>
						<li> <a href="unconfirmed.php?uid='.$uid.'" target="ContentFrame" >Welcome Committee</a> </li>
						<li> <a href="dataManager.php?uid='.$uid.'" target="ContentFrame">Data Manager</a></li>
						<li> <a href="BigMap.php" target="_blank">Big Map</a></li>
						<!--<li><a href="viewUngeocoded.php?u=2wholeproject&uid='.$uid.'" target="ContentFrame">Un-Geocoded Donors</a></li>-->
						<li> <a href="recordTally.php?uid='.$uid.'" target="ContentFrame">Record Tallysheets </a></li>
						<li> <a href="historySwitch.php?uid='.$uid.'" target="ContentFrame">Off/On Pickup History </a></li>					
						<li> <a href="polygon.php" target="ContentFrame" >Create & Edit Map Polygons</a> </li>
						<li> <a href="https://nfp.zendesk.com" target="_blank" >Help Desk/Technical Support</a></li>
						</ul>
				</li>
			';
		}

	// ADD A MEMBER 	-->
			echo '	
				<li>
					<a style="background-color: #4ba74d;" href="newDonorForm.php?uid='.$uid.'" target="ContentFrame">Add A Member</a>
				</li>
			';

			
	echo '</ul>		';
	//ending css menus
	
	//ending header div
	echo '</div>';

$homepage='editMember.php?fdid='.$uid.'&uid='.$uid.'&self=true';

?>






		