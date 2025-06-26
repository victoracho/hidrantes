<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
<title>Plan salvaguarda </title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="author" content=""/>
<meta name="keywords" content=""/>
<meta name="description" content=""/>
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
#barra a {
  color: #fff !important;
  text-decoration: none !important;
}
#barra {
  width: 977px !important;
} 
</style>
<!-- CSS para pantalla -->
<link rel="stylesheet" type="text/css" href="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/estilos.css" />
<link rel="stylesheet" type="text/css" href="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/colorbox.css"/>
<link rel="stylesheet" type="text/css" href="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/jquery.dataTables.css" />
<link rel="stylesheet" type="text/css" href="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/demo_table.css" />
<link rel="stylesheet" type="text/css" href="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/demo_page.css" />
</head>
<body>
<p><?php  ?></p>
<div id="wrap">

  <div id="header">
    <p>Plan de Salvaguarda para Ciudades Patrimonio</p>
  </div>
  <div id="foto">
    <a href="<?php echo $path; ?>">
      <img src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/images/logo-header.png" width="180" height="163" border="0" />
    </a> 
    <div id="fotologos">
      <img src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/images/mac07-13.png" width="101" height="100" border="0" /> 
      <img src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/images/bombergis.png" width="218" height="70" border="0"/>
      <img src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/images/feder.png" width="269" height="70" border="0"/>
    </div>
  </div>
  <div id="contenido">
    <div id="iconos"> 
    <ul class="breadcrumb"> 
<?php
    $f_status=strip_tags($_GET['status']);	
    switch ($modulo){
      case 'dashboard':
        if (!is_jefeguardia()){
          echo'<li><a href="'.$admin_path.'dashboard/" '.getSelectedNav($modulo,"dashboard").'><span>'.$str_lang['LANG_HEADER_TAB_DASH'].'</span></a></li>';
          echo'<li><a href="'.$admin_path.'hidrantes/" '.getSelectedNav($modulo,"hidrantes").'><span>'.$str_lang['LANG_HEADER_TAB_HIDRANTES'].'</span></a></li>';
          echo '<li><a href="'.$admin_path.'municipios/" '.getSelectedNav($modulo,"municipios").'><span>'.$str_lang['LANG_HEADER_TAB_MUNICIPIOS'].'</span></a></li>';		
          if (is_admin())
          {
            echo '<li><a href="'.$admin_path.'usuarios/" '.getSelectedNav($modulo,"usuarios").'><span>'.$str_lang['LANG_HEADER_TAB_USUARIOS'].'</span></a></li>';		
          }							 
        }						
        break;
        case 'hidrantes':
          if (!is_jefeguardia())							
            echo'<li><a href="'.$admin_path.'dashboard/" '.getSelectedNav($modulo,"dashboard").'><span>'.$str_lang['LANG_HEADER_TAB_DASH'].'</span></a></li>';
            echo'<li><a href="'.$admin_path.'hidrantes/" '.getSelectedNav($modulo,"hidrantes").'><span>'.$str_lang['LANG_HEADER_TAB_HIDRANTES'].'</span></a>';
                  echo '</li>';
            echo '<li><a href="'.$admin_path.'municipios/" '.getSelectedNav($modulo,"municipios").'><span>'.$str_lang['LANG_HEADER_TAB_MUNICIPIOS'].'</span></a></li>';		
            if (is_admin() )
          {
            echo '<li><a href="'.$admin_path.'usuarios/" '.getSelectedNav($modulo,"usuarios").'><span>'.$str_lang['LANG_HEADER_TAB_USUARIOS'].'</span></a></li>';		
          }	
        break;
        case 'municipios':
          if (!is_jefeguardia())							
              echo'<li><a href="'.$admin_path.'dashboard/" '.getSelectedNav($modulo,"dashboard").'><span>'.$str_lang['LANG_HEADER_TAB_DASH'].'</span></a></li>';
          echo'<li><a href="'.$admin_path.'hidrantes/" '.getSelectedNav($modulo,"hidrantes").'><span>'.$str_lang['LANG_HEADER_TAB_HIDRANTES'].'</span></a></li>';
          echo '<li><a href="'.$admin_path.'municipios/" '.getSelectedNav($modulo,"municipios").'><span>'.$str_lang['LANG_HEADER_TAB_MUNICIPIOS'].'</span></a></li>';		
            if (is_admin() )
            {
              echo '<li><a href="'.$admin_path.'usuarios/" '.getSelectedNav($modulo,"usuarios").'><span>'.$str_lang['LANG_HEADER_TAB_USUARIOS'].'</span></a></li>';		
            }
        break;				
        case 'usuarios':
          if (!is_jefeguardia())							
            echo'<li><a href="'.$admin_path.'dashboard/" '.getSelectedNav($modulo,"dashboard").'><span>'.$str_lang['LANG_HEADER_TAB_DASH'].'</span></a></li>';
            echo'<li><a href="'.$admin_path.'hidrantes/" '.getSelectedNav($modulo,"hidrantes").'><span>'.$str_lang['LANG_HEADER_TAB_HIDRANTES'].'</span></a></li>';
            echo '<li><a href="'.$admin_path.'municipios/" '.getSelectedNav($modulo,"municipios").'><span>'.$str_lang['LANG_HEADER_TAB_MUNICIPIOS'].'</span></a></li>';		
          if (is_admin())
          {
            echo '<li><a href="'.$admin_path.'usuarios/" '.getSelectedNav($modulo,"usuarios").'><span>'.$str_lang['LANG_HEADER_TAB_USUARIOS'].'</span></a></li>';		
          }
        break;				
    }



  ?>
    </ul>
