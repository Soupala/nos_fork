<?php 
	$output = fopen('php://output', 'w');
	fputcsv($output, array('NC Last Name', 'NC First Name', 'Phone Number', 'Neighborhood Name', 'District Name'));
	date_default_timezone_set('America/Los_Angeles');
	$date=date("m/d/y");
	include("securepage/nfp_password_protect.php"); 
	include ("functions.php");
	include ("config.php");
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=Dropoff_Log_'.$date.'.csv');
	header('Pragma: no-cache');
	
	opendb();
	
	$grabrows = "SELECT members.LastName,members.FirstName,members.PreferredPhone,neighborhoods.NHName,districts.DistrictName
			FROM members
			INNER JOIN groups ON members.MemberID = groups.uID
			INNER JOIN neighborhoods ON members.NHoodID = neighborhoods.NHoodID
			INNER JOIN districts ON neighborhoods.DistrictID = districts.DistrictID
			WHERE groups.nc=1 OR groups.dc=1 "; 


	$rows = mysql_query($grabrows) ;
	
	while ($row = mysql_fetch_assoc($rows)) 
	
	fputcsv($output, $row);
?>