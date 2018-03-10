<?php 
	$output = fopen('php://output', 'w');
	fputcsv($output, array('MemberID', 'Source', 'DateEntered', 'LastName', 'FirstName', 'House', 'StreetName', 'Apt', 'City', 'State', 'Zip', 'NHoodID', 'Accepted', 'ReassignNotes', 'hideFromReassignerQueue', 'PreferredEmail', 'SecondaryEmail', 'PreferredPhone', 'Password', 'Status', 'Notes', 'PUNotes', 'WCNotes', 'PrintName', 'DateLastUpdated', 'UpdatorID', 'latLong', 'routeOrder', 'hasBag', 'WCEmail', 'NCEmail'));
	date_default_timezone_set('America/Los_Angeles');
	$date=date("m/d/y");
	include("securepage/nfp_password_protect.php"); 
	include ("functions.php");
	include ("config.php");
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=CSV_Active_Members_Table_'.$date.'.csv');
	header('Pragma: no-cache');
	opendb();
	$rows = mysql_query('SELECT * FROM members WHERE Status="ACTIVE"');
	while ($row = mysql_fetch_assoc($rows)) 
	fputcsv($output, $row);
?>