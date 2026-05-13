<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Convocatoria;

final class ConvocatoriaController extends Controller
{
    public function index(): void
    {
        $page   = (int) ($_GET['page'] ?? 1);
        $result = Convocatoria::paginate($page, 7);

        $this->render('convocatorias/index', [
            'title'         => 'Convocatorias',
            'convocatorias' => $result['items'],
            'pagination'    => $result,
        ]);
    }

    public function show(string $id): void
    {
        $convocatoria = Convocatoria::find((int) $id);
        if (!$convocatoria) { $this->notFound('Convocatoria no encontrada.'); return; }

        $this->render('convocatorias/show', [
            'title' => $convocatoria['asignatura'],
            'convocatoria' => $convocatoria,
            'inscritos' => Convocatoria::inscritos((int) $id),
            # noInscritos por disponiblesParaFecha
            'alumnosDisponibles' => \App\Models\Alumno::disponiblesParaFecha($convocatoria['fecha']),        ]);
    }

    public function create(): void
    {
        $this->render('convocatorias/form', [
            'title' => 'Nueva convocatoria',
            'convocatoria' => null,
            'old' => [],
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        $errors = Convocatoria::validate($_POST);
        if ($errors) {
            http_response_code(422);
            $this->render('convocatorias/form', [
                'title' => 'Nueva convocatoria',
                'convocatoria' => null,
                'old' => $_POST,
                'errors' => $errors,
            ]);
            return;
        }

        $id = Convocatoria::create($this->cleanInput());
        $this->flash('success', 'Convocatoria creada correctamente.');
        $this->redirect('/convocatorias/' . $id);
    }

    public function edit(string $id): void
    {
        $convocatoria = Convocatoria::find((int) $id);
        if (!$convocatoria) { $this->notFound('Convocatoria no encontrada.'); return; }

        $this->render('convocatorias/form', [
            'title' => 'Editar convocatoria',
            'convocatoria' => $convocatoria,
            'old' => $convocatoria,
            'errors' => [],
        ]);
    }

    public function update(string $id): void
    {
        $convocatoria = Convocatoria::find((int) $id);
        if (!$convocatoria) { $this->notFound('Convocatoria no encontrada.'); return; }

        $errors = Convocatoria::validate($_POST, $convocatoria);
        if ($errors) {
            http_response_code(422);
            $this->render('convocatorias/form', [
                'title' => 'Editar convocatoria',
                'convocatoria' => $convocatoria,
                'old' => $_POST,
                'errors' => $errors,
            ]);
            return;
        }

        Convocatoria::update((int) $id, $this->cleanInput());
        $this->flash('success', 'Convocatoria actualizada correctamente.');
        $this->redirect('/convocatorias/' . $id);
    }

    public function destroy(string $id): void
    {
        $convocatoria = Convocatoria::find((int) $id);
        if (!$convocatoria) { $this->notFound('Convocatoria no encontrada.'); return; }

        if (Convocatoria::hasInscripciones((int) $id)) {
            $this->flash('error',
                "No se puede eliminar la convocatoria de {$convocatoria['asignatura']}: tiene alumnos inscritos."
            );
            $this->redirect('/convocatorias');
            return;
        }

        Convocatoria::delete((int) $id);
        $this->flash('success', 'Convocatoria eliminada correctamente.');
        $this->redirect('/convocatorias');
    }

    private function cleanInput(): array
    {
        return [
            'asignatura'     => trim($_POST['asignatura']     ?? ''),
            'fecha'          => trim($_POST['fecha']          ?? ''),
            'aula'           => trim($_POST['aula']           ?? ''),
            'plazas_maximas' => (int) ($_POST['plazas_maximas'] ?? 0),
        ];
    }
}