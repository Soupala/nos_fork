<?php
	opendb();
	$result=mysql_fetch_array(mysql_query("SELECT data FROM wholeproject WHERE miscName='welcomeEmail'"));
	$content=trim($result['data']);
	
	if(isset($_GET['uid']))
		$id=$_GET['uid'];
	
?>

<div>
	<form id="saveEmail" action="welcomeCommittee.php?id=<?php echo $id ?>&saveEmail=true&tool=email&panel=full" method="post" >
	<!-- THE BUTTONS DIV -->
		<div>
			<input type="submit" value="Save" />
			<!-- show as rendered html -->		
<!--	<input type="button" value="Preview"  onclick="document.getElementById('emailTextDiv').innerHTML=<?php //echo $content; ?>;" />
-->
			<input type="button" value="Preview"  onclick="renderEmail();"/>

			<!-- show as editable text -->		
			<input type="button" value="Edit Html"  onclick="editEmail();"/>
		</div>
	
	
	
		<!-- THE RENDERED HTML -->
		<div  id="renderedDiv" style=" display:block; overflow:auto; bottom:0px; height:88%;">
			<?php echo $content; ?>
		</div>
		<!-- THE EDITOR DIV -->
		<div  id="emailTextDiv" style="width:100%; height:88%; bottom:0px; display:none; ">
			<textarea id="emailText" name="emailText" rows="28" cols="150" ><?php echo trim($content) ?>
			</textarea>
		</div>
		
	</form>

</div>

<script type="text/javascript">

</script>

<?php
	function saveWelcomeEmail($content)
	{
		$result=mysql_query("INSERT INTO wholeProject data='".addslashes($content)."' WHERE miscName='welcomeEmail'");
		
		
	}
?>