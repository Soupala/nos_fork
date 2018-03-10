<?php
//	This is the "Reset A Password" div of the editMember page
//	is should have passed in to it:
//		id		(the id number of the person whose pw is being changed, not the person doing the changing)
//		
//		pw1		these two are the input from the password <input> boxes contained within this document. 
//		pw2		The repetition ensures correct spelling. If you typed it in wrong, you'd never get in. :)
//				
//	This page calls itself when the user clicks <submit> and POSTs the hashed password and the username
//	It then writes the incoming hashed pw to the password field of the member's ID in the database
//
//	This page sits in a little iframe in the editMembers.php page so it can call itself and compute its own input
//	
//
?>
<!DOCTYPE html>
<head>	
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script type="text/javascript" src="js/MD5.js"> </script>
</head>


<body style="background-color:fff; text-align:center;">

		<?php
		if (isset($_GET['fdid']))
			$fdid=$_GET['fdid'];
		else $fdid=$_POST['fdid'];
		
// if ($id) echo '$id:'.$id.'<br/>';
// else echo '$id didn\'t make it to resetpw.php';
		
		include("functions.php");
//first check if passwords boxes have been set
//if so, check if they match
//	if they match, hash the pw and insert into db
//	if not, return to form with error message stating they do not match
			if (isset($_POST["pw1"]) && isset($_POST["pw2"]))
				{

					$pw1=$_POST["pw1"];
					$pw2=$_POST["pw2"];
echo '<script type="text/javascript">
		alert("password boxes were filled with 
		pw1:'.$pw1.' 
		pw2:'.$pw2.'
			");
		</script>';
					if ($pw1 == $pw2)
						{
						//	//hash pw and insert to db
						//	$pw=md5($pw1);
							
							opendb();
							$result=mysql_query("UPDATE members SET Password='".$pw1."' where MemberID=".$fdid);
							
							if ($result)
								echo '<p style="color:lime">update successful</p>';
								else echo '<p style="color:red">COULD NOT UPDATE DATABASE</p><hr/>'.mysql_errno($con) . ': ' . mysql_error($con) . '<hr/>';
							
							


							
							
							
							
							
							echo '<p style="color:green;">New Password Set</p>';
						}
					else
						//return to password reset form with failure notice
						echo '<p style="color:red">The passwords do NOT match</p><br/>Please Try again<br/>';


				}

		//// if no password boxes have been filled, present the password boxes
			//else
			//{
				//echo '<form method="post" >	';
				//echo '<form name="NewPW" action="LoggedIn/resetpw();">	';
				echo '<form name="NewPW" method="post" action="resetpw.php?fdid='.$fdid.'">	';
					echo '<p style="color: #272727; font-size: 16px;">Enter Password Twice</p><br/>
						<input type="password" name="pw1"/>	<br/><br/>
						<input type="password" name="pw2" /> <br/><br/>
						<input type="hidden" name="fdid" value='.$fdid.' />
						';
					//echo '<input type="submit" name="Submit" value="Submit" />	';
					echo ' <input type="button" value="Submit" onclick="hashNsend()" />';
				echo '</form>	';
			//}	



		?>

		<script type="text/javascript">
			function hashNsend()
			{
				var first=document.NewPW.pw1.value;
				var second=document.NewPW.pw2.value;
				var hash1=MD5(first);
				var hash2=MD5(second);
				document.NewPW.pw1.value=hash1;
				document.NewPW.pw2.value=hash2;
				document.NewPW.submit();
			}
		
		</script>
		
		
<!--	</div>
-->
	

	
	
	
	
	
</body>
</html>