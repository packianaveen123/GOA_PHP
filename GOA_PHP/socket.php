<?php

$Socket_create = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($Socket_create, '192.168.0.13', '502');
socket_close($Socket_create);
