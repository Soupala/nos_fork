<?php 
	include("securepage/ashland_password_protect.php"); 
//tallysheet.php needs these variables in $_GET[]:
// id (the logged-in user's memberID), 
// nhid (the NHoodID of the neighborhood), 
// ncid (the ID of the NC)
//so the button is: <input type="button" value="View Tallysheet"  target="ContentFrame" title="View the Tallysheet" onclick="ContentFrame.location.href=\'tallysheet.php?ncid='.$ncid.'&id='.$id.'&nhid='.$nhid.' \'"/> 
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="verify-v1" content="c+H7o9alZ+tSRV6FV+iM3kmM4Cp6fPENNtcxcELXGT8=" />
	<meta name="description" content="Ashland, Oregon's volunteer based community food collection service" />
	<meta name="keywords" content="this, is, the, head, of, tallysheet.pdf" />
	<title>Ashland Food Project</title>
	<link rel="icon" type="image/x-ico" href="images/AFPfavicon.ico" />
	<link rel="shortcut icon" type="image/x-icon" href="images/AFPfavicon.ico" />
	<link rel="stylesheet" type="text/css" href="memberStyles.css" />

	<style type="text/css">
		table {	width:100%;		}
	</style>
</head>

<body class="Content">

<?php 
	include("functions.php"); 

//variable values that get passed in
//$id=$_GET['id'];
//$NCname=$_GET['NCname'];
$NCid=$_GET['ncid'];
//$DCid=$_GET['DCid'];		//maybe don't need?
//$DCname=$_GET['DCname'];
//$district=$_GET['distID'];
//$PUname="*** PUname ***";
$PUdate=getNextPUdate();
//$role=$_GET['role'];



		


//include opendb() function to set up db related variables and connect to db
	
		opendb();
//assumes only one neighborhood assigned per NC
$NHood=mysql_fetch_array(mysql_query("	Select NHoodID,NHName from neighborhoods where NCID=".$NCid));
$NHoodID = $NHood["NHoodID"];
$NHName=$NHood["NHName"];


//pull info from database using name (as determined above)
// $result=mysql_query("SELECT FirstName,LastName,Apt,House,PreDirection,StreetName,PreferredPhone,Notes,PUNotes,MemberID, FROM  members where NHoodID='".$NHoodID."' And Status='Active' ORDER BY City,StreetName,PreDirection,House");
$tallySql="SELECT FirstName, LastName, Apt, House, PreDirection, StreetName, PreferredPhone, Notes, PUNotes, MemberID, routeOrder, City FROM  members WHERE NHoodID='".$NHoodID."' ORDER BY routeOrder,City,StreetName,PreDirection,House";

$result=mysql_query($tallySql);
/*
//debug the query for $result
if($result)
	echo '<p style="color:lime">GOT THE TALLY LIST FROM DATABASE</p><br/>';
else echo '<p style="color:red">COULD NOT GET TALLY LIST FROM DATABASE</p><hr/>'.
	mysql_errno($con) . ': ' . mysql_error($con) . '<hr/>';
*/

$tally=buildTallysheet($result, $NHName, $PUdate);

if(isset($_GET['pdf']))
	tallyPDF($tally);
//	tallyPDF("<p>This is a tallysheet printed as a pdf!</p> <p>(no, not really)</p>");	



	
//////////////////////////			
//	Start The Form		//		
//////////////////////////	
echo '	<form method="post" action="tallyCommit.php">';


//////////////////////////
//	The Buttons			//
//////////////////////////	

	echo '<div class="widget" style="position:absolute; top:2%;  height:75px;">	';
		//echo 'Here is the Tally Sheet for <b >'.$NHName.' </b> ';//. The NC\'s id is: '.$NCid.'<br/>your role is:'.$role;
		
		//echo '<a href=#><img src="../icons/db_comit.png" alt="commit to db" title="commit changes to db" width="30px" height="30px"/> </a>';
		//echo '	<input type="button" value="reload tallysheet" onclick="document.getElementById(\'notesForm\').reset();" />';
		echo '<input type="button" name="savePDF" value="Save as PDF" onclick="location.href=\'tallysheet.php?ncid='.$NCid.'&nhid='.$NHoodID.'&pdf=save \'" />';
		//echo '<form><input type="button" value="Print This Page" onClick="window.print()" /></form>';
		
				
				
				//echo '<a href="htmldoc/ZZZ.html"  >Send To HTMLDOC</a>';

		//echo '<br/> name:'.$NCname.' # NCid:.'.$NCid.' # DCname:'.$DCname.' # PUname:'.$PUname.' # PUdate:'.$PUdate.'<br/>' ;
		
		
		//echo 'NHoodID:'.$NHoodID.' NHName:'.$NHName.'<br/>';
	echo '</div>	';
	
	
	
	
