<?php
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');
$log = false;
$ignore_list[] = "";

if ($log) {
ob_start();
}
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
$dsn = ""
    . "host=ec2-54-243-239-159.compute-1.amazonaws.com "
    . "dbname=d2qjrin3hr4b8b "
    . "user=wbjkjbrpjaxwit "
    . "port=5432 "
    . "sslmode=require "
    . "password=HI63Hu08lOA6LbWHHlw5ABmieD";
$db = pg_connect($dsn);
if (!$db) {
  echo "An error occurred.\n";
  exit;
}

$filename = "puente.xls";
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
$id_in_file = "(";
$id_in_db;
/*CHECK NEW MODELS TO UPLOAD*/
$result = pg_query($db, "SELECT a.id_img as id_img, m.name as marca, mod.name as modelo, a.kilometers, a.color, a.version as version, a.year as ano, a.price as price, a.id FROM autos a INNER JOIN marcas m ON m.id = a.marca_id INNER JOIN modelos mod ON mod.id = a.modelo_id WHERE a.id_img IS NOT NULL ORDER BY a.id_img");
if (!$result) {
  echo "An error occurred.\n";
  exit;
}
$i = 0;
while ($row = pg_fetch_assoc($result)) {
  $id_in_db[] = $row['id_img'];
  $data_in_db[$i]['marca'] = $row['marca'];
  $data_in_db[$i]['modelo'] = $row['modelo'];
  $data_in_db[$i]['version'] = $row['version'];
  $data_in_db[$i]['ano'] = $row['ano'];
  $data_in_db[$i]['precio'] = $row['price'];
  $data_in_db[$i]['km'] = $row['kilometers'];
  if ($row['kilometers'] == 0)
	$data_in_db[$i]['km'] = "0km";
  $data_in_db[$i]['color'] = $row['color'];
  $data_in_db[$i]['id'] = $row['id'];
  $i++;
}
$count_new_cars = 0;
$modified_car = "";
$new_cars = "";
for ($i = 2; $i <= $countRows; $i++) {
		$idfoto =  $cells[$arrColumns[13]][$i];
		if (in_array($idfoto,$ignore_list))
			continue;
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
    
		$idx = array_search($idfoto, $id_in_db);
	
		if (!in_array($idfoto,$id_in_db)){
			$new_cars .= " $count_new_cars - $marca $modelo $ano ". $km ."km $$precio.-  ---- *** $idfoto<br>";
			$count_new_cars++;

		}else{
			$aux = $modified_car;
		    if ($km != "" && $km != $data_in_db[$idx]['km'] && $km != '0 Km')
				$modified_car .= "Changed KM from " . $data_in_db[$idx]['km'] ." to $km <br>";
			if ($precio != $data_in_db[$idx]['precio'])
				$modified_car .= "Changed Price from " . $data_in_db[$idx]['precio'] ." to $precio <br>";
			if ($color != $data_in_db[$idx]['color'])
				$modified_car .= "Changed Color from " . $data_in_db[$idx]['color'] ." to $color <br>";
			if ($aux != $modified_car)
				$modified_car .= "<b> Changes on: $marca $modelo $ano - ID: $idfoto </b><br><br> "; 
		}
		if ($i!=2){
			$id_in_file .= ",";
		}
		$id_in_file .= $idfoto;
              
}
$id_in_file .= ")";

echo "New Cars in Listing: <br>";
echo $new_cars;
echo "<br>Modified Cars in Listing: <br>";
echo $modified_car;

/*END NEW MODELS TO UPLOAD*/

/*CHECK MODELS TO DELETE*/
$result = pg_query($db, "SELECT  m.name, mod.name, a.version, a.year, a.price, a.id FROM autos a INNER JOIN marcas m ON m.id = a.marca_id INNER JOIN modelos mod ON mod.id = a.modelo_id WHERE a.id_img NOT IN $id_in_file ORDER BY m.name, mod.name");

if (!$result) {
  echo "An error occurred.\n";
  exit;
}
echo "Cars to Delete: <br>" ;
while ($row = pg_fetch_row($result)) {
  echo "$row[0] $row[1] $row[2] $row[3] - $$row[4] ---- $row[5]";
  echo "<br />\n";
}

/*END MODELS TO DELETE*/

/* CORRUPTED MODELS */

$result = pg_query($db, "SELECT  m.name, mod.name, a.version, a.year, a.price, a.id FROM autos a INNER JOIN marcas m ON m.id = a.marca_id INNER JOIN modelos mod ON mod.id = a.modelo_id WHERE a.id_img IS NULL ORDER BY m.name, mod.name");

if (!$result) {
  echo "An error occurred.\n";
  exit;
}
echo "<br><br> Cars Corrupted: <br>" ;
while ($row = pg_fetch_row($result)) {
  echo "$row[0] $row[1] $row[2] $row[3] - $$row[4] ---- $row[5]";
  echo "<br />\n";
}

/*END CORRUPTED MODELS*/


/*LOG TO A FILE*/
if ($log) {
	$content = '<html>' . ob_get_clean() . '</html>';
	file_put_contents('stdout.html', $content);
}
