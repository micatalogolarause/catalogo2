<?php
include APP_ROOT . '/app/views/admin/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-gear-fill"></i> Configuración de la Tienda</h3>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Personalización</h5>
                
                <form method="POST" action="<?php echo APP_URL; ?>/<?php echo TENANT_SLUG; ?>/index.php?controller=admin&action=actualizarConfiguracion">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Título de la Empresa</label>
                        <input type="text" class="form-control" name="titulo_empresa" 
                               value="<?php echo sanitizar($tenant['titulo_empresa'] ?? ''); ?>" 
                               placeholder="<?php echo sanitizar($tenant['nombre']); ?>">
                        <small class="text-muted">
                            Este nombre aparecerá en el encabezado de tu tienda. Si lo dejas vacío, se usará el nombre del tenant.
                        </small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold"><i class="bi bi-whatsapp"></i> Número de WhatsApp</label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white">+57</span>
                            <input type="tel" class="form-control" name="whatsapp_phone" id="whatsapp_phone"
                                   value="<?php echo !empty($tenant['whatsapp_phone']) ? sanitizar(str_replace('+57', '', trim($tenant['whatsapp_phone']))) : ''; ?>" 
                                   placeholder="300 123 4567"
                                   pattern="[0-9\s]+">
                        </div>
                        <small class="text-muted">
                            Número de WhatsApp para recibir pedidos (solo dígitos, sin +57).
                        </small>
                    </div>
                    
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const whatsappInput = document.getElementById('whatsapp_phone');
                        
                        // Solo permitir números y espacios
                        whatsappInput.addEventListener('input', function(e) {
                            this.value = this.value.replace(/[^0-9\s]/g, '');
                        });
                    });
                    </script>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estilo de Tema</label>
                            <select class="form-select" name="tema" id="tema">
                                <option value="claro" <?php echo ($tenant['tema'] ?? 'claro') === 'claro' ? 'selected' : ''; ?>>
                                    ☀️ Claro
                                </option>
                                <option value="oscuro" <?php echo ($tenant['tema'] ?? 'claro') === 'oscuro' ? 'selected' : ''; ?>>
                                    🌙 Oscuro
                                </option>
                            </select>
                            <small class="text-muted">Fondo claro u oscuro para tu tienda</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Color Principal</label>
                            <select class="form-select" name="tema_color" id="tema_color">
                                <option value="azul" <?php echo ($tenant['tema_color'] ?? 'azul') === 'azul' ? 'selected' : ''; ?>>
                                    🔵 Azul
                                </option>
                                <option value="verde" <?php echo ($tenant['tema_color'] ?? 'azul') === 'verde' ? 'selected' : ''; ?>>
                                    🟢 Verde
                                </option>
                                <option value="rojo" <?php echo ($tenant['tema_color'] ?? 'azul') === 'rojo' ? 'selected' : ''; ?>>
                                    🔴 Rojo
                                </option>
                                <option value="morado" <?php echo ($tenant['tema_color'] ?? 'azul') === 'morado' ? 'selected' : ''; ?>>
                                    🟣 Morado
                                </option>
                                <option value="naranja" <?php echo ($tenant['tema_color'] ?? 'azul') === 'naranja' ? 'selected' : ''; ?>>
                                    🟠 Naranja
                                </option>
                                <option value="marino" <?php echo ($tenant['tema_color'] ?? 'azul') === 'marino' ? 'selected' : ''; ?>>
                                    🟦 Marino (corporativo)
                                </option>
                                <option value="grafito" <?php echo ($tenant['tema_color'] ?? 'azul') === 'grafito' ? 'selected' : ''; ?>>
                                    ⬛ Grafito (corporativo)
                                </option>
                                <option value="petroleo" <?php echo ($tenant['tema_color'] ?? 'azul') === 'petroleo' ? 'selected' : ''; ?>>
                                    🟩 Petróleo (corporativo)
                                </option>
                                <option value="acero" <?php echo ($tenant['tema_color'] ?? 'azul') === 'acero' ? 'selected' : ''; ?>>
                                    🟦 Acero (corporativo)
                                </option>
                                <option value="gris" <?php echo ($tenant['tema_color'] ?? 'azul') === 'gris' ? 'selected' : ''; ?>>
                                    ⚪ Gris (corporativo)
                                </option>
                            </select>
                            <small class="text-muted">Color del encabezado y botones</small>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Vista previa:</strong> Los cambios se aplicarán después de guardar. Para ver los cambios en la tienda, 
                        <a href="<?php echo APP_URL; ?>/<?php echo sanitizar($_SESSION['tenant_slug']); ?>" target="_blank" class="alert-link">
                            haz clic aquí para abrir tu tienda en una nueva pestaña
                        </a>.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </button>
                        <a href="<?php echo APP_URL; ?>/index.php?controller=admin&action=inicio" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver al Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">🎨 Paleta de Colores</h5>
                <p class="text-muted small">Vista previa de los colores disponibles:</p>
                
                <div class="mb-3">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <div style="width:40px; height:40px; background: linear-gradient(135deg, #5e72e4 0%, #825ee4 100%); border-radius:8px;"></div>
                        <strong>Azul</strong>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <div style="width:40px; height:40px; background: linear-gradient(135deg, #2dce89 0%, #11cdef 100%); border-radius:8px;"></div>
                        <strong>Verde</strong>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <div style="width:40px; height:40px; background: linear-gradient(135deg, #f5365c 0%, #fb6340 100%); border-radius:8px;"></div>
                        <strong>Rojo</strong>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <div style="width:40px; height:40px; background: linear-gradient(135deg, #8965e0 0%, #bc65e0 100%); border-radius:8px;"></div>
                        <strong>Morado</strong>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <div style="width:40px; height:40px; background: linear-gradient(135deg, #fb6340 0%, #ffa500 100%); border-radius:8px;"></div>
                        <strong>Naranja</strong>
                    </div>
                </div>

                <div class="mb-3">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <div style="width:40px; height:40px; background: linear-gradient(135deg, #1f3b70 0%, #2a5298 100%); border-radius:8px;"></div>
                        <strong>Marino (corporativo)</strong>
                    </div>
                </div>

                <div class="mb-3">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <div style="width:40px; height:40px; background: linear-gradient(135deg, #3a3f44 0%, #5a6268 100%); border-radius:8px;"></div>
                        <strong>Grafito (corporativo)</strong>
                    </div>
                </div>

                <div class="mb-3">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <div style="width:40px; height:40px; background: linear-gradient(135deg, #0f4c5c 0%, #2c7a7b 100%); border-radius:8px;"></div>
                        <strong>Petróleo (corporativo)</strong>
                    </div>
                </div>

                <div class="mb-3">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <div style="width:40px; height:40px; background: linear-gradient(135deg, #4682b4 0%, #5f9ea0 100%); border-radius:8px;"></div>
                        <strong>Acero (corporativo)</strong>
                    </div>
                </div>

                <div class="mb-3">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <div style="width:40px; height:40px; background: linear-gradient(135deg, #6c757d 0%, #495057 100%); border-radius:8px;"></div>
                        <strong>Gris (corporativo)</strong>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title mb-3">ℹ️ Información</h5>
                <p class="small text-muted">
                    <strong>Tenant:</strong> <?php echo sanitizar($tenant['nombre']); ?><br>
                    <strong>Slug:</strong> <?php echo sanitizar($tenant['slug']); ?><br>
                    <strong>Estado:</strong> 
                    <span class="badge bg-<?php echo $tenant['estado'] === 'activo' ? 'success' : 'danger'; ?>">
                        <?php echo ucfirst($tenant['estado']); ?>
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<?php
include APP_ROOT . '/app/views/admin/layout/footer.php';
?>
