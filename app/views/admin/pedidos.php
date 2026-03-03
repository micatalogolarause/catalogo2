<?php
include APP_ROOT . '/app/views/admin/layout/header.php';

// Calcular estadísticas
$total_pedidos = count($pedidos);
$en_preparacion = count(array_filter($pedidos, fn($p) => in_array($p['estado'], ['en_pedido', 'alistado', 'empaquetado'])));
$pendientes = count(array_filter($pedidos, fn($p) => $p['estado'] === 'en_pedido'));
$con_faltantes = 0; // Puedes calcular esto según tu lógica
?>

<!-- Encabezado con Título y Subtítulo -->
<div class="text-center mb-4 py-4 bg-light rounded shadow-sm">
    <h2 class="mb-2 text-primary">
        <i class="bi bi-clipboard-check"></i> Gestión de Pedidos - <?php echo htmlspecialchars($_SESSION['tenant_data']['titulo_empresa'] ?? $_SESSION['tenant_data']['nombre'] ?? TENANT_NAME); ?>
    </h2>
    <p class="text-muted mb-0">Sistema de seguimiento y preparación de pedidos</p>
</div>

<!-- Filtros Avanzados -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php" id="filtroForm">
            <input type="hidden" name="controller" value="admin">
            <input type="hidden" name="action" value="pedidos">
            
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Cliente</label>
                    <input type="text" class="form-control" name="cliente" id="filtroCliente"
                           value="<?php echo sanitizar($_GET['cliente'] ?? ''); ?>" 
                           placeholder="Buscar por nombre">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" name="desde" id="filtroDesde"
                           value="<?php echo sanitizar($_GET['desde'] ?? ''); ?>">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="hasta" id="filtroHasta"
                           value="<?php echo sanitizar($_GET['hasta'] ?? ''); ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado" id="filtroEstado">
                        <option value="">📋 Todos</option>
                        <option value="en_pedido" <?php echo isset($_GET['estado']) && $_GET['estado'] === 'en_pedido' ? 'selected' : ''; ?>>⏳ En pedido</option>
                        <option value="alistado" <?php echo isset($_GET['estado']) && $_GET['estado'] === 'alistado' ? 'selected' : ''; ?>>👨‍🍳 En preparación</option>
                        <option value="empaquetado" <?php echo isset($_GET['estado']) && $_GET['estado'] === 'empaquetado' ? 'selected' : ''; ?>>📦 Empaquetado</option>
                        <option value="verificado" <?php echo isset($_GET['estado']) && $_GET['estado'] === 'verificado' ? 'selected' : ''; ?>>✅ Verificado</option>
                        <option value="en_reparto" <?php echo isset($_GET['estado']) && $_GET['estado'] === 'en_reparto' ? 'selected' : ''; ?>>🚚 Listo para entrega</option>
                        <option value="entregado" <?php echo isset($_GET['estado']) && $_GET['estado'] === 'entregado' ? 'selected' : ''; ?>>✔️ Entregados</option>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <div class="w-100">
                        <?php if (!empty($_GET['cliente']) || !empty($_GET['desde']) || !empty($_GET['hasta']) || !empty($_GET['estado'])): ?>
                        <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=pedidos" 
                           class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Limpiar
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle w-100" data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i> Exportar
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=reportePedidosPDF<?php 
                                echo !empty($_GET['cliente']) ? '&cliente=' . urlencode($_GET['cliente']) : '';
                                echo !empty($_GET['desde']) ? '&desde=' . urlencode($_GET['desde']) : '';
                                echo !empty($_GET['hasta']) ? '&hasta=' . urlencode($_GET['hasta']) : '';
                                echo !empty($_GET['estado']) ? '&estado=' . urlencode($_GET['estado']) : '';
                            ?>" target="_blank">
                                <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=reportePedidosExcel<?php 
                                echo !empty($_GET['cliente']) ? '&cliente=' . urlencode($_GET['cliente']) : '';
                                echo !empty($_GET['desde']) ? '&desde=' . urlencode($_GET['desde']) : '';
                                echo !empty($_GET['hasta']) ? '&hasta=' . urlencode($_GET['hasta']) : '';
                                echo !empty($_GET['estado']) ? '&estado=' . urlencode($_GET['estado']) : '';
                            ?>" target="_blank">
                                <i class="bi bi-file-earmark-excel text-success"></i> Excel
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
        
        <script>
        // Filtrado automático en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filtroForm');
            const filtroCliente = document.getElementById('filtroCliente');
            const filtroEstado = document.getElementById('filtroEstado');
            const filtroDesde = document.getElementById('filtroDesde');
            const filtroHasta = document.getElementById('filtroHasta');
            
            let timeoutId;
            
            // Función para enviar el formulario
            function submitForm() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    form.submit();
                }, 500); // Espera 500ms después de escribir
            }
            
            // Filtrar mientras escribe en cliente
            filtroCliente.addEventListener('input', submitForm);
            
            // Filtrar inmediatamente al cambiar estado o fechas
            filtroEstado.addEventListener('change', () => form.submit());
            filtroDesde.addEventListener('change', () => form.submit());
            filtroHasta.addEventListener('change', () => form.submit());
        });
        </script>
    </div>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
            <div class="card-body text-center py-3">
                <h2 class="mb-1 text-primary fw-bold" style="font-size: 2.5rem;"><?php echo $total_pedidos; ?></h2>
                <p class="text-muted mb-0 small">Total Pedidos</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
            <div class="card-body text-center py-3">
                <h2 class="mb-1 fw-bold" style="font-size: 2.5rem; color: #0dcaf0;"><?php echo $en_preparacion; ?></h2>
                <p class="text-muted mb-0 small">En Preparación</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6c757d !important;">
            <div class="card-body text-center py-3">
                <h2 class="mb-1 text-secondary fw-bold" style="font-size: 2.5rem;"><?php echo $pendientes; ?></h2>
                <p class="text-muted mb-0 small">Pendientes</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
            <div class="card-body text-center py-3">
                <h2 class="mb-1 text-danger fw-bold" style="font-size: 2.5rem;"><?php echo $con_faltantes; ?></h2>
                <p class="text-muted mb-0 small">Con Faltantes</p>
            </div>
        </div>
    </div>
