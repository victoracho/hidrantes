<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );

		  $sqls="SELECT h.*,m.municipio from hidrantes h inner join municipios m on (m.municipio_id=h.municipio_id)";
			$row = $bd->get_all_sql($sqls);
$txt='<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
<Document>
    <name>Highlighted Icon</name>
    <description>Place your mouse over the icon to see it display the new icon</description>
    <Style id="highlightPlacemark">
      <IconStyle>
        <Icon>
          <href>http://maps.google.com/mapfiles/kml/paddle/red-stars.png</href>
        </Icon>
      </IconStyle>
    </Style>
    <Style id="normalPlacemark">
      <IconStyle>
        <Icon>
          <href>http://maps.google.com/mapfiles/kml/paddle/wht-blank.png</href>
        </Icon>
      </IconStyle>
    </Style>
    <StyleMap id="exampleStyleMap">
      <Pair>
        <key>normal</key>
        <styleUrl>#normalPlacemark</styleUrl>
      </Pair>
      <Pair>
        <key>highlight</key>
        <styleUrl>#highlightPlacemark</styleUrl>
      </Pair>
    </StyleMap>
';	
$n=count($row);

for ($i=0;$i<$n;$i++)
{
	if ($row[$i]["geon"]!=0)
	{
	$txt.='<Placemark>
      <name>'.$row[$i]["codigo"].'</name>
	  <description>Municipio: '.$row[$i]["municipio"].'</description>
      <styleUrl>#exampleStyleMap</styleUrl>
      <Point>
        <coordinates>'.$row[$i]["geow"].','.$row[$i]["geon"].',0</coordinates>
      </Point>
    </Placemark>';
	}
}
  $txt.='  </Document>
</kml>';
$file=uploadpathadmin."map.kml";
$f = fopen($file,'w+');
fwrite($f,$txt,strlen($txt));
fclose($f); 

comprimir($file);
		
		
	?>
	
	<!-- Contenido -->
	<div id="content">

		
		<div style="margin:10px 0 20px 0px;"><a href="<?=uploadurladmin?>map.kmz">Descargar en google earth(archivo kmz)</a></div>
		<script src="http://www.google.com/jsapi?key=ABQIAAAA2bhnbM7NO4XToGwiNEYbTBQokgt5r13ea6F1jlnVyh63KSliDhTpH-WclqlF7rzfKjGVyOLI7-QC4Q"> </script>
	   <script type="text/javascript">
		  var ge;
		  google.load("earth", "1");

		  function init() {
			 google.earth.createInstance('map3d', initCB, failureCB);
		  }

		  function initCB(instance) {
			 ge = instance;
			 
			 var link = ge.createLink('');
	var href = 'http://hidrantes.bomberostenerife.com/cp-admin/upload/map.kml';
	link.setHref(href);
	var networkLink = ge.createNetworkLink('');
	networkLink.set(link, true, true); // Sets the link, refreshVisibility, and flyToView
	ge.getFeatures().appendChild(networkLink);
			 ge.getWindow().setVisibility(true);
		  }

		  function failureCB(errorCode) {
		  }

		  google.setOnLoadCallback(init);
		  
	   </script>
		
	</div>
	<div id="map3d" style="height: 600px; width: 90%;margin:10px 0 20px 90px;"></div>
	<!-- // Contenido -->