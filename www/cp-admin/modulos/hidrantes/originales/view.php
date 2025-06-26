
<script type="text/javascript">
	$(function() {
		$("#fecha").datepicker();
		initialize1($("#lat").val(),$("#lon").val());
		initializestreetview($("#lat").val(),$("#lon").val());
		
	});
	
	
</script>

<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
$page=get_id_by_uri2();
	$action_edit=$bd->get_all_by_id("hidrantes","hidrante_id",$id);
	$datos= $action_edit;
	
	// Si no existe el id, salgo.				
	if (empty($action_edit))
		{
			Location($admin_path.$modulo."/");
			exit();
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
			<h1>Ver ficha Hidrante</h1>	
			<p class="head-button"><a href="<?php echo $admin_path?>hidrantes//?&page=<?=$page?>"><span><?=$str_lang['LANG_HIDRANTES_TITLE_VOLVER']?></span></a></p>
			<?php
			$previd = $bd->getPrevId($id,"hidrantes","hidrante_id","codigo");
			$nextid = $bd->getnextId($id,"hidrantes","hidrante_id","codigo");
			if ($previd !="") 
				echo '<p class="head-button"><a href="'.$admin_path.'hidrantes/view/'.$previd['hidrante_id'].'/" > <span>  <img src="'.imgadminpath.'flecha.gif" border="0" width="10" height="13" />  Anterior </span></a></p>';
			if ($nextid !="") 
				echo '<p class="head-button"><a href="'.$admin_path.'hidrantes/view/'.$nextid['hidrante_id'].'/" ><span> Siguiente <img src="'.imgadminpath.'flechaR.gif" border="0" width="10" height="13" />  </span></a></p>';
				
			echo '<p class="head-button"><a href="'.$admin_path.'hidrantes/verficha/'.$id.'/" ><span>Ficha en pdf</span></a></p>';
			?>
			<div class="clr"><hr /></div>
		</div>
		
		
		<div class="client-detail">
				
				<div class="orders2">	
					<h2>Fotos</h2>	
					<div class="clr"><hr /></div>					
					<table id="table-parte2">						
						<tbody>					
											
							<tr>
								<td >
								<div class="fotos">							
								<?php
									//Cargamos las fotos
									$vector=$bd->get_all_by_filter("fotos","hidrante_id=".$id);
									$n=count($vector);
									if ($n==0)
									{
										echo "<h4>No hay fotos</h4>";
									}
									for ($i=0;$i<$n;$i++)
									{
										echo '<div class="foto_i"><a href="'.uploadurladmin."new/new_".$vector[$i]["foto"].'" title="" class="thickbox" rel="gallery"><img src="'.uploadurladmin."mini/mini_".$vector[$i]["foto"].'"  border="0" alt="Foto hidrante '.$i.'" dir="rtl"/></a></div>&nbsp;';
										

									}
									
									?>
									
								</div>
								
								</td>
								
							</tr>	
														
						</tbody>		
						</table>
				
		
					</div>
				
				<div class="orders6">	
					<h2>Situación</h2>	
					<div class="clr"><hr /></div>						
					<table id="table-parte">						
						<tbody>								 					
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_CODIGO']?>:</td>
								<td width="30%"><?php echo utf8_encode($datos['codigo'])?></td>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_FECHA']?>:</td>
								<td ><?php echo convert_date($datos['fecha'])?></td>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_FECHAREVISION']?>:</td>
								<td ><?php echo convert_date($datos['fecharevision'])?></td>
							</tr>
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_CALLE']?>:</td>
								<td ><?php echo $datos['calle']?></td>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_EDIFICIO']?>:</td>
								<td ><?php echo $datos['edificio']?></td>
								
							
							
							<?php
									
									$vector=$bd->get_all_by_id("municipios","municipio_id",$datos["municipio_id"]);
									
								?>
								<td class="title" ><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?>:</td>
								<td ><?=$vector["municipio"]?></td>
							</tr>
				
									<?php

										if (($datos['utmx']!=0)&& ($datos['utmy']!=0))
										{				
											
											if (($datos['geon']==0)||($datos['geow']==0))
											{
												
												$objUTM = convertirCoordenadaUTM($datos['utmx'],$datos['utmy'],$datos['uso']);
												$datos['geon']=number_format($objUTM->Lat(),5,'.', '');
												$datos['geow']=number_format($objUTM->Long(),5, '.', '');
											
											}
										}
										else
										{
											if (($datos['geon']!=0)&&($datos['geow']!=0))
											{
												$objUTM = convertirCoordenadaGEO($datos['geon'],$datos['geow']);
												$datos['utmx']=number_format($objUTM->E(),2 ,'.', '');
												$datos['utmy']=number_format($objUTM->N(),2, '.', '');
												$datos['uso'] =substr($objUTM->Z(),0,2);
											}
										}
										
									
									
									?>									
							<tr>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_UTM']?>:</td>
								<td ><label>X:</label><?php echo $datos['utmx']?>&nbsp;&nbsp;<label>Y:</label><?php echo $datos['utmy']?>&nbsp;&nbsp;<label><?=strtoupper($str_lang['LANG_HIDRANTES_HUSO'])?>:</label><?php echo $datos['uso']?></td>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_GEO']?>:</td>
								<td colspan="3"><label>N:</label><?php echo $datos['geon']?>&nbsp;<label>W:</label><?php echo $datos['geow']?></td>
							</tr>
							<tr>
							<?php
								$ruta= utf8_encode($datos['planosituacion']);
								$n=strlen($ruta);
								if ($n>0)
								{
							?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_PLANO']?>:&nbsp;<a href="<?=utf8_encode($datos['planosituacion']);?>" target="_blank" >Ver</a></td>
							<?php
								}else{
							?>
								<td class="title" >Plano&nbsp;de&nbsp;Situación:</td>
							<?php
								}
							?>
								<td colspan="5"><?php echo utf8_encode($datos['planosituacion'])?></td>
							</tr>
							<?php
						if ($datos['utmx']!=0 && $datos['utmy']!=0)
						{
						?>
						<tr><td><h2>Mapas</h2>	</td></tr>
						<tr>
						<td colspan="6">
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
						</table>					                				
				</div>
				
		
				<div class="orders2">	
					<h2><?=$str_lang['LANG_HIDRANTES_TIPOH']?></h2>
					<div class="clr"><hr /></div>						
					<table id="table-parte">		
						<tbody>					
							<tr>
							    <?php
									
									$vector=$bd->get_all_by_id("tiposhidrantes","tipohidrante_id",$datos["tipohidrante_id"]);
								?>
								<td class="title2" ><?=$str_lang['LANG_HIDRANTES_TIPO']?>:</td>
								<td class="data"><?=$vector["tipohidrante"]?></td>
							
							    <?php

									$vector=$bd->get_all_by_id("diametros","diametro_id",$datos["diametro_id"]);
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_DIAMETROS']?>:</td>
								<td class="data"><?=$vector["diametro"]?>	</td>
							
							    <?php
									$vector=$bd->get_all("racores");
									$vector=$bd->get_all_by_id("racores","racor_id",$datos["racor_id"]);
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_RACORES']?>:</td>
								<td class="data"><?=$vector["racor"]?>			</td>
							
								<?
								if ($datos["senyalizado"])
								{
									$senyalizadoSi="Si";
									$senyalizadoNo="";
								}else{
									$senyalizadoSi="";
									$senyalizadoNo="NO";
									}
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_SENYAL']?>:</td>
								<td class="data"><?=$senyalizadoSi;?><?=$senyalizadoNo;?></td>
							</tr>
														
							
						</table>						           				
				</div>
				
			
				<div class="orders4">	
					<h2><?=$str_lang['LANG_HIDRANTES_ABAST']?></h2>		
					<div class="clr"><hr /></div>						
					<table id="table-parte3">						
						<tbody>					
							<tr>
							<?
								if ($datos["redexterior"])
								{
									$redexteriorSi="Si";
									$redexteriorNo="";
								}else{
									$redexteriorSi="";
									$redexteriorNo="No";
									}
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_EXTERIOR']?>:</td>
								<td ><?=$redexteriorSi;?><?=$redexteriorNo;?></td>
							
								<?
								if ($datos["presionadecuado"])
								{
									$presionadecuadoSi="Si";
									$presionadecuadoNo="";
								}else{
									$presionadecuadoSi="";
									$presionadecuadoNo="No";
									}
								?>
								<td class="title" ><?=$str_lang['LANG_HIDRANTES_PRESION']?>:</td>
								<td ><?=$presionadecuadoSi;?><?=$presionadecuadoNo;?></td>
							</tr>
							
														
							</tbody>		
						</table>
						            					
				</div>
			
				<div class="orders4" >	
					<h2><?=$str_lang['LANG_HIDRANTES_MAN']?></h2>	
<div class="clr"><hr /></div>						
					<table id="table-parte3">						
						<tbody>					
							<tr>
							<?
								if ($datos["estadogeneral"])
								{
									$estadogeneralSi="Si";
									$estadogeneralNo="";
								}else{
									$estadogeneralSi="";
									$estadogeneralNo="No";
									}
								?>
								<td class="title"  ><?=$str_lang['LANG_HIDRANTES_ESTADO']?>:</td>
								<td align="left"><?=$estadogeneralSi;?><?=$estadogeneralNo;?></td>
							</tr>		
							
							</tbody>		
						</table>
				
				</div>
				
				<input type="hidden" name="lat" id="lat" value="<?=$datos['geon']?>" />
				<input type="hidden" name="lon" id="lon" value="<?=$datos['geow']?>" />
		</div>
			
	<!-- // Contenido -->
