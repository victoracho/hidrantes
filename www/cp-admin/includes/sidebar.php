<?php defined( '_VALID_MOS' ) or die( 'Restricted access' ); ?>
			<div class="right_side">
				<div class="block_right">
				<?php
				switch ($modulo){
					case 'dashboard':
						echo '<p>Bienvenido <strong>'.$_SESSION['user'].'</strong>.
								Desde este panel puedes controlar la parte dinámica de la página web.
								Para cualquier duda, puedes consultarnos en <strong>'.creator_email.'</strong>.
								</p>';
						break;
					case 'idiomas':
						echo '<p>Recuerda que las noticias y las páginas estan asignadas a un idiomas. Por lo tanto,
								si eliminas un idioma, todas las noticias y páginas <strong>se moverán</strong> al idioma "español".
								</p>								
								<p>El campo enlace permite <strong>dirigir tu web a los difentes idiomas</strong> dados de alta en la aplicación.
								Si sólo tienes un idioma, el defecto, tu web tiene un idioma y el enlace no tendrá validez.
								Si das de alta 2 o más idiomas, el enlace determinará la dirección web. Es decir,
								si tienes el Ingles (enlace, eng) y Español (enlace, esp), las web se dirigirán a:
								<strong>http://eng.laweb.com/ y a http://esp.laweb.com/</strong> .</p>
								<p>Ten en cuenta, que el campo <strong>"Enlace"</strong> no permite acentos, espacios en blanco
								o las "ñ"</p>';
						break;
					case 'paginas':
						echo '<p>Las páginas son el contenido de la web. Puedes añadir páginas y dejarlas pendiente
								de publicación o publicarlas a una determinada fecha.</p>';
						break;														
					case 'correos':
						echo '<p>Esta opción te permite enviar correos de información a los usuarios
								que han decidido subscribirse a tu boletín de noticias e información.
								</p>';
						break;						
					default:
						break;
				}
				?>

				</div>	
		    </div>		