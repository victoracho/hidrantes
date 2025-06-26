<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );

	// Borrar elementos.
	if (isset($_POST['del']))
	{
		if (!empty($_POST['borrar']))
		foreach ($_POST['borrar'] as $borrar)
		{
			$id=$borrar;
			
			// Borro el post2lang
			$tabla1='hidrante_comentarios hc 
			inner join comentarios c on(hc.comentario_id=c.comentario_id)';
			$where1='hc.hidrante_id='+$id;
			$filas=$bd->get_all_by_filter($tabla1,$where1);
			$n=count($filas);
					
			for ($i=0;$i<$n;$i++)
			{
				$bd->delete("comentarios","comentario_id", $filas[$i]["comentario_id"]);	
		
			}
			
						
			$bd->delete("hidrante_comentarios","hidrante_id", $id);	
			$vector=$bd->get_all_by_filter("fotos","hidrante_id=".$id);
			$n=count($vector);
							
			for ($i=0;$i<$n;$i++)
			{
				unlink(uploadpathadmin."new/new_".$vector[$i]['foto']);
				unlink(uploadpathadmin."mini/mini_".$vector[$i]['foto']);
		
			}
			
			$bd->delete("fotos","hidrante_id", $id);	
			
			$bd->delete("hidrantes","hidrante_id", $id);						
			// Genero mensaje 
			$msg=$str_lang['LANG_MSG_OK2'];
						
		}
		// Todo ok.
		echo '<div id="success"><p>'.$msg.' '.$msg2.'</p></div>';				
	}
	
	
	// Filtro.
	/*if (isset($_GET['filter']))
	{
		$buscar=strip_tags($_GET['buscar']);		
		$buscarpor=strip_tags($_GET['buscarpor']);
		$chkobs	=$_GET['chkobs'];
		$where="";
		if (!empty($buscar))
		{
			switch ($buscarpor)
			{
				case "1":$where.="codigo like '%".$buscar."%' and ";
					break;
				case "2":$where.="municipio like '%".$buscar."%' and ";;
					break;
			}
		}		
	}*/

	// Filtro.
	$resFiltrado="";
	if (strlen($_GET['buscar'])>0)
	{
		$buscar=strip_tags($_GET['buscar']);	
		$buscarpor=strip_tags($_GET['buscarpor']);
		$where="";
		if (!empty($buscar))
		{
			switch ($buscarpor)
			{
				case "1":$where.="codigo like '%".$buscar."%' and ";
					break;
				case "2":$where.="municipio like '%".$buscar."%' and ";;
					break;
			}
			$resFiltrado="<span class='filtrados'>Resultados filtrados.</span>";
			/*$where.="(codigo like '%".$buscar."%' or ";
			$where.="municipio like '%".$buscar."%' or ";
			$where.="calle like '%".$buscar."%' or ";
			$where.="edificio like '%".$buscar."%') and ";
			*/
		
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
		<div class="header-doc">
			<h1><?=$str_lang['LANG_HIDRANTES_TITLE_LIST']?></h1>
			<?php
			if (!is_oficialjefe() && !is_uno_uno_dos() && !is_consorcio())
			{
			?>
			<p class="head-button"><a href="<?php echo $admin_path?>hidrantes/insert/<?=$_GET["page"]?>/"><span><?=$str_lang['LANG_HIDRANTES_ADD']?></span></a></p>
			<?php
			}
			?>
			<p class="head-button"><a href="<?php echo $admin_path?>hidrantes/export/<?=$_GET["page"]?>/"><span><?=$str_lang['LANG_HIDRANTES_EXPORTAR_COORDENADAS']?></span></a></p>
			
			<div class="clr"><hr /></div>
		<div class="clr"><hr /></div>				
		</div>
		<?php
		//Paginacion
					$limit = pagination_cant;
					$page = $_GET['page'];					
					$where.=" 1=1";
					if (is_jefeparque())
						$where.=" and parque_id=".$_SESSION['HIDRANTES']['parque_id'];
					$total=$bd->getcounttable('V_hidrantes',$where);
					$pager  = Pager::getPagerData($total, $limit, $page);			
					$offset = $pager->offset;
					$limit  = $pager->limit;
					$page   = $pager->page;				
					$p = new pagination;
					$p->Items($total);
					$p->limit($limit);
					$p->currentPage($page);	
					

		$tPost=$bd->getlisttable("V_hidrantes",$where,"codigo ASC",$offset,$limit);
		if (count($tPost)!=0)
		{

		?>
	
			<!-- Filtros -->
			<div class="search">			
				<form name="frm" action="<?php echo $action_form?>" method="GET" onsubmit="">
					<!-- Select -->
					<table>
					<tr>
					<td><?=$str_lang['LANG_HIDRANTES_BUSCAR']?></td>
					<td>					
						<input type="text" name="buscar" id="buscar" value="<?=$buscar?>" maxlength="100" />
					</td>
					<td>por</td>
					<td>					
						<select id="buscarpor"  name="buscarpor" style="width:150px;">
							<option value="1" <?php if ($buscarpor=="1") echo 'selected="selected"' ?>><?=$str_lang['LANG_HIDRANTES_CODHIDRANTE']?></option>
							<option value="2" <?php if ($buscarpor=="2") echo 'selected="selected"' ?>><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?></option>					
						</select>
					</td>
					
					<td>
						&nbsp;<input type="submit" value="<?=$str_lang['LANG_HIDRANTES_BUSCAR']?>" class="button" name="filter"/> <input type="submit" value="<?=$str_lang['LANG_HIDRANTES_LIMPIAR']?>" class="button" name="limpiar"/>
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
						<?php
						
						if (is_admin() || is_gerente())	
						{
						?>
					<input type="submit" value="<?=$str_lang['LANG_USERS_DEL']?>" class="button"  name="del"/>
					<?php
					}
						?>
			<div class="clr"><hr /></div>		
			</div>
		
	
		
		
	
		
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
							$p->target("/cp/hidrantes//?".$target);
						}			
						$p->show();
					}
					?>  

			</div>
	<!-- // Paginacion -->
			
				<table id="table-list-tasas" class="tsort">
					<thead>
						<tr>
							<th class="first" title="<?=$str_lang['LANG_MUNICIPIOS_SELALL']?>">
							
							<?php
							if (!is_oficialjefe() && !is_uno_uno_dos() && !is_consorcio()){
							?>
							<input type="checkbox" id="check_all" />
							<?php
							}
							?>
							</th>					
							<th style="text-align:left;"><?=$str_lang['LANG_HIDRANTES_FOTO']?></th>
							<th class="headerSortDown"><?=$str_lang['LANG_HIDRANTES_CODIGO']?></th>
							<th class="municipio"><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?></th>							
							<th><?=$str_lang['LANG_MUNICIPIOS_COL_DIRECCION']?></th>									
							<th class="acciones"><?=$str_lang['LANG_USERS_COL_ACCIONES']?></th>
						</tr>
					</thead>
					<tbody>
					<?php
						if (!empty($tPost))
						{	
							/*if (count($tPost)==1)
							{
								//echo $admin_path.$modulo.'/view/'.$tPost[0]['hidrante_id'];							
								
								$op="1";								
								Location ($admin_path.$modulo.'/view/'.$tPost[0]['hidrante_id']."/");
								exit();
							}
							*/
				
						
							foreach ($tPost as $post)
							{		
								
								
								
								echo '
								<tr>
									<td class="first">';
				
									if (!is_oficialjefe() && !is_uno_uno_dos() && !is_consorcio())
									{			
										echo'<input type="checkbox" name="borrar[]" class="list_del" value="'.$post['hidrante_id'].'" />';
									}
									echo'</td>';
									$vfotos=$bd->get_all_by_filter("fotos","hidrante_id=".$post['hidrante_id']);
						
									$n=count($vfotos);
									echo'<td style="width:6%;">';
									if ($n>0)								
										echo '<img src="'.imgadminpath.'ico-estado-ok.gif" />';
									else
										echo '<img src="'.imgadminpath.'no-ok.gif" />';
									echo'</td>';
									echo'<td style="width:10%;">'.$post['codigo'].'</td>
									<td>'.$post['municipio'].'</td>
									<td class="direccion">'.$post['calle'].' '. $post['edificio'].'</td>						
									<td>';								
									if (!is_consorcio())
										echo '<a href="'.$admin_path.$modulo.'/view/'.$post['hidrante_id'].'/'.$_GET["page"].'/" class="general-button">'.$str_lang['LANG_DASH_VER'].'</a>';
									if (!is_jefeguardia() && !is_consorcio() && !is_oficialjefe() && !is_uno_uno_dos())
										echo '&nbsp;<a href="'.$admin_path.$modulo.'/edit/'.$post['hidrante_id'].'/'.$_GET["page"].'/" class="general-button">'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';
									
									echo '&nbsp;<a href="'.$admin_path.$modulo.'/comment/'.$post['hidrante_id'].'/'.$_GET["page"].'/" class="general-button-long">'.$str_lang['LANG_HEADER_TAB_COMENTARIOS'].'</a>';
									if (is_gerente() || is_consorcio() || is_admin())
										echo '&nbsp;<a href="'.$admin_path.$modulo.'/cartas/'.$post['hidrante_id'].'/" class="general-button">'.$str_lang['LANG_HEADER_TAB_CARTAS'].'</a>';
									echo'
									</td> 				
								</tr>';								
							}
						}
						//<a href="'.$admin_path.$modulo.'/view/'.$post['municipio_id'].'/" class="general-button ver">Ver</a>
						?>
					</tbody>
				</table>
				
			</form>
			
		</div>
		<!-- // Paginas -->
		<?php
		}else{
		echo '<b>'.$str_lang['LANG_MSG_ERROR19'].'.</b>';
			echo'<div class="header-doc">			
			<p class="head-button"><a href="'.$admin_path.'hidrantes/"><span>'.$str_lang['LANG_HIDRANTES_TITLE_VOLVER'].'</span></a></p>			
		<div class="clr"><hr /></div>				
		</div>';
		}
		?>
	</div>
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
					$p->target("/cp/hidrantes//?".$target);
				}			
				$p->show();
          	}
			?>  
		<div class="clr"><hr /></div>
	</div>