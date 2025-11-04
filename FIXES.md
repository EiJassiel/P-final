Fixes aplicados

Cambios realizados para resolver errores reportados:

- `config/db.php`
  - Ahora lee las variables de entorno `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME` (con valores por defecto adecuados para Docker: `db`, `root`, `secret`, `mi_base`).
  - Usa un único punto de configuración `$PDO` para todas las conexiones.

- `index.php`
  - Eliminada la línea en blanco inicial para evitar salida antes de `header()` (evita "headers already sent").

- `vistas/login.php`
  - Eliminada la línea en blanco inicial para evitar salida antes de `session_start()`.

- `Api/apiRest.php`
  - Eliminada la conexión PDO hardcodeada a `localhost` y `watchitodb`.
  - Ahora incluye `config/db.php` y reutiliza `$PDO` para evitar errores de socket ("No such file or directory") cuando la app corre en Docker.

Resultado

- El error "Cannot modify header information" y el warning de `session_start()` se resolvieron al eliminar salidas antes de PHP en `index.php` y `vistas/login.php`.
- El error PDO "SQLSTATE[HY000] [2002] No such file or directory" fue causado por una conexión que intentaba usar socket local (host `localhost`) desde dentro del contenedor; al centralizar la configuración y usar `DB_HOST=db` se solucionó.

Pruebas realizadas

- `curl -I http://localhost:8080` -> 302 / redirección correcta
- `curl -sS -X POST "http://localhost:8080/Api/apiRest.php" -d "action=listar_cache"` -> respuesta JSON OK
- Se verificó la base de datos con `docker-compose exec db mysql -u root -psecret -e "SHOW DATABASES;"` y `SHOW TABLES;`.

Siguientes pasos recomendados

- Escanear el repositorio por otras conexiones hardcodeadas a `localhost` o por archivos PHP con BOM/espacios iniciales (puedo hacerlo).
- Documentar en el README notas sobre variables de entorno y cómo levantar el proyecto.
