<div class="page-header">
    <h1>Alumnos</h1>
    <a href="/alumnos/crear" class="btn btn-primary">Nuevo alumno</a>
</div>

<?php if (empty($alumnos)): ?>
    <div class="card"><p>No hay alumnos registrados.</p></div>
<?php else: ?>
    <table class="data-table">
        <thead>
        <tr>
            <th>DNI</th><th>Nombre</th><th>Apellidos</th><th>Email</th>
            <th>Convocatorias</th><th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($alumnos as $a): ?>
            <tr>
                <td>
                    <button type="button" class="btn-link" data-alumno-trigger="<?= $a['id'] ?>"
                            title="Ver convocatorias inscritas">
                        <?= htmlspecialchars($a['dni']) ?>
                    </button>
                </td>
                <td><?= htmlspecialchars($a['nombre']) ?></td>
                <td><?= htmlspecialchars($a['apellidos']) ?></td>
                <td><?= htmlspecialchars($a['email']) ?></td>
                <td><?= count($a['convocatorias']) ?></td>
                <td class="actions">
                    <a href="/alumnos/<?= $a['id'] ?>/editar" class="btn btn-sm">Editar</a>
                    <form method="POST" action="/alumnos/<?= $a['id'] ?>" class="inline-form"
                          onsubmit="return confirm('¿Eliminar a <?= htmlspecialchars($a['nombre'] . ' ' . $a['apellidos']) ?>?');">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php /* Modales (uno por alumno; ocultos hasta que se hace clic en su DNI) */ ?>
    <?php foreach ($alumnos as $a): ?>
        <div class="modal-backdrop" data-alumno-modal="<?= $a['id'] ?>">
            <div class="modal">
                <div class="modal-header">
                    <h2>Convocatorias de <?= htmlspecialchars($a['nombre'] . ' ' . $a['apellidos']) ?></h2>
                    <button type="button" class="modal-close" aria-label="Cerrar">×</button>
                </div>
                <?php if (empty($a['convocatorias'])): ?>
                    <p>Este alumno no está inscrito en ninguna convocatoria.</p>
                <?php else: ?>
                    <ul class="modal-list">
                        <?php foreach ($a['convocatorias'] as $c): ?>
                            <li>
                                <a href="/convocatorias/<?= $c['id'] ?>">
                                    <strong><?= htmlspecialchars($c['asignatura']) ?></strong>
                                </a><br>
                                <small><?= htmlspecialchars($c['fecha']) ?> · Aula <?= htmlspecialchars($c['aula']) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>