<h1>LAYOUT VIEW</h1>
<p class="muted">Página home.</p>

<?php
try {
    $pdo = \App\Core\Database::pdo();
    $convocatorias = (int) $pdo->query('SELECT COUNT(*) FROM convocatorias')->fetchColumn();
    $alumnos       = (int) $pdo->query('SELECT COUNT(*) FROM alumnos')->fetchColumn();
    $inscripciones = (int) $pdo->query('SELECT COUNT(*) FROM inscripciones')->fetchColumn();
    ?>
    <div class="card">
        <h2>Test conexión a BD</h2>
        <ul>
            <li><strong><?= $convocatorias ?></strong> convocatorias</li>
            <li><strong><?= $alumnos ?></strong> alumnos</li>
            <li><strong><?= $inscripciones ?></strong> inscripciones</li>
        </ul>
    </div>
    <?php
} catch (\Throwable $e) {
    ?>
    <div class="card error">
        <h2>Error de conexión</h2>
        <p><?= htmlspecialchars($e->getMessage()) ?></p>
    </div>
    <?php
}
?>