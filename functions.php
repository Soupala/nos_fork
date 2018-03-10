<?php

include('config.php');
/////////////
	$functionsAreLoaded= true;




//	 **********************************************
// * *****	open a connection to the database	***** *
//	 **********************************************

function openPDO()
{
	// variables that are to be set to the specific mySQL database
// 		$dbhost="localhost";
// 		$dbuser="neighborhoodfoodproject";
// 		$dbpw="Winter2012!";
// 		$db="nfp_sandbox";

		$dbhost=getDbHost();	//"localhost";
		$dbuser=getDbUser();	//"neighborhoodfoodproject";
		$dbpw=getDbPw();		//"Winter2012!";
		$db=getDb();			//"nfp_sandbox";
		$dbh= new PDO('mysql:host=localhost;dbname='.$db, $dbuser,$dbpw);
		
		return $dbh;
}

function opendb()
{
	// variables that are to be set to the specific mySQL database
		$dbhost=getDbHost();	//"localhost";
		$dbuser=getDbUser();	//"neighborhoodfoodproject";
		$dbpw=getDbPw();		//"Winter2012!";
		$db=getDb();			//"nfp_sandbox";
		
	//
	// connect to the database 
	//
		GLOBAL $con;
		$con = mysql_connect($dbhost, $dbuser, $dbpw);
		if (!$con)
			{die('Could not connect: '. mysql_error()) ;}
		mysql_select_db($db, $con);

}

//**********************************************
// * *****	List All DCs (for admin)	***** *
// **********************************************


/*
function listDCs($id)
{
//include opendb() function to set up db related variables and connect to db
 opendb();

//query the database and display each DC name as a link to their NCs
	$result=mysql_query("SELECT PrintName,MemberID FROM `members` WHERE Role like '%DC%' ");
		
	//$DCname=$name;
		while ($row=mysql_fetch_array($result))
		{	
			$DCid=$row["MemberID"];
			$DCname=$row["PrintName"];
			echo '<a href="DChome.php?DCname='.$DCname.'&DCid='.$DCid.'" target="ContentFrame" >'.$DCname.'</a><br/>';
		}
		echo '</table>';
//mysql_close($con);
}

*/

/**************************************************************************
 * *****	List All NCs for a particular DC (for admin and DCs )	***** *
 **************************************************************************
*/
/*
function listNCs($DCid)
{
		//include opendb() function to set up db related variables and connect to db
			opendb();
			$result=mysql_query("SELECT PrintName,MemberID FROM `alldonors` WHERE isNC='Y' OR isNC='y'");
		//$result=mysql_query("SELECT LastName, FirstName, MemberId  FROM frozendonortable WHERE Role = 'NC' OR Role = 'Org-NC' OR Role = 'Co-NC' OR Role = 'NCDC' OR Role = 'NCDCSC'" );
		
		echo '<table border="1"><tr><th>click on an NC\'s name to view their tally sheet</th><th>click on an NC\'s name to view their contact list</th></tr>';
		$NCname=$name;
		while ($row=mysql_fetch_array($result))
			{	
				$NCid=$row["MemberId"];
				$NCname=$row["FirstName"].$row["LastName"];
				echo '<tr><td>';
				echo '<a href="tallysheet.php?name='.$NCname.'&id=\''.$NCid.'\'" target="ContentFrame" >'.$NCname.'</a><br/>';
				echo '</td><td>';
				echo '<a href="contactlist.php?name='.$NCname.'" target="ContentFrame" >'.$NCname.'</a><br/>';
				echo '</td></tr>';
			}
			echo '</table>';
		//mysql_close($con);
}

*/

function updateContact($contactID, $lname, $fname, $house, $street, $Apt,$city,$state,$zip,$puid,$ncid,$dcid,$eMail,$email2, $phone,$phone2,$feb,$apr,$jun,$aug,$oct,$dec, $notes, $punotes)
	{
		opendb();
		



	$sql="UPDATE alldonors SET LastName = ".$lname.", FirstName=".$fname.", House=".$house.", StreetName=".$street.", Apt=".$Apt.",City=".$city.",State=".$state.",Zip=".$zip.",PUID=".$puid.",NCID=".$ncid.",DCID=".$dcid."PreferredEmail=".$eMail.", SecondaryEmail=".$email2.", PreferredPhone=".$phone.",SecondaryPhone=".$phone2.",RegularFeb=".$feb.",RegularApr=".$apr.",RegularJun=".$jun.",RegularAug=".$aug.",RegularOct=".$oct.",RegularDec=".$dec.", Notes=".$notes.", PUNotes=".$punotes." WHERE MemberID=".$contactID;

	$result=mysql_query($sql);
	mysql_close($con);
	if ($result)
		echo 'update successful';
	else echo 'COULD NOT UPDATE DATABASE';
//////////////////////
//	log changes 	//		
// 	$dbh=openPDO();
	
// 		$log=$dbh->prepare('INSERT INTO log (memberID, changeMade, dateTime) VALUES ('.$id.', :logData, NOW())');
// 		$log->bindParam(':logData', $logData);
// 		$logData=$sql;
// 		try
// 		{
// 			$log->execute();
// 		}
// 		catch(PDOException $e)
// 	{
// 		echo '<script type="text/javascript"> alert("updateContact() failed to UPDATE the log. \n\n Error: '.$e->getMessage().'")</script>';
// 		die();
// 	}
		
	}//end function







/* the navigation buttons -->
<!-- requires $homepage from LoggedIn/permissionButtons.php if the "home" button is to work. -->
* */
function navButtons($homepage)
{
	echo '<div id="navButtons" style="position:absolute; right:50px; top:35px; background-color:olive;">
			<a href="securepage/nfp_password_protect.php?logout=1" title="logout"> <img src="../icons/exit.png" alt="Logout" width="30px" height="30px" /></a>
			<a href="'.$homepage.'" target="ContentFrame" title="View your Home page" > <img src="../icons/home.png" alt="View Homepage" width="30px" height="30px" /></a>
		
	</div>
	
	';
}
////
//

