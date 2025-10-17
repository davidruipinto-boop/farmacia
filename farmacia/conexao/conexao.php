<?php
$host = "localhost";   
$user = "root";        
$pass = "ccna";             
$db   = "si_farmacia";     

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na ligação: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
