<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Nuevo Cliente</h3>
    <a href="<?php echo tenant_base_url(); ?>/index.php?controller=admin&action=clientes" class="btn btn-secondary">Volver</a>
</div>

<form method="POST" action="<?php echo tenant_base_url(); ?>/index.php?controller=admin&action=guardarCliente">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" name="usuario" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">WhatsApp</label>
            <input type="text" name="whatsapp" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Ciudad</label>
            <input type="text" name="ciudad" class="form-control">
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control">
        </div>
    </div>

    <button class="btn btn-primary">Crear Cliente</button>
</form>

<?php include APP_ROOT . '/app/views/admin/layout/footer.php'; ?>
