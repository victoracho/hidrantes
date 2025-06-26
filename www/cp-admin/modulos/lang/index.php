<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );	
		$p=getUri();
		$_SESSION["Lang"]=$p[3];
		Location($path);
?>