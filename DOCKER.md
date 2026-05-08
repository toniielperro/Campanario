Guía rápida para dockerizar el proyecto "iglesia"

Requisitos:
- Docker y Docker Compose instalados en Windows.

1) Construir y levantar los contenedores

```bash
docker-compose up -d --build
```

2) Instalar dependencias PHP (desde el contenedor `app`)

```bash
docker-compose exec app composer install --no-interaction --prefer-dist
```

3) Generar la llave de la app y configurar .env

- Copia tu `.env` local (o crea uno) y ajusta `DB_HOST=db`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
 - Copia tu `.env` local (o crea uno) y ajusta `DB_HOST=db`, `DB_PORT=3306` (desde el contenedor sigue siendo 3306), `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
  
Nota: Si tu máquina ya usa MySQL en el puerto `3306`, el compose mapea el contenedor a `3307` en el host. No hace falta cambiar `DB_PORT` dentro del contenedor; sólo conecta la aplicación al servicio `db` (DB_HOST=db). Para conectar desde el host (p. ej. MySQL Workbench) usa `localhost:3307`.
- Genera la clave:

```bash
docker-compose exec app php artisan key:generate
```

4) Ejecutar migraciones y seeders (si aplica)

```bash
docker-compose exec app php artisan migrate --seed
```

5) Permisos de storage y cache

```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache || true
```

6) Instalar y compilar assets (opciones)

- Opción A (usar el contenedor `node`):

```bash
# instalar dependencias
docker-compose run --rm node npm install
# compilar (dev/watch/production según tu package.json)
docker-compose run --rm node npm run build
```

- Opción B (en tu máquina anfitrión):

```bash
npm install
npm run build
```

7) Acceder a la aplicación

- Abre el navegador en: http://localhost:8080
 - Abre el navegador en: http://localhost:8081

Nota: Si `localhost:8081` está ocupado en tu máquina, ajusta el puerto en `docker-compose.yml` bajo el servicio `nginx`.

Notas y recomendaciones:
- El servicio `nginx` expone el puerto `8080` y enruta hacia `/public`.
- Variables sensibles deben gestionarse con `.env` y no añadirse al repositorio.
- Si necesitas php extensions adicionales, edita `Dockerfile` y reconstruye con `docker-compose build --no-cache app`.
- Para detener y eliminar contenedores y volúmenes:

```bash
docker-compose down -v
```

Si quieres que cree también un `Makefile` con atajos para estos comandos, lo agrego.
