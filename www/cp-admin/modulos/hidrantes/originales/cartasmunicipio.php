<?php
global $bd;
	global $str_lang;
	$sub=getPrefijoLang();
	
	$action_edit=$bd->get_all_by_id("V_hidrantes","hidrante_id",$hidrante_id);
	$datos= $action_edit;

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
						'Title'=>'Listado de Cartas por municipio',
						'Author'=>'bomberostenerife',
						'Subject'=>'',
						'Creator'=>'bomberostenerife',
						'Producer'=>'Cartas'
						);
	$pdf->addInfo($datacreator);

	//ezImage(image,[padding],[width],[resize],[justification],[array border])
	$pdf->ezImage(imgadminpath."logo.jpg",30,0,'none','left');
	//$pdf->ezSetDy(30);
	$pdf->ezText("\n\n\n",10);	
	
	//Datos de la empresa suminstradora
	$datos_carta=$bd->get_all_by_filter_order("V_CartasComentarios","municipio_id=".$_POST["municipio"],"fecharegistro desc");
	//$lista_comentarios=$bd->getlisttable("V_CartasComentarios","carta_id=".$carta_id,"fecha desc","","");
	$n=count($datos_carta);
	
	for($i=0;$i<$n;$i++)
	{
		$p1= $datos_carta[$i]['codigohidrante'];
		
		$p2= $datos_carta[$i]["fecharegistro"];

		$p3= $datos_carta[$i]["fechaentrada"];

		$p4= iconv('UTF-8', 'ISO8859-1//TRANSLIT',$datos_carta[$i]["comentario"] );
		$destinatario[] = array('col1'=>$p1,'col2'=>$p2,'col3'=>$p3,'col4'=>$p4);
	}
	$titles = array('col1'=>'<b>Código hidrante</b>','col2'=>'<b>fecha de registro de salida del consorcio</b>','col3'=>'<b>fecha de registro de entrada en el ayuntamiento</b>','col4'=>'<b>Comentario</b>');
	
	$options =array('showHeadings'=>1,'shaded'=>1,'showLines'=>1,'xPos'=>30,'xOrientation'=>'right','width'=>500,'fontSize' => 11);
	$pdf->ezTable($destinatario,$titles,'',$options );
	
	ob_end_clean();
	$pdf->ezStream();
	
?>