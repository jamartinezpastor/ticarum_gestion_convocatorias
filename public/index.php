<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) require $file;
});

session_start();

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AlumnoController;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);

// Alumnos (CRUD)
$router->get   ('/alumnos',                [AlumnoController::class, 'index']);
$router->get   ('/alumnos/crear',          [AlumnoController::class, 'create']);
$router->post  ('/alumnos',                [AlumnoController::class, 'store']);
$router->get   ('/alumnos/{id}/editar',    [AlumnoController::class, 'edit']);
$router->put   ('/alumnos/{id}',           [AlumnoController::class, 'update']);
$router->delete('/alumnos/{id}',           [AlumnoController::class, 'destroy']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);