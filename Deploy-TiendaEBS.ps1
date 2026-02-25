# Script de Instalación Automatizada
# Distribuciones EBS - Catálogo Digital
# Para Windows Server 2019 con IIS

<#
.SYNOPSIS
    Instala y configura automáticamente el sitio Tienda EBS en IIS

.DESCRIPTION
    Este script automatiza la instalación completa de la aplicación,
    incluyendo IIS, PHP, MySQL, y todas las configuraciones necesarias.

.PARAMETER SiteName
    Nombre del sitio en IIS (default: TiendaEBS)

.PARAMETER PhysicalPath
    Ruta física donde se instalará (default: C:\inetpub\wwwroot\catalogo_ebs)

.PARAMETER Port
    Puerto HTTP (default: 80)

.PARAMETER DBPassword
    Contraseña para el usuario de base de datos

.EXAMPLE
    .\Deploy-TiendaEBS.ps1 -DBPassword "MiContraseñaSegura2026!"

.NOTES
    Requiere ejecutar como Administrador
    Versión: 1.0
    Fecha: Enero 2026
#>

[CmdletBinding()]
param(
    [Parameter(Mandatory=$false)]
    [string]$SiteName = "TiendaEBS",
    
    [Parameter(Mandatory=$false)]
    [string]$PhysicalPath = "C:\inetpub\wwwroot\catalogo_ebs",
    
    [Parameter(Mandatory=$false)]
    [int]$Port = 80,
    
    [Parameter(Mandatory=$true)]
    [string]$DBPassword,
    
    [Parameter(Mandatory=$false)]
    [string]$SourcePath = "C:\xampp\htdocs\catalogo2"
)

# Verificar privilegios de administrador
if (-NOT ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Error "Este script requiere privilegios de administrador. Ejecute PowerShell como Administrador."
    Exit 1
}

# Configuración
$AppPoolName = "${SiteName}_AppPool"
$DBUser = "tienda_user"
$DBName = "catalogo_tienda"
$ErrorActionPreference = "Continue"

Write-Host @"

╔═══════════════════════════════════════════════════════════════╗
║                                                               ║
║     INSTALADOR AUTOMATIZADO - DISTRIBUCIONES EBS             ║
║     Catálogo Digital para Windows Server 2019 + IIS          ║
║                                                               ║
║     Versión: 1.0                                              ║
║     Fecha: Enero 2026                                         ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝

"@ -ForegroundColor Cyan

Write-Host "`n[CONFIGURACIÓN]" -ForegroundColor Yellow
Write-Host "  Sitio: $SiteName"
Write-Host "  Ruta: $PhysicalPath"
Write-Host "  Puerto: $Port"
Write-Host "  Base de datos: $DBName"
Write-Host "  Usuario BD: $DBUser"
Write-Host ""

# Crear log
$LogFile = "C:\Scripts\Deploy-TiendaEBS-$(Get-Date -Format 'yyyyMMdd-HHmmss').log"
New-Item -Path "C:\Scripts" -ItemType Directory -Force | Out-Null

function Write-Log {
    param([string]$Message)
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    "$timestamp - $Message" | Out-File $LogFile -Append
    Write-Host $Message
}

function Test-CommandExists {
    param([string]$Command)
    $oldPreference = $ErrorActionPreference
    $ErrorActionPreference = 'stop'
    try {
        if (Get-Command $Command) { return $true }
    } catch {
        return $false
    } finally {
        $ErrorActionPreference = $oldPreference
    }
}

# ============================================================
# PASO 1: VERIFICAR REQUISITOS PREVIOS
# ============================================================
Write-Host "`n[1/10] Verificando requisitos previos..." -ForegroundColor Green
Write-Log "[1/10] Verificando requisitos previos..."

# Verificar Windows Server
$OS = Get-WmiObject -Class Win32_OperatingSystem
Write-Log "Sistema operativo: $($OS.Caption)"

if ($OS.Caption -notlike "*Server*") {
    Write-Warning "Este script está diseñado para Windows Server. Continuar de todos modos..."
}

# ============================================================
# PASO 2: INSTALAR IIS
# ============================================================
Write-Host "`n[2/10] Instalando IIS y componentes..." -ForegroundColor Green
Write-Log "[2/10] Instalando IIS..."

