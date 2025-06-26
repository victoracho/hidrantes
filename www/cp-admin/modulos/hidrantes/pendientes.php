<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );

		
	// Filtro.
	if (strlen($_GET['buscar'])>0)
	{
		$buscar=strip_tags($_GET['buscar']);		
		$buscarpor=strip_tags($_GET['buscarpor']);
		$chkobs	=$_GET['chkobs'];
		$where="";
		if (!empty($buscar))
		{
			$resFiltrado="<span class='filtrados'>Resultados filtrados.</span>";
			switch ($buscarpor)
			{
				case "1":$where.="codigo like '%".$buscar."%' and ";
					break;
				case "2":$where.="municipio like '%".$buscar."%' and ";;
					break;
			}
		}		
	}

	if (isset($_GET['limpiar']))
	{
		$buscar="";
		$buscarpor="1";
		$where="";
			
	}
	
?>
	<!-- Contenido -->
	<div id="content">

		<!-- Encabezado -->
		<?
		/*<div class="header-doc">
			<h1><span><?=$str_lang['LANG_HEADER_TAB_PENDIENTES']?></span></h1>			
			<div class="clr"><hr /></div>
		<div class="clr"><hr /></div>				
		</div>*/
		?>
		<?php
		//Paginacion
					
					
	
		
		
		$idparque=0;
		if (is_jefeparque())
			$idparque = $_SESSION['HIDRANTES']['parque_id'];
		elseif ($_GET['chkidparque']>0)
			$idparque=$_GET['chkidparque'];

		?>
	
			<!-- Filtros -->
			<div class="search">			
				<form name="frm" action="<?php echo $action_form?>" method="GET">
					<!-- Select -->
					<table>
					<tr>
					<td><?=$str_lang['LANG_HIDRANTES_BUSCAR_PARQUE']?></td>
					
					<td>					
						<select id="chkidparque"  name="chkidparque" >
							
							<?php
							$vector=$bd->get_all("parques");
							echo cargarCombo($idparque,$vector,"parque_id","parque");
							?>
						</select>
					</td>
					
					<td>
						<input type="submit" value="<?=$str_lang['LANG_HIDRANTES_BUSCAR']?>" class="button" name="filter"/> <input type="submit" value="<?=$str_lang['LANG_HIDRANTES_LIMPIAR']?>" class="button" name="limpiar"/>
					</td>
					</tr>
					<?php
					if ($resFiltrado!="")
					{
						echo '<tr><td colspan="3">'.$resFiltrado.'</td></tr>';
					}
					?>
					</table>	
					<div class="clr"><hr /></div>
				</form>
			</div>	
			
			<!-- // Filtros -->			
			
		<!-- // Encabezado -->
		<form name="frm" action="<?php echo $action_form?>" method="POST">		
		
			<div class="filter">
					<!-- Borrar -->
					<br />						
			<div class="clr"><hr /></div>		
			</div>
	
		<?php
     	$sql2="call P_GetTotalPendientes($idparque)";
	
		$result2 = $bd_mysqli->query($sql2);
		$total= $result2->num_rows;
		if($bd_mysqli->more_results())
		{
			$bd_mysqli->next_result();
		}
		mysqli_free_result($result2);
		$result2->close();
		$limit = pagination_cant;
			$page = (isset($_GET['page']))?$_GET['page']:1;					
		
			$pager  = Pager::getPagerData($total, $limit, $page);			
			$offset = $pager->offset;
			$limit  = $pager->limit;
			$page   = $pager->page;	
		$sql="call P_GetHidrantesPendientes($idparque,$offset,$limit)";
		
		if ($result = $bd_mysqli->query($sql)) {
						
			$p = new pagination;
			$p->Items($total);
			$p->limit($limit);
			$p->currentPage($page);	
						
		?>
		
		<!-- Paginas -->
		<div class="summary-list">			
								<!-- Paginacion -->
			<div class="page-nav2">
				  <?php
					if (!empty($p))
					{
						$p->nextLabel('<strong>&raquo;</strong>');//changing next text
						$p->prevLabel('<strong>&laquo;</strong>');//changing previous text
						$p->nextIcon('');//removing next icon
						$p->prevIcon('');//removing previous icon
						if (!empty($params))
						{				
							$target=implode("&",$params);
							$str="&page=".$_GET['page'];								
							$target=str_replace($str,"",$target);
							$p->target("?".$target);
						}			
						$p->show();
					}
					?>  

			</div>
	<!-- // Paginacion -->
			
				<table id="table-list-tasas" class="tsort">
					<thead>
						<tr>
							
							<th class="first" style="text-align:left;"><?=$str_lang['LANG_HIDRANTES_FOTO']?></th>
							<th class="headerSortDown"><?=$str_lang['LANG_HIDRANTES_CODIGO']?></th>
							<th class="municipio"><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?></th>							
							<th><?=$str_lang['LANG_MUNICIPIOS_COL_DIRECCION']?></th>									
							<th><?=$str_lang['LANG_HIDRANTES_FECHAREVISION']?></th>		
							<th class=""><?=$str_lang['LANG_USERS_COL_ACCIONES']?></th>
						</tr>
					</thead>
					<tbody>
					<?php
						while($obj = $result->fetch_object())
						{
							
											
								echo '<tr>';
						
									$vfotos=$bd->get_all_by_filter("fotos","hidrante_id=".$obj->hidrante_id);
						
									$n=count($vfotos);
									echo'<td style="width:6%;">';
									if ($n>0)								
										echo '<img src="'.imgadminpath.'ico-estado-ok.gif" />';
									else
										echo '<img src="'.imgadminpath.'no-ok.gif" />';
									echo'</td>';
									echo'<td style="width:10%;">'.$obj->codigo.'</td>
									<td>'.$obj->municipio.'</td>
									<td class="direccion">'.$obj->calle.' '.$obj->edificio.'</td>		
									<td style="width:10%;">'.convert_date($obj->fecharevision).'</td>										
									<td>';								
									echo '<a href="'.$admin_path.$modulo.'/edit/'.$obj->hidrante_id.'/'.$_GET["page"].'/" class="general-button">'.$str_lang['LANG_DASH_VER'].'</a>';								
									echo'
									</td> 				
								</tr>';								
							
						}
						//<a href="'.$admin_path.$modulo.'/view/'.$post['municipio_id'].'/" class="general-button ver">Ver</a>
						?>
					</tbody>
				</table>
				
			
			
		</div>
		</form>
		<!-- // Paginas -->
		<?php
		}else{
		echo '<b>'.$str_lang['LANG_MSG_ERROR19'].'.</b>';
			
		}
		?>
	</div>
	
	<?php
	$result->close();
    unset($obj);
    unset($sql);
    unset($query); 
	?>
	<!-- // Contenido -->
		<!-- Paginacion -->
	<div class="page-nav">
          <?php
          	if (!empty($p))
          	{
				$p->nextLabel('<strong>&raquo;</strong>');//changing next text
				$p->prevLabel('<strong>&laquo;</strong>');//changing previous text
				$p->nextIcon('');//removing next icon
				$p->prevIcon('');//removing previous icon
				if (!empty($params))
				{				
					$target=implode("&",$params);
					$str="&page=".$_GET['page'];								
					$target=str_replace($str,"",$target);
					$p->target("?".$target);
				}			
				$p->show();
          	}
			?>  
		<div class="clr"><hr /></div>
	</div>
