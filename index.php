<?php

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
$arrColumns = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G',
    7 => 'H', 8 => 'I', 9 => 'J', 10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O',
    15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W',
    23 => 'X', 24 => 'Y', 25 => 'Z', 26 => 'AA', 27 => 'AB', 28 => 'AC', 29 => 'AD', 30 => 'AE',
    31 => 'AF', 32 => 'AG', 33 => 'AH', 34 => 'AI', 35 => 'AJ', 36 => 'AK', 37 => 'AL', 38 => 'AM',
    39 => 'AN', 40 => 'AO', 41 => 'AP', 42 => 'AQ', 43 => 'AR', 44 => 'AS', 45 => 'AT', 46 => 'AU',
    47 => 'AV', 48 => 'AW', 49 => 'AX', 50 => 'AY', 51 => 'AZ', 52 => 'BA', 53 => 'BB', 54 => 'BC',
    55 => 'BD', 56 => 'BE', 57 => 'BF', 58 => 'BG', 59 => 'BH', 60 => 'BI', 61 => 'BJ', 62 => 'BK',
    63 => 'BL', 64 => 'BM', 65 => 'BN', 66 => 'BO', 67 => 'BP', 68 => 'BQ', 69 => 'BR', 70 => 'BS',
    71 => 'BT', 72 => 'BU', 73 => 'BV', 74 => 'BW', 75 => 'BX', 76 => 'BY', 77 => 'BZ');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/php/Classes/PHPExcel.php';
require_once dirname(__FILE__) . '/php/Classes/PHPExcel/IOFactory.php';
// $enlace = mysql_connect('localhost', 'root', 'tatateta');
// mysql_select_db('puente_updates');


$conn = pg_connect("dbname=publisher");
if (!$conn) {
  echo "An error occurred.\n";
  exit;
}
var_dump($conn);die;
$result = pg_query($conn, "SELECT * FROM autos");
if (!$result) {
  echo "An error occurred.\n";
  exit;
}

while ($row = pg_fetch_row($result)) {
  echo "Author: $row[0]  E-mail: $row[1]";
  echo "<br />\n";
}
die;
$filename = "puente.xlsx";
if (!file_exists($filename)) {
    exit("No existe el archivo puente.XLS." . EOL);
}
$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load($filename);
$objPHPExcel->setActiveSheetIndex(0);
$aSheet = $objPHPExcel->getActiveSheet();
// get number of last Row
$countRows = $aSheet->getHighestRow();

//get number of last column
$highestColumn = $aSheet->getHighestColumn();
$countCols = PHPExcel_Cell::columnIndexFromString($highestColumn);
$cells = array();

for ($i = 1; $i <= $countRows; $i++) {
    for ($y = 0; $y < $countCols; $y++) {
        $cells[$arrColumns[$y]][$i] = $aSheet->getCell($arrColumns[$y] . $i)->getValue();
    }
}

$compare = true;
$initialize = false;
$commitResults = false;
for ($i = 2; $i <= $countRows; $i++) {
        $f_compra = $cells[$arrColumns[0]][$i];
        $tipo = $cells[$arrColumns[1]][$i];
        $marca = $cells[$arrColumns[2]][$i];
        $modelo = $cells[$arrColumns[3]][$i];
        $ano = $cells[$arrColumns[4]][$i];
        $dominio = $cells[$arrColumns[5]][$i];
        $combustible = $cells[$arrColumns[6]][$i];
        $km = $cells[$arrColumns[7]][$i];
        $color = $cells[$arrColumns[8]][$i];
        $precio = $cells[$arrColumns[9]][$i];
        $origen = $cells[$arrColumns[10]][$i];
        $ubicacion = $cells[$arrColumns[11]][$i];
        $estado = $cells[$arrColumns[12]][$i];
        $idfoto = $cells[$arrColumns[13]][$i];
		echo $idfoto . '<br>';
        // $query = "INSERT INTO listado(fcompra,tipo,marca,modelo,ano,DOMINIO,COMBUSTIBLE,KILOMETROS,COLOR,PRECIO,ORIGEN,UBICACION,ESTADO,IDFOTO) VALUES ('$f_compra','$tipo','$marca','$modelo',$ano,'$dominio','$combustible','$km',
            // '$color',$precio,'$origen','$ubicacion','$estado', $idfoto)";

        // $result = mysql_query($query);
        
}
