<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );

	$action_edit=$bd->get_all_by_id("V_Cartas","carta_id",$id);
	$datos_carta= $action_edit;
	
	// Si no existe el id, salgo.				
	if (empty($datos_carta))
		{
			Location($admin_path.$modulo."/cartas/");
			exit();
		}

	$page=get_id_by_uri2();	
	$cal=getPrefijoCal();	



?>

<script type="text/javascript">

	$(function() {
		$("#frm").validate({
		rules: {
			observaciones: {
				required: false
			},
			fecharegistro: {
				required: false,
				date: true
			},
			fechaentrada: {
				required: false,
				date: true
			}
			
		},
		messages: {
			comentario: "<span class='errorMSG'><?=$str_lang['LANG_MSG_ERROR12']?></span><br/>",
			fecharegistro:	 "<span class='errorMSG'><?=$str_lang['LANG_MSG_ERROR13']?></span><br/>",		
			fechaentrada:	 "<span class='errorMSG'><?=$str_lang['LANG_MSG_ERROR13']?></span><br/>",	
			asociado:	 "<span class='errorMSG'><?=$str_lang['LANG_MSG_ERROR14']?></span><br/>"	
		},
		errorLabelContainer: $("#errorfrm")
	   
		   
		
		});

		
	
		$("#fecharegistro").datepicker($.datepicker.regional['<?=$cal?>']);			
		$("#fechaentrada").datepicker($.datepicker.regional['<?=$cal?>']);	
	
	});
</script>

<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
	

  
	/* SE ENVIA EL FORMULARIO ACTUALIZAR */
	if (isset($_POST['enviar'])) 
	{
		var_dump();
		$datos=array();
		// Recojo los datos			
		foreach($_POST as $key => $value)
		{
			$datos[$key]= $value;
		}				
		
		//$fecha=str_replace("/","-",trim($datos['fecha']));
		$campos = array (
			"fecharegistro" => convert_dateBD($datos['fecharegistro']),			
			"numeroregistro" => trim($datos['numeroregistro']),			
			"observaciones" => utf8_decode(trim($datos['observaciones'])),
			"fechaentrada" => convert_dateBD($datos['fechaentrada'])
		
		);
		
		$tabla="cartas";
			$str="";
			$i=0;
			foreach($campos as $key => $value)
			{	
				if ($i==0) $str.=" ".$key."=".entrada_sql($value);
				else $str.=", ".$key."=".entrada_sql($value);
				$i=1;
			}
			
			$sql="UPDATE ".$tabla." SET ".$str." WHERE carta_id=".$datos["id"];
			
			$r = $bd->bbdd_query($sql);	
			if (!$r)
			{
				$msgError= $str_lang['LANG_MSG_ERROR22'];
			}else	{// Todo ok.
		
				$msgOK=$str_lang['LANG_MSG_OK12'];
						
			}

	}
	if (strlen($msgError)>0)
	{
		echo '<div id="error"><p>'.$msgError.'</div>';
	}elseif(strlen($msgOK)>0)
	{
		echo '<div id="success"><p>'.$msgOK.'</p></div>';	
	}
	$action_edit=$bd->get_all_by_id("V_Cartas","carta_id",$id);
	$datos_carta= $action_edit;

?>
	<!-- Contenido -->
	<div id="content">
		<!-- Encabezado -->
		<div class="header-doc">
			<h1><span><?=$str_lang['LANG_CARTAS_EDITAR']?></span></h1>	
            <div class="clr"><hr /></div>
			<p class="head-button"><a href="<?php echo $admin_path?>hidrantes/cartas/<?=$datos_carta['hidrante_id']?>/?&page=<?=$page?>"><span><?=$str_lang['LANG_CARTAS_TITLE_VOLVER']?></span></a></p>			
			<div class="clr"><hr /></div>
		</div>
				
		<div class="client-detail">
				<div class="orders">	
				<form id="frm" name="frm" action="<?php echo $action_form?>" method="POST">							
					<br /><div id="errorfrm" style="float:left;display:block;"></div>					
					<table id="table-parte">						
						<tbody>			
							<tr>
								<td class="title" width="100px"><?=$str_lang['LANG_CARTAS_FECHAR']?>:</td>
								<td ><input type="text" name="fecharegistro" id="fecharegistro" class="short" value="<?=convert_date($datos_carta['fecharegistro'])?>" maxlength="100"/></td>
								
							</tr>
							<tr>
								<td  class="title" width="100px"><?=$str_lang['LANG_CARTAS_NREGISTRO']?>:&nbsp;</td>
								<td> <input type="text" name="numeroregistro" id="numeroregistro" class="short" value="<?=$datos_carta['numeroregistro']?>" maxlength="100"/></td>
							</tr>	
							<tr>
								<td class="title" width="100px"><?=$str_lang['LANG_CARTAS_FECHARE']?>:</td>
								<td ><input type="text" name="fechaentrada" id="fechaentrada" class="short" value="<?=convert_date($datos_carta['fechaentrada'])?>" maxlength="100"/></td>
								
							</tr>	
							<tr>
								<td class="title" width="100px"><?=$str_lang['LANG_MUNICIPIOS_OBS']?>:</td>
								<td ><textarea id="observaciones" name="observaciones"  class="lang-edit" ><?=$datos_carta['observaciones']?></textarea></td>
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