<?php
	include("functions.php");
	opendb();
	
	//debug:
		//echo 'debug line6 - uid:'.$_GET['uid'].' page:'.$_GET['page'].'<br/>';
	

	//load content
	if(isset($_GET['unconfirmed']))
		getUnconfirmedDonorsPanel($_GET['uid'], 30, $_GET['page']);
	if(isset($_GET['ncs']))
		getNcContactPanel();
	if(isset($_GET['dcs']))
		getDcContactPanel();
	if(isset($_GET['geo']))
		getGeoPanel();
	if(isset($_GET['email']))
		getEmailPanel();
function getUnconfirmedDonorsPanel($userID, $maxrows, $page)
{

//debug
		//echo 'userID:'.$userID.' maxrows:'.$maxrows.' page:'.$page.'<br/>';

//////////////////////////////////////////
//	START PAGINATION SECTION			//
//////////////////////////////////////////
		$limitStart=($page-1)*$maxrows;
			$limit=$limitStart.','.$maxrows;

		$numSql="SELECT MemberID from members,groups WHERE (accepted=0 OR hasBag=0) AND (Status='ACTIVE' OR Status='INACTIVE') AND MemberID=uID";
		$numResult=mysql_query($numSql);
		$num=mysql_num_rows($numResult);

		$maxpage=ceil($num/$maxrows);
		$start=($page-1)*$maxrows;
		$end=(($page-1)*$maxrows)+$maxrows-1;
//debug:
	//echo 'number of results:'.$num.' max rows:'.$maxrows.' max page:'.$maxpage.' actual max pages:'.$num/$maxrows.'<br/>';
	//	
	//	echo ' maxrows:'.$maxrows.'  page:'.$page.'  maxpage:'.$maxpage.' start:'.($maxrows*$page).' end:'.(($maxrows*$page)+$maxrows).'<br/>';
		
	echo '<div id="paginationMenu" style="text-align:center; position:relative; top:0px; height:50px; font-size: 18px; font-weight: bolder; color: red;" >
	';
		
		echo '***Showing '.$start.'-'.$end.' of '.$num.' new sign-ups***<br />';
		
		echo ' <a style="text-decoration: none; color: #304b66;" href="#" title="To First Page" onclick="ShowHideDivs(\'leftdiv\'); handleRequest(\'left\', \'unconfirmed\', '.$userID.',1);">&lt&lt </a>	&nbsp';
		if($page>1)
			echo ' <a style="text-decoration: none; color: #304b66;" href="#" title="Back One Page" onclick="ShowHideDivs(\'leftdiv\'); handleRequest(\'left\', \'unconfirmed\', '.$userID.','.($page-1).');">&lt </a>	&nbsp';
			if($page==$maxpage && $maxpage>4)
				echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="ShowHideDivs(\'leftdiv\'); handleRequest(\'left\', \'unconfirmed\', '.$userID.','.($page-4).');">'.($page-4).'</a>		&nbsp';
			if(($page==$maxpage-1 ||$page==$maxpage) && $maxpage>3)
				echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="ShowHideDivs(\'leftdiv\'); handleRequest(\'left\', \'unconfirmed\', '.$userID.','.($page-3).');">'.($page-3).'</a>		&nbsp';
		if($page>2)
			echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="ShowHideDivs(\'leftdiv\'); handleRequest(\'left\', \'unconfirmed\', '.$userID.','.($page-2).');">'.($page-2).'</a>		&nbsp';
		if($page>1)
			echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="ShowHideDivs(\'leftdiv\'); handleRequest(\'left\', \'unconfirmed\', '.$userID.','.($page-1).');">'.($page-1).'</a>		&nbsp';
	echo '<b>&nbsp '.$page.' &nbsp</b>';
		if($page<$maxpage)
			echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="ShowHideDivs(\'leftdiv\'); handleRequest(\'left\', \'unconfirmed\', '.$userID.','.($page+1).');">'.($page+1).'</a>		&nbsp';
		if($page<$maxpage-1)
			echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="ShowHideDivs(\'leftdiv\'); handleRequest(\'left\', \'unconfirmed\', '.$userID.','.($page+2).');">'.($page+2).'</a>		&nbsp';
			if($page<3 && $maxpage>4)
				echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="ShowHideDivs(\'leftdiv\'); handleRequest(\'left\', \'unconfirmed\', '.$userID.','.($page+3).');">'.($page+3).'</a>		&nbsp';
			if($page<2 && $maxpage>5)
				echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="ShowHideDivs(\'leftdiv\'); handleRequest(\'left\', \'unconfirmed\', '.$userID.','.($page+4).');">'.($page+4).'</a>		&nbsp';
		if($page<$maxpage)
			echo ' <a style="text-decoration: none; color: #304b66;" href="#" title="Forward One Page" onclick="ShowHideDivs(\'leftdiv\'); handleRequest(\'left\', \'unconfirmed\', '.$userID.','.($page+1).');">&gt </a>	&nbsp';
		echo ' <a style="text-decoration: none; color: #304b66;" href="#" title="To Last Page" onclick="ShowHideDivs(\'leftdiv\'); handleRequest(\'left\', \'unconfirmed\', '.$userID.','.$maxpage.');">&gt&gt </a>	';
	
	echo'	</div>
	';
	
	//////////////////////////////////////
	//	END PAGINATION SECTION			//
	//////////////////////////////////////	
	
	
	
	
	//$sql='SELECT * FROM members'.$where.' ORDER BY MemberID DESC LIMIT 50';
	
	$sql='SELECT * FROM members, groups WHERE (accepted=0 OR hasBag=0) AND (Status="ACTIVE" OR Status="INACTIVE") AND MemberID=uID ORDER BY MemberID DESC LIMIT '.$limit;
	//debug
		//echo '<hr/>'.$sql.'<hr/>';
	$newDonors=mysql_query($sql);
	
	//	echo '<script type="text/javascript" >alert("The SQL to order the New Donors\'s panel is:\n'.$sql.'")</script>';
	


	
	$donorCount=0;
			
	while($row=mysql_fetch_array($newDonors) )			//limit the number of records displayed 
	{
		$donorCount++;
		if ($donorCount%2==0)
			$divColor="#71aae4";
		else if ($row['NC']==1)
			$divColor="#f9c962";
		else $divColor="#c3ff91";
		
		$address=$row['House'].' '.$row['StreetName'].' ' .$row['City'].' '.$row['State'];
		$latLongBox="latLong".$row['MemberID'];
		if($row['accepted']==1)
			$confirmed='checked="checked"';
		if($row['accepted']==0)
			$confirmed="";
		echo '<div id="div'.$row['MemberID'].'" style="position:relative; color: #2d2e2f; font-size: 16px; border: 4px solid #bbb1a7; border-radius: 8px; solid; background-color:'.$divColor.'; padding:15px; ">';
	
		echo '	<form id="unconfirmedForm'.$row['MemberID'].'" action="welcomeCommittee.php?tool=unconfirmed&id='.$userID.'&save=true&page='.$page.'#div'.$row['MemberID'].'" method="post" >';
		//echo ' <form id="unconfirmedForm'.$row['MemberID'].'" action="welcomeCommittee-ajax.php?u=wholeproject&id='.$userID.'&save=unconfirmed" method="post">';
		
		
		
		  	echo ' 
					<div style="border: solid grey 3px; webkit-border-radius: 20px; moz-border-radius: 20px; border-radius: 20px;padding:0 10px 0 10px; float:right; font-size:20px; font-weight:bold; color:#fafafb; background-color:#2f4b66;">'.$row['MemberID'].'</div>
					<a href="editMember.php?eid='.$row["MemberID"].'&uid='.$userID.'" target="_blank" title="View/Edit this member\'s information" style="float:right"> 
			<img src="icons/edit.png" alt="Edit This Member" width="30px" height="30px" />	</a>
					'.$row['FirstName'].' '.$row['LastName'].'
					<br />
					'.$row['House'].' '.$row['StreetName'].' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '.$row['Apt'].'
					<br/>
					'.$row['City'].' '.$row['State'].' '.$row['Zip'].'
					<br />
					<b></b> '.$row['PreferredPhone'].'
					<br />
					<b></b> <a href="mailto:'.$row['PreferredEmail'].'">'.$row['PreferredEmail'].'</a>
					<br />
					<p style="background-color: #dbe5dd; color: #272727; padding: 5px 10px 5px 10px; margin-top: 15px; border: 2px solid #304b66; border-radius: 10px;"><b>Donor Source:</b> '.$row['Source'].'</p><br />
					';
	
  		echo '<div>'.$row['WCNotes'].'</div>
  		<input type="hidden" name="ud_memberID" value="'.$row["MemberID"].'	" />
  		';				
					
//	welcome email sent?
if($row['WCEmail'])
	$emailsent='checked="checked"';
else
	$emailsent='';
	 echo ' <br />';
	echo '<input type="checkbox" name="wcemail" '.$emailsent.' /> Welcome Email Sent';
	
	
//	NC contacted?
if($row['NCEmail'])
	$emailsent='checked="checked"';
else
	$emailsent='';
  echo ' <br />';
	echo '<input type="checkbox" name="ncemail" '.$emailsent.' /> NC Contacted<br /><br/>';
	
//echo ' <input type="button" name="ShowOnMap" value="Geolocate Donor/Find Nearby NC" onclick="codeAddress(\''.$address.'\', \''.$latLongBox.'\'); " /><br /><br /> ';
				
echo '<a style="margin-bottom: 5px;" class="queuebuttons"  onclick="codeAddress(\''.addslashes($address).'\', \''.$latLongBox.'\'); " >Geocode/Locate on Map</a><br/>';

//LATLONG				
	//echo	'		Lat/Long	';
	echo '<input type="hidden" size="40" name="ud_latLong" id="'.$latLongBox.'" value="'.$row['latLong'].'"/><br />	';
	if ($row['latLong']=="(42.938696,-122.146522)")
		echo '<p style="color:red; text-align:left ;">***donor needs to be geocoded***</p>';
		//echo '<img id="geoImg" src="images/needsGeocoding.png" alt="needs Geocoding" style="color:red;"/>';
	else echo '<p style="color:green; text-align:left;">**donor has been geocoded**</p>';//echo '<img id="geoImg" src="images/hasGeocoding.png" alt="has Geocoding" style="color:green;"/>';
	echo '<br />';	

//				Neighborhood:	<input type="text" name="ud_nid" value="'.$row['NHoodID'].'"/>
//				<br />';

				

// DATE ENTERED
echo 'Date Entered: <input type="text" name="ud_date_entered" id="ud_date_entered" value="'.$row['DateEntered'].'" />';

			echo '<br />	<a href="javascript:toggleTools(\'memberNotesDiv'.$row['MemberID'].'\');">General (Member) Notes +/-	</a><br/>
				<div id="memberNotesDiv'.$row['MemberID'].'" style="display:block"><textarea rows="2" cols="47" name="ud_notes" style="background-color:transparent">'.$row['Notes'].'</textarea></div>
				<a href="javascript:toggleTools(\'puNotesDiv'.$row['MemberID'].'\');">Pickup Notes +/-</a><br/>
				<div id="puNotesDiv'.$row['MemberID'].'" style="display:none;"><textarea rows="2" cols="47" name="ud_punotes" style="background-color:transparent">'.$row['PUNotes'].'</textarea></div>
				<br />';
				
//				Neighborhood:	<input type="text" name="ud_nid" value="'.$row['NHoodID'].'"/>
//				<br />';

// NEIGHBORHOOD COMBOBOX
echo '			Neighborhood:'.allNhoodCombobox("NHbox", "", $row['NHoodID']).'<br /> ';
				
// CONFIRM DONOR 
//echo '			<input type="checkbox" name="confirmed" value="YES" '.$confirmed.' title="Confirm data about this member and remove them from this list" />
//				Confirmed
				
//					';

//SAVE CHANGES
//echo '			<input type="submit" name="Save" value="Save & Send Request to NC" /><br />';
echo '	<a style="margin-top: 10px;" class="queuebuttons"  onclick="document.getElementById(\'unconfirmedForm'.$row['MemberID'].'\').submit();">Save Changes and Assign to NCs Queue</a>';





		echo '	</form>';
	echo '</div>';
		echo '<br />';
		

		
	}//end while



}//end getUnconfirmedDonors()
	
	
	
function getNcContactPanel()
{
	echo '<h2>NC Contact List</h2>';
	 echo allNcContactInfo();	
}
	
function getDcContactPanel()
{
	echo '<h2>DC Contact List</h2>';
	 echo allDcContactInfo();	
}	
	
function getGeoPanel()
{
	echo '		<h2>Find Geocode</h2>
		<input id="newAddress" type="textbox" value="Enter Physical Address" />
			<input type="button" value="Show on Map" onclick="codeAddress(document.getElementById(\'newAddress\').value, \'myCoords\')" />
			(latitude,longitude):		<input id="myCoords" type="textbox" />';
}

function getEmailPanel()
{
	include('emailWelcome.php');
}









?>