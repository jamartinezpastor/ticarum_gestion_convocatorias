<?php
/** @var array $pagination */
if (($pagination['totalPages'] ?? 1) <= 1) return;
$page = $pagination['page'];
$last = $pagination['totalPages'];
?>
<nav class="pagination" aria-label="Paginación">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>" rel="prev">← Anterior</a>
    <?php else: ?>
        <span class="disabled">← Anterior</span>
    <?php endif; ?>

    <span class="info">
        Página <?= $page ?> de <?= $last ?> · <?= $pagination['total'] ?> resultados
    </span>

    <?php if ($page < $last): ?>
        <a href="?page=<?= $page + 1 ?>" rel="next">Siguiente →</a>
    <?php else: ?>
        <span class="disabled">Siguiente →</span>
    <?php endif; ?>
</nav>