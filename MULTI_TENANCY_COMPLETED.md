# 🏢 MULTI-TENANCY IMPLEMENTATION - PROYECTO COMPLETADO

**Estado:** ✅ **COMPLETAMENTE FUNCIONAL**  
**Fecha:** 09 de Enero de 2026  
**Base de Datos:** MySQL/MariaDB 10.4.32  
**Base de Datos:** `catalogo_tienda`

---

## 📋 RESUMEN DE IMPLEMENTACIÓN

Se ha implementado un sistema de **multi-tenancy con aislamiento en base de datos única** para la plataforma de e-commerce. Cada tenant tiene acceso garantizado solo a sus datos a través de filtros a nivel de query SQL.

---

## 🏗️ ARQUITECTURA

### Modelo de Tenancy
- **Tipo:** Single Database, Multiple Tenants (SDMT)
- **Aislamiento:** Column-based (tenant_id en cada tabla)
- **Routing:** URL slug pattern `/catalogo2/{tenant-slug}`

### Componentes Clave

#### 1. **TenantResolver.php** (Middleware)
- **Ubicación:** `config/TenantResolver.php`
- **Función:** Detecta tenant desde URL y carga configuración
- **Constantes Globales:**
  - `TENANT_ID` - ID del tenant actual
  - `TENANT_SLUG` - Slug del tenant (parte de la URL)
  - `TENANT_NAME` - Nombre legible del tenant
  - `TENANT_WHATSAPP` - Número de WhatsApp del tenant

#### 2. **Database Helpers** (Scoped Queries)
- **Ubicación:** `config/database.php`
- **Funciones:**
  - `getTenantId()` - Obtiene ID del tenant actual
  - `ejecutarConsultaScoped($sql, $tipos, $params)` - Prepend tenant_id automáticamente
  - `obtenerFilaScoped($sql, $tipos, $params)` - SELECT único con tenant_id
  - `obtenerFilasScoped($sql, $tipos, $params)` - SELECT múltiple con tenant_id
  - `validarPerteneceATenant($tabla, $id)` - Valida que recurso pertenece al tenant

#### 3. **Integración en index.php**
```php
require_once 'config/TenantResolver.php';
TenantResolver::resolve(); // Debe ejecutarse después de database.php
```

---

## 📊 ESTRUCTURA DE BASE DE DATOS

### Tabla: `tenants`
```sql
CREATE TABLE tenants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) UNIQUE,
    slug VARCHAR(50) UNIQUE,        -- URL identifier (default, mauricio, distribuciones-ebs)
    whatsapp_phone VARCHAR(20),     -- WhatsApp del tenant
    logo VARCHAR(255),
    tema ENUM('claro', 'oscuro'),
    estado ENUM('activo', 'inactivo', 'bloqueado'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tablas con Aislamiento (tenant_id)
- `categorias` - Categorías de productos
- `subcategorias` - Subcategorías de productos
- `productos` - Inventario
- `clientes` - Clientes/Usuarios del tenant
- `carrito` - Carritos de compra
- `pedidos` - Órdenes
- `pedido_detalles` - Detalles de órdenes
- `pedido_historial` - Historial de cambios
- `usuarios` - Usuarios administrativos

**Constraint:** Cada tabla tiene `FOREIGN KEY (tenant_id) REFERENCES tenants(id)`

---

## 👥 TENANTS DISPONIBLES

| ID | Nombre | Slug | WhatsApp | Productos | Clientes | Pedidos |
|----|--------|------|----------|-----------|----------|---------|
| 1 | Tienda Default | `default` | 573112969569 | 11 | 7 | 9 |
| 2 | Mauricio | `mauricio` | 573112969569 | 2 | 2 | 1 |
| 3 | Distribuciones EBS | `distribuciones-ebs` | 573001234567 | 2 | 2 | 1 |

### URLs de Acceso
- **Default:** http://localhost/catalogo2
- **Mauricio:** http://localhost/catalogo2/mauricio
- **EBS:** http://localhost/catalogo2/distribuciones-ebs

---

## 🔒 SEGURIDAD - AISLAMIENTO GARANTIZADO

### En Modelos
Todas las consultas incluyen `tenant_id` en WHERE clause:

```php
// ProductoModel.php - ejemplo
public function obtenerTodos() {
    return obtenerFilasScoped(
        "SELECT * FROM productos WHERE activo = 1",
        "i",
        [1] // 1 = solo activos
    );
}
// Internamente agrega: AND tenant_id = getTenantId()
```

### En Controladores
Cada operación valida pertenencia al tenant:

```php
// tiendaController.php
$cliente = ClienteModel::obtenerPorId($cliente_id); // Incluye validación tenant_id
$pedidos = PedidoModel::obtenerTodos($estado); // Filtra por tenant automáticamente
```

### En Carrito y Checkout
- Productos verificados por `tenant_id` antes de agregar al carrito
- Pedidos creados con `tenant_id` del tenant actual
- WhatsApp utiliza número del tenant, no del cliente

---

## 🛒 FLUJO DE CHECKOUT POR TENANT

### 1. Resolución de Tenant (index.php)
```php
TenantResolver::resolve();
// TENANT_ID, TENANT_SLUG, TENANT_NAME, TENANT_WHATSAPP disponibles
```

### 2. Agregar al Carrito (apiController.php)
```php
// Producto validado con tenant_id
$sql = "SELECT * FROM productos WHERE id = ? AND tenant_id = ?";
$producto = $conexion->execute_query($sql, [$producto_id, getTenantId()])->fetch_assoc();

