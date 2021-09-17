<?php header('Access-Control-Allow-Origin: *');
require 'db_connection.php';
// include 'constant.php';
require_once dirname(__FILE__) . '/ModbusMasterTCP.php';


require 'log.php';
?>


<?php
error_reporting(0);

$conn = $db_conn;

	if(!$conn)
	{
		die("connection faild:" .$conn-> connect_error);

	}
$modbus = new ModbusMaster("192.168.0.10", "TCP");
$Db_delay = 5;
$modbus->connect();
print json_encode($modbus);
$data = array(1);
$modbus->writeMultipleRegister(0, 41, $data, "INT");
// print json_encode($InitializeCompleted);
$modbus->disconnect();

?>

