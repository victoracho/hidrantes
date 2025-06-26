<script type="text/javascript">
	$(function() {
	
		 $('#upload_sp').MultiFile({
		    list: '#lista-files',
			STRING: {
				remove:'[Quitar archivo]',
				selected:'Selecionado: $file'
		  }
		 });
		
	});
	
</script>

<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );

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
			$datos[$key]= trim($value);
		}				
		
		/* Guardo los datos del sujeto objeto */
		
		$campos = array (
			"codigo" => strip_tags($datos['codigo']),
			"empresa" => strip_tags($datos['empresa']),
			"telefono" => strip_tags($datos['telefono']),
			"contacto" => strip_tags($datos['contacto']),
			"observaciones" =>strip_tags($datos['observaciones']),
			"municipio" => strip_tags($datos['municipio']),
			"cp" => strip_tags($datos['cp']),
			"email" => strip_tags($datos['email']),
			"direccion" => strip_tags($datos['direccion']),
			"poblacion" => strip_tags($datos['poblacion']),
			"parque_id" => strip_tags($datos['parque_id']),
			"telefonoayunt" => utf8_decode($datos['telefonoayunt']),
			"contactoayunt" => utf8_decode($datos['contactoayunt']),
			"faxayunt" => utf8_decode($datos['contactoayunt']),
			"ayuntamiento" => utf8_decode($datos['ayuntamiento']),
			"departamento" => utf8_decode($datos['departamento']),
			"emailayunt" => utf8_decode($datos['emailayunt'])
		);
	
		$action_edit=$bd->get_all_by_id("municipios","codigo",$campos['codigo']);
	
		//exit;
		if (!$action_edit)
		{
			if (!empty($datos['codigo']))
			{
				$tabla="municipios";
				$idmunicipio=$bd->insert_con_id($tabla,$campos);	
				
				if (!empty($_FILES))
						{
							
							$n=count($_FILES["upload_file"]["name"]);
							for ($i=0;$i<$n;$i++)
							{
								$aux= array("name"=>$_FILES["upload_file"]["name"][$i],"type"=>$_FILES["upload_file"]["type"][$i],"size"=>$_FILES["upload_file"]["size"][$i],"tmp_name"=>$_FILES["upload_file"]["tmp_name"][$i],"id"=>$idmunicipio,"orden"=>$i);
								
								//cambiartammitad($_FILES["upload_file"]["tmp_name"][$i],$_FILES["upload_file"]["tmp_name"][$i]);
								$vfilename=uploadfiles($aux);
								$error=$vfilename[1];
								if ($error=="")
								{
									$camposfiles = array (								
									"fichero" => $vfilename[0],
									"municipio_id"=>$idmunicipio
									);
									$tablafiles="municipioficheros";
									$bd->insert($tablafiles,$camposfiles);	
								}
								
							}
						
						}
				
				$msg=$str_lang['LANG_MSG_OK1'];		
				echo '<div id="success"><p>'.$msg.'</p></div>';					
			}else
			echo '<div id="error"><p>'.$str_lang['LANG_MSG_ERROR6'].'</p></div>';
		}else
		{
			echo '<div id="error"><p>'.$str_lang['LANG_MSG_ERROR7'].'</p></div>';
		}
				
		
	}

	/*
	 * Obtengo todos los datos del parte asociado.
	 */	
	//$wherePartes = "Anio =".entrada_sql($anio)." and RegAnual=".entrada_sql($regAnual)."";
	//$tPostPartes = $bd->getlisttable("partesdiarios",$wherePartes,$orderPartes,$offsetPartes,$limitPartes);	
	//$datos=$tPostPartes[0];	
