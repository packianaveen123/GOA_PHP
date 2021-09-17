<?php header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods : POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require 'db_connection.php';
require 'constant.php';
require_once dirname(__FILE__) . '/ModbusMasterTCP.php';
require 'log.php';
?>
<?php
$modbus = new ModbusMaster("192.168.0.120", "TCP");
$Db_delay = 5;
$modbus->connect();
$conn = $db_conn;
function hex2float($strHex)
{
  $hex = sscanf($strHex, "%02x%02x%02x%02x%02x%02x%02x%02x");
  $bin = implode('', array_map('chr', $hex));
  $array = unpack("Gnum", $bin);
  return $array['num'];
}

while (1) {

  $sensorData = $modbus->readMultipleRegisters(0, 10, 50);
  $sensorDataReal = $modbus->readMultipleRegisters(0, 7, 45);
  $sensorDataReal2 = $modbus->readMultipleRegisters(0, 12, 45);
  $Temp = array_chunk($sensorData, 2);
  $P2 = PhpType::bytes2signedInt($Temp[0]);
  $P5 = PhpType::bytes2signedInt($Temp[3]);
  $P6 = PhpType::bytes2signedInt($Temp[4]);
  $P7 = PhpType::bytes2signedInt($Temp[5]);
  $P10 = PhpType::bytes2signedInt($Temp[8]);
  $P13 = PhpType::bytes2signedInt($Temp[11]);
  $P16 = PhpType::bytes2signedInt($Temp[14]);
  $P17 = PhpType::bytes2signedInt($Temp[15]);
  $P25 = PhpType::bytes2signedInt($Temp[34]);
  $P27 = PhpType::bytes2signedInt($Temp[25]);
  $P28 = PhpType::bytes2signedInt($Temp[35]);

  $pr = array_chunk($sensorDataReal, 4);

  $b = $pr[0];

  $a = dechex($b[0]);
  for ($x = 1; $x < count($b); $x++) {
    $a = $a . dechex($b[$x]);
  }
  $float = hex2float($a);

  $P3 =  round($float, 1);
  $b = $pr[1];

  $a = dechex($b[0]);
  for ($x = 1; $x < count($b); $x++) {
    $a = $a . dechex($b[$x]);
  }
  $float = hex2float($a);

  $P1 =  round($float, 1);

  $b = $pr[16];

  $a = dechex($b[0]);
  for ($x = 1; $x < count($b); $x++) {
    $a = $a . dechex($b[$x]);
  }
  $float = hex2float($a);

  $P21 =  round($float, 1);
  $b = $pr[15];

  $a = dechex($b[0]);
  for ($x = 1; $x < count($b); $x++) {
    $a = $a . dechex($b[$x]);
  }
  $float = hex2float($a);

  $P23 =  round($float, 1);
  $b = $pr[8];

  $a = dechex($b[0]);
  for ($x = 1; $x < count($b); $x++) {
    $a = $a . dechex($b[$x]);
  }
  $float = hex2float($a);

  $P14 =  round($float, 1);

  $pr1 = array_chunk($sensorDataReal2, 4);
  $b = $pr1[0];

  $a = dechex($b[0]);
  for ($x = 1; $x < count($b); $x++) {
    $a = $a . dechex($b[$x]);
  }
  $float = hex2float($a);

  $P4 =  round($float, 1);

  $b = $pr1[8];

  $a = dechex($b[0]);
  for ($x = 1; $x < count($b); $x++) {
    $a = $a . dechex($b[$x]);
  }
  $float = hex2float($a);

  $P20 =  round($float, 1);
  $b = $pr1[9];

  $a = dechex($b[0]);
  for ($x = 1; $x < count($b); $x++) {
    $a = $a . dechex($b[$x]);
  }
  $float = hex2float($a);

  $P22 =  round($float, 1);
  $b = $pr1[10];

  $a = dechex($b[0]);
  for ($x = 1; $x < count($b); $x++) {
    $a = $a . dechex($b[$x]);
  }
  $float = hex2float($a);

  $P24 =  round($float, 1);


  $logout = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, 41, 1));
  $insertData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testdata`(`test_id`,`P1`,`P2`,`P3`,`P4`,`P5`,`P6`,`P7`,`P10`,`P13`,`P14`,`P16`,`P17`,`P20`,`P21`,`P22`,`P23`,`P24`,`P25`,`P27`,`P28`,`testdataDate`,`Date`)VALUES(0,'$P1','$P2','$P3','$P4','$P5','$P6','$P7','$P10','$P13','$P14','$P16','$P17','$P20','$P21','$P22','$P23','$P24','$P25','$P27','$P28',now(),now())");


  if ($logout == 1) {
    $modbus->disconnect();
    break;
  }
  sleep($data_access_time);
}
