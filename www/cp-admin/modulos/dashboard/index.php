<meta name="robots" content="NOODP"/>
<meta name="verify-v1" content="<?php echo googleverify?>" />
<link rel="shortcut icon" href="<?php echo imgpath; ?>favicon.ico"/>

<!-- Scripts -->
<script src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/jquery.js" type="text/javascript"></script>
<script src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/func.js" type="text/javascript"></script>

<script type="text/javascript"
    src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAifd5RVTGkq7HfZsaojesWH0zWveQE4kA&sensor=true">
</script>

<script>path = "<?php echo path; ?>";</script>

<style>
ul.breadcrumb {
  padding: 10px 16px;
  list-style: none;
  background-color: #eee;
}
ul.breadcrumb li {
  display: inline;
  font-size: 18px;
}
ul.breadcrumb li+li:before {
  padding: 8px;
  color: black;
  content: "/\00a0";
}
ul.breadcrumb li a {
  color: #0275d8;
  text-decoration: none;
}
ul.breadcrumb li a:hover {
  color: #01447e;
  text-decoration: underline;
}
</style>
<!-- CSS para pantalla -->
<link rel="stylesheet" type="text/css" href="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/estilos.css" />
<link rel="stylesheet" type="text/css" href="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/colorbox.css"/>
<link rel="stylesheet" type="text/css" href="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/jquery.dataTables.css" />
<link rel="stylesheet" type="text/css" href="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/demo_table.css" />
<link rel="stylesheet" type="text/css" href="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/demo_page.css" />

<!-- Light Box -->
<link rel="stylesheet" type="text/css" href="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/facebox.css" title="normal"/>
<script type="text/javascript" src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/facebox.js"></script>


<!-- BlockUI -->
<script type="text/javascript" src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/jquery.blockUI.js"></script>

</head>
<body>
<p><?php  ?></p>
<div id="wrap">

  <div id="header">
    <p>Plan de Salvaguarda para Ciudades Patrimonio</p>
  </div>
    
  <div id="foto">
    <a href="<?php echo $path; ?>"><img src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/images/logo-header.png" width="180" height="163" border="0" /></a> <div id="fotologos"><img src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/images/mac07-13.png" width="101" height="100" border="0" /> <img src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/images/bombergis.png" width="218" height="70" border="0"/><img src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/images/feder.png" width="269" height="70" border="0"/>
  </div>
</div>
    
	<?php
		echo '<div id="contenido">';
		echo '<div id="iconos">
      <ul class="breadcrumb">
        <li><a >Escritorio</a></li>
        <li><a href="http://prehidrantes.bomberostenerife.com/cp/hidrantes/">Gestion de Hidrantes</a></li>
        <li><a href="http://prehidrantes.bomberostenerife.com/cp/municipios/">Gestion de Municipios </a></li>
      </ul>
      <br> 
    <a href="'.$admin_path.'logout/" title="salir"><img src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/images/logout.gif" style="vertical-align:middle;"> Salir de la aplicación</a><p>&nbsp;</p></div>';
	?>

<?php
  defined ( '_VALID_MOS' ) or die ( 'Restricted access' );

  if (is_jefeparque()){
    $where .= "parque_id=".$_SESSION['HIDRANTES']['parque_id'];
    //$where .= " and estadocomentario ='EJG'";
    $where .= " and estadocomentario in('','EJG') and not comentario_id in(select distinct comentario_id_jg From  relacion_comentarios )";
    $lista_comentarios =$bd->get_all_by_filter_order("V_Comentarios",$where,"fecha desc");	
  } 

  if(is_consorcio())
	{
		$where .= "estadocomentario ='EJP'";
		$lista_comentarios =$bd->get_all_by_filter_order("V_Comentarios",$where,"fecha desc");	
  }
  if(!is_consorcio())
	{
    $where ="";
    if (is_jefeparque())
      $where .= "parque_id=3";
    $lista_hidrantes=$bd->get_all_by_filter_order("V_hidrantes",$where,"fecha desc limit 5");
  }

  if(is_consorcio())
	{
    $titulo=$str_lang['LANG_DASH_CARTAS1'];
    $where="WHERE p.fecharegistro is NULL or p.numeroregistro='' or p.interno=''";
      $titulo=$str_lang['LANG_DASH_CARTAS2'];
    
    $sql="select count(*) as n, p.* from V_Cartas p $where GROUP BY hidrante_id";
    $lista_cartas=$bd->get_all_sql($sql);

  }

?>

<div id="content">
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
    $('#example').dataTable({
        
    });
} );
</script>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="example" width="100%">
<thead>
<tr>
<th>Últimos Hidrantes</th>
</tr>
</thead>
<tbody>
<?php

if(!empty($lista_hidrantes)){
    foreach($lista_hidrantes as $hidrante){
        echo '<tr>';
        echo '<td>'.'('.convert_date($hidrante["fecha"]).')'.' Hidrante: '.$hidrante['codigo'].' Tipo de Hidrante: '.$hidrante['tipohidrante']. ' Municipio:'. $hidrante['municipio']. '</td>';
        echo '</tr>';
    }
}
?>
</tbody>
</table>
</div>

<?php defined( '_VALID_MOS' ) or die( 'Restricted access' ); ?>

</div> <!--contenido-->
<div id="clear"></div>

<div id="pie">
	<p>Copyright 2012: Plan Salvaguarda. <a href="http://www.cip.es/" target="_blank" >Dise&ntilde;o web y programaci&oacute;n web: Canarias Infopista</a></p>
</div>

</div>
</body>
</html>

