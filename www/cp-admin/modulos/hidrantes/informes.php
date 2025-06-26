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
			<h1><span><?=$str_lang['LANG_HEADER_TAB_INFORMES']?></span></h1>			
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
					
		$tPost=$bd->getlisttable("V_hidrantes",$where,"codigo ASC",$offset, '100000');
		if (count($tPost)!=0)
		{
		?>
	</div>
		<!-- Paginas -->
		<div class="summary-list">			
				<table id="example">
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
						?>
					</tbody>
				</table>
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
	</div>
	</div>
	<!-- // Contenido -->
  <script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        $('#example').dataTable({
        });
    } );
  </script>

