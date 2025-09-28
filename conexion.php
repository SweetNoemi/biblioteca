<?php
// conexion.php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'biblioteca';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    die("Conexión fallida: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
?>
