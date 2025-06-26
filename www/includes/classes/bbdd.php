<?php
class bbdd
{
    var $conn;  //contendra el enlace de nuestra conexion
    var $host;    //nombre o ip de nuestro host
    var $usuario;  //nombre de usuario
    var $pass;    //nuestro password
    var $bd;     //nombre de nuestra base de datos

	function bbdd($host,$usuario,$pass,$bd){
	$this->host = $host;
	$this->usuario = $usuario;
	$this->pass = $pass;
	$this->bd = $bd;
	//ahora llamo al metodo que realiza la conexion
	$this->conn = $this->conectar();
	}
	function conectar(){
	            //trato de conectarme
	        $conex=mysql_connect($this->host, $this->usuario, $this->pass);
	        if($conex){
	            //Si me pude conectar, selecciono la base de datos
	            if(!mysql_select_db($this->bd, $conex))
	                     //si no puedo seleccionar aviso
	                echo "no selecciono la bd";
	        }else
	            echo "no se conecto";  //no pude conectarme, aviso
	        return $conex;  //devuelvo el enlace de la conexion
			}
	function bbdd_query($sql){
		//	mysql_query("SET CHARACTER SET UTF8");
		//	mysql_query("SET NAMES UTF8");		
	    	$r = mysql_query($sql, $this->conn) or die (mysql_error());
	    	return $r;
	}

	function bbdd_fetch($r){
	        $row = mysql_fetch_array($r);
	       return $row;
	}

    function bbdd_num($r){
		$cant = mysql_num_rows($r);
    return $cant;
	}
	 function bbdd_desc(){
	        mysql_close($this->conn);
	    }

	// Obtenemos hijos de una pagina
	function get_page_childreen($p){
		$x=1;
		$parent=array();
		while ($x==1)
		{
			$sql="SELECT parent_id from post WHERE id=".entrada_sql($p);
			$r = $this->bbdd_query($sql);
			$row=$this->bbdd_fetch($r);
			if ($row['parent_id']==0): $x=0;
			else: 
				$parent[]=$row['parent_id'];
				$p=$row['parent_id'];
			endif;
		}
		return $parent;
	}
		
	// Obtenemos el title desde el id
	function get_page_title_by_id($p){
			$sql="SELECT title from post WHERE id=".entrada_sql($p);
			$r = $this->bbdd_query($sql);
			$row=$this->bbdd_fetch($r);
			return $row['title'];
	}	
	
	// Obtenemos el uri desde el id
	function get_uri_by_id($p){
			$sql="SELECT uri from post WHERE id=".entrada_sql($p);
			
			$r = $this->bbdd_query($sql);
			$row=$this->bbdd_fetch($r);
			return $row['uri'];
	}
	
	// Obtenemos todos los datos de un post/page por el uri.
	function get_all_by_uri($uri){
			$sql="SELECT * from post WHERE uri=".entrada_sql($uri)." and status='publish'";
			$r = $this->bbdd_query($sql);
			$row=$this->bbdd_fetch($r);
			return $row;
	}
	
	// Obtenemos todos los datos de un post/page por el uri.
	function get_all($table,$order=""){
			$sql="SELECT * from ".$table;
			if ($order!="")
				$sql.=" order by $order";
			$r = $this->bbdd_query($sql);
			while ($row=$this->bbdd_fetch($r))
			$str[]=$row;
			return $str;
	}
	// Obtenemos todos los datos de un post/page por el uri.
	function get_all_sql($sql){		
			if (empty($sql))
				return $str;
			$r = $this->bbdd_query($sql);
			while ($row=$this->bbdd_fetch($r))
			$str[]=$row;
			return $str;
	}
	function get_all_by_filter($table,$where){
			$sql="SELECT * from ".$table ;
			if (!empty($where))
					$sql.= " WHERE ".$where;			
			$r = $this->bbdd_query($sql);			
			while ($row=$this->bbdd_fetch($r))
			$str[]=$row;			
			return $str;
	}		
function get_all_by_filter_order($table,$where,$order){
			$sql="SELECT * from ".$table ;
			if (!empty($where))
					$sql.= " WHERE ".$where;	
			if (!empty($order))
					$sql.= " ORDER BY ".$order;	
									
			$r = $this->bbdd_query($sql);			
			while ($row=$this->bbdd_fetch($r))
			$str[]=$row;			
			return $str;
	}			
	
	// Obtenemos el title desde el id del post.
	function get_category($p){
			$sql="SELECT category.title,category.cat_id, category.cat_uri from post2cat, category WHERE category.cat_id=post2cat.cat_id and post2cat.post_id=".entrada_sql($p);
			$r = $this->bbdd_query($sql);
			$row=$this->bbdd_fetch($r);
			return $row;
	}	
	
