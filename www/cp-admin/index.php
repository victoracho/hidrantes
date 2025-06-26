<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );

	// Pongo la localizacion aqui
	if ($_SESSION["Lang"]!="PT")	
		setlocale(LC_TIME,"spanish");		
	else
		setlocale(LC_TIME,"portuguese");	
	

	// Cargo las funciones de admin.
	require_once($serveradmin_path.'includes/functions/admin-functions.php' );
	

	// Quito la cache de los navegadores en el admin.
	noCache();	
	
	// Obtenemos el modulo. En admin $datos tiene el modulo.
	$modulo=$datos;	
	
	// Lo cambiamos, ahora $datos tiene el action o la acción :D, add, del, edit...
	$datos=adminUrlRewrite(); 
	
	if (empty($modulo)) 
	{
		// Si esta registrado lo mando al dashboard.
		
		if (check_session()===false) include($serveradmin_path.'modulos/index.php');
		else Location($admin_path."dashboard/");
	}
	else{
			if (file_exists($serveradmin_path.'modulos/'.$modulo.'/index.php'))
			{
				if (check_session()===false) Location($admin_path); // checkeo la sesion.
				
				// Si ok , lo incluyo.
				include($serveradmin_path.'modulos/'.$modulo.'/index.php');
			}
			else Location($admin_path);
		}
?>