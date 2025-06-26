<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );

	// Borrar elementos.
	if (isset($_POST['del']))
	{
		if (!empty($_POST['borrar']))
		foreach ($_POST['borrar'] as $borrar)
		{
			$id=$borrar;
			
			// Para editar tengo que recuperar los datos.
			$action_edit=$bd->get_all_by_id("tasas","id_tasa",$id);
			
				// Si no existe el id, salgo. Si es id=1, salgo tb. es por defecto.					
				if (!empty($action_edit))
				{
					if ($action_edit['status']>1)
					{
						// Borro el post2lang
						$campos=array('status'=>6); // 6=> Como desactivado.
						$bd->update("tasas", $campos, "id_tasa", $id);
						
						// Genero mensaje 
						$msg="Los datos han sido eliminados.";
					}
					else $msg2=" Al menos una de las tasas seleccionadas, no estaba generada y no se ha elimininado";
				}										
		}
		// Todo ok.
		echo '<div id="success"><p>'.$msg.' '.$msg2.'</p></div>';				
	}
	
	// Filtro.
	if (isset($_GET['filter']))
	{
		$f_status=strip_tags($_GET['status']);		
		
		if (!empty($f_status))
		{
			/*
			 * Para que el gestor/administrativo no acceda a que tasas
			 * son retribuibles o no.
			 */ 
			
			if (is_gestor())
				$where = "status='".$f_status."' and status<=4 and";  	
			else $where=" status='".$f_status."' and";		
		}
	}
	else
	{
		if (is_gestor()) $where = "(status=1 or status=2 or status=4) and";  
	}
?>
	<!-- Contenido -->
	<div id="content">
		
		<!-- Encabezado -->
		<div class="header-doc">
			<h1>Listado de Tasas</h1>			
			<!-- Filtros -->
			<div class="search">			
		<?php 	/*	<form name="frm" action="<?php echo $action_form?>" method="GET">
					<!-- Select -->
						<select id="status"  name="status">
							<option value="" selected="selected">Mostrar todas las tasas</option>
							<option value="1" <?php if ($f_status=="1") echo 'selected="selected"' ?>>Sin terminar</option>
							<option value="2" <?php if ($f_status=="2") echo 'selected="selected"' ?>>Tasa no verificada</option>
							<option value="3" <?php if ($f_status=="3") echo 'selected="selected"' ?>>Tasa verificada</option>
							<?php 
							if (is_admin())
							{
							?>
							<option value="4" <?php if ($f_status=="4") echo 'selected="selected"' ?>>Tasa cerrada retribuible</option>
							<option value="5" <?php if ($f_status=="5") echo 'selected="selected"' ?>>Tasa cerrada no retribuible</option>
							<option value="6" <?php if ($f_status=="6") echo 'selected="selected"' ?>>Tasa borrada/no válida</option>
							<?php }?>							
						</select>
	
						<input type="submit" value="Filtrar" class="button" name="filter"/>
					<div class="clr"><hr /></div>
				</form>*/?>
			</div>
			
			<!-- // Filtros -->			
			<div class="clr"><hr /></div>
		</div>
		<!-- // Encabezado -->
			
		<form name="frm" action="<?php echo $action_form?>" method="POST">		
		<div class="filter">
				<!-- Borrar -->
					<input type="submit" value="Borrar" class="button"  name="del"/>
		<div class="clr"><hr /></div>		
		</div>
		
		<!-- Paginas -->
		<div class="summary-list">			
				
				<table id="table-list-tasas" class="tsort">
					<thead>
						<tr>
							<th class="first" title="Selecciona todos"><input type="checkbox" id="check_all" /></th>					
							<th>Cod. Parte</th>
							<th>Cerrado por</th>
							<th>F.A Tasa</th>
							<th>Usuario Alta</th>
							<th>F.M. Tasa</th>
							<th>Usuario Mod.</th>							
							<th>Estado</th>							
							<th>Comentarios</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php
					//Paginacion
					$limit = pagination_cant;
					$page = $_GET['page'];					
					$where.=" 1=1";
					$total=$bd->getcounttable('tasas',$where);
					$pager  = Pager::getPagerData($total, $limit, $page);			
					$offset = $pager->offset;
					$limit  = $pager->limit;
					$page   = $pager->page;				
					$p = new pagination;
					$p->Items($total);
					$p->limit($limit);
					$p->currentPage($page);	
					$tPost=$bd->getlisttable("tasas",$where,"fecha_alta DESC",$offset,$limit);
						if (!empty($tPost))
						foreach ($tPost as $post)
						{
							$anio =  substr($post['id_parte'], 0, 4);
							$regAnual = substr($post['id_parte'], 4);
																			
							$wherePartes = "Anio =".entrada_sql($anio)." and RegAnual=".entrada_sql($regAnual)."";
							$tPostPartes = $bd->getlisttable("partesdiarios",$wherePartes,$orderPartes,$offsetPartes,$limitPartes);
							$postPartes = $tPostPartes[0];
							
							switch ($post['status']) {
								case 1:
									$estado = "Sin terminar";
									
									/* Para partes que estan cerrados y no aparecen como no verificado */
									/*if ($postPartes['Cerrado']==2){
										$campos = array ("status"=>2);
										$bd->update("tasas", $campos, "id_tasa", $post['id_tasa']);
									}*/
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
							$content=strip_tags($post['comentarios']);
							$content=utf8_encode(substr($content, 0, 100));
							if (empty($content)) $content = "No hay comentarios";
							
							if ($postPartes['Cerrado']==2)
								$cerrado = utf8_encode(get_table_value("usuarios", "FullName", "UserID", $postPartes['Owner']));
							else $cerrado = "Sin cerrar";
							echo '
							<tr>
								<td class="first"><input type="checkbox" name="borrar[]" class="list_del" value="'.$post['id_tasa'].'" /></td>
								<td>'.$post['id_parte'].'</td>
								<td>'.$cerrado.'</td>
								<td>'.convert_date($post['fecha_alta']).'</td>
								<td>'.utf8_encode(get_table_value("usuarios", "FullName", "UserID", $post['userid_alta'])).'</td>
								<td>'.convert_date($post['fecha_mod']).'</td>
								<td>'.utf8_encode(get_table_value("usuarios", "FullName", "UserID", $post['userid_mod'])).'</td>								
								<td>'.$estado.'</td>
								<td>'.$content.'</td>
								<td><a href="'.$admin_path.$modulo.'/view/'.$post['id_tasa'].'/" class="general-button ver">Ver</a>';
							
								if ($post['status']!=1)
								echo '<a href="'.$admin_path.$modulo.'/edit/'.$post['id_tasa'].'/" class="general-button">Editar</a>';
								echo'
								</td> 				
							</tr>';								
						}
						?>
					</tbody>
				</table>
				
			</form>
			
		</div>
		<!-- // Paginas -->
		
	</div>
	<!-- // Contenido -->