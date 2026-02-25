# Script de Backup Automático
# Distribuciones EBS - Catálogo Digital

<#
.SYNOPSIS
    Realiza backup automático de archivos y base de datos

.DESCRIPTION
    Crea respaldo completo de la aplicación y base de datos MySQL
    Comprime los archivos y elimina backups antiguos

.PARAMETER BackupPath
    Ruta donde se guardarán los backups (default: D:\Backups\TiendaEBS)

.PARAMETER RetentionDays
    Días que se conservarán los backups (default: 30)

.EXAMPLE
    .\Backup-TiendaEBS.ps1
    .\Backup-TiendaEBS.ps1 -BackupPath "E:\Backups" -RetentionDays 60
#>

param(
    [Parameter(Mandatory=$false)]
    [string]$BackupPath = "D:\Backups\TiendaEBS",
    
    [Parameter(Mandatory=$false)]
    [int]$RetentionDays = 30,
    
    [Parameter(Mandatory=$false)]
    [string]$AppPath = "C:\inetpub\wwwroot\catalogo_ebs",
    
    [Parameter(Mandatory=$false)]
    [string]$DBUser = "tienda_user",
    
    [Parameter(Mandatory=$false)]
    [string]$DBPassword = "",
    
    [Parameter(Mandatory=$false)]
    [string]$DBName = "catalogo_tienda"
)

$ErrorActionPreference = "Continue"
$date = Get-Date -Format 'yyyyMMdd_HHmmss'
$backupFolder = Join-Path $BackupPath $date
$logFile = "C:\Scripts\Logs\Backup-$date.log"

# Crear carpetas
New-Item -Path $backupFolder -ItemType Directory -Force | Out-Null
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

Write-Log "============================================" "INFO"
Write-Log "Iniciando backup de Tienda EBS" "INFO"
Write-Log "Fecha: $date" "INFO"
Write-Log "============================================" "INFO"

# ===========================================
# BACKUP DE ARCHIVOS
# ===========================================
Write-Log "Iniciando backup de archivos..." "INFO"
Write-Log "Origen: $AppPath" "INFO"
Write-Log "Destino: $backupFolder\files" "INFO"

try {
    $filesBackup = Join-Path $backupFolder "files"
    Copy-Item -Path "$AppPath\*" -Destination $filesBackup -Recurse -Force -ErrorAction Stop
    
    # Calcular tamaño
    $size = (Get-ChildItem $filesBackup -Recurse | Measure-Object -Property Length -Sum).Sum / 1MB
    Write-Log "Backup de archivos completado: $([math]::Round($size, 2)) MB" "SUCCESS"
} catch {
    Write-Log "Error al realizar backup de archivos: $_" "ERROR"
}

# ===========================================
# BACKUP DE BASE DE DATOS
# ===========================================
Write-Log "Iniciando backup de base de datos..." "INFO"

try {
    $dbBackupFile = Join-Path $backupFolder "database.sql"
    
    # Buscar mysqldump
    $mysqldumpPaths = @(
        "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe",
        "C:\Program Files\MySQL\MySQL Server 5.7\bin\mysqldump.exe",
        "C:\xampp\mysql\bin\mysqldump.exe",
        "C:\Program Files\MariaDB 10.11\bin\mysqldump.exe"
    )
    
    $mysqldump = $null
    foreach ($path in $mysqldumpPaths) {
        if (Test-Path $path) {
            $mysqldump = $path
            break
        }
    }
    
    if ($mysqldump) {
        Write-Log "Usando mysqldump: $mysqldump" "INFO"
        
        if ($DBPassword) {
            & $mysqldump -u $DBUser -p$DBPassword $DBName > $dbBackupFile 2>&1
        } else {
            & $mysqldump -u $DBUser $DBName > $dbBackupFile 2>&1
        }
        
        if (Test-Path $dbBackupFile) {
            $dbSize = (Get-Item $dbBackupFile).Length / 1MB
            Write-Log "Backup de base de datos completado: $([math]::Round($dbSize, 2)) MB" "SUCCESS"
        } else {
            Write-Log "No se pudo crear el archivo de backup de BD" "ERROR"
        }
    } else {
        Write-Log "No se encontró mysqldump.exe" "WARNING"
        Write-Log "Verifique que MySQL/MariaDB esté instalado correctamente" "WARNING"
    }
} catch {
    Write-Log "Error al realizar backup de base de datos: $_" "ERROR"
}

# ===========================================
# COMPRIMIR BACKUP
# ===========================================
Write-Log "Comprimiendo backup..." "INFO"

try {
    $zipFile = "$backupFolder.zip"
    Compress-Archive -Path "$backupFolder\*" -DestinationPath $zipFile -Force
    
    $zipSize = (Get-Item $zipFile).Length / 1MB
    Write-Log "Backup comprimido: $([math]::Round($zipSize, 2)) MB" "SUCCESS"
    Write-Log "Archivo: $zipFile" "INFO"
    
    # Eliminar carpeta temporal
    Remove-Item $backupFolder -Recurse -Force
    Write-Log "Carpeta temporal eliminada" "INFO"
} catch {
    Write-Log "Error al comprimir backup: $_" "ERROR"
}

# ===========================================
# ELIMINAR BACKUPS ANTIGUOS
# ===========================================
Write-Log "Limpiando backups antiguos (más de $RetentionDays días)..." "INFO"

try {
    $cutoffDate = (Get-Date).AddDays(-$RetentionDays)
    $oldBackups = Get-ChildItem $BackupPath -Filter "*.zip" | Where-Object {$_.LastWriteTime -lt $cutoffDate}
    
    if ($oldBackups) {
        foreach ($backup in $oldBackups) {
            Remove-Item $backup.FullName -Force
            Write-Log "Eliminado: $($backup.Name)" "INFO"
        }
        Write-Log "Backups antiguos eliminados: $($oldBackups.Count)" "SUCCESS"
    } else {
        Write-Log "No hay backups antiguos para eliminar" "INFO"
    }
} catch {
    Write-Log "Error al eliminar backups antiguos: $_" "ERROR"
}

# ===========================================
# RESUMEN
# ===========================================
$totalBackups = (Get-ChildItem $BackupPath -Filter "*.zip").Count
$totalSize = ((Get-ChildItem $BackupPath -Filter "*.zip" | Measure-Object -Property Length -Sum).Sum) / 1GB

Write-Log "============================================" "INFO"
Write-Log "Backup completado" "SUCCESS"
Write-Log "Total de backups: $totalBackups" "INFO"
Write-Log "Espacio usado: $([math]::Round($totalSize, 2)) GB" "INFO"
Write-Log "============================================" "INFO"
