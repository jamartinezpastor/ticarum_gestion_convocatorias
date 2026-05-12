<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Inscripcion;
use App\Models\InscripcionException;

final class InscripcionController extends Controller
{
    public function store(string $convocatoriaId): void
    {
        // Para API REST/OpenAPI: $payload['alumno_id' en vez de $_POST['alumno_id']
        $payload  = json_decode(file_get_contents('php://input'), true) ?? [];
        $alumnoId = (int) ($payload['alumno_id'] ?? 0);

        if ($alumnoId <= 0) {
            $this->json([
                'error' => 'missing_alumno_id',
                'message' => 'Falta el campo alumno_id.',
            ], 400);
        }

        try {
            $id = Inscripcion::crear($alumnoId, (int)$convocatoriaId);
            $this->flash('success', 'Alumno inscrito correctamente.');
            $this->json(['inscripcion_id' => $id], 201);
        } catch (InscripcionException $e) {
            $this->json([
                'error' => $e->code,
                'message' => $e->getMessage(),
            ], $e->httpStatus);
        }
    }

    public function destroy(string $id): void
    {
        if (!Inscripcion::find((int)$id)) {
            $this->json([
                'error' => 'not_found',
                'message' => 'Inscripción no encontrada.',
            ], 404);
        }

        Inscripcion::eliminar((int)$id);
        $this->flash('success', 'Inscripción eliminada correctamente.');
        $this->noContent();
    }
}