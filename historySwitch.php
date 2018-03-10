<!DOCTYPE html>
<html>
<?php
	include("securepage/nfp_password_protect.php");
    include("config.php");	
	include("functions.php");
	opendb();
	if(isset($_GET['NHoodID']))
	$NHoodID=$_GET['NHoodID'];
?>
<script>
function getHistorySwitch(str)
{
if (str=="")
  {
  document.getElementById("txtHint").innerHTML="";
  return;
  } 
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","getHistorySwitch.php?NHoodID="+str,true);
xmlhttp.send();
}
</script>


<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Donation History</title>		
<link rel="stylesheet" type="text/css" href="css/style.css" />


</head>

<body>

<div>
	<h2 style="padding-left: 2px; padding-top: 10px; padding-bottom: 10px; color: green;">Turn On/Off Pickup History<br /> for a Neighborhood</h2>
	
		<form name='neighborhoodswitch' action='getHistorySwitch.php?NHoodID=<?php echo $NHoodID ?>' method='get' > 
<?php 	
				//allow user to select a neighborhood
					
				echo neighborhoodswitch('NHoodID');
				
				echo "<input type='submit' value='SELECT' style='color: green; font-weight: bolder;'> ";
?>
		</form>

</div>


	




</body>
</html>