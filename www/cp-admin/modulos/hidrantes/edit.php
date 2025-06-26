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
		if ($("#lat").val()!=0 && $("#lan").val()!=0)
		{

		initialize1($("#lat").val(),$("#lon").val());
		initializestreetview($("#lat").val(),$("#lon").val());
		}
	});
</script>

<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
set_time_limit(2000); 
ini_set ('memory_limit', "128M");
$page=get_id_by_uri2();

	$action_edit=$bd->get_all_by_id("hidrantes","hidrante_id",$id);
	
	// Si no existe el id, salgo.				
	if (empty($action_edit) or is_jefeguardia())
		{
			Location($admin_path.$modulo."/");
			exit();
		}

	
   /*
    * Tengo que ver si el administrativo esta intentando entrar
    * a una tasa que no puede ver
    */ 

	/*
	if (is_gestor())
	{
		if ($action_edit['status']>4)
		{
			Location($admin_path.$modulo."/");
			exit();			
		}
		
	}	
	*/
	/* Datos que necesito */
	//$anio =  substr($action_edit['id_parte'], 0, 4);
	//$regAnual = substr($action_edit['id_parte'], 4);	

	/* SE ENVIA EL FORMULARIO ACTUALIZAR */
	
	if ($_POST['foto_id']>0)
	{		
		unlink(uploadpathadmin."new/new_".$_POST['fotoname_'.$_POST['foto_id']]);
		unlink(uploadpathadmin."mini/mini_".$_POST['fotoname_'.$_POST['foto_id']]);
			$tabla="fotos";
			
			$sql="DELETE FROM ".$tabla." WHERE foto_id= ".$_POST['foto_id'];
			
			$r = $bd->bbdd_query($sql);	
			if (!$r)
			{
				$error= $str_lang['LANG_MSG_ERROR11'];
			}else	{// Todo ok.
		
			$msgOK=$str_lang['LANG_MSG_OK7'];
		}
	}
	
	if (isset($_POST['upload']))
	{
		//var_dump($_POST);
		
		
		$aux= array("name"=>$_FILES["upload_file"]["name"],"type"=>$_FILES["upload_file"]["type"],"size"=>$_FILES["upload_file"]["size"],"tmp_name"=>$_FILES["upload_file"]["tmp_name"],"id"=>$id,"orden"=>$_POST["orden"]);
								
							
		$vfotoname=uploadimages($aux);
		
		if ($vfotoname[1]=="")
		{
			$campos = array (
						"hidrante_id" => $_POST["id"],
						"fototipo_id" => 1,
						"foto"=>$vfotoname[0]
						);
		   $tabla="fotos";
		   $bd->insert($tabla,$campos);	
		   
		   $msgOK = $str_lang['LANG_MSG_OK5'];
		   Location($admin_path."hidrantes/edit/".$id."/");
	   }else
		$error=$vfotoname[1];

			
	}
	
	
	if (isset($_POST['enviar'])) 
	{
		
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
			"calle" => trim($datos['calle']),
			"edificio" => trim($datos['edificio']),
			"fecha" => convert_dateBD(trim($datos['fecha'])),
			"municipio_id" => trim($datos['municipio']),
			"utmx" => $ux,
			"utmy" => $uy,
			"geon" => $gn,
			"geow" => $gw,	
			"obssituacion" => '',
			"tipohidrante_id" => trim($datos['tiposhidrantes']),
			"diametro_id" => trim($datos['diametros']),
			"racor_id" => trim($datos['racores']),
			"senyalizado" => intval(trim($datos['senyalizado'])),
			"redexterior" => intval(trim($datos['redexterior'])),
			"presionadecuado" => intval(trim($datos['presionadecuado'])),
			"obsabastecimiento" => '',
			"estadogeneral" => intval(trim($datos['estadogeneral'])),
			"obsmantenimiento" => '',
			"planosituacion" => trim($datos['planosituacion']),
			"uso" => $datos['uso'],
			"fecharevision" =>	convert_dateBD(trim($datos['fecharevision']))		
		);
		
		if (!empty($datos['codigo']))
		{
		
			$tabla="hidrantes";
			$str="";
			$i=0;
			foreach($campos as $key => $value)
			{	
				if ($i==0) $str.=" ".$key."=".entrada_sql($value);
				else $str.=", ".$key."=".entrada_sql($value);
				$i=1;
			}
			
			$sql="UPDATE ".$tabla." SET ".$str." WHERE hidrante_id =".$datos["id"];
			
			$r = $bd->bbdd_query($sql);	
			if (!$r)
			{
				$error= $str_lang['LANG_MSG_ERROR5'];
			}else	{// Todo ok.
		
			$msgOK=$str_lang['LANG_MSG_OK3'];
						
			}
		}else
			$error= $str_lang['LANG_MSG_ERROR6'];
		
		
	}

	if (isset($error))
	{
		echo '<div id="error"><p>'.$error.'</div>';
	}elseif(isset($msgOK))
	{
		echo '<div id="success"><p>'.$msgOK.'</p></div>';	
	}
	
	$action_edit=$bd->get_all_by_id("hidrantes","hidrante_id",$id);
	$datos_hidrante= $action_edit;
	/*
	 * Obtengo todos los datos del parte asociado.
	 */	
	//$wherePartes = "Anio =".entrada_sql($anio)." and RegAnual=".entrada_sql($regAnual)."";
	//$tPostPartes = $bd->getlisttable("partesdiarios",$wherePartes,$orderPartes,$offsetPartes,$limitPartes);	
	//$datos=$tPostPartes[0];	
	
	
	
