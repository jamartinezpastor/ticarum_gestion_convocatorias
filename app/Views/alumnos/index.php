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
            <th>DNI</th><th>Nombre</th><th>Apellidos</th><th>Email</th><th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($alumnos as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['dni']) ?></td>
                <td><?= htmlspecialchars($a['nombre']) ?></td>
                <td><?= htmlspecialchars($a['apellidos']) ?></td>
                <td><?= htmlspecialchars($a['email']) ?></td>
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
<?php endif; ?>