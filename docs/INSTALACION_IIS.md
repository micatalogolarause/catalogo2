# Guía de Despliegue en IIS - Windows Server

## 📋 Requisitos Previos
- Windows Server con IIS instalado
- PHP 7.4 o superior instalado en IIS
- MySQL/MariaDB instalado
- Extensión PHP MySQLi habilitada

## 🚀 Instalación Paso a Paso

### 1. Instalar Módulos Necesarios en IIS
Abrir **Administrador del servidor** → **Agregar roles y características**:
- ✅ URL Rewrite Module 2.1 (descargar desde: https://www.iis.net/downloads/microsoft/url-rewrite)
- ✅ PHP Manager for IIS (opcional, facilita configuración)

### 2. Configurar PHP en IIS
```powershell
# Verificar que PHP esté registrado en IIS
php -v

# Habilitar extensiones necesarias en php.ini:
extension=mysqli
extension=mbstring
extension=openssl
```

### 3. Copiar Archivos al Servidor
Copiar todo el contenido de `catalogo2` a:
```
C:\inetpub\wwwroot\catalogo2
```

O a la ruta que prefieras para tu sitio.

### 4. Configurar Sitio en IIS

#### Opción A: Como aplicación en sitio por defecto
1. Abrir **Administrador de IIS**
2. Expandir **Sitios** → **Default Web Site**
3. Clic derecho → **Agregar aplicación**
   - Alias: `catalogo2`
   - Ruta física: `C:\inetpub\wwwroot\catalogo2`
   - Grupo de aplicaciones: `DefaultAppPool` (o crear uno nuevo)

#### Opción B: Como sitio independiente (con IP y puerto específico)
1. Abrir **Administrador de IIS**
2. Clic derecho en **Sitios** → **Agregar sitio web**
   - Nombre del sitio: `TiendaVirtual`
   - Ruta física: `C:\inetpub\wwwroot\catalogo2`
   - Tipo: `http`
   - Dirección IP: `192.168.x.x` (tu IP pública/privada)
   - Puerto: `80` o `8080` (el que prefieras)
   - Nombre de host: (dejar vacío para IP)

### 5. Configurar Permisos
Dar permisos de lectura/escritura a la carpeta:
```powershell
# Ejecutar en PowerShell como Administrador
icacls "C:\inetpub\wwwroot\catalogo2" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\catalogo2\public" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

### 6. Configurar Firewall (si es necesario)
```powershell
# Permitir tráfico en el puerto configurado
New-NetFirewallRule -DisplayName "IIS Tienda HTTP" -Direction Inbound -Protocol TCP -LocalPort 80 -Action Allow
# O para puerto 8080:
New-NetFirewallRule -DisplayName "IIS Tienda HTTP 8080" -Direction Inbound -Protocol TCP -LocalPort 8080 -Action Allow
```

### 7. Configurar Base de Datos MySQL
1. Crear base de datos (se creará automáticamente al primer acceso)
2. O ejecutar manualmente:
```sql
mysql -u root -p < C:\inetpub\wwwroot\catalogo2\database\schema.sql
```

### 8. Verificar Configuración

#### Archivo web.config
El archivo `web.config` ya está incluido y configurado para:
- ✅ Reescritura de URLs
- ✅ Headers de seguridad
- ✅ Manejo de archivos PHP
- ✅ Límites de tamaño de archivos

#### Archivo config.php
La configuración se auto-detecta para trabajar con:
- ✅ IP:Puerto (ej: `http://192.168.1.100:8080`)
- ✅ Dominio con puerto (ej: `http://mitienda.com:8080`)
- ✅ HTTPS automático
- ✅ Rutas relativas dinámicas

## 🔧 Solución de Problemas Comunes

### Error: "HTTP 500 - Error interno del servidor"
**Solución:**
1. Verificar permisos de carpeta
2. Revisar que PHP esté correctamente instalado
3. Habilitar errores detallados temporalmente en `web.config`:
```xml
<httpErrors errorMode="Detailed" />
```

### Error: "No se encuentra la página" después del login
**Causa:** El módulo URL Rewrite no está instalado.
**Solución:**
1. Descargar URL Rewrite 2.1 desde: https://www.iis.net/downloads/microsoft/url-rewrite
2. Instalarlo
3. Reiniciar IIS: `iisreset`

### Error: "Call to undefined function mysqli_connect"
**Solución:**
1. Editar `php.ini` (ubicar con `php --ini`)
2. Descomentar: `extension=mysqli`
3. Reiniciar IIS: `iisreset`

### Las rutas admin no funcionan
**Solución:**
Verificar que `web.config` existe en la raíz del proyecto y contiene las reglas de reescritura.

### No se muestran imágenes
**Solución:**
Verificar permisos en carpeta `public/images`:
```powershell
icacls "C:\inetpub\wwwroot\catalogo2\public\images" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

## 🌐 Acceso a la Aplicación

### Desde el servidor local:
```
http://localhost/catalogo2
http://127.0.0.1/catalogo2
```

### Desde red local/Internet (con IP y puerto):
```
http://192.168.1.100:8080
http://TU_IP_PUBLICA:8080
```

### Credenciales por defecto:
**Admin:**
- Usuario: `admin`
- Contraseña: `admin123`

**Usuarios de prueba:**
- Usuario: `usuario1` / Contraseña: `pass123`
- Usuario: `usuario2` / Contraseña: `pass123`

## 📝 Notas Importantes
1. ✅ El archivo `web.config` es para IIS (equivalente a `.htaccess` de Apache)
2. ✅ La configuración detecta automáticamente IP:puerto
3. ✅ Funciona tanto en XAMPP como en IIS sin cambios manuales
4. ✅ Si cambias de puerto o IP, no necesitas editar nada - se detecta automáticamente
5. ⚠️ Para producción, cambiar contraseñas en `config/database.php`

## 🔒 Seguridad para Producción
Antes de usar en producción:
1. Cambiar contraseña de MySQL
2. Actualizar credenciales admin por defecto
3. Configurar HTTPS (certificado SSL)
4. Deshabilitar mensajes de error detallados
5. Revisar permisos de archivos (solo lectura para la mayoría)

---
✅ **La aplicación ahora está lista para funcionar en IIS con Windows Server**
