<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Convocatoria
{
    public static function all(): array
    {
        return Database::pdo()
            ->query('SELECT * FROM v_convocatorias ORDER BY fecha DESC, asignatura')
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM v_convocatorias WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO convocatorias (asignatura, fecha, aula, plazas_maximas) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$data['asignatura'], $data['fecha'], $data['aula'], $data['plazas_maximas']]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE convocatorias SET asignatura = ?, fecha = ?, aula = ?, plazas_maximas = ? WHERE id = ?'
        );
        $stmt->execute([$data['asignatura'], $data['fecha'], $data['aula'], $data['plazas_maximas'], $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM convocatorias WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function hasInscripciones(int $id): bool
    {
        $stmt = Database::pdo()->prepare(
            'SELECT 1 FROM inscripciones WHERE convocatoria_id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return (bool) $stmt->fetchColumn();
    }

    public static function inscritos(int $id): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT a.*, i.id AS inscripcion_id
               FROM inscripciones i
               JOIN alumnos a ON a.id = i.alumno_id
              WHERE i.convocatoria_id = ?
              ORDER BY a.apellidos, a.nombre'
        );
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /**
     * $existing = null   → caso CREAR (validamos fecha futura)
     * $existing = array  → caso EDITAR (no validamos fecha futura, pero sí plazas >= ocupadas)
     */
    public static function validate(array $data, ?array $existing = null): array
    {
        $errors = [];
        $isEdit = $existing !== null;

        $asignatura = trim($data['asignatura'] ?? '');
        if ($asignatura === '') {
            $errors['asignatura'] = 'La asignatura es obligatoria.';
        } elseif (mb_strlen($asignatura) > 150) {
            $errors['asignatura'] = 'Máximo 150 caracteres.';
        }

        $fecha = trim($data['fecha'] ?? '');
        if ($fecha === '') {
            $errors['fecha'] = 'La fecha es obligatoria.';
        } elseif (!self::isValidDate($fecha)) {
            $errors['fecha'] = 'Formato de fecha no válido.';
        } elseif (!$isEdit && $fecha < date('Y-m-d')) {
            $errors['fecha'] = 'La fecha no puede ser anterior a hoy.';
        }

        $aula = trim($data['aula'] ?? '');
        if ($aula === '') {
            $errors['aula'] = 'El aula es obligatoria.';
        } elseif (mb_strlen($aula) > 50) {
            $errors['aula'] = 'Máximo 50 caracteres.';
        }

        $plazasRaw = (string) ($data['plazas_maximas'] ?? '');
        if ($plazasRaw === '' || !ctype_digit($plazasRaw)) {
            $errors['plazas_maximas'] = 'Debe ser un número entero positivo.';
        } else {
            $plazas = (int) $plazasRaw;
            if ($plazas < 1) {
                $errors['plazas_maximas'] = 'Debe ser al menos 1.';
            } elseif ($isEdit && $plazas < (int) $existing['plazas_ocupadas']) {
                $ocupadas = (int) $existing['plazas_ocupadas'];
                $errors['plazas_maximas'] = "No se puede reducir por debajo de los {$ocupadas} alumnos ya inscritos.";
            }
        }

        return $errors;
    }

    private static function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Listado paginado
     */
    public static function paginate(int $page = 1, int $perPage = 10): array
    {
        $page    = max(1, $page);
        $offset  = ($page - 1) * $perPage;
        $pdo     = Database::pdo();

        $total = (int) $pdo->query('SELECT COUNT(*) FROM convocatorias')->fetchColumn();

        $stmt = $pdo->prepare(
            'SELECT * FROM v_convocatorias
       ORDER BY fecha DESC
          LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items'      => $stmt->fetchAll(),
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => max(1, (int) ceil($total / $perPage)),
        ];
    }
}