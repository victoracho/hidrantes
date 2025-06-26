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
		<div class="header-doc">
			<h1><?=$str_lang['LANG_HEADER_TAB_INFORMES']?></h1>			
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
				<form name="frm" action="<?php echo $action_form?>" method="GET">
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
					<td ><input type="checkbox" name="chkobs" id="chkobs" value="1" <?php if ($chkobs=="1") echo '"checked"'?>/> <label for="chkobs"><?=$str_lang['LANG_HIDRANTES_OBS']?></label>&nbsp;&nbsp;
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
							<th class=""><?=$str_lang['LANG_USERS_COL_ACCIONES']?></th>
						</tr>
					</thead>
					<tbody>
					<?php
						if (!empty($tPost))
						{	
							if (count($tPost)==1)
							{
								//echo $admin_path.$modulo.'/view/'.$tPost[0]['hidrante_id'];							
								
								$op="1";
								if (empty($_GET["chkobs"]))
									$op="0";
								Location ($admin_path.$modulo.'/view/'.$tPost[0]['hidrante_id']."/".$op);
								exit();
							}
						
				
						
							foreach ($tPost as $post)
							{		
								
								
								
								echo '<tr>';
						
									$vfotos=$bd->get_all_by_filter("fotos","hidrante_id=".$post['hidrante_id']);
						
									$n=count($vfotos);
									echo'<td style="width:6%;">';
									if ($n>0)								
										echo '<img src="'.imgadminpath.'ico-estado-ok.gif" />';
									else
										echo "&nbsp;";
									echo'</td>';
									echo'<td style="width:10%;">'.$post['codigo'].'</td>
									<td>'.$post['municipio'].'</td>
									<td class="direccion">'.$post['calle'].' '. $post['edificio'].'</td>						
									<td>';								
									echo '<a href="'.$admin_path.$modulo.'/ficha/'.$post['hidrante_id'].'/'.$_GET["page"].'/'.$_GET['chkobs'].'/" class="general-button">'.$str_lang['LANG_DASH_VER'].'</a>';								
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
			<p class="head-button"><a href="<?php echo $admin_path?>hidrantes/"><span>'.$str_lang['LANG_HIDRANTES_TITLE_VOLVER'].'</span></a></p>			
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
					$p->target("?".$target);
				}			
				$p->show();
          	}
			?>  
		<div class="clr"><hr /></div>
	</div>