<?php $isEdit = $convocatoria !== null; ?>

<h1><?= $isEdit ? 'Editar convocatoria' : 'Nueva convocatoria' ?></h1>

<form method="POST" action="<?= $isEdit ? '/convocatorias/' . $convocatoria['id'] : '/convocatorias' ?>" class="form">
    <?php if ($isEdit): ?>
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="field">
        <label for="asignatura">Asignatura</label>
        <input type="text" id="asignatura" name="asignatura" maxlength="150" required
               value="<?= htmlspecialchars($old['asignatura'] ?? '') ?>">
        <?php if (!empty($errors['asignatura'])): ?>
            <p class="field-error"><?= htmlspecialchars($errors['asignatura']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="fecha">Fecha</label>
        <input type="date" id="fecha" name="fecha" required
               value="<?= htmlspecialchars($old['fecha'] ?? '') ?>">
        <?php if (!empty($errors['fecha'])): ?>
            <p class="field-error"><?= htmlspecialchars($errors['fecha']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="aula">Aula</label>
        <input type="text" id="aula" name="aula" maxlength="50" required
               value="<?= htmlspecialchars($old['aula'] ?? '') ?>">
        <?php if (!empty($errors['aula'])): ?>
            <p class="field-error"><?= htmlspecialchars($errors['aula']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="plazas_maximas">Plazas máximas</label>
        <input type="number" id="plazas_maximas" name="plazas_maximas" min="1" required
               value="<?= htmlspecialchars((string)($old['plazas_maximas'] ?? '')) ?>">
        <?php if ($isEdit && (int) $convocatoria['plazas_ocupadas'] > 0): ?>
            <small>Hay <?= $convocatoria['plazas_ocupadas'] ?> alumnos inscritos. No puedes bajar de ese número.</small>
        <?php endif; ?>
        <?php if (!empty($errors['plazas_maximas'])): ?>
            <p class="field-error"><?= htmlspecialchars($errors['plazas_maximas']) ?></p>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?= $isEdit ? 'Guardar cambios' : 'Crear convocatoria' ?>
        </button>
        <a href="/convocatorias" class="btn">Cancelar</a>
    </div>
</form>