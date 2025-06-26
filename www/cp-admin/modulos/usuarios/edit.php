<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
	$page=get_id_by_uri2();
	$action_edit=$bd->get_all_by_id("usuarios","UserID",$id);
	$datos= $action_edit;
	// Si no existe el id, salgo.				
	if (empty($action_edit))
		{
			Location($admin_path.$modulo."/");
			exit();
		}

   /*
    * Tengo que ver si el administrativo esta intentando entrar
    * a una tasa que no puede ver
    */ 

	/*
	if (is_gestor())
	{
		if ($action_edit['status']>4)
		{
			Location($admin_path.$modulo."/");
			exit();			
		}
		
	}	
	*/
	/* Datos que necesito */
	//$anio =  substr($action_edit['id_parte'], 0, 4);
	//$regAnual = substr($action_edit['id_parte'], 4);	

	/* SE ENVIA EL FORMULARIO ACTUALIZAR */
	if (isset($_POST['enviar'])) 
	{
		
		$datos=array();
		// Recojo los datos			
		foreach($_POST as $key => $value)
		{
			$datos[$key]= $value;
		}				
		
		/* Guardo los datos del sujeto objeto */
		if (!empty($datos['Password']))
				{
		$campos = array (
			"nombre" => utf8_decode($datos['nombre']),
			"Name" =>utf8_decode($datos['Name']),
			"apellidos" => utf8_decode($datos['apellidos']),
			"codigo" => utf8_decode($datos['codigo']),			
			"telefonos" => utf8_decode($datos['telefonos']),
			"email" => utf8_decode($datos['email']),
			"Password" => md5($datos['Password']),
			"perfil_id" => $datos['perfil_id'],
			"activate" => "",
			"parque_id" => $datos['parque_id']
		);
		}else
		{
			$campos = array (
			"nombre" => utf8_decode($datos['nombre']),
			"Name" =>utf8_decode($datos['Name']),
			"apellidos" => utf8_decode($datos['apellidos']),
			"codigo" => utf8_decode($datos['codigo']),			
			"telefonos" => utf8_decode($datos['telefonos']),
			"email" => utf8_decode($datos['email']),
			"perfil_id" => $datos['perfil_id'],
			"activate" => "",
			"parque_id" => $datos['parque_id']
		);
		}
		
		
		if (!empty($datos['Name']))
		{
		
			$tabla="usuarios";
			$str="";
			$i=0;
			foreach($campos as $key => $value)
			{	
				
				if ($i==0) $str.=" ".$key."=".entrada_sql($value);
				else $str.=", ".$key."=".entrada_sql($value);
				$i=1;

			}
			
			
				if (!empty($datos['Password']))
				{
					if ($datos['Password']==$datos['Password2'])
					{
						$sql="UPDATE ".$tabla." SET ".$str." WHERE UserID =".$datos["id"];		
						$r = $bd->bbdd_query($sql);	
						if (!$r)
						{
							echo '<div id="error"><p>Error al actualizar.</div>';
						}else	{// Todo ok.
					
						$msg=$str_lang['LANG_MSG_OK3'];
						echo '<div id="success"><p>'.$msg.'</p></div>';					
						}
					}
					else
					echo '<div id="error"><p>'.$str_lang['LANG_MSG_ERROR1'].'</p></div>';
				}
				else
					{
					$sql="UPDATE ".$tabla." SET ".$str." WHERE UserID =".$datos["id"];		
						$r = $bd->bbdd_query($sql);	
						if (!$r)
						{
							echo '<div id="error"><p'.$str_lang['LANG_MSG_ERROR5'].'div>';
						}else	{// Todo ok.
					
						$msg=$str_lang['LANG_MSG_OK3'];		
						echo '<div id="success"><p>'.$msg.'</p></div>';					
						}
					}
			
		}else
		echo '<div id="error"><p>'.$str_lang['LANG_MSG_ERROR3'].'</div>';
		
		
	}

	/*
	 * Obtengo todos los datos del parte asociado.
	 */	
	//$wherePartes = "Anio =".entrada_sql($anio)." and RegAnual=".entrada_sql($regAnual)."";
	//$tPostPartes = $bd->getlisttable("partesdiarios",$wherePartes,$orderPartes,$offsetPartes,$limitPartes);	
	//$datos=$tPostPartes[0];	
