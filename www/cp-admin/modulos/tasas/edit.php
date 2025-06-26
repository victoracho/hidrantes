<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );

	$action_edit=$bd->get_all_by_id("tasas","id_tasa",$id);
	
	// Si no existe el id, salgo.				
	if (empty($action_edit) || ($action_edit['status']==1))
		{
			Location($admin_path.$modulo."/");
			exit();
		}

   /*
    * Tengo que ver si el administrativo esta intentando entrar
    * a una tasa que no puede ver
    */ 

	if (is_gestor())
	{
		if ($action_edit['status']>4)
		{
			Location($admin_path.$modulo."/");
			exit();			
		}
		
	}	
	
	/* Datos que necesito */
	$anio =  substr($action_edit['id_parte'], 0, 4);
	$regAnual = substr($action_edit['id_parte'], 4);	

	/* SE ENVIA EL FORMULARIO ACTUALIZAR */
	if (isset($_POST['enviar'])) {
		
		$datos=array();
		// Recojo los datos			
		foreach($_POST as $key => $value)
		{
			$datos[$key]= $value;
		}				
		
		/* Guardo los datos del sujeto objeto */
		
		$campos = array (
			"NombreObjetos" => utf8_decode($datos['NombreObjetos']),
			"DNIObjetos" => utf8_decode($datos['DNIObjetos']),
			"DireccionObjetos" => utf8_decode($datos['DireccionObjetos']),
			"TelefonoObjetos" => utf8_decode($datos['TelefonoObjetos']),
			"NombreInmueble" => utf8_decode($datos['NombreInmueble']),
			"DNIInmueble" => utf8_decode($datos['DNIInmueble']),
			"DireccionInmueble" => utf8_decode($datos['DireccionInmueble']),
			"TelefonoInmueble" => utf8_decode($datos['TelefonoInmueble'])	
		);
		
		$tabla="partesdiarios";
		$str="";
		$i=0;
		foreach($campos as $key => $value)
		{	
			if ($i==0) $str.=" ".$key."=".entrada_sql($value);
			else $str.=", ".$key."=".entrada_sql($value);
			$i=1;
		}
		
		$sql="UPDATE ".$tabla." SET ".$str." WHERE Anio =".entrada_sql($anio)." and RegAnual=".entrada_sql($regAnual)."";		
		$r = $bd->bbdd_query($sql);	
		
		/* Validacion simple */
		if ($datos['total_input'] == 0) $error['total_tasa'] = "El total no puede ser 0";
		
		if (empty($error)) {		
				
			// Si el estado es diferente a no_facturable
			if ($datos['status']==4) $datos['no_facturable'] = -1;
			
			$campos = array("userid_mod"=>$_SESSION['id'],
				"fecha_mod"=>date("Y-m-d"),
				"status"=>$datos['status'],
				"no_facturable"=>$datos['no_facturable'],
				"total_tasa"=>$datos['total_tasa'],
				"comentarios"=>utf8_decode($datos['comentarios']));
			
			/* Actualizo la tabla tasas */
			$bd->update("tasas",$campos, "id_tasa", $id);			
					
			/* Borro tasas 2 partes, para añadir de nuevo, mas abajo */
			$bd->delete("tasas2partes", "id_tasa", $id);
			
			/* Tasas2partes */		
			/* Primero personas */
			if (!empty($datos['personal']))
			{
				$total_tasa = 0;
				foreach ($datos['personal'] as $key => $value)
				{
					if ($value['cant']>0 and ($value['horas']>0 or $value['min']>0))
					{
						// id_tp, id_tasa, tasaitem_id,tasaitem_title, precio_u, cant, precio_t
						$rows = $bd->get_all_by_id("tasas_items", "id_item", $key);		    	
					    		
						$tasaitem_title = $rows['item_title'];	
						$total_horas  = $value['horas'] + ($value['min']/60);				
						$total = $value['cant'] * $total_horas * $rows['precio'];
						$campos = array ($id, $key,$tasaitem_title,$rows['precio'],serialize($value),$total);
						
						// Insertamos personal
						$bd->insert("tasas2partes", $campos);	
									
						$total_tasa = $total_tasa + $total;
					}
				}				
			}
			
				/* Vehiculos */
			if (!empty($datos['vehiculos']))
			{
				$coste_kms_total = 0;
				foreach ($datos['vehiculos'] as $key => $value)
				{
					if ($value['cant']>0 and ($value['horas']>0 or $value['min']>0) and $value['kms']>0)
					{
						// Precio Recorrido
						$item_recorrido = get_table_value("tasas_items", "precio", "id_item", 12);					
											
						// id_tp, id_tasa, tasaitem_id,tasaitem_title, precio_u, cant, precio_t
						$rows = $bd->get_all_by_id("tasas_items", "id_item", $key);				    	
					    			
						$tasaitem_title = $rows['item_title'];	

						$total_horas  = $value['horas'] + ($value['min']/60);	
						$coste_vehiculo = $value['cant'] * $total_horas * $rows['precio'];
						$coste_kms = $value['kms'] * $item_recorrido;
	
						$total = $coste_vehiculo + $coste_kms;
						 
						/* Añado vehiculos */
						$campos = array ($id, $key,$tasaitem_title,$rows['precio'],serialize($value),$total);
						
						// Insertamos.
						$bd->insert("tasas2partes", $campos);										
									
						$total_tasa = $total_tasa + $total;
						
						/* Para añadir el registro para el recorrido */
						$coste_kms_total = $coste_kms_total + $coste_kms;
						$lista_id [$key] = $tasaitem_title;
											
					}
				}
	
				if ($coste_kms_total>0)
				{
					// Añado el item recorrido tb. porque es un valor que tengo que guardar.	
					$campos = array ($id, 12, 'Recorrido', $item_recorrido, serialize($lista_id), $coste_kms_total);
					$bd->insert("tasas2partes", $campos);	
				}								
			}
	
			/* Materiales */
			if (!empty($datos['materiales']))
			{			
				foreach ($datos['materiales'] as $key => $value)
				{
					if ($value['horas']>0)
					{
						// id_tp, id_tasa, tasaitem_id,tasaitem_title, precio_u, cant, precio_t
						$rows = $bd->get_all_by_id("tasas_items", "id_item", $key);				    	
					    			
						$tasaitem_title = $rows['item_title'];			

						if ($key!=13)			
							$total = $value ['horas'] * $rows['precio'];
						else //m3/litro
							$total = ($value ['horas']/1000) * $rows['precio'];
						
						// Insertamos.
						$campos = array ($id, $key,$tasaitem_title,$rows['precio'],serialize($value),$total);
						$bd->insert("tasas2partes", $campos);									
									
						$total_tasa = $total_tasa + $total;
					}
				}				
			}		
			if ($total_tasa >0)
			{
				/* Actualizo el total */
				$campos = array("total_tasa"=>$total_tasa);
				$bd->update("tasas",$campos, "id_tasa", $id);
			}				

			$action_edit['status'] = $datos['status'];
			
			// Todo ok.
			$msg="Los datos han sido actualizados.";			
			
			echo '<div id="success"><p>'.$msg.'</p></div>';			
		}
		// Hay errores
		else echo '<div id="error"><p>'.$error['total_tasa'].'</div>';
	}

	/*
	 * Obtengo todos los datos del parte asociado.
	 */	
	$wherePartes = "Anio =".entrada_sql($anio)." and RegAnual=".entrada_sql($regAnual)."";
	$tPostPartes = $bd->getlisttable("partesdiarios",$wherePartes,$orderPartes,$offsetPartes,$limitPartes);	
	$datos=$tPostPartes[0];	
