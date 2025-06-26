<?
$cal=getPrefijoCal();

?>
<script type="text/javascript">
	$(function() {
		$("#fecha").datepicker($.datepicker.regional['<?=$cal?>']);		
		$("#fecharevision").datepicker($.datepicker.regional['<?=$cal?>']);			
		$("#afecharevision").click(function(){
			var fecha=new Date();
			var diames=fecha.getDate();	
			var mes=fecha.getMonth() +1 ;
			var ano=fecha.getFullYear();
			$("#fecharevision").val(diames+'/'+mes+'/'+ano);
			return false;

		});
		
		 $('#upload_sp').MultiFile({
		    list: '#lista-fotos',
		  accept:'gif|jpg|jpeg', STRING: {
		   remove:'[Quitar imagen]',
		   selected:'Selecionado: $file',
		   denied:'Los ficheros $ext no estan permitidos. Ficheros permitidos: .gif, .jpg, .jpeg'
		  }
		 });

		
	});
	
</script>
<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
$page=get_id_by_uri();


	/* SE ENVIA EL FORMULARIO ACTUALIZAR */
	if (isset($_POST['enviar'])) 
	{
		
		$error="";
		$datos=array();
		// Recojo los datos			
		foreach($_POST as $key => $value)
		{
			$datos[$key]= $value;
		}				
		
		/* Guardo los datos del sujeto objeto */
		$ux=trim($datos['utmx']);
		$uy=trim($datos['utmy']);
		$gn=trim($datos['geon']);
		$gw=trim($datos['geow']);
		if ($ux=="") $ux=0;
		if ($uy=="") $uy=0;
		if ($gn=="") $gn=0;
		if ($gw=="") $gw=0;
		if (($ux!=0)&& ($uy!=0))
		{				
			
			if (($gn==0)||($gw==0))
			{
				
				$objUTM = convertirCoordenadaUTM($ux,$uy,$datos['uso']);
				$gn=number_format($objUTM->Lat(),5,'.', '');
				$gw=number_format($objUTM->Long(),5, '.', '');
			
			}
		}
		else
		{
			if (($gn!=0)&&($gw!=0))
			{
				$objUTM = convertirCoordenadaGEO($gn,$gw);
				$ux=number_format($objUTM->E(),2 ,'.', '');
				$uy=number_format($objUTM->N(),2, '.', '');
				$datos['uso'] =substr($objUTM->Z(),0,2);
			}
		}
		
		$campos = array (
			"codigo" => trim($datos['codigo']),
			"edificio" => trim($datos['edificio']),
			"calle" => trim($datos['calle']),
			"utmx" => $ux,
			"utmy" => $uy,
			"geon" => $gn,
			"geow" => $gw,			
			"municipio_id" => trim($datos['municipio']),			
			"obssituacion" => "",
			"tipohidrante_id" => trim($datos['tiposhidrantes']),
			"diametro_id" => trim($datos['diametros']),
			"racor_id" => trim($datos['racores']),
			"senyalizado" => intval(trim($datos['senyalizado'])),
			"redexterior" => intval(trim($datos['redexterior'])),
			"presionadecuado" => intval(trim($datos['presionadecuado'])),
			"obsabastecimiento" => "",
			"estadogeneral" => intval(trim($datos['estadogeneral'])),
			"obsmantenimiento" => "",
			"fecha" => convert_dateBD(trim($datos['fecha'])),
			"comprobo" => "",
			"planosituacion" => trim($datos['planosituacion']),
			"uso"=> $datos['uso'],
			"fecharevision" =>	convert_dateBD(trim($datos['fecharevision']))		
		);
		
		
			if (!empty($datos['codigo']))
			{
				$action_edit=$bd->get_all_by_id("hidrantes","codigo",$campos['codigo']);
	
				//exit;
				if (!$action_edit)
				{
						$tabla="hidrantes";
						$idhidrante=$bd->insert_con_id($tabla,$campos);	
						
					
		
						if (!empty($_FILES["upload_file"]["name"][0]))
						{
							$vFotos= $_FILES["upload_file"];
							$n=count($_FILES["upload_file"]["name"]);
							for ($i=0;$i<$n;$i++)
							{
								$aux= array("name"=>$_FILES["upload_file"]["name"][$i],"type"=>$_FILES["upload_file"]["type"][$i],"size"=>$_FILES["upload_file"]["size"][$i],"tmp_name"=>$_FILES["upload_file"]["tmp_name"][$i],"id"=>$idhidrante,"orden"=>$i);
								
								//cambiartammitad($_FILES["upload_file"]["tmp_name"][$i],$_FILES["upload_file"]["tmp_name"][$i]);
								$vfotoname=uploadimages($aux);
								$camposfotos = array (
								"hidrante_id" => $idhidrante,
								"fototipo_id" => 1,
								"foto"=>$vfotoname[0]
								);
								$tablafoto="fotos";
								$bd->insert($tablafoto,$camposfotos);	
								$error=$vfotoname[1];
								
							}
						
						}
						
						$msgOK=$str_lang['LANG_MSG_OK6'];

				}else
				{
					$error = $str_lang['LANG_MSG_ERROR10'];
				}
			}else
				$error =  $str_lang['LANG_MSG_ERROR6'];
			
		
	}
	if (!empty($error))
	{
		echo '<div id="error"><p>'.$error.'</div>';
	}elseif(isset($msgOK))
	{
		echo '<div id="success"><p>'.$msgOK.'</p></div>';	
	}
	/*
	 * Obtengo todos los datos del parte asociado.
	 */	
	//$wherePartes = "Anio =".entrada_sql($anio)." and RegAnual=".entrada_sql($regAnual)."";
	//$tPostPartes = $bd->getlisttable("partesdiarios",$wherePartes,$orderPartes,$offsetPartes,$limitPartes);	
	//$datos=$tPostPartes[0];	