</div>

<!-- Listado de Pedidos en Cards -->
<?php if (empty($pedidos)): ?>
<div class="alert alert-info text-center">
    <i class="bi bi-info-circle"></i> No hay pedidos registrados
</div>
<?php else: ?>
<div class="pedidos-list">
    <?php foreach ($pedidos as $pedido): ?>
    <?php
        $detalles = isset($pedido['detalles']) ? $pedido['detalles'] : [];
        $total_detalles = count($detalles);
        $listos = 0;
        foreach ($detalles as $d) {
            if (($d['estado_preparacion'] ?? 'pendiente') === 'listo') {
                $listos++;
            }
        }
        $avance = $total_detalles > 0 ? round(($listos / $total_detalles) * 100) : 0;
    ?>
    <div class="card mb-3 border-0 shadow-sm pedido-card">
        <!-- Encabezado del pedido -->
        <div class="card-header bg-light border-bottom">
            <div class="row align-items-center">
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <h6 class="mb-0"><strong>Pedido #<?php echo $pedido['numero_pedido'] ?? $pedido['id']; ?></strong></h6>
                    <small class="text-muted">
                        <?php echo date('d/m/Y - H:i', strtotime($pedido['fecha_creacion'])); ?>
                    </small>
                </div>
                <div class="col-md-6 text-md-end d-flex justify-content-end align-items-center gap-2">
                    <span class="badge bg-light text-dark border" style="font-size: 0.85rem;">
                        <strong><?php echo $listos; ?>/<?php echo $total_detalles; ?></strong>
                    </span>
                    <span class="badge bg-info text-dark" style="font-size: 0.85rem;">
                        <?php echo $avance; ?>%
                    </span>
                    <span class="badge bg-<?php 
                        echo $pedido['estado'] === 'en_pedido' ? 'warning' : 
                             ($pedido['estado'] === 'alistado' ? 'info' : 
                              ($pedido['estado'] === 'en_reparto' ? 'primary' : 
                               ($pedido['estado'] === 'entregado' ? 'success' : 'danger')));
                    ?> fs-6">
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
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Progreso del pedido -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted">Preparación: <strong><?php echo $listos; ?>/<?php echo $total_detalles; ?></strong></small>
                <span class="badge bg-info text-dark"><?php echo $avance; ?>%</span>
            </div>
            <div class="progress mb-3" style="height: 10px; border-radius: 12px; background-color: #e9ecef;">
                <div class="progress-bar" role="progressbar"
                     style="width: <?php echo $avance; ?>%; background: linear-gradient(90deg, #ffc107 0%, #28a745 100%); border-radius: 12px;"
                     aria-valuenow="<?php echo $avance; ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <!-- Información del cliente -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-2">
                        <small class="text-muted d-block"><i class="bi bi-person"></i> Cliente</small>
                        <strong><?php echo sanitizar($pedido['nombre'] ?? $pedido['cliente_nombre'] ?? 'Sin nombre'); ?></strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block"><i class="bi bi-telephone"></i> Teléfono</small>
                        <strong><?php echo sanitizar($pedido['whatsapp'] ?? 'Sin teléfono'); ?></strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <small class="text-muted d-block"><i class="bi bi-envelope"></i> Email</small>
                        <strong><?php echo sanitizar($pedido['email'] ?? 'Sin email'); ?></strong>
                    </div>
                </div>
            </div>

            <!-- Productos del pedido -->
            <?php if (!empty($detalles)): ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted d-block"><i class="bi bi-box"></i> Productos</small>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#productos-<?php echo $pedido['id']; ?>" aria-expanded="false">
                        Ver / actualizar preparación
                    </button>
                </div>
                <!-- Miniaturas -->
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <?php foreach ($detalles as $detalle): 
                        $imgNombre = $detalle['imagen'];
                        $imgUrl = APP_URL . '/public/images/no-image.jpg';
                        
                        // Si hay nombre de imagen en BD
                        if (!empty($imgNombre)) {
                            if (file_exists(APP_ROOT . '/public/images/productos/' . $imgNombre)) {
                                $imgUrl = APP_URL . '/public/images/productos/' . $imgNombre;
                            } else {
                                $imgUrl = tenant_upload_base_url(TENANT_ID) . '/images/' . $imgNombre;
                            }
                        } else {
                            // Buscar el primer archivo de imagen en la carpeta del tenant para este producto
                            $tenant_images_dir = tenant_upload_base_dir(TENANT_ID) . '/images';
                            if (is_dir($tenant_images_dir)) {
                                $files = glob($tenant_images_dir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                                // Buscar archivos que mencionen el ID del producto o nombre similar
                                foreach ($files as $file) {
                                    if (preg_match('/\d+/', basename($file)) || !str_contains(basename($file), 'index.html')) {
                                        $imgUrl = tenant_upload_base_url(TENANT_ID) . '/images/' . basename($file);
                                        break;
                                    }
                                }
                            }
                        }
                    ?>
                    <div class="position-relative" style="width: 50px; height: 50px;">
                        <img src="<?php echo $imgUrl; ?>" 
                             alt="<?php echo sanitizar($detalle['nombre']); ?>"
                             class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: cover;"
                             onerror="this.src='<?php echo APP_URL; ?>/public/images/no-image.jpg'">
                        <span class="badge bg-primary position-absolute" style="top: -8px; right: -8px; font-size: 0.7em;">
                            <?php echo $detalle['cantidad']; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Lista expandible con botón Pendiente/Listo -->
                <div class="collapse" id="productos-<?php echo $pedido['id']; ?>">
                    <div class="list-group">
                        <?php foreach ($detalles as $detalle): 
                            $estadoPrep = $detalle['estado_preparacion'] ?? 'pendiente';
                            $esListo = $estadoPrep === 'listo';
                            $cantEnt = isset($detalle['cantidad_entregada']) ? (int)$detalle['cantidad_entregada'] : ($esListo ? (int)$detalle['cantidad'] : 0);
                            $faltan = max(0, (int)$detalle['cantidad'] - $cantEnt);
                        ?>
                        <div class="list-group-item" data-detalle-id="<?php echo $detalle['id']; ?>">
                            <!-- Fila 1: Imagen + Info Producto -->
                            <div class="d-flex align-items-start gap-3 mb-2">
                                <?php 
                                    $imgNombre = $detalle['imagen'];
                                    $imgUrl = APP_URL . '/public/images/no-image.jpg';
                                    
                                    if (!empty($imgNombre)) {
                                        if (file_exists(APP_ROOT . '/public/images/productos/' . $imgNombre)) {
                                            $imgUrl = APP_URL . '/public/images/productos/' . $imgNombre;
                                        } else {
                                            $imgUrl = tenant_upload_base_url(TENANT_ID) . '/images/' . $imgNombre;
                                        }
                                    } else {
                                        $tenant_images_dir = tenant_upload_base_dir(TENANT_ID) . '/images';
                                        if (is_dir($tenant_images_dir)) {
                                            $files = glob($tenant_images_dir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                                            if (!empty($files)) {
                                                // Usar la primera imagen disponible
                                                $imgUrl = tenant_upload_base_url(TENANT_ID) . '/images/' . basename($files[0]);
                                            }
                                        }
                                    }
                                ?>
                                <img src="<?php echo $imgUrl; ?>" 
                                     alt="<?php echo sanitizar($detalle['nombre']); ?>"
                                     style="width:60px;height:60px;object-fit:cover;" class="rounded flex-shrink-0"
                                     onerror="this.src='<?php echo APP_URL; ?>/public/images/no-image.jpg'">
                                <div style="flex:1;">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <strong><?php echo sanitizar($detalle['nombre']); ?></strong>
                                        <span class="badge bg-primary"><?php echo $detalle['cantidad']; ?>x</span>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <span>Entrega: <strong class="text-success cant-entregada" data-max="<?php echo (int)$detalle['cantidad']; ?>"><?php echo $cantEnt; ?></strong></span>
                                        <span class="ms-2">Faltan: <strong class="text-danger cant-faltan"><?php echo $faltan; ?></strong></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Fila 2: Controles (responsive) -->
                            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                                <input type="number" class="form-control form-control-sm input-cant-ent" style="min-width:90px;" min="0" max="<?php echo (int)$detalle['cantidad']; ?>" value="<?php echo $cantEnt; ?>" data-pedido-id="<?php echo $pedido['id']; ?>" data-detalle-id="<?php echo $detalle['id']; ?>" oninput="actualizarCantidadesVisual(this)">
                                <button class="btn btn-sm btn-outline-primary flex-grow-1 flex-sm-grow-0" onclick="guardarCantidadEntregada(this)" data-pedido-id="<?php echo $pedido['id']; ?>" data-detalle-id="<?php echo $detalle['id']; ?>">Confirmar</button>
                                <button class="btn btn-sm toggle-prep flex-grow-1 flex-sm-grow-0" 
                                        data-estado="<?php echo $esListo ? 'listo' : 'pendiente'; ?>"
                                        data-pedido-id="<?php echo $pedido['id']; ?>"
                                        data-detalle-id="<?php echo $detalle['id']; ?>"
                                        onclick="cambiarPreparacion(this)"
                                        style="min-width:110px; <?php echo $esListo ? 'background-color:#28a745;color:white;' : 'background-color:#6c757d;color:white;'; ?>">
                                    <i class="bi bi-<?php echo $esListo ? 'check-circle' : 'clock'; ?>"></i> <?php echo $esListo ? 'Listo' : 'Pendiente'; ?>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Botones de Empaquetado y Verificado -->
            <?php if (in_array($pedido['estado'], ['alistado', 'en_pedido', 'empaquetado', 'verificado', 'en_reparto'])): ?>
            <div class="d-flex gap-2 justify-content-center mb-3" data-pedido-id="<?php echo $pedido['id']; ?>">
                <?php 
                    $ya_empaquetado = in_array($pedido['estado'], ['empaquetado', 'verificado', 'en_reparto']);
                    // Solo permitir marcar verificado si el estado es exactamente "empaquetado"
                    $puede_verificar = $pedido['estado'] === 'empaquetado';
                    $ya_verificado = in_array($pedido['estado'], ['verificado', 'en_reparto']);
                    $ya_listo_entrega = $pedido['estado'] === 'en_reparto';
                    
                    // Contar productos listos y pendientes, recopilar nombres de faltantes
                    $productos_listos = 0;
                    $productos_pendientes = 0;
                    $nombres_pendientes = array();
                    $cantidad_total = 0;
                    $cantidad_entregada_total = 0;
                    $faltantes_list = array();
                    foreach ($pedido['detalles'] as $det) {
                        $cant = (int)($det['cantidad'] ?? 0);
                        $cantEnt = isset($det['cantidad_entregada']) ? (int)$det['cantidad_entregada'] : (($det['estado_preparacion'] ?? '') === 'listo' ? $cant : 0);
                        $cantidad_total += $cant;
                        $cantidad_entregada_total += $cantEnt;
                        if ($det['estado_preparacion'] === 'listo') {
                            $productos_listos++;
                        } else {
                            $productos_pendientes++;
                            $nombres_pendientes[] = $det['nombre'];
                            $falt = max(0, $cant - $cantEnt);
                            if ($falt > 0) {
                                $faltantes_list[] = $det['nombre'] . ' (-' . $falt . ')';
                            }
                        }
                    }
                    
                    // Permitir Marcar Empaquetado si está en en_pedido, alistado o empaquetado Y hay al menos un producto listo
                    $puede_marcar_empaquetado = in_array($pedido['estado'], ['en_pedido', 'alistado', 'empaquetado']) && $productos_listos > 0;
                    
                    // Permitir Desempaquetar si está en empaquetado, verificado o en_reparto (siempre activo)
                    $puede_desempaquetar = in_array($pedido['estado'], ['empaquetado', 'verificado', 'en_reparto']);
                    
                    // Variable para usar en el botón
                    $puede_empaquetar = $pedido['estado'] === 'empaquetado' ? $puede_desempaquetar : $puede_marcar_empaquetado;
                ?>
                <button type="button" class="btn btn-lg btn-empaquetado-<?php echo $pedido['id']; ?>" 
                        style="flex: 1; max-width: 280px; background-color: #28a745; color: white; border: none; padding: 0.8rem 1.5rem; font-size: 0.95rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);"
                        onclick="<?php echo $pedido['estado'] === 'empaquetado' ? 'desempaquetar(' . $pedido['id'] . ')' : 'marcarEmpaquetadoLista(' . $pedido['id'] . ')'; ?>"
                        <?php 
                            // Si es empaquetado, verificado o en_reparto → SIEMPRE ACTIVO (para desempaquetar)
                            // Si NO es empaquetado → activo solo si puede marcar empaquetado
                            if (in_array($pedido['estado'], ['empaquetado', 'verificado', 'en_reparto'])) {
                                // Desempaquetar siempre activo
                                echo '';
                            } else {
                                // Marcar empaquetado solo si tiene productos listos
                                echo !$puede_marcar_empaquetado ? 'disabled' : '';
                            }
                        ?>>
                    <i class="bi bi-<?php echo $pedido['estado'] === 'empaquetado' ? 'arrow-counterclockwise' : 'box-seam'; ?>"></i> <?php echo $pedido['estado'] === 'empaquetado' ? 'Desempaquetar' : 'Marcar Empaquetado'; ?>
                </button>
                <button type="button" class="btn btn-lg" 
                        style="flex: 1; max-width: 280px; background-color: #28a745; color: white; border: none; padding: 0.8rem 1.5rem; font-size: 0.95rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);"
                        data-bs-toggle="modal" 
                        data-bs-target="#modalVerificarPedido"
                        data-pedido-id="<?php echo $pedido['id']; ?>"
                        data-cliente-nombre="<?php echo htmlspecialchars($pedido['nombre'] ?? $pedido['cliente_nombre'] ?? 'Sin nombre', ENT_QUOTES, 'UTF-8'); ?>"
                        data-total="<?php echo number_format($pedido['total'], 2); ?>"
                        data-productos-count="<?php echo count($pedido['detalles']); ?>"
                        data-productos-listos="<?php echo $productos_listos; ?>"
                        data-productos-pendientes="<?php echo $productos_pendientes; ?>"
                        data-nombres-pendientes="<?php echo htmlspecialchars(json_encode($nombres_pendientes), ENT_QUOTES, 'UTF-8'); ?>"
                        data-cant-total="<?php echo $cantidad_total; ?>"
                        data-cant-entregada="<?php echo $cantidad_entregada_total; ?>"
                        data-cant-faltante="<?php echo max(0, $cantidad_total - $cantidad_entregada_total); ?>"
                        data-faltantes-list="<?php echo htmlspecialchars(json_encode($faltantes_list), ENT_QUOTES, 'UTF-8'); ?>"
                        onclick="abrirModalVerificacion(this)"
                        <?php echo !$puede_verificar ? 'disabled' : ''; ?>>
                    <i class="bi bi-search"></i> <?php echo $ya_verificado ? 'Verificado' : 'Marcar Verificado'; ?>
                </button>
                <button type="button" class="btn btn-lg" 
                        style="flex: 1; max-width: 280px; background-color: #17a2b8; color: white; border: none; padding: 0.8rem 1.5rem; font-size: 0.95rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3);"
                        onclick="marcarListoEntrega(<?php echo $pedido['id']; ?>)"
                        <?php echo !$ya_verificado || $ya_listo_entrega ? 'disabled' : ''; ?>>
                    <i class="bi bi-truck"></i> <?php echo $ya_listo_entrega ? 'Listo para Entrega' : 'Marcar Listo para Entrega'; ?>
                </button>
            </div>
            <?php endif; ?>

            <!-- Total del pedido -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="text-success mb-0">
                        <i class="bi bi-cash-coin"></i> $<?php echo number_format($pedido['total'], 2); ?>
                    </h5>
                </div>
                <div class="col-md-6 text-md-end">
                    <!-- Selector de estado -->
                    <form method="POST" action="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=verPedido&id=<?php echo $pedido['id']; ?>" 
                          class="d-inline me-2 form-cambiar-estado" style="min-width: 170px;" id="formEstado<?php echo $pedido['id']; ?>">
                        <select name="estado" class="form-select form-select-sm select-estado" data-pedido-id="<?php echo $pedido['id']; ?>">
                            <option value="" disabled <?php echo !$pedido['estado'] ? 'selected' : ''; ?>>Cambiar estado...</option>
                            <option value="en_pedido" <?php echo $pedido['estado'] === 'en_pedido' ? 'selected' : ''; ?>>⏳ Pendientes</option>
                            <option value="alistado" <?php echo $pedido['estado'] === 'alistado' ? 'selected' : ''; ?>>👨‍🍳 En preparación</option>
                            <option value="en_reparto" <?php echo $pedido['estado'] === 'en_reparto' ? 'selected' : ''; ?>>🚚 Listos para entrega</option>
                            <option value="entregado" <?php echo $pedido['estado'] === 'entregado' ? 'selected' : ''; ?>>✔️ Entregados</option>
                            <option value="cancelado" <?php echo $pedido['estado'] === 'cancelado' ? 'selected' : ''; ?>>❌ Cancelados</option>
                        </select>
                    </form>

                    <!-- Botones de acción -->
                    <a href="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=verPedido&id=<?php echo $pedido['id']; ?>" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> Ver
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
function filtrarPorEstado(estado) {
    const url = '<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=pedidos';
    if (estado) {
        window.location.href = url + '&estado=' + encodeURIComponent(estado);
    } else {
        window.location.href = url;
    }
}

function cambiarPreparacion(btn) {
    const estadoActual = btn.dataset.estado;
    const nuevoEstado = estadoActual === 'listo' ? 'pendiente' : 'listo';
    const pedidoId = btn.dataset.pedidoId;
    const detalleId = btn.dataset.detalleId;

    const formData = new FormData();
    formData.append('pedido_id', pedidoId);
    formData.append('detalle_id', detalleId);
    formData.append('estado', nuevoEstado);

    fetch('<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=actualizarEstadoPreparacion', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            alert(data.error || 'No se pudo actualizar');
            return;
        }

        // Actualizar botón
        btn.dataset.estado = nuevoEstado;
        btn.innerHTML = `<i class="bi bi-${nuevoEstado === 'listo' ? 'check-circle' : 'clock'}"></i> ${nuevoEstado === 'listo' ? 'Listo' : 'Pendiente'}`;
        btn.style.backgroundColor = nuevoEstado === 'listo' ? '#28a745' : '#6c757d';
        btn.style.color = 'white';

        // Actualizar badges del header y barra
        const card = btn.closest('.card');
        const badgeCounts = card.querySelectorAll('span.badge.bg-light, span.badge.bg-info');
        if (badgeCounts.length === 2) {
            badgeCounts[0].innerHTML = `<strong>${data.listos}/${data.total}</strong>`;
            badgeCounts[1].textContent = `${data.porcentaje}%`;
        }
        const progressBar = card.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = data.porcentaje + '%';
            progressBar.setAttribute('aria-valuenow', data.porcentaje);
        }

        // Actualizar estado del botón Empaquetado dinámicamente
        const btnEmpaquetado = card.querySelector(`.btn-empaquetado-${pedidoId}`);
        if (btnEmpaquetado) {
            // Habilitar si hay AL MENOS UN producto listo (listos > 0)
            if (data.listos > 0) {
                btnEmpaquetado.disabled = false;
            } else {
                btnEmpaquetado.disabled = true;
            }
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error al procesar la solicitud');
    });
}

