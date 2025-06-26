
<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
	$page=get_id_by_uri2();
	$action_edit=$bd->get_all_by_id("municipios","municipio_id",$id);
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

	if ($_POST['fichero_id']>0)
	{		
		unlink(uploadpathadmin."files/".$_POST['filename_'.$_POST['fichero_id']]);
		
			$tabla="municipioficheros";
			
			$sql="DELETE FROM ".$tabla." WHERE fichero_id= ".$_POST['fichero_id'];
			
			$r = $bd->bbdd_query($sql);	
			if (!$r)
			{
				$error= $str_lang['LANG_MSG_ERROR8'];
			}else	{// Todo ok.
		
			$msgOK=$str_lang['LANG_MSG_OK4'];
		}
	}
	
	
	if (isset($_POST['upload']))
	{
		//var_dump($_POST);
		
		$aux=$bd->get_all_by_filter("municipioficheros","municipio_id=".$id." and nombrereal like '".$_FILES["upload_file"]["name"]."'");
		if (count($aux)==0)
		{	
		
				
			
			$aux= array("name"=>$_FILES["upload_file"]["name"],"type"=>$_FILES["upload_file"]["type"],"size"=>$_FILES["upload_file"]["size"],"tmp_name"=>$_FILES["upload_file"]["tmp_name"],"id"=>$id,"orden"=>$_POST["ordenfile"]);
									
								
			$vfilename=uploadfiles($aux);
			
			if ($vfilename[1]=="")
			{
				$campos = array (
							"fichero"=>$vfilename[0],
							"municipio_id" => $id,						
							"nombrereal"=>$_FILES["upload_file"]["name"]
							);
			   $tabla="municipioficheros";
			   $bd->insert($tabla,$campos);	
			   
			   $msgOK = $str_lang['LANG_MSG_OK5'];
		   }else
			$error=$vfilename[1];
		}else
		$error= $str_lang['LANG_MSG_ERROR9'];
	}
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
		
		$campos = array (
			"codigo" => $datos['codigo'],			
			"municipio" => $datos['municipio'],
			"empresa" => $datos['empresa'],
			"telefono" =>$datos['telefono'],
			"contacto" => $datos['contacto'],
			"observaciones" => $datos['observaciones'],
			"cp" => $datos['cp'],
			"email" => $datos['email'],
			"direccion" => $datos['direccion'],
			"poblacion" => $datos['poblacion'],
			"parque_id" =>$datos['parque_id'],
			"telefonoayunt" => $datos['telefonoayunt'],
			"contactoayunt" => $datos['contactoayunt'],
			"faxayunt" => $datos['contactoayunt'],
			"ayuntamiento" => $datos['ayuntamiento'],
			"departamento" => $datos['departamento'],
			"emailayunt" => $datos['emailayunt']
					
		);
		
		if (!empty($datos['codigo']))
		{
		
			$tabla="municipios";
			$str="";
			$i=0;
			foreach($campos as $key => $value)
			{	
				if ($i==0) $str.=" ".$key."=".entrada_sql($value);
				else $str.=", ".$key."=".entrada_sql($value);
				$i=1;
			}
			
			$sql="UPDATE ".$tabla." SET ".$str." WHERE municipio_id =".$datos["id"];		
			$r = $bd->bbdd_query($sql);	
			if (!$r)
			{
				$error =  '<div id="error"><p>Error al actualizar.</div>';
			}else	{// Todo ok.
		
			$msgOK= $str_lang['LANG_MSG_OK3'];
			
			}
		}else
		$error = $str_lang['LANG_MSG_ERROR6'];
		
		
	}

	if (isset($error))
	{
		echo '<div id="error"><p>'.$error.'</div>';
	}elseif(isset($msgOK))
	{
		echo '<div id="success"><p>'.$msgOK.'</p></div>';	
	}
	
	$readonly="";
	$disabled="";
	if (is_oficialjefe() || is_consorcio() || is_uno_uno_dos())
	{
		$readonly="readonly";
		$disabled="disabled";
	}
	
	/*
	 * Obtengo todos los datos del parte asociado.
	 */	
	//$wherePartes = "Anio =".entrada_sql($anio)." and RegAnual=".entrada_sql($regAnual)."";
	//$tPostPartes = $bd->getlisttable("partesdiarios",$wherePartes,$orderPartes,$offsetPartes,$limitPartes);	
	//$datos=$tPostPartes[0];	