?>
	
	
	<!-- Contenido -->
	<div id="content">

		<!-- Encabezado -->

		<div class="header-doc">
			<h1><span><?=$str_lang['LANG_HIDRANTES_ADD']?></span></h1>	
            <div class="clr"><hr /></div>
		<p class="head-button"><a href="<?php echo $admin_path?>hidrantes/?&page=<?=$page?>/"><span><?=$str_lang['LANG_HIDRANTES_TITLE_VOLVER']?></span></a></p>			
			<div class="clr"><hr /></div>
		</div>

		<form name="frm" action="<?php echo $action_form?>" method="POST" enctype="multipart/form-data">	
	
		<div class="client-detail">
				<div class="orders">	
					<table id="table-parte">						
						<tbody>	
                        <tr>
                        <td colspan="2"><h2><?=$str_lang['LANG_HIDRANTES_FOTO']?></h2></td>
                        </tr>	
							<tr>							
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_FOTO_ADD']?>:</td>
								<td > <input type="file" id="upload_sp" name="upload_file[]" class="multifile-applied" maxlength="<?=maxfotos?>"   />
								</td>
							</tr>										
							<tr>
								<td colspan="2">
								<div id="lista-fotos">							

								</div>
								</td>
								
							</tr>	
														
					<tr>
                    <td colspan="2"	>
					<h2><?=$str_lang['LANG_HIDRANTES_SITUACION']?></h2>														
					</td>						
						</tr>	
						 						
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_CODIGO']?>:</td>
								<td ><input type="text" name="codigo" id="codigo" class="short" value="" maxlength="6"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_CALLE']?>:</td>
								<td ><input type="text" name="calle" id="calle" class="large" value="" maxlength="150"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_EDIFICIO']?>:</td>
								<td ><input type="text" name="edificio" id="edificio" class="large" value="" maxlength="100"/></td>
							</tr>	
							
							<tr>
							<?php
									$vector=$bd->get_all("municipios");
									
									if (is_jefeparque())
									{
										$where .= "parque_id=".$_SESSION['HIDRANTES']['parque_id'];
										$vector=$bd->get_all_by_filter_order("municipios",$where,"municipio asc");
									}
									else
										$vector=$bd->get_all("municipios");
									
								?>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?>:</td>
								<td ><select name="municipio" id="municipio"> 
								<?php
									
									echo cargarCombo("-1",$vector,"municipio_id","municipio");
								?>
								</select>
								</td>
							</tr>	
									<?php
								//	echo convertirCoordenadaUTM($datos['utmx'],$datos['utmy']);
									?>	
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_FECHA']?>:</td>
								<td ><input type="text" name="fecha" id="fecha" class="short" value="<?=date('d/m/Y');?>" maxlength="100"/></td>
							</tr>	
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_FECHAREVISION']?>:</td>
								<td ><input type="text" name="fecharevision" id="fecharevision" class="short" value="" maxlength="100"/>&nbsp;<a href="" id="afecharevision"><?=$str_lang['LANG_HIDRANTES_ACTFECHAREVISION']?></a></td>
							</tr>							
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_UTM']?></td>
								<td ><label>X:</label><input type="text" id="utmx" name="utmx" class="short" value="" maxlength="100"/> <label>Y:</label> <input type="text" id="utmy" name="utmy" class="short" value="" maxlength="100"/>
								&nbsp;&nbsp;<?=strtoupper($str_lang['LANG_HIDRANTES_HUSO'])?>
								<select name="uso">
								<?php
								
								for($i=28;$i<=35;$i++)
								{
									if ($datos['uso']==28)
										echo '<option value="'.$i.'" selected>'.$i.'</option>';
									else
										echo '<option value="'.$i.'">'.$i.'</option>';
								}
								?>
								</select>
								</td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_GEO']?></td>
								<td ><label>N:</label><input type="text" id="geon" name="geon" class="short" value="" maxlength="100"/> <label>W:</label><input type="text" id="geow" name="geow" class="short" value="" maxlength="100"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_PLANO']?>:</td>
								<td ><input type="text" name="planosituacion" id="planosituacion" class="large" value="" maxlength="200"/></td>
							</tr>
					<tr>
                    <td colspan="2"><h2><?=$str_lang['LANG_HIDRANTES_TIPOH']?></h2>		</td>
                    </tr>
                    <tr>
							    <?php
									$vector=$bd->get_all("tiposhidrantes");
									
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_TIPO']?>:</td>
								<td ><select name="tiposhidrantes" id="tiposhidrantes"> 
								<?php
									
									echo cargarCombo("-1",$vector,"tipohidrante_id","tipohidrante".$sub);
								?>
								</select>
								</td>
							</tr>	
							<tr>
							    <?php
									$vector=$bd->get_all("diametros");
									
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_DIAMETROS']?>:</td>
								<td ><select name="diametros" id="diametros"> 
								<?php
									
									echo cargarCombo("-1",$vector,"diametro_id","diametro".$sub);
								?>
								</select>
								</td>
							</tr>	
							
							<tr>
							    <?php
									$vector=$bd->get_all("racores");
									
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_RACORES']?>:</td>
								<td ><select name="racores" id="racores"> 
								<?php
									
									echo cargarCombo("-1",$vector,"racor_id","racor".$sub);
								?>
								</select>
								</td>
							</tr>	
														
							<tr>
								
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_SENYAL']?>:</td>
								<td ><input type="radio" id="senyalizadoSi" name="senyalizado" value="1" /> <label for="senyalizadoSi"><?=$str_lang['LANG_HIDRANTES_YES']?></label> <input type="radio" id="senyalizadoNo" name="senyalizado" value="0"  /> <label for="senyalizadoNo"><?=$str_lang['LANG_HIDRANTES_NO']?></label> </td>
							</tr>
														
					<tr>
                    <td colspan="2">		
	
					<h2><?=$str_lang['LANG_HIDRANTES_ABAST']?></h2>					
					</td>						
						</tr>					
							<tr>
							
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_EXTERIOR']?>:</td>
								<td ><input type="radio" id="redexteriorSi" name="redexterior" value="1"  /> <label for="redexteriorSi"><?=$str_lang['LANG_HIDRANTES_YES']?></label> <input type="radio" id="redexteriorNo" name="redexterior" value="0" /> <label for="redexteriorNo"><?=$str_lang['LANG_HIDRANTES_NO']?></label> </td>
							</tr>