function actualizarCantidadesVisual(input) {
    const max = parseInt(input.getAttribute('max')) || 0;
    let val = parseInt(input.value || '0');
    if (val < 0) val = 0;
    if (val > max) val = max;
    input.value = val;
    const row = input.closest('.list-group-item');
    if (row) {
        const ent = row.querySelector('.cant-entregada');
        const falt = row.querySelector('.cant-faltan');
        if (ent) ent.textContent = val;
        if (falt) falt.textContent = Math.max(0, max - val);
    }
}

function guardarCantidadEntregada(btn) {
    const pedidoId = btn.dataset.pedidoId;
    const detalleId = btn.dataset.detalleId;
    const row = btn.closest('.list-group-item');
    const input = row ? row.querySelector('.input-cant-ent') : null;
    if (!input) return;
    const cantidad = parseInt(input.value || '0');

    const formData = new FormData();
    formData.append('pedido_id', pedidoId);
    formData.append('detalle_id', detalleId);
    formData.append('estado', 'pendiente'); // será ajustado por el server según cantidades
    formData.append('cantidad_entregada', cantidad);

    fetch('<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=actualizarEstadoPreparacion', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            alert(data.error || 'No se pudo actualizar');
            return;
        }
        // Refrescar progreso visual de la card
        const card = btn.closest('.card');
        const badgeCounts = card.querySelectorAll('span.badge.bg-light, span.badge.bg-info');
        if (badgeCounts.length === 2) {
            badgeCounts[0].innerHTML = `<strong>${data.listos}/${data.total}</strong>`;
            badgeCounts[1].textContent = `${data.porcentaje}%`;
        }
        const progressBar = card.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = data.porcentaje + '%';
            progressBar.setAttribute('aria-valuenow', data.porcentaje);
        }
        // Ajustar botón empaquetado
        const btnEmpaquetado = card.querySelector(`.btn-empaquetado-${pedidoId}`);
        if (btnEmpaquetado) {
            btnEmpaquetado.disabled = !(data.listos > 0);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error al procesar la solicitud');
    });
}