$IISFeatures = @(
    "Web-Server",
    "Web-WebServer",
    "Web-Common-Http",
    "Web-Default-Doc",
    "Web-Dir-Browsing",
    "Web-Http-Errors",
    "Web-Static-Content",
    "Web-Health",
    "Web-Http-Logging",
    "Web-Performance",
    "Web-Stat-Compression",
    "Web-Dyn-Compression",
    "Web-Security",
    "Web-Filtering",
    "Web-CGI",
    "Web-ISAPI-Ext",
    "Web-ISAPI-Filter",
    "Web-Mgmt-Tools",
    "Web-Mgmt-Console"
)

foreach ($feature in $IISFeatures) {
    Write-Host "  Instalando $feature..." -NoNewline
    try {
        Install-WindowsFeature -Name $feature -ErrorAction SilentlyContinue | Out-Null
        Write-Host " OK" -ForegroundColor Green
        Write-Log "Instalado: $feature"
    } catch {
        Write-Host " ADVERTENCIA" -ForegroundColor Yellow
        Write-Log "Advertencia al instalar $feature : $_"
    }
}

# ============================================================
# PASO 3: INSTALAR URL REWRITE MODULE
# ============================================================
Write-Host "`n[3/10] Instalando URL Rewrite Module..." -ForegroundColor Green
Write-Log "[3/10] Instalando URL Rewrite Module..."

$urlRewriteUrl = "https://download.microsoft.com/download/1/2/8/128E2E22-C1B9-44A4-BE2A-5859ED1D4592/rewrite_amd64_en-US.msi"
$installerPath = "$env:TEMP\urlrewrite2.msi"

try {
    Write-Host "  Descargando URL Rewrite..." -NoNewline
    Invoke-WebRequest -Uri $urlRewriteUrl -OutFile $installerPath -UseBasicParsing
    Write-Host " OK" -ForegroundColor Green
    
    Write-Host "  Instalando URL Rewrite..." -NoNewline
    Start-Process msiexec.exe -ArgumentList "/i $installerPath /quiet /norestart" -Wait -NoNewWindow
    Write-Host " OK" -ForegroundColor Green
    Write-Log "URL Rewrite Module instalado"
    
    Remove-Item $installerPath -Force
} catch {
    Write-Host " ERROR" -ForegroundColor Red
    Write-Log "Error al instalar URL Rewrite: $_"
}

# ============================================================
# PASO 4: VERIFICAR/INSTALAR PHP
# ============================================================
Write-Host "`n[4/10] Verificando PHP..." -ForegroundColor Green
Write-Log "[4/10] Verificando PHP..."

if (Test-CommandExists "php") {
    $phpVersion = php -v
    Write-Host "  PHP encontrado: $($phpVersion[0])" -ForegroundColor Green
    Write-Log "PHP ya instalado: $($phpVersion[0])"
} else {
    Write-Host "  PHP no encontrado. Por favor, instale PHP manualmente." -ForegroundColor Yellow
    Write-Host "  Descargue desde: https://windows.php.net/download/" -ForegroundColor Yellow
    Write-Host "  Guía: Ver DEPLOYMENT_WINDOWS_SERVER_2019.md sección 3.2" -ForegroundColor Yellow
    Write-Log "ADVERTENCIA: PHP no instalado"
}

# Verificar extensiones PHP
Write-Host "`n  Verificando extensiones PHP..." -ForegroundColor Cyan
$requiredExtensions = @("mysqli", "mbstring", "openssl", "curl", "fileinfo")
foreach ($ext in $requiredExtensions) {
    if (php -m | Select-String $ext) {
        Write-Host "    $ext - OK" -ForegroundColor Green
    } else {
        Write-Host "    $ext - NO ENCONTRADA" -ForegroundColor Yellow
    }
}

# ============================================================
# PASO 5: VERIFICAR MYSQL
# ============================================================
Write-Host "`n[5/10] Verificando MySQL..." -ForegroundColor Green
Write-Log "[5/10] Verificando MySQL..."

$mysqlService = Get-Service -Name "MySQL*" -ErrorAction SilentlyContinue
if ($mysqlService) {
    Write-Host "  MySQL encontrado: $($mysqlService.Name) - Estado: $($mysqlService.Status)" -ForegroundColor Green
    Write-Log "MySQL encontrado: $($mysqlService.Name)"
    
    if ($mysqlService.Status -ne "Running") {
        Write-Host "  Iniciando servicio MySQL..." -NoNewline
        Start-Service $mysqlService.Name
        Write-Host " OK" -ForegroundColor Green
    }
} else {
    Write-Host "  MySQL no encontrado. Por favor, instale MySQL o MariaDB." -ForegroundColor Yellow
    Write-Host "  Descargue desde: https://dev.mysql.com/downloads/installer/" -ForegroundColor Yellow
    Write-Log "ADVERTENCIA: MySQL no instalado"
}

