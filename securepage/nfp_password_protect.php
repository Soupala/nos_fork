<?php

###############################################################
# Page Password Protect 2.13
###############################################################
# Visit http://www.zubrag.com/scripts/ for updates
###############################################################
#
# Usage:
# Set usernames / passwords below between SETTINGS START and SETTINGS END.
# Open it in browser with "help" parameter to get the code
# to add to all files being protected.
#    Example: password_protect.php?help
# Include protection string which it gave you into every file that needs to be protected
#
# Add following HTML code to your page where you want to have logout link
# <a href="http://www.example.com/path/to/protected/page.php?logout=1">Logout</a>
#
###############################################################

/*
-------------------------------------------------------------------
SAMPLE if you only want to request login and password on login form.
Each row represents different user.

$LOGIN_INFORMATION = array(
  'zubrag' => 'root',
  'test' => 'testpass',
  'admin' => 'passwd'
);

--------------------------------------------------------------------
SAMPLE if you only want to request only password on login form.
Note: only passwords are listed

$LOGIN_INFORMATION = array(
  'root',
  'testpass',
  'passwd'
);

--------------------------------------------------------------------
*/

##################################################################
#  SETTINGS START
##################################################################
	
	//between here and disconnecting from the database was added by Matthew Swindle in 2011
	//it pulls the array of valid login credentials from a mysql database
	// don't blame the original author if this gets buggy :)
	
	//connect to the config file to pull database login credentials
	//and cookie name (based on city) extension
	// without this, there is a problem with cookies working oddly across food projects
	if(file_exists('config.php'))
	{	//echo 'config.php exists';
		include ('config.php');
	}
	else if(file_exists('../config.php'))
	{	//echo '../config.php exists';
		include('../config.php');
	}
		
	//populate the user/password array from the database
		// variables that are to be set to the specific mySQL database
			$dbhost=getDbHost();	//"localhost";
			$dbuser=getDbUser();	//"neighborhoodfoodproject";
			$dbpw=getDBPw();		//"Winter2012!";
			$db=getDb();			//"nfp_sandbox";
			
	//
	//  connect to the database 
	// 
			$con = mysql_connect($dbhost, $dbuser, $dbpw);
			if (!$con)
				{die('Could not connect: '. mysql_error()) ;}
			mysql_select_db($db, $con);



			
	//determine NCname from database
			//$result=mysql_query("SELECT Email,Password from pw ;");
			$result=mysql_query("SELECT PreferredEmail,Password from members Where PreferredEmail is not NULL ");
// print_r($result);


	// while($row=mysql_fetch_array($result))
	// {
		// print_r($row);
		// echo '<br/>';
	// }



	// create the array variable
$LOGIN_INFORMATION = array();
	
	//fetch each row from the table and place it in the array	
			while ($row=mysql_fetch_array($result))
			{	$LOGIN_INFORMATION[$row['PreferredEmail']]=$row['Password'];	
			//	echo $row['PreferredEmail'].' '.$row['Password'].'<br/>';
			}


mysql_close($con);
//	disconnected from database
//










// Add login/password pairs below, like described above
// NOTE: all rows except last must have comma "," at the end of line
/* $LOGIN_INFORMATION = array(
	'adminUser@example.com' =>'ashlandpw',
	'Donor@example.com' => 'donorpw',
	'NCuser@example.com' => 'ncpw',
	'DCuser@example.com' => 'dcpw',
	);
*/










// request login? true - show login and password boxes, false - password box only
define('USE_USERNAME', true);

// User will be redirected to this page after logout
define('LOGOUT_URL', '../index.php');

// time out after NN minutes of inactivity. Set to 0 to not timeout
define('TIMEOUT_MINUTES', 1200);

// This parameter is only useful when TIMEOUT_MINUTES is not zero
// true - timeout time from last activity, false - timeout time from login
define('TIMEOUT_CHECK_ACTIVITY', true);

##################################################################
#  SETTINGS END
##################################################################


///////////////////////////////////////////////////////
// do not change code below
///////////////////////////////////////////////////////

