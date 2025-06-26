<?php
  $conexion = mysql_connect("213.139.7.101", "hidrantes_user", "v4B3uu7F");
  mysql_select_db("hidrantes_db", $conexion);

  
  
  $sqls="SELECT h.*,m.municipio from hidrantes h inner join municipios m on (m.municipio_id=h.municipio_id)";
  $result = mysql_query($sqls, $conexion) or die(mysql_error());
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

while ($row = mysql_fetch_object($result))
{
	if ($row->geow!=0)
	{
	$txt.='<Placemark>
      <name>Codigo hidrante: '.$row->codigo.'</name>
	  <description>Municipio: '.$row->municipio.'</description>
      <styleUrl>#exampleStyleMap</styleUrl>
      <Point>
        <coordinates>'.$row->geow.','.$row->geon.',0</coordinates>
      </Point>
    </Placemark>';
	}
}
  $txt.='  </Document>
</kml>';
$file="map.kml";
$f = fopen($file,'w+');
fwrite($f,$txt,strlen($txt));
fclose($f); 



/*if ($conn_access = odbc_connect ( "hidrantes_bd", "", "")){
    echo "Conectado correctamente";
    $ssql = "select * from Consulta1";
    if($rs_access = odbc_exec ($conn_access, $ssql)){
       echo "La sentencia se ejecutó correctamente";
       while ($fila = odbc_fetch_object($rs_access)){
          //print_r($fila);
		  $queEmp = insertar($fila, $conexion);
		   //$queEmp = "INSERT INTO municipios (codigo, municipio) VALUES (".$fila->COD_MUNI.", '".$fila->NOMBRE."')";
		   mysql_query($queEmp, $conexion) or die(mysql_error());
	
       }
    }else{
       echo "Error al ejecutar la sentencia SQL";
    }
} else{
    echo "Error en la conexión con la base de datos";
} 
function insertar($fila, $conexion)
{
$tipo=0;
if ($fila->A==1)
$tipo= 1;
if ($fila->B==1)
$tipo= 2;
if ($fila->C==1)
$tipo= 3;
if ($fila->D==1)
$tipo= 4;
if ($fila->E==1)
$tipo= 5;

$diametro=0;
if ($fila->diam1==1)
$diametro= 1;
if ($fila->diam2==1)
$diametro= 2;
if ($fila->diam3==1)
$diametro= 3;

$racor=0;
if ($fila->TipoBar==1)
$racor= 1;
if ($fila->TipoRos==1)
$racor= 2;

$mun=0;
$municipio_id=0;
if (strlen($fila->Municipio)>0)
{
$mun=$fila->Municipio;

$sqls="SELECT municipio_id from municipios where codigo ='".$mun."' ";
$result = mysql_query($sqls, $conexion) or die(mysql_error());
$row = mysql_fetch_assoc($result);

$municipio_id=$row["municipio_id"];
}
$sql='insert into hidrantes(codigo, edificio, calle, utmx,utmy,municipio_id,obssituacion,tipohidrante_id, diametro_id, ';
$sql.='	racor_id, senyalizado, fecha,comprobo) values ';
$sql.="	('$fila->Codigo' ,'".utf8_encode(parse($fila->Edificio))."', '".utf8_encode(parse($fila->Calle))."', '$fila->X', '$fila->Y', $municipio_id, '".utf8_encode(parse($fila->Observ))."', $tipo,	$diametro, 	$racor, $fila->Sen, '$fila->Fechasel','".utf8_encode(parse($fila->Comprobo))."')";
echo $sql;
return $sql;
}
mysql_close($conexion);
function parse($txt)
{
return str_replace("'","\'",$txt);
}
*/
?>