?>
	
	
	<!-- Contenido -->
	<div id="content">
		<div class="header-doc">
			<h1><span><?=$str_lang['LANG_HIDRANTES_EDIT']?></span></h1>			
            <div class="clr"><hr /></div>
			<p class="head-button"><a href="<?php echo $admin_path?>hidrantes//?&page=<?=$page?>"><span><?=$str_lang['LANG_HIDRANTES_TITLE_VOLVER']?></span></a></p>
			<?php
			$previd = $bd->getPrevId($id,"hidrantes","hidrante_id","codigo");
			$nextid = $bd->getnextId($id,"hidrantes","hidrante_id","codigo");
			if ($previd !="") 
				echo '<p class="head-button"><a href="'.$admin_path.'hidrantes/edit/'.$previd['hidrante_id'].'/" > <span>  <img src="'.imgadminpath.'flecha.gif" border="0" width="10" height="13" />  Anterior </span></a></p>';
			if ($nextid !="") 
				echo '<p class="head-button"><a href="'.$admin_path.'hidrantes/edit/'.$nextid['hidrante_id'].'/" ><span> Siguiente <img src="'.imgadminpath.'flechaR.gif" border="0" width="10" height="13" />  </span></a></p>';
			echo '<p class="head-button"><a href="'.$admin_path.'hidrantes/verficha/'.$id.'/" ><span>Ficha en pdf</span></a></p>';
			?>
			
			
			<div class="clr"><hr /></div>
		</div>
		<!-- Encabezado -->
		<div class="header-doc">
			
		</div>
		
		
		
		<div class="client-detail">
		
		
		<form name="frmupload" id="frmupload" action="<?php echo $action_form?>" method="POST" enctype="multipart/form-data">	
		<div class="orders2">	
			<h2><?=$str_lang['LANG_HIDRANTES_FOTO']?></h2>		
	<div class="clr"><hr /></div>	
			<table id="table-parte"  >						
				<tbody>		
