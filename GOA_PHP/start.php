<?php header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'db_connection.php';
require 'log.php';
require_once dirname(__FILE__) . '/ModbusMasterTCP.php';
?> 
<?php

$modbus = new ModbusMaster("192.168.0.120", "TCP");
$modbus->connect();
$data = json_decode(file_get_contents("php://input"));
$TargetRPM = mysqli_real_escape_string($db_conn, trim($data->targetRPM));
$TargetTemp = mysqli_real_escape_string($db_conn, trim($data->targetTemp));

$conn = $db_conn;

if (!$conn) {
  die("connection faild:" . $conn->connect_error);
}
$sql  = "SELECT * FROM `testparamconfig`";
$result  = mysqli_query($conn, $sql);
if (!$result) {
  wh_log("Start : " . $db_conn->error);
}
$rows  = array();

if (mysqli_num_rows($result) > 0) {
  while ($r  = mysqli_fetch_assoc($result)) {
    array_push($rows, $r);
    # code...
  }
  $stage1Temp = $rows[0]['testparamvalue'];
  $stage2Temp = $rows[1]['testparamvalue'];
  $stage1RPM = $rows[2]['testparamvalue'];
}
$sql1  = "SELECT test_id from enertek_combuster_goa.test  order by test_id desc limit 1";
$result1  = mysqli_query($conn, $sql1);
$rows1 = array();

if (mysqli_num_rows($result1) > 0) {
  while ($r  = mysqli_fetch_assoc($result)) {
    array_push($rows1, $r);
    # code...
  }
  $test_id = $rows1[0]['test_id'];
}
//dont forget to give no of blades
$data = array($stage1Temp, $stage2Temp, $stage1RPM, $TargetTemp, $TargetRPM);

$modbus->writeMultipleRegister(0, 1, $data, "INT");
$data1 = array(2);
$modbus->writeMultipleRegister(0, 0, $data1, "INT");
$data3 = $TargetRPM;
$data2 = array(1);
$modbus->writeMultipleRegister(0, 10, $data2, "INT");


$StartCompleted = $modbus->readMultipleRegisters(1, 0, 1);
print json_encode($StartCompleted[1]);

// $modbus->connect();
// $modbus->writeMultipleRegister(0, 3, $data, "INT");
// $modbus->writeMultipleRegister(0, 4, $data, "INT");
// $modbus->writeMultipleRegister(0, 5, $data, "INT");
$modbus->disconnect();
// $TargetRPM = $data->targetRPM

// $modbus = new ModbusMaster("192.168.0.120", "TCP");
// $Db_delay = 5; 
// $modbus->connect();
// print json_encode($TargetRPM);
$inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','Reset Values','S12','s',CURRENT_TIME(),'11')");

$inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`value`,`testcommandsTime`)
VALUES($test_id,'stage1temp','C3','C','$stage1Temp',now())");

$inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`value`,`testcommandsTime`)
VALUES($test_id,'stage2temp','C4','C','$stage2Temp',now())");
$inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`value`,`testcommandsTime`)
VALUES($test_id,'stage3rpm','C5','C','$stage1RPM',now())");
$inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`value`,`testcommandsTime`)
VALUES($test_id,'targettemp','C6','C','$TargetTemp',now())");

$inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`value`,`testcommandsTime`)
VALUES($test_id,'targetrpm','C7','C','$TargetRPM',now())");
$inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`value`,`testcommandsTime`)
VALUES($test_id,'numofblades','C8','C','',now())");
$inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`value`,`testcommandsTime`)
VALUES($test_id,'Start Initiated','C9','C','1',now())");




?>