?>
	
	
	<!-- Contenido -->
	<div id="content">
		
		<!-- Encabezado -->
		<div class="header-doc">
			<h1><?=$str_lang['LANG_MUNICIPIOS_TITLE_EDIT']?></h1>	
			<p class="head-button"><a href="<?php echo $admin_path?>municipios/?&page=<?=$page?>"><span><?=$str_lang['LANG_MUNICIPIOS_TITLE_VOLVER']?></span></a></p>

			<div class="clr"><hr /></div>
		</div>
		
			
		<div class="client-detail">
			
			<div class="data">
				<div class="address">
					<address>

					</address>
					<p class="state"></p>
				</div>
				<div class="orders">	
					<form name="frm" action="<?php echo $action_form?>" method="POST">				
					<table id="table-parte">
						<tbody>					
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_CODMUNICIPIO']?>:</td>
								<td ><input type="text" name="codigo" id="codigo" class="short" value="<?php echo $datos['codigo']?>" maxlength="5" <?=$readonly?>/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_AYUNT']?>:</td>
								<td ><input type="text" name="ayuntamiento" id="ayuntamiento" class="large" value="<?php echo $datos['ayuntamiento']?>" maxlength="200" <?=$readonly?>/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_DIRECCION']?>:</td>
								<td ><input type="text" name="direccion" id="direccion" class="large" value="<?php echo $datos['direccion']?>" maxlength="200" <?=$readonly?>/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_POBLACION']?>:</td>
								<td ><input type="text" name="poblacion" id="poblacion" class="large" value="<?php echo $datos['poblacion']?>" maxlength="200" <?=$readonly?>/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_CP']?>:</td>
								<td ><input type="text" name="cp" id="cp" class="short" value="<?php echo $datos['cp']?>" maxlength="150" <?=$readonly?>/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?>:</td>
								<td ><input type="text" name="municipio" id="municipio" class="large" value="<?php echo $datos['municipio']?>" maxlength="100" <?=$readonly?>/></td>
							</tr>	
							<tr>
							<?php
									$vector=$bd->get_all("parques");
									
									
								?>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_PARQUE']?>:</td>
								<td ><select name="parque_id" id="parque_id" <?=$disabled?>> 
								<?php
									
									echo cargarCombo($datos['parque_id'],$vector,"parque_id","parque".$sub);
									
								?>
								</select>
								</td>
							</tr>	
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_DEPARTAMENTO']?>:</td>
								<td ><input type="text" id="departamento" name="departamento" class="large" value="<?php echo $datos['departamento']?>" maxlength="200" <?=$readonly?>/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_CONTACTO']?>:</td>
								<td ><input type="text" id="contactoayunt" name="contactoayunt" class="large" value="<?php echo $datos['contactoayunt']?>" maxlength="100" <?=$readonly?>/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_TLF']?>:</td>
								<td ><input type="text" name="telefonoayunt" id="telefonoayunt" class="large" value="<?php echo $datos['telefonoayunt']?>" maxlength="100" <?=$readonly?>/></td>
							</tr>	
							<tr>
								<td class="title" >Fax:</td>
								<td ><input type="text" name="faxayunt" id="faxayunt" class="large" value="<?php echo $datos['faxayunt']?>" maxlength="50" <?=$readonly?>/></td>
							</tr>	
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_EMAIL']?>:</td>
								<td ><input type="text" name="emailayunt" id="emailayunt" class="large" value="<?php echo $datos['emailayunt']?>" maxlength="150" <?=$readonly?>/></td>
							</tr>
						</table>
							<br />
						<table id="table-parte">
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_EMPRESA']?>:</td>
								<td ><input type="text" name="empresa" id="empresa" class="large" value="<?php echo $datos['empresa']?>" maxlength="100" <?=$readonly?>/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_TLF']?>:</td>
								<td ><input type="text" name="telefono" id="telefono" class="large" value="<?php echo $datos['telefono']?>" maxlength="20" <?=$readonly?>/></td>
							</tr>	
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_CONTACTO']?>:</td>
								<td ><input type="text" id="contacto" name="contacto" class="large" value="<?php echo $datos['contacto']?>" maxlength="100" <?=$readonly?>/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_USERS_COL_EMAIL']?>:</td>
								<td ><input type="text" name="email" id="email" class="large" value="<?php echo $datos['email']?>" maxlength="150" <?=$readonly?>/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_OBS']?>:</td>
								<td ><textarea id="observaciones" name="observaciones"  class="lang-edit" <?=$readonly?>><?php echo $datos['observaciones']?></textarea></td>								
							</tr>									
							
						</table>
						<?php
							if (!is_consorcio() && !is_oficialjefe() && !is_uno_uno_dos())
							{
							?>	
						
						<p class="button" style="margin-top:20px;text-align:center">
							<input type="hidden" name="id" id="id" value="<?php echo $id?>"/>
							
							<input type="submit" value="<?=$str_lang['LANG_MUNICIPIOS_GUARDAR']?>" class="button" id="enviar" name="enviar"/>
						</p>    
							<?php
							}
							?>
						</form>						
				</div>
				<form name="frmupload" id="frmupload" action="<?php echo $action_form?>" method="POST" enctype="multipart/form-data">	
		<div class="orders2">	
			<h2><?=$str_lang['LANG_MUNICIPIOS_FICHEROS']?></h2>		
			<div class="clr"><hr /></div>			
			<table id="table-parte" >						
				<tbody>		