//	Returns the next pickup date, ie: "June, 2011"
//	Is used on tallysheets and contacts lists
function getNextPUdate()
{
	date_default_timezone_set('America/Los_Angeles');
	$date=getdate();
	$month=$date['mon'];
	//$month=date('m');
	//$month=9;
	$year=$date['year'];
	//$year =date('Y');
//echo 'month:'.$month.' year:.'.$year.'<br/>';
	if ($month%2==0)
	{	$PUdate=$month." / ".$year;
		//$PUdate=date('MY');
//		echo '<br/> inside if (month%2) - - PUdate:'.$PUdate;
	}
	else	
	{	$month+=1;
		$PUdate=$month." / ".$year;
		//$nextM=mktime(0,0,0,$month,0,$year);
		//$PUdate=date('MY',$month);
	//	echo "<br/>odd<br/>";
//	echo '<br/> inside else - - PUdate:'.$PUdate;
	}

	return $PUdate;
}


//get next month
function getNextMonth($date)//date format 'Y-m-d'
{
$date_tmp = explode("-",$date);
$next_date =mktime(0, 0, 0, $date_tmp[1]+1, $date_tmp[2], $date_tmp[0]);
return date('M',$next_date)."(".date('m',$next_date).")";
}
function getPreviousMonth($date)//date format 'Y-m-d'
{
$date_tmp = explode("-",$date);
$next_date =mktime(0, 0, 0, $date_tmp[1]-1, $date_tmp[2], $date_tmp[0]);
return date('M',$next_date)."(".date('m',$next_date).")";
}







//////////////////////////////////
//		EDIT PERMISSIONS		//
//		returns array of 		//
//		field=>permissionLevel	//
//////////////////////////////////

function getEditPermissions($groups)
{	//echo '<p style="color:red; z-index:8;">Now entering getEditPermissions()</p>';
	//	This is the place to change whether the logged in member can view a datum, edit it, or neither
//	"w" (write) 	means members with this Role CAN change the datum 
//	"r" (read)		means members with this Role CAN read the datum but CANNOT change it
//	"h" (hidden)	means members with this Role CANNOT read and CANNOT change the datum
	if ($groups['ADMIN'] || $groups['VC'] || $groups['DM'] || $groups['WC'])
		$permissionsArr=array("LastName" => "w",	"FirstName" => "w",	"House" => "w",	"StreetName" => "w",	"Apt" => "w",	"City" => "w",	"State" => "w",	"Zip" =>"w" , "NHoodID" => "w",	"DistrictID" => "w", "CONFIRMED" => "w",	"PreferredEmail" => "w",	"SecondaryEmail" => "w","PreferredPhone" => "w", "SecondaryPhone" => "w",	"Role" => "w",	"FD" => "w", "NC" => "w", "DC" =>"w", "WC" => "w", "WM" => "w", "DM" => "w", "VC" => "w", "ADMIN" => "w", "Status" => "w", "Notes" => "w",	"PUNotes" => "w",	"PrintName" => "w", "DateEntered" => "w", "Source" => "w", "ChangePW" => "w" );
	else if ($groups ['DC'])
		$permissionsArr=array("LastName" => "w",	"FirstName" => "w",	"House" => "w",	"StreetName" => "w",	"Apt" => "w",	"City" => "w",	"State" => "w",	"Zip" =>"w" , "NHoodID" => "w",	"DistrictID" => "w", "CONFIRMED" => "w",	"PreferredEmail" => "w",	"SecondaryEmail" => "w","PreferredPhone" => "w", "SecondaryPhone" => "w",	"Role" => "w", "FD" => "w", "NC" => "w", "DC" =>"w", "WC" => "r", "WM" => "r", "DM" => "r", "VC" => "r", "ADMIN" => "r", "Status" => "w", "Notes" => "w",	"PUNotes" => "w",	"PrintName" => "w", "DateEntered" => "w", "Source" => "w", "ChangePW" => "w" );
	else if ($groups['NC'])
		$permissionsArr=array("LastName" => "w",	"FirstName" => "w",	"House" => "w",	"StreetName" => "w",	"Apt" => "w",	"City" => "w",	"State" => "w",	"Zip" =>"w" , "NHoodID" => "w",	"DistrictID" => "w", "CONFIRMED" => "w",	"PreferredEmail" => "w",	"SecondaryEmail" => "w", "PreferredPhone" => "w", "SecondaryPhone" => "w",	"Role" => "w", "FD" => "w", "NC" => "w", "DC" =>"r", "WC" => "r", "WM" => "r", "DM" => "r", "VC" => "r", "ADMIN" => "r",	"Status" => "w", "Notes" => "w",	"PUNotes" => "w",	"PrintName" => "w", "DateEntered" => "w", "Source" => "w", "ChangePW" => "w" );
/*	else if ($groups['WC'])
		$permissionsArr=array("LastName" => "w",	"FirstName" => "w",	"House" => "w",	"StreetName" => "w",	"Apt" => "w",	"City" => "w",	"State" => "w",	"Zip" =>"w" , "NHoodID" => "w",	"DistrictID" => "w", "CONFIRMED" => "w",	"PreferredEmail" => "w",	"SecondaryEmail" => "w","PreferredPhone" => "w", "SecondaryPhone" => "w",	"Role" => "w", "FD" => "w", "NC" => "w", "DC" =>"w", "WC" => "w", "WM" => "w", "DM" => "w", "VC" => "w", "ADMIN" => "w", "Status" => "w", "Notes" => "w",	"PUNotes" => "w",	"PrintName" => "w", "DateEntered" => "w", "Source" => "w", "ChangePW" => "w" );
	else if ($groups['DM'])
		$permissionsArr=array("LastName" => "w",	"FirstName" => "w",	"House" => "w",	"StreetName" => "w",	"Apt" => "w",	"City" => "w",	"State" => "w",	"Zip" =>"w" , "NHoodID" => "w",	"DistrictID" => "w", "CONFIRMED" => "w",	"PreferredEmail" => "w",	"SecondaryEmail" => "w","PreferredPhone" => "w", "SecondaryPhone" => "w",	"Role" => "w", "FD" => "w", "NC" => "w", "DC" =>"w", "WC" => "w", "WM" => "w", "DM" => "w", "VC" => "w", "ADMIN" => "w",	"Status" => "w", "Notes" => "w",	"PUNotes" => "w",	"PrintName" => "w", "DateEntered" => "w", "Source" => "w", "ChangePW" => "w" );
*/
	return $permissionsArr;
}



//////////////////////////////////
//			DC COMBOBOX 		//
//////////////////////////////////