	// Comprobar si es una categoria.
	function get_category_id($cat_uri){
			$sql="SELECT cat_id from category where cat_uri=".entrada_sql($cat_uri);
			$r = $this->bbdd_query($sql);
			$row=$this->bbdd_fetch($r);
			return $row[cat_id];
	}
	// Obtener la categoria por el uri
	function get_category_by_uri($cat_uri){
			$sql="SELECT * from category where cat_uri=".entrada_sql($cat_uri);
			$r = $this->bbdd_query($sql);
			$row=$this->bbdd_fetch($r);
			return $row;
	}
	// Comprobar si es una categoria.
	function get_category_list ($p,$sep){
			$str="";
			$sql="SELECT category.title,category.cat_id, category.cat_uri from post2cat, category WHERE category.cat_id=post2cat.cat_id and post2cat.post_id=".entrada_sql($p);
			$r = $this->bbdd_query($sql);
			$i=0;
			while ($row=$this->bbdd_fetch($r))
			{	
				if ($i==0)
				$str.=$row['title'];
				else $str.=" ".$sep." ".$row['title'];
				$i=1;
			}
			return $str;
	}		

	// Actualizar ip de usuario
	function update_user_ip(){
        //Actualizamos la ip, si se ha logeado desde otro sitio o lo ha cambiado.
		$ip=obtener_ip();
		$sql="UPDATE users set ip='".$ip."' where id='".$_SESSION['id']."'";
		$r = $this->bbdd_query($sql);		
		}
	// Obtener ultimos post publicados
	function getlatestpost($limit){
		$sql="SELECT * from post where post_type='post' and status='publish' order by date_i DESC, id DESC limit ".$limit;
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}
	// Obtener ultimas paginas publicadas
	function getlatestpage($limit){
		$sql="SELECT * from post where post_type='page' and status='publish' order by date_i DESC, id DESC limit ".$limit;
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}
	// Obtener post pendientes
	function getdraftpost($limit){
		$sql="SELECT * from post where post_type='post' and status='draft' order by date_i DESC, id DESC limit ".$limit;
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}	
	// Obtener paginas pendientes
	function getdraftpage($limit){
		$sql="SELECT * from post where post_type='page' and status='draft' order by date_i DESC, id DESC limit ".$limit;
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}	

