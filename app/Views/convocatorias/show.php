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

<?php if ($convocatoria['estado'] === 'abierta'): ?>
    <div class="card">
        <h2>Inscribir alumno</h2>
        <?php if (empty($alumnosDisponibles)): ?>
            <p>Todos los alumnos del sistema ya están inscritos en esta convocatoria.</p>
        <?php else: ?>
            <form data-inscripcion-form data-convocatoria-id="<?= $convocatoria['id'] ?>" class="inline-flex-form">
                <select name="alumno_id" required>
                    <option value="">Selecciona un alumno…</option>
                    <?php foreach ($alumnosDisponibles as $a): ?>
                        <option value="<?= $a['id'] ?>">
                            <?= htmlspecialchars($a['apellidos'] . ', ' . $a['nombre']) ?>
                            (<?= htmlspecialchars($a['dni']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Inscribir</button>
            </form>
        <?php endif; ?>
    </div>
<?php elseif ($convocatoria['estado'] === 'completa'): ?>
    <div class="card"><p class="muted">No hay plazas disponibles para nuevas inscripciones.</p></div>
<?php elseif ($convocatoria['estado'] === 'finalizada'): ?>
    <div class="card"><p class="muted">Esta convocatoria ya ha finalizado.</p></div>
<?php endif; ?>

<div class="card">
    <h2>Alumnos inscritos (<?= count($inscritos) ?>)</h2>
    <?php if (empty($inscritos)): ?>
        <p>No hay alumnos inscritos todavía.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
            <tr><th>DNI</th><th>Nombre</th><th>Apellidos</th><th>Email</th><th>Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($inscritos as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['dni']) ?></td>
                    <td><?= htmlspecialchars($a['nombre']) ?></td>
                    <td><?= htmlspecialchars($a['apellidos']) ?></td>
                    <td><?= htmlspecialchars($a['email']) ?></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger"
                                data-inscripcion-delete="<?= $a['inscripcion_id'] ?>">
                            Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<p style="margin-top: 1.5rem;"><a href="/convocatorias">← Volver al listado</a></p>