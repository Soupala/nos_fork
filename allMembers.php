<?php 
	include("securepage/nfp_password_protect.php"); 
	// flatDB takes two parameters, uid and page number
	// (ie.: allMembers.php?id='.$id.'&page='.$page
	include ("functions.php");
	//open the database
	opendb();
	
	if(isset($_POST['page']))
		$page=$_POST['page'];
	else $page=1;
	$uid=$_GET['uid'];
	if(isset($_POST['maxrows']))
		{$maxrows=$_POST['maxrows'];
		}
	else $maxrows=100;
	if(isset($_POST['orderBy']))
		$orderBy=$_POST['orderBy'];
	else $orderBy='LastName';
	 if(isset($_POST['deletionID']))
		{
			$delSQL="DELETE FROM members WHERE MemberID=".$_POST['deletionID'];
			$result=mysql_query($delSQL);
			if($result) $delSQLresult=true;
				else $delSQLresult=false;
		}
//debug:
	// echo '<br/> Passed in via $_POST: page:'.$_POST['page'].' maxrows:'.$_POST['maxrows'];
	
	//if (isset($_POST['DistrictNameBox']))
		//echo '<p style="color:crimson">districtNameBox is set in _POST. value='.$_POST['DistrictNameBox'].'</p>';

		//$num=mysql_query("SELECT * FROM members WHERE Status='ACTIVE' or Status='INACTIVE'");
		$num=mysql_query("SELECT MemberID from members WHERE (Status='ACTIVE' OR Status='INACTIVE') ");
			$numDonors=mysql_num_rows($num);
		$num=mysql_query("SELECT * FROM members WHERE (accepted=1 OR hasBag=1) AND Status='ACTIVE' ");
			$active=mysql_num_rows($num);		//if($active <1) $active=0;
		$num=mysql_query("SELECT * FROM members WHERE (accepted=1 OR hasBag=1) AND Status='INACTIVE'");
			$inactive=mysql_num_rows($num);		//if($inactive <1) $inactive=0;
		$num=mysql_query("SELECT * FROM members WHERE Status='ARCHIVED'");
			$archived=mysql_num_rows($num);		//if(!$num) $archived=0;
		$num=mysql_query("SELECT MemberID FROM members WHERE accepted=0  AND (Status='ACTIVE' OR Status='INACTIVE')");
			$numNew=mysql_num_rows($num);
		
		?>
 
