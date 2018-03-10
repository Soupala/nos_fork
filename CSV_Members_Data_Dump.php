<?php 
	$output = fopen('php://output', 'w');
    fputcsv($output, array('MemberID', 'Source', 'DateEntered', 'LastName', 'FirstName', 'House', 'StreetName', 'Apt', 'City', 'State', 'Zip', 'NHoodId', 'Accepted','Reassign Notes','hideFromReassignerQueue','Preferred Email', 'Secondary Email', 'Preferred Phone', 'PasswordHash', 'status', 'members.notes', 'PUNotes', 'WCNotes', 'PrintName', 'DateLastUpdated', 'UpdaterID', 'latLong', 'RouteOrder','hasBag','WC Email','NC Email','neighborhoods.NHName','neighborhoods.NCID','neighborhoods.DistrictID','neighborhoods.notes','neighborhoods.polylines','neighborhoods.polygon','neighborhoods.center','neighborhoods.zoom','neighborhoods.NCnotes', 'neighborhoods.maxDonors','neighborhoods.historySwitch','neighborhoods.Private Notes', 'districts.DistrictName','disticts.DCID','districts.center','districts.polygon','districts.notes','districts.zoom'));
	date_default_timezone_set('America/Los_Angeles');
	$date=date("m/d/y");
	include("securepage/nfp_password_protect.php"); 
	include ("functions.php");
	include ("config.php");
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=CSV_Database_Dump_'.$date.'.csv');
	header('Pragma: no-cache');
	opendb();
	$rows = mysql_query('select *
from
   members
        inner join
    neighborhoods
        on members.NHoodID = neighborhoods.NHoodID
        inner join 
    districts
        on neighborhoods.DistrictID = districts.DistrictID

ORDER BY members.DateEntered ASC');
	while ($row = mysql_fetch_assoc($rows)) 
	fputcsv($output, $row);
?>