<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );

?>

	<!-- Contenido -->
	<div id="content"  style="margin: 0 0 0 30px;">

		<!-- Encabezado -->
		
		<div class="header-doc">
			<h2><?=$str_lang['LANG_HISTORICO_TITLE_LIST']?></h2>
			
			
		<div class="clr"><hr /></div>			
	
		</div><br/>	
						<!-- Filtros -->
			<div>			
				<form name="frm" action="<?php echo $admin_path.'hidrantes/cartasmunicipio/'?>" method="POST" onsubmit="" target="_blank">
					<!-- Select -->
					<table>
					<caption><b><?=$str_lang['LANG_EXCEL_TITLE3']?></b></caption>
					<tr>
						<?php
							$vector=$bd->get_all("municipios");
							
						?>
						<td ><?=$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO']?>:</td>
						<td ><select name="municipio" id="municipio"> 
						<?php
							
							echo cargarCombo("-1",$vector,"municipio_id","municipio");
						?>
						</select>
						</td>
					
					<td>
						&nbsp;<input type="submit" value="<?=$str_lang['LANG_HIDRANTES_BUSCAR']?>" class="button" name="filter"/> 
					</td>
					</tr>
					
					</table>	
					<div class="clr"><hr /></div>
				</form>
			</div>
			<br/>			
			<div >			
				<form name="frm2" action="<?php echo $admin_path.'hidrantes/cartascodigo/'?>" method="POST" onsubmit="" target="_blank">
					<!-- Select -->
					<table  >
					<caption><b><?=$str_lang['LANG_EXCEL_TITLE4']?></b></caption>
					<tr>
						<td   style="text-align:left"><?=$str_lang['LANG_HIDRANTES_CODIGO']?>:
						</td>
						<td style="text-align:left">
							<input type="text" name="codigo" id="codigo" value="" maxlength="10" />
							
						</td>
					
						<td>
							&nbsp;<input type="submit" value="<?=$str_lang['LANG_HIDRANTES_BUSCAR']?>" class="button" name="filter"/> 
						</td>
					</tr>
									
					
					
					</table>	
					<div class="clr"><hr /></div>
				</form>
			</div>	
					
		</div>
		

	<!-- // Contenido -->