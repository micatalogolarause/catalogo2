# Generar Datos de Prueba

## ¿Qué hace?

El script `seed_datos.php` genera automáticamente para cada tenant:

- **3 categorías** (Electrónica, Ropa, Hogar)
- **3 subcategorías por categoría** (9 subcategorías totales)
- **10 productos por subcategoría** (90 productos totales)
- **10 pedidos** con diferentes estados

## Cómo usar

### Opción 1: Desde el navegador

1. Abre en tu navegador:
```
http://localhost/catalogo2/scripts/seed_datos.php
```

2. El script automáticamente creará los datos en **TODOS los tenants activos**

3. Verás un informe mostrando:
   - ✓ Categorías creadas
   - ✓ Subcategorías creadas  
   - ✓ Productos creados
   - ✓ Pedidos creados

### Opción 2: Desde la terminal

```bash
cd c:\xampp\htdocs\catalogo2
php scripts/seed_datos.php
```

## Datos que se generan

### Categorías
1. **Electrónica**
   - Smartphones (10 productos)
   - Laptops (10 productos)
   - Accesorios (10 productos)

2. **Ropa**
   - Hombres (10 productos)
   - Mujeres (10 productos)
   - Niños (10 productos)

3. **Hogar**
   - Cocina (10 productos)
   - Muebles (10 productos)
   - Decoración (10 productos)

### Pedidos

Se crean 10 pedidos con:
- Estados variados (en_pedido, alistado, empaquetado, etc.)
- 3-5 productos cada uno
- Datos de cliente aleatorios
- Totales calculados automáticamente

## Cambios en Editar Producto

**Problema:** Al cambiar de categoría, se perdía la subcategoría actual.

**Solución:** Ahora cuando cambias de categoría:
- ✓ Se carga la nueva categoría
- ✓ Se mantiene la subcategoría actual seleccionada
- ✓ Se pueden seleccionar subcategorías de la nueva categoría

## Notas

- El script verifica si los datos ya existen antes de crear duplicados
- Cada ejecución solo crea datos **nuevos** (no duplica)
- Los precios de productos son aleatorios entre $50 y $500
- Los stocks son aleatorios entre 10 y 50 unidades
- Las imágenes son referencias placeholder

## Próximos pasos

Para agregar imágenes reales a los productos:
1. Sube imágenes al directorio `public/images/productos/`
2. Actualiza los nombres de imagen en la BD manualmente
3. O modifica el script para usar imágenes específicas