function DCcombobox()
{
	//select DCs by Role
		$query="SELECT FirstName,LastName,MemberID FROM members,groups WHERE members.MemberID=groups.uID AND DC=1 ORDER BY LastName";
		//Role LIKE '%DC%' ORDER BY PrintName";
			$result = mysql_query($query);
			 $DCsBox='<select name="DCsBox">';
			 $DCsBox.='<option value=""> DC Name ( DistrictName )</option>';
			while ($row = mysql_fetch_array($result)) 
			{
				$DCsBox.= '<option value="'.$row['MemberID'].'" >'.$row['LastName'].', '.$row['FirstName'];
			
				$sql=mysql_query("SELECT DistrictName,DCID FROM districts WHERE DCID=".$row['MemberID'] );
					while ($dists=mysql_fetch_array($sql))
					{
						$DCsBox.= ' ('.$dists['DistrictName'].') ';
					}
				$DCsBox.= '</option>';
			}
			$DCsBox.= '</select>';
		 return $DCsBox;

}	//end DCcombobox()

//////////////////////////////////////////////////////
//			 district COMBOBOX 		//
//////////////////////////////////////////////////////
function districtCombobox()
{
	$dQuery=mysql_query("SELECT * FROM districts ORDER BY DistrictName");
	$Dbox='<select name="Dbox"> ';
	while ($row=mysql_fetch_array($dQuery))
	{
		$Dbox.='<option value="'.$row['DistrictID'].'" >'.$row['DistrictName'].'</option>';
	}
	$Dbox.='</select>';
	
	return $Dbox;
}


function getDistNameFromDid($dID)
{
	$row=mysql_fetch_array(mysql_query("SELECT DistrictName FROM districts WHERE DistrictID=".$dID));
	return $row['DistrictName'];
}

function getNhoodNameFromNid($nhID)
{
	$row=mysql_fetch_array(mysql_query("SELECT NHName FROM neighborhoods WHERE NHoodID=".$nhID));
	return $row['NHName'];
}
//////////////////////////////////////////////////////
//			Nhood names in district COMBOBOX 		//
//////////////////////////////////////////////////////
function NhoodCombobox($districtID)
{
	$nhQuery=mysql_query("SELECT * FROM neighborhoods WHERE DistrictID=".$districtID);
	$NHbox='<select name="NHbox"> ';
	while ($row=mysql_fetch_array($nhQuery))
	{
		$NHbox.='<option value="'.$row['NHoodID'].'" >'.$row['NHName'].'</option>';
	}
	$NHbox.='</select>';
	
	return $NHbox;
}
//////////////////////////////////
//		ALL	Nhood COMBOBOX 		//
//	$comboboxName:	(required) the name value of the combobox
//	$onchange: 	javascript for auto action on selection of a value
//	$nhoodid: 	neighborhood ID to have automatically selected on load
//////////////////////////////////
function allNhoodsByNCNameCombobox($comboboxName, $onchange="", $nhoodid='')
{	
	$nhSelected=false;
	$nhQuery=mysql_query("SELECT NHoodID, NHName FROM neighborhoods ORDER BY NHName");
	//if($onchange="noOnchange")
	//	$NHbox='<select name="NHbox"> ';
	//else
	
		$NHbox='<select name="'.$comboboxName.'" onchange="'.$onchange.'" >';
		
	while ($row=mysql_fetch_array($nhQuery))
	{
		if($row['NHoodID']==$nhoodid)
		{	$NHbox.='<option value="'.$row['NHoodID'].'" selected="true">'.$row['NHName'].'</option>';
			$nhSelected=true;
		}
		else
			$NHbox.='<option value="'.$row['NHoodID'].'" >'.$row['NHName'].'</option>';
	}
	if (!$nhSelected) $NHbox.='<option value="" selected="true">*** No Neighborhood Assigned *** </option>';
	$NHbox.='</select>';
	
	return $NHbox;
}


//////////////////////////////////
//		ALL	Nhood COMBOBOX 		//
//	$onchange: 	javascript for auto action on selection of a value
//	$nhoodid: 	neighborhood ID to have automatically selected on load
//////////////////////////////////
function allNhoodCombobox($comboboxName, $onchange="", $nhoodid='')
{	
	$nhQuery=mysql_query("SELECT NHoodID, NHName FROM neighborhoods ORDER BY NHName");
	//if($onchange="noOnchange")
	//	$NHbox='<select name="NHbox"> ';
	//else
		$NHbox='<select name="'.$comboboxName.'" onchange="'.$onchange.'" >';
		$NHbox.='<option value="">*** Select Neighborhood ***</option>';
	while ($row=mysql_fetch_array($nhQuery))
	{
		if($row['NHoodID']==$nhoodid)
			$NHbox.='<option value="'.$row['NHoodID'].'" selected="true">'.$row['NHName'].'</option>';
		else
			$NHbox.='<option value="'.$row['NHoodID'].'" >'.$row['NHName'].'</option>';
	}
	$NHbox.='</select>';
	
	return $NHbox;
}
 
//////////////////////////////////
//			NC COMBOBOX 		//
//	pulls NCs' memberIDs from groups table		//
//////////////////////////////////
function NCcombobox($onchange="", $selected="")
{
	$ncQuery=mysql_query("SELECT FirstName, LastName, MemberID FROM members,groups WHERE groups.NC=1 AND members.MemberID=groups.uID ORDER BY LastName");
	$NCbox='<select name="NCbox" onchange="'.$onchange.'" id="NCbox" > ';
	$NCbox.='<option ></option>';
	while ($row=mysql_fetch_array($ncQuery))
	{	
		if($row['MemberID'] == $selected)
			$NCbox.='<option value="'.$row['MemberID'].'" selected="selected" >'.$row['LastName'].', '.$row['FirstName'].'</option>';
		else
			$NCbox.='<option value="'.$row['MemberID'].'" >'.$row['LastName'].', '.$row['FirstName'].'</option>';
	}
	$NCbox.='</select>';
	
	return $NCbox;

}//end NCcombobox()


//////////////////////////////////////
//			DISTRICTS COMBOBOX 		//
//	used on all-members table
//
//	creates a combobox named "DisrictsBox"
//	echo it elsewhere
//////////////////////////////////////

