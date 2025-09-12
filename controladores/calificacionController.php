<?php

class CalificacionController
{
    private $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function guardarCalificacion($usuario_id, $id_tmdb, $puntuacion, $comentario = null)
{
    if (!$usuario_id || !$id_tmdb || !$puntuacion) {
        return false;
    }

    $tipo = 'pelicula'; // fijo

    // Validar y limpiar comentario
    if ($comentario !== null) {
        $comentario = trim($comentario);
        $comentario = strip_tags($comentario); // quitar etiquetas HTML
        if (strlen($comentario) > 500) {
            $comentario = substr($comentario, 0, 500);
        }
    }

    try {
        $stmt = $this->db->prepare("
            INSERT INTO calificaciones (id_usuario, id_tmdb, tipo, puntuacion, comentario, fecha)
            VALUES (:usuario_id, :id_tmdb, :tipo, :puntuacion, :comentario, NOW())
            ON DUPLICATE KEY UPDATE 
            puntuacion = :puntuacion, 
            comentario = :comentario,
            fecha = NOW()
        ");

        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':id_tmdb' => $id_tmdb,
            ':tipo' => $tipo,
            ':puntuacion' => $puntuacion,
            ':comentario' => $comentario
        ]);
    } catch (PDOException $e) {
        return false;
    }
}




    public function obtenerCalificacion($usuario_id, $id_tmdb)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT puntuacion FROM calificaciones
                WHERE id_usuario = :usuario_id AND id_tmdb = :id_tmdb
                LIMIT 1
            ");
            $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':id_tmdb' => $id_tmdb
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? intval($row['puntuacion']) : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function eliminarCalificacion($usuario_id, $id_tmdb)
{
    try {
        $stmt = $this->db->prepare("
            DELETE FROM calificaciones
            WHERE id_usuario = :usuario_id AND id_tmdb = :id_tmdb
        ");

        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':id_tmdb' => $id_tmdb
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

}
