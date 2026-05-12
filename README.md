# TICARUM — Gestión de Convocatorias

Aplicación web para gestionar convocatorias de examen e inscripciones de alumnos. Desarrollada en **PHP 8.2+ nativo, sin frameworks**, con arquitectura MVC clásica.

## Requisitos

- PHP **8.2** o superior (probado con 8.3)
- MariaDB 10.5+ o MySQL 8.0+
- Cualquier instalación tipo XAMPP, Laragon, MAMP, o stack manual con `php-cli` y `php-mysql`

No requiere Composer ni dependencias adicionales. Swagger UI se carga desde CDN.

## Instalación

### 1. Importar la base de datos

```bash
mysql -u root -p < sql/script_inicializacion_bd.sql
```

O desde phpMyAdmin: Importar ---> seleccionar `sql/script_inicializacion_bd.sql ---> Continuar.

El script crea la base de datos `ticarum_convocatorias`, sus 3 tablas, los triggers, la vista y datos de prueba que cubren todos los casos de validación (convocatoria llena, fecha pasada, conflicto de mismo día, alumnos con y sin inscripciones).

### 2. Configurar la conexión

Copia `config/config.example.php` a `config/config.php` y ajusta los valores según tu entorno:

```php
return [
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'ticarum',
        'user' => 'root',
        'pass' => '',
    ],
];
```

### 3. Arrancar el servidor

Desde la raíz del proyecto:

```bash
php -S localhost:8000 -t public
```

`-t public` define `public/` como la única carpeta accesible vía web. El resto quedan fuera del documentRoot.

## URLs

| URL | Descripción |
|---|---|
| `http://localhost:8000/` | Inicio (verifica conexión con la BD) |
| `http://localhost:8000/alumnos` | Gestión de alumnos |
| `http://localhost:8000/convocatorias` | Gestión de convocatorias |
| `http://localhost:8000/docs/` | **Documentación de la API (Swagger UI)** |
| `http://localhost:8000/openapi.yaml` | Especificación OpenAPI 3.0 en bruto |

## Estructura del proyecto

- **`app/`** — código de la aplicación.
    - **`Controllers/`** — `HomeController`, `AlumnoController`, `ConvocatoriaController`, `InscripcionController`.
    - **`Core/`** — `Router`, `Database` (PDO singleton), `Controller` base.
    - **`Models/`** — `Alumno`, `Convocatoria`, `Inscripcion`, `InscripcionException`.
    - **`Views/`** — `layout.php` + carpetas por recurso (`alumnos/`, `convocatorias/`).
- **`config/`** — `config.example.php` (sí va al repo) y `config.php` (en `.gitignore`, credenciales reales).
    - **`index.php`** — front controller + autoloader PSR-4 + rutas.
    - **`assets/`** — `css/app.css` y `js/app.js`.
    - **`openapi.yaml`** — especificación de la API.
    - **`docs/`** — Swagger UI estático.
- **`sql/schema.sql`** — esquema completo (tablas, triggers, vista) + datos de prueba.
- **`.gitignore`**, **`README.md`**, **`DECISIONES.md`** — raíz del proyecto.


## Resumen de funcionalidades

- **Alumnos**: CRUD completo. Validación de DNI español con módulo 23 de la letra de control. Unicidad de email y DNI. Bloqueo de borrado si hay inscripciones. Al clicar en un DNI del listado se abre un modal con las convocatorias en las que el alumno está inscrito.
- **Convocatorias**: CRUD completo. El listado muestra estado calculado (`abierta` / `completa` / `finalizada`). El detalle incluye los alumnos inscritos y permite alta/baja de inscripciones desde un combo buscable.
- **Inscripciones**: AJAX con JSON puro (REST). Las 4 validaciones del enunciado se aplican dentro de una **transacción SQL con `SELECT ... FOR UPDATE`** sobre la convocatoria.
- **API documentada**: OpenAPI 3.0 con Swagger UI interactivo en `/docs/`.

## Documentación de decisiones técnicas

Ver `DECISIONES.md` para la justificación detallada del diseño: modelo de datos, MVC vs Hexagonal, gestión de transacciones, alternativas descartadas, etc.