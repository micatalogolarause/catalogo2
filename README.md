# 🛒 Distribuciones EBS - Catálogo Digital

## 📋 Descripción del Proyecto

Sistema completo de tienda virtual desarrollado en PHP con patrón MVC, Bootstrap 5, MySQL y compatible con XAMPP e IIS en Windows Server 2019.

### ✨ Características principales:
- ✅ Catálogo dinámico de productos con categorías y subcategorías
- ✅ Carrito de compras con AJAX y persistencia en BD
- ✅ Sistema de pedidos completo con workflow de preparación
- ✅ Panel de administración responsive
- ✅ CRUD de productos con gestión de imágenes
- ✅ **Notificaciones por WhatsApp (sin API - usando wa.me)**
- ✅ Base de datos MySQL con auto-instalación
- ✅ Compatible con XAMPP e IIS
- ✅ **Acceso por IP pública configurado**
- ✅ **Listo para producción en Windows Server 2019**
- ✅ Mobile-first responsive design
- ✅ Scripts automatizados de backup y monitoreo

---

## 🚀 INSTALACIÓN RÁPIDA

### Para Desarrollo (XAMPP)

1. **Copiar archivos**
   ```
   C:\xampp\htdocs\catalogo2
   ```

2. **Iniciar XAMPP**
   - Apache ✓
   - MySQL ✓

3. **Acceder**
   ```
   http://localhost/catalogo2
   ```

**La base de datos se crea automáticamente** ⚡

### Para Producción (Windows Server 2019 + IIS)

#### Opción A: Instalación Automatizada (⚡ Recomendado)

```powershell
# Ejecutar PowerShell como Administrador
cd C:\xampp\htdocs\catalogo2

# Ejecutar instalador
.\Deploy-TiendaEBS.ps1 -DBPassword "TuContraseñaSegura2026!"
```

**¡Listo en 15 minutos!** 🎉

#### Opción B: Instalación Manual

Ver documentación completa: **`DEPLOYMENT_WINDOWS_SERVER_2019.md`**

---

## 📚 DOCUMENTACIÓN COMPLETA

| Documento | Descripción |
|-----------|-------------|
| **`IMPLEMENTACION_RAPIDA.md`** | ⚡ Guía rápida de implementación |
| **`DEPLOYMENT_WINDOWS_SERVER_2019.md`** | 📖 Guía completa paso a paso para producción |
| **`REVISION_CODIGO_PRODUCCION.md`** | ✅ Revisión de código y checklist |
| **`INSTALACION_IIS.md`** | 🌐 Configuración básica de IIS |
| **`INSTALACION.md`** | 💻 Instalación en XAMPP |
| **`INICIO_RAPIDO.md`** | 🎯 Guía de uso de la aplicación |

---

## 🔐 CREDENCIALES POR DEFECTO

### Panel de Administración
- **URL:** `http://localhost/catalogo2/index.php?controller=admin&action=login`
- **Usuario:** admin
- **Contraseña:** admin123
- ⚠️ **CAMBIAR ANTES DE PRODUCCIÓN**

### Base de Datos (Desarrollo)
- **Host:** localhost
- **Usuario:** root
- **Contraseña:** (vacía)
- **Base de datos:** catalogo_tienda

---

## 📁 Estructura del Proyecto

```
catalogo2/
├── app/
│   ├── controllers/          # Controladores MVC
│   ├── models/              # Modelos de datos
│   └── views/               # Vistas HTML
│       ├── admin/           # Panel administrativo
│       ├── tienda/          # Tienda pública
│       └── layout/          # Headers y footers
├── config/                  # Configuración y instalación
├── database/                # Scripts SQL
├── public/
│   ├── css/                 # Estilos CSS
│   ├── js/                  # JavaScript
│   └── images/
│       └── productos/       # Imágenes de productos
├── index.php                # Punto de entrada
├── .htaccess                # Reescritura de URLs (Apache)
└── web.config               # Configuración para IIS
```

---

## 🔧 Configuración

### XAMPP/Apache (Windows)

1. Habilita `mod_rewrite` en Apache:
   - Edita `C:\xampp\apache\conf\httpd.conf`
   - Descomenta: `LoadModule rewrite_module modules/mod_rewrite.so`
   - Reinicia Apache

2. El archivo `.htaccess` se encarga de las reescrituras de URL

### Windows Server con IIS

1. Copia `web.config` a la raíz del proyecto
2. En IIS Manager:
   - Crea un sitio web apuntando a `C:\xampp\htdocs\catalogo2`
   - Asegúrate que el Pool de aplicaciones tenga permisos de lectura/escritura
   - Habilita la reescritura de URLs (Module rewrite debe estar instalado)

### Configuración de MySQL