# ============================================================
# PASO 6: COPIAR ARCHIVOS DE LA APLICACIÓN
# ============================================================
Write-Host "`n[6/10] Copiando archivos de la aplicación..." -ForegroundColor Green
Write-Log "[6/10] Copiando archivos..."

if (-not (Test-Path $SourcePath)) {
    Write-Error "Ruta de origen no encontrada: $SourcePath"
    Write-Log "ERROR: Ruta de origen no encontrada: $SourcePath"
    Exit 1
}

Write-Host "  Origen: $SourcePath"
Write-Host "  Destino: $PhysicalPath"

try {
    # Crear directorio de destino
    New-Item -Path $PhysicalPath -ItemType Directory -Force | Out-Null
    
    # Copiar archivos
    Write-Host "  Copiando archivos..." -NoNewline
    Copy-Item -Path "$SourcePath\*" -Destination $PhysicalPath -Recurse -Force
    Write-Host " OK" -ForegroundColor Green
    Write-Log "Archivos copiados de $SourcePath a $PhysicalPath"
    
    # Crear carpetas adicionales
    $folders = @(
        "$PhysicalPath\public\images\productos",
        "$PhysicalPath\public\invoices",
        "$PhysicalPath\logs"
    )
    
    foreach ($folder in $folders) {
        New-Item -Path $folder -ItemType Directory -Force | Out-Null
        Write-Log "Carpeta creada: $folder"
    }
} catch {
    Write-Host " ERROR" -ForegroundColor Red
    Write-Log "ERROR al copiar archivos: $_"
    Exit 1
}

# ============================================================
# PASO 7: CONFIGURAR IIS
# ============================================================
Write-Host "`n[7/10] Configurando IIS..." -ForegroundColor Green
Write-Log "[7/10] Configurando IIS..."

Import-Module WebAdministration

# Crear Application Pool
Write-Host "  Creando Application Pool..." -NoNewline
try {
    if (Test-Path "IIS:\AppPools\$AppPoolName") {
        Remove-WebAppPool -Name $AppPoolName
    }
    
    New-WebAppPool -Name $AppPoolName | Out-Null
    Set-ItemProperty "IIS:\AppPools\$AppPoolName" -name "managedRuntimeVersion" -value ""
    Set-ItemProperty "IIS:\AppPools\$AppPoolName" -name "startMode" -value "AlwaysRunning"
    Set-ItemProperty "IIS:\AppPools\$AppPoolName" -name "processModel.idleTimeout" -value "00:00:00"
    
    Write-Host " OK" -ForegroundColor Green
    Write-Log "Application Pool creado: $AppPoolName"
} catch {
    Write-Host " ERROR" -ForegroundColor Red
    Write-Log "ERROR al crear Application Pool: $_"
}

