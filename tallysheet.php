<?php 
	include("securepage/nfp_password_protect.php"); 
	include("functions.php");
//tallysheet.php needs these variables in $_GET[]:
// id (the logged-in user's memberID), 
// nhid (the NHoodID of the neighborhood), 
// ncid (the ID of the NC)
//so the button is: <input type="button" value="View Tallysheet"  target="ContentFrame" title="View the Tallysheet" onclick="ContentFrame.location.href=\'tallysheet.php?ncid='.$ncid.'&id='.$id.'&nhid='.$nhid.' \'"/> 
$activeColor="#fafafb";
$inactiveColor="gray";
?>

<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Tallysheet</title>
	<meta name="description" content="NOS Tallysheet">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />
</head>

<body class="Content">

<?php 

//variable values that get passed in
$NCid=$_GET['ncid'];
$PUdate=getNextPUdate();
$nhID=$_GET['nhid'];
$NHoodID=$nhID;
$uid=$_GET['uid'];

//include opendb() function to set up db related variables and connect to db	
opendb();

//assumes only one neighborhood assigned per NC
$NHood=mysql_fetch_array(mysql_query("	Select NHoodID,NHName from neighborhoods where NhoodID=".$nhID));
$NHoodID = $NHood["NHoodID"];
$NHName=$NHood["NHName"];

$DistrictName=getDistrictNameFromNhoodID($NHoodID);

//pull info from database using neighborhood name (as determined above)
$tallySql="SELECT FirstName, LastName, Apt, House,StreetName, PreferredPhone, PreferredEmail, SecondaryEmail, Notes, PUNotes, MemberID, routeOrder, City, Status FROM  members WHERE NHoodID='".$NHoodID."' AND accepted=1 AND hasBag=1 AND(Status='ACTIVE' OR Status='INACTIVE') ORDER BY routeOrder,City,StreetName,House, Apt";

$result=mysql_query($tallySql);
/*
//debug the query for $result
if($result)
	echo '<p style="color:lime">GOT THE TALLY LIST FROM DATABASE</p><br/>';
else echo '<p style="color:red">COULD NOT GET TALLY LIST FROM DATABASE</p><hr/>'.
	mysql_errno($con) . ': ' . mysql_error($con) . '<hr/>';
*/

$sql = "SELECT historySwitch FROM neighborhoods WHERE NHoodID='".$NHoodID."' " ;
$switchResult=mysql_query($sql);
while ($switchCheck=mysql_fetch_array($switchResult))

	{
	if ($switchCheck['historySwitch']==1)
		$tally=buildTallysheet1($result, $NHName, $DistrictName, $PUdate,$inactiveColor,$activeColor);

	else $tally=buildTallysheet2($result, $NHName, $DistrictName, $PUdate,$inactiveColor,$activeColor);	
	}

if(isset($_GET['pdf']))
	tallyPDF($tally, $NHName, $PUdate);
	
//////////////////////////			
//	Start The Form		//		
//////////////////////////	
echo '	<form method="post" action="tallyCommit.php">';


//////////////////////////
//	The Buttons			//
//////////////////////////	

	echo '<div style="margin-right: 15px; margin-left: 15px; left: 25px; top: 55px; padding: 15px;>	';

		echo ' <a href="#" onclick="location.href=\'tallysheet.php?uid='.$uid.'&ncid='.$NCid.'&nhid='.$NHoodID.'&pdf=save \'"><br /><img src="icons/pdf_icon.png" alt="click here for PDF"></a><br />This page is a PREVIEW of your Tallysheet. Please click on the PDF icon in the upper left to VIEW and PRINT your actual Tallysheet. If you do not have Adobe Reader or Adobe Acrobat installed, please <a href="http://www.adobe.com/products/reader.html" target="_blank">go here</a> to download it. Once you open the Tallysheet PDF file on your computer, make sure your page settings/printer settings are set to landscape orientation before printing. <hr> ';
	echo '</div>	';
	

	
