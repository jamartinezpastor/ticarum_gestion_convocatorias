<?php $isEdit = $alumno !== null; ?>

<h1><?= $isEdit ? 'Editar alumno' : 'Nuevo alumno' ?></h1>

<form method="POST" action="<?= $isEdit ? '/alumnos/' . $alumno['id'] : '/alumnos' ?>" class="form">
    <?php if ($isEdit): ?>
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="field">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" maxlength="100" required
               value="<?= htmlspecialchars($old['nombre'] ?? '') ?>">
        <?php if (!empty($errors['nombre'])): ?>
            <p class="field-error"><?= htmlspecialchars($errors['nombre']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" maxlength="150" required
               value="<?= htmlspecialchars($old['apellidos'] ?? '') ?>">
        <?php if (!empty($errors['apellidos'])): ?>
            <p class="field-error"><?= htmlspecialchars($errors['apellidos']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" maxlength="255" required
               value="<?= htmlspecialchars($old['email'] ?? '') ?>">
        <?php if (!empty($errors['email'])): ?>
            <p class="field-error"><?= htmlspecialchars($errors['email']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="dni">DNI</label>
        <input type="text" id="dni" name="dni" maxlength="9" required
               pattern="[0-9]{8}[A-HJ-NP-TV-Za-hj-np-tv-z]"
               value="<?= htmlspecialchars($old['dni'] ?? '') ?>">
        <small>8 dígitos y una letra</small>
        <?php if (!empty($errors['dni'])): ?>
            <p class="field-error"><?= htmlspecialchars($errors['dni']) ?></p>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?= $isEdit ? 'Guardar cambios' : 'Crear alumno' ?>
        </button>
        <a href="/alumnos" class="btn">Cancelar</a>
    </div>
</form>