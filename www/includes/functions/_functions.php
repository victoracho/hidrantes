<?php
	 function obtener_ip()
	 {
		global $HTTP_SERVER_VARS;
		if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"] != "")
			$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
		else
			$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
		return($ip);
	}
	
	function create_key($len){
	   for($i=1;$i<=$len;$i++)$str.=base_convert( rand(0,15),10,16);
	   return $str;
	}
	function create_alfa_key($len)
	{	
			$keychars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";			
			$length = $len;				
			$randkey = "";			
			$max=strlen($keychars)-1;			
			for ($i=0;$i<$length;$i++)			
			$randkey .= substr($keychars, rand(0, $max), 1);
			return $randkey;
	}

	function specialchars( $text, $quotes = 0 ) {
		// Elementos que tenemos que quitar.
		$text = str_replace('&&', '&#038;&', $text);
		$text = str_replace('&&', '&#038;&', $text);
		$text = preg_replace('/&(?:$|([^#])(?![a-z1-4]{1,8};))/', '&#038;$1', $text);
		$text = str_replace('<', '&lt;', $text);
		$text = str_replace('>', '&gt;', $text);
		if ( 'double' === $quotes ) {
			$text = str_replace('"', '&quot;', $text);
		} elseif ( 'single' === $quotes ) {
			$text = str_replace("'", '&#039;', $text);
		} elseif ( $quotes ) {
			$text = str_replace('"', '&quot;', $text);
			$text = str_replace("'", '&#039;', $text);
		}
		return $text;
	}

	function escape($text) {
		$safe_text = specialchars($text, true);
		return $safe_text;
	}

	function entrada_sql($valor)
	{
	    // Retirar las barras si es necesario
	    if (get_magic_quotes_gpc()) {
	        $valor = stripslashes($valor);
	    }
	    	    
	    // Colocar comillas si no es un entero
	    if (!is_int($valor) && strtoupper ($valor)!='NULL') {
	        $valor = "'" . mysql_real_escape_string($valor) . "'";
	    }

	    return $valor;
	}

	function Location($url){
    header("Location:{$url}");
    exit;
    }

	function urlRewrite()
	{
		global $bd;

		$retPath=array();
		
		// datos de entrada
		$requestURI = explode('/',$_SERVER['REQUEST_URI']);	
			
		$s_host=$_SERVER['HTTP_HOST'];
		$host=explode(".",$s_host);
		if (empty($host[0])) // Si envia http://dominio.com lo redireeccion a www.dominio.com
		{
				Location(path);
				exit();
		}		
		// Total idiomas
		$cantLang=$bd->getcountlang();

		$lang=$bd->get_lang_by_uri('www');
		
		// Compruebo si es admin, porque si es admin necesito que vaya al www.
		// Ademas puedo cambiar cosas si no funciona la web si lo pongo aqui.		
		 if ($requestURI[1]==item_admin)
		 {			
		 	if (($host[0]!='tasas') and ($host[0]!='localhost')and ($host[0]!='127'))
			{			 						
				Location(path.item_admin);
				exit();
			}									 	
		 }		
		elseif (empty($lang)) 
		{
			if (($host[0]!='www') and ($host[0]!='localhost')and ($host[0]!='127'))
			{
				Location(path);
				exit();
			}
			else die("Tienes que modificar el enlace del idioma y ponerlo a 'www', si solo hay un idioma");				
		}
		
	/*	if (($host[0]=='www')||($host[0]=='localhost')||($host[0]=='127'))
		{
			if (($cantLang>1) and ($requestURI[1]!=item_admin)) // Si vamos a admin, que no redireccione el idioma.
			{
				// El idioma sera el defecto
				$lang=$bd->get_lang(1);
				$url=str_replace('www',$lang['uri'],path);
				Location($url);
				exit();
			}
		}
		else
		  {
			// Compruebo que el idioma existe en la bd y es valido.
			// Sino lo mandamos fuera.
			$lang=$bd->get_lang_by_uri($host[0]);
				if (empty($lang)) 
				{
					if ($cantLang>1)
						{
						$lang=$bd->get_lang(1);
						$url=str_replace($host[0],$lang['uri'],path);
						Location($url);	
						}
					else Location(path);
				}
				else 
				{
					if ($cantLang==1) Location(path);									
				}					
		    }*/
		$retPath[]="http://".$s_host."/";
		// Modulos error
		$path=$retPath[0];
	  // Esta bien redireccionado el idioma
	  
	  // Tenemos que obtener el modulo
		$requestURI = explode('/',$_SERVER['REQUEST_URI']);	

		$cUri=count($requestURI)-1;
		//if (!empty($requestURI[$cUri]) and (!strstr($requestURI[$cUri],"?page"))) $modulo="error.php";
		// CAMBIO, porque Porque cuando envio el formulario, el action no tiene ?page
		if (!empty($requestURI[$cUri]) and (!strstr($requestURI[$cUri],"?"))) {Location($path.item_error."/");die();}
		else 
		{		
			//Borro el último elemento que esta vacio
			unset($requestURI[$cUri]);
			
			$fModulo=escape($requestURI[1]); // Es el siguientes despues del path.			
			$lModule=escape($requestURI[$cUri-1]);
			
			//Para las comprobaciones necesito un nuevo array.
			foreach ($requestURI as $value)
           		if(!empty($value)) $command[]=$value;
			
			switch ($fModulo)
			{
				case '': // Vacio, es el home
					$modulo="home.php";
					break;	
				case item_error:
					$modulo="error.php";
					break;														
				case item_news: // Noticias
					$modulo="post.php";
					$datos=$bd->get_all_by_uri($lModule);
					// Si datos esta vacio, entonces es una categoria.Si no es un post.
					if (!empty($datos))
						{
						$retcheck=checkpath("post",$command,$datos['id'],$lang);
						if ($retcheck===false) {Location($path.item_error."/");die();}		
						}
					else{
							$cat_id=$bd->get_category_id($lModule); //Categoria superior
							//if (empty($cat_id))	{Location($path.item_error."/");die();}
							if (empty($cat_id)) {$datos='';$modulo="category.php";}// Pagina principal de noticias.
							else {
								   // el modulo es categoria, pero tengo que tener
								   // en cuenta que puede haber dos categorias con el mismo nombre
								   // pero con diferente padre y la URI de esa categoria es repetida
									$modulo="category.php";
									// category se refiere a los titulos, porque los id seran diferentes.
									$retcheck=checkpath("category",$command,$cat_id,$lang);
									if ($retcheck===false) {Location($path.item_error."/");die();}	
									
									$datos=$bd->get_category_by_uri($lModule);														
								}						
						}
					break;
				case item_search: // Busqueda
					$modulo="search.php";
					break;
				case item_feed: // Feeds
					$modulo="feed.php";
					break;			
				case item_admin: // Administracion
					$modulo=item_admin;
					$datos=escape($requestURI[2]); // Los modulos del admin.
					break;
				case item_contacto: // Vacio, es el home
					$modulo=item_contacto;
					$datos=escape($requestURI[2]); // Los modulos del anuncio.
					break;
				case item_inscripcion: // Vacio, es el home
					$modulo=item_inscripcion;
					$datos=escape($requestURI[2]); // Los modulos del anuncio.
					break;															
				case item_galeria: // galeria
					$modulo=galeria;
					$datos=escape($requestURI[2]); // Los modulos de la tienda.
					break;																		
				default:
					$modulo="page.php";
					$datos=$bd->get_all_by_uri($lModule);
					$retcheck=checkpath("page",$command,$datos['id'],$lang);
					if ($retcheck===false) {Location($path.item_error."/");die();}			
					break;								
			}
			
		}
				
		$retPath[]=$modulo;
		$retPath[]=$datos;
		$retPath[]=$lang;
		return $retPath;			
	  
	}
	// Funcion que compara el array de la url con
	// el array que obtengo de la url. De esa manera
	// sabemos que es una url valida.
	function checkpath($type, $command,$id,$lang){
		global $bd;
		$uri=array();
		switch ($type) {
		    case 'page':		    	
		    	if (!empty($lang)) {//Porque si esta vacio solo hay un idioma en la bd.
		    	// Tengo que comprobar si el idioma corrresponde a la pagina.
		    	$tLang=$bd->get_lang_by_id($id);
		    	if ($lang['id']!=$tLang['id']) return false;
		    	}
		     	$parents=$bd->get_page_childreen($id);
		     	foreach ($parents as $par)
				$uri[]=$bd->get_uri_by_id($par);
				// Tengo que invertir el uri en este punto
				// FIXME: Hecho.
				$uri=array_reverse($uri);				
		     	//el ultimo
		     	$uri[]=$bd->get_uri_by_id($id);		     	
		        break;
		    case 'post':
		    	// Lista de categorias
				$cat=$bd->get_category($id);
				$cat_id=$cat['cat_id'];
		    	if (!empty($lang)) {//Porque si esta vacio solo hay un idioma en la bd.
		    	// Tengo que comprobar si el idioma corrresponde a la categoria.
		    	$tLang=$bd->get_cat_lang_by_id($cat_id);
		    	if ($lang['id']!=$tLang['id']) return false;
		    	}				
				$str=$bd->get_cat_uri_path_by_id($cat_id);
				$parents=explode("/",$str);			
				$uri[]=item_news;
		     	foreach ($parents as $par){
					if (!empty($par))
					$uri[]=$par;
		     	}
		     	// La ultima categoria
		     	$uri[]=$cat['cat_uri'];	
		     	//Añado el post.
		     	$uri[]=$bd->get_uri_by_id($id);				
		        break;
		    case 'category':
		    	if (!empty($lang)) {//Porque si esta vacio solo hay un idioma en la bd.
		    	// Tengo que comprobar si el idioma corrresponde a la categoria.
		    	$tLang=$bd->get_cat_lang_by_id($id);
		    	if ($lang['id']!=$tLang['id']) return false;
		    	}			    	
				$str=$bd->get_cat_uri_path_by_id($id);
				$parents=explode("/",$str);			
				$uri[]=item_news;
		     	foreach ($parents as $par){
					if (!empty($par))
					$uri[]=$par;
		     	}	
		     	$uri[]=$bd->get_cat_uri_by_id($id);
		    	break;
		}	
		//Comparamos los dos arrays.
     	if (comparar_arrays($uri,$command)!==0) return false;
     	else return true;			
	}
	function comparar_arrays($op1, $op2)
	{
	    if (count($op1) < count($op2)) {
	        return -1; // $op1 < $op2
	    } elseif (count($op1) > count($op2)) {
	        return 1; // $op1 > $op2
	    }
	    foreach ($op1 as $clave => $val) {
	        if (!array_key_exists($clave, $op2)) {
	            return null; // incomparable
	        } elseif ($val < $op2[$clave]) {
	            return -1;
	        } elseif ($val > $op2[$clave]) {
	            return 1;
	        }
	    }
	    return 0; // $op1 == $op2
	}

	function getParams()
	{
		$query=explode("&",$_SERVER['QUERY_STRING']);
		$escaped = array();
		foreach ($query as $variable => $value)
		{
			$escaped[]=escape((strip_tags($value)));
		}
		return $escaped;
	}	
	
	function getUri()
	{
		$query=explode("/",$_SERVER['REQUEST_URI']);
		$escaped = array();
		foreach ($query as $value)
		{
			$escaped[]=escape((strip_tags($value)));
		}
		return $escaped;
	}	
		
	function retSelected($opc=0,$modulo,$uri)
	{
	   $str='';
	   if (is_array($modulo))
	   if (in_array($uri,$modulo)) $str.=' id="current"';
	   
	   if ($opc==0) return $str;
	   else echo $str;
	}

	function getlinkpath()
	{
		global $path, $bd, $str_lang;
		
		if (!is_home())
		{
		$url=explode("/",escape($_SERVER['REQUEST_URI']));
		unset ($url[count($url)-1]); // El ultimo y el prmiero fuera.		
		$str='<ul class="hotlink">
			  <li><a href="'.$path.'" title="'.$str_lang['HOME'].'" accesskey="i">'.$str_lang['HOME'].'</a></li>';
		if (!is_error())
		for ($i=1; $i<count($url); $i++)
			{
				switch ($url[$i]){			
				case item_news: // Noticias
					$uri_name=ucfirst(item_news);
					break;
				case item_search: // busqueda
					$uri_name=ucfirst(item_search);
					break;	
				case item_feed: // Feeds
					$uri_name=ucfirst(item_feed);
					break;	
				case item_admin: // Admin
					$uri_name=ucfirst(item_admin);
					break;	
				case item_anuncios: // Anuncios
					$uri_name=ucfirst(item_anuncios);
					break;
				case item_contacto: // Anuncios
					$uri_name=ucfirst(item_contacto);
					break;																											
				default:
					//Filtro si es categoria, pagina o post.
					if (is_category())
					{
						$cat=$bd->get_category_by_uri($url[$i]);
						$uri_name=$cat['title'];
					}
					else
					{ 
						$t_page=$bd->get_all_by_uri($url[$i]);
						if (!empty($t_page)) // Es una página o un post
						$uri_name=$t_page['title'];		
						else // Es un post con categorias.
						{
							$cat=$bd->get_category_by_uri($url[$i]);
							$uri_name=$cat['title'];							
						}
					}
					break;					
				}
				$url_sum.=$url[$i]."/";
				$url_str=$path.$url_sum;
				//ultimo enlace no es valido
				if ($i==count($url)-1)
				$str.='<li>&raquo; '.$uri_name.'</li>';
				else $str.='<li>&raquo; <a href="'.$url_str.'" title="'.$uri_name.'">'.$uri_name.'</a></li>';
			}
		else $str.='<li>&raquo; '.$str_lang['PAG_NO_EXISTE_TITLE'].'</li>';
		$str.='</ul>';
		}
		return $str;		
	}

	function is_home(){
		global $modulo;
		if ($modulo=="home.php") return true;
		else return false;
	}
	function is_search(){
		global $modulo;
		if ($modulo=="search.php") return true;
		else return false;
	}
	function is_category(){
		global $modulo;
		if ($modulo=="category.php") return true;
		else return false;
	}	
	function is_page(){
		global $modulo;
		if ($modulo=="page.php") return true;
		else return false;
	}
	function is_post(){
		global $modulo;
		if ($modulo=="post.php") return true;
		else return false;
	}
	function is_error(){
		global $modulo;
		if ($modulo=="error.php") return true;
		else return false;
	}							
	
	function get_title(){
		global $modulo, $datos, $str_lang;		
		if (is_home()) 
			return $str_lang['LANG_HOME_TITLE'];
		elseif (is_search())
			return "Busqueda";
		elseif (is_page() or is_post()) 
			return $datos['title'].titleWeb;
		elseif (is_category())
			return ucwords(item_news.", ".$datos['title']).titleWeb;
		elseif (is_error()) return $str_lang['PAG_NO_EXISTE_TITLE'].titleWeb; // Administracion.
		//elseif (is_admin()) return strtoupper(item_admin).titleWeb; // Administracion.
		else { //Modulos.
				$requestURI = explode('/',$_SERVER['REQUEST_URI']);
				$command = array_values($requestURI);
				unset($command[count($command)-1]);
				unset($command[0]);			
				$str=implode(", ",$command);	
				$str = str_replace(item_contacto,$str_lang['LANG_CONTACTO'],$str);		
				$str = str_replace(item_inscripcion,$str_lang['LANG_INSCRIPCION'],$str);
				$str = str_replace(item_galeria,$str_lang['LANG_GALERIA'],$str);
				return escape(ucwords($str)).titleWeb;
		}
	}	
	
	function convert_date($date){
		  if($date!="")
		  {
          $t=strtotime($date);
          //$data= date("d/m/Y H:i:s",$t);
		  $data= date("d/m/Y",$t);
		  return $data;
		  }
		  return '';
          
	}
	
	function convert_dateBD($date){
	
		  $v=split('/',$date); 	
		 
		  if (count($v)==3)
		  {
			$data=$v[2]."-".$v[1]."-".$v[0];
		  }else
			 $data='NULL';
          //$t=strtotime($date);
          //$data= date("Y-m-d",$t);
          return $data;
	}
	function convert_time($time){
          // 1899-12-30 23:49:00
		  $t=split(" ",$time);
          $data= $t[1];
          return $data;
	}	
	function exec_time_init(){
		$tiempo = microtime(); 
		$tiempo = explode(" ",$tiempo); 
		$tiempo = $tiempo[1] + $tiempo[0]; 	
		return $tiempo;
	}
	function exec_time_fin($tiempoInicio){
		$tiempo = microtime(); 
		$tiempo = explode(" ",$tiempo); 
		$tiempo = $tiempo[1] + $tiempo[0]; 
		$tiempoFin = $tiempo; 
		$tiempoReal = ($tiempoFin - $tiempoInicio);	
		return $tiempoReal;	
	}
	
	// Destruir session si no esta registrado
	function check_session(){
		global $bd_login;
		session_start();
		session_regenerate_id();
		$finger=session_key;	
		
		if(!isset($_SESSION['ua']))
		{
			if (isset($_COOKIE['sEs']))
			{
					$cookie_value = decryptCookie($_COOKIE['sEs']);		
						
					$sql="SELECT * FROM usuarios where Name=".entrada_sql($cookie_value)."";
					
					$r = $bd_login->bbdd_query($sql);
				
					$cant = $bd_login->bbdd_num($r);
						
					if ($cant==0) //Si no existe el usuario, lo mandamos fuera
					{
						Logout();
						return false;	
					}

					$row=$bd_login->bbdd_fetch($r);
					
					// Creamos sesion
					create_session($row['UserID'],$row['Name'],$row['perfil_id'],$row['parque_id']);
			
					$time = time()+60*60*24*30*12; //store cookie for one year
					setcookie('sEs', encryptCookie($row['Name']),$time,'/');			
			}
			else return false;
		}
		else {
		    if($_SESSION['ua'] != md5($_SERVER['HTTP_USER_AGENT'].$finger))
		    return false;
		}
		return true;
	}
		
	function create_session($id, $user, $type, $parque_id){
		 session_start();
		 $finger=session_key;	
		 $_SESSION['ua'] = md5($_SERVER['HTTP_USER_AGENT'].$finger);
		 $_SESSION['id']=$id;
		 $_SESSION['user']=$user;
		 $_SESSION['type']=$type;
		 $_SESSION['HIDRANTES']['parque_id']=$parque_id;
		 session_regenerate_id();		 
	}
	// Usuarios registrados
	function is_consorcio(){
		return ($_SESSION['type']==administrativo);
	}
	// Usuarios registrados
	function is_oficialjefe(){
		return ($_SESSION['type']==oficial_jefe);
	}
	// Usuarios registrados
	function is_uno_uno_dos(){
		return ($_SESSION['type']==uno_uno_dos);
	}
	// Usuarios registrados
	function is_jefeguardia(){
		if ($_SESSION['type']==jefe_de_guardia) return true;
		else return false;
	}
	// Usuarios registrados
	function is_jefeparque(){
		if ($_SESSION['type']==jefe_de_parque) return true;
		else return false;
	}
    // Usuarios con permisos
	function is_gerente(){
		if ($_SESSION['type']==gerente) 
			return true;
		else return false;
	}
	function is_gestor(){
		if ($_SESSION['type']==gestor) 
			return true;
		else return false;
	}
	// Admin.
	function is_admin(){		
		if ($_SESSION['type']==administrador)      // // ->administradores
			return true;
		else return false;
	}
	function getPrefijoLang()
	{
		$sub="";
		if ($_SESSION["Lang"]=="PT")
			$sub="_pt";
		return $sub;
	}	
	function getPrefijoCal()
	{	
		$cal="es";
		if ($_SESSION["Lang"]=="PT")
			$cal="pt-BR";
		return $cal;
	}
	
	function getPerfil()
	{
		global $bd;
		
		
		$action_edit=$bd->get_all_by_id("perfiles","perfil_id",$_SESSION['type']);
		
		if ($_SESSION["Lang"]=="PT")
			return $action_edit["perfil_pt"];
		else
			return $action_edit["perfil"];
	}
	
	function getPerfilbyUserID($userId)
	{
		global $bd;
		
		
		$action_edit=$bd->get_all_by_id("usuarios","UserID",$userId);
		return $action_edit["perfil_id"];
	
	}
	function encryptCookie($value){
	   if(!$value){return false;}
	   $key = session_key;
	   $crypttext = $value;
	   return trim(base64_encode($crypttext)); //encode for cookie*/
	}
	
	function decryptCookie($value){
	  if(!$value){return false;}
	   $key = session_key;
	   $decrypttext =  base64_decode($value); //decode cookie
	   return trim($decrypttext);
	}		
	
	function validate_user(){
		global $bd_login;
		if (!isset($_COOKIE['sEs']))
		{
			$user=entrada_sql(strip_tags($_POST['user']));
			$sql="SELECT * FROM usuarios where Name=".$user."";
			$r = $bd_login->bbdd_query($sql);
			$cant = $bd_login->bbdd_num($r);
			if ($cant==0) //Si no existe el usuario, lo mandamos fuera
			return false;
			
			$password = md5($_POST['pass']);
			$row=$bd_login->bbdd_fetch($r);
			if ($row['Password']!=$password)
			return false;
			
			// Creamos sesion
			create_session($row['UserID'],$row['Name'],$row['perfil_id'],$row['parque_id']);    
		    
            if ($_POST['remember_me']=="1")
            {
				$time = time()+60*60*24*30*12; //store cookie for one year
				setcookie('sEs', encryptCookie($row['Name']),$time,'/');
            }
		}
		else {
					$cookie_value = decryptCookie($_COOKIE['sEs']);				
					$sql="SELECT * FROM usuarios where Name=".entrada_sql($cookie_value)." and activate=''";
					$r = $bd_login->bbdd_query($sql);
					$cant = $bd_login->bbdd_num($r);
					if ($cant==0) //Si no existe el usuario, lo mandamos fuera
					return false;	

					$row=$bd_login->bbdd_fetch($r);
					
					// Creamos sesion
					create_session($row['UserID'],$row['Name'],$row['perfil_id'],$row['parque_id']); 
					
					$time = time()+60*60*24*30*12; //store cookie for one year
					setcookie('sEs', encryptCookie($row['Name']),$time,'/');									 		
			}
			return true;	
	}
	function Logout(){
	session_start();
	session_destroy();
	$time=time()-60;
	setcookie('sEs', '',$time,'/');		
	}	
	
	// Obtener titulo del permiso.	
	function userTypeTitle($type){
		if ($type==1)
		return "Administrador";
		elseif ($type==2)
		return "Gestor";
		else return "Registrado";
	}
	
	// Mirar si esta activado.
	function is_activate($activado){
		if (empty($activado)) return "Activado";
		else return "No Activado";
	}	
	// Añadir clase de error
	function setClassError($error,$input){
		$str='';
		if (!empty($error[$input]))
		$str ='class="error"';	
		return $str;
	}
	// Añadir clase de error
	function getMessageError($error,$input){
		if (!empty($error[$input]))
		$str =' <span class="error">'.$error[$input].'</span>';	
		else $str='';
		return $str;
	}
	
	/* FUNCIONES DE SANITIZE DE WORDPRESS 
	NOS PERMITE CONVERTIR LOS TITULOS DE LOS POST EN URI*/
	function sanitize_title_with_dashes($title) {
		$title = strip_tags($title);
		// Preserve escaped octets.
		$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
		// Remove percent signs that are not part of an octet.
		$title = str_replace('%', '', $title);
		// Restore octets.
		$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
	
		$title = remove_accents($title);
		if (seems_utf8($title)) {
			if (function_exists('mb_strtolower')) {
				$title = mb_strtolower($title, 'UTF-8');
			}
			$title = utf8_uri_encode($title, 200);
		}
	
		$title = strtolower($title);
		$title = preg_replace('/&.+?;/', '', $title); // kill entities
		$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
		$title = preg_replace('/\s+/', '-', $title);
		$title = preg_replace('|-+|', '-', $title);
		$title = trim($title, '-');
	
		return $title;
	}
	
	function remove_accents($string) {
		if ( !preg_match('/[\x80-\xff]/', $string) )
			return $string;
	
		if (seems_utf8($string)) {
			$chars = array(
			// Decompositions for Latin-1 Supplement
			chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
			chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
			chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
			chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
			chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
			chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
			chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
			chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
			chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
			chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
			chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
			chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
			chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
			chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
			chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
			chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
			chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
			chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
			chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
			chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
			chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
			chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
			chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
			chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
			chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
			chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
			chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
			chr(195).chr(191) => 'y',
			// Decompositions for Latin Extended-A
			chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
			chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
			chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
			chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
			chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
			chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
			chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
			chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
			chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
			chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
			chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
			chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
			chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
			chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
			chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
			chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
			chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
			chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
			chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
			chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
			chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
			chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
			chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
			chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
			chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
			chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
			chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
			chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
			chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
			chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
			chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
			chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
			chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
			chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
			chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
			chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
			chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
			chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
			chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
			chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
			chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
			chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
			chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
			chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
			chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
			chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
			chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
			chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
			chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
			chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
			chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
			chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
			chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
			chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
			chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
			chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
			chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
			chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
			chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
			chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
			chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
			chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
			chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
			chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
			// Euro Sign
			chr(226).chr(130).chr(172) => 'E',
			// GBP (Pound) Sign
			chr(194).chr(163) => '');
	
			$string = strtr($string, $chars);
		} else {
			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
				.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
				.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
				.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
				.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
				.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
				.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
				.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
				.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
				.chr(252).chr(253).chr(255);
	
			$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
	
			$string = strtr($string, $chars['in'], $chars['out']);
			$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
			$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
			$string = str_replace($double_chars['in'], $double_chars['out'], $string);
		}
	
		return $string;
	}
	
	function seems_utf8($Str) { # by bmorel at ssi dot fr
		for ($i=0; $i<strlen($Str); $i++) {
			if (ord($Str[$i]) < 0x80) continue; # 0bbbbbbb
			elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
			elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
			elseif ((ord($Str[$i]) & 0xF8) == 0xF0) $n=3; # 11110bbb
			elseif ((ord($Str[$i]) & 0xFC) == 0xF8) $n=4; # 111110bb
			elseif ((ord($Str[$i]) & 0xFE) == 0xFC) $n=5; # 1111110b
			else return false; # Does not match any model
			for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++$i == strlen($Str)) || ((ord($Str[$i]) & 0xC0) != 0x80))
				return false;
			}
		}
		return true;
	}
	function utf8_uri_encode( $utf8_string, $length = 0 ) {
		$unicode = '';
		$values = array();
		$num_octets = 1;
	
		for ($i = 0; $i < strlen( $utf8_string ); $i++ ) {
	
			$value = ord( $utf8_string[ $i ] );
	
			if ( $value < 128 ) {
				if ( $length && ( strlen($unicode) + 1 > $length ) )
					break;
				$unicode .= chr($value);
			} else {
				if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;
	
				$values[] = $value;
	
				if ( $length && ( (strlen($unicode) + ($num_octets * 3)) > $length ) )
					break;
				if ( count( $values ) == $num_octets ) {
					if ($num_octets == 3) {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
					} else {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
					}
	
					$values = array();
					$num_octets = 1;
				}
			}
		}
	
		return $unicode;
	}
	function getInputSelValue($str,$strcomp,$out)
	{
	   if ($str===$strcomp) $ret=$out;
	   return $ret;
	}	
	function get_metakeywords(){
		global $datos;
		$title=$datos['title']	;
		$title=explode(" ", $title);
		$str=autor;
		foreach ($title as $tit)
		if (strlen($tit)>=3) $str.=", ".$tit;	
		return $str;	
	}
	function get_metadescription(){
		global $datos;
		$descripcion = strip_tags($datos['content']);
		$descripcion=substr($descripcion,0,200)."...";			
		if (is_home())
		$descripcion = 'programación web Diseño de páginas web en Tenerife y de Las Palmas de Gran Canaria y Canarias,  Servicios de Internet, Diseño de Páginas Web para sitios webs corporativos y portales empresariales, Registro de dominios, Alta y posicionamiento en buscadores, Desarrollo de CD-Rom Multimedia, Comercio Electrónico, Tienda ON-line, Programación de Aplicaciones Web y Multimedia.';
		
		return $descripcion;		
	}
	
	// Funcion para obtener la operacion en los modulos. y otros datos.
	function modUrlRewrite(){
		$retPath=array();
		$requestURI = explode('/',$_SERVER['REQUEST_URI']);
		$command = array_values($requestURI);
		return $command[3]; // Devuelve la acción.
		
	}
	
	// Funcion para el envio de emails
	function sendMail($from, $from_name, $to, $subject, $message, $attachment, $type,$smtp_user,$smtp_pass,$smtp_host)
	{
		include_once(serverpath."includes/functions/phpmailer/class.phpmailer.php");
		include_once(serverpath."includes/functions/phpmailer/class.smtp.php");
		$mail = new PHPMailer();	
		$mail->PluginDir = serverpath.'includes/functions/phpmailer/';
		$mail->SetLanguage( 'es', ''.serverpath.'includes/functions/phpmailer/language/' );
		$mail->CharSet 	= "UTF-8";
		$mail->From 	= $from;
		$mail->FromName = $from_name;
		$mail->AddAddress($to);
		$mail->Subject 	= $subject;
		$mail->Body = $message;		
		$mail -> IsHTML (true);		
		
		if (!empty($attachment))
		{
			foreach ($attachment as $value)
			$mail->AddAttachment($value);
		}
			
		if ($type==0) //SMTP
		{
		$mail->IsSMTP();
		$mail->Mailer="smtp";
		$mail->SMTPAuth = "1";
		$mail->Username = $smtp_user;
		$mail->Password = $smtp_pass;
		$mail->Host 	= $smtp_host;	
		}
		elseif ($type==1) //Funcion mail
		{
			$mail->IsMail();
			$mail->Mailer="mail";
			
		}		
		if ($mail->Send()) return true;
		else {
			echo $mail->ErrorInfo;
			return false;			
		}
	
	}
	
	// Para filtrar la clase de body y la clase de page-content (css)	
	function get_content_filter(){
		global $a_pages, $str_lang;
		if (is_page())
		{	
			//Obtnemos el uri del padre.
			$q_s=explode("/",$_SERVER['REQUEST_URI']);
			$p_uri=escape($q_s[1]);	
			
			if ($q_s[1]==$a_pages[$str_lang['LANG_PAGE_LINK_ABOUT_US']]['uri'])
			$p_uri=' about-us';
			elseif ($q_s[1]==$a_pages[$str_lang['LANG_PAGE_LINK_CONSUMER']]['uri'])	
			$p_uri=' patients '.escape($q_s[2]);
			elseif (!empty($q_s[2])) $p_uri.=" ".escape($q_s[2]);	
		}
		
		return $p_uri;
	}
	
	// Funcion para obtener el id de la página.
	function get_body_css_id(){
		global $a_uri, $datos, $retPath, $a_pages, $str_lang;
		$modulo=$retPath[1];	

		if (is_home()) $str=' id="home"';
		elseif (is_page())
		{			
			if ($datos['uri']==$a_pages[$str_lang['LANG_PAGE_LINK_PROGRAMA']]['uri'])
			$str=' id="programa"';
			elseif ($datos['uri']==$a_pages[$str_lang['LANG_PAGE_LINK_HISTORIA']]['uri'])
			$str= 'id="historia"';
			elseif ($datos['uri']==$a_pages[$str_lang['LANG_PAGE_LINK_UBICACION']]['uri'])
			$str= 'id="ubicacion"';							
			elseif ($datos['uri']==$a_pages[$str_lang['LANG_PAGE_LINK_RUTAS']]['uri'])
			$str= 'id="rutas"';
			elseif ($datos['uri']==$a_pages[$str_lang['LANG_PAGE_LINK_PATROCINADORES']]['uri'])
			$str= 'id="patrocinadores"';	
			elseif ($datos['uri']==$a_pages[$str_lang['LANG_PAGE_LINK_ENLACES']]['uri'])
			$str= 'id="enlaces"';								
		}
		elseif (is_search()) $str=' id="search"';
		elseif ($modulo==item_contacto) $str=' id="contacto"';
		elseif ($modulo==item_galeria) $str=' id="galeria"';
		elseif ($modulo==item_inscripcion) $str=' id="inscripcion"';
		return $str;
	}
	
		// Obtener un valor determinado de una tabla.
	function get_table_value($table, $value, $campo, $id){
		global $bd;
		$sql="SELECT ".$value." FROM ".$table." where ".$campo."=".entrada_sql($id)."";
		$r = $bd->bbdd_query($sql);
		$row=$bd->bbdd_fetch($r);
		return $row[$value];	
	}

	// Obtener todos los valores de una determinada tabla
	function get_table_all_value($table, $campo, $id){
		global $bd;
		$sql="SELECT * FROM ".$table." where ".$campo."=".entrada_sql($id)."";
		$r = $bd->bbdd_query($sql);
		while ($row=$bd->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}	
	
		// Funcion que me devuelve el texto entre dos textos.
	function get_string_between($string, $start, $end){
	        $string = " ".$string;
	        $ini = strpos($string,$start);
	        if ($ini == 0) return "";
	        $ini += strlen($start);
	        $len = strpos($string,$end,$ini) - $ini;
	        return substr($string,$ini,$len);
	}
	
	// Funcion que me devuelve el mes dado el numero
	function retMonth($num){
		switch ($num) {
			case 1: return "January";	break;
			case 2: return "February";	break;
			case 3: return "March";	break;
			case 4: return "April";	break;
			case 5: return "May";	break;
			case 6: return "June";	break;
			case 7: return "July";	break;
			case 8: return "August";	break;
			case 9: return "September";	break;
			case 10: return "October";break;
			case 11: return "November";break;
			case 12: return "December";	break;	
			default:								
			return false;break;
		}
	}

	// Funcion para leler idiomas
	function readLang($lang)
	{
		//echo "la:".$lang;
		$str_lang=array();
		if (!empty($lang))
		{
			
			if ($lang=="www") $lang="ES";

			if (file_exists(serverpath.'includes/languages/lang_'.$lang.'.csv'))
			$fp = fopen ( serverpath.'includes/languages/lang_'.$lang.'.csv' ,'r' ); 
			else {
					if (file_exists(serverpath.'includes/languages/lang_ES.csv'))
					$fp = fopen ( serverpath.'includes/languages/lang_ES.csv' ,'r' ); 
					else die("Error al leer el fichero de idioma por defecto");
				}
		}
		else {
			if (file_exists(serverpath.'includes/languages/lang_ES.csv'))
			$fp = fopen ( serverpath.'includes/languages/lang_ES.csv' ,'r' ); 
			else die("Error al leer el fichero de idioma por defecto");
		}
		while (( $data = fgetcsv ( $fp , 1000 , "," )) !== FALSE ) { // Mientras hay líneas que leer... 
		
		   $num = count($data);
		   $str="";
			   for ($c=1; $c < $num; $c++) {
			       	$str.=$data[$c];
			       	if ($c+1!=$num) $str.=", ";
			   }
			if (!empty($data[0]) and (!empty($data[1])))
			$str_lang[trim($data[0])]=utf8_encode(trim($str));			   			
		
		} 
		fclose ( $fp ); 
		
		return $str_lang;
	}
	
	function csv_string_to_array($str){

  	$expr="/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/";

    $results=preg_split($expr,trim($str));

    return preg_replace("/^\"(.*)\"$/","$1",$results);

}	
	
	// Graba en fichero.
	function save_file($archivo, $texto)
	{
		#Abrimos el fichero en modo de escritura 
		$DescriptorFichero = fopen($archivo,"w+"); 
		
		#Escribimos 
		fputs($DescriptorFichero,$texto); 	
		
		#Cerramos el fichero 
		fclose($DescriptorFichero);

		return true;
	}
	
	// Descargar fichero
	function dl_file($file){
		//global $path;
	    //First, see if the file exists
	    if (!file_exists($file)) { die("<b>404 File not found!</b>"); }

	    //Gather relevent info about file
	    $len = filesize($file);
	    $filename = basename($file);
	    $file_extension = strtolower(substr(strrchr($filename,"."),1));

	    //This will set the Content-Type to the appropriate setting for the file
	    switch( $file_extension ) {
	          case "pdf": $ctype="application/pdf"; break;
	      case "exe": $ctype="application/octet-stream"; break;
	      case "zip": $ctype="application/zip"; break;
	      case "doc": $ctype="application/msword"; break;
	      case "xls": $ctype="application/vnd.ms-excel"; break;
	      case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
	      case "gif": $ctype="image/gif"; break;
	      case "png": $ctype="image/png"; break;
	      case "jpeg":
	      case "jpg": $ctype="image/jpg"; break;
	      case "mp3": $ctype="audio/mpeg"; break;
	      case "wav": $ctype="audio/x-wav"; break;
	      case "mpeg":
	      case "mpg":
	      case "mpe": $ctype="video/mpeg"; break;
	      case "mov": $ctype="video/quicktime"; break;
	      case "avi": $ctype="video/x-msvideo"; break;

	      //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
	      case "php":
	      case "htm":
	      case "html":
	      die("<b>Cannot be used for ". $file_extension ." files!</b>"); break;

	      default: $ctype="application/force-download";
	    }

	    //Begin writing headers
	    header("Pragma: public");
	    header("Expires: 0");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header("Cache-Control: public");
	    header("Content-Description: File Transfer");

	    //Use the switch-generated Content-Type
	    header("Content-Type: $ctype");

	    //Force the download
	   // $header="Content-Disposition: attachment; filename=".$filename.";";
	    //header($header );
	   // header("Content-Transfer-Encoding: binary");
	    header("Content-Length: ".$len);
	    @readfile($file);
}	
		// Devuelve el array con el titulo y el texto de la pagina
		// funcion auxiliar para el estilo
		function getPagesTranlate()
		{
			global $str_lang, $bd;
			
			$page=array();
			$tPost=$bd->getpage(0,1000000);
			if (!empty($tPost))
			foreach ($tPost as $post)
			{
				$page[$post['id']]['title']=$post['title'];
				$page[$post['id']]['uri']=$post['uri'];
			};
			return $page;			
		}
		
	function convertirCoordenadaUTM($long,$lat,$uso)
	{
	
		
		require_once ('./includes/classes/gpoint/gPoint.php');
		
		
		//
		// The example shows how a single point on the earth can be converted between
		// Latitude/Longitude coordinates and the three map projections supported by
		// the gPoint class.
		//
			$myHome =& new gPoint();	// Create an empty point
			
		//
		//  We start by setting the points Longitude & Latitude. 
		//
		//	$myHome->setLongLat(-121.85831, 37.42104);	// I live in sunny California :-)
		//	echo "I live at: "; $myHome->printLatLong(); echo "<br>";
		//
		// Calculate the coordinates of the point in a UTM projection 
		//
	//		$myHome->convertLLtoTM();
	//		echo "Which in a UTM projection is: "; $myHome->printUTM(); echo "<br>";
		//
		// Set the UTM coordinates of the point to check the reverse conversion
		//
			$myHome->setUTM( $long, $lat, $uso."N");	// Easting/Northing from a GPS
			
			//echo "My GPS says it is this: "; $myHome->printUTM(); echo "<br>";
		//
		// Calculate the Longitude Latitude of the point
		//
			$myHome->convertTMtoLL();
		
			//$x= convertirCoordenada($myHome->Xp(),0);
			
		//	echo "ss".$myHome->Long();
			//return $myHome->printLatLong(); 
			//$x= convertirCoordenada($myHome->Lat(),0);
			//$y= convertirCoordenada($myHome->Long(),1);
			return $myHome;//$myHome->printLatLong(); 
		//	echo "Which converts back to: "; $myHome->printLatLong(); echo "<br>";
	}
	
	
	function convertirCoordenadaGEO($n,$w)
	{
	
		
		require_once ('./includes/classes/gpoint/gPoint.php');
		
		
		//
		// The example shows how a single point on the earth can be converted between
		// Latitude/Longitude coordinates and the three map projections supported by
		// the gPoint class.
		//
			$myHome =& new gPoint('WGS 84');	// Create an empty point
			
		//
		//  We start by setting the points Longitude & Latitude. 
		//
		//	$myHome->setLongLat(-121.85831, 37.42104);	// I live in sunny California :-)
		//	echo "I live at: "; $myHome->printLatLong(); echo "<br>";
		//
		// Calculate the coordinates of the point in a UTM projection 
		//
	//		$myHome->convertLLtoTM();
	//		echo "Which in a UTM projection is: "; $myHome->printUTM(); echo "<br>";
		//
		// Set the UTM coordinates of the point to check the reverse conversion
		//
			$myHome->setLongLat($w, $n);
			
			//echo "My GPS says it is this: "; $myHome->printUTM(); echo "<br>";
		//
		// Calculate the Longitude Latitude of the point
		//
			$myHome->convertLLtoTM();
		
			//$x= convertirCoordenada($myHome->Xp(),0);
		//	echo "ss".$myHome->Lat();
		//	echo "ss".$myHome->Long();
			//return $myHome->printLatLong(); 
			//$x= convertirCoordenada($myHome->Lat(),0);
			//$y= convertirCoordenada($myHome->Long(),1);
			return $myHome;//$myHome->printLatLong(); 
		//	echo "Which converts back to: "; $myHome->printLatLong(); echo "<br>";
	}
	
	function autf8($v)
	{
		$r=array();
		foreach($v as $clave=>$valor)
			$r[$clave]=utf8_decode($valor);
		return $r;	
	}	
	
	function cargarCombo($id,$v,$campo_id,$campo)
	{
		$txt="";
		$txt.='<option value="-1" selected><--Seleccione opción--></option>';
		foreach($v as $key=>$valor)
		{
			if ($id==$valor[$campo_id])
				$txt.='<option value="'.$valor[$campo_id].'" selected>'.utf8_encode($valor[$campo]).'</option>';
			else
				$txt.='<option value="'.$valor[$campo_id].'">'.utf8_encode($valor[$campo]).'</option>';
			
		}
		return $txt;
	}	
	

	function cambiartam($nombre,$archivo,$ancho,$alto)
	{
		
		$tmp=strtolower(substr($nombre, -3));
		
		if ($tmp=="jpg")
		{
			$imagen=imagecreatefromjpeg($nombre);
		}
		if ($tmp=="png")
		{
			$imagen=imagecreatefrompng($nombre);
		}
		if ($tmp=="gif")
		{
			$imagen=imagecreatefromgif($nombre);
		}
	

		$x=imagesx($imagen);
		$y=imagesy($imagen);

		if ($x > $y) 
		{
			$w=$ancho;
			$h=$y*($alto/$x);
		}

		if ($x < $y) 
		{
			$w=$x*($ancho/$y);
			$h=$alto;
		}

		if ($x == $y) 
		{
			$w=$ancho;
			$h=$alto;
		}

		$destino=imagecreatetruecolor($w,$h);
		imagecopyresampled($destino,$imagen,0,0,0,0,$w,$h,$x,$y); 
		if ($tmp=="jpg")
		{
				imagejpeg($destino,$archivo); 
		}
		if ($tmp=="png")
		{
				imagepng($destino,$archivo); 
		}
		if ($tmp=="gif")
		{
				imagegif($destino,$archivo);
		}

		
		imagedestroy($destino); 
		imagedestroy($imagen); 
		
	}
function cambiartammitad($nombre,$archivo)
	{
		
		$tmp=strtolower(substr($nombre, -3));
		if ($tmp=="jpg")
		{
			$imagen=imagecreatefromjpeg($nombre);
		}
		if ($tmp=="png")
		{
			$imagen=imagecreatefrompng($nombre);
		}
		if ($tmp=="gif")
		{
			$imagen=imagecreatefromgif($nombre);
		}
	

		$x=imagesx($imagen);
		$y=imagesy($imagen);

		$w=intval($x/2);
		$h=intval($y/2);

		$destino=imagecreatetruecolor($w,$h);
		imagecopyresampled($destino,$imagen,0,0,0,0,$w,$h,$x,$y); 
		if ($tmp=="jpg")
		{
				imagejpeg($destino,$archivo); 
		}
		if ($tmp=="png")
		{
				imagepng($destino,$archivo); 
		}
		if ($tmp=="gif")
		{
				imagegif($destino,$archivo);
		}
		imagedestroy($destino); 
		imagedestroy($imagen); 
		
	}
	
	function uploadimages($filename)
{

		$path_info = pathinfo($filename['name']);
		$extension = $path_info['extension'];

		
		//datos del arhivo
		$archivo="foto_".$filename["id"]."_".$filename["orden"].".".$extension;
		$nombre_archivo = uploadpathadmin."new/new_".$archivo; // $HTTP_POST_FILES['userfile']['name'];
		$nombre_archivo_mini = uploadpathadmin."mini/mini_".$archivo;
		$tipo_archivo = $filename['type'];
		$tamano_archivo = $filename['size'];
		$error="";
		if ($tamano_archivo >=1048576)
		{
			$error ="El archivo es mayor de 1M";
		}else
			{
			//compruebo si las características del archivo son las que deseo
			if (!((strpos($tipo_archivo, "gif") || strpos($tipo_archivo, "jpeg")) )) {
					  $error = "La extensión o el tamaño de los archivos no es correcta.";
			}else{
				if (move_uploaded_file($filename['tmp_name'], $nombre_archivo)){
				 
				
				}else{
				  $error ="Ocurrió algún error al subir el fichero. No pudo guardarse.";
				}
			}
		}
			
			cambiartam($nombre_archivo,$nombre_archivo_mini,200,200);
			
		return array($archivo,$error);

}

function uploadfiles($filename)
{

		$path_info = pathinfo($filename['name']);
		$extension = $path_info['extension'];

		
		//datos del arhivo
		$archivo="file_".$filename["id"]."_".$filename["orden"].".".$extension;
		$nombre_archivo = uploadpathadmin."files/".$archivo; // $HTTP_POST_FILES['userfile']['name'];
		
		$tipo_archivo = $filename['type'];
		$tamano_archivo = $filename['size'];
		$error="";
		if ($tamano_archivo >=1048576)
		{
			$error ="El archivo es mayor de 1M";
		}else
			{
			
			if (move_uploaded_file($filename['tmp_name'], $nombre_archivo)){
			 
			
			}else{
			  $error ="Ocurrió algún error al subir el fichero. No pudo guardarse.";
			}
			
		}
					
			
		return array($archivo,$error);

}

function comprimir($archivo)
{
	require_once (serverpath.'includes/classes/zipfile.php' );

$zipfile = new zipfile();
$zipfile->add_file(implode("",file($archivo)), $archivo);


 $file=uploadpathadmin."map.kmz";
 
$f = fopen($file,'w+');
fwrite($f,$zipfile->file(),strlen($zipfile->file()));
fclose($f); 

}
/*
function generar_carta($id,$carta_id,$opcion)
{
	global $bd;
	global $str_lang;
	$sub=getPrefijoLang();
	
	$action_edit=$bd->get_all_by_id("V_hidrantes","hidrante_id",$id);
	$datos= $action_edit;
	
	if (($datos['utmx']==0)|| ($datos['utmy']==0))
	{				
												
		if (($datos['geon']!=0)&&($datos['geow']!=0))
		{
			$objUTM = convertirCoordenadaGEO($datos['geon'],$datos['geow']);
			$datos['utmx']=number_format($objUTM->E(),2 ,'.', '');
			$datos['utmy']=number_format($objUTM->N(),2, '.', '');
			$datos['uso'] =substr($objUTM->Z(),0,2);
		}
	}
	
	include(serverpath.'includes/classes/pdf/class.ezpdf.php' );
	$pdf = new Cezpdf();
	$diff=array(
196=>'Adieresis',
228=>'adieresis',
214=>'Odieresis',
246=>'odieresis',
220=>'Udieresis',
252=>'udieresis',
223=>'germandbls',
224=>'agrave',
225=>'aacute',
232=>'egrave',
233=>'eacute',
236=>'igrave',
237=>'iacute',
242=>'ograve',
243=>'oacute',
249=>'ugrave',
250=>'uacute',
200=>'Egrave',
241=>'ntilde'
);
	$pdf->ezSetMargins(0,10,30,40);
	$pdf->selectFont(serverpath.'includes/classes/pdf/fonts/Times-Roman.afm',array('encoding'=>'WinAnsiEncoding','differences'=>$diff));
	//$pdf->selectFont(serverpath.'includes/classes/pdf/fonts/Times-Roman.afm');

	//$pdf->selectFont('./includes/classes/pdf/fonts/Courier.afm');
	$datacreator = array (
						'Title'=>'Ficha de Hidrante',
						'Author'=>'bomberostenerife',
						'Subject'=>'PDF con Tablas',
						'Creator'=>'bomberostenerife',
						'Producer'=>'Ficha_hidrante'
						);
	$pdf->addInfo($datacreator);

	//ezImage(image,[padding],[width],[resize],[justification],[array border])
	$pdf->ezImage(imgadminpath."logo.jpg",30,0,'none','left');
	$pdf->ezSetDy(30);
		
	//$pdf->ezText("\n\n\n",10);
	//Datos de la empresa suminstradora
	$datos_municipio=$bd->get_all_by_id("municipios","municipio_id",$datos["municipio_id"]);
	$lista_comentarios=$bd->getlisttable("V_CartasComentarios","carta_id=".$carta_id,"fecha desc","","");

	
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_CARTAS_NCARTAS'].":</b> ".$lista_comentarios[0]["ncarta"]);
	$destinatario[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_COMENTARIOS_FECHA'].":</b> ".date("d/m/Y"));
	$destinatario[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_MUNICIPIOS_COL_EMPRESA2'].":</b> ".$datos_municipio["empresa"]);
	$destinatario[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_MUNICIPIOS_COL_DIRECCION'].": </b> ".$datos_municipio["direccion"]);
	$destinatario[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>CP: </b> ".$datos_municipio["codigo"] . " <b>".	$str_lang['LANG_MUNICIPIOS_COL_POBLACION'].": </b> ".$datos_municipio["poblacion"]);
	$destinatario[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO'].": </b> ".$datos_municipio["municipio"]);
	$destinatario[] = array('col1'=>$p);

	$titles = array('col1'=>'<b>Numero</b>');
	
	$options =array('showHeadings'=>0,'shaded'=>0,'showLines'=>0,'xPos'=>350,'xOrientation'=>'right','width'=>300,'fontSize' => 11);
	$pdf->ezTable($destinatario,$titles,'',$options );

	$optionsText=array('left' => 30);	
	
	$pdf->ezText("\n\n\n\n",11);
	
	$txt=$str_lang['LANG_CARTAS_TEXTO3'];//"Por la presente se le comunica que el hidrante situado en su municipio, identificado como:\n";
	$pdf->ezText($txt,11,$optionsText);

//	$pdf->ezText("<b>Datos Ficha Hidrante</b>\n",16,$optionsText);
	
	
	$data[] = array('col1'=>"<b>".$str_lang['LANG_CARTAS_HIDRANTE'].":</b> ".$datos["codigo"]);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".$str_lang['LANG_HIDRANTES_CALLE']."/".$str_lang['LANG_HIDRANTES_EDIFICIO'].":</b> ".$datos["calle"]." / ".$datos["edificio"]);
	$data[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO'].": </b> ".$datos["municipio"]);
	$data[] = array('col1'=>$p);
	
	/*$data[] = array('col1'=>"<b>".$str_lang['LANG_HIDRANTES_UTM1'].":</b> X: ".$datos["utmx"]."  Y: ".$datos["utmy"]."  HUSO: ".$datos["uso"] );
	if ($datos['utmx']!=0 && $datos['utmy']!=0)
	{
		$objUTM = convertirCoordenadaUTM($datos['utmx'],$datos['utmy'],$datos['uso']);								
		if ((strlen($datos['geon'])==0)||(strlen($datos['geow'])==0))
		{
			$datos['geon']=number_format($objUTM->Lat(),5);
			$datos['geow']=number_format($objUTM->Long(),5);
		}
	}
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".$str_lang['LANG_HIDRANTES_GEO1'].": </b> N: ".$datos["geon"]."  W: ".$datos["geow"]);
	$data[] = array('col1'=>$p);
*/

/*
	$titles = array('col1'=>'<b>Numero</b>');
	
	$options =array('showHeadings'=>0,'shaded'=>0,'showLines'=>0,'xPos'=>250,'xOrientation'=>'center','width'=>300,'fontSize' => 11);
	$pdf->ezTable($data,$titles,'',$options );
	$pdf->ezText("\n\n",11);
	
	$txt=$str_lang['LANG_CARTAS_TEXTO4'];//"presenta las siguientes incidencias detectadas por nuestros técnicos en la revisión realizada recientemente:\n";
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',$txt);
	$pdf->ezText($p."\n",11,$optionsText);
	
	$pdf->ezText("\n\n",11);
	//$pdf->ezStream();
	$m=count($lista_comentarios);
	$optionsText=array('left' => 100);
	
	
	for ($j=0;$j<$m;$j++)
	{
		
		$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".$str_lang['LANG_HIDRANTES_CARTAS_NUM'].": </b>".($j+1));
		$pdf->ezText($p,11,$optionsText);
		$pdf->ezText("<b>".$str_lang['LANG_COMENTARIOS_FECHA'].": </b>".convert_date($lista_comentarios[$j]["fecha"])."",11,$optionsText);
		$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".$str_lang['LANG_CARTAS_COMENTARIO'].": </b>".$lista_comentarios[$j]["comentario"]."\n");
		$pdf->ezText($p."\n\n",11,$optionsText);
	}
	$optionsText=array('left' => 30);
	
	$txt=$str_lang['LANG_CARTAS_TEXTO5']."\n".$str_lang['LANG_CARTAS_TEXTO6']."\n".$str_lang['LANG_CARTAS_TEXTO7']."\n\n				
					????????????";//"por lo que le solicitamos tomen las medidas oportunas para corregir esta situación en aras de tener el hidrante antes indicado en correcta situación de operatividad para ser usado por nuestro personal cuando sea necesario.
//Para cualquier aclaración puede contactar con este Consorcio en el teléfono 922-533-487.
//Atentamente
                                                    
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',$txt);
	$pdf->ezText($p,11,$optionsText);
	
	if ($opcion==1)
	{
	$pdfcode = $pdf->output();
	$dir = uploadpathadmin.'cartas/';

	$fname = $dir.'carta_'.$id.'_'.$carta_id.'.pdf';
	$fp = fopen($fname,'w');
	fwrite($fp,$pdfcode);
	fclose($fp);
	return 'carta_'.$id.'_'.$carta_id.'.pdf';
	}else
	{
	$pdf->ezStream();
	}
	//$pdf->ezStream();
}
*/
function generar_carta($hidrante_id,$comentario_id,$carta_id,$opcion)
{
	global $bd;
	global $str_lang;
	$sub=getPrefijoLang();
	
	$action_edit=$bd->get_all_by_id("V_hidrantes","hidrante_id",$hidrante_id);
	$datos= $action_edit;
	
	if (($datos['utmx']==0)|| ($datos['utmy']==0))
	{				
												
		if (($datos['geon']!=0)&&($datos['geow']!=0))
		{
			$objUTM = convertirCoordenadaGEO($datos['geon'],$datos['geow']);
			$datos['utmx']=number_format($objUTM->E(),2 ,'.', '');
			$datos['utmy']=number_format($objUTM->N(),2, '.', '');
			$datos['uso'] =substr($objUTM->Z(),0,2);
		}
	}
	
	include(serverpath.'includes/classes/pdf/class.ezpdf.php' );
	$pdf = new Cezpdf();
	$diff=array(
196=>'Adieresis',
228=>'adieresis',
214=>'Odieresis',
246=>'odieresis',
220=>'Udieresis',
252=>'udieresis',
223=>'germandbls',
224=>'agrave',
225=>'aacute',
232=>'egrave',
233=>'eacute',
236=>'igrave',
237=>'iacute',
242=>'ograve',
243=>'oacute',
249=>'ugrave',
250=>'uacute',
200=>'Egrave',
241=>'ntilde'
);
	$pdf->ezSetMargins(0,10,30,40);
	$pdf->selectFont(serverpath.'includes/classes/pdf/fonts/Times-Roman.afm',array('encoding'=>'WinAnsiEncoding','differences'=>$diff));
	//$pdf->selectFont(serverpath.'includes/classes/pdf/fonts/Times-Roman.afm');

	//$pdf->selectFont('./includes/classes/pdf/fonts/Courier.afm');
	$datacreator = array (
						'Title'=>'Ficha de Hidrante',
						'Author'=>'bomberostenerife',
						'Subject'=>'PDF con Tablas',
						'Creator'=>'bomberostenerife',
						'Producer'=>'Ficha_hidrante'
						);
	$pdf->addInfo($datacreator);

	//ezImage(image,[padding],[width],[resize],[justification],[array border])
	$pdf->ezImage(imgadminpath."logo.jpg",30,0,'none','left');
	$pdf->ezSetDy(30);
		
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"".$str_lang['LANG_CARTAS_CONSORCIO']."\r\n".$str_lang['LANG_CARTAS_DIR']."\r\n".$str_lang["LANG_CARTAS_CP_LOCALIDAD"]."\r\n".$str_lang["LANG_CARTAS_ZONA"]."\r\n".$str_lang["LANG_CARTAS_TELEFONO"]."\r\n".$str_lang["LANG_CARTAS_FAX"]);
	$pdf->ezText("\n",10);
	$optionsText=array('left' => -370,'justification'=>'center');
	$pdf->ezText($p,7,$optionsText);	
		
	//$pdf->ezText("\n\n\n",10);
	//Datos de la empresa suminstradora
	$datos_municipio=$bd->get_all_by_id("municipios","municipio_id",$datos["municipio_id"]);
	//$lista_comentarios=$bd->getlisttable("V_CartasComentarios","carta_id=".$carta_id,"fecha desc","","");
	if ($carta_id<=0){
		$ncartas=$bd->getcounttable("cartas","hidrante_id=".$hidrante_id);
		$ncartas=(int)$ncartas+1;
	}else{
		$datos_carta=$bd->get_all_by_id("cartas","carta_id=".$carta_id);
		$ncartas=$datos_carta["interno"];
	}
	
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$datos_municipio['ayuntamiento']."</b>");
	$destinatario[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$datos_municipio["direccion"]."</b> ");
	$destinatario[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".$datos_municipio["codigo"] . " - ".$datos_municipio["poblacion"]."</b> ");
	$destinatario[] = array('col1'=>$p);
/*	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_MUNICIPIOS_COL_EMPRESA2'].":</b> ".$datos_municipio["empresa"]);
	$destinatario[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_MUNICIPIOS_COL_DIRECCION'].": </b> ".$datos_municipio["direccion"]);
	$destinatario[] = array('col1'=>$p);*/
	
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$datos_municipio["municipio"]."</b> ");
	$destinatario[] = array('col1'=>$p);

	$titles = array('col1'=>'<b>Numero</b>');
	
	$options =array('showHeadings'=>0,'shaded'=>0,'showLines'=>0,'xPos'=>350,'xOrientation'=>'right','width'=>300,'fontSize' => 11);
	$pdf->ezTable($destinatario,$titles,'',$options );
	
	$optionsText=array('left' => 275);	
	$pdf->ezText("\n\n",11);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".$str_lang['LANG_CARTAS_NCARTAS'] . ":</b> ".$ncartas);	
	$pdf->ezText($p,11,$optionsText);
