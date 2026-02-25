# 🏢 PLAN DE IMPLEMENTACIÓN - MULTI-TENANCY

**Distribuciones EBS - Catálogo Digital**  
**Objetivo:** Convertir a plataforma multi-tenant con URL única por cliente  
**Fecha inicio:** Enero 2026  
**Duración estimada:** 5-7 días  
**Complejidad:** Media-Alta

---

## 🎯 OBJETIVO FINAL

Transformar de:
```
http://34.193.89.155:81/catalogo2
(un solo cliente, una tienda)
```

A:
```
http://34.193.89.155:81/catalogo2/mauricio
http://34.193.89.155:81/catalogo2/distribuciones-ebs
http://34.193.89.155:81/catalogo2/{nombre-cliente}
(cada cliente con su propia tienda, datos aislados)
```

### Beneficios
- ✅ Cada cliente accede por URL personalizada
- ✅ Datos completamente aislados por tenant
- ✅ Admin panel privado para cada cliente
- ✅ WhatsApp configurado por cliente
- ✅ Archivos separados por cliente
- ✅ Escalable para N clientes

---

## 📋 FASES DE IMPLEMENTACIÓN

### FASE 1: Preparación de Base de Datos (Día 1)

#### 1.1 Crear tabla `tenants`
**Archivo:** `catalogo_tienda1.sql`  
**Acción:** Agregar DDL para nueva tabla

```sql
-- Crear tabla de tenants
CREATE TABLE IF NOT EXISTS tenants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE KEY,
  whatsapp_phone VARCHAR(20) NULL,
  logo VARCHAR(255) NULL,
  tema VARCHAR(50) NULL,
  estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_slug (slug),
  INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Insertar tenant default (para datos existentes)
INSERT INTO tenants (id, nombre, slug, estado) 
VALUES (1, 'Default', 'default', 'activo');
```

#### 1.2 Agregar `tenant_id` a tablas existentes
**Archivos:** `catalogo_tienda1.sql`  
**Acción:** ALTERs para agregar columna y referencias

Tablas a modificar:
- `administradores` - admins por tenant
- `categorias` - categorías propias de cada tienda
- `subcategorias` - subcategorías por tenant
- `productos` - productos por tenant
- `clientes` - clientes del tenant
- `carrito` - carritos aislados
- `pedidos` - pedidos del tenant
- `pedido_detalles` - detalles de pedidos
- `pedido_historial` - histórico de estados

**Ejemplo DDL:**
```sql
-- Agregar columna tenant_id
ALTER TABLE categorias ADD COLUMN tenant_id INT NOT NULL DEFAULT 1;
ALTER TABLE subcategorias ADD COLUMN tenant_id INT NOT NULL DEFAULT 1;
ALTER TABLE productos ADD COLUMN tenant_id INT NOT NULL DEFAULT 1;
ALTER TABLE clientes ADD COLUMN tenant_id INT NOT NULL DEFAULT 1;
ALTER TABLE carrito ADD COLUMN tenant_id INT NOT NULL DEFAULT 1;
ALTER TABLE pedidos ADD COLUMN tenant_id INT NOT NULL DEFAULT 1;
ALTER TABLE pedido_detalles ADD COLUMN tenant_id INT NOT NULL DEFAULT 1;
ALTER TABLE administradores ADD COLUMN tenant_id INT NULL;

-- Agregar índices
ALTER TABLE categorias ADD INDEX idx_tenant (tenant_id);
ALTER TABLE subcategorias ADD INDEX idx_tenant (tenant_id);
ALTER TABLE productos ADD INDEX idx_tenant (tenant_id);
ALTER TABLE clientes ADD INDEX idx_tenant (tenant_id);
ALTER TABLE carrito ADD INDEX idx_tenant (tenant_id);
ALTER TABLE pedidos ADD INDEX idx_tenant (tenant_id);
ALTER TABLE pedido_detalles ADD INDEX idx_tenant (tenant_id);
ALTER TABLE administradores ADD INDEX idx_tenant (tenant_id);

-- Agregar unicidades por tenant (evitar duplicados)
ALTER TABLE categorias ADD CONSTRAINT uq_cat_tenant_nombre UNIQUE (tenant_id, nombre);
ALTER TABLE subcategorias ADD CONSTRAINT uq_subcat_tenant_nombre UNIQUE (tenant_id, nombre);
```