function DistrictsCombobox($comboboxName, $onchange)
{
	$query= "SELECT * FROM 	districts ORDER BY DistrictName";
	$result = mysql_query($query);
	$districtsBox='<select name="'.$comboboxName.'" onchange="'.$onchange.'">';
	$districtsBox.='<option value="" >*** Select District ***</option>';
	while ($row = mysql_fetch_array($result)) 
	{
		$DistrictName=$row['DistrictName'];
		$DistrictID=$row['DistrictID'];
		$dcid=$row['DCID'];
		$thisDC=mysql_fetch_array(mysql_query("SELECT FirstName,LastName FROM members WHERE  MemberID=".$dcid));
		$DCName=$thisDC['FirstName'].' '.$thisDC['LastName'];
		$districtsBox.='<option value='.$DistrictID.'> '.$DistrictName.' (DC: '.$DCName.') </option>';
	}
	$districtsBox.='</select>';
	
	return $districtsBox;
}



//////////////////////////////////////////
//			NEIGHBORHOODS COMBOBOX 		//
//										//
//		box name= input parameter		//
//		value = nhoodID					//
//		displayed = NC name				//
//////////////////////////////////////////

function NeighborhoodsCombobox($comboboxName, $formName='', $selected='')
{	opendb();
	$query= "SELECT * FROM 	neighborhoods ORDER BY NHName";
	$result = mysql_query($query);
	$NHBox='<select name="'.$comboboxName.'" onchange="'.$formName.'.submit();">';
	$NHBox.='<option value="" >Neighborhood Name (NC Name)</option>';
	while ($row = mysql_fetch_array($result)) 
	{
		$NHName=$row['NHName'];
		$DistrictID=$row['DistrictID'];
		$NHoodID=$row['NHoodID'];
		$ncid=$row['NCID'];
		$thisNC=mysql_fetch_array(mysql_query("SELECT FirstName,LastName FROM members WHERE  MemberID=".$ncid));
		$NCName=$thisNC['FirstName'].' '.$thisNC['LastName'];
		$NHBox.='<option value='.$NHoodID.'> '.$NCName.' (NH: '.$NHName.') </option>';
	}
	$NHBox.='</select>';
	
	return $NHBox;
}

//////////////////////////////////////////
//			GET GROUPS 	AND ROLES 		//
//////////////////////////////////////////
function getUserGroups($uid)
{
	opendb();
	$groupsQuery=mysql_query("SELECT * FROM groups WHERE uID='".$uid."'");
		 //$groups=mysql_fetch_array($groupsQuery);
		 return mysql_fetch_array($groupsQuery);
	
	
}

function getUserRoles($uid)
{
	$roleArray=getUserGroups($uid);
	$roles='';
		if($roleArray['NC'])
			$roles.='Neighborhood Coordinator<br/>';
		if($roleArray['DC'])
			$roles.='District Coordinator<br/>';
		if($roleArray['WC'])
			$roles.='Welcome Comittee<br/>';
		if($roleArray['DM'])
			$roles.='Data Manager<br/>';
		if($roleArray['ADMIN'])
			$roles.='Administrator<br/>';
		if($roleArray['VC'])
			$roles.='Volunteer Coordinator<br/>';
		return $roles;
}
//////////////////////////////////////
//			MAPPING FUNCTIONS 		//
//////////////////////////////////////
function nhoodPolys()
{
	echo '';
}

	



	
function logDBChange($sql)
{
		//LOG THE CHANGE
		$dbh=openPDO();
		$log=$dbh->prepare('INSERT INTO log (changeMade, dateTime) VALUES ( :logData, NOW())');
		$log->bindParam(':logData', $logData);
		$logData=$sql;
		try
		{			$log->execute();
		}
		catch(PDOException $e)
		{
			die();
		}
}
	
	
	
function saveRegionName($dbTable, $newName, $regionID)
{
		if($dbTable=="neighborhoods")
		{	$tableNameColumn='NHName';
			$tableIDColumn='NHoodID';
		}
		else if($dbTable=="districts")
		{	$tableNameColumn="DistrictName";
			$tableIDColumn="DistrictID";
		}
		$sql="UPDATE ".$dbTable." SET ".$tableNameColumn."=:theName WHERE ".$tableIDColumn."=".$regionID;
//echo '<p style="color:blue">'.$sql.'</p>';
		$dbh=openPDO();
 		$query=$dbh->prepare($sql);
 		$query->bindParam(':theName', $theName);
 		$theName=$newName;
 		try
 		{	$query->execute();	}
 		catch(PDOException $e)
		{	echo '<p style="color:red">updateContact() failed to UPDATE the log. <br/> Error: '.$e->getMessage().'</p>';
 		die();
		}
}//end saveRegionName()
	
	
///////////////////////////////////////////////
//			PICKUP HISTORY 	
//	To get the little red/green/yellow/white gague, 
//	first store getRecentPickupDates() in a variable, 
//	then call getDonorHistoryTable() with that variable.
//	It places a <table> where you make these calls.
///////////////////////////////////////////////
  
  function getRecentPickupDates($numDates)
  {
	$arr=array();
	$dateSql="SELECT DISTINCT pickupDate FROM pickupHistory ORDER BY pickupDate DESC LIMIT ".$numDates;
	$allDates=mysql_query($dateSql);
	if($allDates)
	{	while($row=mysql_fetch_array($allDates))
		{	$arr[]=$row['pickupDate'];	}
		while (count($arr)<$numDates)
		{	$arr[]=null;	}
	return $arr;
	}
	else return '';
  }

function getDonorHistoryTable($uID, $datesArray,$numDates)
{
	$history= '<table border="1"><tr>';
	for ($index=$numDates-1;$index>=0;$index--)
	{	
		$dateSql="SELECT pickedUp, pickupDate FROM pickupHistory WHERE memberID=".$uID ." AND pickupDate='".$datesArray[$index]."'";
		$result=mysql_query($dateSql);
		if($result)
		{	$row=mysql_fetch_array($result);
			if($row)
			{
				if ($row['pickedUp']==1)	{	$color="#52b522"; $pu=" Y"; }
				else if($row['pickedUp']==0) {	$color="#f54646"; $pu=" N"; }
				else if($row['pickedUp']==2) {	$color="#bbb1a7"; $pu=" V"; }
				else if($row['pickedUp']==3) {	$color="white"; $pu="-"; }
			}
			else {$color="#f3fc60"; $pu="&nbsp;";}
			
			$history.= '<td title="'.$datesArray[$index].'" style="background-color:'.$color.'; width:20px; height:10px; font-size:7pt; text-align: center;">'.$pu.'</td>';
		}
	}
	$history.= '</tr></table>';
	return $history;
}

