<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Detalles del Pedido #<?php echo $pedido['numero_pedido'] ?? $pedido['id']; ?></h3>
    <div class="btn-group">
        <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=generarFacturaPDF&id=<?php echo $pedido['id']; ?>" 
           class="btn btn-danger" target="_blank">
            <i class="bi bi-file-earmark-pdf"></i> Descargar PDF
        </a>
        <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=generarFacturaExcel&id=<?php echo $pedido['id']; ?>" 
           class="btn btn-success" target="_blank">
            <i class="bi bi-file-earmark-excel"></i> Descargar Excel
        </a>
        <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=pedidos" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Información del Cliente</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nombre:</strong> <?php echo sanitizar($pedido['nombre']); ?></p>
                        <p><strong>Email:</strong> <?php echo sanitizar($pedido['email']); ?></p>
                        <p><strong>Teléfono:</strong> <?php echo sanitizar($pedido['telefono']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>WhatsApp:</strong> <?php echo sanitizar($pedido['whatsapp']); ?></p>
                        <p><strong>Ciudad:</strong> <?php echo sanitizar($pedido['ciudad'] ?? 'N/A'); ?></p>
                        <p><strong>Dirección:</strong> <?php echo sanitizar($pedido['direccion']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>Artículos del Pedido</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cant. Pedida</th>
                            <th>Cant. Entregada</th>
                            <th>Faltan</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedido['detalles'] as $detalle): 
                            $cant = (int)($detalle['cantidad'] ?? 0);
                            $cantEnt = isset($detalle['cantidad_entregada']) ? (int)$detalle['cantidad_entregada'] : ((($detalle['estado_preparacion'] ?? '') === 'listo') ? $cant : 0);
                            if ($cantEnt > $cant) { $cantEnt = $cant; }
                            $faltan = max(0, $cant - $cantEnt);
                            $no_se_entrega = $cantEnt === 0;
                        ?>
                        <tr <?php echo $no_se_entrega ? 'class="table-danger"' : ''; ?>>
                            <td>
                                <img src="<?php echo APP_URL; ?>/public/images/productos/<?php echo sanitizar($detalle['imagen']); ?>" 
                                     style="width: 40px; height: 40px; object-fit: cover;" class="me-2">
                                <?php echo sanitizar($detalle['nombre']); ?>
                            </td>
                            <td><?php echo $cant; ?></td>
                            <td class="text-success fw-bold"><?php echo $cantEnt; ?></td>
                            <td class="text-danger fw-bold"><?php echo $faltan; ?></td>
                            <td>$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                            <td><strong><?php echo $cantEnt <= 0 ? '<span class="text-danger">$0.00</span>' : '$' . number_format($detalle['precio_unitario'] * $cantEnt, 2); ?></strong></td>
                            <td>
                                <?php if ($no_se_entrega): ?>
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> NO SE ENTREGA</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Entrega</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total (Solo Entregado):</strong></td>
                            <td><strong class="text-success h5">$<?php 
                                $total_entregados = 0;
                                foreach ($pedido['detalles'] as $detalle) {
                                    $cant = (int)($detalle['cantidad'] ?? 0);
                                    $cantEnt = isset($detalle['cantidad_entregada']) ? (int)$detalle['cantidad_entregada'] : ((($detalle['estado_preparacion'] ?? '') === 'listo') ? $cant : 0);
                                    if ($cantEnt > 0) {
                                        $total_entregados += $detalle['precio_unitario'] * $cantEnt;
                                    }
                                }
                                echo number_format($total_entregados, 2);
                            ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Actualizar Estado</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado del Pedido</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="en_pedido" <?php echo $pedido['estado'] === 'en_pedido' ? 'selected' : ''; ?>>En pedido</option>
                            <option value="alistado" <?php echo $pedido['estado'] === 'alistado' ? 'selected' : ''; ?>>Alistado</option>
                            <option value="empaquetado" <?php echo $pedido['estado'] === 'empaquetado' ? 'selected' : ''; ?>>Empaquetado</option>
                            <option value="verificado" <?php echo $pedido['estado'] === 'verificado' ? 'selected' : ''; ?>>Verificado</option>
                            <option value="en_reparto" <?php echo $pedido['estado'] === 'en_reparto' ? 'selected' : ''; ?>>En reparto</option>
                            <option value="entregado" <?php echo $pedido['estado'] === 'entregado' ? 'selected' : ''; ?>>Entregado</option>
                            <option value="cancelado" <?php echo $pedido['estado'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notas_admin" class="form-label">Notas Internas</label>
                        <textarea class="form-control" id="notas_admin" name="notas_admin" rows="3"><?php echo sanitizar($pedido['notas_admin'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> Guardar Cambios
                    </button>
                </form>

                <hr>

                <div class="alert alert-info">
                    <small>
                        <strong>Fecha de Creación:</strong><br>
                        <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_creacion'])); ?>
                    </small>
                </div>

                <div class="alert alert-<?php echo $pedido['whatsapp_enviado'] ? 'success' : 'warning'; ?>">
                    <small>
                        <i class="bi bi-whatsapp"></i> 
                        <?php echo $pedido['whatsapp_enviado'] ? 'WhatsApp enviado' : 'WhatsApp pendiente'; ?>
                    </small>
                </div>

                <div class="mt-3">
                    <h6>Trazabilidad del Pedido</h6>
                    <?php if (!empty($historial)): ?>
                        <ul class="list-group">
                            <?php foreach ($historial as $h): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>
                                                <?php 
                                                    $labels = [
                                                        'en_pedido' => 'En pedido',
                                                        'alistado' => 'Alistado',
                                                        'empaquetado' => 'Empaquetado',
                                                        'verificado' => 'Verificado',
                                                        'en_reparto' => 'En reparto',
                                                        'entregado' => 'Entregado',
                                                        'cancelado' => 'Cancelado'
                                                    ];
                                                    echo $labels[$h['estado']] ?? ucfirst($h['estado']);
                                                ?>
                                            </strong>
                                            <br>
                                            <small class="text-muted">Nota: <?php echo sanitizar($h['nota'] ?? ''); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <small><?php echo date('d/m/Y H:i', strtotime($h['fecha'])); ?></small><br>
                                            <small class="text-muted">Por: <?php echo sanitizar($h['usuario'] ?? 'Sistema'); ?></small>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Sin historial de cambios aún.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Las funciones de preparación de productos ya no se usan aquí
// Se trasladaron completamente a la lista de pedidos (pedidos.php)
</script>

<?php
include APP_ROOT . '/app/views/admin/layout/footer.php';
?>
