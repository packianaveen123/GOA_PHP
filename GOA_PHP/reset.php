<?php header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'db_connection.php';
require_once dirname(__FILE__) . '/ModbusMasterTCP.php';
require 'log.php';
?>


<?php
$data = json_decode(file_get_contents("php://input"));
$ResetTemp = mysqli_real_escape_string($db_conn, trim($data->ResetTemp));
$ResetRPM = mysqli_real_escape_string($db_conn, trim($data->ResetRPM));
// +*print json_encode($data);		
$data = array($ResetTemp, $ResetRPM);
$modbus = new ModbusMaster("192.168.0.120", "TCP");
$modbus->connect();
$modbus->writeMultipleRegister(0, 4, $data, "INT");
$data3 = $ResetRPM;
$ar = unpack("C*", pack("L", $data3));
$modbus->writeMultipleRegister(0, 90, $ar, "INT");
$modbus->disconnect();
$conn = $db_conn;

if (!$conn) {
  die("connection faild:" . $conn->connect_error);
}
$count = 1;
$sql  = "SELECT test_id from enertek_combuster_goa.test  order by test_id desc limit 1";
$result  = mysqli_query($conn, $sql);
if (!$result) {
  wh_log("Reset : " . $db_conn->error);
}
$rows  = array();

if (mysqli_num_rows($result) > 0) {
  while ($r  = mysqli_fetch_assoc($result)) {
    array_push($rows, $r);
  }
  $test_id = $rows[0]['test_id'];
  wh_log("Reset : Started");
}
$inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`value`) VALUES('$test_id','Reset Values','C10','C',CURRENT_TIME(),'$ResetTemp, $ResetRPM')");

?>

