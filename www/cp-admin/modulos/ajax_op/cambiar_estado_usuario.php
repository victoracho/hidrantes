<?php
include '../../../php/config.php';
require_once(serverpath.'includes/classes/bbdd.php' );
require_once(serverpath.'includes/functions/functions.php' );
require_once(serverpath.'includes/functions/validation.php' );

// Creamos el path del admin
$serveradmin_path=serverpath .'cp-admin/';
$realadmin_path=path .'cp-admin/';
$admin_path=path . item_admin . '/';
$imgadminpath=$realadmin_path. 'images/';

// Conexión a la BD
$bd = new bbdd(sql_host,sql_usuario,sql_pass,sql_db);
	
	if (!empty($_REQUEST['id']))  {		

			$id=strip_tags($_REQUEST['id']); 
			$action_edit=$bd->get_all_by_id("st_usuarios","id_usuario",$id);	
						
			// Actualizamos
			if (!empty($action_edit))
			{
				if ($action_edit['activo']==0)				
				{
					$campos=array("activo"=>1);				
					$bd->update("st_usuarios",$campos,"id_usuario",$id);
					$estado='<img src="'.$imgadminpath.'ico-estado-ok.gif" width="17" height="16" alt="Activo" /> <a href="#'.$id.'" title="Pasar a no activo" class="change_status"><img src="'.$imgadminpath.'ico-estado-inhab-off.gif" width="16" height="16" alt="No activo" /></a>';					
				}	
				else 
				{
					$campos=array("activo"=>0);				
					$bd->update("st_usuarios",$campos,"id_usuario",$id);
					$estado='<a href="#'.$id.'" title="Pasar a activo" class="change_status"><img src="'.$imgadminpath.'ico-estado-ok-off.gif" width="17" height="16" alt="No activo" /></a> <img src="'.$imgadminpath.'ico-estado-inhab.gif" width="16" height="16" alt="No activo" />';															
				}
				echo $estado;
			}									
	}
$bd->bbdd_desc();   
die();			 
?>