//////////////////////////
//	The Tally Sheet		//
//////////////////////////

	echo '<div class="fullWidget" style="padding: 5px; margin-top: 50px;">	';
	echo $tally;	
	echo ' </div>	';
echo ' </form>';
	
mysql_close($con);
		
	
?>


</body>
</html>



<?php

function buildTallysheet1($result, $NHName, $DistrictName, $PUdate,$inactiveColor,$activeColor)
{
	
		//	The header 	//
		echo '<div style="text-align: center;"> ';
		$tally= '<table><tr>	';
		$tally.='<td>Tallysheet for Neighborhood: '.$NHName.' <br />of District: '.$DistrictName.'</td>';
		$tally.= '</tr><tr>	';
		$tally.='<td>'.$PUdate.' </td>';
		$tally.='<td>Save/print date: '.date('F j Y H:i:s').'</td> ';
		$tally.= '</tr></table>	';
		echo '</div> ';
	
	
	
	
//	The tally table	//
		$tally.= '<table  border="1" repeat_header="1" cellpadding="5" cellspacing="0" > 
					<thead><tr style="background-color:yellow">
						  
						  <th style="width: 50px;">House </th>
						  <th style="width: 100px;">Street</th>
						  <th style="width: 10px;">Apt</th>
						  <th style="width: 100px;">First Name</th>
						  <th style="width: 100px;">Last Name</th>
						  <th style="width: 75px;">Phone & Emails</th> 
						  <th style="width: 200px;">PU Notes</th>
					</tr></thead>';
		
		$idx=0;
		if($result)
		while ($row=mysql_fetch_array($result))
			{	
			if($row['Status']=="INACTIVE")
				$tally.='<tr style="background-color:'.$inactiveColor.';">';
			else
				$tally.= '<tr style="background-color:'.$activeColor.';">';
			
				
			if($row['House']=='')
				$house=" - ";
			else $house=$row['House'];
			if($row['StreetName']=='')
				$street=" - ";
			else $street=$row['StreetName'];
			if($row['Apt']=='')
				$apt=" - ";
			else $apt=$row['Apt'];
			if($row['FirstName']=='')
				$firstname="&nbsp; - ";
			else $firstname=$row['FirstName'];
			if($row['LastName']=='')
				$lastname=" - ";
			else $lastname=$row['LastName'];
			if($row['PreferredPhone']=='')
				$phone=" - ";
			else $phone=$row['PreferredPhone'];
			if($row['PreferredEmail']=='')
				$email='';
			else $email=$row['PreferredEmail'];
			if($row['SecondaryEmail']=='')
				$secondemail='';
			else $secondemail=$row['SecondaryEmail'];
			if($row['PUNotes']=='')
				$punotes=" - ";
			else $punotes=$row['PUNotes'];
					 
				$tally.= '<td width="50px">'.$house.'</td>	';
				$tally.= '<td width="100px">'.$street.'</td>';
				$tally.= '<td align="center" width="10px">'.$apt.'</td>';
				$tally.= '<td width="100px">'.$firstname.'</td>';
				$tally.= '<td width="100px">'.$lastname.'</td>';
				$tally.= '<td width="75px">'.$phone.' <a href="mailto:'.$email.'">'.$email.'</a>&nbsp;&nbsp;<a href="mailto:'.$secondemail.'">'.$secondemail.'</a></td>';
				$tally.= '<td style="width: 200px;">'.$punotes.'</td>';
				
				$tally.= '</tr>';
				$idx++;
			}
		$tally.= '</table>';
	
	return $tally;

//closes the function that does not include the Donor History display
}