// Carrito insertado con tenant_id
$sql = "INSERT INTO carrito (tenant_id, session_id, producto_id, cantidad) VALUES (?, ?, ?, ?)";
ejecutarConsultaScoped($sql, "isii", [$session_id, $producto_id, $cantidad]);
```

### 3. Generar Enlace WhatsApp (tiendaController.php)
```php
private function generarEnlaceWhatsApp($numero_cliente, $productos, $total, $pedido_id) {
    // Usar número del TENANT, no del cliente
    $numero = defined('TENANT_WHATSAPP') ? TENANT_WHATSAPP : $numero_cliente;
    
    // Construir mensaje con detalles del pedido
    $mensaje = "¡NUEVO PEDIDO!\n\n";
    $mensaje .= "Cliente: " . $nombre_cliente . "\n";
    // ... más detalles ...
    
    // Generar link WhatsApp
    $whatsapp_link = "https://wa.me/" . preg_replace('/[^0-9+]/', '', $numero) 
                     . "?text=" . urlencode($mensaje);
    return $whatsapp_link;
}
```

### 4. Crear Pedido (tiendaController.php - checkout)
```php
// Pedido creado con tenant_id
$sql = "INSERT INTO pedidos (tenant_id, cliente_id, estado, total) VALUES (?, ?, 'pendiente', ?)";
$resultado = $conexion->execute_query($sql, [getTenantId(), $cliente_id, $total]);
$pedido_id = $conexion->insert_id;

// Detalles agregados con tenant_id
$sql = "INSERT INTO pedido_detalles (tenant_id, pedido_id, producto_id, cantidad, precio_unitario, subtotal) 
        VALUES (?, ?, ?, ?, ?, ?)";
ejecutarConsultaScoped($sql, "iiiidd", 
    [$pedido_id, $producto['id'], $item['cantidad'], $item['precio'], $subtotal]);
