# INICIO RÁPIDO EN 3 PASOS

## 🚀 Paso 1: Iniciar XAMPP
```
1. Abre XAMPP Control Panel
2. Haz clic en "Start" en Apache
3. Haz clic en "Start" en MySQL
4. Espera a que diga "Running"
```

## 🌐 Paso 2: Acceder a la Tienda
```
Abre tu navegador y ve a:
http://localhost/catalogo2

La base de datos se crea automáticamente en el primer acceso.
```

## 🔐 Paso 3: Usar el Panel Admin
```
URL: http://localhost/catalogo2/?controller=admin&action=login

Usuario: admin
Contraseña: admin123
```

---

## ✅ ¿Funcionando todo?

Si ves esto significa que está todo bien:
- ✅ La tienda carga con productos
- ✅ Puedes agregar productos al carrito
- ✅ El panel admin abre con login
- ✅ Puedes crear/editar productos

---

## 🆘 Si algo no funciona

### Error: "No se puede conectar a la base de datos"
```
1. Verifica que MySQL esté corriendo en XAMPP
2. Abre http://localhost/phpmyadmin
3. Si funciona phpmyadmin, el problema es otro
4. Revisa que Apache también esté corriendo
```

### Error 404 - Página no encontrada
```
1. Verifica que Apache esté corriendo
2. Limpia el cache del navegador (Ctrl+Shift+Supr)
3. Intenta acceder a: http://localhost/catalogo2/index.php
```

### Las imágenes no se ven
```
1. Verifica que la carpeta public/images/productos exista
2. Abre http://localhost/phpmyadmin
3. Busca la tabla "productos"
4. Verifica que el campo "imagen" tenga valores
```

---

## 📱 Funcionalidades que puedes probar

### En la Tienda:
1. ✅ Navega por categorías
2. ✅ Haz clic en un producto
3. ✅ Haz clic en "Agregar al Carrito"
4. ✅ Ve al carrito (icono arriba)
5. ✅ Completa el checkout

### En el Admin:
1. ✅ Crea una nueva categoría
2. ✅ Crea un nuevo producto
3. ✅ Sube una imagen
4. ✅ Edita un producto
5. ✅ Mira los pedidos creados

---

## 📞 Información Rápida

| Elemento | Valor |
|----------|-------|
| URL Tienda | http://localhost/catalogo2 |
| URL Admin | http://localhost/catalogo2/?controller=admin&action=login |
| Usuario Admin | admin |
| Contraseña Admin | admin123 |
| Base de Datos | catalogo_tienda |
| Usuario BD | root |
| Contraseña BD | (vacía) |

---

## 🎯 Estructura de URLs

```
// Frontend
http://localhost/catalogo2
http://localhost/catalogo2/?controller=tienda&action=inicio
http://localhost/catalogo2/?controller=tienda&action=categoria&id=1
http://localhost/catalogo2/?controller=tienda&action=producto&id=1
http://localhost/catalogo2/?controller=tienda&action=carrito
http://localhost/catalogo2/?controller=tienda&action=buscar&q=iPhone

// Admin
http://localhost/catalogo2/?controller=admin&action=login
http://localhost/catalogo2/?controller=admin&action=inicio
http://localhost/catalogo2/?controller=admin&action=productos
http://localhost/catalogo2/?controller=admin&action=crearProducto
http://localhost/catalogo2/?controller=admin&action=editarProducto&id=1
http://localhost/catalogo2/?controller=admin&action=pedidos
http://localhost/catalogo2/?controller=admin&action=verPedido&id=1
```

---

## 🎨 Personalización Rápida

### Cambiar nombre de la tienda:
```
Edita: config/config.php
Busca: APP_URL
Cambio el nombre donde dice "Tienda Virtual"
```

### Cambiar colores:
```
Edita: public/css/estilos.css
Busca: colores Bootstrap
Cambia los valores de color
```

### Agregar productos nuevos:
```
1. Ve al admin: .../?controller=admin&action=productos
2. Haz clic en "Nuevo Producto"
3. Completa datos y sube imagen
4. Haz clic en "Crear Producto"
```

---

## 💡 Consejos Útiles

- Limpia el cache del navegador si ves cambios raros
- Usa F12 para abrir DevTools si algo no funciona
- Revisa la consola JavaScript (F12 → Console)
- Los logs de error están en phpmyadmin
- Haz backup de la BD regularmente

---

## 📚 Archivos Importantes

- `config/database.php` - Conexión a la BD
- `config/config.php` - Configuración general
- `app/controllers/tiendaController.php` - Lógica tienda
- `app/controllers/adminController.php` - Lógica admin
- `public/css/estilos.css` - Estilos tienda
- `public/css/admin.css` - Estilos admin

---

**¡Listo! Tu tienda virtual está operativa. Disfruta desarrollando.** 🎉
