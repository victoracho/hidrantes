<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
// Si no existe el id, salgo.	
	$page=get_id_by_uri2();
	$action_edit=$bd->get_all_by_id("hidrantes","hidrante_id",$id);
	$datos_hidrante= $action_edit;			
	if (empty($action_edit))
		{
			Location($admin_path.$modulo."/");
			exit();
		}
?>
<script type="text/javascript">

	function enviar_comentario(comentario_id,estado)
	{
		
		$("#comentario_id").val(comentario_id);
		$("#estado").val(estado);
		$("#generacarta").val("");		
		$("#frm").submit();
	}
	
	function generarcarta(comentario_id)
	{
		
		$("#comentario_id").val(comentario_id);	
		$("#generacarta").val(2);	
	//	$("#frm").attr('target', '_blank'); 		
		$("#frm").submit();
		
	}
</script>

<?php
	
//eliminar comentarios seleccionados
// Borrar elementos.
	
	$msgError = "";
	$msgOK = "";
	if (isset($_POST['del']))
	{
		if (!empty($_POST['borrar']))
		{
			foreach ($_POST['borrar'] as $borrar)
			{
				
				$comentario_id=$borrar;
				
				$vector=$bd->get_all_by_id("cartascomentarios","comentario_id",$comentario_id);

				if (!$vector)
				{
					$bd->delete("comentarios","comentario_id", $comentario_id);	
					$bd->delete("hidrante_comentarios","comentario_id", $comentario_id);	
					$bd->delete("relacion_comentarios","comentario_id_jp", $comentario_id);	
					
					$msgOK=$str_lang['LANG_MSG_OK2'];
				}else
					$msgError=$str_lang['LANG_MSG_ERROR21'];
				
							
			}
		}		
			
	}
	
	$carta_id="";
	//Generar carta
	if (isset($_POST['generacarta']))
	{
		if ($_POST['generacarta']==2)
		{
			$datos=array();
			// Recojo los datos			
			foreach($_POST as $key => $value)
			{
				$datos[$key]= $value;
			}	
			
			$campos = array (
			"estado" => 'EC'
			);
			$tabla="comentarios";
		
			//Actualizamos comentario referente
			$hidrante_id= $datos["id"];
			$where = "hidrante_id= $hidrante_id and estadocomentario ='EJP'";
			$vComentarios=$bd->get_all_by_filter_order("V_Comentarios",$where,"fecha desc");	
			$n=count($vComentarios);
			
			for($j=0;$j<$n;$j++)
			{
				
				$str="";
				$i=0;
				foreach($campos as $key => $value)
				{	
					if ($i==0) $str.=" ".$key."=".entrada_sql($value);
					else $str.=", ".$key."=".entrada_sql($value);
					$i=1;
				}
				
				$sql="UPDATE ".$tabla." SET ".$str." WHERE comentario_id=".$vComentarios[$j]["comentario_id"];
			
				$r = $bd->bbdd_query($sql);	
				if (!$r)		
					$msgError= $str_lang['LANG_MSG_ERROR20'];
			
			}
			
				$str="";
				$i=0;
				foreach($campos as $key => $value)
				{	
					if ($i==0) $str.=" ".$key."=".entrada_sql($value);
					else $str.=", ".$key."=".entrada_sql($value);
					$i=1;
				}
				
				$sql="UPDATE ".$tabla." SET ".$str." WHERE comentario_id=".$datos["comentario_id"];
			
				$r = $bd->bbdd_query($sql);	
				if (!$r)		
					$msgError= $str_lang['LANG_MSG_ERROR20'];
			//Insertamos registro carta
			
			$ncartas=$bd->getcounttable("cartas","hidrante_id=".$hidrante_id);
		
			
				$tabla="cartas";
				$fecha=date("Y-m-d H:i:s");
				$campos = array (
					"hidrante_id" => $hidrante_id,	
					"estado" => "",
					"generado" => 0,			
					"observaciones" => "",
					"UserID" => intval ($_SESSION["id"]),
					"fecha" =>$fecha,
					"registrada" => 0,
					"numeroregistro"=>"",
					"fecharegistro"=>"NULL",
					"cerrada"=>0,
					"ncarta"=>((int)$ncartas+1),
					"interno"=>((int)$ncartas+1),
					"fechaentrada"=>"NULL"
				);	
				$carta_id = $bd->insert_con_id($tabla,$campos);		
				
				if ($carta_id<=0)					
					$msgError= $str_lang['LANG_MSG_ERROR20'];
			
				//Insertamos comentarios carta				
				$tabla = "cartascomentarios";
					$campos = array (
						"carta_id" => $carta_id,			
						"comnetario_id" => $datos["comentario_id"]						
					);	
					$bd->insert($tabla,$campos);
							
			$filename=generar_carta($hidrante_id,$datos["comentario_id"],$carta_id,1);
			$control=false;
			if (strlen($filename)>0)
			{
				$control=true;
				Location($admin_path.'hidrantes/cartas/'.$hidrante_id.'/');
				$msgOK=$str_lang['LANG_MSG_OK10'];					
			}
			else
				$error=$str_lang['LANG_MSG_ERROR16'];
				
			
		}	
			
		
			
	}
	
	
	
	
		if ($_POST['generacarta']!=2  && !empty($_POST['comentario_id']))
		{
		
			$datos=array();
			// Recojo los datos			
			foreach($_POST as $key => $value)
			{
				$datos[$key]= $value;
			}	
			
			$campos = array (
			"estado" => $datos['estado']
			);
		
		
			$tabla="comentarios";
			$str="";
			$i=0;
			foreach($campos as $key => $value)
			{	
				if ($i==0) $str.=" ".$key."=".entrada_sql($value);
				else $str.=", ".$key."=".entrada_sql($value);
				$i=1;
			}
			
			$sql="UPDATE ".$tabla." SET ".$str." WHERE comentario_id=".$datos["comentario_id"];
			//echo $sql;
			$r = $bd->bbdd_query($sql);	
			if (!$r)
			{
				$msgError= $str_lang['LANG_MSG_ERROR20'];
			}else	{// Todo ok.
		
				$msgOK=$str_lang['LANG_MSG_OK11'];
						
			}
		}
	

	
	
	if (strlen($msgError)>0)
	{
		echo '<div id="error"><p>'.$msgError.'</div>';
	}elseif(strlen($msgOK)>0)
	{
		echo '<div id="success"><p>'.$msgOK.'</p></div>';	
	}
	
	
	
