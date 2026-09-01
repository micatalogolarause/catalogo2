# 🚀 Guía de Deployment - Windows Server 2019 + IIS + XAMPP

## 📋 Requisitos Previos

✅ **Sistema Operativo:** Windows Server 2019 o superior  
✅ **PHP:** 8.2+ (vía XAMPP)  
✅ **MySQL/MariaDB:** 10.4+ (vía XAMPP)  
✅ **Servidor Web:** IIS 10 o XAMPP Apache  
✅ **IP Pública:** 34.193.89.155  
✅ **Puertos:** 81 (XAMPP), 80/443 (IIS, opcional)

---

## 🔧 Paso 1: Configuración de XAMPP en Puerto 81

### 1.1 Editar archivo de configuración Apache

```
C:\xampp\apache\conf\httpd.conf
```

**Cambiar:**
```apache
Listen 80
```

**Por:**
```apache
Listen 81
```

### 1.2 Editar httpd-ssl.conf (si usas HTTPS)

```
C:\xampp\apache\conf\extra\httpd-ssl.conf
```

**Cambiar:**
```apache
Listen 443
```

**Por:**
```apache
Listen 8443
```

### 1.3 Reiniciar Apache

```bash
# Panel de control de XAMPP o:
C:\xampp\apache\bin\apache.exe -k restart
```

---

## 📂 Paso 2: Estructura de Carpetas del Proyecto

```
C:\xampp\htdocs\catalogo2\
├── index.php                    # Punto de entrada
├── config/
│   ├── config.php              # Configuración (✅ Auto-detecta IP:puerto)
│   ├── database.php            # Conexión BD
│   ├── TenantResolver.php       # Multi-tenancy
│   └── ...
├── app/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   └── ...
├── public/
│   ├── css/
│   ├── js/
│   ├── images/
│   ├── tenants/                # Carpetas por tenant (uploads)
│   └── .htaccess              # ✅ Redirige requests
├── .htaccess                   # ✅ Configuración URL rewriting
└── scripts/
    └── seed_datos.php
```

---

## 🔐 Paso 3: Permisos de Carpetas (IMPORTANTE)

### Carpetas que necesitan permisos de escritura:

```bash
# Acceso a la carpeta del proyecto
icacls C:\xampp\htdocs\catalogo2 /grant "IIS AppPool\DefaultAppPool":F /T

# O específicamente para uploads:
icacls C:\xampp\htdocs\catalogo2\public\tenants /grant "IIS AppPool\DefaultAppPool":F /T
icacls C:\xampp\htdocs\catalogo2\public\images /grant "IIS AppPool\DefaultAppPool":F /T
```

**Para XAMPP (Apache):**
```bash
icacls C:\xampp\htdocs\catalogo2\public\tenants /grant "Users":M /T
```

---

## 🗄️ Paso 4: Base de Datos

### Conexión automática desde config/database.php

```php
define('DB_HOST', 'localhost');      // XAMPP: localhost
define('DB_USER', 'root');           // Usuario por defecto
define('DB_PASS', '');               // Sin contraseña por defecto
define('DB_NAME', 'catalogo_tienda');
```

**✅ El sistema crear automáticamente la BD en el primer acceso**

Si necesitas acceso remoto a MySQL:

```
C:\xampp\mysql\bin\mysql -u root -h 0.0.0.0
```

Edita `C:\xampp\mysql\bin\my.ini`:
```ini
bind-address = 0.0.0.0
```

---

## 🌐 Paso 5: Configuración de URLs (Auto-detectada)

### La aplicación detecta automáticamente:

✅ **IP + Puerto:** `http://34.193.89.155:81/catalogo2`  
✅ **Dominio + Puerto:** `http://tu-dominio.com:81/catalogo2`  
✅ **HTTPS:** `https://34.193.89.155:8443/catalogo2`

**No requiere cambios en config.php**

### URLs de acceso:

```
# Super-Admin
http://34.193.89.155:81/catalogo2/index.php?controller=superAdmin&action=login

# Tienda Default
http://34.193.89.155:81/catalogo2/default

# Tienda Tech Store
http://34.193.89.155:81/catalogo2/tech-store

# Admin Tienda Default
http://34.193.89.155:81/catalogo2/default/index.php?controller=admin
```

---

## 📝 Paso 6: Configuración de .htaccess (URL Rewriting)

### Archivo: `C:\xampp\htdocs\catalogo2\.htaccess`

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Permitir directorios y archivos existentes
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Redirigir todo a index.php manteniendo query string
    RewriteRule ^(.*)$ index.php?/$1 [QSA,L]
</IfModule>

# Bloquear acceso a archivos sensibles
<FilesMatch "\.(env|php\.bak|sql|log)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Permitir acceso a assets
<FilesMatch "\.(jpg|jpeg|png|gif|css|js|ico|svg|woff|woff2|ttf|eot)$">
    Order allow,deny
    Allow from all
