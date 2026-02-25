# FASE 2 COMPLETADA: Middleware Multi-Tenancy

## ✅ Archivos Creados/Modificados

### 1. config/TenantResolver.php (NUEVO)
Clase middleware para resolver el tenant desde la URL.

**Características:**
- Detecta tenant desde URL: `/catalogo2/mauricio`
- Soporta parámetro GET: `?tenant=mauricio`
- Valida estado del tenant (activo/inactivo/bloqueado)
- Guarda tenant en sesión para requests posteriores
- Define constantes globales: TENANT_ID, TENANT_SLUG, TENANT_NAME, TENANT_WHATSAPP
- Fallback a tenant "default" si no hay slug en URL

**Métodos Públicos:**
```php
TenantResolver::resolve()                // Detectar y establecer tenant
TenantResolver::getCurrentTenant()       // Obtener datos del tenant actual
TenantResolver::getTenantId()            // Obtener ID del tenant
TenantResolver::isDefaultTenant()        // Verificar si es tenant default
```

### 2. index.php (MODIFICADO)
Integrado el middleware de tenant.

**Cambios:**
```php
// Después de database.php, antes del routing:
require_once 'config/TenantResolver.php';
TenantResolver::resolve();

// A partir de aquí TENANT_ID está disponible
```

### 3. config/database.php (MODIFICADO)
Agregadas funciones helper para queries scoped por tenant.

**Nuevas Funciones:**

#### getTenantId()
```php
$tenant_id = getTenantId(); // Retorna TENANT_ID o 1 por defecto
```

#### ejecutarConsultaScoped($sql, $tipos, $params)
```php
// La query debe tener ? para tenant_id al inicio
ejecutarConsultaScoped(
    "SELECT * FROM productos WHERE tenant_id = ? AND activo = ?",
    "i",  // Tipos DESPUÉS del tenant_id
    [1]   // Parámetros DESPUÉS del tenant_id
);
// Internamente se convierte a: tipos="ii", params=[TENANT_ID, 1]
```

#### obtenerFilaScoped($sql, $tipos, $params)
```php
$producto = obtenerFilaScoped(
    "SELECT * FROM productos WHERE tenant_id = ? AND id = ?",
    "i",
    [$producto_id]
);
```

#### obtenerFilasScoped($sql, $tipos, $params)
```php
$categorias = obtenerFilasScoped(
    "SELECT * FROM categorias WHERE tenant_id = ? AND activa = ?",
    "i",
    [1]
);
```

#### agregarFiltroTenant($sql)
```php
$sql = "SELECT * FROM productos WHERE activo = 1";
$sql = agregarFiltroTenant($sql);
// Resultado: "SELECT * FROM productos WHERE tenant_id = ? AND activo = 1"
```

#### validarPerteneceATenant($tabla, $id)
```php
if (!validarPerteneceATenant('productos', $producto_id)) {
    die("Acceso no autorizado");
}
```

---

## 📋 Cómo Funciona el Routing Multi-Tenant

### URLs Soportadas:

**Tenant Específico:**
```
http://localhost/catalogo2/mauricio                    → Tenant: mauricio
http://localhost/catalogo2/mauricio/tienda            → Tenant: mauricio, Controller: tienda
http://localhost/catalogo2/mauricio/tienda/productos  → Tenant: mauricio, Controller: tienda, Action: productos
http://localhost/catalogo2/distribuciones-ebs         → Tenant: distribuciones-ebs
```

**Tenant Default (Backward Compatibility):**
```
http://localhost/catalogo2                  → Tenant: default (id=1)
http://localhost/catalogo2/tienda           → Tenant: default (id=1)
http://localhost/catalogo2/admin            → Tenant: default (id=1)
```

**Parámetro GET (Alternativo):**
```
http://localhost/catalogo2?tenant=mauricio  → Tenant: mauricio
```

### Detección de Slug:

1. **Prioridad 1:** Parámetro GET `?tenant=slug`
2. **Prioridad 2:** Primer segmento de URL (si no es controlador conocido)
3. **Fallback:** Tenant "default" (id=1)

### Validaciones:

- ✅ Formato válido: `[a-z0-9\-]+` (minúsculas, números, guiones)
- ✅ No conflicto con controllers: `admin`, `tienda`, `auth`, `api`
- ✅ Estado activo en BD
- ✅ Tenant existe en tabla `tenants`

---

## 🔧 Patrones de Uso en Controllers

### FORMA 1: Usar funciones Scoped (RECOMENDADO)

```php
// En cualquier controller o model:

// Obtener todas las categorías del tenant
$categorias = obtenerFilasScoped(
    "SELECT * FROM categorias WHERE tenant_id = ? AND activa = ?",
    "i",
    [1]
);

// Obtener un producto específico del tenant
$producto = obtenerFilaScoped(
    "SELECT * FROM productos WHERE tenant_id = ? AND id = ?",
    "i",
    [$producto_id]
);

// INSERT con tenant_id
$stmt = ejecutarConsultaScoped(
    "INSERT INTO categorias (tenant_id, nombre, descripcion) VALUES (?, ?, ?)",
    "ss",
    [$nombre, $descripcion]
);

// UPDATE con tenant_id
$stmt = ejecutarConsultaScoped(
    "UPDATE productos SET stock = ? WHERE tenant_id = ? AND id = ?",
    "iii",
    [$nuevo_stock, $producto_id]
);

// DELETE con tenant_id
$stmt = ejecutarConsultaScoped(
    "DELETE FROM productos WHERE tenant_id = ? AND id = ?",
    "i",
    [$producto_id]
);
```

