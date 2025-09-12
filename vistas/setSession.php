<?php
session_start();
$_SESSION['user'] = [
    'id' => $_POST['id'],
    'nombre' => $_POST['nombre'],
    'correo' => $_POST['correo'],
    'tipo' => $_POST['tipo']
];
echo 'ok';
