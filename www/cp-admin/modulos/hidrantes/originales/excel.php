<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
$cal=getPrefijoCal();
?>
<script type="text/javascript">
	var control=false;
	$(function() {
		$("#fechaini").datepicker($.datepicker.regional['<?=$cal?>']);			
		$("#fechafin").datepicker($.datepicker.regional['<?=$cal?>']);		
		if (!control)
			$("#error").hide();
	});
	
	function borrar()
	{
		$("#error").hide();

	}
		
		function enviar(url,file)
		{		
			borrar();
			$.ajax({
			  type: 'POST',
			  url: url,
			  data: $("#frm2").serialize(),
			  success: function(msg)
			  {
				
				 msg = msg.replace(/^\s*|\s*$/g,"");
				 
				if (msg!='-1')
				{
				
					$("#error").show();
					$("#error").html(msg);
				}else
				{
					
					document.location.href=file;
								
				}
				
			  }			  
			});
			
		}
</script>
<?php
		
	if (isset($_POST['municipio']))
	{
		$where="1=1";	
		if (is_jefeparque())
				$where.=" and parque_id=".$_SESSION['HIDRANTES']['parque_id'];

		if ($_POST['municipio'] != "-1")
					$where .= " and municipio_id=".$_POST['municipio'];	
						
		$vector=$bd->get_all_by_filter_order("V_hidrantes",$where,"municipio asc");

		$n=count($vector);
		if ($n>0){
					
		}
		else
		{
			$msgError=$str_lang['LANG_MSG_ERROR24'];
		}
		
	}
	
	if (isset($_POST['fechaini']))
	{
		$where="1=1";	
		if (is_jefeparque())
				$where.=" and parque_id=".$_SESSION['HIDRANTES']['parque_id'];

				
		if ($_POST['fechaini']!="" && $_POST['fechafin']!="")
			$where .=" and fechacomentario BETWEEN '".convert_dateBD($_POST['fechaini'])."' AND '".convert_dateBD($_POST['fechafin'])."'";
		elseif ($_POST['fechaini']!="")
			$where .=" and fechacomentario = '".convert_dateBD($_POST['fechaini'])."'";			
		elseif ($_POST['fechafin']!="")
			$where .=" and fechacomentario = '".convert_dateBD($_POST['fechafin'])."'";
		$vector=$bd->get_all_by_filter_order("V_HidrantesComentarios",$where,"codigo asc, fechacomentario desc");

		$n=count($vector);
		if ($n>0){
			Location($admin_path.'hidrantes/hidrantesfechas/');
			exit;			
		}
		else
		{
			$msgError=$str_lang['LANG_MSG_ERROR24'];
		}
	}
	if (isset($_POST['codigo']))
	{
		$where="1=1";	
		if (is_jefeparque())
				$where.=" and parque_id=".$_SESSION['HIDRANTES']['parque_id'];

		$codigo =trim($_POST["codigo"]);	
		if ($codigo !="")
			$where .=" and codigo like '%".$codigo."%'";			

		$vector=$bd->get_all_by_filter_order("V_hidrantes",$where,"codigo asc");	
		$n=count($vector);
		if ($n>0){
					
		}
		else
		{
			$msgError=$str_lang['LANG_MSG_ERROR24'];
		}
	}
	
	echo '<div id="error"><p>'.$msgError.'</div>';
	if (isset($msgError))
	{
	?>
	<script type="text/javascript">
		control =true;
		$("#error").show();
	</script>
	<?php
	
	}else
	{
		if (isset($_POST['codigo']))
		{
			Location($admin_path.'hidrantes/hidrantescodigo/'.$codigo.'/');
			//exit;	
		}
		if (isset($_POST['municipio']))
		{
			Location($admin_path.'hidrantes/hidrantesmunicipio/'.$_POST['municipio'].'/');
			//exit;	
		}
	}
?>


	<!-- Contenido -->

	<div id="content"  style="margin: 0 0 0 30px;">

		<!-- Encabezado -->
		
		<div class="header-doc">
			<h2><?=$str_lang['LANG_HIDRANTES_EXCEL']?></h2>
			
			
		<div class="clr"><hr /></div>			
	
		</div><br/>	
						<!-- Filtros -->
						
						
			<div>			
				<form name="frm" action="<?php echo $action_form?>" method="POST" onsubmit="borrar();">
					<!-- Select -->
					<table>
					<caption style="text-align:left;"><b><?=$str_lang['LANG_EXCEL_TITLE4']?></b></caption>
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
			<br/>				
			<div>			
				<form name="frm3" action="<?php echo $action_form?>" method="POST" onsubmit="borrar();">
					<!-- Select -->
					<table>
					<caption style="text-align:left;"><b><?=$str_lang['LANG_EXCEL_TITLE1']?></b></caption>
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
				<form name="frm2" id="frm2"  action="<?=$admin_path.'hidrantes/hidrantesfechas/'?>" method="POST" onsubmit="">
					<!-- Select -->
					<table  style="width:40%;">
					<caption style="text-align:left;"><b><?=$str_lang['LANG_EXCEL_TITLE2']?></b></caption>					
					<tr>
						<td ><?=$str_lang['LANG_COMENTARIOS_FECHA']?>:</td>
						<td style="text-align:left">
							<input type="text" name="fechaini" id="fechaini" value="<?=date('d/m/Y')?>" maxlength="10" />&nbsp;hasta&nbsp;
							<input type="text" name="fechafin" id="fechafin" value="<?=date('d/m/Y')?>" maxlength="10" />
						</td>
					
					<td>
						&nbsp;<input type="button" value="<?=$str_lang['LANG_HIDRANTES_BUSCAR']?>" class="button" name="filter" onclick="enviar('<?=$admin_path.'hidrantes/hidrantesfechas/'?>','<?=uploadurladmin.'export/hidrantesPorFecha.xls'?>');return false;"/> 
					</td>
					</tr>
					<tr>
						<td colspan="3" style="text-align:left"><input type="checkbox" name="comentarios" id="comentarios3" value="1" />&nbsp;<label for="comentarios3"><?=$str_lang['LANG_EXCEL_COMENTARIOS']?></label></td>
						
							
						</td>
				
					</tr>
					
					</table>	
					<div class="clr"><hr /></div>
				</form>
			</div>	
					
		</div>
		
<div id="resexcel"></div>
	<!-- // Contenido -->