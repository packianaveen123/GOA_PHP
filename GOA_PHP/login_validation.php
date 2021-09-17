<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once dirname(__FILE__) . '/ModbusMasterTCP.php';
require 'db_connection.php';

//set zero in login bit in PLC 
$modbus = new ModbusMaster("192.168.0.120", "TCP");
$Db_delay = 5;
$modbus->connect();
$data = array(0);
$modbus->writeMultipleRegister(0, 41, $data, "INT");
$modbus->writeMultipleRegister(0, 42, $data, "INT");

$modbus->disconnect();
require 'log.php';
// POST DATA
$data = json_decode(file_get_contents("php://input"));
$sql1  = "DELETE FROM enertek_combuster_goa.testdata WHERE test_id = 0";
$result1  = mysqli_query($conn,$sql1);
if(isset($data->user_name) 
	&& isset($data->password) 
	&& !empty(trim($data->user_name)) 
	&& !empty(trim($data->password))
	){
    $username = mysqli_real_escape_string($db_conn, trim($data->user_name));
    $password = mysqli_real_escape_string($db_conn, trim($data->password));
    $en_password = md5($password);
        $insertUser = mysqli_query($db_conn,"select username from user where email = '$username' and pwd = '$en_password'");
        if(!$insertUser){
            wh_log("LoginPage : " . $db_conn -> error);
        }
        $count = mysqli_num_rows($insertUser);  
        $rows  = array();
        if(mysqli_num_rows($insertUser) > 0){
        while ($r  = mysqli_fetch_assoc($insertUser)) {
            array_push($rows, $r);
            # code...
        }

        

    }
        if($count == 1){    
            //$last_id = mysqli_insert_id($db_conn);
            echo json_encode(["success",$rows[0]['username']]);
            // print json_encode($rows);
            
           
        }
        else{
            echo json_encode(["failed"]);
        }
    
   
    
}
else{
    echo json_encode(["success"=>0,"msg"=>"Please fill all the required fields!"]);
}