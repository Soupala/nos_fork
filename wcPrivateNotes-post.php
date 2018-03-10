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
<br /><br />
<?php

		$privateNotes=$_POST['privateNotes'];
		$NHName=$_POST['NHName'];
		$NHoodID=$_POST['NHoodID'];
        
        $query = "UPDATE neighborhoods SET privateNotes='$privateNotes' WHERE NHoodID='$NHoodID' ";
        mysql_query($query) or die ("Error in query: $query");
        echo ' <h2 style="color: green; padding:15px;">Update to '.$NHName.' Neighborhood was Successful. <br /><br />Close out this tab and CARRY ON!</h2> ';

?>

</body>
</html>