//////////////////////////////////////////
//     ON/OFF Switch for DONOR HISTORY  //
//////////////////////////////////////////

function neighborhoodswitch($comboboxName, $formName='', $selected='')
{
	$query= "SELECT * FROM 	neighborhoods ORDER BY NHName";
	$result = mysql_query($query);
	$NHBox='<select name="'.$comboboxName.'" onchange="'.$formName.'.submit();">';
	$NHBox.='<option value="" >Neighborhood</option>';
	while ($row = mysql_fetch_array($result)) 
	{
		$NHName=$row['NHName'];
		$NHoodID=$row['NHoodID'];
		$NHBox.='<option value='.$NHoodID.'> '.$NHName.' </option>';
	}
	$NHBox.='</select>';
	
	return $NHBox;
}	

	
	//TOGGLE DONOR-HAS-BAG
	function setHasBag($fdid)
	{
		opendb();
		//date_default_timezone_set('America/Los_Angeles');
		//$date=date('M d Y');
	//	$wcnotesArray=mysql_fetch_array(mysql_query("SELECT WCNotes FROM members WHERE MemberID=".$fdid));
	//	$wcnotes= $wcnotesArray['WCNotes'];
		$wcnotes='Received Bag '.getTodaysDate();
		
		// echo $wcnotes;
		
		$sql="UPDATE members SET accepted=1, hasBag=1, WCNotes='".$wcnotes."' WHERE MemberID=".$fdid;
		$query=mysql_query($sql);
		if(!$query) echo mysql_error();
	}
	
	
	
	
	
	function statusMenu($status)
	{
		$theBox= '<select name="statusMenu" >';
		if($status=="ACTIVE")
			$theBox.='<option title="An active member of the Food Project" selected>Active</option>';
		else $theBox.='<option title="An active member of the Food Project">Active</option>';
		if($status=="INACTIVE")
			$theBox.='<option title="On break, away for vacation, or otherwise temporarily off the pickup route (but still a part of a neighborhood)" selected>Inactive</option>';
		else $theBox.='<option title="On break, away for vacation, or otherwise temporarily off the pickup route (but still a part of a neighborhood)">Inactive</option>';
		if($status=="ARCHIVED")
			$theBox.='<option title="Permanentely deactivated, stored only for archival reasons" selected>Archived</option>';
		else
			$theBox.='<option title="Permanentely deactivated, stored only for archival reasons">Archived</option>';
		$theBox.=	'</select>';
		
		return $theBox;
	}
	//lists all member email addresses in the neighborhood, separated by commas for cut/paste into email client for bulk emails
	function nhBulkEmail($nhid)
	{
		$sql="Select PreferredEmail,SecondaryEmail FROM members WHERE NHoodID=".$nhid." AND (Status='ACTIVE')";
		$result=mysql_query($sql);
		$emailList='';
		while($row=mysql_fetch_array($result))
		{	if($row['PreferredEmail']!='')
			$emailList.=$row['PreferredEmail'].', ';
			if($row['SecondaryEmail']!='')
			$emailList.=$row['SecondaryEmail'].', ';
		}
		return $emailList;
	}
	//lists all member email addresses in the neighborhood, separated by semicolons for cut/paste into email client for bulk emails
	function nhBulkEmailSemicolon($nhid)
	{
		$sql="Select PreferredEmail,SecondaryEmail FROM members WHERE NHoodID=".$nhid." AND (Status='ACTIVE')";
		$result=mysql_query($sql);
		$emailList='';
		while($row=mysql_fetch_array($result))
		{	if($row['PreferredEmail']!='')
			$emailList.=$row['PreferredEmail'].'; ';
			if($row['SecondaryEmail']!='')
			$emailList.=$row['SecondaryEmail'].'; ';
		}
		return $emailList;
	}
	//lists all NC email addresses in the district, separated by commas for cut/paste into email client for bulk emails to one's NCs
	function districtBulkEmail($did)
	{
		$sql="Select PreferredEmail,SecondaryEmail FROM members,neighborhoods WHERE DistrictID=".$did." AND members.MemberID=neighborhoods.NCID AND (Status='ACTIVE')";
		$result=mysql_query($sql);
		$emailList='';
		while($row=mysql_fetch_array($result))
		{	if($row['PreferredEmail']!='')
			$emailList.=$row['PreferredEmail'].', ';
			if($row['SecondaryEmail']!='')
			$emailList.=$row['SecondaryEmail'].', ';
		}
		return $emailList;
	}
	
	//lists all NC email addresses in the district, separated by semicolons for cut/paste into email client for bulk emails to one's NCs
	function districtBulkEmailSemicolon($did)
	{
		$sql="Select PreferredEmail,SecondaryEmail FROM members,neighborhoods WHERE DistrictID=".$did." AND members.MemberID=neighborhoods.NCID AND (Status='ACTIVE')";
		$result=mysql_query($sql);
		$emailList='';
		while($row=mysql_fetch_array($result))
		{	if($row['PreferredEmail']!='')
			$emailList.=$row['PreferredEmail'].'; ';
			if($row['SecondaryEmail']!='')
			$emailList.=$row['SecondaryEmail'].'; ';
		}
		return $emailList;
	}

	
		//lists all DC email addresses in the Food Project, separated by commas for cut/paste into email client for bulk emails to all DCs
	function wpBulkEmail()
	{
		$sql="Select PreferredEmail,SecondaryEmail FROM members,districts WHERE members.MemberID=districts.DCID AND (Status='ACTIVE') ";
		$result=mysql_query($sql);
		$emailList='';
		while($row=mysql_fetch_array($result))
		{	if($row['PreferredEmail']!='')
			$emailList.=$row['PreferredEmail'].', ';
			if($row['SecondaryEmail']!='')
			$emailList.=$row['SecondaryEmail'].', ';
		}
		return $emailList;
	}
	
			//lists all DC email addresses in the Food Project, separated by semicolons for cut/paste into email client for bulk emails to all DCs
	function wpBulkEmailSemicolon()
	{
		$sql="Select PreferredEmail,SecondaryEmail FROM members,districts WHERE members.MemberID=districts.DCID AND (Status='ACTIVE') ";
		$result=mysql_query($sql);
		$emailList='';
		while($row=mysql_fetch_array($result))
		{	if($row['PreferredEmail']!='')
			$emailList.=$row['PreferredEmail'].'; ';
			if($row['SecondaryEmail']!='')
			$emailList.=$row['SecondaryEmail'].'; ';
		}
		return $emailList;
	}
	
	
	function saveMergedNhoods($keeperBox, $discardBox)
	{
		//move all members of discardBox to keeperBox
		$sql="UPDATE members SET NHoodID=".$keeperBox." WHERE NHoodID=".$discardBox;
		$move=mysql_query($sql);
		//if the move worked, delete discardBox
		if($move)
			$del=mysql_query("DELETE FROM neighborhoods WHERE NHoodID=".$discardBox);
	}
	
	
	
	function getNHNameFromNhoodID($NhoodID)
	{
		$sql=mysql_query("SELECT NHName FROM neighborhoods WHERE NHoodID=".$NhoodID);
		if($sql)
		{	$nhname=mysql_fetch_array($sql);
			return $nhname['NHName'];
		}
		else return '';
	}
	
	function getNCNameFromNhoodID($NhoodID)
	{
		$sql=mysql_query("SELECT MemberID,FirstName,LastName FROM members,neighborhoods WHERE neighborhoods.NHoodID=".$NhoodID." AND members.MemberID=neighborhoods.NCID");
		
		if($sql)
		{	$theName=mysql_fetch_array($sql);
		return $theName['FirstName'].' '.$theName['LastName'];
		}
		else return '';
		
	}
	
	function getDCNameFromNhoodID($NhoodID)
	{
		$sql=mysql_query("SELECT FirstName,LastName FROM members, neighborhoods, districts where districts.DistrictID=neighborhoods.DistrictID AND neighborhoods.NHoodID=".$NhoodID." and members.MemberID=districts.DCID");
		
		if($sql)
		{	$theName=mysql_fetch_array($sql);
		return $theName['FirstName'].' '.$theName['LastName'];
		}
		else return '';
	}
	
	function getDistrictNameFromNhoodID($NhoodID)
	{
		$sql=mysql_query("SELECT DistrictName FROM  neighborhoods, districts where districts.DistrictID=neighborhoods.DistrictID AND neighborhoods.NHoodID=".$NhoodID);
		
		if($sql)
		{	$theName=mysql_fetch_array($sql);
		return $theName['DistrictName'];
		}
		else return '';
	}
	
	function getPrivateNotesFromNhoodID($NhoodID)
	{
		$sql=mysql_query("SELECT privateNotes FROM  neighborhoods WHERE neighborhoods.NHoodID=".$NhoodID);
		
		if($sql)
		{	$thePrivateNotes=mysql_fetch_array($sql);
		return $thePrivateNotes['privateNotes'];
		}
		else return '';
	}
	
	
	
	function getTodaysDate()
	{
		date_default_timezone_set('America/Los_Angeles');
		return date('M d Y');
	}
	
	
	
	function getNumNhDonors($nhid)
	{
		$sql=mysql_query("select COUNT('MemberID') AS num FROM members WHERE NHoodID=".$nhid." AND (Status='ACTIVE' OR Status='INACTIVE') ");
		if($sql)
		{	$row=mysql_fetch_array($sql);
			return $row['num'];
		}
		else return '';
	}
	
	function getMaxNhDonors($nhid)
	{
		$sql= mysql_query("SELECT maxDonors as num FROM neighborhoods WHERE NHoodID=".$nhid);
		if($sql)
		{	$row=mysql_fetch_array($sql);
			return $row['num'];
		}
		else return '';
	}
	function saveMaxDonors($post, $nhid)
	{
		$sql=mysql_query('UPDATE neighborhoods SET maxDonors='.$post['maxdonors'].' WHERE NHoodID='.$nhid);
	}


	function ncContactList($dID)
{
	$sql="SELECT FirstName, LastName, PreferredEmail, PreferredPhone,NHName FROM members,neighborhoods WHERE neighborhoods.DistrictID=".$dID." AND  members.MemberID=neighborhoods.NCID";
	$result=mysql_query($sql);
	
	//echo '<table border=1>	';
	//	echo '<tr><th> Name </th><th> email</th> <th>Phone</th></tr>	';
		while($row=mysql_fetch_array($result))
		{
					echo '<b>Neighborhood:</b> '.$row['NHName'].'<br />
						<b>Name:</b> '.$row['FirstName'].' '.$row['LastName'] .' <br />
						<b>Email:</b> <a href="mailto:'.$row['PreferredEmail'].'">'.$row['PreferredEmail'] .'</a> <br />
						<b>Phone:</b> '.$row['PreferredPhone'] .' <br />
						<hr />';
					
					
		}

	//echo '</table>	';
}


