<!DOCTYPE html>
<html>
<?php

	include("securepage/nfp_password_protect.php"); 
	include('functions.php');
	include("config.php");
	opendb();
	
	
?>
<head>
	
	
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	
	
</head>

<body>
<?php

$size = count($_POST['pickedUp']);

$i = 0;
while ($i < $size) {
		$pickedUp=$_POST['pickedUp'][$i];
		$recordID=$_POST['recordID'][$i];
        
        $query = "UPDATE pickupHistory SET pickedUp='$pickedUp' WHERE recordID='$recordID' ";
        mysql_query($query) or die ("Error in query: $query");
        echo ' <br /><em style="color: green; font-size: 22px; font-weight: bolder;">Update for History Record ID '.$recordID.' was successful!</em> ';
        ++$i;
}
?>

</body>
</html>