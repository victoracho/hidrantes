<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );

	// Borrar elementos.
	if (isset($_POST['del']))
	{
		if (!empty($_POST['borrar']))
		foreach ($_POST['borrar'] as $borrar)
		{
			$id=$borrar;
			
			// Borro el post2lang
			
			$bd->delete("municipios","municipio_id", $id);						
			// Genero mensaje 
			$msg=$str_lang['LANG_MSG_OK2'];
								
		}
		// Todo ok.
		echo '<div id="success"><p>'.$msg.' '.$msg2.'</p></div>';				
	}
	
	
	// Filtro.
/*	if (isset($_GET['filter']))
	{
		$f_status=strip_tags($_GET['status']);		
		
		if (!empty($f_status))
		{
			/*
			 * Para que el gestor/administrativo no acceda a que tasas
			 * son retribuibles o no.
			 */ 
			
		/*	if (is_gestor())
				$where = "status='".$f_status."' and status<=4 and";  	
			else $where=" status='".$f_status."' and";		
		}
	}
	else
	{
		if (is_gestor()) $where = "(status=1 or status=2 or status=4) and";  
	}
	*/
?>
	<!-- Contenido -->
	<div id="content">
		
		<!-- Encabezado -->
		<div class="header-doc">
			<h1><?=$str_lang['LANG_MUNICIPIOS_TITLE_LIST']?></h1>
			<?php
			if (!is_consorcio() && !is_oficialjefe() && !is_uno_uno_dos())
			{
			
			echo '<p class="head-button"><a href="'.$admin_path.'municipios/insert/"><span>'.$str_lang['LANG_MUNICIPIOS_TITLE_INSERT'].'</span></a></p>';
			
			}
			?>
		<div class="clr"><hr /></div>				
		</div>
		

		
			<!-- Filtros -->
			<!--<div class="search">			-->
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
	<!-- 		</div>-->	
			
			<!-- // Filtros -->			
			
		<!-- // Encabezado -->
		<?php
		if (!is_consorcio()  && !is_oficialjefe() && !is_uno_uno_dos() )
		{
		?>		
		<form name="frm" action="<?php echo $action_form?>" method="POST">		
		<div class="filter">
				<!-- Borrar --><br />
					<input type="submit" value="<?=$str_lang['LANG_USERS_DEL']?>" class="button"  name="del"/>
		<div class="clr"><hr /></div>		
		</div>
		<?php
		}
		?>
		<!-- Paginas -->
		<div class="summary-list">			
				
				<table id="table-list-tasas" class="tsort">
					<thead>
						<tr>
							<th class="first" title="<?=$str_lang['LANG_MUNICIPIOS_SELALL']?>">
							<?php
							if (!is_oficialjefe() && !is_consorcio() && !is_uno_uno_dos())
							{
							?>
							<input type="checkbox" id="check_all" />
							<?php
							}
							?>
							</th>					
							<th class="headerSortDown" style="width:4%;"><?=$str_lang['LANG_MUNICIPIOS_COL_CODMUN']?></th>
							<th style="width:4%;"><?=$str_lang['LANG_MUNICIPIOS_COL_CP']?></th>							
							<th><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?></th>		
							<th style="width:4%;"><?=$str_lang['LANG_MUNICIPIOS_COL_EMPRESA']?></th>							
							<th><?=$str_lang['LANG_MUNICIPIOS_COL_CONTACTO']?></th>
							<th><?=$str_lang['LANG_USERS_COL_TLF']?></th>
							<th style="width:20%;"><?=$str_lang['LANG_USERS_COL_EMAIL']?></th>							
							<th style="width:1%;"><?=$str_lang['LANG_USERS_COL_ACCIONES']?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					//Paginacion
					$limit = pagination_cant;
					$page = $_GET['page'];					
					$where.=" 1=1";
					$total=$bd->getcounttable('municipios',$where);
					$pager  = Pager::getPagerData($total, $limit, $page);			
					$offset = $pager->offset;
					$limit  = $pager->limit;
					$page   = $pager->page;				
					$p = new pagination;
					$p->Items($total);
					$p->limit($limit);
					$p->currentPage($page);	
					$tPost=$bd->getlisttable("municipios",$where,"codigo ASC",$offset,$limit);
						if (!empty($tPost))
						foreach ($tPost as $post)
						{							
							echo '
							<tr>
								<td class="first">';
								if (!is_oficialjefe() && !is_consorcio() && !is_uno_uno_dos())
									echo'<input type="checkbox" name="borrar[]" class="list_del" value="'.$post['municipio_id'].'" />';
								echo'</td>
								<td>'.$post['codigo'].'</td>
								<td>'.$post['cp'].'</td>
								<td>'.$post['municipio'].'</td>
								<td>'.$post['empresa'].'</td>									
								<td>'.$post['contacto'].'</td>
								<td>'.$post['telefono'].'</td>								
								<td>'.$post['email'].'</td>								
								<td>';
								if (!is_consorcio() && !is_oficialjefe() && !is_uno_uno_dos())
								{										
								echo '<a href="'.$admin_path.$modulo.'/edit/'.$post['municipio_id'].'/'.$_GET['page'].'/" class="general-button">'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';								
								}else
									echo '<a href="'.$admin_path.$modulo.'/edit/'.$post['municipio_id'].'/'.$_GET['page'].'/" class="general-button">'.$str_lang['LANG_DASH_VER'].'</a>';
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