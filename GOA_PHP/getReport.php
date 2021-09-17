<?php header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'db_connection.php';
require_once dirname(__FILE__) . '/ModbusMasterTCP.php';
?> 
<?php
$conn = $db_conn;

	if(!$conn)
	{	
		die("connection faild:" .$conn-> connect_error);

	}
$data = json_decode(file_get_contents("php://input"));
$turboIdVal = mysqli_real_escape_string($db_conn, trim($data->turboIdVal));
$testno = mysqli_real_escape_string($db_conn, trim($data->testno));
$sql  = "SELECT test_id from enertek_combuster_goa.test where turboconfig_id = '$turboIdVal' and testno = '$testno'";
$result  = mysqli_query($conn,$sql);
$rows  = array();
	if(mysqli_num_rows($result) > 0){
		while ($r  = mysqli_fetch_assoc($result)) {
			array_push($rows, $r);
			# code...
		}
		
		// $turboIdVal = mysqli_real_escape_string($db_conn, trim($rows[0]->test_id));
		$test_id = $rows[0]['test_id'];		

	}
$sql  = "SELECT testdataDate, P6 as RPM,P1 as 'Ambient Pr',P2 as 'Ambient temp',P3 as 'Compressor Inlet Pr',P4 as 'Compressor outlet Pr', P5 as 'Compressor Diff venturi Pr',P7 as 'Compressor Inlet temp', P10 as 'Compressor outlet temp',P13 as 'Combustor outlet temp', P14 as 'Combustor Inlet Pr',P16 as 'Turbine Inlet temp',P17 as 'Turbine outlet temp',P20 as 'Turbine vibration',P21 as 'Fuel flow',P22 as 'Fuel Pr', P23 as 'Oil Pr',P24 as 'Oil flow Rate',P25 as 'Oil Brg Inlet Temp',P27 'Oil Tank Temp'  FROM enertek_combuster_goa.testdata where test_id = '$test_id'";
$result  = mysqli_query($conn,$sql);
$rows  = array();
	if(mysqli_num_rows($result) > 0){
		while ($r  = mysqli_fetch_assoc($result)) {
			array_push($rows, $r);
			# code...
		}
		
		// $turboIdVal = mysqli_real_escape_string($db_conn, trim($rows[0]->test_id));
		print json_encode($rows);		

	}

// print json_encode($turboIdVal);	
?>


