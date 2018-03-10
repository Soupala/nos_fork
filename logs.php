<!DOCTYPE html>
<?php

	include("securepage/nfp_password_protect.php"); 
	include('functions.php');
	opendb();


	
?>

<head>
	
	<link rel="stylesheet" type="text/css" href="memberStyles.css" />
</head>

<body>

	
		<div class="widget" id="bugsTextDiv" style="width:100%; height:100%; bottom:0px; left:0px; ">
				<?php 
				
				$logtype='';
				
				$query=mysql_query("SELECT * FROM log ORDER BY dateTime DESC");
				while($row=mysql_fetch_array($query))
				{
					$logtype='';
					if($row['signup']==1) $logtype=" signup ";
					if($row['memberTable']==1) $logtype.=" memberTable ";
					if($row['addNewLogTypeHere']==1) $logtype.=" addNewLogTypeHere";
					
					
					echo '<hr/>
							Log Time: '.$row['dateTime'].'<br/>
							Log Type: '.$logtype.'<br/>
							Log Data: <br/>
							'.trim($row['changeMade']).'
						<hr/>';
					
				}
				
				
				
				
				
				
				?>
		</div>
		


</body>
</html>


