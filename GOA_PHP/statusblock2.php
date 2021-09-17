<?php header('Access-Control-Allow-Origin: *');
require 'db_connection.php';
require 'constant.php';

require 'log.php';
?>

<?php

$conn = $db_conn;
if (!$conn) {
  die("connection faild:" . $conn->connect_error);
}
//GOARIG_7002 -del 

// $sql  = "select P1,P2,P3,P4,P5,P6,P7,P10,P13,P14,P16,P17,P20,P21,P22,P23,P24,P25,P27,testdatadate from testdata order by `testdata_id` desc limit $graphLimit";

//GOARIG_7002 - add

$sql  = "SELECT C1,C2,C3,C4,C5,C6,C7,C8,C10,C11,C12,C13,C14,C15,C16,C17,C18,C19,C20,C21,C22,C23,C24,C25,C26 FROM enertek_combuster_goa.received order by testdata_id desc limit 2";


$result  = mysqli_query($conn, $sql);
if (!$result) {
  wh_log("Graph : " . $db_conn->error);
}
$rows  = array();

if (mysqli_num_rows($result) > 0) {
  while ($r  = mysqli_fetch_assoc($result)) {
    array_push($rows, $r);
    # code...
  }

  echo json_encode($rows);
} else {
  echo "no data";
}

mysqli_close($conn);


?>