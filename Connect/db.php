<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';     
$user = 'root';          
$pass = '';             
$db   = 'bookingbordgame'; 
$port = 3306;            

$mysqli = new mysqli($host, $user, $pass, $db, $port);
$mysqli->set_charset('utf8mb4');