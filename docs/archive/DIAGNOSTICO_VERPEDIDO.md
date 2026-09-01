# 🔍 DIAGNÓSTICO: verPedido No Carga en Otros Tenants

## Resumen del Problema
- **Síntoma**: verPedido solo carga detalles en tenant "mauricio", no en "distribuciones-ebs", "tech-store", etc.
- **Causa Probable**: TENANT_ID no se está resolviendo correctamente en algunos tenants

## Pasos de Diagnóstico

### PASO 1: Verificar Tenants en la BD
Accede a: `http://larause:81/catalogo2/diagnostico_tenants.php`

**Qué esperar**:
- Deberías ver una tabla con todos los tenants registrados
- Cada tenant debería tener un `id` numérico y un `slug`
- Debería haber un contador de pedidos por tenant

**Qué verificar**:
- ¿Los slugs coinciden exactamente con los que usas en las URLs?
  - Ejemplos correctos: `mauricio`, `distribuciones-ebs`, `tech-store`
  - Ejemplos incorrectos: `Mauricio`, `Distribuciones-EBS`, `Tech Store`
  - Los slugs deben estar en minúsculas y con guiones (sin espacios)

### PASO 2: Acceder a verPedido desde Cada Tenant
Prueba estas URLs (reemplaza `123` con un ID de pedido existente):

**En mauricio** (el que funciona):
```
http://larause:81/catalogo2/mauricio/index.php?controller=admin&action=verPedido&id=123
```

**En distribuciones-ebs** (probablemente falla):
```
http://larause:81/catalogo2/distribuciones-ebs/index.php?controller=admin&action=verPedido&id=123
```

**En tech-store** (probablemente falla):
```
http://larause:81/catalogo2/tech-store/index.php?controller=admin&action=verPedido&id=123
```

### PASO 3: Usar Diagnóstico Específico de verPedido
Accede a: `http://larause:81/catalogo2/diagnostico_verpedido.php?id=123`

Esta página te mostrará:
- Qué tenant se resolvió desde la URL actual
- Si el pedido existe en el tenant resuelto
- Si el pedido existe en OTRO tenant (comparación)
- Los detalles del pedido (si existen)

**Resultado esperado en mauricio**:
```
✅ PEDIDO ENCONTRADO en tenant_id=1
Tabla de detalles con productos
```

**Resultado probablé en distribuciones-ebs**:
```
❌ PEDIDO NO ENCONTRADO en tenant_id=X
(El sistema te dirá en qué tenant_id está realmente el pedido)
```

## Interpretación de Resultados

### Caso A: Pedido en TODOS los Tenants
Si el diagnóstico muestra que el pedido existe en tenant_id=1 (mauricio) pero dices que no carga en otros tenants:
- **Posible causa**: Los pedidos se están creando SOLO en tenant_id=1, no en otros tenants
- **Solución**: Verificar la lógica de creación de pedidos en tiendaController

### Caso B: Pedido NO Existe en Otros Tenants
Si el diagnóstico muestra que los pedidos de distribuciones-ebs están guardados con tenant_id=2 o 3:
- **Posible causa**: El verPedido no está buscando en el tenant_id correcto
- **Síntoma**: TENANT_ID se está resolviendo como 1 en lugar de 2 o 3
- **Solución**: Debuggear TenantResolver.resolve()

## Comandos SQL para Verificación Rápida

Si tienes acceso a MySQL, ejecuta estos comandos:

```sql
-- Ver todos los tenants
SELECT id, nombre, slug, estado FROM tenants;

-- Ver pedidos por tenant
SELECT tenant_id, COUNT(*) as total_pedidos FROM pedidos GROUP BY tenant_id;

-- Ver detalles de un pedido específico
SELECT pd.id, pr.nombre, pd.cantidad, pd.estado_preparacion, pd.tenant_id 
FROM pedido_detalles pd
LEFT JOIN productos pr ON pr.id = pd.producto_id
WHERE pd.pedido_id = 123;
```

## Pasos Siguientes

**Si el diagnóstico muestra problema en TenantResolver**:
1. Accede a: `http://larause:81/catalogo2/mauricio/index.php` sin parámetros
2. Verifica que muestre: `TENANT_ID: 1`, `TENANT_SLUG: mauricio`
3. Luego: `http://larause:81/catalogo2/distribuciones-ebs/index.php`
4. Verifica que muestre el ID y slug correcto para distribuciones-ebs

**Si los slugs no coinciden**:
- Revisa la tabla `tenants` en la BD
- Los slugs deben ser exactamente como aparecen en la URL

**Si TENANT_ID es siempre 1**:
- TenantResolver no está detectando correctamente el slug desde la URL
- Necesitaremos revisar la función `detectTenantFromUrl()` en TenantResolver.php

## Reporta Tus Hallazgos

Cuando ejecutes los diagnósticos, reporta:
1. ¿Qué tenants aparecen en la tabla? (nombres exactos y slugs)
2. ¿Cuántos pedidos hay en cada tenant?
3. Para un ID de pedido específico:
   - ¿En qué tenant está almacenado?
   - ¿Qué TENANT_ID se resuelve en cada URL?
4. ¿Aparecen los detalles en algún tenant?

---

**Archivos de diagnóstico creados**:
- `diagnostico_tenants.php` - Vista general de todos los tenants
- `diagnostico_verpedido.php` - Análisis detallado de un pedido específico