</FilesMatch>
```

### Para IIS (si lo usas):

Crear `web.config` en raíz del proyecto:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="Rewrite requested file or directory to itself">
                    <match url="^(.*)$" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php?/{R:1}" />
                </rule>
            </rules>
        </rewrite>
        <security>
            <requestFiltering>
                <fileExtensions>
                    <add fileExtension=".env" allowed="false" />
                    <add fileExtension=".sql" allowed="false" />
                </fileExtensions>
            </requestFiltering>
        </security>
    </system.webServer>
</configuration>
```

---

## 🔒 Paso 7: Seguridad en Producción

### 7.1 Crear archivo `.env` (sin commitear a git)

```ini
# .env (raíz del proyecto)
APP_ENV=production
DB_HOST=localhost
DB_USER=root
DB_PASS=tu_contraseña_segura
DB_NAME=catalogo_tienda
SESSION_TIMEOUT=3600
```

**IMPORTANTE:** Cambiar contraseña de MySQL por defecto

```bash
C:\xampp\mysql\bin\mysql -u root
> ALTER USER 'root'@'localhost' IDENTIFIED BY 'TuContraseñaSegura123!';
> FLUSH PRIVILEGES;
```

### 7.2 Deshabilitar instalador en producción

Editar `index.php`:

```php
// Comentar o eliminar sección de instalación automática
// if (!$resultado || $resultado->num_rows == 0) {
//     require_once 'config/installer.php';
// }
```

### 7.3 Configurar logs

Crear carpeta `C:\xampp\htdocs\catalogo2\logs` con permisos de escritura

En `config/database.php`:

```php
error_log("Evento importante", 3, APP_ROOT . "/logs/app.log");
```

---

## ✅ Paso 8: Checklist de Deployment

- [ ] XAMPP instalado en Windows Server
- [ ] Apache escuchando puerto 81 (probado con http://localhost:81)
- [ ] MySQL ejecutándose y accesible
- [ ] Contraseña de MySQL cambiada
- [ ] Carpeta `catalogo2` en `C:\xampp\htdocs\`
- [ ] Permisos de escritura en carpetas de uploads
- [ ] `.htaccess` o `web.config` configurado
- [ ] Acceso a BD remota habilitado (si es necesario)
- [ ] Certificado SSL configurado (opcional)
- [ ] Firewall permitiendo puertos 81, 3306
- [ ] Primera carga en http://34.193.89.155:81/catalogo2 exitosa
- [ ] BD `catalogo_tienda` creada automáticamente
- [ ] Super-admin login funcional (superadmin/SuperAdmin123!)

---

## 🧪 Paso 9: Testing Post-Deployment

### 9.1 Verificar conectividad

```bash
# Acceso público desde otra máquina
curl http://34.193.89.155:81/catalogo2/index.php?controller=superAdmin&action=login

# O abrir en navegador
http://34.193.89.155:81/catalogo2/
```

### 9.2 Pruebas funcionales

```php
# Acceso a super-admin
Usuario: superadmin
Contraseña: SuperAdmin123!

# Acceso a tenant
http://34.193.89.155:81/catalogo2/default

# Admin de tenant
http://34.193.89.155:81/catalogo2/default/index.php?controller=admin
Usuario: admin
Contraseña: admin123
```

### 9.3 Verificar BD

```bash
C:\xampp\mysql\bin\mysql -u root -p
> USE catalogo_tienda;
> SHOW TABLES;
> SELECT * FROM tenants;
```

---

## 🆘 Solución de Problemas

### Error: "No se puede conectar a MySQL"

```bash
# Verificar servicio MySQL
net start MySQL

# O vía panel XAMPP:
C:\xampp\xampp-control.exe
```

### Error: "Carpeta uploads sin permisos"

```bash
icacls C:\xampp\htdocs\catalogo2\public\tenants /grant:r "SYSTEM":(OI)(CI)(F)
```

### URLs no reescriben correctamente

- ✅ Verificar `mod_rewrite` habilitado en Apache (`httpd.conf`)
- ✅ Confirmar `.htaccess` existe en raíz
- ✅ Verificar que `AllowOverride All` en `httpd.conf`

### Lentitud o timeout en BD

- ✅ Aumentar `max_execution_time` en `php.ini` (de 30 a 300)
- ✅ Verificar conexión TCP es local (localhost vs 127.0.0.1)

---

## 📞 Contacto y Soporte

- **Documentación:** [CREDENCIALES.md](CREDENCIALES.md)
- **Base de datos:** `catalogo_tienda`
- **Logs:** `C:\xampp\htdocs\catalogo2\logs\`
- **PHP Info:** http://34.193.89.155:81/phpinfo.php

---

**Última actualización:** 13 de Enero, 2026  
**Versión:** 1.0 Production Ready