# Crear sitio web
Write-Host "  Creando sitio web..." -NoNewline
try {
    if (Get-Website -Name $SiteName -ErrorAction SilentlyContinue) {
        Remove-Website -Name $SiteName
    }
    
    New-Website -Name $SiteName `
                -PhysicalPath $PhysicalPath `
                -ApplicationPool $AppPoolName `
                -Port $Port | Out-Null
    
    Write-Host " OK" -ForegroundColor Green
    Write-Log "Sitio web creado: $SiteName en puerto $Port"
} catch {
    Write-Host " ERROR" -ForegroundColor Red
    Write-Log "ERROR al crear sitio web: $_"
}

# Configurar documento predeterminado
Write-Host "  Configurando documento predeterminado..." -NoNewline
try {
    Clear-WebConfiguration -PSPath "IIS:\Sites\$SiteName" -Filter "/system.webServer/defaultDocument/files"
    Add-WebConfiguration -PSPath "IIS:\Sites\$SiteName" -Filter "/system.webServer/defaultDocument/files" -Value @{value="index.php"}
    Add-WebConfiguration -PSPath "IIS:\Sites\$SiteName" -Filter "/system.webServer/defaultDocument/files" -Value @{value="index.html"}
    
    Write-Host " OK" -ForegroundColor Green
    Write-Log "Documento predeterminado configurado"
} catch {
    Write-Host " ADVERTENCIA" -ForegroundColor Yellow
    Write-Log "Advertencia al configurar documento predeterminado: $_"
}

# ============================================================
# PASO 8: CONFIGURAR PERMISOS
# ============================================================
Write-Host "`n[8/10] Configurando permisos de archivos..." -ForegroundColor Green
Write-Log "[8/10] Configurando permisos..."

try {
    Write-Host "  Permisos base..." -NoNewline
    icacls $PhysicalPath /grant "IIS_IUSRS:(OI)(CI)RX" /T /Q | Out-Null
    Write-Host " OK" -ForegroundColor Green
    
    Write-Host "  Permisos de escritura (public)..." -NoNewline
    icacls "$PhysicalPath\public" /grant "IIS_IUSRS:(OI)(CI)F" /T /Q | Out-Null
    Write-Host " OK" -ForegroundColor Green
    
    Write-Host "  Permisos de escritura (images)..." -NoNewline
    icacls "$PhysicalPath\public\images" /grant "IIS_IUSRS:(OI)(CI)F" /T /Q | Out-Null
    Write-Host " OK" -ForegroundColor Green
    
    Write-Host "  Permisos de escritura (invoices)..." -NoNewline
    icacls "$PhysicalPath\public\invoices" /grant "IIS_IUSRS:(OI)(CI)F" /T /Q | Out-Null
    Write-Host " OK" -ForegroundColor Green
    
    Write-Host "  Permisos de escritura (logs)..." -NoNewline
    icacls "$PhysicalPath\logs" /grant "IIS_IUSRS:(OI)(CI)F" /T /Q | Out-Null
    Write-Host " OK" -ForegroundColor Green
    
    Write-Log "Permisos de archivo configurados"
} catch {
    Write-Host " ERROR" -ForegroundColor Red
    Write-Log "ERROR al configurar permisos: $_"
}

# ============================================================
# PASO 9: CONFIGURAR BASE DE DATOS
# ============================================================
Write-Host "`n[9/10] Configurando base de datos..." -ForegroundColor Green
Write-Log "[9/10] Configurando base de datos..."

if (Test-CommandExists "mysql") {
    $tempSqlFile = "$env:TEMP\create_db.sql"
    
    $sqlScript = @"
CREATE DATABASE IF NOT EXISTS $DBName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DBUser'@'localhost' IDENTIFIED BY '$DBPassword';
GRANT ALL PRIVILEGES ON ${DBName}.* TO '$DBUser'@'localhost';
FLUSH PRIVILEGES;
"@
    
    $sqlScript | Out-File $tempSqlFile -Encoding UTF8
    
    Write-Host "  Creando base de datos y usuario..." -NoNewline
    try {
        # Intentar conectar (requerirá contraseña root)
        Write-Host ""
        Write-Host "  Se requiere la contraseña de root de MySQL:" -ForegroundColor Yellow
        mysql -u root -p < $tempSqlFile
        
        Write-Host "  Base de datos configurada OK" -ForegroundColor Green
        Write-Log "Base de datos $DBName creada, usuario $DBUser configurado"
        
        # Actualizar config/database.php
        Write-Host "  Actualizando configuración..." -NoNewline
        $dbConfigPath = "$PhysicalPath\config\database.php"
        $dbConfig = Get-Content $dbConfigPath -Raw
        $dbConfig = $dbConfig -replace "define\('DB_USER', '.*?'\);", "define('DB_USER', '$DBUser');"
        $dbConfig = $dbConfig -replace "define\('DB_PASS', '.*?'\);", "define('DB_PASS', '$DBPassword');"
        $dbConfig | Set-Content $dbConfigPath -Encoding UTF8
        Write-Host " OK" -ForegroundColor Green
        Write-Log "Configuración de BD actualizada en config/database.php"
        
    } catch {
        Write-Host " ERROR" -ForegroundColor Red
        Write-Log "ERROR al configurar base de datos: $_"
        Write-Host "  Configure manualmente la base de datos." -ForegroundColor Yellow
    }
    
    Remove-Item $tempSqlFile -Force -ErrorAction SilentlyContinue
} else {
    Write-Host "  MySQL CLI no encontrado. Configure la BD manualmente." -ForegroundColor Yellow
    Write-Log "ADVERTENCIA: No se pudo configurar BD automáticamente"
}

# ============================================================
# PASO 10: CONFIGURAR FIREWALL
# ============================================================
Write-Host "`n[10/10] Configurando firewall..." -ForegroundColor Green
Write-Log "[10/10] Configurando firewall..."

try {
    Write-Host "  Abriendo puerto $Port..." -NoNewline
    
    # Eliminar regla existente si existe
    Remove-NetFirewallRule -DisplayName "HTTP Tienda EBS" -ErrorAction SilentlyContinue | Out-Null
    
    # Crear nueva regla
    New-NetFirewallRule -DisplayName "HTTP Tienda EBS" `
                        -Direction Inbound `
                        -Protocol TCP `
                        -LocalPort $Port `
                        -Action Allow `
                        -Profile Any | Out-Null
    
    Write-Host " OK" -ForegroundColor Green
    Write-Log "Firewall configurado: Puerto $Port abierto"
} catch {
    Write-Host " ERROR" -ForegroundColor Red
    Write-Log "ERROR al configurar firewall: $_"
}

# ============================================================
# PASO 11: INICIAR SITIO
# ============================================================
Write-Host "`nIniciando sitio web..." -ForegroundColor Green
Write-Log "Iniciando sitio web..."

try {
    Start-Website -Name $SiteName
    Write-Host "Sitio iniciado correctamente" -ForegroundColor Green
    Write-Log "Sitio $SiteName iniciado"
} catch {
    Write-Host "Error al iniciar sitio: $_" -ForegroundColor Red
    Write-Log "ERROR al iniciar sitio: $_"
}

# ============================================================
# RESUMEN FINAL
# ============================================================
Write-Host @"

╔═══════════════════════════════════════════════════════════════╗
║                                                               ║
║     INSTALACIÓN COMPLETADA                                    ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝

"@ -ForegroundColor Green

$ipAddress = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object {$_.IPAddress -ne "127.0.0.1"} | Select-Object -First 1).IPAddress

Write-Host "✅ INFORMACIÓN DEL SITIO:" -ForegroundColor Cyan
Write-Host "   Nombre: $SiteName"
Write-Host "   Ruta: $PhysicalPath"
Write-Host "   Puerto: $Port"
Write-Host ""
Write-Host "🌐 ACCESO:" -ForegroundColor Cyan
Write-Host "   Local: http://localhost:$Port"
Write-Host "   Red local: http://${ipAddress}:$Port"
Write-Host ""
Write-Host "🔐 CREDENCIALES POR DEFECTO:" -ForegroundColor Cyan
Write-Host "   Admin: usuario = admin, contraseña = admin123"
Write-Host "   ⚠️  CAMBIAR ANTES DE USAR EN PRODUCCIÓN"
Write-Host ""
Write-Host "🗄️ BASE DE DATOS:" -ForegroundColor Cyan
Write-Host "   Base de datos: $DBName"
Write-Host "   Usuario: $DBUser"
Write-Host "   Contraseña: (la que especificaste)"
Write-Host ""
Write-Host "📋 PRÓXIMOS PASOS:" -ForegroundColor Yellow
Write-Host "   1. Verificar acceso al sitio"
Write-Host "   2. Cambiar contraseña de admin"
Write-Host "   3. Configurar port forwarding en el router (para IP pública)"
Write-Host "   4. Instalar certificado SSL (opcional)"
Write-Host "   5. Configurar backups automáticos"
Write-Host ""
Write-Host "📄 Log de instalación guardado en:" -ForegroundColor Cyan
Write-Host "   $LogFile"
Write-Host ""
Write-Host "📚 Documentación completa en:" -ForegroundColor Cyan
Write-Host "   $PhysicalPath\DEPLOYMENT_WINDOWS_SERVER_2019.md"
Write-Host "   $PhysicalPath\REVISION_CODIGO_PRODUCCION.md"
Write-Host ""

Write-Log "============================================"
Write-Log "INSTALACIÓN COMPLETADA EXITOSAMENTE"
Write-Log "Sitio: $SiteName"
Write-Log "URL Local: http://localhost:$Port"
Write-Log "URL Red: http://${ipAddress}:$Port"
Write-Log "============================================"

# Abrir navegador
Write-Host "¿Desea abrir el sitio en el navegador? (S/N): " -NoNewline -ForegroundColor Cyan
$response = Read-Host
if ($response -eq "S" -or $response -eq "s") {
    Start-Process "http://localhost:$Port"
}

Write-Host "`n✅ ¡Instalación completada! Revise el log para más detalles." -ForegroundColor Green
Write-Host ""
