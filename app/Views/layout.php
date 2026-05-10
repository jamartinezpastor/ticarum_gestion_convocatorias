<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
</body>
</html>