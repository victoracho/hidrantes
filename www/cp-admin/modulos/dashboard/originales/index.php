<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
	
	include $serveradmin_path.'includes/header.php';
	
	
	
?>
	<!-- Contenido -->
	<div id="content">

		<!-- Encabezado -->
		<div class="header-doc desktop">
			<h1><?=$str_lang['LANG_HEADER_TAB_DASH']?></h1>
			<div class="clr"><hr /></div>
		</div>
		<!-- // Encabezado -->
	
		<!-- Avisos importantes -->
		<div class="important">
			<div class="inner">
				<ul>
				<?php
				if (is_admin() || is_gestor())
					echo '<li>'.$str_lang['LANG_DASH_WELLCOME'].', <a href="'.$admin_path.'usuarios/">'.$_SESSION['user'].'</a></li>';
				else
					echo '<li>'.$str_lang['LANG_DASH_WELLCOME'].', '.$_SESSION['user'].'</li>';
				?>
				
				</ul>
			</div>
			<div class="inner">
				<ul>		
		
				<?php
				//Mostramos lista de comentarios
				
				
				$where ="";
				$lista_comentarios="";
				if (is_jefeparque())
				{
					$where .= "parque_id=".$_SESSION['HIDRANTES']['parque_id'];
					//$where .= " and estadocomentario ='EJG'";
					$where .= " and estadocomentario in('','EJG') and not comentario_id in(select distinct comentario_id_jg From  relacion_comentarios )";
					
					$lista_comentarios=$bd->get_all_by_filter_order("V_Comentarios",$where,"fecha desc");	
	

				}elseif (is_consorcio())
				{
					$where .= "estadocomentario ='EJP'";
					$lista_comentarios=$bd->get_all_by_filter_order("V_Comentarios",$where,"fecha desc");	
				}
				
				?>
				
					<br />
					
					
					<?php
					if (!empty($lista_comentarios))
					{
						echo "<h3>".$str_lang['LANG_DASH_LASTCOMMENT']."</h3>";
					}
					
					foreach ($lista_comentarios as $key=>$val)
					{
						
						
						$action_edit=$bd->get_all_by_id("hidrantes","hidrante_id",$val['hidrante_id']);
					?>					
						<li>(<?=convert_date($val["fecha"])?>)<?php printf($str_lang['LANG_DASH_TEXTCOMMENT'],$val['Name'],utf8_encode($val['asociado']))?> <?=$action_edit["codigo"]?></b>&nbsp;<a href="<?=$admin_path."hidrantes/comment/".$val['hidrante_id']."/"?>"><?=$str_lang['LANG_DASH_VER']?></a></li>

					<?php
					
					}
					?>		

					</ul>					
				</div>
					
			<?php
			
				if (!is_consorcio())
				{
				?>
					<div class="inner">
						<ul>
			
				<?php
			
					//Mostramos lista de hidrantes
					$where ="";
					if (is_jefeparque())
						$where .= "parque_id=".$_SESSION['HIDRANTES']['parque_id'];
					$lista_hidrantes=$bd->get_all_by_filter_order("V_hidrantes",$where,"fecha desc");
		
					?>
					
						<br />
						
						
						<?php
						if (!empty($lista_hidrantes))
						{
							echo "<h3>".$str_lang['LANG_DASH_LASTHIDRANTE']."</h3>";
						}
						$i=0;
						foreach ($lista_hidrantes as $key=>$val)
						{
							if ($i==5)
							{
								break;
							}
							
						?>					
						<li>(<?=convert_date($val["fecha"])?>)<?php printf($str_lang['LANG_DASH_TEXTHIDRANTE'],$val['codigo'],$val["tipohidrante".$sub])?> <?=$val["municipio"]?>&nbsp;<a href="<?=$admin_path."hidrantes/view/".$val['hidrante_id']."/"?>"><?=$str_lang['LANG_DASH_VER']?></a></li>

						<?php
						$i++;
						}
						?>		

						</ul>					
					</div>
				<?php
				}
				if (is_consorcio())
				{
				?>
				<div class="inner">
					<ul>

				<?php
	
				
				$titulo=$str_lang['LANG_DASH_CARTAS1'];
				$where="WHERE p.fecharegistro is NULL or p.numeroregistro='' or p.interno=''";
					$titulo=$str_lang['LANG_DASH_CARTAS2'];
				
				$sql="select count(*) as n, p.* from V_Cartas p $where GROUP BY hidrante_id";
				$lista_cartas=$bd->get_all_sql($sql);
	
				?>
				
					<br />
					
					
					<?php
					
					
					echo "<h3>$titulo</h3>";
					$i=0;
					foreach ($lista_cartas as $key=>$val)
					{
					
							?>
							<li>(<?=convert_date($val["fecha"])?>) <?php printf($str_lang['LANG_DASH_TEXTO2'],$val['n'],$val["Name"])?> (<?=$val["codigo"]?>)</b>&nbsp;<a href="<?=$admin_path."hidrantes/cartas/".$val['hidrante_id']."/"?>"><?=$str_lang['LANG_DASH_VER']?></a></li>
							
					<?php		
					}
					if (empty($lista_cartas))
					{
						echo "<li>".$str_lang['LANG_DASH_NO_CARTAS']."</li>";
					}
					?>		

					</ul>					
				</div>
				<?php

				}
				?>
			</div>
			
	</div>
	<!-- // Contenido -->

<?php 
	include $serveradmin_path.'includes/footer.php';
?>