function dcContactList()
{
	$sql="SELECT FirstName, LastName, PreferredEmail, PreferredPhone,DistrictName FROM members,districts WHERE   members.MemberID=districts.DCID";
	$result=mysql_query($sql);
	
	//echo '<table border=1>	';
	//	echo '<tr><th> Name </th><th> email</th> <th>Phone</th></tr>	';
		while($row=mysql_fetch_array($result))
		{
					echo '<b>District:</b> '.$row['DistrictName'].'<br />
						<b>DC Name:</b> '.$row['FirstName'].' '.$row['LastName'] .' <br />
						<b>Email:</b> <a href="mailto:'.$row['PreferredEmail'].'">'.$row['PreferredEmail'] .'</a> <br />
						<b>Phone:</b> '.$row['PreferredPhone'] .' <br />
						<hr>';
					
					
		}

	//echo '</table>	';
}
	
	
	
////////////////////////////////////////////////////////////////
//	TRANSFERRED FROM MAPFUNCTIONS.PHP
////////////////////////////////////////////////////////////////

function newNhood($nhname, $ncid, $dID)
{
	//echo '<p style="color:red;">About to create a new Neighborhood called </p>';
	//$nhname=$_POST['newNhoodName'];
	//$ncID=$_POST['NCbox'];
	$sql="INSERT INTO neighborhoods (NHName, NCID, DistrictID) VALUES ('".$nhname."', ".$ncid.", ".$dID.")";
	$query=mysql_query($sql);
		if(!$query)
		//echo '<p style="color:lime">Created new Neighborhood</p>';
	 echo 'Failed to create new Neighborhood
		The error was:
		'. mysql_error().'
		the SQL was:\n'.$sql.'")';

	//logDBChange($sql);		
		
		
}

 


	
function populateNewDonorsDiv($nhID, $ncid)
{
//used in neighborhood.php to populate the confirm/deny tool
	$sql="SELECT * FROM members WHERE NHoodID=".$nhID." AND (accepted=0 OR accepted=3 OR hasBag=0)";
	$newDonors=mysql_query($sql);
		// if($newDonors)
			// echo '<script type="text/javascript"> alert("populateNewDonorsDiv() sql \n SUCCESSFUL");</script>	';
		// else
			// echo '<script type="text/javascript"> alert("populateNewDonorsDiv() sql \n UNSUCCESSFUL\n\n'.mysql_error().'\n\n The SQL query was:\n'.$sql.'");</script>	';
			
//borrowed from unconfirmed.php->getUnconfirmedDonors()
	while($row=mysql_fetch_array($newDonors) )			//limit the number of records displayed 
	{
		
		$fdID=$row['MemberID'];
		$address=$row['House'].' '.$row['StreetName'].' ' .$row['City'].' '.$row['State'];
		$latLongBox="latLong".$row['MemberID'];
		// if($row['CONFIRMED']==TRUE)
			// $confirmed='checked="checked"';
		// if($row['CONFIRMED']==FALSE)
			// $confirmed="";
			
		
			
			echo '<p style="text-align:left;">
				<b>'.  $row['FirstName'].' '.$row['LastName'].'</b><br />
				'.	$row['House'].' '.$row['StreetName'].'<br />
				'.	$row['City'].', '.$row['State'].' '.$row['Zip'].'
				</p>';
				
			echo ' 		
				<br />
				<b>Email:</b>	<a href="mailto: '.$row['PreferredEmail'].'">'.$row['PreferredEmail'].'</a>
				<br />
				<b>Phone:</b>	'.$row['PreferredPhone'].'
				<br /><br />
				';

// DATE ENTERED AND GEOLOCATION
	echo '			Date Entered:
				<input type="text" name="ud_date_entered" id="ud_date_entered" value="'.$row['DateEntered'].'" readonly="readonly" /> 
				<br />
				';
	echo	'
			
				<form id="ncAcceptDonorForm" action="neighborhood.php?uid='.$_GET['uid'].'&nh='. $nhID .'&ncid='. $ncid .'&accept=true&tool=newDonorsDiv" method="post" >
				<input type="hidden" size="30" name="ud_latLong" id="'.$latLongBox.'" value="'.trim($row['latLong']).'"/>
				<br />
				<input type="button" name="ShowOnMap" value="Show Donor Location On Map" class="queuebuttons" style="padding: 10px; margin-top: 5px;" onclick="addMarkerToMap(\''.$address.'\', \''.$latLongBox.'\');" />
				<br />
				
				';
				
	echo	'				
				<a style="text-align:center;" href="javascript:toggleTools(\'memberNotesDiv'.$fdID.'\');">
				<br /> Member Notes +/-</a><br/>
				<div id="memberNotesDiv'.$fdID.'">
					<textarea rows="3" cols="50" name="ud_notes" readonly>'.$row['Notes'].'</textarea>
	
				
				</div>
				<a style="text-align:center;" href="javascript:toggleTools(\'pickupNotesDiv'.$fdID.'\');"> 
				Pickup Notes +/-</a><br/>
				<div id="pickupNotesDiv'.$fdID.'">
					<textarea rows="2" cols="50" name="ud_punotes" style="width:100%;" readonly>'.$row['PUNotes'].'</textarea>
	
				</div>
				<input type="hidden" name="ud_memberID" value="'.$row["MemberID"].'	" />';

				
//THE ACCEPTED BUTTON

				echo '		
				<br />
				<input type="hidden" name="fdID" value="'.$fdID.'" />	';
				echo '	<input type="submit" value="Accept" class="queuebuttons" style="padding: 10px; margin: 4px;" />	';
				echo '	</form>		';


//THE HAS-BAG BUTTON	
		if ($row['hasBag']==0) $bagColor="green";
		else $bagColor="black";
		if ($row['accepted']==3) $hideHasBag="hidden";
		else $hideHasBag="button";
	echo '    <form id="DonorHasBagForm'.$row['MemberID'].'" action="neighborhood.php?uid='.$_GET['uid'].'&ncid='. $ncid .'&nh='. $nhID .'&hasbag=true&tool=newDonorsDiv" method="post" > ';
	echo '		<input type="hidden" name="fdID" value="'.$fdID.'" />	';
	echo '		<input type="'.$hideHasBag.'" name="hasbag" value="Contacted & Has A Bag" class="queuebuttons" style="padding: 10px; margin: 4px; color:'.$bagColor.'" onclick="document.getElementById(\'DonorHasBagForm'.$row['MemberID'].'\').submit(); " />	';
	echo '		<input type="hidden" value="Accepted" style="padding: 5px; margin: 4px;" />	';
	echo '	</form>	';

			
//THE DECLINE BUTTON	

	echo '	<form id="ncDeclineDonorForm'.$row['MemberID'].'" action="neighborhood.php?uid='.$_GET['uid'].'&ncid='.$ncid.'&nh='. $nhID .'&accept=false&tool=newDonorsDiv" method="post" >';
	echo '		<input type="hidden" name="fdID" value="'.$fdID.'" />	';
	echo ' 		<input type="hidden" name="memberNotes" value="'.$row['Notes'].'" />	';
	echo '		<input type="button" name="decline" value="Decline" class="queuebuttons" style="padding: 10px; margin: 4px;" onclick="document.getElementById(\'ncDeclineDonorForm'.$row['MemberID'].'\').submit();" />	';
	echo '	</form>		';
	echo '<hr/><hr/>';
	}//end while
}	
	
