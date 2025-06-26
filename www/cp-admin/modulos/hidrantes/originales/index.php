<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
	// Quito la cache de los navegadores en el admin.
	
	if ($datos == "viewpdf")
	{
		$id=get_id_by_uri();
		$comentario_id=get_id_by_uri2();
		include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
		exit;
	}
	if ($datos == "googlemapampliado")
	{
		$id=get_id_by_uri();
	
		include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
		exit;
	}
	if ($datos == "export")
	{
		$id=get_id_by_uri();
	
		include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
		exit;
	}
	if ($datos == "hidrantesmunicipio")
	{
		$id=get_id_by_uri();
	
		include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
		exit;
	}
	if ($datos == "hidrantesfechas")
	{
		$id=get_id_by_uri();
	
		include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
		exit;
	}
	if ($datos == "hidrantescodigo")
	{
		$id=get_id_by_uri();
	
		include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
		exit;
	}
	if ($datos == "cartasmunicipio")
	{
		$id=get_id_by_uri();
	
		include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
		exit;
	}
	if ($datos == "cartascodigo")
	{
		$id=get_id_by_uri();
	
		include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
		exit;
	}
	if ($datos == "exportListado")
	{
		include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
		exit;
	}
	if ($datos == "verficha")
	{
		$id=get_id_by_uri();		
		include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
		exit;
	}
	include $serveradmin_path."/includes/header.php";  	

	switch ($datos)
		{
			case 'view':
				$action_title="Ver municipio";
				$id=get_id_by_uri();
				$op=get_id_by_uri2();
			
				$action_form=$admin_path.$modulo.'/'.$datos.'/'.$id.'/';			
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;			
			case 'ficha':
				$action_title="Ver ficha";
				$id=get_id_by_uri();
				$op=get_id_by_uri3();
			
				$action_form=$admin_path.$modulo.'/'.$datos.'/'.$id.'/';			
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;			
				
			case 'edit':
				$action_title="Editar hidrante";
				$action_button="formButtonSave";
				$id=get_id_by_uri();
				
				$action_form=$admin_path.$modulo.'/'.$datos.'/'.$id.'/';			
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			case 'editarcomentario':
				$action_title="Editar hidrante";
				$action_button="formButtonSave";
				$id=get_id_by_uri();
				
				$action_form=$admin_path.$modulo.'/'.$datos.'/'.$id.'/';			
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			case 'editarcarta':
				$action_title="Editar hidrante";
				$action_button="formButtonSave";
				$id=get_id_by_uri();
				
				$action_form=$admin_path.$modulo.'/'.$datos.'/'.$id.'/';			
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			case 'del':
				$id=get_id_by_uri();
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				include($serveradmin_path."modulos/".$modulo."/listado.php");
				break;
			
			case 'insert':
				$action_title="Añadir hidrante";
				$action_button="formButtonSave";
				$id=get_id_by_uri();
				$action_form=$admin_path.$modulo.'/'.$datos.'/';
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			
			case 'comment':
				$action_title="comentarios a hidrante";
				$action_button="formButtonSave";
				$id=get_id_by_uri();
				
				$action_form=$admin_path.$modulo.'/'.$datos.'/'.$id.'/';			
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			case 'insertacomentario':
				$action_title="Añadir hidrante";
				$action_button="formButtonSave";
				$id=get_id_by_uri();
				$action_form=$admin_path.$modulo.'/'.$datos.'/'.$id.'/';
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			case 'insertacomentariojp':
				$action_title="Añadir hidrante";
				$action_button="formButtonSave";
				$id=get_id_by_uri();
				$op=get_id_by_uri2();
				$action_form=$admin_path.$modulo.'/'.$datos.'/'.$id.'/';
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			case 'insertacomentarioadmin':
				$action_title="Añadir hidrante";
				$action_button="formButtonSave";
				$id=get_id_by_uri();
				$action_form=$admin_path.$modulo.'/'.$datos.'/'.$id.'/';
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			case 'viewpdf':

				$action_title="PDF de hidrante";
				$action_button="formButtonSave";
				$id=get_id_by_uri();
				$action_form=$admin_path.$modulo.'/'.$datos.'/'.$id.'/';	
							
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			case 'cartas':

				$action_title="Ver municipio";
				$action_button="formButtonSave";
				$id=get_id_by_uri();
				$op=get_id_by_uri2();
			
				$action_form=$admin_path.$modulo.'/'.$datos.'/'.$id.'/';			
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;			
			case 'informes':
				$action_title="Informes de hidrante";
				$action_button="formButtonSave";
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			case 'excel':
				$action_title="Informes de hidrante";
				$action_button="formButtonSave";
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			case 'listadocartapdf':
				$action_title="Informes de hidrante";
				$action_button="formButtonSave";
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			
			case 'pendientes':
				$action_title="Hidrantes pendientes de revisar";
				$action_button="formButtonSave";
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			case 'listadocartas':
				$action_title="Hidrantes pendientes de revisar";
				$action_button="formButtonSave";
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			case 'earth':
				$action_title="Mapa de hidrantes";
				$action_button="formButtonSave";
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;
			default:
				$action_form=$admin_path.$modulo.'/'.$datos.'/';			
				include($serveradmin_path."modulos/".$modulo."/listado.php");
				break;
		}
	include $serveradmin_path."includes/footer.php";	
?>
