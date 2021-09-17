<?php header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'db_connection.php';
require_once dirname(__FILE__) . '/ModbusMasterTCP.php';
?>


<?php
$data = array(3);
$modbus = new ModbusMaster("192.168.0.9", "TCP");
$modbus->connect();
$modbus->writeMultipleRegister(0, 0, $data, "INT");
$modbus->disconnect();
print json_encode($modbus);
?>
