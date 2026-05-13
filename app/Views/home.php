<h1>TICARUM</h1>
<p class="muted">Sistema de gestión de convocatorias de examen e inscripciones de alumnos.</p>

<?php if (!$dbOk): ?>
    <div class="card error">
        <h2>Error de conexión a la base de datos</h2>
        <p><?= htmlspecialchars($dbError) ?></p>
        <p>Revisa <code>config/config.php</code>.</p>
    </div>
<?php else: ?>

    <div class="stats-grid">
        <a href="/alumnos" class="stat-card">
            <span class="stat-label">Alumnos</span>
            <span class="stat-value"><?= $stats['alumnos_total'] ?></span>
            <span class="stat-detail"><?= $stats['alumnos_con_inscripcion'] ?> con inscripciones</span>
        </a>

        <a href="/convocatorias" class="stat-card">
            <span class="stat-label">Convocatorias</span>
            <span class="stat-value"><?= $stats['convocatorias_total'] ?></span>
            <span class="stat-detail">
                <span class="badge badge-abierta"><?= $stats['por_estado']['abierta'] ?> abiertas</span>
                <span class="badge badge-completa"><?= $stats['por_estado']['completa'] ?> completas</span>
                <span class="badge badge-finalizada"><?= $stats['por_estado']['finalizada'] ?> finalizadas</span>
            </span>
        </a>

        <div class="stat-card">
            <span class="stat-label">Inscripciones</span>
            <span class="stat-value"><?= $stats['inscripciones_total'] ?></span>
            <span class="stat-detail">total registradas</span>
        </div>
    </div>

    <?php if (!empty($proximas)): ?>
        <h2 style="margin-top: 1rem;">Próximas convocatorias abiertas</h2>
        <table class="data-table">
            <thead>
            <tr><th>Asignatura</th><th>Fecha</th><th>Aula</th><th>Plazas libres</th></tr>
            </thead>
            <tbody>
            <?php foreach ($proximas as $c): ?>
                <tr>
                    <td><a href="/convocatorias/<?= $c['id'] ?>"><?= htmlspecialchars($c['asignatura']) ?></a></td>
                    <td><?= htmlspecialchars($c['fecha']) ?></td>
                    <td><?= htmlspecialchars($c['aula']) ?></td>
                    <td><?= $c['plazas_disponibles'] ?> / <?= $c['plazas_maximas'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php endif; ?>