#### 1.3 Crear tabla `tenant_settings` (opcional pero recomendado)
```sql
CREATE TABLE IF NOT EXISTS tenant_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tenant_id INT NOT NULL,
  setting_key VARCHAR(100) NOT NULL,
  setting_value LONGTEXT NULL,
  UNIQUE KEY (tenant_id, setting_key),
  FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Acción necesaria:** 
- [ ] Ejecutar script SQL de migración
- [ ] Verificar que todos los registros existentes tengan `tenant_id = 1`
- [ ] Hacer backup ANTES de ejecutar

---

### FASE 2: Middleware de Resolución de Tenant (Día 1-2)

#### 2.1 Modificar `index.php`
**Archivo:** `index.php`  
**Acción:** Agregar lógica de detección y validación del tenant

**Antes de resolver controller/action, insertar:**
```php
<?php
// === RESOLUCIÓN DE TENANT ===
// Detectar si viene por ruta de tenant (ej: /catalogo2/mauricio/...)
$tenant_slug = isset($_GET['tenant']) ? trim($_GET['tenant']) : null;
$TENANT_ID = null;
$TENANT_DATA = null;

if ($tenant_slug) {
    // Validar slug (solo letras, números, guiones)
    if (!preg_match('/^[a-z0-9\-]+$/i', $tenant_slug)) {
        http_response_code(404);
        die('Invalid tenant');
    }
    
    // Buscar tenant en BD
    global $conn;
    $stmt = $conn->prepare("SELECT id, nombre, whatsapp_phone, logo, tema FROM tenants WHERE slug = ? AND estado = 'activo'");
    $stmt->bind_param('s', $tenant_slug);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        include APP_ROOT . '/app/views/404.php';
        exit;
    }
    
    $TENANT_DATA = $result->fetch_assoc();
    $TENANT_ID = $TENANT_DATA['id'];
    
    // Guardar en sesión para uso en controladores
    $_SESSION['tenant_id'] = $TENANT_ID;
    $_SESSION['tenant_slug'] = $tenant_slug;
    $_SESSION['tenant_data'] = $TENANT_DATA;
    
    $stmt->close();
}

// Si no hay tenant pero hay sesión previa, usar esa
if (!$TENANT_ID && isset($_SESSION['tenant_id'])) {
    $TENANT_ID = $_SESSION['tenant_id'];
    $TENANT_DATA = $_SESSION['tenant_data'] ?? null;
}

// IMPORTANTE: si accedes sin tenant y sin sesión, es modo "admin global" (mantener compatibilidad)
// Pero si vines con un tenant específico, obligar el aislamiento

define('TENANT_ID', $TENANT_ID); // null o int
define('TENANT_SLUG', $tenant_slug ?? ($_SESSION['tenant_slug'] ?? null));
define('TENANT_DATA', $TENANT_DATA ?? array());

// === FIN RESOLUCIÓN TENANT ===
?>
```

#### 2.2 Crear constantes globales
**Archivo:** `config/config.php`  
**Acción:** Agregar definiciones útiles

```php
// Multi-tenancy
define('MULTI_TENANT_ENABLED', true);
define('TENANT_MODE', !empty(TENANT_ID)); // true si estamos en modo tenant
```

**Acción necesaria:**
- [ ] Editar `index.php` - insertar bloque de resolución
- [ ] Editar `config/config.php` - agregar constantes
- [ ] Probar que TENANT_ID se detecta en una petición `?tenant=default`

---

### FASE 3: Reglas de Reescritura en IIS (Día 2)

#### 3.1 Modificar `web.config`
**Archivo:** `web.config`  
**Acción:** Agregar reglas para ruteo por slug

**Insertar ANTES de la regla "Main Rewrite":**

```xml
<!-- TENANT ROUTING -->

