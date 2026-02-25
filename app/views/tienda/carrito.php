<?php
include APP_ROOT . '/app/views/layout/header.php';
?>

<div class="row">
    <div class="col-lg-8">
        <h1 class="mb-4"><i class="bi bi-cart-check"></i> Carrito de Compras</h1>
        <div class="table-responsive mt-4">
            <table class="table table-hover align-middle" id="tabla-carrito">
                <thead class="table-dark">
                    <tr>
                        <th style="min-width: 200px;">Producto</th>
                        <th class="d-none d-md-table-cell text-center" style="width: 100px;">Precio</th>
                        <th style="width: 200px;">Cantidad</th>
                        <th class="d-none d-md-table-cell text-center" style="width: 100px;">Subtotal</th>
                        <th class="text-center" style="width: 80px;">Acción</th>
                    </tr>
                </thead>
                <tbody id="carrito-items">
                    <tr class="text-center">
                        <td colspan="5"><small class="text-muted">Cargando carrito...</small></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card sticky-lg-top" style="top: 100px;">
            <div class="card-header">
                <h5 class="mb-0">Resumen del Pedido</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total:</strong>
                    <strong id="total" class="text-primary h5">$0.00</strong>
                </div>
                <a href="<?php echo APP_URL . '/' . sanitizar($_SESSION['tenant_slug']); ?>" class="btn btn-outline-secondary btn-sm w-100 mb-2">
                    <i class="bi bi-arrow-left"></i> Seguir Comprando
                </a>
                <button class="btn btn-danger btn-sm w-100 mb-3" id="btn-vaciar">
                    <i class="bi bi-trash"></i> Vaciar Carrito
                </button>
                <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                    <i class="bi bi-whatsapp"></i> Confirmar Pedido
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Checkout -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                 <h5 class="modal-title">Confirmar Pedido por WhatsApp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-checkout" method="POST" action="<?php echo APP_URL; ?>/index.php?controller=tienda&action=checkout">
                <div class="modal-body">
                    <h6 class="mb-3">Información de Envío</h6>
                        <p class="alert alert-info"><i class="bi bi-info-circle"></i> No se realizará pago en línea. Solo necesitamos tu nombre y WhatsApp.</p>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="whatsapp" class="form-label">WhatsApp (incluir código de país) *</label>
                        <div class="input-group">
                            <select class="form-select" id="whatsapp_codigo" name="whatsapp_codigo" style="max-width: 140px;" required>
                                <?php
                                $paises = include APP_ROOT . '/config/paises.php';
                                if (!is_array($paises)) {
                                    $paises = array(
                                        '+57' => 'Colombia',
                                        '+1' => 'Estados Unidos / Canadá',
                                        '+34' => 'España',
                                        '+52' => 'México',
                                        '+55' => 'Brasil',
                                    );
                                }
                                foreach ($paises as $codigo => $pais):
                                    $selected = ($codigo === '+57') ? 'selected' : '';
                                ?>
                                <option value="<?php echo $codigo; ?>" <?php echo $selected; ?>>
                                    <?php echo $codigo; ?> - <?php echo substr($pais, 0, 15); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="tel" class="form-control" id="whatsapp" name="whatsapp" 
                                   placeholder="10 dígitos" required>
                        </div>
                        <small class="text-muted">Recibirás confirmación en WhatsApp</small>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="btn-procesar-pago">
                            <i class="bi bi-send"></i> Enviar Confirmación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Cargar carrito
function cargarCarrito() {
    fetch('<?php echo APP_URL; ?>/index.php?controller=api&action=obtenerCarrito', { method: 'GET' })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos del carrito:', data);
            let html = '';
            if (!data.items || data.items.length === 0) {
                html = '<tr class="text-center"><td colspan="5"><div class="alert alert-info"><i class="bi bi-cart-x"></i> El carrito está vacío. <a href="<?php echo APP_URL; ?>">¡Empieza a comprar!</a></div></td></tr>';
                document.getElementById('subtotal').textContent = '$0.00';
                document.getElementById('total').textContent = '$0.00';
            } else {
                let total = 0;
                // Invertir el orden de los items para mostrar del último al primero
                data.items.reverse().forEach(item => {
                    total += parseFloat(item.subtotal);
                    html += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${item.imagen}" style="width:50px; height:50px; object-fit:cover;" class="me-2 rounded">
                                    <div>
                                        <div class="fw-bold">${item.nombre}</div>
                                        <small class="text-muted d-md-none">$${parseFloat(item.precio).toFixed(2)}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell text-center">$${parseFloat(item.precio).toFixed(2)}</td>
                            <td>
                                <div class="d-flex flex-column gap-2" style="min-width: 180px;">
                                    <div class="input-group input-group-sm" style="width:100px;">
                                        <input type="number" class="form-control text-center" 
                                               style="font-weight: bold; font-size: 1.1em;" 
                                               value="${item.cantidad}" min="1" max="${item.stock}"
                                               onchange="validarYActualizarCarrito(${item.producto_id}, this)">
                                        <span class="input-group-text" style="font-size: 0.8em; color: #666;">/${item.stock}</span>
                                    </div>
                                    <div class="btn-group btn-group-sm d-flex flex-wrap gap-1" role="group" style="max-width: 320px;">
                                        <button type="button" class="btn btn-outline-primary flex-fill" onclick="actualizarCarrito(${item.producto_id}, Math.min(12, ${item.stock}))">12</button>
                                        <button type="button" class="btn btn-outline-primary flex-fill" onclick="actualizarCarrito(${item.producto_id}, Math.min(24, ${item.stock}))">24</button>
                                        <button type="button" class="btn btn-outline-primary flex-fill" onclick="actualizarCarrito(${item.producto_id}, Math.min(36, ${item.stock}))">36</button>
                                        <button type="button" class="btn btn-outline-primary flex-fill" onclick="actualizarCarrito(${item.producto_id}, Math.min(48, ${item.stock}))">48</button>
                                        <button type="button" class="btn btn-outline-primary flex-fill" onclick="actualizarCarrito(${item.producto_id}, Math.min(60, ${item.stock}))">60</button>
                                        <button type="button" class="btn btn-outline-primary flex-fill" onclick="actualizarCarrito(${item.producto_id}, Math.min(72, ${item.stock}))">72</button>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell text-center fw-bold">$${parseFloat(item.subtotal).toFixed(2)}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger" onclick="eliminarDelCarrito(${item.producto_id})" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                document.getElementById('subtotal').textContent = '$' + total.toFixed(2);
                document.getElementById('total').textContent = '$' + total.toFixed(2);
            }
            document.getElementById('carrito-items').innerHTML = html;
        })
        .catch(error => {
            console.error('Error al cargar carrito:', error);
            document.getElementById('carrito-items').innerHTML = '<tr class="text-center"><td colspan="5"><div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Error al cargar el carrito. Por favor, recarga la página.</div></td></tr>';
        });
}

