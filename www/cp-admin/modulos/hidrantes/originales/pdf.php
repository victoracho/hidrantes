<?php 


//$action_edit=$bd->get_all_by_id("hidrantes","hidrante_id",$id);
//$datos= $action_edit;

$pdf = new Cezpdf();
$pdf->selectFont(path.'icludes/classes/pdf/fonts/Helvetica.afm');
$pdf->ezText('Mi primer pdf en PHP', 30);
$pdf->ezStream();

/*$pdf->selectFont('./includes/classes/pdf/fonts/Courier.afm');
$datacreator = array (
					'Title'=>'Ficha de Hidrante',
					'Author'=>'bomberostenerife',
					'Subject'=>'PDF con Tablas',
					'Creator'=>'unijimpe@hotmail.com',
					'Producer'=>'http://blog.unijimpe.net'
					);
$pdf->addInfo($datacreator);

$data[] = array('num'=>1, 'mes'=>'Enero');
$data[] = array('num'=>2, 'mes'=>'Febrero');
$data[] = array('num'=>3, 'mes'=>'Marzo');
$data[] = array('num'=>4, 'mes'=>'Abril');
$data[] = array('num'=>5, 'mes'=>'Mayo');
$data[] = array('num'=>6, 'mes'=>'Junio');
$data[] = array('num'=>7, 'mes'=>'Julio');
$data[] = array('num'=>8, 'mes'=>'Agosto');
$data[] = array('num'=>9, 'mes'=>'Septiembre');
$data[] = array('num'=>10, 'mes'=>'Octubre');
$data[] = array('num'=>11, 'mes'=>'Noviembre');
$data[] = array('num'=>12, 'mes'=>'Diciembre');

$titles = array('num'=>'<b>Numero</b>', 'mes'=>'<b>Mes</b>');

$pdf->ezText("<b>Meses en PHP</b>\n",16);
$pdf->ezText("Listado de Meses\n",12);
$pdf->ezTable($data,$titles,'Meses',$options );
$pdf->ezText("\n\n\n",10);
$pdf->ezText("<b>Fecha:</b> ".date("d/m/Y"),10);
$pdf->ezText("<b>Hora:</b> ".date("H:i:s")."\n\n",10);
$pdf->ezStream();*/
?>