<?php
							//Cargamos las fotos
							$vector=$bd->get_all_by_filter("fotos","hidrante_id=".$id);
							//$vector=$bd->get_all("fotos");
							$n=count($vector);
							if ($n<maxfotos){
						?>
				
					<tr>							
						<td class="title" style="width:10%" ><?=$str_lang['LANG_HIDRANTES_FOTO_ADD']?>:</td>
						<td style="width:20%"> <input type="file" id="upload_sp" name="upload_file"  class="large"/></td>
						<?php	
							if ($n<maxfotos){
							?>
							<td style="width:40%;text-align:left;"> 
								<input type="submit" value="<?=$str_lang['LANG_HIDRANTES_FOTO_ADD']?>" class="button" id="upload" name="upload"/>
						
							</td>
							<?php
							}
						?>	
						
					</tr>				
							<?php					
							}
							?>
					<tr>
						<td colspan="4">
						<div class="fotos">							
						<?php
							//Cargamos las fotos
						//	$vector=$bd->get_all_by_filter("fotos","hidrante_id=".$id);
							//$vector=$bd->get_all("fotos");
							//$n=count($vector);
							
							for ($i=0;$i<$n;$i++)
							{
								if ($i==maxfotos){
								break;
								}
								echo '<div class="foto_i"><a href="'.uploadurladmin."new/new_".$vector[$i]["foto"].'" title="" class="thickbox" rel="gallery"><img src="'.uploadurladmin."mini/mini_".$vector[$i]["foto"].'?p='.rand(100,1000).'"  border="0" alt="Foto hidrante '.$i.'" dir="rtl"/></a><br /><a href="#" class="borrafoto" onclick="javascript:eliminar('.$vector[$i]["foto_id"].');">'.$str_lang['LANG_HIDRANTES_FOTO_DEL'].'</a></div>&nbsp;';
								echo '<input type="hidden" name="fotoname_'.$vector[$i]["foto_id"].'"  value="'.$vector[$i]["foto"].'"/>';
								
								/*$tam=getimagesize(uploadurladmin.$vector[$i]["foto"]); 
								//var_dump($tam);

								//if($tam[0] > 500 || $tam[1] > 500)
								//{

									cambiartam(uploadpathadmin.$vector[$i]["foto"], uploadpathadmin."new/new_".$vector[$i]["foto"], 200, 200);
								//}*/

							}
							
							?>
							
						
						</div>
								
														
						
						
						</td>
						
					</tr>	
												
				</tbody>		
				</table>
					<input type="hidden" name="foto_id" id="foto_id" value=""/>
					<input type="hidden" name="id" id="id" value="<?php echo $id?>"/>
					<input type="hidden" name="orden" id="orden" value="<?php echo ($n+1)?>"/>
				
							
		</div>
		</form>
		<form name="frm" action="<?php echo $action_form?>" method="POST">	
				<div class="orders2">	
					<h2><?=$str_lang['LANG_HIDRANTES_SITUACION']?></h2>		
					<div class="clr"><hr /></div>						
					<table id="table-parte">						
						<tbody>		
						 						
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_CODIGO']?>:</td>
								<td ><input type="text" name="codigo" id="codigo" class="short" value="<?php echo utf8_encode($datos_hidrante['codigo'])?>" maxlength="6"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_CALLE']?>:</td>
								<td ><input type="text" name="calle" id="calle" class="large" value="<?php echo $datos_hidrante['calle']?>" maxlength="150"/></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_EDIFICIO']?>:</td>
								<td ><input type="text" name="edificio" id="edificio" class="large" value="<?php echo utf8_encode($datos_hidrante['edificio'])?>" maxlength="100"/></td>
							</tr>	
							
							<tr>
							<?php
									$vector=$bd->get_all("municipios");
									
								?>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?>:</td>
								<td ><select name="municipio" id="municipio"> 
								<?php
									echo "dd".$datos_hidrante['municipio_id'];
									echo cargarCombo($datos_hidrante['municipio_id'],$vector,"municipio_id","municipio");
								?>
								</select>
								</td>
							</tr>	
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_FECHA']?>:</td>
								<td ><input type="text" name="fecha" id="fecha" class="short" value="<?php echo convert_date($datos_hidrante['fecha'])?>" maxlength="100"/></td>
							</tr>
							
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_FECHAREVISION']?>:</td>
								<td ><input type="text" name="fecharevision" id="fecharevision" class="short" value="<?php echo convert_date($datos_hidrante['fecharevision'])?>" maxlength="100"/>&nbsp;<a href="" id="afecharevision"><?=$str_lang['LANG_HIDRANTES_ACTFECHAREVISION']?></a></td>
							</tr>
									<?php
									
									if (($datos_hidrante['utmx']!=0)&& ($datos_hidrante['utmy']!=0))
										{				
											
											if (($datos_hidrante['geon']==0)||($datos_hidrante['geow']==0))
											{
												
												$objUTM = convertirCoordenadaUTM($datos_hidrante['utmx'],$datos_hidrante['utmy'],$datos_hidrante['uso']);
												$datos_hidrante['geon']=number_format($objUTM->Lat(),5, '.', '');
												$datos_hidrante['geow']=number_format($objUTM->Long(),5, '.', '');
											
											}
										}
										else
										{
											if (($datos_hidrante['geon']!=0)&&($datos_hidrante['geow']!=0))
											{
												$objUTM = convertirCoordenadaGEO($datos_hidrante['geon'],$datos_hidrante['geow']);
												$datos_hidrante['utmx']=number_format($objUTM->E(),2, '.', '');
												$datos_hidrante['utmy']=number_format($objUTM->N(),2, '.', '');
												$datos_hidrante['uso'] =substr($objUTM->Z(),0,2);
											}
										}
									/*$objUTM = convertirCoordenadaUTM($datos_hidrante['utmx'],$datos_hidrante['utmy'],$datos_hidrante['uso']);
									
									if (($datos['geon']==0)||($datos['geow']==0))
									{
										$datos_hidrante['geon']=number_format($objUTM->Lat(),5);
										$datos_hidrante['geow']=number_format($objUTM->Long(),5);
									}*/
									
									?>									
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_UTM']?></td>
								<td ><label>X:</label><input type="text" id="utmx" name="utmx" class="short" value="<?php echo utf8_encode($datos_hidrante['utmx'])?>" maxlength="100"/> <label>Y:</label> <input type="text" id="utmy" name="utmy" class="short" value="<?php echo utf8_encode($datos_hidrante['utmy'])?>" maxlength="100"/>
								&nbsp;&nbsp;<?=strtoupper($str_lang['LANG_HIDRANTES_HUSO'])?>
								<select name="uso">
								<?php
								if ($datos_hidrante['uso']=="")
									$datos_hidrante['uso']=28;
								for($i=28;$i<=35;$i++)
								{
									if ($datos_hidrante['uso']==$i)
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
								<td ><label>N:</label><input type="text" id="geon" name="geon" class="short" value="<?php echo utf8_encode($datos_hidrante['geon'])?>" maxlength="100"/> <label>W:</label><input type="text" id="geow" name="geow" class="short" value="<?php echo utf8_encode($datos_hidrante['geow'])?>" maxlength="100"/></td>
							</tr>
							<tr>
							<?php
								$ruta= utf8_encode($datos_hidrante['planosituacion']);
								$n=strlen($ruta);
								if ($n>0)
								{
							?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_PLANO']?>:&nbsp;<a href="<?=utf8_encode($datos_hidrante['planosituacion']);?>" target="_blank" >Ver</a></td>
							<?php
								}else{
							?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_PLANO']?>:</td>
							<?php
								}
							?>
								<td ><input type="text" name="planosituacion" id="planosituacion" class="large" value="<?php echo utf8_encode($datos_hidrante['planosituacion'])?>" maxlength="200"/></td>
							</tr>
					
						</tbody>
						</table>					                				
				</div>
				<div class="orders6">	
				<h2>Mapas</h2>	
					<div class="clr"><hr /></div>						
					<table id="table-parte">						
						<tbody>		
						 						
					
					
						<?php
						if ($datos_hidrante['utmx']!=0 && $datos_hidrante['utmy']!=0)
						{
						?>
						<tr>
						<td >
						<div id="contentmap">
							<div id="map_canvas" ></div>
							<div id="pano" ></div>
							<div class="clr"><hr /></div>          					
						</div>
						<div id="contentg">
							<div id="contentleft"><a href="<?php echo $admin_path?>hidrantes/googlemapampliado/<?=$id?>/?keepThis=false&op=1&TB_iframe=false&height=500&width=585" class="thickbox" title="">Ampliar mapa</a></div>
							<div id="contentright"><a href="<?php echo $admin_path?>hidrantes/googlemapampliado/<?=$id?>/?keepThis=true&op=2&TB_iframe=true&height=490&width=585" class="thickbox" title="">Ampliar mapa</a>							</div>
						</div>
						</td>
						</tr>
						<?php
						}
						?>
						</tbody>	</table>					                				
				</div>
				<div class="orders">	
					<h2><?=$str_lang['LANG_HIDRANTES_TIPOH']?></h2>					
					<table id="table-parte">						
						<tbody>					
							<tr>
							    <?php
									$vector=$bd->get_all("tiposhidrantes");
									
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_TIPO']?>:</td>
								<td ><select name="tiposhidrantes" id="tiposhidrantes"> 
								<?php
									
									echo cargarCombo($datos_hidrante['tipohidrante_id'],$vector,"tipohidrante_id","tipohidrante".$sub);
								?>
								</select>
								</td>
							</tr>	
							<tr>
							    <?php
									$vector=$bd->get_all("diametros","orden");
									
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_DIAMETROS']?>:</td>
								<td ><select name="diametros" id="diametros"> 
								<?php
									
									echo cargarCombo($datos_hidrante['diametro_id'],$vector,"diametro_id","diametro".$sub);
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
									
									echo cargarCombo($datos_hidrante['racor_id'],$vector,"racor_id","racor".$sub);
								?>
								</select>
								</td>
							</tr>	
														
							<tr>
								<?
								if ($datos_hidrante["senyalizado"])
								{
									$senyalizadoSi="checked";
									$senyalizadoNo="";
								}else{
									$senyalizadoSi="";
									$senyalizadoNo="checked";
									}
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_SENYAL']?>:</td>
								<td ><input type="radio" id="senyalizadoSi" name="senyalizado" value="1" <?=$senyalizadoSi;?>/> <label for="senyalizadoSi"><?=$str_lang['LANG_HIDRANTES_YES']?></label> <input type="radio" id="senyalizadoNo" name="senyalizado" value="0"  <?=$senyalizadoNo;?>/> <label for="senyalizadoNo"><?=$str_lang['LANG_HIDRANTES_NO']?></label> </td>
							</tr>
														
							
						</table>						           				
				</div>
				
			
				<div class="orders">	
					<h2><?=$str_lang['LANG_HIDRANTES_ABAST']?></h2>					
					<table id="table-parte">						
						<tbody>					
							<tr>
							<?
								if ($datos_hidrante["redexterior"])
								{
									$redexteriorSi="checked";
									$redexteriorNo="";
								}else{
									$redexteriorSi="";
									$redexteriorNo="checked";
									}
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_EXTERIOR']?>:</td>
								<td ><input type="radio" id="redexteriorSi" name="redexterior" value="1" <?=$redexteriorSi;?> /> <label for="redexteriorSi"><?=$str_lang['LANG_HIDRANTES_YES']?></label> <input type="radio" id="redexteriorNo" name="redexterior" value="0" <?=$redexteriorNo;?> /> <label for="redexteriorNo"><?=$str_lang['LANG_HIDRANTES_NO']?></label> </td>
							</tr>
<tr>
								<?
								if ($datos_hidrante["presionadecuado"])
								{
									$presionadecuadoSi="checked";
									$presionadecuadoNo="";
								}else{
									$presionadecuadoSi="";
									$presionadecuadoNo="checked";
									}
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_PRESION']?>:</td>
								<td ><input type="radio" id="presionadecuadoSi" name="presionadecuado" value="1" <?=$presionadecuadoSi;?> /> <label for="presionadecuadoSi"><?=$str_lang['LANG_HIDRANTES_YES']?></label> <input type="radio" id="presionadecuadoNo" name="presionadecuado" value="0" <?=$presionadecuadoNo;?> /> <label for="presionadecuadoNo"><?=$str_lang['LANG_HIDRANTES_NO']?></label> </td>
							</tr>
							
										
							
						</table>
						            					
				</div>
			
				<div class="orders">	
					<h2><?=$str_lang['LANG_HIDRANTES_MAN']?></h2>					
					<table id="table-parte">						
						<tbody>					
							<tr>
							<?
								if ($datos_hidrante["estadogeneral"])
								{
									$estadogeneralSi="checked";
									$estadogeneralNo="";
								}else{
									$estadogeneralSi="";
									$estadogeneralNo="checked";
									}
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_ESTADO']?>:</td>
								<td ><input type="radio" id="estadogeneralSi" name="estadogeneral" value="1" <?=$estadogeneralSi;?> /> <label for="estadogeneralSi"><?=$str_lang['LANG_HIDRANTES_YES']?></label> <input type="radio" id="estadogeneralNo" name="estadogeneral" value="0" <?=$estadogeneralNo;?> /> <label for="estadogeneralNo"><?=$str_lang['LANG_HIDRANTES_NO']?></label> </td>
							</tr>						
						<!--	<tr>
								<td class="title" >Observaciones de mantenimiento:</td>
								<td ><textarea id="obsmantenimiento" name="obsmantenimiento"  class="lang-edit"><?php echo $datos_hidrante['obsmantenimiento']?></textarea></td>
							</tr>	
							-->							
							
						</table>
						<p class="button" style="margin-top:20px;text-align:center">
							<input type="hidden" name="id" id="id" value="<?php echo $id?>"/>
							<input type="submit" value="<?=$str_lang['LANG_HIDRANTES_UPDATE']?>" class="button" id="enviar" name="enviar"/>
							<input type="hidden" name="lat" id="lat" value="<?=$datos_hidrante['geon']?>" />
							<input type="hidden" name="lon" id="lon" value="<?=$datos_hidrante['geow']?>" />
						</p>                					
				</div>
		</form>	
		
		</div>
		
		
	</div>
	
	<!-- // Contenido -->
