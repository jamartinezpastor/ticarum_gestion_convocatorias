<div class="page-header">
    <div>
        <h1><?= htmlspecialchars($convocatoria['asignatura']) ?></h1>
        <p class="subhead">
            <?= htmlspecialchars($convocatoria['fecha']) ?> ·
            Aula <?= htmlspecialchars($convocatoria['aula']) ?> ·
            <span class="badge badge-<?= htmlspecialchars($convocatoria['estado']) ?>">
                <?= htmlspecialchars($convocatoria['estado']) ?>
            </span>
        </p>
    </div>
    <a href="/convocatorias/<?= $convocatoria['id'] ?>/editar" class="btn">Editar</a>
</div>

<div class="card">
    <h2>Plazas</h2>
    <p>
        <strong><?= $convocatoria['plazas_ocupadas'] ?></strong> ocupadas de
        <strong><?= $convocatoria['plazas_maximas'] ?></strong>
        (<?= $convocatoria['plazas_disponibles'] ?> disponibles)
    </p>
</div>

<div class="card">
    <h2>Alumnos inscritos (<?= count($inscritos) ?>)</h2>
    <?php if (empty($inscritos)): ?>
        <p>No hay alumnos inscritos todavía.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
            <tr><th>DNI</th><th>Nombre</th><th>Apellidos</th><th>Email</th></tr>
            </thead>
            <tbody>
            <?php foreach ($inscritos as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['dni']) ?></td>
                    <td><?= htmlspecialchars($a['nombre']) ?></td>
                    <td><?= htmlspecialchars($a['apellidos']) ?></td>
                    <td><?= htmlspecialchars($a['email']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<p style="margin-top: 1.5rem;"><a href="/convocatorias">← Volver al listado</a></p>