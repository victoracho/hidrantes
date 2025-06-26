<?php
ob_start();
session_start();
// no direct access
define( '_VALID_MOS', 1 );

//error_reporting(E_ALL);
//ini_set('display_errors', '1');


// Incluimos archivo de configuración
include( 'php/config.php' );

// Creamos el path del admin
$serveradmin_path=serverpath .'cp-admin/';
$realadmin_path=path .'cp-admin/';
$admin_path=path . item_admin . '/';
$imgadminpath=$realadmin_path. 'images/';

// Incluimos clases y funciones.
require_once(serverpath.'includes/classes/bbdd.php' );
require_once(serverpath.'includes/classes/pagination.class.php' );
require_once(serverpath.'includes/classes/pager.php' );
require_once(serverpath.'includes/classes/sanitize.php' );


require_once(serverpath.'includes/functions/functions.php' );
require_once(serverpath.'includes/functions/validation.php' );
//cargamos idioma
echo "<h1>HOLA<h1>";
// Conexión a la BD
$bd = new bbdd(sql_host,sql_usuario,sql_pass,sql_db);
//$bd_login = new bbdd(sql_host2,sql_usuario2,sql_pass2,sql_db2);
$bd_login = new bbdd(sql_host,sql_usuario,sql_pass,sql_db);
// Recogemos el querystring completo limpio.

$bd_mysqli=new mysqli(sql_host,sql_usuario,sql_pass);
$bd_mysqli->select_db(sql_db);

$params=getParams();

// El array del uri.

$a_uri=getUri();

$lang = strtoupper(substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2));
if(!(isset($_SESSION["Lang"])) || $_SESSION["Lang"]=="")
	$_SESSION["Lang"] =$lang;
	
$str_lang=readLang($_SESSION["Lang"]);

//var_dump($str_lang);

$path = path;
$modulo = item_admin;
$datos = $a_uri[2];
//var_dump($a_uri);
//var_dump($_SERVER);
if ($a_uri[2]=="lang")
	$_SESSION["Lang"]=$a_uri[3];

/* Proteccion */

if ($a_uri[1]!='cp')
{
	Location ($path.'cp/');
	exit();
}

// Cargamos el modulo. Admin o Web
if (item_admin==$modulo) //Si es administrador.
include(serverpath."cp-admin/index.php");
else {
	$temp=explode(".",$modulo); 
	if (count($temp)>1) // Es un modulo generico
	include(serverpath."modulos/".$modulo);
	else include(serverpath."modulos/".$modulo."/index.php");// Es un modulo personalizado
}

$bd->bbdd_desc();
$bd_login->bbdd_desc();
ob_end_flush();
?>