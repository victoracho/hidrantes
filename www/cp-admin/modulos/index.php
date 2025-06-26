<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
		// Sistema de Login.
	
		include $serveradmin_path.'includes/login_header.php'; 

		// Login
		if (isset($_POST['submit']))
			if (validate_user())
			{			
				if (!is_jefeguardia()) // Si es administrador al dashboard.
					
					 Location($admin_path."dashboard/"); // Si no para fuera.
				else 
		     	{
					if (is_jefeguardia() )
					{
						Location($admin_path."hidrantes/"); // Si no para fuera.
					}
					else
					{
						Logout();
						Location(path);		     		
					}
		     	}
		     exit;
			}
			else $error=1;
			//<a href="<$admin_pathlang/ES/">Español</a>&nbsp;<a href="admin_pathlang/PT/" >Portugués </a>
?>
			
			<p class="logo"><?=$str_lang['LANG_AUTOR']?></p>
			  <?php 
			  if ($error==1) echo'
			  	<div id="warning">
					<p><strong>Error: el nombre de usuario no es válido</strong></p>
				</div>';
			  ?>
			<div class="access">
				<div class="inner">
					<form action="<?php echo $admin_path?>" method="post">
						<label for="user"><?=$str_lang['LANG_LOGIN_USER']?> <input type="text" id="user" name="user"/></label>
						<label for="pass"><?=$str_lang['LANG_LOGIN_PASS']?> <input type="password" id="pass" name="pass"/></label>
						<label for="remember_me" class="check-radio"><input type="checkbox" name="remember_me" id="remember_me" checked="checked" value="1"/> <?=$str_lang['LANG_LOGIN_RECORDAR']?></label>
						<p><input type="submit" name="submit" value="<?=$str_lang['LANG_LOGIN_ACCESS']?>" class="button" /></p>
					</form>
				</div>
			</div>			  

	<?php
                $bd_login->bbdd_desc();
		include $serveradmin_path.'includes/login_footer.php';
?>