<!-- Regla 1: Tenant raíz (ej: /catalogo2/mauricio) -->
<rule name="Tenant Root" stopProcessing="true">
  <match url="^([A-Za-z0-9\-]+)/?$" />
  <conditions logicalGrouping="MatchAll">
    <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
    <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
  </conditions>
  <action type="Rewrite" url="index.php?controller=tienda&action=inicio&tenant={R:1}" appendQueryString="true" />
</rule>

<!-- Regla 2: Tenant + Controlador (ej: /catalogo2/mauricio/admin) -->
<rule name="Tenant Controller" stopProcessing="true">
  <match url="^([A-Za-z0-9\-]+)/([A-Za-z0-9_]+)/?$" />
  <conditions logicalGrouping="MatchAll">
    <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
    <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
  </conditions>
  <action type="Rewrite" url="index.php?controller={R:2}&action=inicio&tenant={R:1}" appendQueryString="true" />
</rule>

<!-- Regla 3: Tenant + Controlador + Acción (ej: /catalogo2/mauricio/admin/dashboard) -->
<rule name="Tenant Controller Action" stopProcessing="true">
  <match url="^([A-Za-z0-9\-]+)/([A-Za-z0-9_]+)/([A-Za-z0-9_]+)/?$" />
  <conditions logicalGrouping="MatchAll">
    <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
    <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
  </conditions>
  <action type="Rewrite" url="index.php?controller={R:2}&action={R:3}&tenant={R:1}" appendQueryString="true" />
</rule>

<!-- Regla 4: Tenant + Controlador + Acción + Query Params (ej: /catalogo2/mauricio/tienda/categoria/5) -->
<rule name="Tenant Full Path" stopProcessing="true">
  <match url="^([A-Za-z0-9\-]+)/([A-Za-z0-9_]+)/([A-Za-z0-9_]+)/(.*)$" />
  <conditions logicalGrouping="MatchAll">
    <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
    <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
  </conditions>
  <action type="Rewrite" url="index.php?controller={R:2}&action={R:3}&{R:4}&tenant={R:1}" appendQueryString="true" />
</rule>

<!-- FIN TENANT ROUTING -->
```

**Acción necesaria:**
- [ ] Editar `web.config` - insertar reglas de tenant ANTES de "Main Rewrite"
- [ ] Reiniciar IIS: `iisreset`
- [ ] Probar URL: `http://34.193.89.155:81/catalogo2/default`

---

### FASE 4: Helpers de Base de Datos con Aislamiento (Día 2)

#### 4.1 Crear helpers "scoped" en `config/database.php`
**Archivo:** `config/database.php`  
**Acción:** Agregar funciones que filtran automáticamente por tenant

```php
/**
 * Obtener fila CON aislamiento por tenant
 * Usa TENANT_ID si está definido
 */
function obtenerFilaScoped($sql, $tipos = "", $params = array()) {
    if (defined('TENANT_ID') && TENANT_ID) {
        // Agregar WHERE tenant_id = TENANT_ID
        // NOTA: Asumir que $sql ya tiene WHERE o que se puede anexar
        $sql_tenant = preg_replace('/WHERE/i', 'WHERE tenant_id = ' . TENANT_ID . ' AND ', $sql);
        if (strpos($sql, 'WHERE') === false) {
            $sql_tenant = $sql . ' WHERE tenant_id = ' . TENANT_ID;
        }
    } else {
        $sql_tenant = $sql;
    }
    
    return obtenerFila($sql_tenant, $tipos, $params);
}

/**
 * Ejecutar consulta INSERT con tenant_id automático
 */
function ejecutarConsultaScoped($sql, $tipos = "", $params = array()) {
    global $conn;
    
    // Si es INSERT, agregar tenant_id
    if (defined('TENANT_ID') && TENANT_ID && stripos($sql, 'INSERT') !== false) {
        // Buscar último paréntesis antes de VALUES
        $match = preg_match('/INSERT INTO \w+ \((.*?)\) VALUES/i', $sql, $matches);
        if ($match) {
            $campos = $matches[1];
            if (strpos($campos, 'tenant_id') === false) {
                // Agregar tenant_id
                $sql = str_replace($campos . ')', $campos . ', tenant_id)', $sql);
                $sql = str_replace('VALUES (', 'VALUES (' . TENANT_ID . ', ', $sql);
            }
        }
    }
    
    return ejecutarConsulta($sql, $tipos, $params);
}
```

