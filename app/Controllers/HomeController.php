<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class HomeController extends Controller
{
    public function index(): void
    {
        try {
            $pdo = Database::pdo();

            $stats = [
                'alumnos_total'           => (int) $pdo->query('SELECT COUNT(*) FROM alumnos')->fetchColumn(),
                'alumnos_con_inscripcion' => (int) $pdo->query('SELECT COUNT(DISTINCT alumno_id) FROM inscripciones')->fetchColumn(),
                'convocatorias_total'     => (int) $pdo->query('SELECT COUNT(*) FROM convocatorias')->fetchColumn(),
                'inscripciones_total'     => (int) $pdo->query('SELECT COUNT(*) FROM inscripciones')->fetchColumn(),
                'por_estado'              => ['abierta' => 0, 'completa' => 0, 'finalizada' => 0],
            ];

            $rows = $pdo->query('SELECT estado, COUNT(*) AS n FROM v_convocatorias GROUP BY estado')->fetchAll();
            foreach ($rows as $r) {
                $stats['por_estado'][$r['estado']] = (int) $r['n'];
            }

            $proximas = $pdo->query(
                "SELECT * FROM v_convocatorias
                  WHERE fecha >= CURDATE()
                AND plazas_ocupadas < plazas_maximas
                 ORDER BY fecha ASC
                LIMIT 5"
            )->fetchAll();

            $this->render('home', [
                'title'    => 'TICARUM',
                'dbOk'     => true,
                'stats'    => $stats,
                'proximas' => $proximas,
            ]);
        } catch (\Throwable $e) {
            $this->render('home', [
                'title'   => 'TICARUM',
                'dbOk'    => false,
                'dbError' => $e->getMessage(),
            ]);
        }
    }
}