//////////////////////////
//	The Tally Sheet		//
//////////////////////////



	// echo '<div class="widget" style="position:relative; top:75px; width: 25cm; height:19cm;">	';
	
	echo '<div class="widget" style="position:relative; top:75px; width: 25cm; ">	';
	
	echo $tally;	
	echo ' </div>	';
	//echo '<input type="hidden" name="tally" value='.$tally.'/>';
echo ' </form>';
	
mysql_close($con);
		
	
?>


</body>
</html>



<?php
function buildTallysheet($result, $NHName, $PUdate)
{
	
		//	The header 	//
		$tally= '<table  ><tr>	';
		//$tally.= '<td>	District Coordinator: <u>'.$DCname.'</u>	</td><td >	<b>Ashland Food Project - '.$PUdate.'</b>	</td><td>	Pickup Person:____________	</td>	';
		$tally.='<td>Tally Sheet for '.$NHName.'</td>';
		$tally.= '</tr><tr>	';
		$tally.='<td>'.$PUdate.' </td>';
		//$tally.= '<td>	NC or Pickup Person: <u>'.$PUname.'</u>		</td><td>		</td><td>	Y:__________ N:__________	</td>	';
		$tally.= '</tr></table>	';
	
	
	
	
//	The tally table	//
		$tally.= '<table  border="1" > 
					<tr>
						  <th>Route <br/>Order</th><th>First Name</th><th>Last Name</th><th>Address</th><th> Y / N </th>  <th>PU Notes</th>
					</tr>';
		
		$idx=0;
		while ($row=mysql_fetch_array($result))
			{	
				if ($idx % 2 ==0)
					$tally.= '<tr style="background-color:#F5FFFA">';
				else
					$tally.= '<tr style="background-color:#C5E3BF">';
			//editMember button	//	
			//	$tally.= '<td><a href="editMember.php?id='.$row["MemberID"].'&fname='.$row["FirstName"].'&role='.$role/*$row["Role"]*/.'&printName='.$row["PrintName"].'&email='.$row["PreferredEmail"].'" target="ContentFrame" title="View/Edit this member\'s information" > <img src="../icons/edit.png" alt="Edit This Member" width="30px" height="30px" /></a></td>';

				$tally.= '<td>'.$row["routeOrder"].'</td>';
				$tally.= '<td>'.$row["FirstName"].'</td>';
				$tally.= '<td>'.$row["LastName"].'</td>';
				$tally.= '<td>'.$row["House"].' '.$row["Apt"].' '.$row["StreetName"].',  '.$row["City"].'</td>	';
				$tally.= '<td align="center">
					 <input type="radio" name="'.$row["MemberID"].'" value="Y" />
					
					  <input type="radio" name="'.$row["MemberID"].'" value="N" />
					 </td>';
				//echo '<td>'.$row["PreferredPhone"].'</td>';
				//echo '<td>'.$row["Status"].'</td>';
			//	echo '<td><input type="text" size="35" style="background-color:transparent" value="'.$row["Notes"].'"/></td>';
				
				//$tally.= '<td><input type="text" size="35" style="background-color:transparent" value="'.$row["PUNotes"].'"/></td>';
				$tally.= '<td><textarea id="punotes"  style="background-color:transparent; " rows="2" cols="30">'.$row["PUNotes"].'</textarea></td>';
				$tally.= '</tr>';
				$idx++;
			}
		$tally.= '</table>';
	
	return $tally;
}



function tallyPDF($tally)
{
	include('./mpdf/mpdf.php');
	$mpdf= new mPDF();
	$mpdf->debug=true;
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->WriteHTML($tally);
	$mpdf->Output('mpdfTester.pdf','D');
//exit;
}
?>