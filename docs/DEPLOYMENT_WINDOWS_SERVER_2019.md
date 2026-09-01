# 🚀 Guía de Implementación en Windows Server 2019 con IIS e IP Pública

**Distribuciones EBS - Catálogo Digital**  
**Versión:** 1.0 - Producción  
**Fecha:** Enero 2026

---

## 📑 Índice
1. [Requerimientos del Sistema](#requerimientos)
2. [Pre-instalación - Preparar el Servidor](#pre-instalacion)
3. [Instalación de Componentes](#instalacion-componentes)
4. [Configuración de IIS](#configuracion-iis)
5. [Configuración de Base de Datos](#configuracion-base-datos)
6. [Despliegue de la Aplicación](#despliegue)
7. [Configuración de IP Pública](#configuracion-ip-publica)
8. [Seguridad y Hardening](#seguridad)
9. [Optimización](#optimizacion)
10. [Mantenimiento](#mantenimiento)
11. [Solución de Problemas](#troubleshooting)

---

## <a name="requerimientos"></a>📋 1. REQUERIMIENTOS DEL SISTEMA

### Hardware Mínimo
- **CPU:** 2 núcleos @ 2.0 GHz
- **RAM:** 4 GB (recomendado 8 GB)
- **Disco:** 60 GB SSD (mínimo 40 GB libres)
- **Red:** Tarjeta de red con IP estática

### Hardware Recomendado (Producción)
- **CPU:** 4 núcleos @ 2.5 GHz o superior
- **RAM:** 16 GB
- **Disco:** 120 GB SSD NVMe
- **Red:** Conexión 100 Mbps+ con IP pública fija

### Software Requerido
| Componente | Versión Mínima | Versión Recomendada |
|-----------|----------------|---------------------|
| Windows Server | 2019 Standard | 2019 Datacenter |
| IIS | 10.0 | 10.0 |
| PHP | 7.4 | 8.1+ |
| MySQL / MariaDB | 5.7 / 10.3 | 8.0 / 10.11 |
| URL Rewrite Module | 2.1 | 2.1 |
| Visual C++ Redistributable | 2015-2022 | Última versión |

### Extensiones PHP Requeridas
```ini
extension=mysqli
extension=mbstring
extension=openssl
extension=curl
extension=fileinfo
extension=gd2
extension=json
extension=session
```

### Requisitos de Red
- ✅ IP pública estática (o Dynamic DNS)
- ✅ Puerto 80 (HTTP) abierto en firewall
- ✅ Puerto 443 (HTTPS) abierto en firewall
- ✅ Router con Port Forwarding configurado (80 → servidor:80)
- ✅ DNS configurado (opcional pero recomendado)

---

## <a name="pre-instalacion"></a>⚙️ 2. PRE-INSTALACIÓN - PREPARAR EL SERVIDOR

### 2.1 Actualizar Windows Server
```powershell
# Ejecutar Windows Update
Start-Process ms-settings:windowsupdate

# O mediante PowerShell
Install-Module PSWindowsUpdate
Get-WindowsUpdate
Install-WindowsUpdate -AcceptAll -AutoReboot
```

### 2.2 Configurar IP Estática
```powershell
# Ver adaptadores de red
Get-NetAdapter

# Configurar IP estática (reemplazar valores)
New-NetIPAddress -InterfaceAlias "Ethernet" -IPAddress 192.168.1.100 -PrefixLength 24 -DefaultGateway 192.168.1.1
Set-DnsClientServerAddress -InterfaceAlias "Ethernet" -ServerAddresses ("8.8.8.8","8.8.4.4")
```

### 2.3 Crear Usuario de Servicio (Opcional)
```powershell
# Crear usuario para IIS Application Pool
$Password = ConvertTo-SecureString "P@ssw0rdSegur0!" -AsPlainText -Force
New-LocalUser "IIS_TiendaEBS" -Password $Password -FullName "Usuario IIS Tienda" -Description "Usuario para app pool de Tienda EBS"
Add-LocalGroupMember -Group "IIS_IUSRS" -Member "IIS_TiendaEBS"
```

---

## <a name="instalacion-componentes"></a>💿 3. INSTALACIÓN DE COMPONENTES

### 3.1 Instalar IIS con Componentes Necesarios
```powershell
# Ejecutar como Administrador
Install-WindowsFeature -name Web-Server -IncludeManagementTools
Install-WindowsFeature -name Web-Asp-Net45
Install-WindowsFeature -name Web-CGI
Install-WindowsFeature -name Web-ISAPI-Ext
Install-WindowsFeature -name Web-ISAPI-Filter
Install-WindowsFeature -name Web-Includes
Install-WindowsFeature -name Web-HTTP-Errors
Install-WindowsFeature -name Web-Common-HTTP
Install-WindowsFeature -name Web-Performance
Install-WindowsFeature -name Web-Security
Install-WindowsFeature -name Web-Filtering
Install-WindowsFeature -name Web-IP-Security
```

### 3.2 Instalar PHP en Windows Server

#### Opción A: Instalación Manual (Recomendado)
```powershell
# 1. Descargar PHP desde https://windows.php.net/download/
# Elegir: PHP 8.1 - Non Thread Safe - x64

# 2. Extraer a C:\PHP
New-Item -Path "C:\PHP" -ItemType Directory
# Extraer ZIP descargado a C:\PHP

# 3. Agregar PHP al PATH
[Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\PHP", [EnvironmentVariableTarget]::Machine)

# 4. Copiar php.ini-production a php.ini
Copy-Item "C:\PHP\php.ini-production" "C:\PHP\php.ini"

# 5. Editar php.ini (abrir con Notepad++)
# Descomentar y configurar:
```

**Editar `C:\PHP\php.ini`:**
```ini
; Descomentar estas extensiones
extension_dir = "C:\PHP\ext"
extension=mysqli
extension=mbstring
extension=openssl
extension=curl
extension=fileinfo
extension=gd
extension=pdo_mysql

; Configuración de sesión
session.save_path = "C:\PHP\tmp"
upload_tmp_dir = "C:\PHP\tmp"

; Límites
upload_max_filesize = 10M
post_max_size = 12M
memory_limit = 256M
max_execution_time = 300

; Zona horaria
date.timezone = America/Bogota

; Errores (producción)
display_errors = Off
log_errors = On
error_log = "C:\PHP\logs\php_errors.log"
```

**Crear carpetas necesarias:**
```powershell
New-Item -Path "C:\PHP\tmp" -ItemType Directory
New-Item -Path "C:\PHP\logs" -ItemType Directory
icacls "C:\PHP\tmp" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\PHP\logs" /grant "IIS_IUSRS:(OI)(CI)M" /T
```

#### Opción B: Web Platform Installer (Alternativa)
```powershell
# Descargar e instalar Web Platform Installer
Start-Process "https://www.microsoft.com/web/downloads/platform.aspx"

# Buscar e instalar: "PHP 8.1.x for IIS"
```

### 3.3 Registrar PHP en IIS
```powershell
# Configurar PHP Handler en IIS
Import-Module WebAdministration

# Agregar PHP como FastCGI
Add-WebConfiguration -Filter /system.webServer/fastCgi -PSPath IIS:\ -Value @{
    fullPath="C:\PHP\php-cgi.exe"
    maxInstances=4
    instanceMaxRequests=10000
    activityTimeout=300
    requestTimeout=300
}

# Agregar Handler Mapping
New-WebHandler -Name "PHP_via_FastCGI" -Path "*.php" -Verb "*" -Modules "FastCgiModule" -ScriptProcessor "C:\PHP\php-cgi.exe" -ResourceType File
```

### 3.4 Instalar URL Rewrite Module 2.1
```powershell
# Descargar e instalar
$urlRewriteUrl = "https://download.microsoft.com/download/1/2/8/128E2E22-C1B9-44A4-BE2A-5859ED1D4592/rewrite_amd64_en-US.msi"
$installerPath = "$env:TEMP\urlrewrite2.msi"
Invoke-WebRequest -Uri $urlRewriteUrl -OutFile $installerPath
Start-Process msiexec.exe -ArgumentList "/i $installerPath /quiet /norestart" -Wait
Remove-Item $installerPath
```

### 3.5 Instalar MySQL / MariaDB

#### Opción A: MySQL 8.0
```powershell
# Descargar MySQL Installer desde https://dev.mysql.com/downloads/installer/
# Ejecutar MySQL Installer y seleccionar:
# - MySQL Server 8.0
# - MySQL Workbench (opcional)

# Configuración durante instalación:
# - Tipo: Development Computer o Server
# - Puerto: 3306
# - Root password: [CONTRASEÑA SEGURA]
# - Crear usuario: tienda_user
```

#### Opción B: MariaDB 10.11 (Alternativa más ligera)
```powershell
# Descargar desde https://mariadb.org/download/
# Ejecutar instalador MSI
# Configurar:
# - Puerto: 3306
# - Root password: [CONTRASEÑA SEGURA]
# - Habilitar acceso remoto: NO (por seguridad)
```

**Verificar instalación:**
```powershell
# Verificar servicio MySQL
Get-Service -Name "MySQL*"

# Probar conexión
mysql -u root -p
```

### 3.6 Instalar Visual C++ Redistributable
```powershell
# PHP requiere Visual C++ Redistributable 2015-2022
# Descargar desde: https://aka.ms/vs/17/release/vc_redist.x64.exe
$vcRedistUrl = "https://aka.ms/vs/17/release/vc_redist.x64.exe"
$vcInstallerPath = "$env:TEMP\vc_redist.x64.exe"
Invoke-WebRequest -Uri $vcRedistUrl -OutFile $vcInstallerPath
Start-Process $vcInstallerPath -ArgumentList "/install /quiet /norestart" -Wait
Remove-Item $vcInstallerPath
```

---

## <a name="configuracion-iis"></a>🌐 4. CONFIGURACIÓN DE IIS

### 4.1 Crear Sitio Web en IIS

```powershell
# Importar módulo de IIS
Import-Module WebAdministration

# Variables de configuración
$siteName = "TiendaEBS"
$physicalPath = "C:\inetpub\wwwroot\catalogo_ebs"
$ipAddress = "*"  # Todas las IPs, o específica ej: "192.168.1.100"
$port = 80
$appPoolName = "TiendaEBS_AppPool"

# Crear Application Pool
New-WebAppPool -Name $appPoolName
Set-ItemProperty IIS:\AppPools\$appPoolName -name "managedRuntimeVersion" -value ""
Set-ItemProperty IIS:\AppPools\$appPoolName -name "startMode" -value "AlwaysRunning"
Set-ItemProperty IIS:\AppPools\$appPoolName -name "processModel.idleTimeout" -value "00:00:00"

# Crear carpeta física
New-Item -Path $physicalPath -ItemType Directory -Force

# Crear sitio web
New-Website -Name $siteName -PhysicalPath $physicalPath -ApplicationPool $appPoolName -IPAddress $ipAddress -Port $port

# Iniciar sitio
Start-Website -Name $siteName
```

### 4.2 Configurar Permisos del Sitio
```powershell
# Dar permisos a IIS_IUSRS
$path = "C:\inetpub\wwwroot\catalogo_ebs"
icacls $path /grant "IIS_IUSRS:(OI)(CI)RX" /T
icacls "$path\public" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "$path\public\images" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "$path\public\invoices" /grant "IIS_IUSRS:(OI)(CI)F" /T

# Si usas usuario de servicio personalizado:
# icacls $path /grant "IIS_TiendaEBS:(OI)(CI)RX" /T
```

### 4.3 Configurar Document por Defecto
```powershell
# Establecer index.php como documento predeterminado
Clear-WebConfiguration -PSPath "IIS:\Sites\$siteName" -Filter "/system.webServer/defaultDocument/files"
Add-WebConfiguration -PSPath "IIS:\Sites\$siteName" -Filter "/system.webServer/defaultDocument/files" -Value @{value="index.php"}
Add-WebConfiguration -PSPath "IIS:\Sites\$siteName" -Filter "/system.webServer/defaultDocument/files" -Value @{value="index.html"}
```

---

## <a name="configuracion-base-datos"></a>🗄️ 5. CONFIGURACIÓN DE BASE DE DATOS

### 5.1 Crear Base de Datos y Usuario
```sql
-- Conectar como root
mysql -u root -p

-- Crear base de datos
CREATE DATABASE catalogo_tienda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario para la aplicación (CAMBIAR CONTRASEÑA)
CREATE USER 'tienda_user'@'localhost' IDENTIFIED BY 'P@ssw0rd_Segur0_2026!';

-- Otorgar permisos
GRANT ALL PRIVILEGES ON catalogo_tienda.* TO 'tienda_user'@'localhost';
FLUSH PRIVILEGES;

-- Verificar
SHOW GRANTS FOR 'tienda_user'@'localhost';
EXIT;
```

### 5.2 Importar Esquema de Base de Datos
```powershell
# Si tienes archivo SQL de respaldo
mysql -u tienda_user -p catalogo_tienda < C:\inetpub\wwwroot\catalogo_ebs\catalogo_tienda.sql

# La aplicación creará las tablas automáticamente en el primer acceso
```

### 5.3 Configurar Conexión en la Aplicación
**Editar `config/database.php`:**
```php
<?php
// Configuración de producción
define('DB_HOST', 'localhost');
define('DB_USER', 'tienda_user');
define('DB_PASS', 'P@ssw0rd_Segur0_2026!');  // Cambiar por tu contraseña
define('DB_NAME', 'catalogo_tienda');
define('DB_CHARSET', 'utf8mb4');
```

---

## <a name="despliegue"></a>📦 6. DESPLIEGUE DE LA APLICACIÓN

### 6.1 Copiar Archivos al Servidor

#### Opción A: Mediante RDP y Carpeta Compartida
```powershell
# En el servidor, habilitar carpeta compartida temporalmente
New-SmbShare -Name "Deployment" -Path "C:\inetpub\wwwroot" -FullAccess "Administrador"

# Desde máquina local, copiar archivos vía red
# \\192.168.1.100\Deployment\catalogo_ebs
```

#### Opción B: Mediante FTP (FileZilla Server)
1. Instalar FileZilla Server en Windows Server
2. Crear usuario FTP apuntando a `C:\inetpub\wwwroot`
3. Subir archivos mediante FileZilla Client

#### Opción C: Git (Recomendado para actualizaciones)
```powershell
# Instalar Git
winget install Git.Git

# Clonar repositorio (si tienes)
cd C:\inetpub\wwwroot
git clone https://tu-repositorio.git catalogo_ebs

# O copiar archivos directamente
Copy-Item "C:\xampp\htdocs\catalogo2\*" "C:\inetpub\wwwroot\catalogo_ebs\" -Recurse -Force
```

### 6.2 Verificar Estructura de Archivos
```
C:\inetpub\wwwroot\catalogo_ebs\
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
├── config/
│   ├── config.php
│   ├── database.php
│   └── whatsapp.php
├── public/
│   ├── css/
│   ├── js/
│   ├── images/
│   │   └── productos/
│   └── invoices/
├── index.php
├── web.config  ⭐ IMPORTANTE
└── catalogo_tienda.sql
```

### 6.3 Verificar web.config
El archivo `web.config` ya está incluido. Verificar que contenga:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="Main Rewrite" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="true" />
                    <conditions logicalGrouping="MatchAll">
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php?url={R:1}" appendQueryString="true" />
                </rule>
            </rules>
        </rewrite>
        <!-- Resto de configuración... -->
    </system.webServer>
</configuration>
```

### 6.4 Crear Carpetas con Permisos de Escritura
```powershell
$basePath = "C:\inetpub\wwwroot\catalogo_ebs"

# Crear carpetas si no existen
New-Item -Path "$basePath\public\images\productos" -ItemType Directory -Force
New-Item -Path "$basePath\public\invoices" -ItemType Directory -Force

# Dar permisos de escritura
icacls "$basePath\public\images" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "$basePath\public\invoices" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

---

## <a name="configuracion-ip-publica"></a>🌍 7. CONFIGURACIÓN DE IP PÚBLICA

### 7.1 Configurar Router para Port Forwarding

**En tu Router (por IP interna del router, ej: 192.168.1.1):**

1. Acceder al panel de administración del router
2. Buscar sección: **Port Forwarding / NAT / Virtual Server**
3. Crear regla:
   ```
   Servicio: HTTP_TiendaEBS
   Puerto Externo: 80
   IP Interna: 192.168.1.100 (IP del servidor)
   Puerto Interno: 80
   Protocolo: TCP
   Estado: Habilitado
   ```
4. Si usas HTTPS, agregar regla para puerto 443
5. Guardar y reiniciar router

### 7.2 Configurar Firewall de Windows
```powershell
# Permitir tráfico HTTP
New-NetFirewallRule -DisplayName "HTTP Tienda EBS" -Direction Inbound -Protocol TCP -LocalPort 80 -Action Allow -Profile Any

# Permitir tráfico HTTPS
New-NetFirewallRule -DisplayName "HTTPS Tienda EBS" -Direction Inbound -Protocol TCP -LocalPort 443 -Action Allow -Profile Any

# Verificar reglas
Get-NetFirewallRule | Where-Object {$_.DisplayName -like "*Tienda*"}
```

### 7.3 Obtener y Verificar IP Pública
```powershell
# Obtener IP pública actual
(Invoke-WebRequest -Uri "https://api.ipify.org").Content

# O mediante navegador:
Start-Process "https://www.whatismyip.com"
```

### 7.4 Configurar DNS (Opcional pero Recomendado)

#### Opción A: Dominio propio con proveedor DNS
1. Comprar dominio (ej: `tiendaebs.com`)
2. En panel de DNS del proveedor, crear registro A:
   ```
   Tipo: A
   Nombre: @  (o www)
   Valor: TU_IP_PUBLICA
   TTL: 3600
   ```

#### Opción B: Dynamic DNS (No-IP, DynDNS)
```powershell
# Registrar en https://www.noip.com (gratis)
# Crear hostname: tiendaebs.ddns.net
# Descargar e instalar cliente DUC (Dynamic Update Client)
# El cliente actualizará automáticamente tu IP pública
```

### 7.5 Probar Acceso Externo
```powershell
# Desde otra red (móvil 4G/5G), acceder a:
# http://TU_IP_PUBLICA
# o
# http://tiendaebs.ddns.net
```

---

## <a name="seguridad"></a>🔒 8. SEGURIDAD Y HARDENING

### 8.1 Configurar HTTPS con Certificado SSL

#### Opción A: Let's Encrypt (Gratuito)
```powershell
# Instalar win-acme
Invoke-WebRequest -Uri "https://github.com/win-acme/win-acme/releases/latest/download/win-acme.v2.x.x.zip" -OutFile "$env:TEMP\win-acme.zip"
Expand-Archive "$env:TEMP\win-acme.zip" -DestinationPath "C:\win-acme"

# Ejecutar y seguir asistente
cd C:\win-acme
.\wacs.exe

# Seleccionar:
# - Nuevo certificado
# - IIS
# - Seleccionar sitio "TiendaEBS"
# - Renovación automática cada 60 días
```

#### Opción B: Certificado de CA comercial
1. Comprar certificado SSL (GoDaddy, Namecheap, etc.)
2. Generar CSR en IIS
3. Enviar CSR a CA
4. Instalar certificado recibido en IIS

### 8.2 Forzar HTTPS (Redirección)
**Agregar a `web.config` (después de la instalación del certificado):**
```xml
<rewrite>
    <rules>
        <!-- Forzar HTTPS -->
        <rule name="Force HTTPS" stopProcessing="true">
            <match url="(.*)" />
            <conditions>
                <add input="{HTTPS}" pattern="^OFF$" />
            </conditions>
            <action type="Redirect" url="https://{HTTP_HOST}/{R:1}" redirectType="Permanent" />
        </rule>
        
        <!-- Resto de reglas... -->
    </rules>
</rewrite>
```

### 8.3 Cambiar Credenciales por Defecto
```sql
-- Conectar a MySQL
mysql -u root -p

USE catalogo_tienda;

-- Cambiar contraseña de admin (nueva contraseña hasheada con SHA256)
UPDATE administradores 
SET password = SHA2('Nueva_Contraseña_Segura_2026!', 256) 
WHERE usuario = 'admin';

-- Verificar
SELECT usuario, nombre FROM administradores;
```

### 8.4 Configurar Límites de Seguridad en web.config
```xml
<security>
    <requestFiltering>
        <!-- Limitar tamaño de archivos subidos a 10MB -->
        <requestLimits maxAllowedContentLength="10485760" maxQueryString="2048" />
        
        <!-- Bloquear extensiones peligrosas -->
        <fileExtensions>
            <add fileExtension=".exe" allowed="false" />
            <add fileExtension=".dll" allowed="false" />
            <add fileExtension=".bat" allowed="false" />
            <add fileExtension=".cmd" allowed="false" />
        </fileExtensions>
    </requestFiltering>
</security>
```

### 8.5 Ocultar Información del Servidor
```xml
<!-- En web.config -->
<httpProtocol>
    <customHeaders>
        <remove name="X-Powered-By" />
        <add name="X-Content-Type-Options" value="nosniff" />
        <add name="X-Frame-Options" value="SAMEORIGIN" />
        <add name="X-XSS-Protection" value="1; mode=block" />
        <add name="Referrer-Policy" value="strict-origin-when-cross-origin" />
    </customHeaders>
</httpProtocol>
```

### 8.6 Deshabilitar Errores Detallados en Producción
**En `config/config.php`:**
```php
// Producción
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', APP_ROOT . '/logs/php_errors.log');
```

**En `web.config`:**
```xml
<httpErrors errorMode="Custom" defaultResponseMode="File">
    <remove statusCode="404" />
    <error statusCode="404" path="C:\inetpub\wwwroot\catalogo_ebs\404.html" responseMode="File" />
    <remove statusCode="500" />
    <error statusCode="500" path="C:\inetpub\wwwroot\catalogo_ebs\500.html" responseMode="File" />
</httpErrors>
```

### 8.7 Configurar Backups Automáticos
```powershell
# Crear script de backup
$backupScript = @"
`$date = Get-Date -Format 'yyyyMMdd_HHmmss'
`$backupPath = "D:\Backups\TiendaEBS\`$date"
New-Item -Path `$backupPath -ItemType Directory -Force

# Backup de archivos
Copy-Item "C:\inetpub\wwwroot\catalogo_ebs\*" "`$backupPath\files\" -Recurse -Force

# Backup de base de datos
& "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe" -u tienda_user -pP@ssw0rd_Segur0_2026! catalogo_tienda > "`$backupPath\database.sql"

# Comprimir
Compress-Archive -Path "`$backupPath\*" -DestinationPath "`$backupPath.zip"
Remove-Item `$backupPath -Recurse -Force

# Eliminar backups antiguos (más de 30 días)
Get-ChildItem "D:\Backups\TiendaEBS" -Filter "*.zip" | Where-Object {`$_.LastWriteTime -lt (Get-Date).AddDays(-30)} | Remove-Item
"@

# Guardar script
$backupScript | Out-File "C:\Scripts\Backup-TiendaEBS.ps1" -Encoding UTF8

# Crear tarea programada (diaria a las 2:00 AM)
$action = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-File C:\Scripts\Backup-TiendaEBS.ps1"
$trigger = New-ScheduledTaskTrigger -Daily -At 2:00AM
$principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest
Register-ScheduledTask -TaskName "Backup Tienda EBS" -Action $action -Trigger $trigger -Principal $principal
```

---

## <a name="optimizacion"></a>⚡ 9. OPTIMIZACIÓN

### 9.1 Habilitar Caché en IIS
```xml
<!-- En web.config -->
<staticContent>
    <clientCache cacheControlMode="UseMaxAge" cacheControlMaxAge="7.00:00:00" />
</staticContent>
```

### 9.2 Habilitar Compresión
```powershell
# Habilitar compresión dinámica y estática
Enable-WindowsOptionalFeature -Online -FeatureName IIS-HttpCompressionDynamic
Enable-WindowsOptionalFeature -Online -FeatureName IIS-HttpCompressionStatic

# Configurar en IIS
Set-WebConfigurationProperty -PSPath "MACHINE/WEBROOT/APPHOST" -Filter "system.webServer/httpCompression" -Name "doDynamicCompression" -Value "True"
Set-WebConfigurationProperty -PSPath "MACHINE/WEBROOT/APPHOST" -Filter "system.webServer/httpCompression" -Name "doStaticCompression" -Value "True"
```

### 9.3 Optimizar MySQL
**Editar `C:\ProgramData\MySQL\MySQL Server 8.0\my.ini`:**
```ini
[mysqld]
# Optimizaciones para servidor con 8GB RAM
innodb_buffer_pool_size = 2G
innodb_log_file_size = 512M
max_connections = 200
query_cache_size = 128M
tmp_table_size = 128M
max_heap_table_size = 128M

# Reiniciar servicio
Restart-Service MySQL80
```

### 9.4 Configurar Application Pool para Mejor Rendimiento
```powershell
# Configurar App Pool
Set-ItemProperty "IIS:\AppPools\TiendaEBS_AppPool" -name "recycling.periodicRestart.time" -value "00:00:00"
Set-ItemProperty "IIS:\AppPools\TiendaEBS_AppPool" -name "processModel.idleTimeout" -value "00:00:00"
Set-ItemProperty "IIS:\AppPools\TiendaEBS_AppPool" -name "startMode" -value "AlwaysRunning"
Set-ItemProperty "IIS:\AppPools\TiendaEBS_AppPool" -name "recycling.periodicRestart.memory" -value 500000
```

---

## <a name="mantenimiento"></a>🛠️ 10. MANTENIMIENTO

### 10.1 Monitoreo del Sistema
```powershell
# Script de monitoreo
$monitorScript = @"
# Verificar estado de servicios
Get-Service -Name "W3SVC","MySQL80" | Select-Object Name, Status

# Verificar uso de recursos
Get-Counter '\Processor(_Total)\% Processor Time'
Get-Counter '\Memory\Available MBytes'

# Verificar espacio en disco
Get-PSDrive C | Select-Object Used,Free

# Verificar sitio web
try {
    `$response = Invoke-WebRequest -Uri "http://localhost" -UseBasicParsing -TimeoutSec 10
    Write-Host "Sitio OK - Código: `$(`$response.StatusCode)"
} catch {
    Write-Host "ERROR - Sitio no responde: `$(`$_.Exception.Message)" -ForegroundColor Red
}
"@

$monitorScript | Out-File "C:\Scripts\Monitor-TiendaEBS.ps1"

# Ejecutar cada hora
$action = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-File C:\Scripts\Monitor-TiendaEBS.ps1"
$trigger = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Hours 1) -RepetitionDuration ([TimeSpan]::MaxValue)
Register-ScheduledTask -TaskName "Monitor Tienda EBS" -Action $action -Trigger $trigger
```

### 10.2 Limpieza de Logs Antiguos
```powershell
# Script de limpieza
$cleanupScript = @"
# Limpiar logs de IIS mayores a 30 días
Get-ChildItem "C:\inetpub\logs\LogFiles" -Recurse -Filter "*.log" | 
    Where-Object {`$_.LastWriteTime -lt (Get-Date).AddDays(-30)} | 
    Remove-Item -Force

# Limpiar logs de PHP mayores a 30 días
Get-ChildItem "C:\PHP\logs" -Filter "*.log" | 
    Where-Object {`$_.LastWriteTime -lt (Get-Date).AddDays(-30)} | 
    Remove-Item -Force

# Limpiar cuentas de cobro antiguas (mayores a 90 días)
Get-ChildItem "C:\inetpub\wwwroot\catalogo_ebs\public\invoices" -Filter "*.html" | 
    Where-Object {`$_.LastWriteTime -lt (Get-Date).AddDays(-90)} | 
    Remove-Item -Force

Write-Host "Limpieza completada"
"@

$cleanupScript | Out-File "C:\Scripts\Cleanup-TiendaEBS.ps1"

# Ejecutar semanalmente
$action = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-File C:\Scripts\Cleanup-TiendaEBS.ps1"
$trigger = New-ScheduledTaskTrigger -Weekly -DaysOfWeek Sunday -At 3:00AM
Register-ScheduledTask -TaskName "Cleanup Tienda EBS" -Action $action -Trigger $trigger
```

### 10.3 Actualización de la Aplicación
```powershell
# Script de actualización segura
$updateScript = @"
# 1. Crear backup
& "C:\Scripts\Backup-TiendaEBS.ps1"

# 2. Detener sitio
Stop-Website -Name "TiendaEBS"

# 3. Copiar nuevos archivos (desde carpeta de staging)
Copy-Item "C:\Staging\catalogo_ebs\*" "C:\inetpub\wwwroot\catalogo_ebs\" -Recurse -Force -Exclude "config","public\images"

# 4. Ejecutar migraciones de BD si hay
# mysql -u tienda_user -p catalogo_tienda < C:\Staging\migrations.sql

# 5. Limpiar caché de PHP (reiniciar app pool)
Restart-WebAppPool -Name "TiendaEBS_AppPool"

# 6. Iniciar sitio
Start-Website -Name "TiendaEBS"

Write-Host "Actualización completada"
"@

$updateScript | Out-File "C:\Scripts\Update-TiendaEBS.ps1"
```

---

## <a name="troubleshooting"></a>🔧 11. SOLUCIÓN DE PROBLEMAS

### Error: HTTP 500 - Error Interno del Servidor

**Causa:** Error en PHP o configuración.

**Solución:**
```powershell
# 1. Habilitar errores detallados temporalmente
# En web.config:
<httpErrors errorMode="Detailed" />

# 2. Revisar logs
Get-Content "C:\PHP\logs\php_errors.log" -Tail 50
Get-Content "C:\inetpub\logs\LogFiles\W3SVC1\*.log" -Tail 20

# 3. Verificar permisos
icacls "C:\inetpub\wwwroot\catalogo_ebs" | Select-String "IIS_IUSRS"
```

### Error: Página en Blanco / No se Muestra Nada

**Causa:** Error de PHP sin logging.

**Solución:**
```powershell
# Verificar que PHP funcione
cd C:\inetpub\wwwroot\catalogo_ebs
php -f index.php

# Revisar php.ini
notepad C:\PHP\php.ini
# Verificar: display_errors = On (temporalmente)
```

### Error: No se Conecta a la Base de Datos

**Solución:**
```powershell
# 1. Verificar servicio MySQL
Get-Service MySQL80
Start-Service MySQL80

# 2. Probar conexión manualmente
mysql -u tienda_user -p -h localhost

# 3. Verificar credenciales en config/database.php
notepad C:\inetpub\wwwroot\catalogo_ebs\config\database.php
```

### Error: 404 en Rutas Admin

**Causa:** URL Rewrite no instalado o web.config incorrecto.

**Solución:**
```powershell
# Verificar URL Rewrite instalado
Get-WindowsFeature -Name "*Rewrite*"

# Verificar web.config existe
Test-Path "C:\inetpub\wwwroot\catalogo_ebs\web.config"

# Verificar reglas en IIS
Get-WebConfiguration -PSPath "IIS:\Sites\TiendaEBS" -Filter "system.webServer/rewrite/rules"
```

### Error: No se Suben Imágenes

**Solución:**
```powershell
# Verificar permisos de escritura
icacls "C:\inetpub\wwwroot\catalogo_ebs\public\images\productos" /grant "IIS_IUSRS:(OI)(CI)F" /T

# Verificar límites de tamaño en php.ini
php -i | Select-String "upload_max_filesize"
php -i | Select-String "post_max_size"
```

### Sitio No Accesible desde Internet

**Checklist:**
```powershell
# 1. Verificar IP pública
(Invoke-WebRequest -Uri "https://api.ipify.org").Content

# 2. Verificar firewall
Get-NetFirewallRule | Where-Object {$_.LocalPort -eq 80}

# 3. Verificar port forwarding en router
# Acceder a http://192.168.1.1 (o IP de tu router)

# 4. Probar localmente primero
Start-Process "http://localhost"

# 5. Probar desde red interna con IP local
Start-Process "http://192.168.1.100"

# 6. Usar herramienta externa de verificación
Start-Process "https://www.yougetsignal.com/tools/open-ports/"
```

---

## ✅ CHECKLIST FINAL DE IMPLEMENTACIÓN

### Pre-Despliegue
- [ ] Windows Server 2019 actualizado
- [ ] IP estática configurada
- [ ] IIS instalado y funcionando
- [ ] PHP 8.1+ instalado y registrado en IIS
- [ ] MySQL/MariaDB instalado y configurado
- [ ] URL Rewrite Module instalado
- [ ] Visual C++ Redistributable instalado

### Configuración
- [ ] Sitio web creado en IIS
- [ ] Application Pool configurado
- [ ] Permisos de carpeta asignados
- [ ] Base de datos creada
- [ ] Usuario de BD creado con permisos
- [ ] Archivos copiados al servidor
- [ ] web.config presente y correcto
- [ ] config/database.php configurado

### Red y Seguridad
- [ ] Firewall de Windows configurado
- [ ] Port forwarding en router configurado
- [ ] IP pública identificada
- [ ] DNS configurado (opcional)
- [ ] Certificado SSL instalado (recomendado)
- [ ] HTTPS forzado (si SSL instalado)
- [ ] Contraseñas por defecto cambiadas
- [ ] Errores detallados deshabilitados

### Optimización y Mantenimiento
- [ ] Compresión habilitada
- [ ] Caché configurado
- [ ] MySQL optimizado
- [ ] Script de backup configurado
- [ ] Script de monitoreo configurado
- [ ] Script de limpieza configurado
- [ ] Logs rotando correctamente

### Verificación Final
- [ ] Sitio accesible desde `http://localhost`
- [ ] Sitio accesible desde IP local (192.168.x.x)
- [ ] Sitio accesible desde IP pública
- [ ] Login admin funciona
- [ ] Catálogo de productos muestra correctamente
- [ ] Carrito funciona
- [ ] Checkout y WhatsApp funcionan
- [ ] Panel admin accesible y operativo
- [ ] Imágenes se suben correctamente
    - [ ] Cuentas de Cobro se generan correctamente

---

## 📞 SOPORTE Y CONTACTO

**Distribuciones EBS**  
WhatsApp: +57 311 2969569  
Email: soporte@distribucionesebs.com  

**Documentación Adicional:**
- `INSTALACION.md` - Instalación básica en XAMPP
- `INSTALACION_IIS.md` - Guía básica de IIS
- `INICIO_RAPIDO.md` - Guía de uso rápido
- `README.md` - Información general del proyecto

---

**Versión del documento:** 1.0  
**Última actualización:** Enero 2026  
**Autor:** GitHub Copilot con Claude Sonnet 4.5

---

✅ **¡Tu aplicación está lista para producción en Windows Server 2019!**
