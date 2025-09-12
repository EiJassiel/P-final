<?php
class authController {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

public function validarLogin($correo, $password) {
    if (empty($correo) || empty($password)) {
        return ['status' => 'error', 'msg' => 'Correo y contraseña requeridos'];
    }

    $stmt = $this->db->prepare("SELECT id_usuario, nombre, contrasena_hash, rol FROM usuarios WHERE correo = :correo");
    $stmt->execute(['correo' => $correo]);

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $row['contrasena_hash'])) {
            return [
                'status' => 'ok',
                'tipo' => ($row['rol'] === 'admin') ? 'admin' : 'usuario',
                'id' => $row['id_usuario'],
                'nombre' => $row['nombre'],
                'correo' => $correo
                ];
        }
    }
    return ['status' => 'error', 'msg' => 'Credenciales incorrectas'];
}


    public function register($correo, $usuario, $password) {
        if (empty($correo) || empty($usuario) || empty($password)) {
            return [
                'success' => false,
                'msg' => 'Todos los campos son obligatorios'
            ];
        }

        // Validar si el usuario ya existe
        $stmt = $this->db->prepare("SELECT id_usuario FROM usuarios WHERE correo = :correo OR nombre = :nombre");
        $stmt->execute(['correo' => $correo, 'nombre' => $usuario]);

        if ($stmt->rowCount() > 0) {
            return [
                'success' => false,
                'msg' => 'El usuario o correo ya existe'
            ];
        }

        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, contrasena_hash, correo, rol, fecha_registro) VALUES (:nombre, :hash, :correo, 'usuario', NOW())");
            $success = $stmt->execute([
                'nombre' => $usuario,
                'hash' => $hash,
                'correo' => $correo
            ]);
        } catch (PDOException $e) {
            return [
                'success' => false,
                'msg' => 'Error SQL: ' . $e->getMessage()
            ];
        }

        if ($success) {
            return [
                'success' => true,
                'msg' => 'Usuario registrado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'msg' => 'Error al registrar el usuario'
            ];
        }
    }
}
?>
