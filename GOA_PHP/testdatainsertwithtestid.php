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
$data = json_decode(file_get_contents("php://input"));
if (isset($data->status)) {
  $status = mysqli_real_escape_string($db_conn, trim($data->status));




  // print json_encode($status);
  if (!$conn) {
    die("connection faild:" . $conn->connect_error);
  }
  $sql  = "SELECT test_id FROM `test` order by `test_id` desc limit 1";
  $result  = mysqli_query($conn, $sql);
  if (!$result) {
    wh_log("Data Insert : " . $db_conn->error);
  }
  $rows  = array();

  if (mysqli_num_rows($result) > 0) {
    while ($r  = mysqli_fetch_assoc($result)) {
      array_push($rows, $r);
      # code...
    }

    if ($status == "Start initiated") {
      $test_id = $rows[0]['test_id'];
    } else if ($status == "Statusblock loading") {
      $test_id = 0;
    }
    wh_log("Start (PLC TO DB) : Started");
  }
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

    $initiate = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, 0, 1));
    $nshutdowncompleted = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, 51, 1));
    $eshutdown = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, 74, 1));

    $insertData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testdata`(`test_id`,`P1`,`P2`,`P3`,`P4`,`P5`,`P6`,`P7`,`P10`,`P13`,`P14`,`P16`,`P17`,`P20`,`P21`,`P22`,`P23`,`P24`,`P25`,`P27`,`P28`,`testdataDate`,`Date`)VALUES('$test_id','$P1','$P2','$P3','$P4','$P5','$P6','$P7','$P10','$P13','$P14','$P16','$P17','$P20','$P21','$P22','$P23','$P24','$P25','$P27','$P28',now(),now())");

    if ($nshutdowncompleted == 1) {
      $modbus->disconnect();
      break;
    }

    if ($eshutdown == 1) {
      $modbus->disconnect();
      break;
    }
    sleep($data_access_time);
  }
}