?>
	<form name="frm" action="<?php echo $action_form?>" method="POST">
	
	<!-- Contenido -->
	<div id="content">
		
		<!-- Encabezado -->
		<div class="header-doc">
			<h1><span><?=$str_lang['LANG_EDIT_TITLE_LISTADO']?></span></h1>	
            <div class="clr"><hr /></div>
			<p class="head-button"><a href="<?php echo $admin_path?>usuarios/?&page=<?=$page?>"><span><?=$str_lang['LANG_INSERT_VOLVER']?></span></a></p>
			<div class="clr"><hr /></div>
		</div>
		
		<!-- // Encabezado -->
		
		<?php 
			/*switch ($action_edit['status']) {
				case 1:
					$estado = "Sin terminar";
				break;								
				case 2:
					$estado = "Tasa no verificada";				
				break;
				case 3:
					$estado = "Tasa verificada";			
				break;	
				case 4:
					$estado = "Tasa facturable";
				break;															
				case 5:
					$estado = "Tasa no facturable";
				break;	
				case 6:
					$estado = "Tasa borrada/no válida";
				break;		
				case 7:
					$estado = "Tasa pendiente de cobro";
				break;
				case 8:
					$estado = "Tasa cobrada";
				break;		
				case 9:
					$estado = "Tasa no cobrable";
				break;																																									
			}	
*/			

		?>		
		<div class="client-detail">
			
			<div class="data">
				<div class="address">
					<address>

					</address>
					<p class="state"></p>
				</div>
				<div class="orders">				
					<table id="table-parte">
						<tbody>
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_NAME']?>:</td>
								<td ><input type="text" name="nombre" id="nombre" class="large" value="<?=utf8_encode($datos['nombre'])?>" maxlength="150"/></td>
							</tr>
							<tr>
									<td class="title" ><?=$str_lang['LANG_USERS_COL_LASTNAME']?>:</td>
								<td ><input type="text" name="apellidos" id="apellidos" class="large" value="<?=utf8_encode($datos['apellidos'])?>" maxlength="150"/></td>
							</tr>	
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_COD']?>:</td>
								<td ><input type="text" name="codigo" id="codigo" class="large" value="<?=utf8_encode($datos['codigo'])?>" maxlength="50"/></td>
							</tr>
							<tr>
							<?php
									$vector=$bd->get_all("parques");
									
									
								?>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_PARQUE']?>:</td>
								<td ><select name="parque_id" id="parque_id"> 
								<?php
									
									echo cargarCombo($datos['parque_id'],$vector,"parque_id","parque".$sub);
								?>
								</select>
								</td>
							</tr>		
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_TLF']?>:</td>
								<td ><input type="text" name="telefonos" id="telefonos" class="large" value="<?=utf8_encode($datos['telefonos'])?>" maxlength="200"/></td>
							</tr>	
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_EMAIL']?>:</td>
								<td ><input type="text" id="email" name="email" class="large" value="<?=utf8_encode($datos['email'])?>" maxlength="100"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_USER']?>:</td>
								<td ><input type="text" id="Name" name="Name" class="short" value="<?=utf8_encode($datos['Name'])?>" maxlength="100"/></td>							
							</tr>	
							<tr>
							<?php
									$vector=$bd->get_all("perfiles");
									
									
								?>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_PERFIL']?>:</td>
								<td ><select name="perfil_id" id="perfil_id"> 
								<?php
									
									echo cargarCombo($datos['perfil_id'],$vector,"perfil_id","perfil".$sub);
								?>
								</select>
								</td>
							</tr>	
							<tr>
								<td class="title" ><?=$str_lang['LANG_INSERT_CLAVE']?>:</td>
								<td ><input type="password" id="Password" name="Password" class="short" value="" maxlength="100"/></td>									
							</tr>	
							<tr>
								<td class="title" ><?=$str_lang['LANG_INSERT_RCLAVE']?>:</td>
								<td ><input type="password" id="Password2" name="Password2" class="short" value="" maxlength="100"/></td>									
							</tr>			
							
													
							
						</table>
						<p class="button" style="margin-top:20px;text-align:center">
							<input type="hidden" name="id" id="id" value="<?php echo $id?>"/>
							<input type="submit" value="<?=$str_lang['LANG_INSERT_UPDATE']?>" class="button" id="enviar" name="enviar"/>
						</p>                					
				</div>
				<div class="clr"><hr /></div>
			</div>
		</div>
		
	</div>
	</form>
	<!-- // Contenido -->