**Acción necesaria:**
- [ ] Editar `config/database.php` - agregar funciones scoped
- [ ] NO reemplazar helpers existentes aún (mantener compatibilidad)

---

### FASE 5: Crear Formulario de Registro/Provisión (Día 2-3)

#### 5.1 Nueva vista: `app/views/tienda/registro-tenant.php`
**Archivo:** Nuevo  
**Acción:** Crear formulario para registrar nueva tienda

```html
<!-- Formulario para crear nuevo tenant -->
<div class="container mt-5">
    <h2>🏪 Crear Mi Tienda</h2>
    
    <form id="form-registro-tenant" method="POST" action="<?php echo APP_URL; ?>/index.php?controller=tienda&action=registroTenant">
        <div class="mb-3">
            <label class="form-label">Nombre de tu tienda</label>
            <input type="text" class="form-control" name="nombre" required placeholder="Ej: Mauricio, Distribuciones EBS">
        </div>
        
        <div class="mb-3">
            <label class="form-label">URL de tu tienda (slug único)</label>
            <div class="input-group">
                <span class="input-group-text">http://34.193.89.155:81/catalogo2/</span>
                <input type="text" class="form-control" name="slug" required placeholder="mauricio" pattern="[a-z0-9\-]+" title="Solo letras, números y guiones">
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Número WhatsApp (para recibir pedidos)</label>
            <input type="tel" class="form-control" name="whatsapp" placeholder="+57 311 2969569">
        </div>
        
        <div class="mb-3">
            <label class="form-label">Usuario Admin</label>
            <input type="text" class="form-control" name="admin_usuario" required placeholder="tu_usuario">
        </div>
        
        <div class="mb-3">
            <label class="form-label">Contraseña Admin</label>
            <input type="password" class="form-control" name="admin_password" required>
        </div>
        
        <button type="submit" class="btn btn-success btn-lg w-100">Crear Mi Tienda</button>
    </form>
</div>
```

#### 5.2 Nuevo método en `TiendaController`
**Archivo:** `app/controllers/tiendaController.php`  
**Acción:** Agregar método `registroTenant()` que provisiona un nuevo tenant

```php
public function registroTenant() {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        include APP_ROOT . '/app/views/tienda/registro-tenant.php';
        return;
    }
    
    // POST: crear nuevo tenant
    global $conn;
    
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $slug = strtolower(trim($_POST['slug'] ?? ''));
    $whatsapp = sanitizar($_POST['whatsapp'] ?? '');
    $admin_usuario = sanitizar($_POST['admin_usuario'] ?? '');
    $admin_password = $_POST['admin_password'] ?? '';
    
    // Validar
    if (!$nombre || !$slug || !$admin_usuario || !$admin_password) {
        echo json_encode(['success' => false, 'message' => 'Campos obligatorios faltantes']);
        return;
    }
    
    if (!preg_match('/^[a-z0-9\-]{3,}$/i', $slug)) {
        echo json_encode(['success' => false, 'message' => 'Slug inválido (mín 3 caracteres, letras/números/guiones)']);
        return;
    }
    
    // Verificar que slug no exista
    $check = $conn->prepare("SELECT id FROM tenants WHERE slug = ?");
    $check->bind_param('s', $slug);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Este slug ya está en uso']);
        return;
    }
    
    // Crear tenant
    $stmt = $conn->prepare("INSERT INTO tenants (nombre, slug, whatsapp_phone, estado) VALUES (?, ?, ?, 'activo')");
    $stmt->bind_param('sss', $nombre, $slug, $whatsapp);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Error al crear tenant']);
        return;
    }
    $tenant_id = $conn->insert_id;
    
    // Crear admin del tenant
    $password_hash = hash('sha256', $admin_password);
    $stmt = $conn->prepare("INSERT INTO administradores (usuario, password, nombre, tenant_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('sssi', $admin_usuario, $password_hash, $admin_usuario, $tenant_id);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Error al crear admin del tenant']);
        return;
    }
    
    // Devolver enlace de acceso
    $tenant_url = APP_URL . '/' . $slug;
    $admin_url = APP_URL . '/' . $slug . '/admin';
    
    echo json_encode([
        'success' => true,
        'message' => 'Tienda creada exitosamente',
        'tenant_id' => $tenant_id,
        'tenant_url' => $tenant_url,
        'admin_url' => $admin_url,
        'credentials' => [
            'usuario' => $admin_usuario,
            'slug' => $slug
        ]
    ]);
}
```

