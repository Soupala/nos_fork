<?php 
	$output = fopen('php://output', 'w');
	fputcsv($output, array('Neighborhood Name', 'District Name'));
	date_default_timezone_set('America/Los_Angeles');
	$date=date("m/d/y");
	include("securepage/nfp_password_protect.php"); 
	include ("functions.php");
	include ("config.php");
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=NH_District_List_'.$date.'.csv');
	header('Pragma: no-cache');
	opendb();
	$rows = mysql_query('SELECT NHName,DistrictName from neighborhoods,districts WHERE neighborhoods.DistrictID=districts.DistrictID') ;
	while ($row = mysql_fetch_assoc($rows)) 
	fputcsv($output, $row);
?>