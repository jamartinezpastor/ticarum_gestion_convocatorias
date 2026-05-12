<div class="page-header">
    <h1>Convocatorias</h1>
    <a href="/convocatorias/crear" class="btn btn-primary">Nueva convocatoria</a>
</div>

<?php if (empty($convocatorias)): ?>
    <div class="card"><p>No hay convocatorias registradas.</p></div>
<?php else: ?>
    <table class="data-table">
        <thead>
        <tr>
            <th>Asignatura</th><th>Fecha</th><th>Aula</th><th>Plazas</th>
            <th>Estado</th><th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($convocatorias as $c): ?>
            <tr>
                <td>
                    <a href="/convocatorias/<?= $c['id'] ?>"><?= htmlspecialchars($c['asignatura']) ?></a>
                </td>
                <td><?= htmlspecialchars($c['fecha']) ?></td>
                <td><?= htmlspecialchars($c['aula']) ?></td>
                <td>
                    <?= $c['plazas_ocupadas'] ?> / <?= $c['plazas_maximas'] ?>
                </td>
                <td>
                        <span class="badge badge-<?= htmlspecialchars($c['estado']) ?>">
                            <?= htmlspecialchars($c['estado']) ?>
                        </span>
                </td>
                <td class="actions">
                    <a href="/convocatorias/<?= $c['id'] ?>" class="btn btn-sm">Ver</a>
                    <a href="/convocatorias/<?= $c['id'] ?>/editar" class="btn btn-sm">Editar</a>
                    <!-- Cambiado onsubmit="" por data-confirm, que es recogido por el handler confirmDialog() de app.js -->
                    <?php $tieneInscritos = (int) $c['plazas_ocupadas'] > 0; ?>
                    <form method="POST" action="/convocatorias/<?= $c['id'] ?>" class="inline-form"
                          data-confirm="¿Eliminar la convocatoria de <?= htmlspecialchars($c['asignatura']) ?>?">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-sm btn-danger btn-icon-only"
                                <?= $tieneInscritos ? 'disabled' : '' ?>
                                title="<?= $tieneInscritos
                                        ? 'No se puede eliminar: tiene ' . $c['plazas_ocupadas'] . ' inscrito(s).'
                                        : 'Eliminar convocatoria' ?>">
                            <svg class="icon" aria-hidden="true"><use href="#icon-trash"/></svg>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>