// show usage example
if(isset($_GET['help'])) {
  die('Include following code into every page you would like to protect, at the very beginning (first line):<br/>&lt;?php include("' . str_replace('\\','\\\\',__FILE__) . '"); ?&gt;');
}

// timeout in seconds
$timeout = (TIMEOUT_MINUTES == 0 ? 0 : time() + TIMEOUT_MINUTES * 60);

// logout?
if(isset($_GET['logout'])) {
  // setcookie("verify".getFpCity(), '', $timeout, '/'); // clear password;
  // setcookie("nfpid".getFpCity(),'',$timeout,'/');	//clear the id cookie
   setcookie("verify".getFpCity(), '', -3600, '/'); // clear password;
  setcookie("nfpid".getFpCity(),'', -3600,'/');	//clear the id cookie
  header('Location: ' . LOGOUT_URL);
  exit(); 
}

if(!function_exists('showLoginPasswordProtect')) {

// show login form
function showLoginPasswordProtect($error_msg) {
?>

<div class="mainLogin" id="mainLogin" style="top: 0px; left: 0px; right: 0px; bottom: 0px; width: auto; height: auto; padding: 10px;">
  <form method="post">
  <div class="mainLoginCenter" style="font-family: Arial, Serif; top: 0px; width: 1024px; height: 700px; background-image: url(images/login1024.jpg); margin-left: auto; margin-right: auto; border: 1px green; border-radius: 10px;">
			<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
				   
	 <center>   <font color="#ff1316"><?php echo $error_msg; ?></font></center>
	 <br />
	<center>
		<?php if (USE_USERNAME) echo ' <font color="#fff" size="14px">Email</font><br /><input type="input" name="access_login" /><br /><font color="#fff" size="14px">Password</font><br />'; ?></center>
	   <center> <input type="password" name="access_password" /><p></p><input type="submit" name="Submit" value="Submit" /></center>
  </form>
  <br />
  <center><a style="font-size:9px; color: #fff; font-family: Arial, Serif;" href="http://www.zubrag.com/scripts/password-protect.php" title="Download Password Protector">Powered by Password Protect</a></center>
  </div>
  </div>
   






<?php
  // stop at this point
  die();
}
}

			// user provided password
			if (isset($_POST['access_password'])) {

			  $login = isset($_POST['access_login']) ? $_POST['access_login'] : '';
			  $pass = $_POST['access_password'];
//echo '<br/> $login: '.$login;
//echo '<br/><hr/>from ashland_password_protect.php line 190<br/>$pass:'.$pass.'<br/>';
	$pass=md5($pass);
//echo 'md5($pass):'.$pass.'<br/> ';			  
			  if (!USE_USERNAME && !in_array($pass, $LOGIN_INFORMATION)
			  || (USE_USERNAME && ( !array_key_exists($login, $LOGIN_INFORMATION) || $LOGIN_INFORMATION[$login] != $pass ) )
			  ) {
				showLoginPasswordProtect("Incorrect password.");
			  }
			  else {
				// set cookie if password was validated
				setcookie("verify".getFpCity(), md5($login.'%'.$pass), $timeout, '/');		//set the username cookie
				setcookie("nfpid".getFpCity(),$login,$timeout,'/');							//set the id cookie
				// Some programs (like Form1 Bilder) check $_POST array to see if parameters passed
				// So need to clear password protector variables
				unset($_POST['access_login']);
				unset($_POST['access_password']);
				unset($_POST['Submit']);
			  }




}

else {

  // check if password cookie is set
  if (!isset($_COOKIE['verify'.getFpCity()])) {
    showLoginPasswordProtect("");
  }

  // check if cookie is good
  $found = false;
  foreach($LOGIN_INFORMATION as $key=>$val) {
    $lp = (USE_USERNAME ? $key : '') .'%'.$val;
    if ($_COOKIE['verify'.getFpCity()] == md5($lp)) {
      {$found = true;
      $uname=$key;
  }
      // prolong timeout
      if (TIMEOUT_CHECK_ACTIVITY) {
        setcookie("verify".getFpCity(), md5($lp), $timeout, '/');
      }
      break;
    }
  }
  if (!$found) {
    showLoginPasswordProtect("");
  }

}



?>
