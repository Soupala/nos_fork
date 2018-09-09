<?php

	echo "geocoding <br>";
	//include config and functions to connect ot database
	include('config.php');
	include('functions.php');
	//function for retrieving location
	function getLocation($address)
	{
		$url = "http://maps.google.com/maps/api/geocode/json?&address=".urlencode($address);

		$result_json = file_get_contents($url);
		$result = json_decode($result_json);
		if($result->status == "OK")
			return $result->results[0]->geometry->location;
		else
			return false;
	}
	//select all records from members table where member is geocoded
	$con = opendb();
	$result = $con->query("SELECT * FROM members WHERE latLong = '(42.938696,-122.146522)'");
	//iterate over returned records
	//geocode each member
	while($member = $result->fetch_assoc())
	{
		if($member['House'] != "" && $member['StreetName'] != "" && $member['City'] != "" && $member['State'] != "")
		{
			//get location of member
			$address = $member['House']." ". $member['StreetName']." ".$member['City']." ".$member['State'];
			$location = getLocation($address);
			$lat = $location->lat;
			$lng = $location->lng;

			//insert location into database
			$con->query("UPDATE members SET latLong = '(".$lat.",".$lng.")' WHERE MemberID = '".$member['MemberID']."'");
		}
	}
	$con->close();
?>