### FORMA 2: Usar Constante TENANT_ID Manualmente

```php
// Acceder a la constante global
$tenant_id = TENANT_ID;

// SELECT manual
$sql = "SELECT * FROM productos WHERE tenant_id = $tenant_id AND activo = 1";
$resultado = $conn->query($sql);

// Con prepared statement manual
$stmt = $conn->prepare("SELECT * FROM productos WHERE tenant_id = ? AND id = ?");
$stmt->bind_param("ii", $tenant_id, $producto_id);
$stmt->execute();
$resultado = $stmt->get_result();
```

### FORMA 3: Helper agregarFiltroTenant()

```php
// Si tienes una query existente sin tenant_id:
$sql = "SELECT * FROM productos WHERE activo = 1 ORDER BY nombre";

// Agregar filtro de tenant automáticamente:
$sql = agregarFiltroTenant($sql);
// Resultado: "SELECT * FROM productos WHERE tenant_id = ? AND activo = 1 ORDER BY nombre"

// Ejecutar con tenant_id:
$productos = obtenerFilasScoped($sql, "i", [1]);
```

---

## 🛡️ Seguridad y Validación

### Validar Pertenencia al Tenant

Antes de editar/eliminar un registro, valida que pertenezca al tenant:

```php
// En adminController.php al editar producto:
public function editarProducto() {
    $producto_id = $_POST['id'];
    
    // Validar que el producto pertenece al tenant actual
    if (!validarPerteneceATenant('productos', $producto_id)) {
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para editar este producto']);
        exit;
    }
    
    // Continuar con la edición...
}
```

### Manejo de Errores

**Tenant no encontrado (404):**
- URL: `/catalogo2/tienda-inexistente`
- Response: Página HTML 404 "Tienda No Encontrada"

**Tenant bloqueado/inactivo (403):**
- URL: `/catalogo2/tenant-bloqueado`
- Response: Página HTML 403 "Tienda No Disponible"

---

## 🧪 Testing

### Crear Tenants de Prueba

```sql
-- Tenant 1: Mauricio
INSERT INTO tenants (nombre, slug, whatsapp_phone, estado, tema)
VALUES ('Mauricio', 'mauricio', '573112969569', 'activo', 'default');

-- Tenant 2: Distribuciones EBS
INSERT INTO tenants (nombre, slug, whatsapp_phone, estado, tema)
VALUES ('Distribuciones EBS', 'distribuciones-ebs', '573001234567', 'activo', 'default');
```

### Probar URLs

1. **Tenant Default:**
   - http://localhost/catalogo2
   - Debe mostrar productos del tenant_id=1

2. **Tenant Mauricio:**
   - http://localhost/catalogo2/mauricio
   - Debe mostrar productos del tenant "mauricio"

3. **Tenant No Existe:**
   - http://localhost/catalogo2/noexiste
   - Debe mostrar página 404 "Tienda No Encontrada"

---

## 📊 Estado Actual

### Constantes Disponibles Globalmente:

```php
TENANT_ID          // int - ID del tenant (ej: 1, 2, 3)
TENANT_SLUG        // string - Slug del tenant (ej: "mauricio")
TENANT_NAME        // string - Nombre del tenant (ej: "Mauricio")
TENANT_WHATSAPP    // string - Teléfono WhatsApp del tenant
```

### Variables de Sesión:

```php
$_SESSION['tenant_id']      // ID del tenant
$_SESSION['tenant_slug']    // Slug del tenant
$_SESSION['tenant_data']    // Array completo de datos del tenant
```

---

## ⚠️ IMPORTANTE para Desarrolladores

### SIEMPRE Incluir tenant_id en Queries:

❌ **INCORRECTO:**
```php
$sql = "SELECT * FROM productos WHERE activo = 1";
```

✅ **CORRECTO (Opción 1):**
```php
$productos = obtenerFilasScoped(
    "SELECT * FROM productos WHERE tenant_id = ? AND activo = ?",
    "i",
    [1]
);
```

✅ **CORRECTO (Opción 2):**
```php
$tenant_id = TENANT_ID;
$sql = "SELECT * FROM productos WHERE tenant_id = $tenant_id AND activo = 1";
```

### Reglas de Oro:

1. **NUNCA** hacer SELECT/UPDATE/DELETE sin filtro de tenant_id
2. **SIEMPRE** incluir tenant_id en INSERT
3. **SIEMPRE** validar pertenencia al tenant antes de editar/eliminar
4. **USAR** funciones Scoped cuando sea posible (más seguro)
5. **PROBAR** con múltiples tenants para validar aislamiento

---

## 🚀 Próximos Pasos

### FASE 3: IIS Rewrite Rules
- Crear web.config con URL rewriting
- Patrón: `/catalogo2/{slug}` → `/catalogo2/?tenant={slug}`

### FASE 4: Aislar Controllers
- Modificar tiendaController.php
- Modificar adminController.php
- Agregar tenant_id a todas las queries

### FASE 5: Tenant Registration
- Crear formulario de registro
- Crear tenantController.php
- Auto-provisioning de tenant

---

## 📝 Notas

- Middleware ejecuta automáticamente en cada request
- Sesión mantiene tenant entre requests
- Compatible con backward compatibility (tenant default)
- Preparado para IIS rewrite rules
- Performance: 1 query adicional por request (cacheable en sesión)

---

**Fase 2 Completada** ✅  
**Fecha:** 2026-01-09  
**Siguiente:** Fase 3 - IIS Rewrite Rules
