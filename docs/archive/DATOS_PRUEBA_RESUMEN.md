# Resumen: Datos de Prueba y Correcciones

## ✅ Completado

### 1. Script de Generación de Datos
**Archivo**: `scripts/seed_datos.php`

Crea automáticamente para CADA tenant:
- 3 Categorías
- 3 Subcategorías por categoría (9 total)
- 10 Productos por subcategoría (90 total)
- 10 Pedidos con detalles

**Cómo usar:**
```
http://localhost/catalogo2/scripts/seed_datos.php
```

El script:
- ✓ Verifica si datos ya existen (no crea duplicados)
- ✓ Genera precios aleatorios ($50-$500)
- ✓ Genera stocks aleatorios (10-50)
- ✓ Crea pedidos con estados variados
- ✓ Calcula totales automáticamente

### 2. Corrección: Editar Producto

**Problema antes:**
- Al cambiar categoría, se perdía la subcategoría actual
- Había que volver a seleccionar todo

**Solución implementada:**
- Se almacena el ID de subcategoría en un atributo `data-selected-id`
- Cuando cambias categoría, se mantiene la subcategoría seleccionada
- Si la subcategoría existe en la nueva categoría, se mantiene
- Si no existe, se puede seleccionar una nueva

**Archivo modificado:**
- `app/views/admin/editar_producto.php`

### 3. Documentación
**Archivos creados:**
- `SEED_DATOS_README.md` - Instrucciones de uso

## Datos de Prueba Incluidos

### Electrónica
- Smartphones: Samsung Galaxy S21, iPhone 13 Pro, Xiaomi 12, etc.
- Laptops: MacBook Pro, Dell XPS, HP Pavilion, etc.
- Accesorios: Cargadores, cables, fundas, audífonos, etc.

### Ropa
- Hombres: Camisetas, pantalones, camisas, polos, etc.
- Mujeres: Blusas, jeans, vestidos, tops, faltas, etc.
- Niños: Ropa infantil variada

### Hogar
- Cocina: Licuadora, microondas, cafetera, horno, etc.
- Muebles: Sofás, mesas, sillas, camas, closets, etc.
- Decoración: Cuadros, espejos, lámparas, cojines, etc.

## Próximos Pasos (Opcional)

Si necesitas imágenes reales para los productos:

1. Descarga imágenes de ejemplo
2. Súbelas a `public/images/productos/`
3. Actualiza los nombres en la BD

O modifica el script `seed_datos.php` para usar URLs de imágenes específicas.

## Verificación

Todos los archivos pasaron verificación de sintaxis:
- ✅ `scripts/seed_datos.php`
- ✅ `app/views/admin/editar_producto.php`

## Para ejecutar

### Opción 1: Browser
```
http://localhost/catalogo2/scripts/seed_datos.php
```

### Opción 2: Terminal
```
php scripts/seed_datos.php
```

El script mostrará un informe detallado de lo que creó.

---

**Estado:** ✅ Listo para usar