?>
	
	
	<!-- Contenido -->
	<div id="content">
		<!-- Encabezado -->
		<div class="header-doc">
			<h1><span><?=$str_lang['LANG_COMENTARIOS_TITLE']?></span></h1>	
            <div class="clr"><hr /></div>
			<?php
			if (!is_consorcio())
			{
			?>
			<p class="head-button"><a href="<?php echo $admin_path?>hidrantes//?&page=<?=$page?>"><span><?=$str_lang['LANG_HIDRANTES_TITLE_VOLVER']?></span></a></p>			
			<?
			}
			if (is_jefeguardia() || is_jefeparque())
			{
			?>
			<p class="head-button"><a href="<?php echo $admin_path?>hidrantes/insertacomentario/<?=$id?>/<?=$_GET["page"]?>/"><span><?=$str_lang['LANG_COMENTARIOS_ADD']?></span></a></p>			
		<?php
			}
			$previd = $bd->getPrevId($id,"hidrantes","hidrante_id","codigo");
			$nextid = $bd->getnextId($id,"hidrantes","hidrante_id","codigo");
			if ($previd !="") 
				echo '<p class="head-button"><a href="'.$admin_path.'hidrantes/comment/'.$previd['hidrante_id'].'/" > <span>  <img src="'.imgadminpath.'flecha.gif" border="0" width="10" height="13" />  Anterior </span></a></p>';
			if ($nextid !="") 
				echo '<p class="head-button"><a href="'.$admin_path.'hidrantes/comment/'.$nextid['hidrante_id'].'/" ><span> Siguiente <img src="'.imgadminpath.'flechaR.gif" border="0" width="10" height="13" />  </span></a></p>';
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
							<tr>
								<?php
									
									$vector=$bd->get_all_by_id("tiposhidrantes","tipohidrante_id",$datos_hidrante["tipohidrante_id"]);
									
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_TIPOH']?>:</td>
								<td colspan="3"><?=$vector["tipohidrante"]?></td>
							</tr>
							
														
							
						</table>
					            					
				</div>
				<div class="clr"><hr /></div>	
		</div>		
				
		
		<?php
		//Paginacion
					$limit = pagination_cant;
					$page = $_GET['page'];					
					$where=" hidrante_id=".$id;
					if (is_jefeparque())
						$where.=" and parque_id=".$_SESSION['HIDRANTES']['parque_id'];
					$total=$bd->getcounttable('V_Comentarios',$where);
					$pager  = Pager::getPagerData($total, $limit, $page);			
					$offset = $pager->offset;
					$limit  = $pager->limit;
					$page   = $pager->page;				
					$p = new pagination;
					$p->Items($total);
					$p->limit($limit);
					$p->currentPage($page);	
					
		
		$tPost=$bd->getlisttable("V_Comentarios",$where,"fecha desc,comentario_id desc",$offset,$limit);
		if (count($tPost)!=0)
		{

		
			
		?>
	
			
		<form id="frm" name="frm" action="<?php echo $action_form?>" method="POST">		
		
	
	
	
				
	
		
		<!-- Paginas -->
		
		<div class="summary-list">		
			<?
			if (is_jefeguardia() || is_jefeparque() || is_consorcio())			
			{
			
			?>
			<div class="filter">
											
					<input type="submit" value="<?=$str_lang['LANG_USERS_DEL']?>" class="button"  name="del"/>
					
			<div class="clr"><hr /></div>		
			</div>
			<?php
			}
			?>
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
							$p->target("/cp/hidrantes/comment/".$id."/?".$target);
						}			
						$p->show();
					}
					?>  

			</div>
	<!-- // Paginacion -->
			
				<table id="table-list-tasas" class="tsort">
					<thead>
						<tr>
							<th class="first" title="<?=$str_lang['LANG_MUNICIPIOS_SELALL']?>"></th>
							<th class="headerSortDown" style="text-align:left;width:10%;"><?=$str_lang['LANG_COMENTARIOS_FECHA']?></th>
							<th  style="width:10%;"><?=$str_lang['LANG_COMENTARIOS_ASOCIADO']?></th>
							<th style="width:10%;"><?=$str_lang['LANG_USERS_COL_USER']?></th>							
							<th style="width:45%;"><?=$str_lang['LANG_HEADER_TAB_COMENTARIOS']?></th>									
							<th style="width:25%;" ><?=$str_lang['LANG_USERS_COL_ACCIONES']?></th>
						</tr>
					</thead>
					<tbody>
					<?php
						if (!empty($tPost))
						{	
											
						
							foreach ($tPost as $post)
							{		
								
							
								
								echo '<tr><td class="first">';
								
								if ($post['user_id']==$_SESSION['id'])	
								{
									if (is_jefeguardia() )
									{
										switch ($post['estadocomentario'])
										{
											case "":
												if ($post['user_id']==$_SESSION['id'])
													echo '<input type="checkbox" name="borrar[]" class="list_del" value="'.$post['comentario_id'].'" />';	
												break;
											default:												
												echo '';
												break;
										}
									}elseif (is_jefeparque())
									{
										switch ($post['estadocomentario'])
										{		
											case "":										
											case "EJG":
												if ($post['user_id']==$_SESSION['id'])
													echo '<input type="checkbox" name="borrar[]" class="list_del" value="'.$post['comentario_id'].'" />';	
												break;
											default:
												echo '';
												break;
										}									
									}elseif (is_consorcio())
									{
										switch ($post['estadocomentario'])
										{
											case "EJP":
																								
												if ($post['user_id']==$_SESSION['id'])
													echo '<input type="checkbox" name="borrar[]" class="list_del" value="'.$post['comentario_id'].'" />';	
												break;
										/*	case "":												
												if ($post['user_id']==$_SESSION['id'])
													echo '<input type="checkbox" name="borrar[]" class="list_del" value="'.$post['comentario_id'].'" />';	
												
												break;*/
										}									
									}elseif(is_admin())
									{
										switch ($post['estadocomentario'])
										{
											default:
												if ($post['user_id']==$_SESSION['id'])
													echo '<input type="checkbox" name="borrar[]" class="list_del" value="'.$post['comentario_id'].'" />';	
												break;
										}
									
									}
									echo '</td>';
									
								}									
								else
									echo'</td>';
									
									echo'<td>'.convert_date($post['fecha']);																					
									echo'</td>';
									echo'<td >'.utf8_encode($post['asociado']).'</td>
									<td>'.$post['Name'].'</td>
									<td style="text-align:left;margin-left:4px;">'.$post['comentario'].'</td>						
									<td style="text-align:left;margin-left:4px;">';																	
									if (is_jefeguardia() )
									{
										switch ($post['estadocomentario'])
										{											
											case "":												
												echo '<a href="#" class="general-button" onclick="enviar_comentario(\''.$post['comentario_id'].'\',\'EJG\');return false;">'.$str_lang['LANG_COMENTARIOS_ENVIAR'].'</a>';
												if ($post['user_id']==$_SESSION['id'])
													echo '<a href="'.$admin_path.'hidrantes/editarcomentario/'.$post['comentario_id'].'/'.$_GET["page"].'/" class="general-button">'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';
												break;
										}
									}elseif (is_jefeparque())
									{
										switch ($post['estadocomentario'])
										{
											case "EJG":
												
												//if (getPerfilbyUserID($post['user_id']) == jefe_de_parque)
												if (getPerfilbyUserID($post['user_id']) == jefe_de_parque)
													echo '<a href="#" class="general-button" onclick="enviar_comentario(\''.$post['comentario_id'].'\',\'EJP\');return false;">'.$str_lang['LANG_COMENTARIOS_ENVIAR'].'</a>';
												if ($post['user_id']==$_SESSION['id'])
													echo '<a href="'.$admin_path.'hidrantes/editarcomentario/'.$post['comentario_id'].'/'.$_GET["page"].'/" class="general-button">'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';
												echo '<a href="'.$admin_path.'hidrantes/insertacomentariojp/'.$id.'/'.$post['comentario_id'].'/'.$_GET["page"].'/" class="general-button">'.$str_lang['LANG_COMENTARIOS_ANYADIR'].'</a>';												
												break;
											case "":
												if (getPerfilbyUserID($post['user_id']) == jefe_de_parque)
													echo '<a href="#" class="general-button" onclick="enviar_comentario(\''.$post['comentario_id'].'\',\'EJP\');return false;">'.$str_lang['LANG_COMENTARIOS_ENVIAR'].'</a>';
												if ($post['user_id']==$_SESSION['id'])
													echo '<a href="'.$admin_path.'hidrantes/editarcomentario/'.$post['comentario_id'].'/'.$_GET["page"].'/" class="general-button">'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';
												
												break;
										}									
									}elseif (is_consorcio())
									{
										switch ($post['estadocomentario'])
										{
											case "EJP":
												//if (getPerfilbyUserID($post['user_id']) == administrativo){
													echo '<a href="'.$admin_path.$modulo."/viewpdf/".$id.'/'.$post['comentario_id']."/".'" class="general-button-xlong" target="_blank">'.$str_lang['LANG_CARTAS_PREVIEWCARTA'].'</a>';
													echo '<a href="#" class="general-button-long" onclick="generarcarta(\''.$post['comentario_id'].'\',\'2\');return false;">'.$str_lang['LANG_CARTAS_GENERARCARTA'].'</a>';													
													if (getPerfilbyUserID($post['user_id']) != administrativo)
														echo '<a href="'.$admin_path.'hidrantes/insertacomentarioadmin/'.$id.'/'.$post['comentario_id'].'/'.$_GET["page"].'/" class="general-button">'.$str_lang['LANG_COMENTARIOS_ANYADIR'].'</a>';
													if (getPerfilbyUserID($post['user_id']) == administrativo)
														echo '<a href="'.$admin_path.'hidrantes/editarcomentario/'.$post['comentario_id'].'/'.$_GET["page"].'/" class="general-button">'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';
													
												//}
												break;
											
											/*case "":
												if (getPerfilbyUserID($post['user_id']) == administrativo)
												{
													echo '<a href="'.$admin_path.$modulo."/viewpdf/".$id.'/'.$post['comentario_id']."/".'" class="general-button-xlong" target="_blank">'.$str_lang['LANG_CARTAS_PREVIEWCARTA'].'</a>';
													echo '<a href="#" class="general-button-long" onclick="generarcarta(\''.$post['comentario_id'].'\',\'2\');return false;">'.$str_lang['LANG_CARTAS_GENERARCARTA'].'</a>';		
												
													echo '<a href="'.$admin_path.'hidrantes/editarcomentario/'.$post['comentario_id'].'/'.$_GET["page"].'/" class="general-button">'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';
												
												}
												
												break;*/
										}									
									}elseif(is_admin())
									{
										switch ($post['estadocomentario'])
										{
											case "EJG":
												echo '';
												break;
											default:												
												
												echo '<a href="" class="general-button">'.$str_lang['LANG_COMENTARIOS_ENVIAR'].'</a>';
												echo '<a href="" class="general-button">'.$str_lang['LANG_HEADER_TAB_EDITAR'].'</a>';
												break;
										}
									
									}
									
									
									echo'
									</td> 				
								</tr>';								
							}
						}
						//<a href="'.$admin_path.$modulo.'/view/'.$post['municipio_id'].'/" class="general-button ver">Ver</a>
						?>
					</tbody>
				</table>

		</div>
		<input type="hidden" name="comentario_id" id="comentario_id"  value="" />
		<input type="hidden" name="estado" id="estado"  value="" />
		<input type="hidden" name="generacarta" id="generacarta"  value="" />
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
