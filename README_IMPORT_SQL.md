README - Importar SQL al contenedor MySQL

Este archivo documenta el procedimiento que usé para importar `sql/watchitodb (3).sql` dentro
del contenedor MySQL definido en `docker-compose.yml`.

Resumen:
- Servicio DB: `db` (contenedor `mysql_db`, imagen `mysql:8.0`)
- Base de datos objetivo: `mi_base`
- Root password: `secret` (según `docker-compose.yml`)

Comandos (ejecutar desde la raíz del proyecto en Windows/cmd.exe):

# 1) Copiar el archivo SQL al contenedor
docker cp "C:\Users\HP EliteBook 840 G4\Downloads\CineApp\sql\watchitodb (3).sql" mysql_db:/tmp/watchitodb.sql

# 2) Ejecutar la importación dentro del contenedor
docker exec -i mysql_db sh -c "mysql -u root -psecret mi_base < /tmp/watchitodb.sql"

# 3) Verificar (listar tablas)
docker exec -i mysql_db sh -c "mysql -u root -psecret -e \"SHOW TABLES;\" mi_base"

Notas y variaciones:
- Si usas PowerShell puedes hacer una tubería en lugar de docker cp:
  Get-Content -Raw "sql\watchitodb (3).sql" | docker exec -i mysql_db mysql -u root -psecret mi_base
- Si tu contenedor no se llama `mysql_db`, usa el nombre real o `docker-compose exec db ...`.
- La contraseña en línea produce una advertencia de seguridad por parte de mysql; considera usar archivos de configuración seguros si es para producción.

Estado final: el archivo fue copiado al contenedor e importado. Se listaron tablas y la importación parece exitosa.
