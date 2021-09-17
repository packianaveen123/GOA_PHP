
<?php header('Access-Control-Allow-Origin: *');
require 'db_connection.php';
require 'constant.php';

require 'log.php';
?>

<?php
$conn = $db_conn;

	if(!$conn)
	{
		die("connection faild:" .$conn-> connect_error);

	}

	$sql  = "SELECT dataacesstime FROM enertek_combuster_goa.configuration";
	$result  = mysqli_query($conn,$sql);

	$rows  = array();

	if(mysqli_num_rows($result) > 0){
		while ($r  = mysqli_fetch_assoc($result)) {
			array_push($rows, $r);
			# code...
		}

		print json_encode($rows[0]['dataacesstime']*1000);
		// $data_access_time = $rows[0]['dataacesstime'];

	}

	mysqli_close($conn);


?>