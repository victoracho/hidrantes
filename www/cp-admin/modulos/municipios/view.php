<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );

	$action_edit=$bd->get_all_by_id("tasas","id_tasa",$id);
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

	if (is_gestor())
	{
		if ($action_edit['status']>4)
		{
			Location($admin_path.$modulo."/");
			exit();			
		}
		
	}
		
	/*
	 * Obtengo todos los datos del parte asociado.
	 */
	
	$anio =  substr($action_edit['id_parte'], 0, 4);
	$regAnual = substr($action_edit['id_parte'], 4);	
	
	$wherePartes = "Anio =".entrada_sql($anio)." and RegAnual=".entrada_sql($regAnual)."";
	$tPostPartes = $bd->getlisttable("partesdiarios",$wherePartes,$orderPartes,$offsetPartes,$limitPartes);	
	$datos=$tPostPartes[0];		
	
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
	<!-- Contenido -->
	<div id="content">
		
		<!-- Encabezado -->
		<div class="header-doc">
			<h1><span>Consulta tasa: <?php echo $action_edit['id_tasa']?></span></h1>
            <div class="clr"><hr /></div>
			<p class="head-button"><a href="<?php echo $admin_path?>tasas/?status=<?php echo $action_edit['status']?>&filter=Filtrar"><span>Listado tasas</span></a></p>
			<div class="clr"><hr /></div>
		</div>
		<!-- // Encabezado -->
		
		<div class="client-detail">
			<?php 
			$razon = utf8_encode(get_table_value("tasas_items_nofac","concepto", "id_nofac", $action_edit['no_facturable']));
			if (empty($razon)) $razon = 'Otros';
			?>
			<p class="code"><span><strong>Estado tasa:</strong> <?php echo $estado?><?php if ($action_edit['status']==5) echo ' | <strong>Razón:</strong> '.$razon?></span></p>
			<div class="data">
				<div class="address">
					<address>

					</address>
					
					<p class="state"></p>
				</div>
				<div class="orders">
					<h2>Parte asociado: <a href="#content" id="div_parte"/><?php echo $action_edit['id_parte']?></a> | ID Tasa: <a href="#content" id="a_tasa"><?php echo $action_edit['id_tasa']?></a></h2>					
					<table id="table-parte">
						<tbody>
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
								<td colspan="2"><?php echo utf8_encode($datos['NombreObjetos'])?></td>
								<td class="title" colspan="2">DNI ó CIF:</td>
								<td colspan="2"><?php echo $datos['DNIObjetos']?></td>
							</tr>	
							<tr>
								<td class="title" colspan="2">Dirección del dueño de los objetos:</td>
								<td colspan="2"><?php echo utf8_encode($datos['DireccionObjetos'])?></td>
								<td class="title" colspan="2">Teléfono:</td>
								<td colspan="2"><?php echo $datos['TelefonoObjetos']?></td>
							</tr>	
							<tr>
								<td class="title" colspan="2">Dueño del inmueble:</td>
								<td colspan="2"><?php echo utf8_encode($datos['NombreInmueble'])?></td>
								<td class="title" colspan="2">DNI ó CIF:</td>
								<td colspan="2"><?php echo $datos['DNIInmueble']?></td>								
							</tr>		
							<tr>
								<td class="title"colspan="2">Dirección del dueño del inmueble:</td>
								<td colspan="2"><?php echo utf8_encode($datos['DireccionInmueble'])?></td>
								<td class="title" colspan="2">Teléfono:</td>
								<td colspan="2"><?php echo $datos['TelefonoInmueble']?></td>								
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
						//($datos);
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
		                      			<td>'.$datos['personal'][$id_item][cant].'</td>';
		                      		
		                      		$tab++;	
		                      		
		                      		$total_horas = $datos['personal'][$id_item]['horas'] + ($datos['personal'][$id_item]['min'] / 60 );
		                      		$total = $datos['personal'][$id_item]['cant'] * $total_horas * $post['precio'];
									                      									
									$total_tasa = $total_tasa + $total;     	
		
									if ($datos['personal'][$id_item][horas]>0)
										$str_1 = " ".$datos['personal'][$id_item][horas]."h ";
									else $str_1 ="";
										
									if ($datos['personal'][$id_item][min]>0)
										$str_2 = " ".$datos['personal'][$id_item][min]."m ";
									else $str_2 = "";							
									
		                      		echo '
		                      			<td>'.$str_1.' '.$str_2.'</td>                       			
		                      			<td id="ctasa_'.$id_item.'" '.$class.'>'.$post['precio'].' &euro;</td>
		                      			<td id="p_t_'.$id_item.'" class="subtotal_personal '.$class_style.'">'.number_format($total, 3).' &euro;</td>
		                      		</tr>';	
		                      		
		                      		$tab++;												
							}
						
						$total_parte = $total_parte + $total_tasa;								
						?>                       	                     	
							<tr class="total <?php echo $class_style?>" >								
								<td colspan="4" style="text-align:right">Total Tasas de Personal:</td>
								<td id="total_personal"><?php echo number_format($total_tasa, 3)?> &euro;</td>								
							</tr>						
                      	</table>
                      </fieldset>
	                      
                      <fieldset>
                      <legend>Tasas de vehículos</legend>
                      	<table class="tTasas">
                      		<tr>
	                      		<th>Vehículo</th>	                      		
	                      		<th>Unidades</th>
	                      		<th>Horas</th>	                      		
	                      		<th >Tar. hora</th>
	                      		<th >Coste veh.</th>
	                      		<th>Kms. recorridos</th>
	                      		<th >Tar. Km.</th>
	                      		<th >Coste kms</th>
	                      		<th >Total</th>
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
	                      			<td>'.$datos['vehiculos'][$id_item][cant].'</td>';
	                      		
	                      		$tab++;	
								
	                      		$total_horas = $datos['vehiculos'][$id_item] ['horas'] + ($datos['vehiculos'][$id_item] ['min']/60);                      		
								$coste_vehiculo = $datos['vehiculos'][$id_item]['cant'] * $total_horas * $post['precio'];
								
								$coste_kms = $datos['vehiculos'][$id_item]['kms'] * $item_recorrido;
								$total = $coste_vehiculo + $coste_kms;								
								$total_tasa = $total_tasa + $total;                   		
	                      		
								if ($datos['vehiculos'][$id_item][horas]>0)
									$str_1 = " ".$datos['vehiculos'][$id_item][horas]."h ";
								else $str_1 ="";
									
								if ($datos['vehiculos'][$id_item][min]>0)
									$str_2 = " ".$datos['vehiculos'][$id_item][min]."m ";
								else $str_2 = "";
																	
	                      		echo '
	                      			<td>'.$str_1.' '.$str_2.'</td>  
	                      			<td id="ctasa_'.$id_item.'" '.$class.'>'.$post['precio'].' &euro;</td>     
	                      			<td id="v_t_'.$id_item.'" '.$class.'>'.$coste_vehiculo.' &euro;</td>                 			
	                      			<td>'.$datos['vehiculos'][$id_item][kms].'</td>
	                      			<td class="item_recorrido '.$class_style.'">'.$item_recorrido.' &euro;</td>
	                      			<td id="v_kmtotal_'.$id_item.'" '.$class.'>'.$coste_kms.' &euro;</td>
	                      			<td id="v_sub_'.$id_item.'" class="subtotal_vehiculos '.$class_style.'">'.number_format($total, 3).' &euro;</td>
	                      		</tr>';			
	
	                      		$tab++;									
							}   		   
						
						$total_parte = $total_parte + $total_tasa;
						?>
							<tr class="total <?php echo $class_style?>">							
								<td colspan="8" style="text-align:right">Total Tasas de Vehículos:</td>
								<td id="total_vehiculos"><?php echo number_format($total_tasa, 3)?> &euro;</td>									
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
									
									$total = $datos['materiales'][$id_item]['horas'] * $post['precio'];								
									$total_tasa = $total_tasa + $total;    							
									
		                      		echo '
		                      		<tr>
		                      			<td class="title">'.utf8_encode($post['item_title']).'</td>
		                      			<td>'.$datos['materiales'][$id_item][horas].'</td>
		                      			<td id="ctasa_'.$id_item.'" '.$class.'>'.$post['precio'].' '.$post['unidad'].'</td>
		                      			<td id="m_sub_'.$id_item.'" class="subtotal_materiales '.$class_style.'">'.number_format($total, 3).' &euro;</td>
		                      		</tr>';								
									$tab++;	       								
								}	                      	
							
								$total_parte = $total_parte + $total_tasa;
							?>
								<tr class="total <?php echo $class_style?>">
									<td colspan="3" style="text-align:right">Total Tasas de Materiales:</td>
									<td id="total_materiales"><?php echo number_format($total_tasa)?> &euro;</td>								
								</tr>
	                      	</table>                        
	                      </fieldset>        
		
	                      <fieldset >
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
	                      
	                                                                             
	                      <fieldset >                    
	                      	<table class="tTasas">
	                      		<tr>
		                      		<th>Comentarios:</th>	                      		
	                      		</tr>                      
	                      		<tr>
	                      			<td><?php echo utf8_encode($datos['comentarios'])?></td>
	                      		</tr>
	                      	</table> 
	                      </fieldset>	   
	                                         
	                      <?php $tab++?>                     					
						</div>
				</div>
				<div class="clr"><hr /></div>
			</div>
		</div>
		
	</div>
	<!-- // Contenido -->