<br>
<?php
    switch ($datos)
    {
      case "view":
        $id=get_id_by_uri();
        echo '
        <div id="menu">
        <div id="barra">';
        if (!is_consorcio())
          echo'<a href="'.$admin_path.'hidrantes/view/'.$id.'/" '.getSelectedNav($datos,"view").'>'.$str_lang['LANG_HEADER_TAB_FICHA'].'</a>	';
        if (!is_jefeguardia() && !is_consorcio() && !is_oficialjefe && !is_uno_uno_dos())
          echo'<a href="'.$admin_path.'hidrantes/edit/'.$id.'/" '.getSelectedNav($datos,"edit").'>'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';
        
          echo '<a href="'.$admin_path.'hidrantes/comment/'.$id.'/" '.getSelectedNav($datos,"comment").'>'.$str_lang['LANG_HEADER_TAB_COMENTARIOS'].'</a>';
        if (is_gerente() || is_consorcio() || is_admin()) 									
          echo '<a href="'.$admin_path.'hidrantes/cartas/'.$id.'/" '.getSelectedNav($datos,"cartas").'>'.$str_lang['LANG_HEADER_TAB_CARTAS'].'</a>';
        echo '
        </div>
        </div>';
        break;
      case "comment":
        $id=get_id_by_uri();
        echo '
        <div id="menu">
        <div id="barra">
        ';
        if (!is_consorcio())
          echo '<a href="'.$admin_path.'hidrantes/view/'.$id.'/" '.getSelectedNav($datos,"view").'>'.$str_lang['LANG_HEADER_TAB_FICHA'].'</a>';
        if (!is_jefeguardia() && !is_consorcio()&& !is_oficialjefe && !is_uno_uno_dos())
          echo'<a href="'.$admin_path.'hidrantes/edit/'.$id.'/" '.getSelectedNav($datos,"edit").'>'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';
          echo '<a href="'.$admin_path.'hidrantes/comment/'.$id.'/" '.getSelectedNav($datos,"comment").'>'.$str_lang['LANG_HEADER_TAB_COMENTARIOS'].'</a>';
        if (is_gerente() || is_consorcio() || is_admin()) 									
          echo '<a href="'.$admin_path.'hidrantes/cartas/'.$id.'/" '.getSelectedNav($datos,"cartas").'>'.$str_lang['LANG_HEADER_TAB_CARTAS'].'</a>';
        echo '</div>
        </div>';
        break;
      case "edit":
        $id=get_id_by_uri();
        echo '
        <div id="menu">
        <div id="barra">
        ';
        if (!is_consorcio())
          echo '<a href="'.$admin_path.'hidrantes/view/'.$id.'/" '.getSelectedNav($datos,"view").'>'.$str_lang['LANG_HEADER_TAB_FICHA'].'</a>';
        if (!is_jefeguardia() && !is_consorcio()&& !is_oficialjefe && !is_uno_uno_dos())
          echo'<a href="'.$admin_path.'hidrantes/edit/'.$id.'/" '.getSelectedNav($datos,"edit").'>'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';
        
          echo '<a href="'.$admin_path.'hidrantes/comment/'.$id.'/" '.getSelectedNav($datos,"comment").'>'.$str_lang['LANG_HEADER_TAB_COMENTARIOS'].'</a>	';
        if (is_gerente() || is_consorcio() || is_admin()) 									
          echo '<a href="'.$admin_path.'hidrantes/cartas/'.$id.'/" '.getSelectedNav($datos,"cartas").'>'.$str_lang['LANG_HEADER_TAB_CARTAS'].'</a>';
        echo '</div>
        </div>';
        break;
      case "cartas":
        $id=get_id_by_uri();
        echo '
        <div id="menu">
        <div id="barra">';
        if (!is_consorcio())
          echo '<a href="'.$admin_path.'hidrantes/view/'.$id.'/" '.getSelectedNav($datos,"view").'>'.$str_lang['LANG_HEADER_TAB_FICHA'].'</a>';
        if (!is_jefeguardia() && !is_consorcio()&& !is_oficialjefe && !is_uno_uno_dos())
          echo'<a href="'.$admin_path.'hidrantes/edit/'.$id.'/" '.getSelectedNav($datos,"edit").'>'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';
        
          echo '<a href="'.$admin_path.'hidrantes/comment/'.$id.'/" '.getSelectedNav($datos,"comment").'>'.$str_lang['LANG_HEADER_TAB_COMENTARIOS'].'</a>';
        if (is_gerente() || is_consorcio() || is_admin()) 									
          echo '<a href="'.$admin_path.'hidrantes/cartas/'.$id.'/" '.getSelectedNav($datos,"cartas").'>'.$str_lang['LANG_HEADER_TAB_CARTAS'].'</a>';
        echo '</div>
        </div>';
        break;
      case "informes":
          echo '
        <div id="menu">
        <div id="barra">';
        echo '<a href="'.$admin_path.'hidrantes/"  ><span>'.$str_lang['LANG_HIDRANTES_TITLE_LIST'].'</a>';
        if (is_consorcio())
        {
          echo '<a href="'.$admin_path.'hidrantes/listadocartas/" '.getSelectedNav($datos,"listadocartas").'>'.$str_lang['LANG_CARTAS_LISTADO_1'].'</a>	';
            echo '<a href="'.$admin_path.'hidrantes/listadocartapdf/" '.getSelectedNav($datos,"listadocartapdf").'>'.$str_lang['LANG_CARTAS_LISTADO_2'].'</a>	';
        }else
        {										
          echo '<a href="'.$admin_path.'hidrantes/informes/" '.getSelectedNav($datos,"informes").'>'.$str_lang['LANG_HEADER_TAB_INFORMES'].'</a>	
          <a href="'.$admin_path.'hidrantes/excel/" '.getSelectedNav($datos,"excel").'>'.$str_lang['LANG_HEADER_TAB_EXCEL'].'</a>	
          <a href="'.$admin_path.'hidrantes/pendientes/" '.getSelectedNav($datos,"pendientes").'>'.$str_lang['LANG_HEADER_TAB_PENDIENTES'].'</a>';
          echo '<a href="'.$admin_path.'hidrantes/earth/" '.getSelectedNav($datos,"earth").'>Google&nbsp;Earth</a>';
        }
        
        echo'</div>
        </div>';	
        break;	
      case "pendientes":
          echo '
        <div id="menu">
        <div id="barra">';
        echo '<a href="'.$admin_path.'hidrantes/" >'.$str_lang['LANG_HIDRANTES_TITLE_LIST'].'</a>';
        if (is_consorcio())
        {
          echo '<a href="'.$admin_path.'hidrantes/listadocartas/" '.getSelectedNav($datos,"listadocartas").'>'.$str_lang['LANG_CARTAS_LISTADO_1'].'</a>	';
            echo '<a href="'.$admin_path.'hidrantes/listadocartapdf/" '.getSelectedNav($datos,"listadocartapdf").'>'.$str_lang['LANG_CARTAS_LISTADO_2'].'</a>';
        }else
        {										
          echo '<a href="'.$admin_path.'hidrantes/informes/" '.getSelectedNav($datos,"informes").'>'.$str_lang['LANG_HEADER_TAB_INFORMES'].'</a>	
          <a href="'.$admin_path.'hidrantes/excel/" '.getSelectedNav($datos,"excel").'><span>'.$str_lang['LANG_HEADER_TAB_EXCEL'].'</a>	
          <a href="'.$admin_path.'hidrantes/pendientes/" '.getSelectedNav($datos,"pendientes").'>'.$str_lang['LANG_HEADER_TAB_PENDIENTES'].'</a>';
          echo '<a href="'.$admin_path.'hidrantes/earth/" '.getSelectedNav($datos,"earth").'>Google&nbsp;Earth</span></a>	';
        }
        
        echo'</div>
        </div>';	
        break;		
      case "listadocartas":
          echo '
        <div id="menu">
        <div id="barra">';
        echo '<a href="'.$admin_path.'hidrantes/"  ><span>'.$str_lang['LANG_HIDRANTES_TITLE_LIST'].'</a>';
        if (is_consorcio())
        {
          echo '<a href="'.$admin_path.'hidrantes/listadocartas/" '.getSelectedNav($datos,"listadocartas").'>'.$str_lang['LANG_CARTAS_LISTADO_1'].'</a>	';
            echo '<a href="'.$admin_path.'hidrantes/listadocartapdf/" '.getSelectedNav($datos,"listadocartapdf").'>'.$str_lang['LANG_CARTAS_LISTADO_2'].'</a>	';
        }else
        {										
          echo '<a href="'.$admin_path.'hidrantes/informes/" '.getSelectedNav($datos,"informes").'>'.$str_lang['LANG_HEADER_TAB_INFORMES'].'</a>	
          <a href="'.$admin_path.'hidrantes/excel/" '.getSelectedNav($datos,"excel").'>'.$str_lang['LANG_HEADER_TAB_EXCEL'].'</a>	
          <a href="'.$admin_path.'hidrantes/pendientes/" '.getSelectedNav($datos,"pendientes").'>'.$str_lang['LANG_HEADER_TAB_PENDIENTES'].'</a>	';
          echo '<a href="'.$admin_path.'hidrantes/earth/" '.getSelectedNav($datos,"earth").'>Google&nbsp;Earth</a>	';
        }
        
        echo'</div>
        </div>';	
        break;		
      case "listadocartapdf":
          echo '
        <div id="menu">
        <div id="barra">';
        echo '<a href="'.$admin_path.'hidrantes/"  >'.$str_lang['LANG_HIDRANTES_TITLE_LIST'].'</a>';
        if (is_consorcio())
        {
          echo '<a href="'.$admin_path.'hidrantes/listadocartas/" '.getSelectedNav($datos,"listadocartas").'>'.$str_lang['LANG_CARTAS_LISTADO_1'].'</a>	';
          echo '<a href="'.$admin_path.'hidrantes/listadocartapdf/" '.getSelectedNav($datos,"listadocartapdf").'>'.$str_lang['LANG_CARTAS_LISTADO_2'].'</a>	';
        }else
        {										
          echo '<a href="'.$admin_path.'hidrantes/informes/" '.getSelectedNav($datos,"informes").'>'.$str_lang['LANG_HEADER_TAB_INFORMES'].'</a>	
         <a href="'.$admin_path.'hidrantes/excel/" '.getSelectedNav($datos,"excel").'>'.$str_lang['LANG_HEADER_TAB_EXCEL'].'</a>	
          <a href="'.$admin_path.'hidrantes/pendientes/" '.getSelectedNav($datos,"pendientes").'>'.$str_lang['LANG_HEADER_TAB_PENDIENTES'].'</a>	';
          echo '<a href="'.$admin_path.'hidrantes/earth/" '.getSelectedNav($datos,"earth").'>Google&nbsp;Earth</a>	';
        }
        echo'</div>
        </div>';	
        break;														
      case "earth":
          echo '
        <div id="menu">
        <div id="barra">';
        echo '<a href="'.$admin_path.'hidrantes/" >'.$str_lang['LANG_HIDRANTES_TITLE_LIST'].'</a>';
        if (is_consorcio())
        {
          echo '<a href="'.$admin_path.'hidrantes/listadocartas/" '.getSelectedNav($datos,"listadocartas").'>'.$str_lang['LANG_CARTAS_LISTADO_1'].'</a>	';
            echo '<a href="'.$admin_path.'hidrantes/listadocartapdf/" '.getSelectedNav($datos,"listadocartapdf").'>'.$str_lang['LANG_CARTAS_LISTADO_2'].'</a>	';
        }else
        {										
          echo '<a href="'.$admin_path.'hidrantes/informes/" '.getSelectedNav($datos,"informes").'>'.$str_lang['LANG_HEADER_TAB_INFORMES'].'</a>	
          <a href="'.$admin_path.'hidrantes/excel/" '.getSelectedNav($datos,"excel").'>'.$str_lang['LANG_HEADER_TAB_EXCEL'].'</a>	
         <a href="'.$admin_path.'hidrantes/pendientes/" '.getSelectedNav($datos,"pendientes").'>'.$str_lang['LANG_HEADER_TAB_PENDIENTES'].'</a>	';
          echo '<a href="'.$admin_path.'hidrantes/earth/" '.getSelectedNav($datos,"earth").'><span>Google&nbsp;Earth</a>	';
        }
        echo'</div>
        </div>';	
  
        break;
      case "excel":
      echo '
        <div id="menu">
        <div id="barra">';
        echo '<a href="'.$admin_path.'hidrantes/" ><span>'.$str_lang['LANG_HIDRANTES_TITLE_LIST'].'</a>	';
        if (is_consorcio())
        {
          echo '<a href="'.$admin_path.'hidrantes/listadocartas/" '.getSelectedNav($datos,"listadocartas").'>'.$str_lang['LANG_CARTAS_LISTADO_1'].'</a>	';
            echo '<a href="'.$admin_path.'hidrantes/listadocartapdf/" '.getSelectedNav($datos,"listadocartapdf").'>'.$str_lang['LANG_CARTAS_LISTADO_2'].'</a>	';
        }else
        {										
          echo '<a href="'.$admin_path.'hidrantes/informes/" '.getSelectedNav($datos,"informes").'>'.$str_lang['LANG_HEADER_TAB_INFORMES'].'</a>	
          <a href="'.$admin_path.'hidrantes/excel/" '.getSelectedNav($datos,"excel").'>'.$str_lang['LANG_HEADER_TAB_EXCEL'].'</a>	
          <a href="'.$admin_path.'hidrantes/pendientes/" '.getSelectedNav($datos,"pendientes").'>'.$str_lang['LANG_HEADER_TAB_PENDIENTES'].'</a>';
          echo '<a href="'.$admin_path.'hidrantes/earth/" '.getSelectedNav($datos,"earth").'>Google&nbsp;Earth</a>	';
        }

        echo'</div>
        </div>';	
        break;
      case "":
        echo '
        <div id="menu">
        <div id="barra">';
        echo '<a href="'.$admin_path.'hidrantes/" class="active">'.$str_lang['LANG_HIDRANTES_TITLE_LIST'].'</a>	';
        if (is_consorcio())
        {
          echo '<a href="'.$admin_path.'hidrantes/listadocartas/" '.getSelectedNav($datos,"listadocartas").'>'.$str_lang['LANG_CARTAS_LISTADO_1'].'</a>	';
            echo '<a href="'.$admin_path.'hidrantes/listadocartapdf/" '.getSelectedNav($datos,"listadocartapdf").'>'.$str_lang['LANG_CARTAS_LISTADO_2'].'</a>	';
        }else
        {										
          echo '<a href="'.$admin_path.'hidrantes/informes/" '.getSelectedNav($datos,"informes").'>'.$str_lang['LANG_HEADER_TAB_INFORMES'].'</a>	
          <a href="'.$admin_path.'hidrantes/excel/" '.getSelectedNav($datos,"excel").'>'.$str_lang['LANG_HEADER_TAB_EXCEL'].'</a>	
          <a href="'.$admin_path.'hidrantes/pendientes/" '.getSelectedNav($datos,"pendientes").'>'.$str_lang['LANG_HEADER_TAB_PENDIENTES'].'</a>	';
          echo '<a href="'.$admin_path.'hidrantes/earth/" '.getSelectedNav($datos,"earth").'><span>Google&nbsp;Earth</a>	';
        }
        
        echo'</div>
        </div>';
        break;
    }
  ?>
<?php echo '<br> <a href="'.$admin_path.'logout/" title="salir"><img src="http://prehidrantes.bomberostenerife.com/cp-admin/dashboard/images/logout.gif" style="vertical-align:middle;"> Salir de la aplicación</a><p>&nbsp;</p></div>';
?>

    