function acceptDonor($fdID, $latLong, $ncid)
{
		$sql=mysql_fetch_array(mysql_query("SELECT FirstName,LastName FROM members WHERE MemberID=".$ncid));
		$ncName=$sql['FirstName'].' '.$sql['LastName'];
		
		$newNotes='<p style="width: 360px; background-color: blue; color: white; padding: 15px; font-size: 16px; font-weight: bolder; border: 1px solid blue; border-radius: 10px;">
		'.getTodaysDate().': Accepted by '.$ncName.'</p>'; 
	
	$sql="UPDATE members SET accepted=1,WCNotes='".$newNotes."',latLong='".$latLong."' WHERE MemberID=".$fdID;
	//echo '<script type="text/javascript"> 		alert("acceptDonor():\n		You ACCEPTED a donor\n		ID: '.$fdID.'\n		sql to execute:'.$sql.'	");</script>	';
		
	$result=mysql_query($sql);
	if($result)
	{	//echo '<script type="text/javascript"> 		alert("acceptDonor():\n result returned true	");</script>	';
	}
	else
		echo '<script type="text/javascript"> 
		alert("acceptDonor():\n result returned FALSE\n\n Error:\n'.mysql_error().'	\n\nSQL attempt:\n'.$sql.'");</script>	';
}


