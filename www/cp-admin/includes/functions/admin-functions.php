<?php
	function adminUrlRewrite(){
		$retPath=array();
		$requestURI = explode('/',$_SERVER['REQUEST_URI']);
		$command = array_values($requestURI);
		
		return $command[3]; // Devuelve la acción.
	}
	
	function getSelectedNav($modulo,$uri)
	{
	   $str='';
	   if ($uri==$modulo) $str.='class="active"';
	   return $str;
	}	
	
	function getSelectedSubNav($status,$current)
	{
	   $str='';
	   if ($status==$current) $str.='class="active"';
	   return $str;
	}	
		
	function get_id_by_uri(){
		$retPath=array();
		$requestURI = explode('/',$_SERVER['REQUEST_URI']);
		$command = array_values($requestURI);
		if (is_numeric($command[4]))
		return $command[4];		// Devuelve el id de la accion
		else return 0;
	}
	function get_id_by_uri2(){
		$retPath=array();
		$requestURI = explode('/',$_SERVER['REQUEST_URI']);
		$command = array_values($requestURI);
		if (is_numeric($command[5]))
		return $command[5];		// Devuelve el id de la accion
		else return -1;
	}
	function get_id_by_uri3(){
		$retPath=array();
		$requestURI = explode('/',$_SERVER['REQUEST_URI']);
		$command = array_values($requestURI);		
		return $command[6];		// Devuelve el id de la accion
		
	}
	
	function get_id_by_uri_i($i){
		$retPath=array();
		$requestURI = explode('/',$_SERVER['REQUEST_URI']);
		$command = array_values($requestURI);		
		return $command[$i];		// Devuelve el id de la accion
		
	}
	
	function noCache() {


	  /*header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
	  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	  header("Cache-Control: no-store, no-cache, must-revalidate");
	  header("Cache-Control: post-check=0, pre-check=0", false);
	  header("Pragma: no-cache");*/
	  
	  header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
	//  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	  header("Cache-Control: no-cache");
	  header("Pragma: no-cache");
	}
	
	
	
?>