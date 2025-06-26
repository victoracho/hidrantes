<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );	

// Filtro.
	$resFiltrado="";
	if (strlen($_GET['buscar'])>0)
	{
		$buscar=strip_tags($_GET['buscar']);		
		$where="";
		if (!empty($buscar))
		{
			$resFiltrado="<span class='filtrados'>Resultados filtrados.</span>";
			$where.="numeroregistro like '%".$buscar."%' and ";
		
			
		
		}		
	}
	if (isset($_GET['limpiar']))
	{
		$buscar="";
		$buscarpor="1";
		$where="";
		$resFiltrado="";
			
	}
?>
	<!-- Contenido -->
	<div id="content">
		
		<!-- Encabezado -->
		<div class="header-doc">
			<h1><span><?=$str_lang['LANG_HISTORICO_TITLE_LIST']?></span></h1>			
		<div class="clr"><hr /></div>				
		</div>
		

		
				
			
		<!-- // Encabezado -->
						<!-- Filtros -->
			<div class="search">			
				<form name="frm" action="<?php echo $action_form?>" method="GET" onsubmit="">
					<!-- Select -->
					<table>
					<tr>
					<td><?=$str_lang['LANG_CARTAS_NREGISTRO']?></td>
					<td>					
						<input type="text" name="buscar" id="buscar" value="<?=$buscar?>" maxlength="100" />
					</td>
					<?php
					/*<td>por</td>
					<td>					
						<select id="buscarpor"  name="buscarpor" style="width:150px;">
							<option value="1" <?php if ($buscarpor=="1") echo 'selected="selected"' ?>><?=$str_lang['LANG_HIDRANTES_CODHIDRANTE']?></option>
							<option value="2" <?php if ($buscarpor=="2") echo 'selected="selected"' ?>><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?></option>					
						</select>
					</td>*/
					?>
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

				</form>
				<div class="clr"><hr /></div>	
			</div>	<div class="clr"><hr /></div>	
		<!-- Paginas -->
		<div class="summary-list">			

			
			<!-- // Filtros -->		
				<table id="table-list-tasas" class="tsort">
					<thead>
						<tr>					
							<th class="first" style="width:1px;" ></th>	
							<th  style="width:15%;"><?=$str_lang['LANG_HIDRANTES_CODIGO']?></th>
														
							<th style="width:15%;"><?=$str_lang['LANG_CARTAS_FECHAR']?></th>		
							<th style="width:15%;"><?=$str_lang['LANG_CARTAS_NREGISTRO']?></th>							
							<th style="width:15%;"><?=$str_lang['LANG_CARTAS_INTERNO']?></th>	
							<th class="headerSortDown" style="width:30%;"><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?></th>							
							<th style="width:10%;"><?=$str_lang['LANG_USERS_COL_ACCIONES']?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					//Paginacion
					$limit = pagination_cant;
					$page = $_GET['page'];					
					$where.=" 1=1";
					$total=$bd->getcounttable('V_CartasHidrante',$where);
					$pager  = Pager::getPagerData($total, $limit, $page);			
					$offset = $pager->offset;
					$limit  = $pager->limit;
					$page   = $pager->page;				
					$p = new pagination;
					$p->Items($total);
					$p->limit($limit);
					$p->currentPage($page);	
					$tPost=$bd->getlisttable("V_CartasHidrante",$where,"municipio ASC, ncarta asc",$offset,$limit);
						if (!empty($tPost))
						foreach ($tPost as $post)
						{							
							echo '
							<tr>	
								<td></td>
								<td>'.$post['codigo'].'</td>															
								<td>'.$post['fecharegistro'].'</td>									
								<td>'.$post['numeroregistro'].'</td>
								<td>'.$post['interno'].'</td>		
								<td>'.$post['municipio'].'</td>								
								<td>';
								
									echo '<a href="'.$admin_path.$modulo.'/cartas/'.$post['hidrante_id'].'/'.$_GET['page'].'/" class="general-button">'.$str_lang['LANG_DASH_VER'].'</a>';
								echo'
								</td> 				
							</tr>';								
						}
						//<a href="'.$admin_path.$modulo.'/view/'.$post['municipio_id'].'/" class="general-button ver">Ver</a>
						?>
					</tbody>
				</table>
				
			</form>
			
		</div>
	<!-- // Contenido -->
	<br />
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
	</div>
	
