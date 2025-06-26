<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
$page=get_id_by_uri();

/** PHPExcel */

require_once(serverpath.'includes/classes/PHPExcel.php');


$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("BomberosTenerife")
							 ->setLastModifiedBy("BomberosTenerife")
							 ->setTitle("Datos Hidrantes")
							 ->setSubject("")
							 ->setDescription("Listado de Códigos de Hidrantes y sus coordenadas N y W")
							 ->setKeywords("Codigo Coordenadas Hidrantes")
							 ->setCategory("");


							 $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);



// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('C1', 'Código de Hidrantes')
            ->setCellValue('B1', 'Coordenada N')
            ->setCellValue('A1', 'Coordenada W');
            
$objPHPExcel->getActiveSheet()->getStyle("A1:D1")->getFont()->setBold(true);
			
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);


$objPHPExcel->getActiveSheet()->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// Miscellaneous glyphs, UTF-8
$vector=$bd->get_all("hidrantes","codigo");

$n=count($vector);
$j=2;
$objUTM="";

for($i=0;$i<$n;$i++)
{
	$coordn=$vector[$i]['geon'];
	$coordw=$vector[$i]['geow'];
	$utmx=$vector[$i]['utmx'];
	$utmy=$vector[$i]['utmy'];
	if ($coordn=="") $coordn=0;
	if ($coordw=="") $coordw=0;
	if ($utmx=="") $utmx=0;
	if ($utmy=="") $utmy=0;
	

	if (($coordn==0)||($coordw==0))
	{

		if (($utmx!=0)&&($utmy!=0)&&($vector[$i]['uso']!=0))
		{	
		
		
			$objUTM = convertirCoordenadaUTM($utmx,$utmy,$vector[$i]['uso']);
			
			
			$coordn=number_format($objUTM->Lat(),5);
			$coordw=number_format($objUTM->Long(),5);
			
		}
		
	}else
	{
		if (($utmx==0)||($utmy==0))
		{	
		
			if (($coordn!=0)&&($coordw!=0))
			{
				//echo $coordn." ".$coordw." ".$vector[$i]["codigo"]."<br>" ;
				
				$objUTM = convertirCoordenadaGEO($coordn,$coordw);
	
				$utmx=number_format($objUTM->E(),2,'.','');
				$utmy=number_format($objUTM->N(),2,'.','');
				$vector[$i]['uso'] =substr($objUTM->Z(),0,2);
			
				
				
			}
		}
		
	}

	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('C'.$j, $vector[$i]["codigo"])
            ->setCellValue('B'.$j, $coordn)
			->setCellValue('A'.$j, $coordw);
	
	$j++;
	
	
}

// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Hidrantes');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);



$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(uploadpathadmin.'export/hidrantesExcel.xls');

?>

<?php
//$filename=uploadpathadmin.'export/hidrantesExcel.xls';
$filename=uploadurladmin.'export/hidrantesExcel.xls';

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


	