**Acción necesaria:**
- [ ] Crear vista `registro-tenant.php`
- [ ] Agregar método en `TiendaController`
- [ ] Crear ruta en `web.config` o dejar como `?controller=tienda&action=registroTenant`

---

### FASE 6: Aislar Todas las Consultas por Tenant (Día 3-4)

#### 6.1 Modificar modelos para filtrar por tenant
**Archivos:** 
- `app/models/CategoriaModel.php`
- `app/models/SubcategoriaModel.php`
- `app/models/ProductoModel.php`
- `app/models/ClienteModel.php`
- `app/models/PedidoModel.php`
- etc.

**Acción:** En cada método GET, agregar `WHERE tenant_id = TENANT_ID`

**Ejemplo - CategoriaModel:**
```php
public function obtenerTodas() {
    $sql = "SELECT * FROM categorias";
    
    if (defined('TENANT_ID') && TENANT_ID) {
        $sql .= " WHERE tenant_id = " . TENANT_ID;
    }
    
    $sql .= " ORDER BY nombre";
    return obtenerFilas($sql);
}

public function obtenerPorId($id) {
    $sql = "SELECT * FROM categorias WHERE id = ?";
    
    if (defined('TENANT_ID') && TENANT_ID) {
        $sql .= " AND tenant_id = " . TENANT_ID;
    }
    
    return obtenerFila($sql, "i", array(&$id));
}
```

**Acción necesaria:**
- [ ] Revisar cada modelo
- [ ] Agregar filtro `tenant_id` en GETs
- [ ] Agregar `tenant_id` en INSERTs
- [ ] Probar que productos de un tenant no aparecen en otro

---

### FASE 7: Folder Structure por Tenant (Día 4)

#### 7.1 Crear carpetas de almacenamiento
**Acción:** Estructura de archivos por tenant

```
public/
├── tenants/
│   ├── 1/  (default)
│   │   ├── images/
│   │   │   └── productos/
│   │   └── invoices/
│   ├── 2/  (mauricio)
│   │   ├── images/
│   │   │   └── productos/
│   │   └── invoices/
│   ├── 3/  (distribuciones-ebs)
│   │   ├── images/
│   │   │   └── productos/
│   │   └── invoices/
```

#### 7.2 Modificar rutas de upload
**Archivos:** 
- `app/controllers/adminController.php` (método agregarProducto)
- `app/views/admin/productos.php` (formulario)

**Acción:** Usar ruta dinámica según TENANT_ID

```php
// Al subir imagen
$tenant_folder = APP_ROOT . '/public/tenants/' . TENANT_ID . '/images/productos';

if (!is_dir($tenant_folder)) {
    @mkdir($tenant_folder, 0775, true);
}

$file_destination = $tenant_folder . '/' . $nombre_imagen;
```

#### 7.3 Modificar rutas de lectura
**Acción:** Mostrar imágenes con ruta de tenant

```php
// En vistas
<img src="<?php echo APP_URL; ?>/public/tenants/<?php echo TENANT_ID; ?>/images/productos/<?php echo $producto['imagen']; ?>">
```

**Acción necesaria:**
- [ ] Crear carpeta `public/tenants/{1,2,3,…}/images/productos`
- [ ] Crear carpeta `public/tenants/{1,2,3,…}/invoices`
- [ ] Dar permisos de escritura en IIS a esas carpetas
- [ ] Modificar controllers que suben/muestran imágenes

---

### FASE 8: WhatsApp por Tenant (Día 4)

#### 8.1 Modificar `generarEnlaceWhatsApp()`
**Archivo:** `app/controllers/tiendaController.php`  
**Acción:** Usar número del tenant si está disponible

