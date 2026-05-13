<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title><?= htmlspecialchars($title ?? 'TICARUM') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<header class="topbar">
    <div class="container">
        <a href="/" class="brand">GESTIÓN CONVOCATORIAS</a>
        <nav>
            <a href="/">Inicio</a>
            <a href="/alumnos">Alumnos</a>
            <a href="/convocatorias">Convocatorias</a>
        </nav>
    </div>
</header>
<main class="container">
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?>">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?= $content ?>
</main>
<script src="/assets/js/app.js" defer></script>

<!-- Modal eliminación -->
<div class="modal-backdrop" id="confirm-modal">
    <div class="modal">
        <div class="modal-header">
            <h2>Confirmar</h2>
            <button type="button" class="modal-close" aria-label="Cerrar">×</button>
        </div>
        <p data-confirm-message style="margin-bottom: 1.5rem;"></p>
        <div class="modal-actions">
            <button type="button" class="btn" data-confirm-cancel>Cancelar</button>
            <button type="button" class="btn btn-danger" data-confirm-ok>
                <svg class="icon" aria-hidden="true"><use href="#icon-trash"/></svg>
                Eliminar
            </button>
        </div>
    </div>
</div>
</body>

<svg style="display:none" aria-hidden="true">
    <symbol id="icon-trash" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="3 6 5 6 21 6"/>
        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
        <path d="M10 11v6"/>
        <path d="M14 11v6"/>
        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
    </symbol>
</svg>

</html>