
<?php header('Access-Control-Allow-Origin: *');
require 'db_connection.php';
require 'log.php';
?>

<?php



	$conn = $db_conn;

	if(!$conn)
	{
		die("connection faild:" .$conn-> connect_error);

	}

	// $sql  = "SELECT turboconfig_id,turboname,installeddate,nozzlearea,numofblades,description,status FROM enertek_combuster_goa.turboconfig where status != 'Completed' order by  turboconfig_id desc";
  $sql  = "SELECT turboconfig_id,turboname,installeddate,nozzlearea,numofblades,description,status FROM enertek_combuster_goa.turboconfig order by  turboconfig_id desc";
	$result  = mysqli_query($conn,$sql);
	if(!$result){
            wh_log("Turboconfig : " . $db_conn -> error);
        }
	$rows  = array();

	if(mysqli_num_rows($result) > 0){
		while ($r  = mysqli_fetch_assoc($result)) {
			array_push($rows, $r);
			# code...
		}
		
		print json_encode($rows);

	}

	else
	{
		print json_encode($rows);

	}

	mysqli_close($conn);


?>