<?php
							//Cargamos las fotos
							$vector=$bd->get_all_by_filter("municipioficheros","municipio_id=".$id);
							//$vector=$bd->get_all("fotos");
							$n=count($vector);
					if (!is_consorcio() && !is_oficialjefe() && !is_uno_uno_dos())
					{		
							if ($n<maxfiles){
						?>
					
					
				
					<tr>							
						<td class="title" style="width:10%" ><?=$str_lang['LANG_MUNICIPIOS_FICHEROS_ADD']?>:</td>
						<td style="width:20%"> <input type="file" id="upload_sp" name="upload_file"  class="large"/></td>
				
							
							<td style="width:40%;text-align:left;"> <p class="button" style="margin-top:20px;text-align:left">
								<input type="submit" value="<?=$str_lang['LANG_MUNICIPIOS_FICHEROS_ADD']?>" class="button" id="upload" name="upload"/>
							</p>
							</td>
							
							</tr>				
							<?php					
							}
					}
							?>

					<tr>
						<td colspan="4">
						<div class="fotos">							
						<?php
							//Cargamos las fotos
						//	$vector=$bd->get_all_by_filter("fotos","hidrante_id=".$id);
							//$vector=$bd->get_all("fotos");
							//$n=count($vector);
							
							for ($i=0;$i<$n;$i++)
							{
								if ($i==maxfiles){
								break;
								}
								echo '<div class="file_i"><a href="'.uploadurladmin."files/".$vector[$i]["fichero"].'" title="" >'.$vector[$i]["nombrereal"].'</a>&nbsp;';
								if (!is_consorcio() && !is_oficialjefe() && !is_uno_uno_dos())
								{
									echo '<a href="#" class="borrafoto" onclick="javascript:eliminarfichero('.$vector[$i]["fichero_id"].');">['.$str_lang['LANG_MUNICIPIOS_FICHEROS_DEL'].']</a>';
								}
								echo'</div>&nbsp;';
								echo '<input type="hidden" name="filename_'.$vector[$i]["fichero_id"].'"  value="'.$vector[$i]["fichero"].'"/>';
								
								

							}
							
							?>
							
						
						</div>
								
														
						
						
						</td>
						
					</tr>	
												
				</tbody>		
				</table>
					<input type="hidden" name="fichero_id" id="fichero_id" value=""/>
					<input type="hidden" name="idfile" id="idfile" value="<?php echo $id?>"/>
					<input type="hidden" name="ordenfile" id="ordenfile" value="<?php echo ($n+1)?>"/>
				
							
		</div>
		</form>
				<div class="clr"><hr /></div>

		</div>
		
	</div>

	<!-- // Contenido -->
