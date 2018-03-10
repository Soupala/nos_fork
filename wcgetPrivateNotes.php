<?php
	include("securepage/nfp_password_protect.php");
    include("config.php");	
	include("functions.php");
	opendb();
	
$NHoodID=$_GET['NHoodID'];


$sql="SELECT * FROM neighborhoods WHERE NHoodID = '".$NHoodID."'";

$result = mysql_query($sql);

echo "<h2 style='padding-left: 2px; padding-top: 10px; padding-bottom: 10px; color: green;'>Welcome Committee<br />Private Notes for this Neighborhood</h2> ";

echo "<table width='50%' border='1px' cellpadding='15px' bgcolor='#aef871' style='color: #272727; font-size: 16px;'>
<tr>
<th>Neighborhood ID</th>
<th>Neighborhood Name</th>
<th>NCID</th>
<th>WC Private Notes</th>
</tr>";

while($row = mysql_fetch_array($result))
  {
  echo "<form name='privateNotesUpdating' action='wcPrivateNotes-post.php?' method='post' > ";
  echo "<tr>";
  echo "<td>".$row['NHoodID']."<br /><input type='hidden' name='NHoodID' value='" . $row['NHoodID'] . "' /></td>";
  echo "<td>" . $row['NHName'] . "<input type='hidden' name='NHName' value='" . $row['NHName'] . "' /></td>";
  echo "<td>" . $row['NCID'] . "</td>";
  echo "<td>
		<textarea  name='privateNotes' rows='10' cols='36' style='background-color:white; border: 1px solid #bbb1a7; border-radius: 1px;' / >".$row['privateNotes']."</textarea><br />
  </td>";
  echo "</tr> ";
  echo "<tr>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td><input type='submit' value='Save' align='center' style='padding: 5px;' /></td> ";
		echo "</tr>";
		echo "</form>";
  }
echo "</table>";

mysql_close($con);
?>