//	set_locale(LC_ALL,"es_ES@euro","es_ES","esp");
	$fecha= strftime("%A %d de %B del %Y");
	$p= $str_lang['LANG_CARTAS_EN']." ".$fecha;	
	$pdf->ezText($p,11,$optionsText);
	
	
	$pdf->ezText("\n\n",11);
	$optionsText=array('left' => 30);	
	


	
	$txt=$str_lang['LANG_CARTAS_TEXTO3'];//"Por la presente se le comunica que el hidrante situado en su municipio, identificado como:\n";
	$pdf->ezText($txt,11,$optionsText);
	$pdf->ezText("\n",11);
//	$pdf->ezText("<b>Datos Ficha Hidrante</b>\n",16,$optionsText);
	
	
	$data[] = array('col1'=>"<b>".$str_lang['LANG_CARTAS_HIDRANTE'].":</b> ".$datos["codigo"]);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".$str_lang['LANG_HIDRANTES_CALLE']."/".$str_lang['LANG_HIDRANTES_EDIFICIO'].":</b> ".$datos["calle"]." / ".$datos["edificio"]);
	$data[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO'].": </b> ".$datos["municipio"]);
	$data[] = array('col1'=>$p);
	
	/*$data[] = array('col1'=>"<b>".$str_lang['LANG_HIDRANTES_UTM1'].":</b> X: ".$datos["utmx"]."  Y: ".$datos["utmy"]."  HUSO: ".$datos["uso"] );
	if ($datos['utmx']!=0 && $datos['utmy']!=0)
	{
		$objUTM = convertirCoordenadaUTM($datos['utmx'],$datos['utmy'],$datos['uso']);								
		if ((strlen($datos['geon'])==0)||(strlen($datos['geow'])==0))
		{
			$datos['geon']=number_format($objUTM->Lat(),5);
			$datos['geow']=number_format($objUTM->Long(),5);
		}
	}
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".$str_lang['LANG_HIDRANTES_GEO1'].": </b> N: ".$datos["geon"]."  W: ".$datos["geow"]);
	$data[] = array('col1'=>$p);
*/


	$titles = array('col1'=>'<b>Numero</b>');
	
	$options =array('showHeadings'=>0,'shaded'=>0,'showLines'=>0,'xPos'=>250,'xOrientation'=>'center','width'=>300,'fontSize' => 11);
	$pdf->ezTable($data,$titles,'',$options );
	$pdf->ezText("\n\n",11);
	
	$txt=$str_lang['LANG_CARTAS_TEXTO4'];//"presenta las siguientes incidencias detectadas por nuestros técnicos en la revisión realizada recientemente:\n";
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',$txt);
	$pdf->ezText($p."\n\n",11,$optionsText);
	
	//$pdf->ezText("\n\n",11);
	//$pdf->ezStream();
	//$m=count($vComentarios);
	$optionsText=array('left' => 70);
	
	
	//for ($j=0;$j<$m;$j++)
	//{
	//	$comentario_id= $vComentarios[$j];
		$datos_comentario=$bd->get_all_by_id("comentarios","comentario_id",$comentario_id);
		//$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".$str_lang['LANG_HIDRANTES_CARTAS_NUM'].": </b>".$ncartas);
		//$pdf->ezText($p,11,$optionsText);
		//$pdf->ezText("<b>".$str_lang['LANG_COMENTARIOS_FECHA'].": </b>".convert_date($datos_comentario["fecha"])."",11,$optionsText);
		$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',$datos_comentario["comentario"]."\n\n");
		$pdf->ezText($p,11,$optionsText);
	//}
	$optionsText=array('left' => 30,'justification'=>'full');
	
	$txt=$str_lang['LANG_CARTAS_TEXTO5']." ".$str_lang['LANG_CARTAS_TEXTO6']."\n".$str_lang['LANG_CARTAS_TEXTO7']."\n\n".$str_lang['LANG_CARTAS_TEXTO8']."\n".$str_lang['LANG_CARTAS_TEXTO9']."\n".$str_lang['LANG_CARTAS_TEXTO10'];//"por lo que le solicitamos tomen las medidas oportunas para corregir esta situación en aras de tener el hidrante antes indicado en correcta situación de operatividad para ser usado por nuestro personal cuando sea necesario.
//Para cualquier aclaración puede contactar con este Consorcio en el teléfono 922-533-487.
//Atentamente
   // $pdf->ezText("\n\n",11);                                              
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',$txt);
	$pdf->ezText($p,11,$optionsText);
	$optionsText=array('left' => 30,'justification'=>'center');
	 $pdf->ezText("\n\n\n\n\n\n",11);   
	 $pdf->ezText("\n\n\n\n\n",11);   
$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"".$str_lang['LANG_CARTAS_CONSORCIO']."\r\n".$str_lang['LANG_CARTAS_DIR']." ".$str_lang["LANG_CARTAS_CP_LOCALIDAD"]."\r\n".$str_lang["LANG_CARTAS_ZONA"]." ".$str_lang["LANG_CARTAS_TELEFONO"]." ".$str_lang["LANG_CARTAS_FAX"]);
$pdf->ezText($p,7,$optionsText);	
	
	
	if ($opcion==1)
	{
	$pdfcode = $pdf->output();
	$dir = uploadpathadmin.'cartas/';

	$fname = $dir.'carta_'.$hidrante_id.'_'.$carta_id.'.pdf';
	$fp = fopen($fname,'w');
	fwrite($fp,$pdfcode);
	fclose($fp);
	return 'carta_'.$hidrante_id.'_'.$carta_id.'.pdf';
	}else
	{
	ob_end_clean();
	$pdf->ezStream();
	}
	//$pdf->ezStream();
}

