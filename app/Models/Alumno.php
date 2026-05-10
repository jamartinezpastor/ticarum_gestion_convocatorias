<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Alumno
{
    public static function all(): array
    {
        return Database::pdo()
            ->query('SELECT * FROM alumnos ORDER BY apellidos, nombre')
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare( 'SELECT * FROM alumnos WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO alumnos (nombre, apellidos, email, dni) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$data['nombre'], $data['apellidos'], $data['email'], $data['dni']]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE alumnos SET nombre = ?, apellidos = ?, email = ?, dni = ? WHERE id = ?'
        );
        $stmt->execute([$data['nombre'], $data['apellidos'], $data['email'], $data['dni'], $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM alumnos WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function hasInscripciones(int $id): bool
    {
        $stmt = Database::pdo()->prepare(
            'SELECT 1 FROM inscripciones WHERE alumno_id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Devuelve un array de errores indexado por campo. Vacío si todo es válido.
     * $exceptId permite ignorar el mismo registro que se esta editando.
     */
    public static function validate(array $data, ?int $exceptId = null): array
    {
        $errors = [];

        $nombre = trim($data['nombre'] ?? '');
        if ($nombre === '') {
            $errors['nombre'] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($nombre) > 100) {
            $errors['nombre'] = 'Máximo 100 caracteres.';
        }

        $apellidos = trim($data['apellidos'] ?? '');
        if ($apellidos === '') {
            $errors['apellidos'] = 'Los apellidos son obligatorios.';
        } elseif (mb_strlen($apellidos) > 150) {
            $errors['apellidos'] = 'Máximo 150 caracteres.';
        }

        $email = trim($data['email'] ?? '');
        if ($email === '') {
            $errors['email'] = 'El email es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Formato de email no válido.';
        } elseif (self::existsBy('email', $email, $exceptId)) {
            $errors['email'] = 'Ya existe un alumno con este email.';
        }

        $dni = strtoupper(trim($data['dni'] ?? ''));
        if ($dni === '') {
            $errors['dni'] = 'El DNI es obligatorio.';
        } elseif (!self::isValidDni($dni)) {
            $errors['dni'] = 'DNI no válido (8 dígitos + letra de control).';
        } elseif (self::existsBy('dni', $dni, $exceptId)) {
            $errors['dni'] = 'Ya existe un alumno con este DNI.';
        }

        return $errors;
    }

    public static function isValidDni(string $dni): bool
    {
        if (!preg_match('/^(\d{8})([A-HJ-NP-TV-Z])$/i', $dni, $m)) {
            return false;
        }
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        return strtoupper($m[2]) === $letters[(int) $m[1] % 23];
    }

    // $field es siempre un literal interno ('email' o 'dni'), nunca input del usuario.
    private static function existsBy(string $field, string $value, ?int $exceptId): bool
    {
        $sql = "SELECT 1 FROM alumnos WHERE {$field} = ?";
        $params = [$value];
        if ($exceptId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $exceptId;
        }
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }
}