<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-receipt"></i> Gestión de Cuentas de Cobro</h3>
</div>

<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php" id="filtroFormFacturas">
            <input type="hidden" name="controller" value="admin">
            <input type="hidden" name="action" value="facturas">
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" class="form-control" name="busqueda" id="filtroBusquedaFacturas"
                           value="<?php echo sanitizar($_GET['busqueda'] ?? ''); ?>" 
                           placeholder="No. Cuenta de Cobro o Nombre del Cliente">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" name="desde" id="filtroDesdeFacturas"
                           value="<?php echo sanitizar($_GET['desde'] ?? ''); ?>">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="hasta" id="filtroHastaFacturas"
                           value="<?php echo sanitizar($_GET['hasta'] ?? ''); ?>">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado" id="filtroEstadoFacturas">
                        <option value="">Todos</option>
                        <option value="en_pedido" <?php echo ($_GET['estado'] ?? '') === 'en_pedido' ? 'selected' : ''; ?>>En pedido</option>
                        <option value="alistado" <?php echo ($_GET['estado'] ?? '') === 'alistado' ? 'selected' : ''; ?>>Alistado</option>
                        <option value="empaquetado" <?php echo ($_GET['estado'] ?? '') === 'empaquetado' ? 'selected' : ''; ?>>Empaquetado</option>
                        <option value="verificado" <?php echo ($_GET['estado'] ?? '') === 'verificado' ? 'selected' : ''; ?>>Verificado</option>
                        <option value="en_reparto" <?php echo ($_GET['estado'] ?? '') === 'en_reparto' ? 'selected' : ''; ?>>En reparto</option>
                        <option value="entregado" <?php echo ($_GET['estado'] ?? '') === 'entregado' ? 'selected' : ''; ?>>Entregado</option>
                        <option value="cancelado" <?php echo ($_GET['estado'] ?? '') === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <?php if (!empty($_GET['busqueda']) || !empty($_GET['desde']) || !empty($_GET['hasta']) || !empty($_GET['estado'])): ?>
                    <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=facturas" class="btn btn-secondary flex-fill">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
        
        <script>
        // Filtrado automático en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filtroFormFacturas');
            const filtroBusqueda = document.getElementById('filtroBusquedaFacturas');
            const filtroEstado = document.getElementById('filtroEstadoFacturas');
            const filtroDesde = document.getElementById('filtroDesdeFacturas');
            const filtroHasta = document.getElementById('filtroHastaFacturas');
            
            let timeoutId;
            
            // Función para enviar el formulario
            function submitForm() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    form.submit();
                }, 500); // Espera 500ms después de escribir
            }
            
            // Filtrar mientras escribe en búsqueda
            if (filtroBusqueda) filtroBusqueda.addEventListener('input', submitForm);
            
            // Filtrar inmediatamente al cambiar estado o fechas
            if (filtroEstado) filtroEstado.addEventListener('change', () => form.submit());
            if (filtroDesde) filtroDesde.addEventListener('change', () => form.submit());
            if (filtroHasta) filtroHasta.addEventListener('change', () => form.submit());
        });
        </script>
    </div>
</div>

<!-- Tabla de facturas -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No. Cuenta de Cobro</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>WhatsApp</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($facturas)): ?>
                    <tr>
                            <td colspan="7" class="text-center text-muted">
                            <i class="bi bi-inbox"></i> No se encontraron cuentas de cobro
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($facturas as $factura): ?>
                        <tr>
                            <td>
                                <?php
                                // Mostrar numero_cuenta_cobro si existe, sino numero_pedido, sino id
                                $display_num = $factura['numero_cuenta_cobro'] ?? $factura['numero_pedido'] ?? $factura['id'];
                                ?>
                                <strong>#<?php echo str_pad($display_num, 6, '0', STR_PAD_LEFT); ?></strong>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($factura['fecha_creacion'])); ?></td>
                            <td><?php echo sanitizar($factura['cliente_nombre'] ?? 'N/A'); ?></td>
                            <td><?php echo sanitizar($factura['whatsapp']); ?></td>
                            <td><strong>$<?php echo number_format($factura['total'], 0); ?></strong></td>
                            <td>
                                <?php
                                $estados = [
                                    'en_pedido' => ['text' => 'En Pedido', 'class' => 'info'],
                                    'alistado' => ['text' => 'Alistado', 'class' => 'primary'],
                                    'empaquetado' => ['text' => 'Empaquetado', 'class' => 'primary'],
                                    'verificado' => ['text' => 'Verificado', 'class' => 'warning'],
                                    'en_reparto' => ['text' => 'En Reparto', 'class' => 'info'],
                                    'entregado' => ['text' => 'Entregado', 'class' => 'success'],
                                    'cancelado' => ['text' => 'Cancelado', 'class' => 'danger']
                                ];
                                $estado = $estados[$factura['estado']] ?? ['text' => 'Desconocido', 'class' => 'secondary'];
                                ?>
                                <span class="badge bg-<?php echo $estado['class']; ?>">
                                    <?php echo $estado['text']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=verPedido&id=<?php echo $factura['id']; ?>" 
                                       class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=generarFacturaPDF&id=<?php echo $factura['id']; ?>" 
                                       class="btn btn-sm btn-danger" title="Descargar PDF" target="_blank">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/index.php?controller=admin&action=generarFacturaExcel&id=<?php echo $factura['id']; ?>" 
                                       class="btn btn-sm btn-success" title="Descargar Excel" target="_blank">
                                        <i class="bi bi-file-earmark-excel"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($facturas)): ?>
        <div class="alert alert-info mt-3">
            <i class="bi bi-info-circle"></i>
            <strong>Total de cuentas de cobro:</strong> <?php echo count($facturas); ?> registros encontrados
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
include APP_ROOT . '/app/views/admin/layout/footer.php';
?>
