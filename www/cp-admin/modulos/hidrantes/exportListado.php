<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
//$page=get_id_by_uri();
$buscar = urldecode(get_id_by_uri_i(4));
$buscarpor=get_id_by_uri_i(5);
$where="";


/** PHPExcel */

require_once(serverpath.'includes/classes/PHPExcel.php');


$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("BomberosTenerife")
							 ->setLastModifiedBy("BomberosTenerife")
							 ->setTitle("Listado Hidrantes")
							 ->setSubject("")
							 ->setDescription("Listado de los Datos de Hidrantes")
							 ->setKeywords("Hidrantes datos codigo direccion municipio")
							 ->setCategory("Listado");


$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);


// Add some data
if ($buscarpor==2)
{
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'LISTADO DE HIDRANTES FILTRADO POR MUNICIPIO "'.$buscar.'" ');
			
}else
{
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'LISTADO DE HIDRANTES FILTRADO POR CÓDIGO DE HIDRANTE "'.$buscar.'" ');

}

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', 'Código de Hidrantes')
            ->setCellValue('B2', 'Municipio')
            ->setCellValue('C2', 'Direccion')
			->setCellValue('D2', 'Coordenadas GEO')
			->setCellValue('E2', 'Coordenadas UTM');
            
$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);			
$objPHPExcel->getActiveSheet()->getStyle("A2:E2")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
			
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);


$objPHPExcel->getActiveSheet()->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
// Miscellaneous glyphs, UTF-8


switch ($buscarpor)
{
	case "1":$where.="codigo like '%".$buscar."%' and ";
		break;
	case "2":$where.="municipio like '%".$buscar."%' and ";
		break;
}
if (is_jefeparque())
		$where.=" and parque_id=".$_SESSION['HIDRANTES']['parque_id']." and ";

$where.="1=1";		

$vector=$bd->get_all_by_filter_order("V_hidrantes",$where,"codigo asc");

$n=count($vector);
$j=3;
$objUTM="";
for($i=0;$i<$n;$i++)
{
	$coordn=$vector[$i]['geon'];
	$coordw=$vector[$i]['geow'];


	if (($vector[$i]['geon']==0)||($vector[$i]['geow']==0))
	{


		if (($vector[$i]['utmx']!=0)&&($vector[$i]['utmy']!=0)&&($vector[$i]['uso']!=0))
		{	
		
			$objUTM = convertirCoordenadaUTM($vector[$i]['utmx'],$vector[$i]['utmy'],$vector[$i]['uso']);
			
			
			$coordn=number_format($objUTM->Lat(),5);
			$coordw=number_format($objUTM->Long(),5);
			
		}
		
	}else
	{
		if (($vector[$i]['utmx']==0)||($vector[$i]['utmy']==0))
		{	
		
			$objUTM = convertirCoordenadaGEO($vector[$i]['geon'],$vector[$i]['geow']);
			$vector[$i]['utmx']=number_format($objUTM->E(),2 ,'.', '');
			$vector[$i]['utmy']=number_format($objUTM->N(),2, '.', '');
			$vector[$i]['uso'] =substr($objUTM->Z(),0,2);
			
		}
	
	}

	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$j, $vector[$i]["codigo"])
            ->setCellValue('B'.$j, $vector[$i]["municipio"])
			->setCellValue('C'.$j, $vector[$i]["calle"]." ".$vector[$i]["edificio"])
			->setCellValue('D'.$j, "N: ".$coordn. " W: ".$coordw)
			->setCellValue('E'.$j, "X: ".$vector[$i]['utmx']." Y: ".$vector[$i]['utmy']);
	
	$j++;
	
	
}

// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Hidrantes');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);



$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(uploadpathadmin.'export/hidrantesInformeExcel.xls');

?>

<?php
//$filename=uploadpathadmin.'export/hidrantesExcel.xls';
$filename=uploadurladmin.'export/hidrantesInformeExcel.xls';

header("Location:".$filename);
/*echo basename($filename);
echo filesize($filename);
echo readfile($filename);
*/
/*
header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false);
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=\"".basename($filename)."\";");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".@filesize($filename));
            set_time_limit(0);
			@readfile("$filename") ;*/
            //@readfile("$filename") or die("File not found.");
//exit;
?>


	

