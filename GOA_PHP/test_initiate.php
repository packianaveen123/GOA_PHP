<?php

require_once dirname(__FILE__) . '/ModbusMasterTCP.php';


$modbus = new ModbusMaster("192.168.0.120", "TCP");

$modbus->connect();
