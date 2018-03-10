<doctype html>
<?php include("securepage/nfp_password_protect.php"); 
	include ("functions.php");
	opendb();
	$id=$_GET['id'];
	
	if (isset($_POST['NCbox']))
		$ncSelected=$_POST['NCbox'];
	else $ncSelected='';
	
	if(isset($_POST['save']))
		saveNCnotes($_POST);
?>


<head>
</head>

<body>
	<a style="color:red" href="unconfirmed.php?id=<?php echo $id ?>"><== Return to Welcome Committee Page</a>
	
		<div class="leftWidget" id="editNCNotes">
			<form name="ncNotesSelector" id="ncNotesSelector" action="ncNotes.php?id=<?php echo $id ?>" method="post">
				<?php echo NeighborhoodsCombobox('NCbox', 'ncNotesSelector', $ncSelected);
				
				?>
			</form>
		</div>
		
		<div>
			<form name="ncNotesForm" id="ncNotesForm" action="ncNotes.php?id=<?php echo $id ?>" method="post">
			<textarea  name="NCnotes" id="NCnotes" rows="10" cols="36" style="background-color:transparent" / >
				<?php if(isset($_POST['NCbox'])) 
					{	$sql=mysql_query("SELECT NCnotes FROM neighborhoods WHERE NCID=".$_POST['NCbox']);
						if($sql)
						{	$notes= mysql_fetch_array($sql);
							echo $notes['NCnotes'];
						}
						else echo 'sql query failed. '.mysql_error();
					}
					else if(isset($_POST['NCbox']) && $notes==null)
						echo ' *** No Notes yet for this NC ***';
					else
						echo '**** No NC Selected ****';
				?>
			</textarea>
			<input type="submit" value="save" />
		</div>
</body>
</html>

<?php
	function saveNCnotes($post)
	{
		$sql='UPDATE neighborhoods SET NCnotes='.$post['save'];
		$result=mysql_query($sql);
		
		
		
	}
?>