<!doctype html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Logged into Member Database Application of the Neighborhood Food Project</title>
	<meta name="description" content="Member DB App">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />
	<script type="text/javascript" src="js/swapdivs.js"> </script>
	<script type="text/javascript" src="js/toggletools.js"> </script>
	<script src="js/libs/modernizr-2.5.3.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"> </script>
	<script type="text/javascript" >
		function submitOnEnter(e, form)
		{
			var key=e.keyCode || e.which;
			if (key==13)
				{form.submit();}
		}
		
		function formSubmit( thePage)
		{
			document.getElementById('page').value=thePage;
			document.getElementById("flatDBForm").submit();	
		}
		
		 
		function deleteThisMember(MemberID)
		{	//debug
			//alert("homemade alert box called");
			
			var thebox=document.getElementById("alertbox");
			var thespan=document.getElementById("alertboxtext");
			var theform=document.getElementById("alertboxform");
			var theID=document.getElementById("deletionID");
			
			thebox.style.left="25%";
			theID.value=MemberID;
			thespan.innerHTML="<p style=' text-align:center; margin-top:10%;' > You are about to delete the member with MemberID:"+MemberID+"<br/><br/>"
				+"Are you sure you want to do this? <br/><br/>"
				+"This deletion CANNOT BE UNDONE OR RECOVERED</p>";
				
		}
		
		function doOnLoad()
		{
			<?php 
				if(isset($delSQLresult) && $delSQLresult==true)
					echo 'alert("(line103) Member deleted. ID='.$_POST['deletionID'].'")';
			
			?>
		}
	</script>

	<style type="text/css">
		
		tr, td, th {padding:10px 10px 10px 10px; text-align:center; border:1px solid #bbb1a7;}
		table {color:#2d2e2f }
		
	</style>
</head>

<body style="top: 50px; margin: 15px;" onload="doOnLoad()">

<?php 
//debug:
	//print_r($_POST);	?>
	
<!-- HOMEMADE "ALERT() BOX" DIV -->
<div id="alertbox" style="position:fixed; top:25%; left:-250%; height:50%; width:50%; background-color:#de040a; color:white;	border: 3px white;	-webkit-border-radius: 8px;	-moz-border-radius: 8px;	border-radius: 8px;	padding: 25px; z-index:20;">
	<form id="alertboxform" action="allMembers.php?uid=<?php echo $uid ?>" method="post" >
		<span id="alertboxtext"> this is a homemade alert box</span>
		<br/>
		<input id="deletionID" name='deletionID' type='hidden'  />
		<br/>
		<input type="button" value="Cancel" style="float:left;" onclick="document.getElementById('alertbox').style.left='-250%'" />
		<input type="button" value="Do It" style="float:right;" onclick="document.getElementById('alertboxform').submit()" />
	</form>
</div>
	
<h1 style="color: #2f4b66; left: 15px; top:5px;">All-Members<a href="allMembers.php?uid=<?php echo $uid ?>" target="ContentFrame" style="padding-left: 15px;"><img src="icons/reload.png" alt="reload" /></a></h2>
<br/>
<p style="font-size: 11px;">***Currently <?php echo $numDonors ?> records.  Initial load of this page limited to first <?php echo $maxrows ?> records.***	<span style="float:right;"><?php echo $active ?> active records, <?php echo $inactive ?> inactive records, <?php echo $archived ?> archived records, <?php echo $numNew ?> new members</span></p>

<div style="margin-bottom: 10px;">

 



	<?php
	
	//debug:
		//echo 'POST maxrows:'.$_POST['maxrows'];
	
	
	//$fname=$_GET['fname'];
/* to get records/page and to advance from page to page:
	need a MaxEntriesPerPage
	

*/	
////////////////////////////////////////////////////////////////////////////////
//		Construct the SQL query from the search boxes on top
//			- Each box that has a search string in it gets AND-ed to the LIKE
//			clause of the SQL query (the first gets the WHERE)
//			- When the search is initiated, each of text area is POST-ed to this same page
////////////////////////////////////////////////////////////////////////////////
$like="";
	//search db WHERE $field LIKE $searchterm
	if (isset($_POST["LastNameBox"]))
	{
	
	 if ($_POST["DistrictNameBox"]!="")
//	if ($like == "")
	{	$like = ' as m, neighborhoods as n , districts as d WHERE n.DistrictID=d.DistrictID AND m.NHoodID=n.NHoodID AND n.DistrictID = '.$_POST["DistrictNameBox"].' ';
//	else $like.=' AND DistrictID = "'.$_POST["DistrictNameBox"];
	$orderBy= " NHName, LastName";
	}
//first,check if $like is passed in from $_POST
	if(isset($_POST['like']))
		$like=$_POST['like'];
	else
	{
		if ($_POST["NHNameBox"]!="")
			if ($like == "")
				$like = 'as m, neighborhoods as n WHERE m.NHoodID=n.NHoodID AND n.NHoodID='.$_POST["NHNameBox"];
			else $like.=' AND NHName LIKE "%'.$_POST["NHNameBox"].'%" ';
	
		if ($_POST["LastNameBox"] !="")
			if ($like=="")
				$like='WHERE LastName LIKE "%'.$_POST["LastNameBox"].'%" ';
			else $like.=' AND LastName LIKE "%'.$_POST["LastNameBox"].'%" ';
		if ($_POST["FirstNameBox"] !="")
			if ($like=="")
				$like='WHERE FirstName LIKE "%'.$_POST["FirstNameBox"].'%" ';
			else $like.=' AND FirstName LIKE "%'.$_POST["FirstNameBox"].'%" ';
		
		else if ($_POST["HouseBox"]!="")
			if ($like == "")
				$like = 'WHERE House='.$_POST["HouseBox"];
			else $like.='  AND House='.$_POST["HouseBox"];
		
		else if ($_POST["StreetBox"] !="")
			if ($like == "")
				$like = 'WHERE StreetName LIKE "%'.$_POST["StreetBox"].'%" ';
			else $like.=' AND StreetName LIKE "%'.$_POST["StreetBox"].'%" ';
		else if ($_POST["AptBox"] !="")
			if ($like == "")
				$like = 'WHERE Apt LIKE "%'.$_POST["AptBox"].'%" ';
			else $like.=' AND Apt LIKE "%'.$_POST["AptBox"].'%" ';
		else if ($_POST["CityBox"]!="")
			if ($like == "")
				$like = 'WHERE City LIKE "%'.$_POST["CityBox"].'%" ';
			else $like.=' AND City LIKE "%'.$_POST["CityBox"].'%" ';
		else if ($_POST["ZipBox"]!="")
			if ($like == "")
				$like = 'WHERE Zip LIKE "%'.$_POST["ZipBox"].'%" ';
			else $like.=' AND Zip LIKE "%'.$_POST["ZipBox"].'%" ';

		else if ($_POST["NCBox"]!="")
				if ($like == "")
				$like = 'WHERE Zip LIKE "%'.$_POST["ZipBox"].'%" ';
			else $like.=' AND Zip LIKE "%'.$_POST["ZipBox"].'%" ';
			
		else if ($_POST["PrefEmailBox"]!="")
			if ($like == "")
				$like = 'WHERE PreferredEmail LIKE "%'.$_POST["PrefEmailBox"].'%" ';
			else $like.=' AND PreferredEmail LIKE "%'.$_POST["PrefEmailBox"].'%" ';
		else if ($_POST["PrefPhoneBox"]!="")
			if ($like == "")
				$like = 'WHERE PreferredPhone LIKE "%'.$_POST["PrefPhoneBox"].'%" ';
			else $like.=' AND PreferredPhone LIKE "%'.$_POST["PrefPhoneBox"].'%" ';
//		else if ($_POST["DateEnteredBox"]!="")
		else if (!isset($_POST["DateEnteredBox"]))
			if ($like == "")
				$like = 'WHERE  DateEntered LIKE "%'.$_POST["DateEnteredBox"].'%" ';
			else $like.='  AND DateEntered LIKE "%'.$_POST["DateEnteredBox"].'%" ';

		else if ($_POST["SourceBox"]!="")
			if ($like == "")
				$like = 'WHERE  Source LIKE "%'.$_POST["SourceBox"].'%" ';
			else $like.=' AND Source LIKE "%'.$_POST["SourceBox"].'%" ';
		
		else if ($_POST["PUNotesBox"]!="")
			if ($like == "")
				$like = 'WHERE  PUNotes LIKE "%'.$_POST["PUNotesBox"].'%" ';
			else $like.=' AND PUNotes LIKE "%'.$_POST["PUNotesBox"].'%" ';

		//else if ($_POST["NotesBox"]!="")
			//if ($like == "")
				//$like = 'WHERE  Notes LIKE "'.$_POST["NotesEnteredBox"].'%" ';
			//else $like.='  AND Notes LIKE "'.$_POST["NotesBox"].'%" ';

	}	
		if($like=="") $like=" ";
		
//echo '<br/>$districtBox: '.$_POST["DistrictBox"];
	}

//echo '$like= '.$like;
	
				
				
				
//////////////////////////////////////////////////////////////////////////////
	//orderBy:
		// if (isset($_GET["orderBy"]) )
		// {	//$orderBy=mysql_real_escape_string($_GET["orderBy"]);	

			// $orderBy=$_GET["orderBy"];
		// }
		// else if(!isset($orderBy)) $orderBy="MemberID";
		
		
	
	
	//$maxRows=100;			//maximum number of rows to display at once
	$role="ADMIN";				// Role of the member doing the editing

	
//echo 'your role: '.$role;
//	$permissionsArr=getEditPermissions($role);

	
	


	
	
	if(isset($_GET['orderBy']) && $_GET["orderBy"]=="Neighborhood")
	{	$sql="SELECT * FROM members,neighborhoods WHERE members.NHoodID=neighborhoods.NHoodID ORDER BY NHName,LastName ";
		$result=mysql_query($sql);
	}
	else if(isset($_GET['orderBy']) && $_GET["orderBy"]=="District")
	{	$sql="SELECT * FROM members,neighborhoods,districts WHERE members.NHoodID=neighborhoods.NHoodID AND neighborhoods.DistrictID=".$_POST['DistrictNameBox']." ORDER BY DistrictName,NHName ";
		$result=mysql_query($sql);
	}
	else
	//get all member info
	$sql="SELECT * FROM members ".$like." ORDER BY ".$orderBy;
	$result= mysql_query($sql);
	
	//debug:
		//echo $sql;
	
	
	//////////////////////////////////////////
//	START PAGINATION SECTION			//
//////////////////////////////////////////
		$limitStart=($page-1)*$maxrows;
			$limit=$limitStart.','.$maxrows;

		$num=mysql_num_rows($result);
		
		$maxpage=ceil($num/$maxrows);
		$start=($page-1)*$maxrows;
		$end=(($page-1)*$maxrows)+$maxrows-1;
//debug:
	// echo 'number of results:'.$num.' max rows:'.$maxrows.' max page:'.$maxpage.' actual max pages:'.$num/$maxrows.'<br/>';
		
		// echo ' maxrows:'.$maxrows.'  page:'.$page.'  maxpage:'.$maxpage.' start:'.($maxrows*$page).' end:'.(($maxrows*$page)+$maxrows).'<br/>';
		
	echo '<div id="paginationMenu" style="text-align:center; position:relative; top:0px; height:50px; font-size: 18px; font-weight: bolder; color: red;" >
	'; 
		
		echo '***Showing '.$start.'-'.$end.' of '.$num.' Members***<br />';
		
		echo ' <a style="text-decoration: none; color: #304b66;"  title="To First Page" href="#" onclick="formSubmit(1);">				&lt;&lt; </a>	&nbsp;';
		if($page>1)
			echo ' <a style="text-decoration: none; color: #304b66;"  title="Back One Page" href="#" onclick="formSubmit('.($page-1).');">&lt; </a>	&nbsp;';
			if($page==$maxpage && $maxpage>4)
				echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="formSubmit('.($page-4).');">'.($page-4).'</a>		&nbsp;';
			if(($page==$maxpage-1 ||$page==$maxpage) && $maxpage>3)
				echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="formSubmit('.($page-3).');" >'.($page-3).'</a>		&nbsp;';
		if($page>2)
			echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="formSubmit('.($page-2).');"">'.	($page-2).'</a>		&nbsp;';
		if($page>1)
			echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="formSubmit('.($page-1).');">'.($page-1).'</a>		&nbsp;';
	echo '<b>&nbsp '.$page.' &nbsp</b>';				//current page
		if($page<$maxpage)
			echo' <a style="text-decoration: none; color: #304b66;" href="#" onclick="formSubmit('.($page+1).');">'.($page+1).'</a>		&nbsp;';
		if($page<$maxpage-1)
			echo' <a style="text-decoration: none; color: #304b66;"  href="#" onclick="formSubmit('.($page+2).');">'.($page+2).'</a>		&nbsp;';
			if($page<3 &&$maxpage>4)
				echo' <a style="text-decoration: none; color: #304b66;"  href="#" onclick="formSubmit('.($page+3).');">'.($page+3).'</a>		&nbsp;';
			if($page<2 && $maxpage>3)
				echo' <a style="text-decoration: none; color: #304b66;"  href="#" onclick="formSubmit('.($page+4).');">'.($page+4).'</a>		&nbsp;';
		if($page<$maxpage)
			echo ' <a style="text-decoration: none; color: #304b66;"  title="Forward One Page" href="#" onclick="formSubmit('.($page+1).');">&gt </a>	&nbsp;';
		echo ' <a style="text-decoration: none; color: #304b66;"  title="To Last Page" href="#" onclick="formSubmit('.$maxpage.');">		&gt;&gt; </a>	';
	
	echo'	</div>
	';
	
	
	//////////////////////////////////////
	//	END PAGINATION SECTION			//
	//////////////////////////////////////

	 
	
//the limited results:	
	$sql.=" LIMIT ".$limit;
	$result=mysql_query($sql);
	
	
	
	
	
//////////////////////////////
//	Member data table		//	
//////////////////////////////
 

	echo '	<form id="flatDBForm" action="allMembers.php?uid='.$uid.'" method="post" onkeypress="submitOnEnter(event, this)">';
	
	// echo 'Limit by Neighborhood: '.	allNhoodCombobox("NHNameBox", "document.getElementById('flatDBForm').submit()" ).'<br/>';
	// echo 'Limit by District: '. DistrictsComboBox("DistrictNameBox", "document.getElementById('flatDBForm').submit()" ).'<br/>';
	
	//the maxrows to show on a page
	//	echo 'Rows per page: <input type="text" id="maxrows" name="maxrows" value="'.$maxrows.'" />';
	echo '<input type="hidden" id="page" name="page" value="'.$page.'"/>
		Rows to Display per Page:<input type="text" id="maxrows" name="maxrows" value="'.$maxrows.'" size="4"/>
		<input type="hidden" id="orderBy" name="orderBy" value="'.$orderBy.'" />
		';
	
			echo '<table border="1" style="font-size:10pt; border: 1px #bbb1a7; top:100px;" > ';
			
			
//////////////////////////////////////////////////////////////
// The column headers. user clicks on one to order by that column

			echo '<tr>
				<th class="dbTable" >row</th>
				<th class="dbTable" >edit</th>
				<th class="dbTable" >MemberID</th>
				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'LastName\'; formSubmit(1);">	Last Name</a> </th>
				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'FirstName\'; formSubmit(1);">First Name </a>	</th>
				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'House\'; formSubmit(1);">House	</a></th>
				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'StreetName\'; formSubmit(1);">Street Name	</a></th>
				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'Apt\'; formSubmit(1);">Apt	</a></th>
				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'City\'; formSubmit(1);">City </a></th>
				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'State\'; formSubmit(1);">State	</a></th>
				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'Zip\'; formSubmit(1);">Zip Code	</a></th>
				
				
				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'PreferredEmail\'; formSubmit(1);"> Email	</a></th>
								
				
				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'SecondaryEmail\'; formSubmit(1);"> Secondary Email	</a></th>

				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'PreferredPhone\'; formSubmit(1);"> Phone	</a></th>

				
				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'DateEntered\'; formSubmit(1);">Date Entered	</a></th>
				<th class="dbTable" >Source	</th>
				<th class="dbTable" >Role	</th>
				<th class="dbTable" >Pickup Notes	</th>
				<th class="dbTable" >Notes	</th>
				<th class="dbTable" ><a href="#" onclick="document.getElementById(\'orderBy\').value=\'Status\'; formSubmit(1);">Status	</a></th>
				<th class="dbTable"  >Pickup History	</th>
				<th class="dbTable" >NC	</th>
				<th class="dbTable" >DC	</th>

				<th class="dbTable" >	Neighborhood <br/>(filter only)	</th>
				<th class="dbTable" >	District <br/> (filter only)	</th>
				<th class="dbTable" > DELETE </th>
				<th class="dbTable" >row</th>
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
				<td></td>
				<td  ><input class="queuebuttons" style="padding: 10px;" type="submit" value="search"  /></td>
				<td ></td>
				<td ><input type="text"	name="LastNameBox" value="'.$_POST["LastNameBox"].'" style="width:90%" /></td>
				<td ><input type="text"	name="FirstNameBox" value="'.$_POST["FirstNameBox"].'" style="width:90%" /></td>
				<td ><input type="text"	name="HouseBox" value="'.$_POST["HouseBox"].'" style="width:90%" /></td>
				<td ><input type="text"	name="StreetBox" value="'.$_POST["StreetBox"].'" style="width:90%" /></td>
				<td ><input type="text"	name="AptBox" value="'.$_POST["AptBox"].'" style="width:90%" /></td>
				<td ><input type="text"	name="CityBox" value="'.$_POST["CityBox"].'" style="width:90%" /></td>
				<td ><input type="text"	name="StateBox" value="'.$_POST["StateBox"].'" style="width:90%" /></td>
				<td ><input type="text"	name="ZipBox" value="'.$_POST["ZipBox"].'" style="width:90%" /></td>
		
				<td ><input type="text"	name="PrefEmailBox" value="'.$_POST["PrefEmailBox"].'" style="width:90%" /> </td>
				<td ><input type="text"	name="PrefPhoneBox" value="'.$_POST["PrefPhoneBox"].'" style="width:90%" /> </td>
				
				<td ><input type="text"	name="SecondEmailBox" value="'.$_POST["SecondEmailBox"].'" style="width:90%" /> </td>
				
				<td ><input type="text"	name="DateEnteredBox" value="'.$_POST["DateEnteredBox"].'" style="width:90%" /></td>
				<td ><input type="text"	name="SourceBox" value="'.$_POST["SourceBox"].'" style="width:90%" /></td>
				<td ><input type="hidden"	name="RoleBox" value="'.$_POST["RoleBox"].'" style="width:90%" /></td>
				<td ><input type="hidden"	name="PUNotesBox" value="'.$_POST["PUNotesBox"].'" style="width:90%" /></td>
				 <td ><input type="hidden"	name="NotesBox" value="'.$_POST["NotesBox"].'" style="width:90%" /></td>
				 <td></td>
				<td></td>
				<td ><input type="hidden"	name="NCBox" value="'.$_POST["NCBox"].'" style="width:90%" /></td>
				<td ><input type="hidden"	name="DCBox" value="'.$_POST["DCBox"].'" style="width:90%" /></td>
				 <!--				 <td ><input type="hidden"	name="NHNameBox" value="'.$_POST["NHNameBox"].'" style="width:90%" /></td>  -->
				<td>'.allNhoodCombobox("NHNameBox", "document.getElementById('flatDBForm').submit()" ).'</td>
				
				
			<!--	  <td ><input type="hidden"	name="DistrictNameBox" value="'.$_POST["DistrictNameBox"].'" style="width:90%" /> </td> 	-->
				<td>'.DistrictsComboBox("DistrictNameBox", "document.getElementById('flatDBForm').submit()" ).'</td>
				<td></td>
				<td></td>
			</tr>';
	else
		echo '
			<tr>  
				 <td></td>
				<td ><input type="submit" value="search"  /></td>
				<td ></td>
				<td ><input type="text"	name="LastNameBox"  style="width:90%" /></td>
				<td ><input type="text"	name="FirstNameBox" style="width:90%"/></td>
				<td ><input type="text"	name="HouseBox" style="width:90%"/></td>
				<td ><input type="text"	name="StreetBox" style="width:90%" /></td>
				<td ><input type="text"	name="AptBox" style="width:90%" /></td>
				<td ><input type="text"	name="CityBox" style="width:90%" /></td>
				<td ><input type="text"	name="StateBox" style="width:90%" /></td>
				<td ><input type="text"	name="ZipBox" style="width:90%" /></td>
			
				<td ><input type="text"	name="PrefEmailBox" style="width:90%" /></td>
				<td ><input type="text"	name="SecondEmailBox" style="width:90%" /></td>
				<td ><input type="text"	name="PrefPhoneBox" style="width:90%" /></td>
				
				<td ><input type="text"	name="DateEnteredBox" style="width:90%"/></td>
				<td ><input type="text"	name="SourceBox" style="width:90%"/></td>
				<td ><input type="hidden"	name="RoleBox" style="width:90%" /></td>
				<td ><input type="hidden"	name="PUNotesBox" style="width:90%"/></td>
				<td ><input type="hidden"	name="NotesBox" style="width:90%"/></td> 
				<td ></td>
				<td></td>
				<td ><input type="hidden"	name="NCBox" style="width:90%" /></td>
				<td ><input type="hidden"	name="DCBox" style="width:90%" /></td>
	<!--		<td ><input type="hidden"	name="NHNameBox" style="width:90%" /></td> 
				  <td ><input type="hidden"	name="DistrictNameBox" style="width:90%" /></td> 		-->			 
				<td>'.allNhoodCombobox("NHNameBox", "document.getElementById('flatDBForm').submit()" ).'</td>
				<td>'.DistrictsComboBox("DistrictNameBox", "document.getElementById('flatDBForm').submit()" ).'</td>
				<td></td>
				<td></td>
			</tr>';
//	
// end search boxes
////////////////////////////////
	
	

	
	
	//start debug
		//echo $sql;
	//end debug
	
	
///////////////////////////////////////
// Populate the table from the database			
	$count=1;
	while ($row=mysql_fetch_array($result) )
	//	while(	 $count<=$maxRows)
		{
	//		$row=mysql_fetch_array($result) ;
			
			if($count%2==0)
				echo '<tr style="background-color:#fafde9; border: 1px #bbb1a7;" >';
			else
				echo 	'<tr style="background-color:#aef871; border: 1px #bbb1a7;" >';
					//row count
					echo '<td>'.$count.'</td>';
	
			//edit user button		
				echo '<td><a href="editMember.php?fdid='.$row["MemberID"].'&uid='.$uid.'"  title="View/Edit this member\'s information" target="_blank" > <img src="icons/edit.png" alt="Edit This Member" width="30px" height="30px" /></a>
				
				</td>';
				// //row
					// echo '<td>'.$count.'</td> ';
				//MemberID
				  echo '<td ><p>'.$row["MemberID"].'</p></td>';
				//LastName
					echo '<td ><p>'.$row["LastName"].'</p></td>	';		
				//FirstName
					echo '<td ><p>'.$row["FirstName"].'</p></td>	';		
				//House
					echo '<td ><p>'.$row["House"].'</p></td>	';		
				//StreetName
					echo '<td ><p>'.$row["StreetName"].'</p></td>	';	
				//Apt
					echo '<td >'.$row["Apt"].'</td>	';		
				//City
					echo '<td >'.$row["City"].'</td>	';		
				//State
					echo '<td >'.$row["State"].'</td>	';		
				//Zip
					echo '<td >'.$row["Zip"].'</td>	';		

				
				
				//PreferredEmail
					echo '<td ><a href="mailto:'.$row["PreferredEmail"].'">'.$row["PreferredEmail"].'</a></td>	';	

				//SecondaryEmail
					echo '<td ><a href="mailto:'.$row["SecondaryEmail"].'">'.$row["SecondaryEmail"].'</a></td>	';					
	
				//PreferredPhone
					echo '<td >'.$row["PreferredPhone"].'</td>	';		
		
				//Date Entered, Source, PU Notes, and Notes
					echo '<td >'.$row["DateEntered"].'</td>	';
					echo '<td >'.$row["Source"].'</td>	';
				//Role
					echo '<td >'.getUserRoles($row["MemberID"]).'</td>	';
					echo '<td >'.$row["PUNotes"].'</td>	';
					echo '<td >'.$row["Notes"].'</td>	';
					//Status
					echo '<td> '.$row['Status'].'</td>';
				//pickup history
					$numDates=6;
					$dates=getRecentPickupDates($numDates);
					if($dates=='')	echo '<td ></td>';
					else echo '<td >'.getDonorHistoryTable($row["MemberID"], $dates, $numDates).'</td>';
				//NC
					echo '<td> <a target="_blank" href="neighborhood.php?nh='.$row['NHoodID'].'&uid='.$uid.'&tab=true" >'.getNCNameFromNhoodID($row['NHoodID']).'</a></td>';
				//DC
					if($row['NHoodID']!= '')
					{	$didSQL=mysql_query("SELECT DistrictID FROM neighborhoods WHERE NHoodID=".$row['NHoodID']);
						if($didSQL)
						{	$didResult=mysql_fetch_array($didSQL); 
							echo '<td> <a target="_blank" href="district.php?d='.$didResult['DistrictID'].'&uid='.$uid.'&tab=true" >'.getDCNameFromNhoodID($row['NHoodID']).'</a></td>';
						}
						else echo mysql_error().' LINE 369';
					}
					else echo '<td></td>';
					
					//NHood
					//echo '<td>'.$row["NHoodID"].'</td>	';		
						//get name of NHood
						if (isset($row['NHoodID']))
						{	$sql = mysql_query('Select NHName from neighborhoods where NHoodID = '.$row["NHoodID"]);
							$nhoodName = mysql_fetch_array($sql);
							echo '<td > '.$nhoodName["NHName"].'</td>';
						}
						else
						{echo '<td > N/A </td>';
						}
				//District
					//echo '<td>'.$row["DistrictID"].'</td>	';	
							//get name of District
						if (isset($row['DistrictID']))
						{	$sql = mysql_query('Select DistrictName from districts where DistrictID = '.$row["DistrictID"]);
							$districtName = mysql_fetch_array($sql);
							echo '<td > '.$districtName["DistrictName"].'</td>';
						
  						//DELETE
  						echo '<td><input type="button" onclick="deleteThisMember('.$row["MemberID"].');" value="X" style="background-color: #f4f4f4; color: #ff1317; font-weight: bolder; border: 1px solid #bbb1a7; border-radius: 8px;" />';
  						
  						//row count
  						echo '<td>'.$count.'</td>';
							
						}
						else
							{
								//get nhood of member
								//get districtid of nhood
								$districtName=" ? ";
								if($row['NHoodID']!='')
								{
									$sql="SELECT DistrictID FROM neighborhoods WHERE NHoodID=".$row['NHoodID'];
									$distIDarr=mysql_fetch_array(mysql_query($sql));
								//get districtname via districtid
									if($distIDarr!='')
									{
										$query="SELECT DistrictName FROM districts WHERE DistrictID=".$distIDarr['DistrictID'];
										$distName=mysql_fetch_array(mysql_query($query));
										$districtName=$distName['DistrictName'];
									}
								}
								 
								//SELECT DistrictName FROM districts,neighborhoods WHERE neighborhoods.NHoodID=".$row['NHoodID']." AND districts.DistrictID=neighborhoods.DistrictID
								//echo '<script type="text/javascript">alert("$sql:\n'.$sql.'\n$query:\n'.$query.'") </script>';
								
								
								//$sql= mysql_query('SELECT DistrictName FROM districts,neighborhoods WHERE districts.DistrictID=neighborhoods.DistrictID')
								echo '<td >'.$districtName.'</td> ';
								
								//DELETE
								echo '<td><input type="button" onclick="deleteThisMember('.$row["MemberID"].');" value="X" style="background-color: #f4f4f4; color: #ff1317; font-weight: bolder; border: 1px solid #bbb1a7; border-radius: 8px;" />';
								
								//row count
								echo '<td>'.$count.'</td>';
							}
					
					
					
					
			echo '</tr>';
		$count=$count+1;
		}
			
			echo'</table>	';
			
//		echo '</div>	';
		
	
	//	Close Form	//	
	echo' </form>';

	
	//close table div container
  echo ' </div> ';
	

	
	mysql_close($con);

	
	?>

</body>



</html>
