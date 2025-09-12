<?php
class PreferenciasController {
    private $PDO;
    public function __construct($PDO) {
        $this->PDO = $PDO;
    }

    public function obtenerPreferencias($usuario_id) {
        $stmt = $this->PDO->prepare("SELECT generos FROM preferencias_usuario WHERE usuario_id = :usuario_id");
        $stmt->execute(['usuario_id' => $usuario_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['generos'])) {
            return json_decode($row['generos'], true);
        }
        return [];
    }
    public function guardarPreferencias($usuario_id, $generosJson) {
        $stmt = $this->PDO->prepare(
            "REPLACE INTO preferencias_usuario (usuario_id, generos) VALUES (:usuario_id, :generos)"
        );
        $stmt->execute([
            'usuario_id' => $usuario_id,
            'generos' => $generosJson
        ]);
        return true;
    }
}