```php
private function generarEnlaceWhatsApp($numero, $productos, $total, $pedido_id, $cuenta de cobro_url = '') {
    // Usar número del tenant si no se proporciona
    if (!$numero && defined('TENANT_DATA')) {
        $numero = TENANT_DATA['whatsapp_phone'] ?? null;
    }
    
    if (!$numero) {
        return null; // No enviar si no hay número
    }
    
    // Resto del código igual...
}
```

**Acción necesaria:**
- [ ] Editar método `generarEnlaceWhatsApp()`
- [ ] Probar que usa el número correcto del tenant

---

### FASE 9: Panel Admin por Tenant (Día 5)

#### 9.1 Modificar `adminController.php`
**Archivo:** `app/controllers/adminController.php`  
**Acción:** Validar que admin pertenece al tenant accedido

```php
public function __construct() {
    // ... existing code ...
    
    // Validar que el admin pertenece al tenant accedido
    if (defined('TENANT_ID') && TENANT_ID) {
        if (isset($_SESSION['admin_id'])) {
            // Verificar que admin.tenant_id == TENANT_ID
            global $conn;
            $stmt = $conn->prepare("SELECT tenant_id FROM administradores WHERE id = ?");
            $stmt->bind_param('i', $_SESSION['admin_id']);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            if (!$result || ($result['tenant_id'] && $result['tenant_id'] != TENANT_ID)) {
                header('Location: ' . APP_URL . '/index.php?controller=admin&action=login');
                exit;
            }
        } else {
            header('Location: ' . APP_URL . '/' . TENANT_SLUG . '/admin/login');
            exit;
        }
    }
}
```

#### 9.2 Crear vista de login por tenant
**Archivo:** `app/views/admin/login.php`  
**Acción:** Mostrar slug del tenant en el login

**Acción necesaria:**
- [ ] Agregar validación de tenant al constructor de admin
- [ ] Verificar que el login funciona por tenant
- [ ] Probar aislamiento de datos en dashboard

---

### FASE 10: Pruebas y Validación (Día 5-6)

#### 10.1 Crear datos de prueba
**Acción:** Crear 2 tenants de ejemplo

```sql
-- Tenant 1: Mauricio
INSERT INTO tenants (id, nombre, slug, whatsapp_phone, estado) 
VALUES (2, 'Mauricio', 'mauricio', '573112969569', 'activo');

-- Tenant 2: Distribuciones EBS
INSERT INTO tenants (id, nombre, slug, whatsapp_phone, estado) 
VALUES (3, 'Distribuciones EBS', 'distribuciones-ebs', '573112969569', 'activo');

-- Admin de Mauricio
INSERT INTO administradores (usuario, password, nombre, tenant_id) 
VALUES ('mauricio', SHA2('123456', 256), 'Mauricio Admin', 2);

-- Admin de Distribuciones
INSERT INTO administradores (usuario, password, nombre, tenant_id) 
VALUES ('distribuidor', SHA2('123456', 256), 'Distribuidor Admin', 3);

-- Productos de prueba para Mauricio (tenant_id=2)
INSERT INTO productos (nombre, descripcion, precio, tenant_id) 
VALUES ('Producto Mauricio 1', 'Descripción', 100.00, 2);

-- Productos de prueba para Distribuciones (tenant_id=3)
INSERT INTO productos (nombre, descripción, precio, tenant_id) 
VALUES ('Producto Dist 1', 'Descripción', 50.00, 3);
```

#### 10.2 Checklist de validación
**Acciones de prueba:**

