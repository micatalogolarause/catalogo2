<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="row g-2">
    <div class="col-4">
        <div class="card text-white bg-primary">
            <div class="card-body p-2 text-center">
                <div class="small"><i class="bi bi-box"></i> Productos</div>
                <div class="fw-bold fs-4"><?php echo $total_productos; ?></div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card text-white bg-success">
            <div class="card-body p-2 text-center">
                <div class="small"><i class="bi bi-cart-check"></i> Pedidos</div>
                <div class="fw-bold fs-4"><?php echo $total_pedidos; ?></div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card text-white bg-info">
            <div class="card-body p-2 text-center">
                <div class="small"><i class="bi bi-speedometer"></i> En pedido</div>
                <div class="fw-bold fs-4"><?php echo count(array_filter($pedidos_recientes, function($p) { return $p['estado'] === 'en_pedido'; })); ?></div>
            </div>
        </div>
    </div>
</div>

<h3 class="mt-4">Pedidos Recientes</h3>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos_recientes as $pedido): ?>
            <tr>
                <td>#<?php echo $pedido['numero_pedido'] ?? $pedido['id']; ?></td>
                <td><?php echo sanitizar($pedido['nombre']); ?></td>
                <td>$<?php echo number_format($pedido['total'], 2); ?></td>
                <td>
                    <span class="badge bg-<?php 
                        echo $pedido['estado'] === 'en_pedido' ? 'warning' : 
                             ($pedido['estado'] === 'alistado' ? 'info' : 
                              ($pedido['estado'] === 'en_reparto' ? 'primary' : 
                               ($pedido['estado'] === 'entregado' ? 'success' : 'danger')));
                    ?>">
                        <?php 
                            $labels = [
                                'en_pedido' => 'En pedido',
                                'alistado' => 'Alistado',
                                'en_reparto' => 'En reparto',
                                'entregado' => 'Entregado',
                                'cancelado' => 'Cancelado'
                            ];
                            echo $labels[$pedido['estado']] ?? ucfirst($pedido['estado']);
                        ?>
                    </span>
                </td>
                <td><?php echo date('d/m/Y', strtotime($pedido['fecha_creacion'])); ?></td>
                <td>
                    <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=verPedido&id=<?php echo $pedido['id']; ?>" 
                       class="btn btn-sm btn-outline-primary">Ver</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include APP_ROOT . '/app/views/admin/layout/footer.php';
?>
