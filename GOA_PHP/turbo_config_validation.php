<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'db_connection.php';
require 'log.php';
// POST DATA
$data = json_decode(file_get_contents("php://input"));

$conn = $db_conn;

if (!$conn) {
  die("connection faild:" . $conn->connect_error);
}
$turbo_id = mysqli_real_escape_string($db_conn, trim($data->turbo_id));
$date = mysqli_real_escape_string($db_conn, trim($data->date));
$nozzle_area = mysqli_real_escape_string($db_conn, trim($data->nozzle_area));
$description = mysqli_real_escape_string($db_conn, trim($data->descriptions));
$noofblades = mysqli_real_escape_string($db_conn, trim($data->noofblades));

$insertUser = mysqli_query($db_conn, "INSERT INTO `enertek_combuster_goa`.`turboconfig`(`turboname`,`installeddate`,`nozzlearea`,`numofblades`,`description`,`status`,`completiondate`)VALUES('$turbo_id','$date','$nozzle_area','$noofblades', '$description','installed',now())");

// $count = mysqli_num_rows($insertUser);  
if ($insertUser) {

  // $sql  = "SELECT turboconfig_id,turboname,installeddate,nozzlearea,numofblades,description,status FROM enertek_combuster_goa.turboconfig where status != 'Completed' order by  turboconfig_id desc";
  $sql  = "SELECT turboconfig_id,turboname,installeddate,nozzlearea,numofblades,description,status FROM enertek_combuster_goa.turboconfig  order by  turboconfig_id desc";
  $result  = mysqli_query($conn, $sql);
  if (!$result) {
    wh_log("Trubine Insert : " . $db_conn->error);
  }
  $rows  = array();

  if (mysqli_num_rows($result) > 0) {
    while ($r  = mysqli_fetch_assoc($result)) {
      array_push($rows, $r);
      # code...
    }

    print json_encode($rows);
  }
} else {

  print json_encode("no_data");
}
