<?php
echo '
<script type="text/javascript">
	//make the map		
		var latlng = new google.maps.LatLng'. $_GET['location'] .';
		var options = {
			zoom: 17,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.HYBRID
		}
		var map = new google.maps.Map(document.getElementById("popupMap"), options);
		
		var marker = new google.maps.Marker(
			{
			  position: latlng,
			  map: map,
			  draggable:true
			});
		

		google.maps.event.addListener(marker, "dragend", function()
			{
				document.getElementById("popupLatLong").value="("+marker.getPosition.lat()+", "+marker.getPosition.lng()+")";
			});

</script>
';
?>
