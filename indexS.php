<?php include("securepage/nfp_password_protect.php"); ?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Logged into Neighborhood Organizing System of the Neighborhood Food Project</title>
	<meta name="description" content="Neighborhood Organizing System">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/memberStyles.css" />
	<link rel="stylesheet" type="text/css" href="css/headerNav.css" />
	<script type="text/javascript" src="js/swapdivs.js"> </script>
	<script type="text/javascript" src="js/toggletools.js"> </script>
	<script src="js/libs/modernizr-2.5.3.min.js"></script>


</head>

<body  class="indexS" >
  <!--[if lt IE 7]><p class=chromeframe>Yikes! Your browser is <em>ancient.</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->


	<?php
		include('config.php');
		include("functions.php");
		opendb();
//
// set up member info variables
		if(isset($login))
			$email=$login;
		else if(isset($_COOKIE['nfpid'.getFPCity()])) 
			$email=$_COOKIE['nfpid'.getFpCity()];
		else 
		{	
			echo '<p style="color:red; text-align:center; top:100px; margin-left:100px; margin-right:100px; margin-top:100px; margin-bottom:100px;"> Your session timed out. Click <a href="securepage/nfp_password_protect.php?logout=1">here</a> to log back in</p> ';
			die();
		}
		

		$sql="SELECT * FROM members WHERE PreferredEmail = '".$email."'";
		$query=mysql_query($sql);
		

		$theArr=mysql_fetch_array($query);
		$uid=$theArr["MemberID"];
		$fdid=$theArr["MemberID"];
		$fname=$theArr["FirstName"];
		$printName=$theArr["PrintName"];
	
		$groups=getUserGroups($uid);

// SET UP $role SO permissionbuttons.php CAN ASSIGN THE CORRECT $homepage
		 if ($groups['ADMIN'])
			 $role='ADMIN';
		else if ($groups['DC'])
			$role='DC';
		else if ($groups['NC'])
			$role='NC';
	 
		mysql_close($con);
	//	disconnected from database
	//
	
	?>
	
	
<!--	//////////////////////////////////
		//	set up the Nav			//
		//////////////////////////////////
-->	
	

<!--	<div class="permissionButtons" > -->			
	<?php //echo 'Hello, '.$email; ?>


	<?php include('permissionButtons.php'); ?>
		
	
	
	
	<iframe src="<?php echo $homepage ?>" class="ContentFrame" name="ContentFrame" style="top: 0px; border: 0px;" >
	<p>You are using a browser that does not support iframes.</p>
	</iframe>	