```

---

## 📦 DATOS DE PRUEBA

### Productos por Tenant

**Tenant 1 (Default):**
- iPhone 15 Pro ($999.99)
- Samsung Galaxy S24 ($899.99)
- MacBook Pro 16 ($2,499.99)
- Dell XPS 15 ($1,799.99)
- Camiseta Premium ($49.99)
- Pantalón Casual ($79.99)
- Vestido Casual ($89.99)
- Jeans Premium ($99.99)
- Horno Eléctrico ($299.99)
- Juego de Cama King ($199.99)
- Computador ($100,000)

**Tenant 2 (Mauricio):**
- Servicio de Consultoría ($150.00)
- App Web Custom ($2,000.00)

**Tenant 3 (Distribuciones EBS):**
- Lote Mayorista ($500.00)
- Monitor 24 Bulk ($1,200.00)

---

## ✅ VERIFICACIÓN Y TESTING

### Tests Disponibles

1. **test-isolation.php** - Verifica aislamiento de datos
   - Acceso: http://localhost/catalogo2/test-isolation.php
   - Muestra: Productos, categorías, clientes, pedidos por tenant

2. **test-checkout-flow.php** - Simula flujo completo de checkout
   - Acceso: http://localhost/catalogo2/test-checkout-flow.php
   - Verifica: Creación de pedidos con tenant_id correcto
   - Verifica: WhatsApp por tenant funciona
   - Verifica: Aislamiento de datos post-compra

3. **test-tenant.php** - Test básico de resolución (ya existía)
   - Acceso: http://localhost/catalogo2/test-tenant.php

### Resultados Validados

✅ Cada tenant ve solo sus productos  
✅ Cada tenant tiene sus clientes aislados  
✅ Cada tenant tiene sus pedidos aislados  
✅ WhatsApp usa número del tenant, no del cliente  
✅ Carrito está aislado por tenant  
✅ Checkout funciona independientemente por tenant  
✅ No hay cross-tenant data leakage  

---

## 📝 MODELOS MODIFICADOS

### ProductoModel.php
- `obtenerTodos()` - Scoped con tenant_id
- `obtenerPorId()` - Validación tenant_id
- `obtenerPorCategoria()` - JOIN con validación tenant_id
- `obtenerPorSubcategoria()` - JOIN con validación tenant_id
- `crear()` - Inserta con tenant_id automático
- `actualizar()` - Validación tenant_id en WHERE
- `eliminar()` - Soft delete con validación tenant_id
- `buscar()` - Búsqueda filtrada por tenant_id

### CategoriaModel.php
- `obtenerTodas()` - Scoped queries
- `obtenerPorId()` - Con tenant_id
- `crear()` - Auto tenant_id
- `actualizar()` - Con validación
- `eliminar()` - Con validación
- `obtenerConSubcategorias()` - JOINs con tenant_id

### SubcategoriaModel.php
- Todas las operaciones CRUD con tenant_id

### PedidoModel.php
- `crear()` - Inserta con tenant_id
- `obtenerPorId()` - JOINs con validación
- `obtenerTodos()` - Filtro tenant_id
- `actualizarEstado()` - Validación tenant_id
- `marcarWhatsAppEnviado()` - Con validación
- `registrarHistorial()` - Inserta con tenant_id
- `obtenerHistorial()` - Filtro tenant_id

### ClienteModel.php
- Todas las consultas con tenant_id
- `crear()` - Auto tenant_id
- `obtenerPorId()` - Scoped
- `obtenerPorEmail()` - Scoped (para login)

---

## 🎮 CONTROLADORES MODIFICADOS

### tiendaController.php
- **checkout()** - Pedidos con tenant_id
  - Validación de productos por tenant
  - Creación de cliente con tenant_id
  - Pedidos creados con tenant_id
  - WhatsApp usa TENANT_WHATSAPP
  - generarEnlaceWhatsApp() modificada

### apiController.php
- **agregarAlCarrito()** - Carrito aislado por tenant
- **obtenerCarrito()** - Carrito con tenant_id
- **actualizarCarrito()** - Validación tenant_id
- **eliminarDelCarrito()** - Validación tenant_id
- **vaciarCarrito()** - Validación tenant_id
- **obtenerSubcategorias()** - Scoped por tenant_id

---

## 🚀 PRÓXIMAS FASES (Opcionales)

### Fase 3: IIS Rewrite Rules
- Solo si se migra a IIS en producción
- XAMPP no requiere esto

### Fase 4: Tenant Registration
- UI para crear nuevos tenants
- Provisioning automático de datos iniciales

### Fase 8: Per-Tenant Upload Folders
- `/public/tenants/{tenant_id}/images`
- `/public/tenants/{tenant_id}/uploads`

---

## 📌 NOTAS IMPORTANTES

1. **session_start()** debe ejecutarse antes de `TenantResolver::resolve()`
2. **TenantResolver::resolve()** debe ejecutarse después de `require_once 'config/database.php'`
3. Todos los helpers de database.php prepend automáticamente tenant_id
4. No remover la validación de tenant_id de ninguna query crítica
5. Para admin panel, validar que usuario pertenece al tenant_id correcto
6. WhatsApp por tenant está implementado en tiendaController.php

---

## 🔗 REFERENCIAS RÁPIDAS

**Archivos Críticos:**
- Middleware: `config/TenantResolver.php`
- Helpers: `config/database.php`
- Entry Point: `index.php`
- Models: `app/models/*.php`
- Controllers: `app/controllers/tiendaController.php`, `apiController.php`

**Base de Datos:**
- Tabla Tenants: `tenants`
- Tabla Datos: Todas excepto `tenants`

**Tests:**
- `test-isolation.php` - Aislamiento
- `test-checkout-flow.php` - Flujo completo
- `test-tenant.php` - Resolución básica

---

**✅ Sistema listo para producción con multi-tenancy completa y segura.**