	// Obtener todos los post
	function getpost($offset,$limit){
		$sql="SELECT * from post where post_type='post' order by date_i DESC limit $offset, $limit";
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}
	// Obtener todos los post
	function posts($offset,$limit){
		$sql="SELECT * from post where post_type='post' and status='publish' order by date_i DESC, id DESC limit $offset, $limit";
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}	
	// Obtener todas las paginas
	function getpage($offset,$limit){
		$sql="SELECT * from post where post_type='page' order by date_i DESC limit $offset, $limit";
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}
	// Obtener todas las paginas
	function page($offset,$limit){
		$sql="SELECT * from post where post_type='page' and status='publish' order by date_i DESC, id DESC limit $offset, $limit";
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}	
	// Obtener contador paginas/post
	function getcountpagepost($type){
		$sql="SELECT count(*) as total from post where post_type='".$type."'";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);
		return $row['total'];		
	}
	// Obtener todos los usuarios
	function getcountusers(){
		$sql="SELECT count(*) as total from users";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);	
		return $row['total'];	
	}		
	// Obtener todos los usuarios
	function getusers($offset,$limit){
		$sql="SELECT * from users order by id DESC limit $offset, $limit";
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}
	// Obtener todos los idiomas con paginacion.
	function getlang($offset,$limit){
		$sql="SELECT * from language order by id DESC limit $offset, $limit";
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}
	// Obtener contador idiomas
	function getcountlang(){
		$sql="SELECT count(*) as total from language";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);
		return $row['total'];		
	}	
	// Obtener cantidad de paginas en un idioma
	function getlangpagecant($lang_id){
		$sql="SELECT count(*) as total from post2lang, post where post2lang.lang_id=".$lang_id." and post2lang.post_id=post.id and post.post_type='page'";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);
		return $row['total'];		
	}
	// Obtener cantidad de post en un idioma
	function getlangpostcant($lang_id){
		$sql="SELECT count( * ) AS total FROM post2cat, cat2lang, post 
		WHERE cat2lang.lang_id =".$lang_id." AND cat2lang.cat_id = post2cat.cat_id 
		AND post2cat.post_id = post.id AND post_type = 'post'";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);
		return $row['total'];		
	}
	// Insertar en la bd
	function insert($tabla, $campos){
		$str="";
		foreach ($campos as $cam) $str.=",".entrada_sql($cam);
		$sql="INSERT INTO ".$tabla." VALUES (''".$str.")";
		
		$r = $this->bbdd_query($sql);		
	}
	function insert_con_id($tabla, $campos){
		$str="";
		foreach ($campos as $cam) $str.=",".entrada_sql($cam);
		$sql="INSERT INTO ".$tabla." VALUES (''".$str.")";
		
		$r = $this->bbdd_query($sql);	
		return	mysql_insert_id($this->conn);  
	}
	// Insertar en la bd
	function insert_sin_id($tabla, $campos){
		$str="";
		$i=0;
		foreach ($campos as $cam)
			{
			if ($i==0) $str.=entrada_sql($cam);
			else $str.=",".entrada_sql($cam);
			$i++;
			}
		$sql="INSERT INTO ".$tabla." VALUES (".$str.")";
	
		$r = $this->bbdd_query($sql);
	}	
	// Insertar en la bd
	function update($tabla, $campos,$campo_id,$id){
		$str="";
		$i=0;
		foreach($campos as $key => $value)
		{	
			if ($i==0) $str.=" ".$key."=".entrada_sql($value);
			else $str.=", ".$key."=".entrada_sql($value);
			$i=1;
		}
		
		$sql="UPDATE ".$tabla." SET ".$str." WHERE ".$campo_id."=".entrada_sql($id);		
		$r = $this->bbdd_query($sql);		
	}

	// Eliminar en la bd mediante id
	function delete($tabla, $campo, $id){
		$sql="DELETE FROM ".$tabla." WHERE ".$campo."=".entrada_sql($id)."";
		$r = $this->bbdd_query($sql);		
	}	

	// Eliminar en la bd mediante consulta
	function delete_more($tabla, $where){
		$sql="DELETE FROM ".$tabla." WHERE ".$where."";
		$r = $this->bbdd_query($sql);		
	}	
		
	// Comprobar si se repite un campo
	function check_repeat($tabla, $campo,$valor){
		$sql="SELECT count(*) as total FROM ".$tabla." WHERE ".$campo."=".entrada_sql($valor)."";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);
		if ($row['total']==0) return false;
		else return true;		
	}	
	// Obtener los datos a partir de un id
	function get_all_by_id($tabla, $campo, $id){
		$sql="SELECT * from ".$tabla." WHERE ".$campo."=".entrada_sql($id)."";		
		
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);
		return $row;
	}

	// Obtener el uri completo de una pagina.
	function get_uri_path_by_id($p){
		$padre=array_reverse($this->get_page_childreen($p));
		$str="";
		foreach ($padre as $par)
		{
		$turi=$this->get_uri_by_id($par);
		if (!empty($turi))
		$str.=$turi."/";	
		}	
		return $str;		
	}

	// Obtener el listado de páginas mediante select
	function display_children_option($parent, $level, $post_type,$selected,$exclude) { 
	   // retrieve all children of $parent 
	   if (empty($exclude))
	   $sql="SELECT title,id FROM post WHERE parent_id=".entrada_sql($parent)." and post_type='".$post_type."'";
	   else $sql="SELECT title,id FROM post WHERE parent_id=".entrada_sql($parent)." and post_type='".$post_type."' and id!=".entrada_sql($exclude)."";
		$r = $this->bbdd_query($sql);
	   // display each child 	   
	   while ($row=$this->bbdd_fetch($r)) { 
	   		$str="";
	   		if ($row['id']==$selected) $str=' selected="selected"';
	       // indent and display the title of this child 
	       echo '<option value="'.$row['id'].'" '.$str.'>'.str_repeat(' -- ',$level).$row['title'].'</option>'; 
	
	       // call this function again to display this 
	       // child's children 
	       $this->display_children_option($row['id'], $level+1,$post_type,$selected,$exclude); 
	   } 
	}
	function get_firstlevel_child_id($p,$post_type){
		$sql="SELECT id FROM post WHERE parent_id=".entrada_sql($p)." and post_type='".$post_type."'";
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row['id'];
		return $str;		
	}	
	function get_lang_by_id($p){
		$sql="SELECT language.* FROM language, post2lang WHERE language.id=post2lang.lang_id and post_id=".entrada_sql($p)."";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);
		return $row;		
	}
	// Obtener todos los idiomas
	function display_lang_list($selected){
		$sql="SELECT * from language order by lang";
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		{
		$str="";
		if ($row['id']==$selected) $str=' selected="selected"';
		echo '<option value="'.$row['id'].'" '.$str.'>'.$row['lang'].'</option>'; 
		}		
	}	


	// Obtener el listado de páginas mediante select, es una funcion recursiva.
	function display_catchildren_option($parent, $level,$selected,$exclude) { 
	   // retrieve all children of $parent 
	   if (empty($exclude))
	   $sql="SELECT title,cat_id FROM category WHERE parent_id=".entrada_sql($parent)."";
	   else $sql="SELECT title,cat_id FROM category WHERE parent_id=".entrada_sql($parent)." and cat_id!=".entrada_sql($exclude)."";
		$r = $this->bbdd_query($sql);
	   // display each child 	   
	   while ($row=$this->bbdd_fetch($r)) { 
	   		$str="";
	   		if ($row['cat_id']==$selected) $str=' selected="selected"';
	       // indent and display the title of this child 
	       echo '<option value="'.$row['cat_id'].'" '.$str.'>'.str_repeat(' -- ',$level).$row['title'].'</option>'; 
	
	       // call this function again to display this 
	       // child's children 
	       $this->display_catchildren_option($row['cat_id'], $level+1,$selected,$exclude); 
	   } 
	}
	// Obtener contador idiomas
	function getcountcat(){
		$sql="SELECT count(*) as total from category";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);
		return $row['total'];		
	}	
	
	// Obtener todos los idiomas con paginacion.
	function getcat($offset,$limit){
		$sql="SELECT * from category order by cat_id DESC limit $offset, $limit";
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}
	// Obtengo el uri de una categoria por el id.
	function get_cat_uri_by_id($p){
			$sql="SELECT cat_uri from category WHERE cat_id=".entrada_sql($p);
			$r = $this->bbdd_query($sql);
			$row=$this->bbdd_fetch($r);
			return $row['cat_uri'];
	}
	// Obtenemos hijos de una categoria
	function get_cat_childreen($p){
		$x=1;
		$parent=array();
		while ($x==1)
		{
			$sql="SELECT parent_id from category WHERE cat_id=".$p;
			$r = $this->bbdd_query($sql);
			$row=$this->bbdd_fetch($r);
			if ($row['parent_id']==0): $x=0;
			else: 
				$parent[]=$row['parent_id'];
				$p=$row['parent_id'];
			endif;
		}
		return $parent;
	}
	// Obtener el listado de hijos de una categoria padre.
	function get_cat_parent(&$parent,$p){
		$sql="SELECT cat_id from category WHERE parent_id=".$p;
		$r = $this->bbdd_query($sql);
		 while ($row=$this->bbdd_fetch($r))
		 {			
			$parent[]=$row['cat_id'];
			$this->get_cat_parent($parent,$row['cat_id']);
		 }
		return $parent;
	}	
	
	// Obtener el uri completo de una categoria.
	function get_cat_uri_path_by_id($p){
		$padre=array_reverse($this->get_cat_childreen($p));
		$str="";
		foreach ($padre as $par)
		{
		$turi=$this->get_cat_uri_by_id($par);
		if (!empty($turi))
		$str.=$turi."/";	
		}	
		return $str;		
	}
	// Obtener el idioma de una categoría
	function get_cat_lang_by_id($p){
		$sql="SELECT language.* FROM language, cat2lang WHERE language.id=cat2lang.lang_id and cat_id=".entrada_sql($p)."";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);
		return $row;		
	}			
	// Obtener el total de noticias en una categoria
	function get_count_post_on_cat($cat_id){
		$sql="SELECT count(*) as total from post2cat WHERE cat_id=".entrada_sql($cat_id)."";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);
		return $row['total'];		
	}
	// Obtener el primer nivel de hijos de una categoria.	
	function get_cat_firstlevel_child_id($p){
		$sql="SELECT cat_id FROM category WHERE parent_id=".entrada_sql($p)."";
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row['cat_id'];
		return $str;		
	}	
	// Obtener todas las categorias de un idiomas
	function get_cat_list_by_lang($lang_id){
		$sql="SELECT cat_id FROM  cat2lang WHERE lang_id=".entrada_sql($lang_id)."";		
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row['cat_id'];
		return $str;			
	}	
	// Obtener el idioma de una categoría
	function get_lang($p){
		$sql="SELECT * FROM language WHERE id=".entrada_sql($p)."";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);
		return $row;		
	}
	function get_lang_by_uri($p){
		$sql="SELECT * FROM language WHERE uri=".entrada_sql($p)."";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);
		return $row;		
	}
	function get_lang_text_by_uri($p){
		if (!empty($p))
		$sql="SELECT text_title,text FROM language_text,language WHERE language.uri=".entrada_sql($p)." and language.id=language_text.lang_id order by text_title ASC";
		else $sql="SELECT text_title, text FROM language_text WHERE lang_id=1 order by text_title ASC";
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[$row['text_title']]=$row['text'];
		return $str;			
	}

	function get_lang_text_by_id($p){
		$sql="SELECT text_title,text FROM language_text WHERE lang_id=".entrada_sql($p)." order by text_title ASC";
		$r = $this->bbdd_query($sql);
		while ($row=$this->bbdd_fetch($r))
		$str[$row['text_title']]=($row['text']);
		return $str;			
	}		
	
	// Obtenemos el permalink de un post.
	function get_post_permalink($p){
		global $path;
			$s_host=$_SERVER['HTTP_HOST'];
			$host=explode(".",$s_host);
			$tcat=$this->get_category($p['id']);
			$idioma=$this->get_cat_lang_by_id($tcat['cat_id']);
			if ($this->getcountlang()>1)
			$path_n=str_replace($host[0],$idioma['uri'],$path);	
			else $path_n=path;	
			$str=$this->get_cat_uri_path_by_id($tcat['cat_id']);	
			$url=$path_n.item_news.'/'.$str.$tcat['cat_uri']."/".$p['uri']."/";				
		return $url;		
	}
	// Obtenemos el permalink de una pagina.
	function get_page_permalink($p){
		global $path;		
			$s_host=$_SERVER['HTTP_HOST'];
			$host=explode(".",$s_host);		
			$idioma=$this->get_lang_by_id($p['id']);
			$str=$this->get_uri_path_by_id($p['id']);
			if ($this->getcountlang()>1)
			$path_n=str_replace($host[0],$idioma['uri'],$path);	
			else $path_n=path;					
			$url=$path_n.$str.$p['uri']."/";							
		return $url;		
	}

	function page_list($limit_i, $limit_f){	
		$tPost=$this->page($limit_i,$limit_f);
		if (!empty($tPost))
		{
			echo '<ul>';
			foreach ($tPost as $post)
				{	
					$url=$this->get_page_permalink($post);				
					echo '<li><a href="'.$url.'" title="'.$post['title'].'">'.$post['title'].'</a></li>';
				}	
			echo '</ul>';
		}										
		}

	function post_list($limit_i, $limit_f){	
		$tPost=$this->posts($limit_i,$limit_f);
		if (!empty($tPost))
		{
			echo '<ul>';
			foreach ($tPost as $post)
				{	
					$url=$this->get_post_permalink($post);				
					echo '<li><a href="'.$url.'" title="'.$post['title'].'">'.$post['title'].'</a></li>';
				}	
			echo '</ul>';
		}										
		}			
	// Obtener contador registros
	function getcounttable($table, $where){
		$sql="SELECT count(*) as total from ".$table."";
		if (!empty($where)) $sql.=" WHERE ".$where;
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);	
		return $row['total'];	
	}

	// Obtener listado de una tabla
	function getlisttable($table,$where, $order,$offset,$limit){
		$sql="SELECT * from ".$table."";
		if (!empty($where)) $sql.=" WHERE ".$where;
		if (!empty($order))	$sql.=" order by ".$order." ";
		if (!empty($limit)) 
		$sql.=" limit $offset, $limit";			
		$r = $this->bbdd_query($sql);	
		while ($row=$this->bbdd_fetch($r))
		$str[]=$row;
		return $str;		
	}

	/* Obtener la configuracion de la tienda */
	function getSettings(){
		$sql="SELECT settings from st_settings";
		$r = $this->bbdd_query($sql);
		$row=$this->bbdd_fetch($r);	
		return unserialize($row['settings']);	
	}	
	
	function getnextId($id,$tabla,$campo,$orden)
	{
		$sql="SELECT $campo from $tabla order by $orden";
		$r = $this->bbdd_query($sql);
		
		$next=false;
		While ($row=$this->bbdd_fetch($r))
		{
			
			if ($next)
			{
				return $row;
			}			
			if ($row[$campo]==$id)
			{
				$next=true;
			}
		}
		return "";
		
		
	}
	
	function getPrevId($id,$tabla,$campo,$orden)
	{
		$sql="SELECT $campo from $tabla order by $orden";
		$r = $this->bbdd_query($sql);
		$i=0;

		While ($row=$this->bbdd_fetch($r))
		{
			$result[$i]= $row;
			if ($row[$campo]==$id)
			{
				return $result[$i-1];
			}
			$i++;
		}
		return "";
		
		
	}
	
}

?>