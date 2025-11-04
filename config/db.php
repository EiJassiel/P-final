<?php
/*
 * Configuración de la conexión a la base de datos.
 * Se intentan leer las variables de entorno (usadas por docker-compose). Si no existen,
 * se usan valores por defecto compatibles con una instalación local.
 */

$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'secret';
$db = getenv('DB_NAME') ?: 'mi_base';

try {
    $PDO = new PDO("mysql:host={$host};dbname={$db};charset=utf8", $user, $password);
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error en la conexion de la bd: ' . $e->getMessage());
}
?>