?>
	<form name="frm" action="<?php echo $action_form?>" method="POST">
	
	<!-- Contenido -->
	<div id="content">
		
		<!-- Encabezado -->
		<div class="header-doc">
			<h1><span>Editar Tasa: <?php echo $action_edit['id_tasa']?></span></h1>
            <div class="clr"><hr /></div>
			<p class="head-button"><a href="<?php echo $admin_path?>tasas/?status=<?php echo $action_edit['status']?>&filter=Filtrar"><span>Listado tasas</span></a></p>
			<div class="clr"><hr /></div>
		</div>
		<!-- // Encabezado -->
		
		<?php 
			switch ($action_edit['status']) {
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
		?>		
		<div class="client-detail">
			<p class="code"><span><strong>Estado tasa:</strong> 
			<select name="status" id="status" style="font-size:x-small">
				<option value="2" <?php if ($action_edit['status']==2) echo ' selected="selected"'?>>Tasa no verificada</option>				
				<option value="4" <?php if ($action_edit['status']==4) echo ' selected="selected"'?>>Tasa facturable</option>			
				<option value="5" <?php if ($action_edit['status']==5) echo ' selected="selected"'?>>Tasa no facturable</option>
				<?php 
				if (is_admin())
				{
				?>					
				<option value="6" <?php if ($action_edit['status']==6) echo ' selected="selected"'?>>Tasa borrada/no valida</option>	
				<option value="7" <?php if ($action_edit['status']==7) echo ' selected="selected"'?>>Tasa pendiente de cobro</option>
				<option value="8" <?php if ($action_edit['status']==8) echo ' selected="selected"'?>>Tasa cobrada</option>
				<option value="9" <?php if ($action_edit['status']==9) echo ' selected="selected"'?>>Tasa no cobrable</option>		
				<?php }?>													
			</select>    
			</span>		

			<span id="nofac" <?php if ($action_edit['status']!=5) echo 'style="display:none"'?>> | <strong>Razón:</strong>
			<select id="no_facturable" name="no_facturable">
				<?php 
					$tPostFac = $bd->getlisttable("tasas_items_nofac",'','',0,1000);	
					if (!empty($tPostFac))
						foreach ($tPostFac as $postFac) {
							if ($postFac['id_nofac'] == $action_edit['no_facturable'])
								$str= 'selected="selected"';
							else $str='';
							echo '<option value="'.$postFac['id_nofac'].'" '.$str.'>'.utf8_encode($postFac['concepto']).'</option>';
						}
											
				?>
				<option value="0" <?php if ($action_edit['no_facturable']==0) echo 'selected="selected"'?>>Otros</option>
			</select>
			</span>
			</p>
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
                        <td colspan="8"><h2>Parte asociado: <a href="#content" id="div_parte"/><?php echo $action_edit['id_parte']?></a> | ID Tasa: <a href="#content" id="a_tasa"><?php echo $action_edit['id_tasa']?></a></h2>					</td>
                        </tr>
							<tr>
								<th colspan="8">SOLICITUD DEL SERVICIO</th>
							</tr>
							<tr>
								<td class="title" colspan="2">Parque de bomberos movilizado:</td>
								<td colspan="2"><?php echo utf8_encode(get_table_value("parques",'ParqueDesc','Parque',$datos['Parque']))?></td>
								
								<td class="title" colspan="2">Solicitud a traves del 1-1-2:</td>
								<td colspan="2"><img src="<?php echo imgpath?><?php echo ($datos['Alert112']==-1)? 'vistobueno.gif':'b_drop.png'; ?>"/></td>
							</tr>		
							<tr>
								<td class="title"colspan="2">Es un parte general:</td>
								<td colspan="2"><img src="<?php echo imgpath?><?php echo ($datos['Multiple']==-1 && $datos['Letra'] == 'A')? 'vistobueno.gif':'b_drop.png'; ?>"/></td>
								<td class="title" colspan="2">Es un parte vinculado:</td>
								<td colspan="2"><img src="<?php echo imgpath?><?php echo ($datos['Multiple']==-1 && $datos['Letra'] == 'B')? 'vistobueno.gif':'b_drop.png'; ?>"/></td>
							</tr>	
							<tr>
								<td class="title">Año:</td>
								<td><?php echo $datos['Anio']?></td>
								<td class="title">Nº Reg. Anual:</td>
								<td><?php echo $datos['RegAnual']?></td>
								<td class="title">Nº Reg. Mensual:</td>
								<td><?php echo $datos['RegMensual']?></td>
								<td class="title">Fecha Servicio:</td>
								<td><?php echo convert_date($datos['FecParte'])?></td>																								
							</tr>	
							
							<tr>
								<td class="title" colspan="2">Nº Teléfono:</td>
								<td colspan="2"><?php echo $datos['Telefono']?></td>
								<td class="title" colspan="2">Nombre de la persona que solicita el servicio:</td>
								<td colspan="2"><?php echo utf8_encode($datos['PersSolicita'])?></td>
							</tr>	
							<tr>
								<td class="title" colspan="2">Alertante que requiere el servicio:</td>
								<td colspan="6"><?php echo utf8_encode($datos['Alertante'])?></td>
							</tr>		
							<tr>
								<td class="title" colspan="2">Domicilio:</td>
								<td colspan="6"><?php echo utf8_encode($datos['DomicilioSolicita'])?></td>
							</tr>	
							<tr>
								<td class="title" colspan="2">Hora de Requerimiento:</td>
								<td colspan="2"><?php echo convert_time($datos['HoraReq'])?></td>
								<td class="title" colspan="2">Municipio donde se realiza el servicio:</td>
								<td colspan="2"><?php echo utf8_encode(get_table_value("municipios","NombreMunicipio","CodigoMunicipio",$datos['Municipio']));?></td>
							</tr>
							<tr>
								<td class="title" colspan="2">Tipo de Servicio:</td>
								<td colspan="2"><?php echo utf8_encode(get_table_value("tiposservicios","TipoServicioDesc","TipoServicio",$datos['TipoServicio']))?></td>
								<td class="title" colspan="2">Clasificación:</td>
								<td colspan="2">
								<?php
								switch ($datos['Clasificacion']) {
									case 1:
										echo "Nivel II";
										break;
										
									case 2:
										echo "Nivel I";
										break;
									case 3:
										echo "Nivel III";																		
										break;
									
									default:
										;
									break;
								} 
								
								?>
							</tr>	
							<tr>
								<td class="title" colspan="2">Lugar del Siniestro:</td>
								<td colspan="6"><?php echo utf8_encode($datos['LugarSin'])?></td>
							</tr>	
							<tr>
								<td class="title" colspan="2">Objetos Siniestrados:</td>
								<td colspan="6"><?php echo utf8_encode($datos['ObjetosSin'])?></td>
							</tr>							
							<tr>
								<th colspan="8">DETALLES DEL SERVICIO</th>
							</tr>							
							<tr>
								<td class="title" colspan="2">Dueño de los objetos:</td>
								<td colspan="2"><input type="text" name="NombreObjetos" id="NombreObjetos" class="large" value="<?php echo utf8_encode($datos['NombreObjetos'])?>"/></td>
								<td class="title" colspan="2">DNI ó CIF:</td>
								<td colspan="2"><input type="text" name="DNIObjetos" id="DNIObjetos" value="<?php echo $datos['DNIObjetos']?>"/></td>
							</tr>	
							<tr>
								<td class="title" colspan="2">Dirección del dueño de los objetos:</td>
								<td colspan="2"><input type="text" name="DireccionObjetos" id="DireccionObjetos" class="large" value="<?php echo utf8_encode($datos['DireccionObjetos'])?>"/></td>
								<td class="title" colspan="2">Teléfono:</td>
								<td colspan="2"><input type="text" name="TelefonoObjetos" id="TelefonoObjetos" value="<?php echo $datos['TelefonoObjetos']?>"/></td>
							</tr>	
							<tr>
								<td class="title" colspan="2">Dueño del inmueble:</td>
								<td colspan="2"><input type="text" id="NombreInmueble" name="NombreInmueble" class="large" value="<?php echo utf8_encode($datos['NombreInmueble'])?>"/></td>
								<td class="title" colspan="2">DNI ó CIF:</td>
								<td colspan="2"><input type="text" id="DNIInmueble" name="DNIInmueble"  value="<?php echo $datos['DNIInmueble']?>"/></td>								
							</tr>		
							<tr>
								<td class="title"colspan="2">Dirección del dueño del inmueble:</td>
								<td colspan="2"><input type="text" id="DireccionInmueble" name="DireccionInmueble" class="large" value="<?php echo utf8_encode($datos['DireccionInmueble'])?>"/></td>
								<td class="title" colspan="2">Teléfono:</td>
								<td colspan="2"><input type="text" id="TelefonoInmueble" name="TelefonoInmueble"  value="<?php echo $datos['TelefonoInmueble']?>"/></td>								
							</tr>
							<tr>
								<td class="title" colspan="2">Causas que lo provocaron u originaron:</td>
								<td colspan="6"><?php echo utf8_encode($datos['Causas'])?></td>
							</tr>	
							<tr>
								<td class="title">Hora de salida del parque:</td>
								<td><?php echo convert_time($datos['HoraSalida'])?></td>
								<td class="title">Hora de llegada al lugar del siniestro:</td>
								<td><?php echo convert_time($datos['HoraIniServicio'])?></td>
								<td class="title">Hora de finalización de la intervención:</td>
								<td><?php echo convert_time($datos['HoraFinServicio'])?></td>
								<td class="title">Hora de llegada al parque:</td>
								<td><?php echo convert_time($datos['HoraLlegada'])?></td>																								
							</tr>	
							<tr>
								<td class="title" colspan="2">Material y/o medio utilizados:</td>
								<td colspan="6"><?php echo utf8_encode($datos['MaterialUtilizado'])?></td>
							</tr>	
							<tr>
								<th colspan="8">COMPOSICIÓN DEL TREN DEL SERVICIO</th>
							</tr>	
							<tr>
								<td class="title" colspan="2">Nº de vehículos:</td>
								<td colspan="2"><?php echo $datos['NumVehiculos']?></td>
								<td class="title" colspan="2">Nº de personas:</td>
								<td colspan="2"><?php echo $datos['NumPersonas']?></td>								
							</tr>
							<tr>
								<td class="title" colspan="2">Vehículos:</td>
								<td colspan="6"><?php echo utf8_encode($datos['Vehiculos'])?></td>
							</tr>		
							<tr>
								<td colspan="1">PERSONAL QUE INTERVINO</td>
							</tr>
							<tr>
								<td class="title" colspan="2">Mandos:</td>
								<td colspan="6"><?php echo utf8_encode($datos['MandosInter'])?></td>
							</tr>
							<tr>
								<td class="title" colspan="2">Conductores:</td>
								<td colspan="6"><?php echo utf8_encode($datos['ConductoresInter'])?></td>
							</tr>		
							<tr>
								<td class="title" colspan="2">Bomberos:</td>
								<td colspan="6"><?php echo utf8_encode($datos['BomberosInter'])?><td>
							</tr>	
							<tr>
								<td class="title" colspan="2">Personal técnico que asistio:</td>
								<td colspan="6"><?php echo utf8_encode($datos['PersonalT'])?></td>
							</tr>
							<tr>
								<td class="title" colspan="2">Agentes de la autoridad que asistieron:</td>
								<td colspan="6"><?php echo utf8_encode($datos['Agentes'])?></td>
							</tr>		
							<tr>
								<td class="title" colspan="2">Colaboración de otros servicios:</td>
								<td colspan="6"><?php echo utf8_encode($datos['Colaboracion'])?></td>
							</tr>	
							<tr>
								<td class="title" colspan="2">Autoridades que se presentaron:</td>
								<td colspan="6"><?php echo utf8_encode($datos['Autoridades'])?></td>
							</tr>
							<tr>
								<td class="title" colspan="2">Naturaleza de los objetos, inmueble siniestrado, etc.:</td>
								<td colspan="2"><?php echo utf8_encode($datos['Naturaleza'])?></td>
							</tr>	
							<tr>
								<th colspan="8">MEMORIA O INFORME</th>
							</tr>								
							<tr>
								<td class="title" colspan="2">Mando que dirige la intervención:</td>
								<td colspan="2"><?php echo utf8_encode($datos['NombreMando'])?></td>
								<td class="title" colspan="2">Jefe de guardia que emite el informe</td>
								<td colspan="2"><?php echo utf8_encode($datos['NombreJefeTurno'])?></td>
							</tr>	
							<tr>
								<td colspan="8">Con sujeción a los datos que anteceden y previo requerimiento de la persona reseñada, se presta el presente servicio siguiendo el orden expuesto a continuación:</td>
							</tr>
							<tr>
								<td class="title" colspan="2">Requerimiento:</td>
								<td colspan="6"><?php echo utf8_encode($datos['Requerimiento'])?></td>
							</tr>		
							<tr>
								<td class="title" colspan="2">Informe del Requerimiento:</td>
								<td colspan="6"><?php echo utf8_encode($datos['InformeSuceso'])?></td>
							</tr>	
							<tr>
								<td class="title" colspan="2">Reconocimiento:</td>
								<td colspan="6"><?php echo utf8_encode($datos['Reconocimiento'])?></td>
							</tr>
							<tr>
								<td class="title" colspan="2">Actuación:</td>
								<td colspan="6"><?php echo utf8_encode($datos['Actuacion'])?></td>
							</tr>		
							<tr>
								<td class="title" colspan="2">Observaciones</td>
								<td colspan="6"><?php echo utf8_encode($datos['Observaciones'])?></td>
							</tr>
						</table>
					
				<?php 

					/*
					 * Obtengo todos los datos de las tasas
					 */
					
				    // Voy rellenando $datos
				    /* Personal */
					$where = "id_tasa =".entrada_sql($action_edit['id_tasa'])." and tasaitem_id IN (select id_item from tasas_items where cat_id = 1)";
					$tPost = $bd->getlisttable("tasas2partes",$where,$order,$offset,$limit);
					if (!empty($tPost))
						foreach ($tPost as $post)
						$datos['personal'][$post['tasaitem_id']]  = unserialize($post['cant']);		
					
					 /* Vehiculos */
					$where = "id_tasa =".entrada_sql($action_edit['id_tasa'])." and tasaitem_id IN (select id_item from tasas_items where cat_id = 2)";
					$tPost = $bd->getlisttable("tasas2partes",$where,$order,$offset,$limit);
					if (!empty($tPost))
						foreach ($tPost as $post)
						$datos['vehiculos'][$post['tasaitem_id']]  = unserialize($post['cant']);	
				
					 /* Materiales */
					$where = "id_tasa =".entrada_sql($action_edit['id_tasa'])." and tasaitem_id IN (select id_item from tasas_items where cat_id = 4)";
					$tPost = $bd->getlisttable("tasas2partes",$where,$order,$offset,$limit);
					if (!empty($tPost))
						foreach ($tPost as $post)
						$datos['materiales'][$post['tasaitem_id']]  = unserialize($post['cant']);		
				
					$datos['comentarios'] = $action_edit['comentarios'];
					$datos['status'] = $action_edit['status'];						
				?>					
					<div id="div_tasa" style="margin-top:40px">
                      <fieldset>
                      <legend>Tasas de personal</legend>
                      	<table class="tTasas">
                      		<tr>
	                      		<th>Cargo</th>
	                      		<th>Nº Personas</th>
	                      		<th>Horas</th>
	                      		<th>Tarifa hora</th>
	                      		<th>Total</th>
                      		</tr>
                      	<?php     
                      	$tab=1;        
                      	$total_parte = 0;  
                      	$total_tasa=0;        	
                      	
                      	$tPost = $bd->getlisttable('tasas_items','cat_id=1', $order, $offset, $limit);
						if (!empty($tPost))
							foreach ($tPost as $post)
							{
								$id_item = $post['id_item'];
	                      		echo '
	                      		<tr>
	                      			<td class="title">'.utf8_encode($post['item_title']).'</td>
	                      			<td><input type="text" value="'.$datos['personal'][''.$id_item.''][cant].'" name="personal['.$id_item.'][cant]" id="p_c_'.$id_item.'" maxlength="3" tabindex="'.$tab.'"/></td>';
	                      		
	                      		$tab++;	
	                      		
	                      		$total_horas = $datos['personal'][''.$id_item.'']['horas'] + ($datos['personal'][''.$id_item.'']['min'] / 60 );
	                      		
								$total = $datos['personal'][''.$id_item.'']['cant'] * $total_horas * $post['precio'];								
								$total_tasa = $total_tasa + $total;     							                 	
								
	                      		echo '
	                      			<td>
	                      			<input type="text" value="'.$datos['personal'][''.$id_item.''][horas].'" name="personal['.$id_item.'][horas]" id="p_h_'.$id_item.'" maxlength="2" tabindex="'.$tab.'"/>h :';
	                      		
	                      		$tab++;
	                      		echo'
	                      			<input type="text" value="'.$datos['personal'][''.$id_item.''][min].'" name="personal['.$id_item.'][min]" class="min" id="p_m_'.$id_item.'" maxlength="2" tabindex="'.$tab.'"/>m</td>                      			
	                      			<td id="ctasa_'.$id_item.'" '.$class.'>'.$post['precio'].' &euro;</td>
	                      			<td id="p_t_'.$id_item.'" class="subtotal_personal '.$class_style.'">'.$total.' &euro;</td>
	                      		</tr>';	
	                      		
	                      		$tab++;							
						}
							$total_parte = $total_parte + $total_tasa;
						?>                       	                     	
							<tr class="total <?php echo $class_style?>" >								
								<td colspan="4" style="text-align:right">Total Tasas de Personal:</td>
								<td id="total_personal"><?php echo $total_tasa?> &euro;</td>								
							</tr>						
                      	</table>
                      </fieldset>
	                      
                      <fieldset>
                      <legend>Tasas de vehículos</legend>
                      	<table class="tTasas">
                      		<tr>
	                      		<th>Veh&iacute;culo</th>	                      		
	                      		<th>Unidades</th>
	                      		<th>Horas</th>	                      		
	                      		<th>Tar. hora</th>
	                      		<th>Coste veh.</th>
	                      		<th>Kms. recorridos</th>
	                      		<th>Tar. Km.</th>
	                      		<th>Coste kms</th>
	                      		<th>Total</th>
                      		</tr>                      
                      	<?php 
                      	$total_tasa=0;
                      	// KMS.
						$item_recorrido = get_table_value('tasas_items', 'precio', 'id_item', 12);
						                      	
                      	$tPost = $bd->getlisttable('tasas_items','cat_id=2', $order, $offset, $limit);
						if (!empty($tPost))
							foreach ($tPost as $post)
							{   
								$id_item = $post['id_item'];
								
	                      		echo '
	                      		<tr>
	                      			<td class="title">'.utf8_encode($post['item_title']).'</td>
	                      			<td><input type="text" value="'.$datos['vehiculos'][''.$id_item.''][cant].'" name="vehiculos['.$id_item.'][cant]" id="v_c_'.$id_item.'" maxlength="3" tabindex="'.$tab.'"/></td>';
	                      		
	                      		$tab++;	
								
	                      		$total_horas = $datos['vehiculos'][''.$id_item.''] ['horas'] + ($datos['vehiculos'][''.$id_item.''] ['min']/60);
	                      		
								$coste_vehiculo = $datos['vehiculos'][''.$id_item.'']['cant'] * $total_horas * $post['precio'];
								$coste_kms = $datos['vehiculos'][''.$id_item.'']['kms'] * $item_recorrido;
								$total = $coste_vehiculo + $coste_kms;								
								$total_tasa = $total_tasa + $total;                   		
	                      		
	                      		echo '
	                      			<td><input type="text" value="'.$datos['vehiculos'][''.$id_item.''][horas].'" name="vehiculos['.$id_item.'][horas]" id="v_h_'.$id_item.'" maxlength="2" tabindex="'.$tab.'"/>h : ';
	                      		
	                      		$tab++;	
	                      		
	                      		echo'
	                      			<input type="text" value="'.$datos['vehiculos'][''.$id_item.''][min].'" name="vehiculos['.$id_item.'][min]" id="v_m_'.$id_item.'" maxlength="2" tabindex="'.$tab.'"/>m</td>  
	                      			<td id="ctasa_'.$id_item.'" '.$class.'>'.$post['precio'].' &euro;</td>     
	                      			<td id="v_t_'.$id_item.'" '.$class.'>'.$coste_vehiculo.' &euro;</td>                 			
	                      			<td><input type="text" value="'.$datos['vehiculos'][''.$id_item.''][kms].'" name="vehiculos['.$id_item.'][kms]" id="v_k_'.$id_item.'" maxlength="3" tabindex="'.$tab.'"/></td>
	                      			<td class="item_recorrido '.$class_style.'">'.$item_recorrido.' &euro;</td>
	                      			<td id="v_kmtotal_'.$id_item.'" '.$class.'>'.$coste_kms.' &euro;</td>
	                      			<td id="v_sub_'.$id_item.'" class="subtotal_vehiculos '.$class_style.'">'.$total.' &euro;</td>
	                      		</tr>';			
	
	                      		$tab++;	
						}
						
						$total_parte = $total_parte + $total_tasa;
						?>
							<tr class="total <?php echo $class_style?>">							
								<td colspan="8" style="text-align:right">Total Tasas de Vehículos:</td>
								<td id="total_vehiculos"><?php echo $total_tasa?> &euro;</td>									
							</tr>						      
                      	</table>                
                      </fieldset>
	                                           
                      <fieldset>
                      	<legend>Tasas de materiales consumidos</legend>
                      		<table class="tTasas">
	                      		<tr>
		                      		<th>Material</th>	                      		
		                      		<th>Unidad</th>	                      		
		                      		<th>Tarifa/Tasa</th>
		                      		<th>Total</th>
	                      		</tr>                      
	                      	<?php 
	                      	$total_tasa=0;
                      		$tPost = $bd->getlisttable('tasas_items','cat_id=4', $order, $offset, $limit);
							if (!empty($tPost))
								foreach ($tPost as $post)
								{  
									$id_item = $post['id_item'];
									
									if ($id_item==13)
									$total = ($datos['materiales'][''.$id_item.'']['horas']/1000) * $post['precio'];
									else $total = $datos['materiales'][''.$id_item.'']['horas'] * $post['precio'];
																
									$total_tasa = $total_tasa + $total;    							
									
		                      		echo '
		                      		<tr>
		                      			<td class="title">'.utf8_encode($post['item_title']).'</td>
		                      			<td><input type="text" value="'.$datos['materiales'][''.$id_item.''][horas].'" name="materiales['.$id_item.'][horas]" tabindex="'.$tab.'" id="m_c_'.$id_item.'"/></td>
		                      			<td id="ctasa_'.$id_item.'" '.$class.'>'.$post['precio'].' '.$post['unidad'].'</td>
		                      			<td id="m_sub_'.$id_item.'" class="subtotal_materiales '.$class_style.'">'.$total.' &euro;</td>
		                      		</tr>';								
									$tab++;	                      		
							}
							
							$total_parte = $total_parte + $total_tasa;
							?>
								<tr class="total <?php echo $class_style?>">
									<td colspan="3" style="text-align:right">Total Tasas de Materiales:</td>
									<td id="total_materiales"><?php echo $total_tasa?> &euro;</td>								
								</tr>
	                      	</table>                        
	                      </fieldset>        
		
	                      <fieldset>
	                      <legend>Totales</legend>                      
	                      	<table class="tTasas">
	                      		<tr>
		                      		<th>Totales</th>	                      		
	                      		</tr>                      
	                      		<tr>
	                      			<td style="text-align:center"><input type="hidden" id="total_input" name="total_input" value="<?php echo $total_parte?>"><span id="total_txt"><?php echo number_format($total_parte, 3)?> &euro;</span></td>
	                      		</tr>
	                      	</table> 
	                      </fieldset>	                                                       
	                      
	                      <fieldset>                    
	                      	<table class="tTasas">
	                      		<tr>
		                      		<th>Comentarios:</th>	                      		
	                      		</tr>                      
	                      		<tr>
	                      			<td><textarea rows="10" style="width:100%" id="comentarios" name="comentarios" tabindex="<?php echo $tab++;?>"><?php echo utf8_encode($datos['comentarios'])?></textarea></td>
	                      		</tr>
	                      	</table> 
	                      </fieldset>	
	                      	                      
	                      <?php $tab++?>
						</div>	                      
						<p class="button" style="margin-top:20px;text-align:center">
							<input type="hidden" name="id" id="id" value="<?php echo $id?>"/>
							<input type="submit" value="Actualizar Tasa" class="button" id="enviar" name="enviar"/>
						</p>                					
				</div>
				<div class="clr"><hr /></div>
			</div>
		</div>
		
	</div>
	</form>
	<!-- // Contenido -->
