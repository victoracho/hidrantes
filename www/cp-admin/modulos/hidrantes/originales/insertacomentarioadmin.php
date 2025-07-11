<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
	$comentario_id=get_id_by_uri2();	
	$page=get_id_by_uri3();	
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

<?php
 
	/* SE ENVIA EL FORMULARIO ACTUALIZAR */
	if (isset($_POST['enviar'])) 
	{
		
		$datos=array();
		// Recojo los datos			
		foreach($_POST as $key => $value)
		{
			$datos[$key]= $value;
		}				
		
		$estado="EJP";
		//$fecha=str_replace("/","-",trim($datos['fecha']));
		$campos = array (
			"fecha" => convert_dateBD($datos['fecha']),			
			"comentario" => trim($datos['comentario']),
			"asociadoid" => trim($datos['asociadoid']),			
			"asociado" => get_asociado($datos['asociadoid']),
			"comentariojefe" => trim($datos['comentario']),
			"comentarioconsorcio" => trim($datos['comentario']),
			"estado" => $estado
		);
		
		if (!empty($datos['comentario']))
		{
		
			$tabla="comentarios";
			$comentario_id=$bd->insert_con_id($tabla,$campos);	
			if (!empty($comentario_id))
			{
				
				$tabla="hidrante_comentarios";
				$campos = array (
					"hidrante_id" => $datos["id"],
					"comentario_id" => $comentario_id,
					"user_id" => intval ($_SESSION["id"]),
					"Name" => $_SESSION["user"]
												
				);
				$bd->insert($tabla,$campos);
				
				
				
				
			}
			$campos = array (
			"estado" => "EC"
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
			
			$r = $bd->bbdd_query($sql);	
		
			$msg=$str_lang['LANG_MSG_OK8'];
			echo '<div id="success"><p>'.$msg.'</p></div>';					

		}else
		echo '<div id="error"><p>'.$str_lang['LANG_MSG_ERROR15'].'</p></div>';

	}
	
	$action_edit=$bd->get_all_by_id("V_Comentarios","comentario_id",$comentario_id);
	$datos_comentario= $action_edit;
	
?>
	<!-- Contenido -->
	<div id="content">
		<!-- Encabezado -->
		<div class="header-doc">
			<h1><?=$str_lang['LANG_COMENTARIOS_ADD']?></h1>	
			<p class="head-button"><a href="<?php echo $admin_path?>hidrantes/comment/<?=$id?>/?&page=<?=$page?>"><span><?=$str_lang['LANG_COMENTARIOS_TITLE_VOLVER']?></span></a></p>			
			<div class="clr"><hr /></div>
		</div>
				
		<div class="client-detail">
				<div class="orders">	
				<form id="frm" name="frm" action="<?php echo $action_form?>" method="POST">		
					
					<br /><br /><br /><div id="errorfrm" style="float:left;display:block;"></div>					
					<table id="table-parte">						
						<tbody>			
							<tr>
								<td class="title" width="100px"><?=$str_lang['LANG_COMENTARIOS_FECHA']?>:</td>
								<td ><input type="text" name="fecha" id="fecha" class="short" value="" maxlength="100"/></td>
								
							</tr>
							<tr>
								<td  class="title" width="100px"><?=$str_lang['LANG_COMENTARIOS_ASOCIADO']?>:&nbsp;</td>
								<?php
									$vector=$bd->get_all("incidencias");
									
								?>								
								<td style="text-align:left;"><select name="asociado" id="asociado" disabled> 
								<?php
									
									echo cargarCombo($datos_comentario['asociadoid'],$vector,"incidencia_id","incidencia".$lan);
								?>
								</select>
								</td>
							</tr>	
							<tr>
								<td class="title" width="100px"><?=$str_lang['LANG_HEADER_TAB_COMENTARIOS']?>:</td>
								<td colspan="3"><textarea id="comentario" name="comentario"  class="lang-edit" class="required"></textarea></td>
							</tr>								
							
						</table>
						<p class="button" style="margin-top:20px;text-align:center">
							<input type="hidden" name="id" id="id" value="<?php echo $id?>"/>
							<input type="hidden" name="asociadoid" id="asociadoid" value="<?php echo $datos_comentario['asociadoid']?>"/>
							<input type="hidden" name="comentario_id" id="comentario_id" value="<?php echo $comentario_id?>"/>
							<input type="submit" value="<?=$str_lang['LANG_CARTAS_GUARDAR']?>" class="button" id="enviar" name="enviar"/>
						</p>                					
					</form>
				</div>
		</div>
	</div>