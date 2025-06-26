<?php

$datos=$bd->get_all_by_id("hidrantes","hidrante_id",$id);

$objUTM = convertirCoordenadaUTM($datos['utmx'],$datos['utmy']);
									
if ((strlen($datos['geon'])==0)||(strlen($datos['geow'])==0))
{
	$datos['geon']=number_format($objUTM->Lat(),5);
	$datos['geow']=number_format($objUTM->Long(),5);
}
$lat=$datos['geon'];
$lon=$datos['geow'];

$opcion = $_GET["op"];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
<title>Google Map</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

<link rel="shortcut icon" href="http://hidrantes.bomberostenerife.com/images/favicon.ico"/>
<link rel="stylesheet" type="text/css" href="http://hidrantes.bomberostenerife.com/cp-admin/css/style.css" title="normal"/>
<link rel="stylesheet" href="http://hidrantes.bomberostenerife.com/cp-admin/css/thickbox.css" type="text/css" media="screen" />
 <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAA2bhnbM7NO4XToGwiNEYbTBQokgt5r13ea6F1jlnVyh63KSliDhTpH-WclqlF7rzfKjGVyOLI7-QC4Q" type="text/javascript"></script>

<script src="http://hidrantes.bomberostenerife.com/cp-admin/scripts/jquery.js" type="text/javascript"></script>
<script type="text/javascript" src="http://hidrantes.bomberostenerife.com/cp-admin/scripts/thickbox.js"></script>



<script type="text/javascript">
function initialize2(lat,lon) {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map_canvas2"),{size: new GSize(585,500)});
		map.addControl(new GMapTypeControl());
        var latlng = new GLatLng(lat,lon);
        map.addOverlay(new GMarker(latlng));
   	    map.setMapType(G_HYBRID_MAP);
		map.addControl(new GSmallMapControl());
		//map.setCenter(new GLatLng(lat+0.002226, lon-0.004301), 17);
		map.setCenter(new GLatLng(lat, lon), 17);

      }
    }

 function initializestreetview2(lat,lon) {
      var fenwayPark = new GLatLng(lat,lon);
      panoramaOptions = { latlng:fenwayPark };
      myPano = new GStreetviewPanorama(document.getElementById("map_canvas2"), panoramaOptions);
      GEvent.addListener(myPano, "error", handleNoFlash);
    }

	
	$(function() {
		var lat=<?=$lat?>;
		var lon=<?=$lon?>;
		var opcion=<?=$opcion?>;
		if (opcion==1)
		{
			initialize2(lat,lon);
		}else{
			initializestreetview2(lat,lon);
			}
	});
</script>

</head>

<body >
<div id="contentmap2">
	<div id="map_canvas2" ></div>
</div>
</body>
</html>