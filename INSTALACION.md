# GUÍA DE INSTALACIÓN Y CONFIGURACIÓN

## Cambios Rápidos Necesarios

### 1. Configuración de Base de Datos
Archivo: `config/database.php`
```php
define('DB_HOST', 'localhost');      // Tu servidor MySQL
define('DB_USER', 'root');           // Tu usuario MySQL
define('DB_PASS', '');               // Tu contraseña MySQL
define('DB_NAME', 'catalogo_tienda');
```

### 2. Integración WhatsApp (Opcional)
Archivo: `config/config.php`
```php
define('WHATSAPP_ACCOUNT_SID', 'your_account_sid');
define('WHATSAPP_AUTH_TOKEN', 'your_auth_token');
define('WHATSAPP_PHONE_FROM', '+1234567890');
```

### 3. URL de la Aplicación
Edita `APP_URL` en `config/config.php` según donde alojes:
```php
// Para XAMPP local
define('APP_URL', 'http://localhost/catalogo2');

// Para IP pública
define('APP_URL', 'http://192.168.1.100/catalogo2');
```

---

## Primeros Pasos

### 1. Iniciar XAMPP
```
Windows: Abre XAMPP Control Panel
- Inicia Apache
- Inicia MySQL
```

### 2. Acceder al Proyecto
```
Navegador: http://localhost/catalogo2
- La BD se crea automáticamente
- Se cargan 10 productos de ejemplo
```

### 3. Login Admin
```
URL: http://localhost/catalogo2/index.php?controller=admin&action=login
Usuario: admin
Contraseña: admin123
```

---

## Productos Pre-cargados

1. **iPhone 15 Pro** - Electrónica/Smartphones ($999.99)
2. **Samsung Galaxy S24** - Electrónica/Smartphones ($899.99)
3. **MacBook Pro 16** - Electrónica/Laptops ($2499.99)
4. **Dell XPS 15** - Electrónica/Laptops ($1799.99)
5. **Camiseta Premium Hombre** - Ropa/Hombre ($49.99)
6. **Pantalón Casual Hombre** - Ropa/Hombre ($79.99)
7. **Vestido Casual Mujer** - Ropa/Mujer ($89.99)
8. **Jeans Premium Mujer** - Ropa/Mujer ($99.99)
9. **Horno Eléctrico** - Hogar/Cocina ($299.99)
10. **Juego de Cama King** - Hogar/Dormitorio ($199.99)

Todos con imágenes placeholder de demostración.

---

## Estructura de Usuarios

### Base de Datos Automática:
```
Base de Datos: catalogo_tienda

Tablas principales:
- categorias
- subcategorias
- productos
- usuarios
- clientes
- pedidos
- pedido_detalles
- carrito
```

### Usuario Predeterminado:
```
Usuario: admin
Contraseña: admin123
Rol: Administrador
```

---

## Configuración IIS (Windows Server)

### Pasos:

1. **Abrir IIS Manager**
   - Windows + R → `inetmgr`

2. **Crear Sitio Web**
   - Click derecho en "Sitios" → "Agregar sitio web"
   - Nombre: `Catalogo Tienda`
   - Ruta física: `C:\xampp\htdocs\catalogo2`
   - Enlace HTTP: puerto 80

3. **Permisos**
   - Click derecho carpeta → Propiedades → Seguridad
   - Agregar `IIS_IUSRS` con permisos de Lectura y Escritura
   - Especialmente para: `public/images/productos/`

4. **Módulos URL Rewrite**
   - Descargar desde: https://www.iis.net/downloads/microsoft/url-rewrite
   - Instalar (requiere reinicio)

5. **Configuración web.config**
   - El archivo ya está en la raíz del proyecto

---

## Verificar Instalación

### ✅ Checklist:

- [ ] XAMPP instalado y ejecutándose
- [ ] MySQL está activo
- [ ] Archivos en `C:\xampp\htdocs\catalogo2`
- [ ] Acceso a `http://localhost/catalogo2` sin errores
- [ ] Base de datos creada automáticamente
- [ ] 10 productos visibles en la tienda
- [ ] Login admin funciona
- [ ] Panel administrativo accesible
- [ ] Carrito de compras funciona
- [ ] Imágenes se muestran correctamente

---

## Solución de Problemas Comunes

### "Error de conexión a MySQL"
```
Solución:
1. Verifica que MySQL esté corriendo en XAMPP
2. Revisa usuario/contraseña en config/database.php
3. Reinicia MySQL
```

### "Las URLs no funcionan correctamente"
```
Solución Apache:
1. Habilita mod_rewrite: httpd.conf
2. Reinicia Apache

Solución IIS:
1. Instala URL Rewrite Module
2. Verifica web.config
3. Reinicia IIS
```

### "No se ve la carpeta de imágenes"
```
Solución:
1. Crea: C:\xampp\htdocs\catalogo2\public\images\productos\
2. Cambia permisos con: icacls "ruta" /grant Everyone:(OI)(CI)F
3. Reinicia Apache/IIS
```

### "Carrito vacío después de recargar"
```
Solución:
1. Revisa que las sesiones estén habilitadas
2. Limpia cookies del navegador
3. Verifica carpeta de sesiones en: C:\xampp\php\tmp
```

---

## Copia de Seguridad

### Hacer backup:

```bash
# Base de datos
mysqldump -u root catalogo_tienda > backup.sql

# Archivos
xcopy C:\xampp\htdocs\catalogo2 D:\backup\catalogo2 /E /I
```

### Restaurar backup:

```bash
# Base de datos
mysql -u root catalogo_tienda < backup.sql

# Archivos
xcopy D:\backup\catalogo2 C:\xampp\htdocs\catalogo2 /E /I /Y
```

---

## Próximos Pasos

1. Personalizar nombre de la tienda
2. Cambiar logo y colores
3. Configurar WhatsApp
4. Agregar más productos
5. Crear categorías propias
6. Implementar sistema de pago
7. Configurar dominio propio
8. Implementar SSL/HTTPS

---

**¡Listo! Tu tienda virtual está lista para usar.**
