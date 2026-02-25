# 🔐 Credenciales de Acceso - Sistema Multi-Tenant

## 🛡️ Super Administrador
**URL:** http://localhost/catalogo2/index.php?controller=superAdmin&action=login

- **Usuario:** `superadmin`
- **Contraseña:** `SuperAdmin123!`
- **Permisos:** Gestión global de todos los tenants, crear/desactivar tenants, gestionar usuarios

---

## 🏪 Tenants Activos

### 1️⃣ Tienda Default
**URL Tienda:** http://localhost/catalogo2/default  
**URL Admin:** http://localhost/catalogo2/default/index.php?controller=admin

- **Usuario Admin:** `admin`
- **Contraseña:** `admin123`
- **WhatsApp:** +573112969569
- **Estado:** ✅ Activo
- **Tema:** Azul
- **Productos:** 102 activos
- **Clientes:** 17
- **Pedidos:** 19

---

### 2️⃣ Mauricio
**URL Tienda:** http://localhost/catalogo2/mauricio  
**URL Admin:** http://localhost/catalogo2/mauricio/index.php?controller=admin

- **Usuario Admin:** `admin` (crear desde super-admin si no existe)
- **Contraseña:** `admin123`
- **WhatsApp:** +573112969569
- **Estado:** ✅ Activo
- **Tema:** Petróleo
- **Productos:** 92 activos (90 de seed + 2 anteriores)
- **Clientes:** 12 (10 de seed + 2 anteriores)
- **Pedidos:** 11 (10 de seed + 1 anterior)

---

### 3️⃣ Distribuciones EBS
**URL Tienda:** http://localhost/catalogo2/distribuciones-ebs  
**URL Admin:** http://localhost/catalogo2/distribuciones-ebs/index.php?controller=admin

- **Usuario Admin:** `admin` (crear desde super-admin si no existe)
- **Contraseña:** `admin123`
- **WhatsApp:** +573001234567
- **Estado:** ✅ Activo
- **Tema:** Azul
- **Productos:** 92 activos (90 de seed + 2 anteriores)
- **Clientes:** 12 (10 de seed + 2 anteriores)
- **Pedidos:** 11 (10 de seed + 1 anterior)

---

### 4️⃣ Tech Store - Prueba
**URL Tienda:** http://localhost/catalogo2/tech-store  
**URL Admin:** http://localhost/catalogo2/tech-store/index.php?controller=admin

- **Usuario Admin:** `admin_tech`
- **Contraseña:** `Tech123!@`
- **WhatsApp:** +573334567890
- **Estado:** ✅ Activo
- **Tema:** Naranja
- **Productos:** 102 activos (90 de seed + 12 anteriores)
- **Clientes:** 10 (de seed)
- **Pedidos:** 10 (de seed)

---

## 🗄️ Base de Datos
- **Host:** localhost
- **Usuario:** `root`
- **Contraseña:** (vacía)
- **Base de Datos:** `catalogo_tienda`

---

## 📋 Notas Importantes


> Importante: No usar la raíz http://localhost/catalogo2 (es entorno de pruebas). Entra siempre por el login de super-admin o por la URL con el slug del tenant.

### Jerarquía de Roles
1. **superadmin** - Acceso total al sistema
2. **admin** - Administrador de un tenant específico
3. **editor** - Puede editar contenido
4. **viewer** - Solo visualización

### URLs Útiles
- **Dashboard Super Admin:** http://localhost/catalogo2/index.php?controller=superAdmin&action=dashboard
- **Gestión de Tenants:** http://localhost/catalogo2/index.php?controller=superAdmin&action=tenants
- **Crear Nuevo Tenant:** http://localhost/catalogo2/index.php?controller=superAdmin&action=formularioCrearTenant

### Funcionalidades Implementadas
✅ Multi-tenancy con aislamiento por URL  
✅ Super-admin con gestión global  
✅ Crear/activar/desactivar tenants  
✅ Gestión de usuarios por tenant  
✅ Uploads per-tenant con seguridad  
✅ Validación automática de estado en cada request  
✅ Stock visible en productos  
✅ Imágenes con fallback a ruta legacy  
✅ Personalización de título y tema por tenant  
✅ 10 esquemas de color (Azul, Verde, Rojo, Morado, Naranja, Marino, Grafito, Petróleo, Acero, Gris)  
✅ Temas claro y oscuro  
✅ Filtrado automático de productos activos/inactivos  
✅ Login/registro de clientes deshabilitado por seguridad  
✅ Generación de cuentas de cobro PDF con colores corporativos  
✅ Exportación Excel de cuentas de cobro  
✅ Reportes de productos (PDF/Excel) con filtros  
✅ Reportes de pedidos (PDF/Excel) con filtros avanzados  
✅ Script de seeding para datos de prueba (90 productos, 10 clientes, 10 pedidos por tenant)  
✅ Edición de productos mantiene subcategoría seleccionada

### Scripts de Utilidad
- **Limpiar sesión:** http://localhost/catalogo2/limpiar-sesion.php
- **Debug tenant:** http://localhost/catalogo2/debug-tenant.php
- **Generar datos de prueba:** `php scripts/seed_datos.php` (ejecutar desde terminal o navegador)

---

## 📊 Resumen de Datos

| Tenant              | Productos | Clientes | Pedidos | Usuario Admin | Estado  |
|---------------------|-----------|----------|---------|---------------|---------|
| Tienda Default      | 102       | 17       | 19      | admin         | ✅ Activo |
| Mauricio            | 92        | 12       | 11      | (crear)       | ✅ Activo |
| Distribuciones EBS  | 92        | 12       | 11      | (crear)       | ✅ Activo |
| Tech Store          | 102       | 10       | 10      | admin_tech    | ✅ Activo |

> **Nota:** Los tenants "Mauricio" y "Distribuciones EBS" necesitan crear su usuario administrador desde el panel de super-admin.

---

## 🧪 Flujo de Pruebas

### 1. Login Super Admin
1. Ir a: http://localhost/catalogo2/index.php?controller=superAdmin&action=login
2. Usuario: `superadmin` / Contraseña: `SuperAdmin123!`
3. Verificar dashboard con estadísticas globales

### 2. Gestión de Tenants
1. Ver lista de tenants
2. Activar/desactivar tech-store
3. Verificar que la tienda muestra "⚠️ Tienda No Disponible" cuando está inactiva

### 3. Gestión de Usuarios
1. Desde super-admin, ir a "Usuarios" de cualquier tenant
2. Crear nuevo usuario
3. Probar login con el nuevo usuario
4. **Nota:** Los tenants Mauricio y Distribuciones EBS necesitan crear su usuario admin desde el super-admin

### 4. Crear Nuevo Tenant
1. Click en "Crear Nuevo Tenant"
2. Llenar formulario (slug único, WhatsApp, datos admin)
3. Sistema crea automáticamente: 3 categorías, 6 subcategorías, carpetas uploads
4. **Tip:** Ejecutar `php scripts/seed_datos.php` después para poblar con productos de prueba

### 5. Probar Tienda como Cliente
1. Navegar a cualquier tenant (ej: http://localhost/catalogo2/default)
2. Ver productos con stock visible
3. Agregar al carrito
4. Realizar pedido

---

**Última actualización:** 11 de Enero, 2026  
**Datos actualizados:** Todos los tenants tienen productos de prueba generados con el script de seeding.
