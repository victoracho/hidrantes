<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
$page=get_id_by_uri2();

	$action_edit=$bd->get_all_by_id("hidrantes","hidrante_id",$id);
	$datos_hidrante= $action_edit;
	
	// Si no existe el id, salgo.				
	if (empty($action_edit))
		{
			Location($admin_path.$modulo."/");
			exit();
		}


?>

<script type="text/javascript">
	
	function openpdf(carta_id)
	{
		$("#carta_id").val(carta_id);
		$("#frm").attr('target', '_blank'); 
		$("#frm").submit();
	
	}
	

</script>

<?php
	
	if (isset($_POST["carta_id"]))
	{
		$carta_id=$_POST["carta_id"];
		if ($carta_id>0)
		{
			ob_end_clean();
			dl_file(uploadpathadmin.'cartas/carta_'.$id.'_'.$carta_id.'.pdf');
			exit;
		}
	}
?>
	
	<!-- Contenido -->
	<div id="content">
		<!-- Encabezado -->
		<div class="header-doc">
			<h1><span><?=$str_lang['LANG_HEADER_TAB_CARTAS']?></span></h1>	
            <div class="clr"><hr /></div>
			<?php
			if (!is_consorcio())
			{
			?>
			<p class="head-button"><a href="<?php echo $admin_path?>hidrantes//?&page=<?=$page?>"><span><?=$str_lang['LANG_HIDRANTES_TITLE_VOLVER']?></span></a></p>			
			<?php
			}else{
				echo'<p class="head-button"><a href="'.$admin_path.'hidrantes/listadocartas/?&page='.$page.'"><span>'.$str_lang['LANG_LISTADO_TITLE_VOLVER'].'</span></a></p>';
			}
			?>
			<div class="clr"><hr /></div>
		</div>
		
		
		<div class="client-detail">
			<div class="orders">	
				<h2><?=$str_lang['LANG_COMENTARIOS_DHIDRANTE']?></h2>														
				<table id="table-parte">						
					<tbody>								 					
						<tr>
							<td class="title" ><?=$str_lang['LANG_HIDRANTES_CODIGO']?>:</td>
							<td  ><?php echo utf8_encode($datos_hidrante['codigo'])?></td>
							<td class="title" ><?=$str_lang['LANG_HIDRANTES_FECHA']?>:</td>
							<td ><?php echo convert_date($datos_hidrante['fecha'])?></td>
						</tr>
						
						
						<tr>
							<?php
								
								$vector=$bd->get_all_by_id("municipios","municipio_id",$datos_hidrante["municipio_id"]);
								
							?>
							<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?>:</td>
							<td ><?=$vector["municipio"]?></td>
							<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_DIRECCION']?>:</td>
							<td ><?=$datos_hidrante["calle"].' '.$datos_hidrante["edificio"]?></td>
						</tr>																
					</tbody>
				</table>				            				
			</div>
		</div>		
				<?php
		//Paginacion
					$limit = pagination_cant;
					$page = $_GET['page'];					
					$where=" hidrante_id=".$id;					
					$total=$bd->getcounttable('V_CartasComentarios',$where);
					$pager  = Pager::getPagerData($total, $limit, $page);			
					$offset = $pager->offset;
					$limit  = $pager->limit;
					$page   = $pager->page;				
					$p = new pagination;
					$p->Items($total);
					$p->limit($limit);
					$p->currentPage($page);	
					
		
		$tPost=$bd->getlisttable("V_CartasComentarios",$where,"interno desc",$offset,$limit);
		if (count($tPost)!=0)
		{

		
			
		?>
	
			
		<form id="frm" name="frm" action="<?php echo $action_form?>" method="POST">		
		
	
	
	
				
	
		
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
							$p->target("/cp/hidrantes/cartas/".$id."/?".$target);
						}			
						$p->show();
					}
					?>  

			</div>
	<!-- // Paginacion -->
			
				<table id="table-list-tasas" class="tsort">
					<thead>
						<tr>
							<th class="first" style="width:1%;"></th>
							<th class="headerSortDown" style="width:10%;"><?=$str_lang['LANG_CARTAS_INTERNO']?></th>
							<th  style="text-align:left;width:10%;"><?=$str_lang['LANG_CARTAS_FECHAR']?></th>
							<th  style="width:10%;"><?=$str_lang['LANG_CARTAS_NREGISTRO']?></th>
							<th   style="width:4%;"><?=$str_lang['LANG_CARTAS_FECHARE']?></th>

							<th style="width:47%;"><?=$str_lang['LANG_CARTAS_COMENTARIO']?></th>									
							<th style="width:18%;" ><?=$str_lang['LANG_USERS_COL_ACCIONES']?></th>
						</tr>
					</thead>
					<tbody>
					<?php
						if (!empty($tPost))
						{	
											
							$j=1;
							foreach ($tPost as $post)
							{		
								

									echo '<tr><td class="first"></td><td class="first">'.$post['interno'].'</td>';
									echo'<td>'.convert_date($post['fecharegistro']);																					
									echo'</td>';
									echo'<td >'.$post['numeroregistro'].'</td>';
									echo'<td>'.convert_date($post['fechaentrada']);						
									echo'</td>';
									
									
									echo'								
									<td style="text-align:left;margin-left:4px;">'.$post['comentario'].'</td>						
									<td style="text-align:left;">';			
										echo '<a href="#" class="general-button-long"  onclick="openpdf('.$post['carta_id'].');return false;">'.$str_lang['LANG_CARTAS_ONLY_VIEW'].'</a>';
										echo '&nbsp;<a href="'.$admin_path.'hidrantes/editarcarta/'.$post['carta_id'].'/'.$_GET["page"].'/" class="general-button">'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';
										

									echo'
									</td> 				
								</tr>';								
								$j++;
							}
						}
						//<a href="'.$admin_path.$modulo.'/view/'.$post['municipio_id'].'/" class="general-button ver">Ver</a>
						?>
					</tbody>
				</table>

		</div>
		<input type="hidden" name="carta_id" id="carta_id"  value="" />		
		<input type="hidden" name="id" id="id" value="<?php echo $id?>"/>
		</form>
		<!-- // Paginas -->
		<?php
		}else{
		echo '<b>'.$str_lang['LANG_MSG_ERROR18'].'.</b>';
			echo'<div class="header-doc">								
					<div class="clr"><hr /></div>				
				</div>';
		}
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
					$p->target("/cp/hidrantes/comment/".$id."/?".$target);
				}			
				$p->show();
          	}
			?>  
		<div class="clr"><hr /></div>
	</div>
		
				
		
		
	</div>
	<?php
	unset($_POST);
	?>
	<!-- // Contenido -->
