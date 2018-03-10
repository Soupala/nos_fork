<?php 
	$output = fopen('php://output', 'w');
	fputcsv($output, array('First Name', 'Last Name', 'Additional Emails'));
	date_default_timezone_set('America/Los_Angeles');
	$date=date("m/d/y");
	include("securepage/nfp_password_protect.php"); 
	include ("functions.php");
	include ("config.php");
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=CSV_All_Member_Active_Additional_Emails_'.$date.'.csv');
	header('Pragma: no-cache');
	opendb();
	$rows = mysql_query('SELECT FirstName, LastName, SecondaryEmail FROM members WHERE Status="ACTIVE"') ;
	while ($row = mysql_fetch_assoc($rows)) 
	fputcsv($output, $row);
?>