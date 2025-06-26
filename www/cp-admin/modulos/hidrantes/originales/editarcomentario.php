<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );

	$action_edit=$bd->get_all_by_id("V_Comentarios","comentario_id",$id);
	$datos_comentario= $action_edit;
	
	// Si no existe el id, salgo.				
	if (empty($datos_comentario))
		{
			Location($admin_path.$modulo."/comment/");
			exit();
		}

	$page=get_id_by_uri2();	
	$cal=getPrefijoCal();	
	$lan=getPrefijoLang();
	


?>

<script type="text/javascript">

	$(function() {
		$("#frm").validate({
		rules: {
			comentario: "required",
			fecha: {
				required: true,
				date: true
			},
			asociado:{
				min: 0
			}
		},
		messages: {
			comentario: "<span class='errorMSG'><?=$str_lang['LANG_MSG_ERROR12']?></span><br/>",
			fecha:	 "<span class='errorMSG'><?=$str_lang['LANG_MSG_ERROR13']?></span><br/>",		
			asociado:	 "<span class='errorMSG'><?=$str_lang['LANG_MSG_ERROR14']?></span><br/>"	
		},
		errorLabelContainer: $("#errorfrm")
	   
		   
		
		});

		
	
		$("#fecha").datepicker($.datepicker.regional['<?=$cal?>']);			
	
	});
</script>

<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
	

  
	/* SE ENVIA EL FORMULARIO ACTUALIZAR */
	if (isset($_POST['enviar'])) 
	{

		$datos=array();
		// Recojo los datos			
		foreach($_POST as $key => $value)
		{
			$datos[$key]= $value;
		}				
		
		//$fecha=str_replace("/","-",trim($datos['fecha']));
		$campos = array (
			"fecha" => convert_dateBD($datos['fecha']),			
			"comentario" => trim($datos['comentario']),
			"asociadoid" => trim($datos['asociado']),			
			"asociado" => get_asociado($datos['asociado']),
			"comentariojefe" => trim($datos['comentario']),
			"comentarioconsorcio" => trim($datos['comentario'])
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
			
			$sql="UPDATE ".$tabla." SET ".$str." WHERE comentario_id=".$datos["id"];
			
			$r = $bd->bbdd_query($sql);	
			if (!$r)
			{
				$msgError= $str_lang['LANG_MSG_ERROR23'];
			}else	{// Todo ok.
		
				$msgOK=$str_lang['LANG_MSG_OK13'];
						
			}

	}
	if (strlen($msgError)>0)
	{
		echo '<div id="error"><p>'.$msgError.'</div>';
	}elseif(strlen($msgOK)>0)
	{
		echo '<div id="success"><p>'.$msgOK.'</p></div>';	
	}
	$action_edit=$bd->get_all_by_id("V_Comentarios","comentario_id",$id);
	$datos_comentario= $action_edit;

?>
	<!-- Contenido -->
	<div id="content">
		<!-- Encabezado -->
		<div class="header-doc">
			<h1><?=$str_lang['LANG_COMENTARIOS_EDITAR']?></h1>	
			<p class="head-button"><a href="<?php echo $admin_path?>hidrantes/comment/<?=$datos_comentario['hidrante_id']?>/?&page=<?=$page?>"><span><?=$str_lang['LANG_COMENTARIOS_TITLE_VOLVER']?></span></a></p>			
			<div class="clr"><hr /></div>
		</div>
				
		<div class="client-detail">
				<div class="orders">	
				<form id="frm" name="frm" action="<?php echo $action_form?>" method="POST">		
					<br /><br /><br /><div id="errorfrm" style="float:left;display:block;"></div>					
					<table id="table-parte">						
						<tbody>			
							<tr>
								<td class="title" width="150px"><?=$str_lang['LANG_COMENTARIOS_FECHA']?>:</td>
								<td ><input type="text" name="fecha" id="fecha"  class="short" value="<?=convert_date($datos_comentario['fecha'])?>" maxlength="100"/></td>
								
							</tr>
							<tr>
								<td  class="title" width="100px"><?=$str_lang['LANG_COMENTARIOS_ASOCIADO']?>:&nbsp;</td>
								
							<?php
									$vector=$bd->get_all("incidencias");
									
								?>								
								<td style="text-align:left;"><select name="asociado" id="asociado"> 
								<?php
									
									echo cargarCombo($datos_comentario['asociadoid'],$vector,"incidencia_id","incidencia".$lan);
								?>
								</select>
								</td>
							</tr>	
							<tr>
								<td class="title" width="100px"><?=$str_lang['LANG_HEADER_TAB_COMENTARIOS']?>:</td>
								<td colspan="3"><textarea id="comentario" name="comentario"  class="lang-edit" class="required"><?=$datos_comentario['comentario']?></textarea></td>
							</tr>								
							
						</table>
						<p class="button" style="margin-top:20px;text-align:center">
							<input type="hidden" name="id" id="id" value="<?php echo $id?>"/>
							<input type="submit" value="<?=$str_lang['LANG_CARTAS_GUARDAR']?>" class="button" id="enviar" name="enviar"/>
						</p>                					
					</form>
				</div>
		</div>
	</div>