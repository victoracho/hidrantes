<?php
// RECUERDA QUE PARA SUBDOMINIOS HAY QUE CONFIGURAR EL HTACCESS, PLESK Y SERVIDOR APACHE (DNS WILKACARDS)

// Sesiones
define ('session_key','83jkfkasddkfaFASDFAJHSdkf84327349824');

// Datos conexión a la Base de datos (MySql)
define('sql_host','localhost');  // Host, nombre del servidor o IP del servidor Mysql.
define('sql_usuario','prehidrantes');        // Usuario de Mysql
define('sql_pass','Laravel21!');           // contraseña de Mysql
define('sql_db','admin_prehidrantes');     // Base de datos que se usará.

// SOLO SE USA PARA EL LOGIN
define('sql_host2','localhost');  // Host, nombre del servidor o IP del servidor Mysql.
define('sql_usuario2','partesdemo_user');        // Usuario de Mysql
define('sql_pass2','Cip123456');           // contraseña de Mysql
define('sql_db2','partesdemo_db');     // Base de datos que se usará.

// Path del servidor
define('path','http://prehidrantes.eu/'); //Url base
define('serverpath','/var/www/html/'); // path del servidor
define('imgpath', path .'images/');
define('imgadminpath', path .'cp-admin/images/');
define('imgserverpath', serverpath .'images/');
define('csspath', path .'css/');
define('scriptpath', path .'scripts/');
define('uploadpath', serverpath .'upload/');
define('uploadpathadmin', serverpath .'cp-admin/upload/');
define('uploadurladmin', path .'cp-admin/upload/');
define('uploadurl', path .'upload/');

// Palabras reservadas.
define('item_error','pagina-no-disponible');
define('item_news','news');
define('item_search','search');
define('item_admin','cp');
define('item_feed','feed');
define('item_sitemap','sitemap');
define('item_contacto','contactar');

// Perfiles usuarios
define('administrador',114);
define('gestor',120);
define('gerente',128);
define('jefe_de_parque',130);
define('jefe_de_guardia',140);
define('administrativo',150);
define('oficial_jefe',160);
define('uno_uno_dos',170);

// Otras configuraciones
define('creator','Canarias Infopista');
define('creator_email','soporte@cip.es');
define('autor','Gestión de Hidrantes - CBT');
define('autor_email',"soporte@cip.es");
define('hometitle','Gestión de Hidrantes');
define('titleWeb',' &laquo; '. autor);
define('analytics','');
define('googleverify','');
define('w_pagination_cant',20); // Paginacion de la web.
define('pagination_cant',20); // Paginacion de la parte de administracion.
define('maxfotos',4); 
define('maxfiles',4); 

// EMAIL
define('smtp_host','mail.cip.es');
define('smtp_user','sendmail@cip.es');
define('smtp_pass','19rELMOQ');
define('mail_type','0'); // 0->smtp  1->mail
?>
