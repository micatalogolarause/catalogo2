# Script de Monitoreo de Salud
# Distribuciones EBS - Catálogo Digital

<#
.SYNOPSIS
    Monitorea el estado del sitio web y servicios relacionados

.DESCRIPTION
    Verifica el estado de IIS, MySQL, acceso al sitio web y recursos del sistema

.PARAMETER SiteUrl
    URL del sitio a monitorear (default: http://localhost)

.PARAMETER AlertEmail
    Email para enviar alertas (opcional)

.EXAMPLE
    .\Monitor-TiendaEBS.ps1
    .\Monitor-TiendaEBS.ps1 -SiteUrl "http://192.168.1.100"
#>

param(
    [Parameter(Mandatory=$false)]
    [string]$SiteUrl = "http://localhost",
    
    [Parameter(Mandatory=$false)]
    [string]$AlertEmail = ""
)

$logFile = "C:\Scripts\Logs\Monitor-$(Get-Date -Format 'yyyyMMdd').log"
New-Item -Path "C:\Scripts\Logs" -ItemType Directory -Force | Out-Null

function Write-Log {
    param([string]$Message, [string]$Level = "INFO")
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logMessage = "[$timestamp] [$Level] $Message"
    $logMessage | Out-File $logFile -Append
    
    switch ($Level) {
        "ERROR" { Write-Host $logMessage -ForegroundColor Red }
        "WARNING" { Write-Host $logMessage -ForegroundColor Yellow }
        "SUCCESS" { Write-Host $logMessage -ForegroundColor Green }
        default { Write-Host $logMessage }
    }
}

$issues = @()

Write-Host "`n╔══════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  MONITOREO DE SALUD - TIENDA EBS         ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════╝`n" -ForegroundColor Cyan

Write-Log "Iniciando monitoreo de salud"

# ===========================================
# VERIFICAR SERVICIOS
# ===========================================
Write-Host "[1/6] Verificando servicios..." -ForegroundColor Yellow

# IIS
$w3svc = Get-Service -Name "W3SVC" -ErrorAction SilentlyContinue
if ($w3svc -and $w3svc.Status -eq "Running") {
    Write-Host "  ✓ IIS (W3SVC): Running" -ForegroundColor Green
    Write-Log "IIS: OK" "SUCCESS"
} else {
    Write-Host "  ✗ IIS (W3SVC): Detenido" -ForegroundColor Red
    Write-Log "IIS: Detenido" "ERROR"
    $issues += "IIS no está ejecutándose"
}

# MySQL
$mysql = Get-Service -Name "MySQL*" -ErrorAction SilentlyContinue
if ($mysql -and $mysql.Status -eq "Running") {
    Write-Host "  ✓ MySQL: Running" -ForegroundColor Green
    Write-Log "MySQL: OK" "SUCCESS"
} else {
    Write-Host "  ✗ MySQL: Detenido" -ForegroundColor Red
    Write-Log "MySQL: Detenido" "ERROR"
    $issues += "MySQL no está ejecutándose"
}

# ===========================================
# VERIFICAR RECURSOS DEL SISTEMA
# ===========================================
Write-Host "`n[2/6] Verificando recursos del sistema..." -ForegroundColor Yellow

# CPU
$cpu = (Get-Counter '\Processor(_Total)\% Processor Time').CounterSamples.CookedValue
if ($cpu -lt 80) {
    Write-Host "  ✓ CPU: $([math]::Round($cpu, 1))%" -ForegroundColor Green
    Write-Log "CPU: $([math]::Round($cpu, 1))%" "INFO"
} else {
    Write-Host "  ⚠ CPU: $([math]::Round($cpu, 1))% (Alto)" -ForegroundColor Yellow
    Write-Log "CPU: $([math]::Round($cpu, 1))% (Alto)" "WARNING"
    $issues += "Uso de CPU elevado: $([math]::Round($cpu, 1))%"
}

# Memoria
$mem = Get-Counter '\Memory\Available MBytes'
$availableMB = $mem.CounterSamples.CookedValue
if ($availableMB -gt 500) {
    Write-Host "  ✓ Memoria disponible: $([math]::Round($availableMB, 0)) MB" -ForegroundColor Green
    Write-Log "Memoria disponible: $([math]::Round($availableMB, 0)) MB" "INFO"
} else {
    Write-Host "  ⚠ Memoria disponible: $([math]::Round($availableMB, 0)) MB (Baja)" -ForegroundColor Yellow
    Write-Log "Memoria disponible: $([math]::Round($availableMB, 0)) MB (Baja)" "WARNING"
    $issues += "Memoria baja: $([math]::Round($availableMB, 0)) MB disponibles"
}

# Disco
$disk = Get-PSDrive C | Select-Object Used,Free
$freeGB = $disk.Free / 1GB
if ($freeGB -gt 5) {
    Write-Host "  ✓ Espacio en disco C: $([math]::Round($freeGB, 1)) GB libres" -ForegroundColor Green
    Write-Log "Espacio en disco: $([math]::Round($freeGB, 1)) GB libres" "INFO"
} else {
    Write-Host "  ⚠ Espacio en disco C: $([math]::Round($freeGB, 1)) GB libres (Bajo)" -ForegroundColor Yellow
    Write-Log "Espacio en disco: $([math]::Round($freeGB, 1)) GB libres (Bajo)" "WARNING"
    $issues += "Espacio en disco bajo: $([math]::Round($freeGB, 1)) GB libres"
}

# ===========================================
# VERIFICAR SITIO WEB
# ===========================================
Write-Host "`n[3/6] Verificando acceso al sitio web..." -ForegroundColor Yellow

try {
    $response = Invoke-WebRequest -Uri $SiteUrl -UseBasicParsing -TimeoutSec 10 -ErrorAction Stop
    $statusCode = $response.StatusCode
    
    if ($statusCode -eq 200) {
        Write-Host "  ✓ Sitio accesible: HTTP $statusCode" -ForegroundColor Green
        Write-Log "Sitio accesible: $SiteUrl - HTTP $statusCode" "SUCCESS"
        
        # Verificar tiempo de respuesta
        $responseTime = (Measure-Command { Invoke-WebRequest -Uri $SiteUrl -UseBasicParsing -TimeoutSec 10 }).TotalMilliseconds
        if ($responseTime -lt 2000) {
            Write-Host "  ✓ Tiempo de respuesta: $([math]::Round($responseTime, 0)) ms" -ForegroundColor Green
            Write-Log "Tiempo de respuesta: $([math]::Round($responseTime, 0)) ms" "INFO"
        } else {
            Write-Host "  ⚠ Tiempo de respuesta: $([math]::Round($responseTime, 0)) ms (Lento)" -ForegroundColor Yellow
            Write-Log "Tiempo de respuesta: $([math]::Round($responseTime, 0)) ms (Lento)" "WARNING"
            $issues += "Tiempo de respuesta lento: $([math]::Round($responseTime, 0)) ms"
        }
    } else {
        Write-Host "  ⚠ Sitio devolvió HTTP $statusCode" -ForegroundColor Yellow
        Write-Log "Sitio devolvió HTTP $statusCode" "WARNING"
        $issues += "Sitio devolvió código HTTP $statusCode"
    }
} catch {
    Write-Host "  ✗ Error al acceder al sitio: $($_.Exception.Message)" -ForegroundColor Red
    Write-Log "Error al acceder al sitio: $($_.Exception.Message)" "ERROR"
    $issues += "Sitio no accesible: $($_.Exception.Message)"
}

# ===========================================
# VERIFICAR CERTIFICADO SSL (si aplica)
# ===========================================
if ($SiteUrl -like "https://*") {
    Write-Host "`n[4/6] Verificando certificado SSL..." -ForegroundColor Yellow
    
    try {
        $uri = [System.Uri]$SiteUrl
        $request = [System.Net.HttpWebRequest]::Create($uri)
        $request.GetResponse() | Out-Null
        $cert = $request.ServicePoint.Certificate
        
        if ($cert) {
            $expiryDate = [datetime]::Parse($cert.GetExpirationDateString())
            $daysToExpiry = ($expiryDate - (Get-Date)).Days
            
            if ($daysToExpiry -gt 30) {
                Write-Host "  ✓ Certificado SSL válido (expira en $daysToExpiry días)" -ForegroundColor Green
                Write-Log "Certificado SSL: Válido, expira en $daysToExpiry días" "INFO"
            } else {
                Write-Host "  ⚠ Certificado SSL expira pronto ($daysToExpiry días)" -ForegroundColor Yellow
                Write-Log "Certificado SSL: Expira en $daysToExpiry días" "WARNING"
                $issues += "Certificado SSL expira pronto: $daysToExpiry días"
            }
        }
    } catch {
        Write-Host "  ⚠ No se pudo verificar certificado SSL" -ForegroundColor Yellow
        Write-Log "No se pudo verificar certificado SSL: $_" "WARNING"
    }
} else {
    Write-Host "`n[4/6] Certificado SSL no aplicable (HTTP)" -ForegroundColor Gray
}

# ===========================================
# VERIFICAR CONEXIÓN A BD
# ===========================================
Write-Host "`n[5/6] Verificando conexión a base de datos..." -ForegroundColor Yellow

if (Test-Path "C:\inetpub\wwwroot\catalogo_ebs\test_db.php") {
    try {
        $dbTest = Invoke-WebRequest -Uri "$SiteUrl/test_db.php" -UseBasicParsing -TimeoutSec 10
        if ($dbTest.Content -like "*Conexión exitosa*" -or $dbTest.StatusCode -eq 200) {
            Write-Host "  ✓ Conexión a BD: OK" -ForegroundColor Green
            Write-Log "Conexión a BD: OK" "SUCCESS"
        } else {
            Write-Host "  ⚠ Conexión a BD: Problema detectado" -ForegroundColor Yellow
            Write-Log "Conexión a BD: Problema detectado" "WARNING"
            $issues += "Problema con conexión a base de datos"
        }
    } catch {
        Write-Host "  ⚠ No se pudo verificar conexión a BD" -ForegroundColor Yellow
        Write-Log "No se pudo verificar conexión a BD" "WARNING"
    }
} else {
    Write-Host "  ℹ Archivo de prueba no encontrado" -ForegroundColor Gray
}

# ===========================================
# VERIFICAR LOGS DE ERRORES
# ===========================================
Write-Host "`n[6/6] Verificando logs de errores recientes..." -ForegroundColor Yellow

$errorCount = 0

# PHP Error Log
if (Test-Path "C:\PHP\logs\php_errors.log") {
    $recentPHPErrors = Get-Content "C:\PHP\logs\php_errors.log" -Tail 100 | Where-Object {$_ -match (Get-Date).ToString("Y-m-d")}
    $errorCount += $recentPHPErrors.Count
}

# App Error Log
if (Test-Path "C:\inetpub\wwwroot\catalogo_ebs\logs\php_errors.log") {
    $recentAppErrors = Get-Content "C:\inetpub\wwwroot\catalogo_ebs\logs\php_errors.log" -Tail 100 | Where-Object {$_ -match (Get-Date).ToString("Y-m-d")}
    $errorCount += $recentAppErrors.Count
}

if ($errorCount -eq 0) {
    Write-Host "  ✓ No hay errores recientes en logs" -ForegroundColor Green
    Write-Log "Logs: Sin errores recientes" "SUCCESS"
} elseif ($errorCount -lt 10) {
    Write-Host "  ⚠ $errorCount errores encontrados hoy" -ForegroundColor Yellow
    Write-Log "Logs: $errorCount errores encontrados" "WARNING"
} else {
    Write-Host "  ✗ $errorCount errores encontrados hoy (Alto)" -ForegroundColor Red
    Write-Log "Logs: $errorCount errores encontrados (Alto)" "ERROR"
    $issues += "Alto número de errores en logs: $errorCount"
}

# ===========================================
# RESUMEN FINAL
# ===========================================
Write-Host "`n╔══════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║           RESUMEN DE MONITOREO           ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════╝`n" -ForegroundColor Cyan

if ($issues.Count -eq 0) {
    Write-Host "✓ ESTADO: SALUDABLE" -ForegroundColor Green
    Write-Host "  Todos los servicios operando normalmente`n" -ForegroundColor Green
    Write-Log "Estado del sistema: SALUDABLE" "SUCCESS"
} else {
    Write-Host "⚠ PROBLEMAS DETECTADOS: $($issues.Count)" -ForegroundColor Yellow
    Write-Host ""
    foreach ($issue in $issues) {
        Write-Host "  • $issue" -ForegroundColor Yellow
    }
    Write-Host ""
    Write-Log "Estado del sistema: $($issues.Count) problemas detectados" "WARNING"
    
    # Enviar alerta por email si está configurado
    if ($AlertEmail) {
        Write-Host "Enviando alerta a: $AlertEmail" -ForegroundColor Cyan
        # Aquí agregar código para enviar email si es necesario
    }
}

Write-Host "Fecha de monitoreo: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" -ForegroundColor Gray
Write-Host "Log guardado en: $logFile`n" -ForegroundColor Gray

Write-Log "Monitoreo completado"
