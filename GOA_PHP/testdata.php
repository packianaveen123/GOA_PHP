
<?php header('Access-Control-Allow-Origin: *');
require 'db_connection.php';
require 'constant.php';
require_once dirname(__FILE__) . '/ModbusMasterTCP.php';
require 'log.php';
?>
<?php

$modbus = new ModbusMaster();
$Db_delay = 5;
$modbus->connect();
$conn = $db_conn;

if (!$conn) {
  die("connection faild:" . $conn->connect_error);
}
$count = 1;
$Count1 = 1;
$sql  = "SELECT test_id from enertek_combuster_goa.test  order by test_id desc limit 1";
$result  = mysqli_query($conn, $sql);
if (!$result) {
  wh_log("Test Data : " . $db_conn->error);
}
$rows  = array();

if (mysqli_num_rows($result) > 0) {
  while ($r  = mysqli_fetch_assoc($result)) {
    array_push($rows, $r);
  }
  $test_id = $rows[0]['test_id'];
  wh_log("Initialize (PLC to Code) : Started");
}

while (1) {

  $initiatecompleted = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $InitateCompletedc, 1));
  $startcompleted = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $StartCompletedc, 1));
  $nshutdown = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, 0, 1));
  $nshutdowncompleted = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $NShutdownCompletedc, 1));
  $ignite = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $Ignitec, 1));
  $gasopened = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $GasOpenedc, 1));
  $stage1 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $Stage1c, 1));
  $fuelopened = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $FuelOpenedc, 1));
  $stage2 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $Stage2c, 1));
  $gasclosed = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $GasClosedc, 1));
  $stage3 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $Stage3c, 1));
  $eshutdown = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $EShutdownc, 1));
  $eshutdownC = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, 93, 1));
  $SV1 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $flame, 1));
  $SV2 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $compr_ACV, 1));
  $SV3 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $ASCV, 1));
  $SV4 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $bypass, 1));
  $SV5 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $kerosine_pump, 1));
  $SV6 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $lube_oil_pump, 1));
  $SV7 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $cooling_pump, 1));
  $SV8 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $keronsine_FF, 1));
  $SV9 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $Air_injector_SV, 1));
  $SV10 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $PilotFlameAir, 1));
  $SV11 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, $accetline_gas, 1));
  $SV12 = PhpType::bytes2signedInt($modbus->readMultipleRegisters(0, 42, 1));
  $valves = [$SV1, $SV2, $SV3, $SV4, $SV5, $SV6, $SV7, $SV8, $SV9, $SV10, $SV11, $SV12];


  $valves = implode(',', $valves);
  if ($initiatecompleted == 1 && $count == 1) {
    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','Initialize Completed','S1','s',CURRENT_TIME(),'$valves')");
    $count++;
  }
  if ($startcompleted == 1 && $count == 2) {
    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','Start Completed','S2','s',CURRENT_TIME(),'$valves')");
    $count++;
  }
  if ($ignite == 1 && $count == 3) {
    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','Ignite','S3','s',CURRENT_TIME(),'$valves')");
    $count++;
  }
  if ($gasopened == 1 && $count == 4) {
    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','Gas Opened','S4','s',CURRENT_TIME(),'$valves')");
    $count++;
  }
  if ($stage1 == 1 && $count == 5) {
    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','Stage1','S5','s',CURRENT_TIME(),'$valves')");
    $count++;
  }
  if ($fuelopened == 1 && $count == 6) {
    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','Fuel Opened','S6','s',CURRENT_TIME(),'$valves')");
    $count++;
  }
  if ($stage2 == 1 && $count == 7) {
    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','Stage2','S7','s',CURRENT_TIME(),'$valves')");
    $count++;
  }
  if ($gasclosed == 1 && $count == 8) {
    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','Gas Closed','S8','s',CURRENT_TIME(),'$valves')");
    $count++;
  }
  if ($stage3 == 1 && $count == 9) {
    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','Stage3','S9','s',CURRENT_TIME(),'$valves')");
    $count++;
  }
  if ($nshutdown == 3 && $Count1 == 1) {
    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','N.Shutdown Initiated','S10','s',CURRENT_TIME(),'$valves')");
    $Count1++;
  }
  if ($nshutdowncompleted == 1) {

    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','N.Shutdown Completed','S10','s',CURRENT_TIME(),'$valves')");
    $modbus->disconnect();
    break;
  }


  if ($eshutdown == 1 && $Count1 == 1) {

    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','E.Shutdown Initiated','S11','s',CURRENT_TIME(),'$valves')");
    $Count1++;
  }
  if ($eshutdownC == 1) {

    $inserttestData = mysqli_query($conn, "INSERT INTO `enertek_combuster_goa`.`testcommands`(`test_id`,`name`,`index`,`type`,`testcommandsTime`,`valvestatus`) VALUES('$test_id','E.Shutdown Completed','S11','s',CURRENT_TIME(),'$valves')");

    $modbus->disconnect();
    break;
  }
  if ($logFileConstant == 2) {
    $DATA = strval($initiatecompleted) . ',' . strval($startcompleted) . ',' . strval($nshutdowncompleted) . ',' . strval($ignite) . ',' . strval($gasopened) . ',' . strval($stage1) . ',' . strval($fuelopened) . ',' . strval($stage2) . ',' . strval($gasclosed) . ',' . strval($stage3) . ',' . strval($eshutdown) . ',' . strval($SV1) . ',' . strval($SV2) . ',' . strval($SV3) . ',' . strval($SV4) . ',' . strval($SV5) . ',' . strval($SV6) . ',' . strval($SV7) . ',' . strval($SV8) . ',' . strval($SV9) . ',' . strval($SV10);

    wh_log("Command And Valve Status :" . $DATA);
  }
  sleep($data_access_time);
}

?>