function marcarEmpaquetadoLista(pedidoId) {
    const formData = new FormData();
    formData.append('pedido_id', pedidoId);
    formData.append('estado', 'empaquetado');
    
    fetch('<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=actualizarEstadoPedido', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al actualizar');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error al procesar');
    });
}

function desempaquetar(pedidoId) {
    if (!confirm('¿Regresar este pedido a estado Alistado?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('pedido_id', pedidoId);
    formData.append('estado', 'alistado');
    
    fetch('<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=actualizarEstadoPedido', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al desempaquetar');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error al procesar');
    });
}

function verificarPedidoLista(pedidoId, observaciones = '') {
    const formData = new FormData();
    formData.append('pedido_id', pedidoId);
    formData.append('estado', 'verificado');
    formData.append('observaciones', observaciones);
    
    fetch('<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=actualizarEstadoPedido', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al verificar');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error al procesar');
    });
}

function abrirModalVerificacion(btn) {
    // Leer datos del botón
    const pedidoId = btn.dataset.pedidoId;
    const nombre = btn.dataset.clienteNombre;
    const total = btn.dataset.total;
    const productosCount = btn.dataset.productosCount;
    const productosListos = parseInt(btn.dataset.productosListos);
    const productosPendientes = parseInt(btn.dataset.productosPendientes);
    const nombresPendientesJson = btn.dataset.nombresPendientes;
    const cantTotal = parseInt(btn.dataset.cantTotal || '0');
    const cantEntregada = parseInt(btn.dataset.cantEntregada || '0');
    const cantFaltante = parseInt(btn.dataset.cantFaltante || '0');
    const faltantesListJson = btn.dataset.faltantesList || '[]';
    
    document.getElementById('modalClienteNombre').textContent = nombre;
    document.getElementById('modalPedidoTotal').textContent = '$' + parseFloat(total).toLocaleString('es-CO', {minimumFractionDigits: 2});
    document.getElementById('modalPedidoProductos').textContent = productosCount;
    document.getElementById('btnConfirmarVerificacion').dataset.pedidoId = pedidoId;
    
    // Parsear nombres de productos pendientes
    let nombresPendientes = [];
    try {
        nombresPendientes = JSON.parse(nombresPendientesJson);
        console.log('Nombres pendientes parseados:', nombresPendientes);
    } catch(e) {
        console.error('Error parseando nombres pendientes:', e);
        console.log('JSON recibido:', nombresPendientesJson);
    }
    
    // Generar observaciones automáticas
    let faltantesDet = [];
    try { faltantesDet = JSON.parse(faltantesListJson); } catch(e) { faltantesDet = []; }

    let observaciones = '';
    if (cantFaltante > 0) {
        observaciones = `⚠️ PEDIDO INCOMPLETO\n\n`;
        observaciones += `✅ Cantidad enviada: ${cantEntregada} de ${cantTotal}\n`;
        observaciones += `❌ Cantidad faltante: ${cantFaltante}\n`;
        if (faltantesDet.length > 0) {
            observaciones += `\nFaltantes por producto:\n`;
            faltantesDet.forEach(linea => {
                observaciones += `  • ${linea}\n`;
            });
        } else if (nombresPendientes.length > 0) {
            observaciones += `\nProductos NO entregados:\n`;
            nombresPendientes.forEach(prod => { observaciones += `  • ${prod}\n`; });
        }
        observaciones += `\nSe cobra solo por lo enviado (${cantEntregada}).`;
    } else {
        observaciones = `✅ PEDIDO COMPLETO\n\n`;
        observaciones += `Se envían ${cantEntregada} de ${cantTotal}. Todos listos.`;
    }
    
    document.getElementById('modalObservaciones').value = observaciones;
    
    const modal = new bootstrap.Modal(document.getElementById('modalVerificarPedido'));
    modal.show();
}

