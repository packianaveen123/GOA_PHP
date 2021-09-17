
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

$sql  = "SELECT * FROM enertek_combuster_goa.comparision_table";
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