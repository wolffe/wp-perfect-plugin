<?php
function gmaps_header() {
	?>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<?php
}

// Main function to generate google map
function mapme($attr) {
	// default atts
	$attr = shortcode_atts(array(	
		'lat'   => '0', 
		'lon'    => '0',
		'id' => 'map',
		'z' => '15',
		'w' => '100%',
		'h' => '300',
		'maptype' => 'TERRAIN',
		'address' => '',
		'kml' => '',
		'marker' => '',
		'markerimage' => '',
		'traffic' => 'no',
		'infowindow' => ''
	), $attr);

	//add_action('wp_head', 'gmaps_header');
	$returnme = '
		<div class="w3p-google-maps" id="' .$attr['id'] . '" style="width:' . $attr['w'] . '; height:' . $attr['h'] . 'px;"></div>
		<script>
		var latlng = new google.maps.LatLng(' . $attr['lat'] . ', ' . $attr['lon'] . ');
		var myOptions = {
			zoom: ' . $attr['z'] . ',
			center: latlng,
			mapTypeId: google.maps.MapTypeId.' . $attr['maptype'] . ',
			streetViewControl: false
		};
		var ' . $attr['id'] . ' = new google.maps.Map(document.getElementById("' . $attr['id'] . '"),
		myOptions);';

		//kml
		if($attr['kml'] != '') {
			//Wordpress converts "&" into "&#038;", so converting those back
			$thiskml = str_replace("&#038;","&",$attr['kml']);		
			$returnme .= '
			var kmllayer = new google.maps.KmlLayer(\''.$thiskml.'\');
			kmllayer.setMap('.$attr['id'].');';
		}

		//traffic
		if($attr['traffic'] == 'yes') {
			$returnme .= '
			var trafficLayer = new google.maps.TrafficLayer();
			trafficLayer.setMap('.$attr['id'].');';
		}

		//address
		if($attr['address'] != '') {
			$returnme .= '
		    var geocoder_'.$attr['id'].' = new google.maps.Geocoder();
			var address = \''.$attr['address'].'\';
			geocoder_'.$attr['id'].'.geocode({ \'address\': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					'.$attr['id'].'.setCenter(results[0].geometry.location);';

					if($attr['marker'] !='') {
						//add custom image
						if($attr['markerimage'] !='') {
							$returnme .= 'var image = "'. $attr['markerimage'] .'";';
						}
						$returnme .= '
						var marker = new google.maps.Marker({
							map: ' . $attr['id'] . ', ';
							if ($attr['markerimage'] !='')
							{
								$returnme .= 'icon: image,';
							}
						$returnme .= '
							position: ' . $attr['id'] . '.getCenter()
						});';

						//infowindow
						if($attr['infowindow'] != '') {
							//first convert and decode html chars
							$thiscontent = htmlspecialchars_decode($attr['infowindow']);
							$returnme .= '
							var contentString = \'' . $thiscontent . '\';
							var infowindow = new google.maps.InfoWindow({
								content: contentString
							});
										
							google.maps.event.addListener(marker, \'click\', function() {
							  infowindow.open(' . $attr['id'] . ',marker);
							});';
						}
					}
			$returnme .= '
				} else {
				alert("Geocode was not successful for the following reason: " + status);
			}
			});';
		}

		//marker: show if address is not specified
		if ($attr['marker'] != '' && $attr['address'] == '') {
			//add custom image
			if ($attr['markerimage'] !='') {
				$returnme .= 'var image = "'. $attr['markerimage'] .'";';
			}

			$returnme .= '
				var marker = new google.maps.Marker({
				map: ' . $attr['id'] . ', ';
				if ($attr['markerimage'] !='') {
					$returnme .= 'icon: image,';
				}
			$returnme .= '
				position: ' . $attr['id'] . '.getCenter()
			});';

			//infowindow
			if($attr['infowindow'] != '') {
				$returnme .= '
				var contentString = \'' . $attr['infowindow'] . '\';

				var infowindow = new google.maps.InfoWindow({
					content: contentString
				});
							
				google.maps.event.addListener(marker, \'click\', function() {
				  infowindow.open(' . $attr['id'] . ',marker);
				});
				infowindow.open(map1,marker);';
			}
		}

	$returnme .= '</script>';

	return $returnme;
}
add_shortcode('map', 'mapme');
?>
