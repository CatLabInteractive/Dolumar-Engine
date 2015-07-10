<?php 
	$width = 75;
	$height = 75;

	$px = ceil (MAXMAPSTRAAL / $width);
	$py = ceil (MAXMAPSTRAAL / $height);

?>

<html>
	<head>
		<title>World Map</title>
		
		<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.4.4/leaflet.css" />
		<!--[if lte IE 8]>
		    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.4.4/leaflet.ie.css" />
		<![endif]-->

		<script src="http://cdn.leafletjs.com/leaflet-0.4.4/leaflet.js"></script>

		<style type="text/css">
			*
			{
				margin: 0px;
				padding: 0px;
			}
		</style>
	</head>
	<body>	
		<div id="map" style="width: 100%; height: 100%;"></div>

		<script>
			var map = new L.Map('map', {
			    center: [90, 0],
			    zoom: 1,
			    layers: new L.TileLayer('<?=ABSOLUTE_URL?>image/world/?x={x}&y={y}&width=<?=$width?>&height=<?=$height?>&zoom={z}', {
			    	tileSize: <?=$width?>,
			    	minZoom: 1,
			    	maxZoom: 18
			    })
			});
		</script>
	</body>
</html>
