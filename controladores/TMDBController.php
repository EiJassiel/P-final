<?php
// controladores/TMDBController.php
// Funciones para interactuar con la API de TMDB

class TMDBController {
    private $api_key = 'ea8fe354a8ae6ed97aa6c2ad4a48f1e6';
    private $token = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJlYThmZTM1NGE4YWU2ZWQ5N2FhNmMyYWQ0YTQ4ZjFlNiIsIm5iZiI6MTc1MzQyNTI1Ny43ODgsInN1YiI6IjY4ODMyNTY5MjBlNTFkNjFiNzI0NWNlYSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.GfDZmtwqbst2YLMQCLv3WkF0-DQo0m7dSvoJy-QnEJg';

    public function obtenerDatosTMDB($id_tmdb) {
        $url = "https://api.themoviedb.org/3/movie/$id_tmdb?api_key={$this->api_key}&language=es-ES";
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: Bearer {$this->token}\r\nAccept: application/json\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        $respuesta = @file_get_contents($url, false, $context);
        if ($respuesta === FALSE) {
            error_log("TMDB API error (obtenerDatosTMDB): No se pudo obtener datos para id $id_tmdb");
            return null;
        }
        $datos = json_decode($respuesta, true);
        if (!$datos || !isset($datos['title'])) {
            error_log("TMDB API error (obtenerDatosTMDB): Respuesta vacía o inválida para id $id_tmdb");
            return null;
        }
        return [
            'titulo' => $datos['title'] ?? '',
            'sinopsis' => $datos['overview'] ?? '',
            'imagen' => 'https://image.tmdb.org/t/p/w500' . ($datos['poster_path'] ?? '')
        ];
    }

    public function obtenerRecomendacionesPorGenero($ids_genero) {
    if (empty($ids_genero)) {
        error_log("obtenerRecomendacionesPorGenero: ids_genero vacío");
        return [];
    }
    $genero_str = implode(',', $ids_genero);
    $url = "https://api.themoviedb.org/3/discover/movie?api_key={$this->api_key}&with_genres=$genero_str&language=es-ES&page=1";
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer {$this->token}\r\nAccept: application/json\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);
    if ($response === FALSE) {
        error_log("TMDB API error (obtenerRecomendacionesPorGenero): No se pudo obtener recomendaciones para géneros $genero_str");
        return [];
    }
    $datos = json_decode($response, true);
    if (!$datos || !isset($datos['results'])) {
        error_log("TMDB API error (obtenerRecomendacionesPorGenero): Respuesta vacía o inválida para géneros $genero_str");
        return [];
    }