function get_asociado($id)
{		
	global $bd;
	$action_edit=$bd->get_all_by_id("incidencias","incidencia_id",$id);
	
	$lan=getPrefijoLang();
	return $action_edit["incidencia".$lan];
}

function generar_ficha($id)
{
	global $bd;
	global $str_lang;
	$sub=getPrefijoLang();
	
	$action_edit=$bd->get_all_by_id("V_hidrantes","hidrante_id",$id);
	$datos= $action_edit;
	
	$dir = uploadpathadmin.'fichas/';
	$fname = $dir.'ficha_'.$id.'.pdf';
	copy($dir."plantilla.pdf",$fname);  
	
	require_once(serverpath.'includes/classes/fpdf16/fpdf.php');
	require_once(serverpath.'includes/classes/FPDI/fpdi.php');

	// initiate FPDI
$pdf =& new FPDI();
// add a page
$pdf->AddPage();
// set the sourcefile
$pdf->setSourceFile($dir."plantilla.pdf");
// import page 1
$tplIdx = $pdf->importPage(1);

//Escribimos datos pagina 1
// now write some text above the imported page

//Linea 1 
$pdf->SetFont('Arial');
$pdf->SetXY(62, 63);
$pdf->Write(0, $datos["codigo"]);

$pdf->SetXY(154, 63);
$pdf->Write(0, convert_date($datos["fecharevision"]));

//Linea 2
$pdf->SetXY(62, 70);
$pdf->Write(0, $datos["edificio"]);

//Linea 3
$pdf->SetXY(62, 77);
$pdf->Write(0, $datos["calle"]);

//Linea 3
$pdf->SetXY(62, 99);
$pdf->Write(0, $datos["utmx"]);

$pdf->SetXY(92, 99);
$pdf->Write(0, $datos["utmy"]);
$pdf->SetXY(125, 99);
$pdf->Write(0, $datos["geon"]);
$pdf->SetXY(156, 99);
$pdf->Write(0, $datos["geow"]);

//Linea 4
$pdf->SetXY(62, 106);
$pdf->Write(0, $datos["municipio"]);



//Tipo de hidrante

switch ($datos['tipohidrante_id'])
{
	case 1:	$pdf->SetXY(97, 194);
		$pdf->Write(0, "x");
		break;
	case 2:$pdf->SetXY(97, 201.5);
		$pdf->Write(0, "x");
		break;
	case 3:$pdf->SetXY(97, 208.5);
		$pdf->Write(0, "x");
		break;
	case 4:$pdf->SetXY(97, 215);
		$pdf->Write(0, "x");
		break;
	case 5:$pdf->SetXY(97, 222);
		$pdf->Write(0, "x");
		break;
	case 6:$pdf->SetXY(97, 229);
		$pdf->Write(0, "x");
		break;
}


//Diametro
switch ($datos['diametro_id'])
{
	case 1:	$pdf->SetXY(170.5, 201.5);
		$pdf->Write(0, "x");
		break;
	case 2:$pdf->SetXY(170.5, 208.5);
		$pdf->Write(0, "x");
		break;
	case 3:$pdf->SetXY(170.5, 215);
		$pdf->Write(0, "x");
		break;
	case 4:$pdf->SetXY(170.5, 222);
		$pdf->Write(0, "x");
		break;

}


//Racores
switch ($datos['racor_id'])
{
	case 1:	$pdf->SetXY(170.5, 236.5);
		$pdf->Write(0, "x");
		break;
	case 2:$pdf->SetXY(170.5, 243.5);
		$pdf->Write(0, "x");
		break;
	

}

//señalizado
if ($datos['senyalizado'])
{
	$pdf->SetXY(98, 243.5);
	$pdf->Write(0, "x");
}

	
// use the imported page and place it at point 10,10 with awidth of 100 mm
$pdf->useTemplate($tplIdx, 5, 5, 200);

//Pagina 2

$pdf->AddPage();
$tplIdx = $pdf->importPage(2);
//Red exterior linea 1
if ($datos['redexterior'])
{
	$pdf->SetXY(117.5, 57.5);
	$pdf->Write(0, "x");
}else{
	$pdf->SetXY(173.2, 57.5);
	$pdf->Write(0, "x");
}

//Presion caudal linea 2
if ($datos['presionadecuado'])
{
	$pdf->SetXY(117.5, 64.5);
	$pdf->Write(0, "x");
}else{
	$pdf->SetXY(173.2, 64.5);
	$pdf->Write(0, "x");
}

//Estado adecuado linea 3
if ($datos['estadogeneral'])
{
	$pdf->SetXY(117.5, 118);
	$pdf->Write(0, "x");
}else{
	$pdf->SetXY(173.2, 118);
	$pdf->Write(0, "x");
}


$pdf->useTemplate($tplIdx, 5, 5, 200);

	$pdf->Output('ficha_'.$id.'.pdf', 'D');
	
	
	}
	//$pdf->ezText("\n\n\n",10);
	//Datos de la empresa suminstradora
	/*
	$datos_municipio=$bd->get_all_by_id("municipios","municipio_id",$datos["municipio_id"]);

	
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_MUNICIPIOS_COL_EMPRESA2'].":</b> ".$datos_municipio["empresa"]);
	$destinatario[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_MUNICIPIOS_COL_DIRECCION'].": </b> ".$datos_municipio["direccion"]);
	$destinatario[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>CP: </b> ".$datos_municipio["codigo"] . " <b>".	$str_lang['LANG_MUNICIPIOS_COL_POBLACION'].": </b> ".$datos_municipio["poblacion"]);
	$destinatario[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO'].": </b> ".$datos_municipio["municipio"]);
	$destinatario[] = array('col1'=>$p);

	$titles = array('col1'=>'<b>Numero</b>');
	
	$options =array('showHeadings'=>0,'shaded'=>0,'showLines'=>0,'xPos'=>350,'xOrientation'=>'right','width'=>300,'fontSize' => 11);
	$pdf->ezTable($destinatario,$titles,'',$options );

	$optionsText=array('left' => 30);	
	
	$pdf->ezText("\n\n\n\n",11);
	
	$txt=$str_lang['LANG_CARTAS_TEXTO3'];//"Por la presente se le comunica que el hidrante situado en su municipio, identificado como:\n";
	$pdf->ezText($txt,11,$optionsText);

//	$pdf->ezText("<b>Datos Ficha Hidrante</b>\n",16,$optionsText);

	$data[] = array('col1'=>"<b>".$str_lang['LANG_CARTAS_HIDRANTE'].":</b> ".$datos["codigo"]);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".$str_lang['LANG_HIDRANTES_CALLE']."/".$str_lang['LANG_HIDRANTES_EDIFICIO'].":</b> ".$datos["calle"]." / ".$datos["edificio"]);
	$data[] = array('col1'=>$p);
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".	$str_lang['LANG_MUNICIPIOS_COL_MUNICIPIO'].": </b> ".$datos["municipio"]);
	$data[] = array('col1'=>$p);
	
	$data[] = array('col1'=>"<b>".$str_lang['LANG_HIDRANTES_UTM1'].":</b> X: ".$datos["utmx"]."  Y: ".$datos["utmy"]."  HUSO: ".$datos["uso"] );
	if ($datos['utmx']!=0 && $datos['utmy']!=0)
	{
		$objUTM = convertirCoordenadaUTM($datos['utmx'],$datos['utmy'],$datos['uso']);								
		if ((strlen($datos['geon'])==0)||(strlen($datos['geow'])==0))
		{
			$datos['geon']=number_format($objUTM->Lat(),5);
			$datos['geow']=number_format($objUTM->Long(),5);
		}
	}
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".$str_lang['LANG_HIDRANTES_GEO1'].": </b> N: ".$datos["geon"]."  W: ".$datos["geow"]);
	$data[] = array('col1'=>$p);



	$titles = array('col1'=>'<b>Numero</b>');
	
	$options =array('showHeadings'=>0,'shaded'=>0,'showLines'=>0,'xPos'=>250,'xOrientation'=>'center','width'=>300,'fontSize' => 11);
	$pdf->ezTable($data,$titles,'',$options );
	$pdf->ezText("\n\n",11);
	
	$txt=$str_lang['LANG_CARTAS_TEXTO4'];//"presenta las siguientes incidencias detectadas por nuestros técnicos en la revisión realizada recientemente:\n";
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',$txt);
	$pdf->ezText($p,11,$optionsText);
	
	$lista_comentarios=$bd->getlisttable("V_CartasComentarios","carta_id=".$carta_id,"fecha desc","","");
	//$pdf->ezStream();
	$m=count($lista_comentarios);
	$optionsText=array('left' => 100);
	
	
	for ($j=0;$j<$m;$j++)
	{
		$pdf->ezText("<b>".$str_lang['LANG_HIDRANTES_FECHA'].": </b>".$lista_comentarios[$j]["fecha"]."",11,$optionsText);
		$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',"<b>".$str_lang['LANG_CARTAS_COMENTARIO'].": </b>".$lista_comentarios[$j]["comentario"]."\n");
		$pdf->ezText($p."\n\n",11,$optionsText);
	}
	$optionsText=array('left' => 30);
	
	$txt=$str_lang['LANG_CARTAS_TEXTO5']."
	".$str_lang['LANG_CARTAS_TEXTO6']."
	".$str_lang['LANG_CARTAS_TEXTO7']."
					
					????????????";//"por lo que le solicitamos tomen las medidas oportunas para corregir esta situación en aras de tener el hidrante antes indicado en correcta situación de operatividad para ser usado por nuestro personal cuando sea necesario.
//Para cualquier aclaración puede contactar con este Consorcio en el teléfono 922-533-487.
//Atentamente
                                                    
	$p= iconv('UTF-8', 'ISO8859-1//TRANSLIT',$txt);
	$pdf->ezText($p,11,$optionsText);
	
	if ($opcion==1)
	{*/
	
	
	
	//$pdf->ezStream();

#

?>
