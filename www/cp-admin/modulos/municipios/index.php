<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
   
	include $serveradmin_path."/includes/header.php";  	
	
	switch ($datos)
		{
			case 'view':
				$action_title="Ver municipio";
				$id=get_id_by_uri();
				$action_form=$admin_path.$modulo.'/'.$datos.'/'.$id.'/';			
				include($serveradmin_path."modulos/".$modulo."/".$datos.".php");
				break;			
			case 'edit':
				$action_title="Editar municipio";
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
				include($serveradmin_path."modulos/".$modulo."/insert.php");
				break;
			default:
				include($serveradmin_path."modulos/".$modulo."/listado.php");
				break;
		}
	include $serveradmin_path."includes/footer.php";	
?>
