<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include_once '../controladores/authController.php';
include_once '../controladores/TMDBController.php';
include_once '../controladores/PreferenciasController.php';
include_once '../controladores/calificacionController.php';



/// SE MEJORA ESTE FRMATO DE API PARALOGRAR UNA MAYOR Y MEJOR INTEGRACION CON LAS BUENAS PRACTICAS DE PROGRAMACION

// Reutilizar la configuración centralizada en config/db.php
require_once __DIR__ . '/../config/db.php';
// `config/db.php` crea la variable `$PDO` o termina la ejecución en caso de error.
if (!isset($PDO) || !$PDO instanceof PDO) {
    error_log('PDO ERROR: no se pudo inicializar $PDO desde config/db.php');
    echo json_encode(['status' => 'error', 'msg' => 'Error de conexión PDO: configuración inválida']);
    exit;
}
$conexion = $PDO;


$auth = new authController($conexion);
$preferenciasController = new PreferenciasController($conexion);
$action = $_POST['action'] ?? '';
$tmdb = new TMDBController();




switch ($action) {

    case 'listar_cache':
    // 1. Obtener todo el cache local
    $stmt = $conexion->prepare("SELECT * FROM cache_tmdb ORDER BY fecha_cache DESC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cache = [];
    foreach ($rows as $row) {
        $contenido = json_decode($row['json_data'], true);
        $cache[$row['id_tmdb']] = [
            'id_tmdb' => $row['id_tmdb'],
            'tipo' => $row['tipo'],
            'estado' => $row['estado'],
            'titulo' => $row['override_titulo'] ?: ($contenido['titulo'] ?? ''),
            'sinopsis' => $row['override_sinopsis'] ?: ($contenido['sinopsis'] ?? ''),
            'imagen' => $row['override_imagen'] ?: ($contenido['imagen'] ?? ''),
            'fecha_cache' => $row['fecha_cache'],
            'fuente' => 'cache'
        ];
    }

    // 2. Obtener populares y nuevos de TMDB
    $populares = $tmdb->obtenerPopulares();
    $nuevos = $tmdb->obtenerNuevos();
    $tmdb_contenido = array_merge($populares, $nuevos);

    $result = [];
    foreach ($tmdb_contenido as $item) {
        // Ahora usamos id_tmdb en lugar de id
        $id_tmdb = $item['id_tmdb'] ?? null;
        if (!$id_tmdb) {continue;}

        if (isset($cache[$id_tmdb])) {
            // Si está en cache, usamos los datos locales
            $result[] = $cache[$id_tmdb];
        } else {
            // Si no está en cache, usamos los datos directos de TMDB
            $result[] = [
                'id_tmdb' => $id_tmdb,
                'tipo' => 'pelicula',
                'estado' => 'activo',
                // Usar las claves correctas que devuelve TMDBController
                'titulo' => $item['titulo'] ?? '',
                'sinopsis' => $item['sinopsis'] ?? '',
                'imagen' => $item['imagen'] ?? null,
                'fecha_cache' => null,
                'fuente' => 'tmdb'
            ];
        }
    }

    // 3. Agregar los cacheados que no están en TMDB
    foreach ($cache as $id => $item) {
        if (!in_array($id, array_column($result, 'id_tmdb'))) {
            $result[] = $item;
        }
    }

    echo json_encode(['status' => 'ok', 'contenido' => $result]);
    break;


        case 'login':
    session_start();

    if (!isset($_SESSION['login_intentos'])) {
        $_SESSION['login_intentos'] = 0;
    }
    if (!isset($_SESSION['bloqueo_inicio'])) {
        $_SESSION['bloqueo_inicio'] = null;
    }

    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';

    $tiempoBloqueo = 60; // segundos

    // Si hay bloqueo activo, comprobar si expiró
    if ($_SESSION['bloqueo_inicio'] !== null) {
        $tiempoTranscurrido = time() - $_SESSION['bloqueo_inicio'];

        if ($tiempoTranscurrido < $tiempoBloqueo) {
            // Aumentar intentos incluso estando bloqueado
            $_SESSION['login_intentos']++;

            if ($_SESSION['login_intentos'] >= 100) {
                echo json_encode(['status' => 'castigo']);
                break;
            }

            echo json_encode(['status' => 'lock']);
            break;
        } else {
            // Bloqueo expiró, limpiar
            $_SESSION['bloqueo_inicio'] = null;
        }
    }

    // Validamos login (sin bloqueo activo)
    $userData = $auth->validarLogin($correo, $password);

    if (isset($userData['status']) && $userData['status'] === 'ok') {
        // Login correcto
        $_SESSION['login_intentos'] = 0;
        $_SESSION['bloqueo_inicio'] = null;
        echo json_encode($userData);
    } else {
        // Login incorrecto
        $_SESSION['login_intentos']++;

        if ($_SESSION['login_intentos'] >= 10) {
            echo json_encode(['status' => 'castigo']);
        } elseif ($_SESSION['login_intentos'] >= 5) {
            if ($_SESSION['bloqueo_inicio'] === null) {
                $_SESSION['bloqueo_inicio'] = time();
            }
            echo json_encode(['status' => 'lock']);
        } else {
            echo json_encode(['status' => 'fail']);
        }
    }
    break;







    case 'registro':
        $usuario = $_POST['usuario'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $password = $_POST['password'] ?? '';
        $result = $auth->register($correo, $usuario, $password);
        echo json_encode($result);
        break;
 case 'explorar_genero':
    $id_genero = $_POST['id_genero'] ?? '';
    if ($id_genero) {
        $peliculas = $tmdb->obtenerPeliculasFormateadasPorGenero([$id_genero]);
        echo json_encode([
            'status' => 'ok',
            'debug' => [
                'id_genero' => $id_genero,
                'total' => count($peliculas)
            ],
            'peliculas' => $peliculas
        ]);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'ID de género no especificado', 'peliculas' => []]);
    }
    break;

    case 'guardar_cache':
                $id_tmdb = $_POST['id_tmdb'] ?? '';
                $tipo = $_POST['tipo'] ?? '';
                $estado = $_POST['estado'] ?? 'activo';
                $titulo = $_POST['titulo'] ?? null;
                $sinopsis = $_POST['sinopsis'] ?? null;
                $imagen = $_POST['imagen'] ?? null;

                if ($estado === 'activo' && (is_null($titulo) || is_null($sinopsis) || is_null($imagen))) {
                    $datos_tmdb = (new TMDBController())->obtenerDatosTMDB($id_tmdb);
                    if ($datos_tmdb) {
                        if (is_null($titulo)){   $titulo = $datos_tmdb['titulo'];}
                        if (is_null($sinopsis)){ $sinopsis = $datos_tmdb['sinopsis'];}
                        if (is_null($imagen))   {$imagen = $datos_tmdb['imagen'];}
    }
}

$json_data = json_encode([
    'titulo' => $titulo,
    'sinopsis' => $sinopsis,
    'imagen' => $imagen
]);

$stmt = $conexion->prepare("REPLACE INTO cache_tmdb (id_tmdb, tipo, json_data, fecha_cache, estado, override_titulo, override_sinopsis, override_imagen) VALUES (:id_tmdb, :tipo, :json_data, NOW(), :estado, :override_titulo, :override_sinopsis, :override_imagen)");
$success = $stmt->execute([
    'id_tmdb' => $id_tmdb,
    'tipo' => $tipo,
    'json_data' => $json_data,
    'estado' => $estado,
    'override_titulo' => null,
    'override_sinopsis' => null,
    'override_imagen' => null
]);

echo $success ?
    json_encode(['status' => 'ok', 'msg' => 'Contenido almacenado']) :
    json_encode(['status' => 'error', 'msg' => 'No se pudo guardar']);

        break;
        
    case 'consultar_cache':
        $id_tmdb = $_POST['id_tmdb'] ?? '';
        $stmt = $conexion->prepare("SELECT * FROM cache_tmdb WHERE id_tmdb = :id_tmdb");
        $stmt->execute(['id_tmdb' => $id_tmdb]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $tmdb_data = $tmdb->obtenerDatosTMDB($id_tmdb);
            echo $tmdb_data ?
                json_encode(['status' => 'original', 'contenido' => $tmdb_data]) :
                json_encode(['status' => 'error', 'msg' => 'No se pudo obtener datos de TMDB']);
            break;
        }

        if (isset($row['estado']) && $row['estado'] === 'inactivo') {
            echo json_encode(['status' => 'inactivo', 'msg' => 'Contenido ocultado por el admin']);
            break;
        }

        // Si hay override, usarlo; si no, usar json_data
        $contenido = json_decode($row['json_data'], true);
        if (!empty($row['override_titulo'])) {$contenido['titulo'] = $row['override_titulo'];}
        if (!empty($row['override_sinopsis'])){$contenido['sinopsis'] = $row['override_sinopsis'];}
        if (!empty($row['override_imagen'])) {$contenido['imagen'] = $row['override_imagen'];}

        echo json_encode([
            'status' => 'override',
            'contenido' => [
                'titulo' => $contenido['titulo'] ?? '',
                'sinopsis' => $contenido['sinopsis'] ?? '',
                'imagen' => $contenido['imagen'] ?? ''
            ]
        ]);
        break;

    case 'guardar_preferencias':
        $usuario_id = $_POST['usuario_id'] ?? '';
        $generosJson = $_POST['generos'] ?? '[]'; // <-- NO decodifiques aquí
        $ok = $preferenciasController->guardarPreferencias($usuario_id, $generosJson);
        echo $ok ?
            json_encode(['status' => 'ok', 'msg' => 'Preferencias guardadas']) :
            json_encode(['status' => 'error', 'msg' => 'No se pudieron guardar']);
        break;

    case 'verificar_preferencias':
        $usuario_id = $_POST['usuario_id'] ?? '';
        $stmt = $conexion->prepare("SELECT generos FROM preferencias_usuario WHERE usuario_id = :usuario_id");
        $stmt->execute(['usuario_id' => $usuario_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        echo (!$row || empty($row['generos'])) ?
            json_encode(['status' => 'no_preferencias']) :
            json_encode(['status' => 'preferencias_ok']);
        break;

  case 'generos':
        $json_path = __DIR__ . '/../WebServices/generos.json';
        if (file_exists($json_path)) {
            $json = file_get_contents($json_path);
            $generos = json_decode($json, true);
            echo json_encode(['status' => 'ok', 'generos' => $generos]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'No se encontró el archivo de géneros']);
        }
        break;

 case 'get_preferencias':
    $usuario_id = $_POST['usuario_id'] ?? '';

    if (empty($usuario_id)) {
        echo json_encode(['status' => 'error', 'msg' => 'ID de usuario no proporcionado']);
        break;
    }

    $generos = $preferenciasController->obtenerPreferencias($usuario_id);

    if (empty($generos)) {
        echo json_encode(['status' => 'sin_preferencias', 'generos' => []]);
    } else {
        echo json_encode(['status' => 'ok', 'generos' => $generos]);
    }
    break;

    case 'obtener_recomendados':
        $usuario_id = $_POST['usuario_id'] ?? '';
        $generos_elegidos = $preferenciasController->obtenerPreferencias($usuario_id); // Esto ya devuelve [28,35,18]

        // $generos_elegidos ya es un array de IDs TMDB
        $peliculas = $tmdb->obtenerRecomendacionesPorGenero($generos_elegidos);

        if (empty($generos_elegidos)) {
            echo json_encode(['status' => 'sin_preferencias', 'msg' => 'No tienes preferencias guardadas', 'peliculas' => []]);
        } elseif (empty($peliculas)) {
            echo json_encode(['status' => 'sin_resultados', 'msg' => 'No se encontraron recomendaciones para tus géneros', 'peliculas' => []]);
        } else {
            echo json_encode(['status' => 'ok', 'peliculas' => $peliculas]);
        }
        break;



   case 'populares':
    $resultados = $tmdb->obtenerPopulares();
    $ids = array_column($resultados, 'id_tmdb');  // Cambiado aquí
    $peliculas = [];
    if ($ids) {
        $in = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $conexion->prepare("SELECT * FROM cache_tmdb WHERE id_tmdb IN ($in)");
        $stmt->execute($ids);
        $cache = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $cache[$row['id_tmdb']] = $row;
        }
    } else {
        $cache = [];
    }
    foreach ($resultados as $movie) {
        define('FECHA_NO_DISPONIBLE', 'Fecha no disponible');
        define('ETIQUETA_LANZAMIENTO', 'Fecha de lanzamiento');
        define('SIN_TITULO','Sin titulo');
        $id = $movie['id_tmdb'];  // Cambiado aquí
        if (isset($cache[$id])) {
            $row = $cache[$id];
            if (isset($row['estado']) && $row['estado'] === 'inactivo'){continue;}
            $contenido = json_decode($row['json_data'], true);
            $peliculas[] = [
                'titulo' => $row['override_titulo'] ?: ($contenido['titulo'] ?? $movie['titulo'] ?? SIN_TITULO),
                'sinopsis' => $row['override_sinopsis'] ?: ($contenido['sinopsis'] ?? $movie['sinopsis'] ?? ''),
                'imagen' => $row['override_imagen'] ?: ($contenido['imagen'] ?? $movie['imagen']),
                'calificacion' => $movie['calificacion'] ?? 'N/A',
                ETIQUETA_LANZMIENTO => $movie[ETIQUETA_LANZMIENTO] ?? FECHA_NO_DISPONIBLE
            ];
        } else {
            $peliculas[] = [
                'titulo' => $movie['titulo'] ?? SIN_TITULO,
                'sinopsis' => $movie['sinopsis'] ?? '',
                'imagen' => $movie['imagen'] ?? null,
                'calificacion' => $movie['calificacion'] ?? 'N/A',
                ETIQUETA_LANZMIENTO => $movie[ETIQUETA_LANZMIENTO] ?? FECHA_NO_DISPONIBLE
            ];
        }
    }
    echo json_encode(['status' => 'ok', 'peliculas' => $peliculas]);
    break;


case 'nuevos':
    $resultados = $tmdb->obtenerNuevos();
    $ids = array_column($resultados, 'id_tmdb');
    $peliculas = [];

    if ($ids) {
        $in = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $conexion->prepare("SELECT * FROM cache_tmdb WHERE id_tmdb IN ($in)");
        $stmt->execute($ids);
        $cache = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $cache[$row['id_tmdb']] = $row;
        }
    } else {
        $cache = [];
    }

    foreach ($resultados as $movie) {
        $id = $movie['id_tmdb'];
        if (isset($cache[$id])) {
            $row = $cache[$id];
            if (isset($row['estado']) && $row['estado'] === 'inactivo') {continue;}
            $contenido = json_decode($row['json_data'], true);
            $peliculas[] = [
                'id_tmdb' => $id,
                'titulo' => $row['override_titulo'] ?: ($contenido['titulo'] ?? $movie['titulo'] ?? SIN_TITULO),
                'sinopsis' => $row['override_sinopsis'] ?: ($contenido['sinopsis'] ?? $movie['sinopsis'] ?? ''),
                'imagen' => $row['override_imagen'] ?: ($contenido['imagen'] ?? $movie['imagen']),
                'calificacion' => $movie['calificacion'] ?? 'N/A',
                ETIQUETA_LANZMIENTO => $movie[ETIQUETA_LANZMIENTO] ?? 'Fecha no disponible'
            ];
        } else {
            $peliculas[] = $movie; // Ya viene formateado desde el controller
        }
    }

    echo json_encode(['status' => 'ok', 'peliculas' => $peliculas]);
    break;



            case 'guardar_calificacion':
        $usuario_id = $_POST['usuario_id'] ?? '';
        $id_tmdb = $_POST['id_tmdb'] ?? '';
        $puntuacion = $_POST['puntuacion'] ?? '';
        $comentario = $_POST['comentario'] ?? ''; // <-- añade esto

        $califController = new CalificacionController($conexion);
        $ok = $califController->guardarCalificacion($usuario_id, $id_tmdb, $puntuacion, $comentario); // <-- pásalo aquí

        echo json_encode([
            'status' => $ok ? 'ok' : 'error',
            'msg' => $ok ? 'Calificación guardada' : 'No se pudo guardar'
        ]);
    break;


    case 'obtener_calificacion':
        $usuario_id = $_POST['usuario_id'] ?? '';
        $id_tmdb = $_POST['id_tmdb'] ?? '';

        $califController = new CalificacionController($conexion);
        $valor = $califController->obtenerCalificacion($usuario_id, $id_tmdb);

        echo json_encode([
            'status' => 'ok',
            'calificacion' => $valor
        ]);
        break;

    case 'peliculas_calificadas':
    $usuario_id = $_POST['usuario_id'] ?? '';

    if (!$usuario_id) {
        echo json_encode(['status' => 'error', 'msg' => 'ID de usuario faltante']);
        break;
    }

    $tmdb = new TMDBController();

    $peliculas = $tmdb->obtenerPeliculasCalificadasPorUsuario($usuario_id, $conexion);

    // Debug para ver qué se obtuvo realmente
    error_log("Películas encontradas para usuario $usuario_id: " . count($peliculas));
    error_log(print_r($peliculas, true));

    echo json_encode(['status' => 'ok', 'peliculas' => $peliculas]);
    exit; // Para que no siga ejecutando código después
    break;

    case 'eliminar_calificacion':
    $usuario_id = $_POST['usuario_id'] ?? '';
    $id_tmdb = $_POST['id_tmdb'] ?? '';

    if (empty($usuario_id) || empty($id_tmdb)) {
        echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos']);
        break;
    }

    $califController = new CalificacionController($conexion);
    $ok = $califController->eliminarCalificacion($usuario_id, $id_tmdb);

    echo json_encode([
        'status' => $ok ? 'ok' : 'error',
        'msg' => $ok ? 'Calificación eliminada' : 'No se pudo eliminar la calificación'
    ]);
    break;

default:
    echo json_encode(['status' => 'error', 'msg' => 'Acción no válida']);
    break;
    


}
