
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

$sql  = "SELECT P6,P4,P23,P25,C13,C18,P3,C2,P16 FROM enertek_combuster_goa.received order by testdata_id desc limit 1";
$result  = mysqli_query($conn, $sql);

$rows  = array();

if (mysqli_num_rows($result) > 0) {
  while ($r  = mysqli_fetch_assoc($result)) {
    array_push($rows, $r);
    # code...
  }

  print json_encode($rows);
} else {
  echo "no data";
}

mysqli_close($conn);


?>