    // ✅ Limitar a 10 resultados
    return array_slice($datos['results'], 0, 10);
}


    public function obtenerPopulares() {
    $url = "https://api.themoviedb.org/3/movie/popular?api_key={$this->api_key}&language=es-ES";
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer {$this->token}\r\nAccept: application/json\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);

    if ($response === FALSE) {
        error_log("TMDB API error (obtenerPopulares): No se pudo obtener populares");
        return [];
    }

    $datos = json_decode($response, true);
    if (!$datos || !isset($datos['results'])) {
        error_log("TMDB API error (obtenerPopulares): Respuesta vacía o inválida");
        return [];
    }

    // Formatear resultados igual que en obtenerNuevos()
    return array_map(function ($movie) {
        return [
            'id_tmdb' => $movie['id'] ?? null,
            'titulo' => $movie['title'] ?? 'Sin título',
            'sinopsis' => $movie['overview'] ?? '',
            'imagen' => isset($movie['poster_path']) ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'] : null,
            'calificacion' => $movie['vote_average'] ?? 'N/A',
            'fecha_lanzamiento' => $movie['release_date'] ?? 'Fecha no disponible'
        ];
    }, $datos['results']);
}


    public function obtenerNuevos() {
    $url = "https://api.themoviedb.org/3/movie/now_playing?api_key={$this->api_key}&language=es-ES";
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer {$this->token}\r\nAccept: application/json\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);

    if ($response === FALSE) {
        error_log("TMDB API error (obtenerNuevos): No se pudo obtener lanzamientos");
        return [];
    }

    $datos = json_decode($response, true);
    if (!$datos || !isset($datos['results'])) {
        error_log("TMDB API error (obtenerNuevos): Respuesta vacía o inválida");
        return [];
    }

    // Formatear resultados
    return array_map(function ($movie) {
        return [
            'id_tmdb' => $movie['id'] ?? null,
            'titulo' => $movie['title'] ?? 'Sin título',
            'sinopsis' => $movie['overview'] ?? '',
            'imagen' => isset($movie['poster_path']) ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'] : null,
            'calificacion' => $movie['vote_average'] ?? 'N/A',
            'fecha_lanzamiento' => $movie['release_date'] ?? 'Fecha no disponible'
        ];
    }, $datos['results']);
}


    public function obtenerPeliculasFormateadasPorGenero($ids_genero) {
    if (empty($ids_genero)) {
        error_log("obtenerRecomendacionesPorGenero: ids_genero vacío");
        return [];
    }

    $genero_str = implode(',', $ids_genero);
    $url = "https://api.themoviedb.org/3/discover/movie?api_key={$this->api_key}&with_genres=$genero_str&language=es-ES&page=1";

    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer {$this->token}\r\nAccept: application/json\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);

    if ($response === FALSE) {
        error_log("TMDB API error (obtenerRecomendacionesPorGenero): No se pudo obtener recomendaciones para géneros $genero_str");
        return [];
    }

    $datos = json_decode($response, true);
    if (!$datos || !isset($datos['results'])) {
        error_log("TMDB API error (obtenerRecomendacionesPorGenero): Respuesta vacía o inválida para géneros $genero_str");
        return [];
    }

    // 🔁 Formatear resultados
    $peliculas = array_map(function ($movie) {
        return [
            'id' => $movie['id'] ?? '',
            'titulo' => $movie['title'] ?? 'Sin título',
            'sinopsis' => $movie['overview'] ?? '',
            'imagen' => isset($movie['poster_path']) && $movie['poster_path']
                ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path']
                : null,
            'calificacion' => $movie['vote_average'] ?? 'N/A',
            'fecha_lanzamiento' => $movie['release_date'] ?? 'Fecha no disponible',
            'genre_ids' => $movie['genre_ids'] ?? []
        ];
    }, $datos['results']);

    return array_slice($peliculas, 0, 10); // 🔟 máximo
}


// Devuelve la información de una película calificada por el usuario, 
// desde TMDB o desde la base local si fue editada por el admin
public function obtenerPeliculasCalificadasPorUsuario($id_usuario, $conn) {
    // 1. Traemos las calificaciones del usuario
    $stmt = $conn->prepare("SELECT * FROM calificaciones WHERE id_usuario = ? AND tipo = 'pelicula'");
    $stmt->execute([$id_usuario]);
    $calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $resultado = [];

    foreach ($calificaciones as $calif) {
        $id_tmdb = $calif['id_tmdb'];

        // 2. Buscamos si hay override en cache_tmdb para esa peli
        $stmt2 = $conn->prepare("SELECT * FROM cache_tmdb WHERE id_tmdb = ?");
        $stmt2->execute([$id_tmdb]);
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $contenido = json_decode($row['json_data'], true) ?: [];

            // Si hay override, usarlos, si no tomar json_data
            $titulo = $row['override_titulo'] !== null ? $row['override_titulo'] : ($contenido['titulo'] ?? '');
            $sinopsis = $row['override_sinopsis'] !== null ? $row['override_sinopsis'] : ($contenido['sinopsis'] ?? '');
            $imagen = $row['override_imagen'] !== null ? $row['override_imagen'] : ($contenido['imagen'] ?? null);

            $pelicula = [
                'id_tmdb' => $id_tmdb,
                'titulo' => $titulo,
                'sinopsis' => $sinopsis,
                'imagen' => $imagen,
                'puntuacion' => $calif['puntuacion'],
                'comentario' => $calif['comentario'],
                'fuente' => 'local'
            ];
        } else {
            // 3. Si no hay override, obtener datos de TMDB
            $tmdb_data = $this->obtenerDatosTMDB($id_tmdb);
            if ($tmdb_data) {
                $pelicula = array_merge($tmdb_data, [
                    'id_tmdb' => $id_tmdb,
                    'puntuacion' => $calif['puntuacion'],
                    'comentario' => $calif['comentario'],
                    'fuente' => 'tmdb'
                ]);
            } else {
                // Si ni en cache ni en TMDB, no agregar
                continue;
            }
        }

        $resultado[] = $pelicula;
    }

    return $resultado;
}

}
