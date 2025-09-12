<?php
// TemaController.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tema'])) {
    $tema = $_POST['tema'] === 'oscuro' ? 'oscuro' : 'claro';

    // Seteamos la cookie por 30 días xd
    setcookie('tema', $tema, time() + (86400 * 30), "/");

    echo json_encode(['success' => true, 'tema' => $tema]);
    exit();
}

echo json_encode(['success' => false]);