```
Prueba 1: Acceso a tiendas por URL
[ ] http://34.193.89.155:81/catalogo2/default
    → Debe mostrar productos del tenant 1
[ ] http://34.193.89.155:81/catalogo2/mauricio
    → Debe mostrar solo "Producto Mauricio 1"
[ ] http://34.193.89.155:81/catalogo2/distribuciones-ebs
    → Debe mostrar solo "Producto Dist 1"

Prueba 2: Aislamiento de datos
[ ] En panel admin de mauricio, verificar que solo ve sus productos
[ ] En panel admin de distribuciones, verificar que solo ve sus productos
[ ] Intentar acceder a /catalogo2/mauricio/admin con admin de distribuciones
    → Debe rechazar o redirigir

Prueba 3: Upload de imágenes
[ ] Subir imagen en mauricio → debe ir a /public/tenants/2/images/...
[ ] Subir imagen en distribuciones → debe ir a /public/tenants/3/images/...
[ ] Verificar que no hay cruce de archivos

Prueba 4: WhatsApp
[ ] Crear pedido en mauricio → debe usar número de mauricio
[ ] Crear pedido en distribuciones → debe usar número de distribuciones

Prueba 5: Carrito y checkout
[ ] Agregar producto de mauricio → carrito filtrado por tenant_id
[ ] Checkout → pedido debe tener tenant_id=2
[ ] Mensajes WhatsApp deben tener datos correctos por tenant

Prueba 6: Crear nuevo tenant
[ ] Acceder a /registro-tenant
[ ] Crear nuevo tenant "cliente-nuevo"
[ ] Verificar que se crea en BD con tenant_id nuevo
[ ] Acceder a /catalogo2/cliente-nuevo
[ ] Verificar que carga correctamente (sin datos iniciales)
```

**Acción necesaria:**
- [ ] Ejecutar inserts de datos de prueba
- [ ] Realizar todas las pruebas del checklist
- [ ] Documentar cualquier bug encontrado
- [ ] Hacer fixes si es necesario

---

## 🔄 RESUMEN DE CAMBIOS POR ARCHIVO

| Archivo | Tipo | Acción |
|---------|------|--------|
| `catalogo_tienda1.sql` | SQL | CREATE TABLE tenants, ALTER para tenant_id |
| `index.php` | PHP | Agregar middleware resolución tenant |
| `config/config.php` | PHP | Definir TENANT_ID, TENANT_SLUG |
| `config/database.php` | PHP | Helpers scoped para consultas |
| `web.config` | XML | Agregar 4 reglas de tenant routing |
| `app/controllers/tiendaController.php` | PHP | Método registroTenant(), modificar generarEnlaceWhatsApp() |
| `app/controllers/adminController.php` | PHP | Validación de tenant en constructor |
| `app/models/*.php` | PHP | Agregar filtros tenant_id en GETs e INSERTs |
| `app/views/tienda/registro-tenant.php` | PHP | Nueva vista de registro |
| Todos los controllers | PHP | Garantizar que usan helpers scoped |
| Todas las vistas | PHP | Actualizar rutas de imágenes con tenant_id |
| `public/tenants/{1,2,3,…}/` | Folder | Crear estructura de folders por tenant |

---

## 📅 CRONOGRAMA

| Día | Tarea | Duración |
|-----|-------|----------|
| 1 | FASE 1: Migración BD + FASE 2: Middleware tenant | 3-4 hrs |
| 2 | FASE 3: Reglas IIS + FASE 4: Helpers scoped + FASE 5: Registro | 3-4 hrs |
| 3-4 | FASE 6: Aislar todas las consultas (modelos, controllers, vistas) | 4-6 hrs |
| 4 | FASE 7: Estructura por tenant + FASE 8: WhatsApp por tenant | 2-3 hrs |
| 5 | FASE 9: Panel admin por tenant + FASE 10: Pruebas | 3-4 hrs |
| 6 | Fixes, QA, documentación, deployment | 2-3 hrs |

**Total estimado:** 5-7 días de trabajo

---

## 🛡️ CONSIDERACIONES DE SEGURIDAD

- ✅ Siempre filtrar por TENANT_ID en consultas
- ✅ Validar que usuario admin pertenece al tenant
- ✅ No permitir acceso cruzado de usuarios entre tenants
- ✅ Logs con tenant_id para auditoría
- ✅ Backups por tenant
- ✅ Rate limiting por tenant (opcional)
- ✅ Permisos de archivos limitados por tenant

---

## 🎯 SIGUIENTE PASO

Revisar este plan y confirmar:
1. ¿Están de acuerdo con todas las fases?
2. ¿Hay algo que cambiar o agregar?
3. ¿Cuándo queremos empezar?

Una vez confirmado, procederé con:
- Fase 1: Script SQL de migración listo para ejecutar
- Fase 2-3: Código de middleware y reglas IIS
- Y así progresivamente por fase