function actualizarCarrito(productoId, cantidad) {
    fetch('<?php echo APP_URL; ?>/index.php?controller=api&action=actualizarCarrito', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'producto_id=' + productoId + '&cantidad=' + cantidad
    })
    .then(() => {
        cargarCarrito();
        actualizarCarroBadge();
    })
    .catch(error => console.error('Error:', error));
}

function validarYActualizarCarrito(productoId, input) {
    const cantidad = parseInt(input.value);
    const maxStock = parseInt(input.max);
    
    if (isNaN(cantidad) || cantidad < 1) {
        input.value = 1;
        actualizarCarrito(productoId, 1);
    } else if (cantidad > maxStock) {
        alert(`La cantidad no puede exceder el stock disponible (${maxStock})`);
        input.value = maxStock;
        actualizarCarrito(productoId, maxStock);
    } else {
        actualizarCarrito(productoId, cantidad);
    }
}

function eliminarDelCarrito(productoId) {
    fetch('<?php echo APP_URL; ?>/index.php?controller=api&action=eliminarDelCarrito', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'producto_id=' + productoId
    })
    .then(() => {
        cargarCarrito();
        actualizarCarroBadge();
    })
    .catch(error => console.error('Error:', error));
}

const btnVaciar = document.getElementById('btn-vaciar');
if (btnVaciar) {
    btnVaciar.addEventListener('click', function() {
        if (confirm('¿Estás seguro de que deseas vaciar el carrito?')) {
            fetch('<?php echo APP_URL; ?>/index.php?controller=api&action=vaciarCarrito', { method: 'POST' })
                .then(() => {
                    cargarCarrito();
                    actualizarCarroBadge();
                })
                .catch(error => console.error('Error:', error));
        }
    });
}

// Inicializar form checkout solo si existe
const formCheckout = document.getElementById('form-checkout');
if (formCheckout) {
    formCheckout.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Obtener carrito actual
        fetch('<?php echo APP_URL; ?>/index.php?controller=api&action=obtenerCarrito', { method: 'GET' })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(carritoData => {
            if (!carritoData.items || carritoData.items.length === 0) {
                alert('El carrito está vacío');
                return;
            }
            
            let formData = new FormData(formCheckout);
            // Agregar carrito como JSON (usar items del API)
            formData.append('carrito_json', JSON.stringify(carritoData.items));
            document.getElementById('btn-procesar-pago').disabled = true;
            
            fetch('<?php echo APP_URL; ?>/index.php?controller=tienda&action=checkout', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en respuesta del servidor: ' + response.status);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Respuesta no válida:', text);
                        throw new Error('Respuesta inválida del servidor: ' + text);
                    }
                });
            })
            .then(data => {
                document.getElementById('btn-procesar-pago').disabled = false;
                if (data.success) {
                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('checkoutModal'));
                    if (modal) modal.hide();
                    
                    // Mostrar mensaje de confirmación
                    alert('✅ ¡Pedido #' + data.pedido_id + ' confirmado!\n\n📱 Se abrirá WhatsApp para que recibas tu cuenta de cobro.');
                    
                    // Limpiar carrito localmente
                    localStorage.removeItem('carrito');
                    sessionStorage.clear();
                    
                    // Abrir WhatsApp con el mensaje
                    if (data.whatsapp_link) {
                        window.open(data.whatsapp_link, '_blank');
                    }
                    
                    // Esperar 1 segundo y redirigir a la página principal
                    setTimeout(function() {
                        window.location.href = '<?php echo APP_URL; ?>';
                    }, 1000);
                } else {
                    alert('Error: ' + (data.message || 'Error desconocido al procesar el pedido'));
                }
            })
            .catch(error => {
                document.getElementById('btn-procesar-pago').disabled = false;
                console.error('Error completo:', error);
                alert('Error al procesar el pedido: ' + error.message);
            });
        })
        .catch(error => {
            console.error('Error al cargar el carrito:', error);
            alert('Error al cargar el carrito: ' + error.message);
        });
    });
}

cargarCarrito();
</script>

<?php
include APP_ROOT . '/app/views/layout/footer.php';
?>