function declineDonor($fdID,$ncid,$newNotes,$dbh)
{
	//get the NC's name
	$ncNameSql="Select FirstName,LastName FROM members WHERE MemberID=".$ncid;
	$ncNameResult=mysql_fetch_array(mysql_query($ncNameSql));
	$ncName=$ncNameResult['FirstName'].' '.$ncNameResult['LastName'];
	
	//get the new donor's WCNotes 
	//$notesSql="SELECT WCNotes FROM members WHERE MemberID=".$fdID;
	//$notesResult=mysql_fetch_array(mysql_query($notesSql));
	//$notes=$notesResult['WCNotes'];
	//get the updated notes from $_POST
	
	//if newNotes != notes, update new donor's 
	//	Notes with "Declined by SoAndSo on DateTimeStamp"
	//if($newNotes!=$notes)
	  	$newNotes='<p style="width: 360px; background-color: #f9e725; color: #b4005f; padding: 15px; font-size: 16px; font-weight: bolder; border: 1px solid #f9e725; border-radius: 10px;">'.getTodaysDate().': Declined by '.$ncName.' </p>'; 
	//	set NHoodID=null
		
	//UPDATE new donor's WCNotes using prepared statement 
	$query=$dbh->prepare("UPDATE members SET WCNotes=:theNotes, NHoodID=NULL, accepted=0 WHERE MemberID=".$fdID);
	$query->bindParam(':theNotes', $theNotes);
	
	$theNotes=trim($newNotes);
	
	try
	{		$query->execute();	}
	catch(PDOException $e)
	{
		echo '<script type="text/javascript"> alert("declineDonor() failed to UPDATE the notes field. \r\n Error: '.$e->getMessage().'\n Debug info: mapFunctions.php->declineDonor()")</script>';
		die();
	}
		//echo '<script type="text/javascript"> alert("declineDonor() succeeded in UPDATE-ing the notes field. ")</script>';
}//end function declineDonor()





function allDcContactInfo()
{
	$theList='	<table>
	';
	$sql="SELECT FirstName, LastName, PreferredEmail, PreferredPhone FROM members,groups WHERE (members.Status='ACTIVE' OR members.Status='INACTIVE') and	members.MemberID=groups.uID and groups.DC=1 ORDER BY LastName";
	
	//debug
		//echo 'allNcContactInfo SQL: '.$sql;
		
	$result=mysql_query($sql);
	
	while($row=mysql_fetch_array($result))
	{
		$theList.= '<tr onmouseover="this.style.backgroundColor=\'#aef871\'" onmouseout="this.style.backgroundColor=\'white\'">
			<td style="padding: 0px 5px 0px 5px">'.$row['LastName'].', '.$row['FirstName'].'</td>
			<td style="padding: 0px 5px 0px 5px"><a href="mailto:'.$row['PreferredEmail'].'"</a>'.$row['PreferredEmail'].'</td>
			<td style="padding: 0px 5px 0px 5px">'.$row['PreferredPhone'].'</td>
			</tr>';
			
	}
	$theList.='</table>';
	return $theList;
}


function allNcContactInfo()
{
	$theList='<table>
	';
	$sql="SELECT FirstName, LastName, PreferredEmail, PreferredPhone FROM members,groups WHERE	(members.Status='ACTIVE' OR members.Status='INACTIVE') and members.MemberID=groups.uID and groups.NC=1  ORDER BY LastName";
	
	//debug
		//echo 'allNcContactInfo SQL: '.$sql;
	
	$result=mysql_query($sql);
	
	
	while($row=mysql_fetch_array($result))
	{
		$theList.= '<tr onmouseover="this.style.backgroundColor=\'#aef871\'" onmouseout="this.style.backgroundColor=\'white\'">
			<td style="padding: 0px 5px 0px 5px">'.$row['LastName'].', '.$row['FirstName'].'</td>
			<td style="padding: 0px 5px 0px 5px"><a href="mailto:'.$row['PreferredEmail'].'"</a>'.$row['PreferredEmail'].'</td>
			<td style="padding: 0px 5px 0px 5px">'.$row['PreferredPhone'].'</td>
			</tr>';
	
	}
	$theList.='</table>';
	return $theList;
}

function recordedTallys()
{
	$theList='<table>
	';
	$sql="SELECT FirstName, LastName, PreferredEmail, PreferredPhone FROM members,groups WHERE	(members.Status='ACTIVE' OR members.Status='INACTIVE') and members.MemberID=groups.uID and groups.NC=1  ORDER BY LastName";
	
	//debug
		//echo 'allNcContactInfo SQL: '.$sql;
	
	$result=mysql_query($sql);
	
	
	while($row=mysql_fetch_array($result))
	{
		$theList.= '<tr onmouseover="this.style.backgroundColor=\'#aef871\'" onmouseout="this.style.backgroundColor=\'white\'">
			<td style="padding: 0px 5px 0px 5px">'.$row['LastName'].', '.$row['FirstName'].'</td>
			<td style="padding: 0px 5px 0px 5px"><a href="mailto:'.$row['PreferredEmail'].'"</a>'.$row['PreferredEmail'].'</td>
			<td style="padding: 0px 5px 0px 5px">'.$row['PreferredPhone'].'</td>
			</tr>';
	
	}
	$theList.='</table>';
	return $theList;
}




function deleteMember($MemberID)
{
	$sql="DELETE FROM members WHERE MemberID=".$MemberID;
	$result=mysql_query($sql);
	
	if($result)
		echo 'alert("member deleted")';
	else 
		echo 'alert("Delete function failed<br/>
		'.mysql_error.'")';
}












//	END OF FUNCTIONS
?>