1. Asegúrate que MySQL esté corriendo
2. Las credenciales por defecto en `config/database.php`:
   - Host: `localhost`
   - Usuario: `root`
   - Contraseña: (vacía)
   - Base de datos: se crea automáticamente como `catalogo_tienda`

---

## 📊 Base de Datos Automática

Cuando accedes por primera vez, el sistema:
1. ✅ Crea la BD `catalogo_tienda`
2. ✅ Crea todas las tablas necesarias
3. ✅ Inserta usuario admin
4. ✅ Inserta 3 categorías de ejemplo
5. ✅ Inserta 6 subcategorías
6. ✅ Inserta 10 productos de prueba
7. ✅ Genera imágenes placeholder para los productos

---

## 🛍️ Uso de la Tienda

### Para Clientes:

1. **Navegar por categorías**: Usa el menú superior
2. **Ver productos**: Haz clic en cualquier producto
3. **Agregar al carrito**: Botón "Agregar al Carrito"
4. **Carrito**: Revisa, modifica cantidades o vacía
5. **Checkout**: Completa datos y envía pedido
6. **Confirmación**: Recibirás notificación por WhatsApp

### Para Administradores:

1. **Login**: `http://localhost/catalogo2/index.php?controller=admin&action=login`
2. **Dashboard**: Vista general de estadísticas
3. **Categorías**: CRUD completo
4. **Productos**: Crear, editar, eliminar, subir imágenes
5. **Pedidos**: Ver, actualizar estado, ver detalles

---

## 🔐 Seguridad

- ✅ Contraseñas hasheadas con SHA256
- ✅ Consultas preparadas contra SQL Injection
- ✅ Validación de entrada con sanitizar()
- ✅ Sesiones de usuario
- ✅ Headers de seguridad (X-Frame-Options, X-Content-Type-Options)
- ✅ Protección contra CSRF (verificación de sesión)

---

## 📱 Integración WhatsApp

Para habilitar notificaciones por WhatsApp:

1. Regístrate en [Twilio](https://www.twilio.com)
2. Obtén credenciales (Account SID, Auth Token, Phone Number)
3. Edita `config/config.php`:

```php
define('WHATSAPP_ACCOUNT_SID', 'your_account_sid');
define('WHATSAPP_AUTH_TOKEN', 'your_auth_token');
define('WHATSAPP_PHONE_FROM', '+1234567890');
```

4. Descomenta el código en `tiendaController.php` línea ~180

---

## 🌐 Acceso por IP Pública

### En XAMPP:

1. Obtén tu IP local: `ipconfig` (Busca IPv4 Address)
2. Accede desde: `http://TU_IP:80/catalogo2`

### En Windows Server:

1. Configura el sitio en IIS para escuchar en todas las IPs
2. Abre puertos 80 (HTTP) y 443 (HTTPS) en firewall
3. Accede desde: `http://TU_IP_PUBLICA/catalogo2`

### Nota de Seguridad:

- Usa HTTPS en producción (SSL Certificate)
- Configura firewall correctamente
- Usa contraseñas fuertes
- Habilita autenticación en base de datos

---

## 🐛 Solución de Problemas

### Error "Base de datos no encontrada"
- Verifica que MySQL está corriendo
- Revisa credenciales en `config/database.php`

### Error "Módulo rewrite no encontrado"
- En Apache: Habilita `mod_rewrite`
- En IIS: Instala URL Rewrite Module

### Errores de imagen al subir
- Verifica permisos de carpeta `public/images/productos/`
- Cambia permisos: `icacls "ruta" /grant Everyone:(OI)(CI)F`

### Error 404 en admin
- Verifica la sesión está activa
- Limpia cookies del navegador

---

## 📝 Archivos Importantes

| Archivo | Descripción |
|---------|-------------|
| `index.php` | Punto de entrada |
| `config/database.php` | Conexión a BD |
| `config/config.php` | Configuración general |
| `config/installer.php` | Instalación automática |
| `web.config` | Configuración IIS |
| `.htaccess` | Reescritura URLs Apache |

---

## 🚀 Próximas Mejoras

- [ ] Sistema de pago (Paypal, Stripe)
- [ ] Autenticación de clientes
- [ ] Historial de pedidos para clientes
- [ ] Sistema de reseñas y calificaciones
- [ ] Reportes de ventas avanzados
- [ ] Descuentos y cupones
- [ ] Integración con más proveedores de SMS

---

## 📞 Soporte

Para problemas o preguntas, revisa:
- Logs de PHP: `C:\xampp\apache\logs\error.log`
- Logs de MySQL: `C:\xampp\mysql\data\mysql_error.log`

---

## 📄 Licencia

Proyecto de código abierto. Úsalo libremente para tus proyectos.

---

**Versión:** 1.0  
**Última actualización:** Diciembre 2024
