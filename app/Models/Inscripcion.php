<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Inscripcion
{
    /**
     * Ejecuta las 4 validaciones de una transacción con SELECT ... FOR UPDATE
     * . o 2 peticiones simultáneas vs la última plaza.
     */
    public static function crear(int $alumnoId, int $convocatoriaId): int
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            // Bloquea la fila de la convocatoria hasta COMMIT/ROLLBACK.
            $stmt = $pdo->prepare(
                'SELECT id, asignatura, fecha, plazas_maximas, plazas_ocupadas
                   FROM convocatorias
                  WHERE id = ?
                  FOR UPDATE'
            );
            $stmt->execute([$convocatoriaId]);
            $conv = $stmt->fetch();
            if (!$conv) {
                throw new InscripcionException('not_found_convocatoria', 'Convocatoria no encontrada.', 404);
            }

            $stmt = $pdo->prepare('SELECT id FROM alumnos WHERE id = ?');
            $stmt->execute([$alumnoId]);
            if (!$stmt->fetchColumn()) {
                throw new InscripcionException('not_found_alumno', 'Alumno no encontrado.', 404);
            }

            // Validación 1
            if ($conv['fecha'] < date('Y-m-d')) {
                throw new InscripcionException('date_passed', 'La fecha de la convocatoria ya ha pasado.', 409);
            }

            // Validación 2
            if ((int) $conv['plazas_ocupadas'] >= (int) $conv['plazas_maximas']) {
                throw new InscripcionException('capacity_full', 'La convocatoria no tiene plazas disponibles.', 409);
            }

            // Validación 3
            $stmt = $pdo->prepare(
                'SELECT 1 FROM inscripciones WHERE alumno_id = ? AND convocatoria_id = ?'
            );
            $stmt->execute([$alumnoId, $convocatoriaId]);
            if ($stmt->fetchColumn()) {
                throw new InscripcionException('already_inscribed', 'El alumno ya está inscrito en esta convocatoria.', 409);
            }

            // Validación 4
            $stmt = $pdo->prepare(
                'SELECT c.asignatura
                   FROM inscripciones i
                   JOIN convocatorias c ON c.id = i.convocatoria_id
                  WHERE i.alumno_id = ? AND c.fecha = ?'
            );
            $stmt->execute([$alumnoId, $conv['fecha']]);
            $sameDay = $stmt->fetch();
            if ($sameDay) {
                throw new InscripcionException(
                    'same_day_conflict',
                    "El alumno ya tiene una inscripción ese día ({$sameDay['asignatura']}).",
                    409
                );
            }

            // Inserta. El trigger AFTER INSERT actualiza a +1 plazas_ocupadas.
            $stmt = $pdo->prepare(
                'INSERT INTO inscripciones (alumno_id, convocatoria_id) VALUES (?, ?)'
            );
            $stmt->execute([$alumnoId, $convocatoriaId]);
            $id = (int) $pdo->lastInsertId();

            $pdo->commit();
            return $id;

        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM inscripciones WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function eliminar(int $id): void
    {
        // El trigger AFTER DELETE disminuye en -1 las plazas_ocupadas automáticamente.
        $stmt = Database::pdo()->prepare('DELETE FROM inscripciones WHERE id = ?');
        $stmt->execute([$id]);
    }
}