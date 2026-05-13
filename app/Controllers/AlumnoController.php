<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Alumno;

final class AlumnoController extends Controller
{
    public function index(): void
    {
        $page   = (int) ($_GET['page'] ?? 1);
        $result = Alumno::paginateWithConvocatorias($page, 7);

        $this->render('alumnos/index', [
            'title'      => 'Alumnos',
            'alumnos'    => $result['items'],
            'pagination' => $result,
        ]);
    }

    public function create(): void
    {
        $this->render('alumnos/form', [
            'title' => 'Nuevo alumno',
            'alumno' => null,
            'old' => [],
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        $errors = Alumno::validate($_POST);
        if ($errors) {
            http_response_code(422);
            $this->render('alumnos/form', [
                'title' => 'Nuevo alumno',
                'alumno' => null,
                'old' => $_POST,
                'errors' => $errors,
            ]);
            return;
        }

        Alumno::create($this->cleanInput());
        $this->flash('success', 'Alumno creado correctamente.');
        $this->redirect('/alumnos');
    }

    public function edit(string $id): void
    {
        $alumno = Alumno::find((int) $id);
        if (!$alumno) { $this->notFound('Alumno no encontrado.'); return; }

        $this->render('alumnos/form', [
            'title' => 'Editar alumno',
            'alumno' => $alumno,
            'old' => $alumno,
            'errors' => [],
        ]);
    }

    public function update(string $id): void
    {
        $alumno = Alumno::find((int) $id);
        if (!$alumno) { $this->notFound('Alumno no encontrado.'); return; }

        # No se re-dirige, se re-renderizam el formulario con HTTP 422 con los datos 'old' (lo que tecleó el usuario))
        $errors = Alumno::validate($_POST, (int) $id);
        if ($errors) {
            http_response_code(422);
            $this->render('alumnos/form', [
                'title' => 'Editar alumno',
                'alumno' => $alumno,
                'old' => $_POST,
                # 'errors' => $errors,
            ]);
            return;
        }

        Alumno::update((int) $id, $this->cleanInput());
        $this->flash('success', 'Alumno actualizado correctamente.');
        $this->redirect('/alumnos');
    }

    public function destroy(string $id): void
    {
        $alumno = Alumno::find((int) $id);
        if (!$alumno) { $this->notFound('Alumno no encontrado.');return; }

        if (Alumno::hasInscripciones((int) $id)) {
            $this->flash('error',
                "No se puede eliminar a {$alumno['nombre']} {$alumno['apellidos']}: tiene inscripciones."
            );
            $this->redirect('/alumnos');
            return;
        }

        Alumno::delete((int) $id);
        $this->flash('success', 'Alumno eliminado correctamente.');
        $this->redirect('/alumnos');
    }

    private function cleanInput(): array
    {
        return [
            'nombre'    => trim($_POST['nombre']    ?? ''),
            'apellidos' => trim($_POST['apellidos'] ?? ''),
            'email'     => trim($_POST['email']     ?? ''),
            'dni'       => strtoupper(trim($_POST['dni'] ?? '')),
        ];
    }
}