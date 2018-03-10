<!DOCTYPE html>
<?php 
	include("securepage/nfp_password_protect.php");
	include('functions.php');
	include('config.php');
	opendb();
	$uid=$_GET['uid'];

	
// return to tool used before reloading the page
	//if(isset($_GET['tool']))
		//$tool=$_GET['tool'];
	//else $tool='unconfirmedDonors';
	
?>


<html>
<head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />
	<script type="text/javascript">
		function ShowHideDivs(idOfDivToShow)
		{
			
			if(idOfDivToShow == "Switches")
				document.getElementById("Switches").style.display = "block";
			else
				document.getElementById("Switches").style.display = "none";
			
			if(idOfDivToShow == "ExportCSV")
				document.getElementById("ExportCSV").style.display = "block";
			else
				document.getElementById("ExportCSV").style.display = "none";
				
		}
		
	
	</script>
	
</head>

 <body onload="initialize()"> 
 
<h1 style="color: #2f4b66; padding: 5px 5px 15px 15px;">Data Manager</h1>
<br />
	
 
  
	<!-- TITLE BAR -->	
		<div class="gearsWidget" >
				<ul id="adminNav">
				<li><a href="recordTally.php?uid=<?php echo $uid ?>" target="ContentFrame">Record Tallysheets</a></li>
				<li><a href="#" onclick="ShowHideDivs('Switches');">Other Options</a></li>
				<li><a href="#" onclick="ShowHideDivs('ExportCSV');">Export Data</a></li>
				</ul>
		</div>

		
	
		

<div class="fullWidget" id="Switches">
		<div style="width: 450px; padding: 10px;">
		<h2><a href="historySwitch.php?uid='.$uid.'" target="ContentFrame">OFF/ON Pickup History</a></h2>
		<p>Use this tool for turning on/off the display of pickup history in a specific Neighborhood. The pickup history will hide itself from the View Donor list and Tallysheet of that Neighborhood.</p><br />
		</div>
		<div style="width: 450px; padding: 10px;">
		<h2><a href="logs.php">Logs</a></h2>
		<p>This is the first line of defense for accidental deletion of data. Every time someone makes a change to a Profile or a new record comes in via the Sign Up Form, that data is saved into this log file.</p>
		</div>
		
		
</div>	

<div class="fullWidget" id="ExportCSV">
		<div style="width:450px;">
		<h2>Export Data</h2>
		<p style="padding: 10px 0px 10px 0px">Click to download data as a spreadsheet.  To open the file on your computer, you'll need some sort of spreadsheet software such as Microsoft Excel, Google Spreadsheets, or Open Office Calc.</p>
		<table border="1" cellpadding="10" width="450">
		<tr style="font-weight: bold;">
		<td style="padding: 10px">Description</td><td style="padding: 10px">Download</td>
		</tr>
		<tr style="background-color:#aef871; ">
		<td style="padding: 10px">#1- All Members Emails- Primary (excludes INACTIVE and ARCHIVED) </td><td style="padding: 10px"><a href="CSV_All_Member_Active_Email.php">Export to .CSV</a></td>
		</tr>
		<tr style="background-color: #fafde9; ">
		<td style="padding: 10px">#2- All Additional Emails (these are the emails saved into the "Additional Emails" field in the Profiles.)</td><td style="padding: 10px"><a href="CSV_All_Member_Active_Additional_Emails.php">Export to .CSV</a></td>
		</tr>
		<tr style="background-color:#aef871; ">
		<td style="padding: 10px">#3- All NC Emails- Primary (excludes INACTIVE AND ARCHIVED) </td><td style="padding: 10px"><a href="CSV_All_NC_Emails.php">Export to .CSV</a></td>
		</tr>
		<tr style="background-color: #fafde9; ">
		<td style="padding: 10px">#4- All NC Additional Emails (these are the emails saved into the "Additional Emails" field in the Profiles.)</td><td style="padding: 10px"><a href="CSV_All_Member_Active_Additional_Emails.php">Export to .CSV</a></td>
		</tr>
		<tr style="background-color: #aef871; ">
		<td style="padding: 10px">#5- This is a simple two column (Neighborhood Name) + (District Name) spreadsheet you can export for things like keeping track of Tallysheets turned in and/or recorded. </td><td style="padding: 10px"><a href="CSV_NH_District_List.php">Export to .CSV</a></td>
		</tr>
		<tr style="background-color:#fafde9; ">
		<td style="padding: 10px">#6- Dropoff Log Sheet </td><td style="padding: 10px"><a href="CSV_Dropoff_Log.php">Export to .CSV</a></td>
		</tr>
		<tr style="background-color:#aef871; ">
		<td style="padding: 10px">#7- This is a dump of the members table from MySQL Database (excludes INACTIVE and ARCHIVED). </td><td style="padding: 10px"><a href="CSV_Active_Members_Table.php">Export to .CSV</a></td>
		</tr>
		<tr style="background-color:#fafde9; ">
		<td style="padding: 10px">#8- This is a dump of people with key missing info (No Neighborhood Assigned, or No Email, or No Primary Phone) </td><td style="padding: 10px"><a href="CSV_no_email_phone_neighborhood.php">Export to .CSV</a></td>
		</tr>
		<tr style="background-color:#aef871; ">
		<td style="padding: 10px">#9- This is a full dump of all your member data with appended Neighborhood and District data. It does not include Pickup History.  If you wish to do custom analysis of Pickup History data, you'll need to download that data seperately and use special software that can use a "key" between members data and the pickup history data. </td><td style="padding: 10px"><a href="CSV_Members_Data_Dump.php">Export to .CSV</a></td>
		</tr>
		<tr style="background-color:#fafde9; ">
		<td style="padding: 10px">#10- This is a dump of the MySQL table that stores all the Pickup History data. The "key" that associates Pickup History records to member data is MemberID. Please note that some of your oldest pickup history may have been purged in order to make room for fresh data if it goes back more than two years.</td><td style="padding: 10px"><a href="CSV_pickup_history_dump.php">Export to .CSV</a></td>
		</tr>
		</table>
		</div>
</div>		
		
		
		


</body>
</html>

<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script src="js/plugins.js"></script>
  <script src="js/uiFunctions.js"></script>