function confirmarVerificacion() {
    const pedidoId = document.getElementById('btnConfirmarVerificacion').dataset.pedidoId;
    const observaciones = document.getElementById('modalObservaciones').value;
    
    // Cerrar el modal correctamente
    const modalElement = document.getElementById('modalVerificarPedido');
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }
    
    // Limpiar backdrop manualmente por si acaso
    setTimeout(() => {
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }, 300);
    
    // Limpiar observaciones para próximo uso
    document.getElementById('modalObservaciones').value = '';
    
    verificarPedidoLista(pedidoId, observaciones);
}

function marcarListoEntrega(pedidoId) {
    if (!confirm('¿Marcar este pedido como listo para entrega?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('pedido_id', pedidoId);
    formData.append('estado', 'en_reparto');
    formData.append('observaciones', 'Pedido listo para entrega');
    
    fetch('<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=actualizarEstadoPedido', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al marcar listo para entrega');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error al procesar');
    });
}
</script>

<!-- Modal de Verificación de Pedido -->
<div class="modal fade" id="modalVerificarPedido" tabindex="-1" aria-labelledby="labelModalVerificar" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="labelModalVerificar">Verificar Pedido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" role="alert">
                    <h5>¡Excelente! Todo en orden</h5>
                    <p class="mb-0">Todos los productos están listos para empaquetar.</p>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Cliente:</strong>
                        <p id="modalClienteNombre"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Total:</strong>
                        <p id="modalPedidoTotal"></p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Productos:</strong>
                    <p id="modalPedidoProductos" class="badge bg-info"></p>
                </div>
                
                <div class="mb-3">
                    <label for="modalObservaciones" class="form-label">Observaciones (opcional):</label>
                    <textarea class="form-control" id="modalObservaciones" rows="2" placeholder="Notas sobre el pedido..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarVerificacion" onclick="confirmarVerificacion()">Verificar Pedido</button>
            </div>
        </div>
    </div>
</div>

<script>
// Limpiar backdrop cuando se cierra el modal de verificación
document.addEventListener('DOMContentLoaded', function() {
    const modalVerificar = document.getElementById('modalVerificarPedido');
    if (modalVerificar) {
        modalVerificar.addEventListener('hidden.bs.modal', function () {
            // Limpiar cualquier backdrop residual
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
    }
    
    // Manejar cambio de estado desde el dropdown
    document.querySelectorAll('.select-estado').forEach(function(select) {
        select.addEventListener('change', function() {
            if (this.value) {
                const form = this.closest('form');
                console.log('Enviando formulario:', form.action);
                form.submit();
            }
        });
    });
});
</script>

<?php
include APP_ROOT . '/app/views/admin/layout/footer.php';
?>
