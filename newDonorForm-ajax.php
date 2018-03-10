<?php
	$fname=$_GET['fname'];
	$lname=$_GET['lname'];
	$email=$_GET['email'];
	$returnable='';

	
	include('functions.php');
	opendb();
	
	
//first, make sure the email address isn't in the database	
		if(trim($email)!='')
		{
			$emailsql="SELECT PreferredEmail FROM members WHERE PreferredEmail='".$email."'";
			$emailResult=mysql_query($emailsql);

	//if a row is returned, then the email IS in the database, so reject the new donor
			if(mysql_num_rows($emailResult) > 0)
			{
				//echo 'number of email that match: '.mysql_num_rows($emailResult);
				echo 'emailexists';
			}
		}

//if no rows are returned, check firstname, lastname	
		else
		{		$sql="SELECT * FROM members WHERE LastName LIKE '".$lname."' AND FirstName LIKE '".$fname."'";
				$result=mysql_query($sql);
				
	//if no rows are found that match the fname-lname, return ''
				if(mysql_num_rows($result)==0)
					//$returnable= 'numrows=0 : '.mysql_num_rows($result);
					//echo '0';
					return;
					
	// if there wasn't an error and at least one member has that name, list them
				else if($result && mysql_num_rows($result)>0)
				{
		
					$returnable= 'The following similar members are already in the database:
					
						';
					while($row=mysql_fetch_array($result))
					{
						$returnable.= $row['FirstName'].' '.$row['LastName'].'
						';
						$returnable.= $row['House'].' '.$row['StreetName'].'
						
						';
						if($row['Apt'] !='') $returnable.= 'Apt: '.$row['Apt'];
					
					}
					$returnable.='
			Save this member anyway?

			';
		//send back the result

					echo $returnable;
					
				}
				
				
		// if there was an error, send back the error announcement
				else if(!$result) 
					$returnable=mysql_error().'<br/>SQL: '.$sql;
		}		
				
?>
