<?php defined( '_VALID_MOS' ) or die( 'Restricted access' );
/*

*/
/* PHPExcel */
	
	
set_time_limit(2000); 
ini_set ('memory_limit', "128M");
require_once(serverpath.'includes/classes/PHPExcel.php');

$codigo=get_id_by_uri();
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



$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'LISTADO DE HIDRANTES');
$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
$objPHPExcel->getActiveSheet()->getStyle('A:P')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			
function poneCabecera($j)
{
	global $objPHPExcel;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$j, 'Código de Hidrantes')			
				->setCellValue('B'.$j, 'Fecha de alta')
				->setCellValue('C'.$j, 'Fecha última Revisión')
				->setCellValue('D'.$j, 'Calle')
				->setCellValue('E'.$j, 'Edificio')
				->setCellValue('F'.$j, 'Municipio')
				->setCellValue('G'.$j, 'Coordenadas Geográficas')
				->setCellValue('H'.$j, 'Coordenadas UTM')
				->setCellValue('I'.$j, 'Plano de Situación')
				->setCellValue('J'.$j, 'Tipo de Hidrante')
				->setCellValue('K'.$j, 'Diámetros')
				->setCellValue('L'.$j, 'Racores')
				->setCellValue('M'.$j, 'Señalizado')
				->setCellValue('N'.$j, 'Desde red exterior')
				->setCellValue('O'.$j, 'Presión caudal adecuado')
				->setCellValue('P'.$j, 'Estado general adecuado');

				
				
		$objPHPExcel->getActiveSheet()->getStyle("A$j:P$j")->getFont()->setBold(true);			
	$objPHPExcel->getActiveSheet()->getStyle("A$j:R$j")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');				
	/*$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	*/

	
}
function poneCabeceraComentarios($j)
{
	global $objPHPExcel;
	$objPHPExcel->setActiveSheetIndex(0)				
				->setCellValue('B'.$j, 'Fecha Comentario')
				->setCellValue('C'.$j, 'Asociado')
				->setCellValue('D'.$j, 'Usuario')
				->setCellValue('E'.$j, 'Comentario');

	
	$objPHPExcel->getActiveSheet()->getStyle("B$j:E$j")->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle("B$j:G$j")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');							
	/*$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	*/

	
}
function ponDatosHidrante($vector,$i,$j)
{
	global $objPHPExcel;
	
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
	
	
	if ($vector[$i]["senyalizado"]==1)
	{
		$seny='SI';
	}else
		$seny='NO';
		
	$red='NO';	
	if ($vector[$i]['redexterior'])
	{	
		$red='SI';
	}	
	$pres='NO';		
	if ($vector[$i]['presionadecuado'])
	{
		$pres='SI';
	}	
	$est='NO';		
	if ($vector[$i]['estadogeneral'])
	{
		$est='SI';
	}	
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('M'.$j, $seny)
		->setCellValue('N'.$j, $red)
		->setCellValue('O'.$j, $pres)
		->setCellValue('P'.$j, $est);
		
    $objPHPExcel->setActiveSheetIndex(0)      
		   ->setCellValue('A'.$j, $vector[$i]["codigo"])
            ->setCellValue('B'.$j, convert_date($vector[$i]["fecha"]))
			->setCellValue('C'.$j, convert_date($vector[$i]["fecharevision"]))
			->setCellValue('D'.$j, $vector[$i]["calle"])
			->setCellValue('E'.$j, $vector[$i]["edificio"])
			->setCellValue('F'.$j, $vector[$i]["municipio"]);
	
	$objPHPExcel->setActiveSheetIndex(0)   			
			->setCellValue('G'.$j, "N: ".$coordn. " W: ".$coordw)
			->setCellValue('H'.$j, "X: ".$vector[$i]['utmx']." Y: ".$vector[$i]['utmy'])
			->setCellValue('I'.$j, $vector[$i]["planosituacion"]);
	
	$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('J'.$j, $vector[$i]['tipohidrante'])
			->setCellValue('K'.$j, $vector[$i]['diametro'])
			->setCellValue('L'.$j, $vector[$i]['racor']);
}


function ponDatosComentario($vector,$i,$j)
{
	global $objPHPExcel;
	
	
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B'.$j, convert_date($vector[$i]['fechacomentario']))
		->setCellValue('C'.$j,  $vector[$i]['asociado'])
		->setCellValue('D'.$j,  $vector[$i]['Name'])
		->setCellValue('E'.$j, $vector[$i]['comentario']);
		
}
// Miscellaneous glyphs, UTF-8

$where="1=1";	
if (is_jefeparque())
		$where.=" and parque_id=".$_SESSION['HIDRANTES']['parque_id'];


if ($codigo !="")
	$where .=" and codigo like '%".$codigo."%'";			



$vector=$bd->get_all_by_filter_order("V_hidrantes",$where,"codigo asc");

$n=count($vector);

$j=2;
$objUTM="";
$hidranteIni="";

poneCabecera($j);
$j++;
for($i=0;$i<$n;$i++)
{
	
	ponDatosHidrante($vector,$i,$j);
	
	$j++;
		

}

// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Hidrantes');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);



$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(uploadpathadmin.'export/hidrantesPorcodigo.xls');

?>

<?php
//$filename=uploadpathadmin.'export/hidrantesExcel.xls';
$filename=uploadurladmin.'export/hidrantesPorcodigo.xls';

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


	