?>
	<form name="frm" action="<?php echo $action_form?>" method="POST" enctype="multipart/form-data">
	
	<!-- Contenido -->
	<div id="content">
		
		<!-- Encabezado -->
		<div class="header-doc">
			<h1><span><?=$str_lang['LANG_MUNICIPIOS_TITLE_INSERT']?></span></h1>	
            <div class="clr"><hr /></div>
			<p class="head-button"><a href="<?php echo $admin_path?>municipios/"><span><?=$str_lang['LANG_MUNICIPIOS_TITLE_VOLVER']?></span></a></p>			
			<div class="clr"><hr /></div>
		</div>
	
		<!-- // Encabezado -->
		
		
		<div class="client-detail">
			
			<div class="data">
				<div class="orders">	
					<table id="table-parte">						
						<tbody>		
                        <tr>
                        <td colspan="2"><h2><?=$str_lang['LANG_MUNICIPIOS_FICHEROS']?></h2>	</td>
                        </tr>
							<tr>							
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_FICHEROS_ADD']?>:</td>
								<td > <input type="file" id="upload_sp" name="upload_file[]" class="multifile-applied" maxlength="<?=maxfiles?>"   />
								</td>
							</tr>										
							<tr>
								<td colspan="2">
								<div id="lista-files">							

								</div>
								</td>
								
							</tr>	
														
						<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_CODMUNICIPIO']?>:</td>
								<td ><input type="text" name="codigo" id="codigo" class="short" value="" maxlength="5"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_AYUNT']?>:</td>
								<td ><input type="text" name="ayuntamiento" id="ayuntamiento" class="large" value="" maxlength="200"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_DIRECCION']?>:</td>
								<td ><input type="text" name="direccion" id="direccion" class="large" value="" maxlength="200"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_POBLACION']?>:</td>
								<td ><input type="text" name="poblacion" id="poblacion" class="large" value="" maxlength="200"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_CP']?>:</td>
								<td ><input type="text" name="cp" id="cp" class="short" value="" maxlength="10"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?>:</td>
								<td ><input type="text" name="municipio" id="municipio" class="large" value="" maxlength="100"/></td>
							</tr>
							<tr>
							<?php
									$vector=$bd->get_all("parques");
									
									
								?>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_PARQUE']?>:</td>
								<td ><select name="parque_id" id="parque_id"> 
								<?php
									
									echo cargarCombo("-1",$vector,"parque_id","parque".$sub);
								?>
								</select>
								</td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_DEPARTAMENTO']?>:</td>
								<td ><input type="text" id="departamento" name="departamento" class="large" value="" maxlength="200"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_CONTACTO']?>:</td>
								<td ><input type="text" id="contactoayunt" name="contactoayunt" class="large" value="" maxlength="100"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_TLF']?>:</td>
								<td ><input type="text" name="telefonoayunt" id="telefonoayunt" class="large" value="" maxlength="100"/></td>
							</tr>	
							<tr>
								<td class="title" >Fax:</td>
								<td ><input type="text" name="faxayunt" id="faxayunt" class="large" value="" maxlength="20"/></td>
							</tr>	
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_EMAIL']?>:</td>
								<td ><input type="text" name="emailayunt" id="emailayunt" class="large" value="" maxlength="150"/></td>
							</tr>
					
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_EMPRESA']?>:</td>
								<td ><input type="text" name="empresa" id="empresa" class="large" value="" maxlength="100"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_TLF']?>:</td>
								<td ><input type="text" name="telefono" id="telefono" class="large" value="" maxlength="20"/></td>
							</tr>	
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_CONTACTO']?>:</td>
								<td ><input type="text" id="contacto" name="contacto" class="large" value="" maxlength="100"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_EMAIL']?>:</td>
								<td ><input type="text" id="email" name="email" class="large" value="" maxlength="150"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_OBS']?>:</td>
								<td ><textarea id="observaciones" name="observaciones"  class="lang-edit"></textarea></td>								
							</tr>									
							
						</table>
						<p class="button" style="margin-top:20px;text-align:center">
							<input type="hidden" name="id" id="id" value="<?php echo $id?>"/>
							<input type="submit" value="<?=$str_lang['LANG_MUNICIPIOS_GUARDAR']?>" class="button" id="enviar" name="enviar"/>
						</p>                					
				</div>
				<div class="clr"><hr /></div>
			</div>
		</div>
		
	</div>
	</form>
	<!-- // Contenido -->