function buildTallysheet2($result, $NHName, $DistrictName, $PUdate,$inactiveColor,$activeColor)
{
	
		//	The header 	//
		echo '<div style="text-align: center;"> ';
		$tally= '<table><tr>	';
		$tally.='<td>Tallysheet for Neighborhood: '.$NHName.' <br />of District: '.$DistrictName.'</td>';
		$tally.= '</tr><tr>	';
		$tally.='<td>'.$PUdate.' </td>';
		$tally.='<td>Save/print date: '.date('F j Y H:i:s').'</td> ';
		$tally.= '</tr></table>	';
		echo '</div> ';
	
	
	
	
//	The tally table	//
		$tally.= '<table  border="1" repeat_header="1" cellpadding="5" cellspacing="0" > 
					<thead><tr style="background-color:yellow">
						  
						  <th style="width: 50px;">House </th>
						  <th style="width: 100px;">Street</th>
						  <th style="width: 10px;">Apt</th>
						  <th style="width: 100px;">First Name</th>
						  <th style="width: 100px;">Last Name</th>
						  <th style="width: 75px;">Phone & Emails</th>
						  <th style="width: 100px;">Pickup History</th>
						  <th style="width: 50px;"> Pickup </th>  
						  <th style="width: 200px;">PU Notes</th>
					</tr></thead>';
		
		$idx=0;
		if($result)
		while ($row=mysql_fetch_array($result))
			{	
			if($row['Status']=="INACTIVE")
				$tally.='<tr style="background-color:'.$inactiveColor.';">';
			else
				$tally.= '<tr style="background-color:'.$activeColor.';">';
			
				
			if($row['House']=='')
				$house=" - ";
			else $house=$row['House'];
			if($row['StreetName']=='')
				$street=" - ";
			else $street=$row['StreetName'];
			if($row['Apt']=='')
				$apt=" - ";
			else $apt=$row['Apt'];
			if($row['FirstName']=='')
				$firstname="&nbsp; - ";
			else $firstname=$row['FirstName'];
			if($row['LastName']=='')
				$lastname=" - ";
			else $lastname=$row['LastName'];
			if($row['PreferredPhone']=='')
				$phone=" - ";
			else $phone=$row['PreferredPhone'];
			if($row['PreferredEmail']=='')
				$email='';
			else $email=$row['PreferredEmail'];
			if($row['SecondaryEmail']=='')
				$secondemail='';
			else $secondemail=$row['SecondaryEmail'];
			if($row['PUNotes']=='')
				$punotes=" - ";
			else $punotes=$row['PUNotes'];
			
				//$tally.= '<td>'.$row["routeOrder"].'</td>';
			//pickup History
			$numDates=6;
			$dates=getRecentPickupDates($numDates);
			if($dates) $donorHist=getDonorHistoryTable($row["MemberID"], $dates, $numDates);
			else $donorHist="N/A"; 
					 
				$tally.= '<td width="50px">'.$house.'</td>	';
				$tally.= '<td width="100px">'.$street.'</td>';
				$tally.= '<td align="center" width="10px">'.$apt.'</td>';
				$tally.= '<td width="100px">'.$firstname.'</td>';
				$tally.= '<td width="100px">'.$lastname.'</td>';
				$tally.= '<td width="75px">'.$phone.' <a href="mailto:'.$email.'">'.$email.'</a>&nbsp;&nbsp;<a href="mailto:'.$secondemail.'">'.$secondemail.'</a></td>';
				$tally.= '<td width="100px">'. $donorHist .'</td>';
				$tally.= '<td align="center" width="50px"> Y&nbsp;&nbsp;&nbsp;N&nbsp;&nbsp;&nbsp;V</td>';
				$tally.= '<td style="width: 200px;">'.$punotes.'</td>';
				
				$tally.= '</tr>';
				$idx++;
			}
		$tally.= '</table>';
	
	return $tally;
}



function tallyPDF($tally, $nhname, $pudate)
{
	include('./mpdf/mpdf.php');
	$mpdf= new mPDF('', 'Letter-L');
	$mpdf->shrink_tables_to_fit=0;
	$mpdf->debug=true;
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->SetHeader(''.$nhname.'|'.$pudate.'|{PAGENO}');
	$mpdf->WriteHTML($tally);
	$mpdf->Output('Tallysheet_'.$nhname.'_'.$pudate.'.pdf','D');
//exit;
}
?>