<?php
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'watchitodb';

try {
    $PDO = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('Error en la conexion de la bd: ' . $e->getMessage());
}
?>