<tr>
								
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_PRESION']?>Presión&nbsp;y&nbsp;caudal&nbsp;adecuado:</td>
								<td ><input type="radio" id="presionadecuadoSi" name="presionadecuado" value="1" /> <label for="presionadecuadoSi"><?=$str_lang['LANG_HIDRANTES_YES']?></label> <input type="radio" id="presionadecuadoNo" name="presionadecuado" value="0" /> <label for="presionadecuadoNo"><?=$str_lang['LANG_HIDRANTES_NO']?></label> </td>
							</tr>
							
					
														
							
	<tr>
    <td colspan="2">	
					<h2><?=$str_lang['LANG_HIDRANTES_MAN']?></h2>					
					</td>						
						</tr>					
							<tr>
							
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_ESTADO']?>:</td>
								<td ><input type="radio" id="estadogeneralSi" name="estadogeneral" value="1"  /> <label for="estadogeneralSi"><?=$str_lang['LANG_HIDRANTES_YES']?></label> <input type="radio" id="estadogeneralNo" name="estadogeneral" value="0"  /> <label for="estadogeneralNo"><?=$str_lang['LANG_HIDRANTES_NO']?></label> </td>
							</tr>						
									</tbody>	
							
						</table>
						<p class="button" style="margin-top:20px;text-align:center">
							<input type="hidden" name="id" id="id" value="<?php echo $id?>"/>
							<input type="hidden" name="vfotos" id="vfotos" value=""/>
							<input type="hidden" name="nfotos" id="nfotos" value="0"/>
							<input type="submit" value="<?=$str_lang['LANG_HIDRANTES_UPDATE']?>" class="button" id="enviar" name="enviar"/>
						</p>                					
				</div>
		</div>
		</form>
	</div>
	
	<!-- // Contenido -->
