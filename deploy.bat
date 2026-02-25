@echo off
REM ====================================================================
REM DEPLOYMENT WINDOWS SERVER 2019 + IIS - XAMPP Puerto 81
REM Script automatizado de verificacion y configuracion
REM ====================================================================

setlocal enabledelayedexpansion

REM Colores para Windows
set GREEN=[OK]
set RED=[ERR]
set WARN=[WARN]
set INFO=[INFO]

title Deployment - Catalogo Multi-Tenant - Windows Server 2019

echo.
echo ╔════════════════════════════════════════════════════════════════════╗
echo ║                                                                    ║
echo ║  DEPLOYMENT WINDOWS SERVER 2019 + IIS - CATALOGO MULTI-TENANT     ║
echo ║                                                                    ║
echo ║  Puerto: 81 (XAMPP)                                               ║
echo ║  Hostname: larause                                                ║
echo ║                                                                    ║
echo ╚════════════════════════════════════════════════════════════════════╝
echo.

REM ====================================================================
REM PASO 1: Verificar estructura de directorios
REM ====================================================================
echo.
echo ════════════════════════════════════════════════════════════════════
echo PASO 1: VERIFICACION DE ESTRUCTURA
echo ════════════════════════════════════════════════════════════════════
echo.

REM Directorios principales
if exist "app\controllers" (echo %GREEN% app\controllers) else (echo %RED% app\controllers FALTA)
if exist "app\models" (echo %GREEN% app\models) else (echo %RED% app\models FALTA)
if exist "app\views" (echo %GREEN% app\views) else (echo %RED% app\views FALTA)
if exist "config" (echo %GREEN% config) else (echo %RED% config FALTA)
if exist "public\css" (echo %GREEN% public\css) else (echo %RED% public\css FALTA)
if exist "public\js" (echo %GREEN% public\js) else (echo %RED% public\js FALTA)
if exist "public\images" (echo %GREEN% public\images) else (echo %RED% public\images FALTA)
if exist "public\tenants" (echo %GREEN% public\tenants) else (echo %RED% public\tenants FALTA)
if exist "scripts" (echo %GREEN% scripts) else (echo %RED% scripts FALTA)

echo.

REM Archivos críticos
if exist "index.php" (echo %GREEN% index.php) else (echo %RED% index.php FALTA)
if exist "config\database.php" (echo %GREEN% config\database.php) else (echo %RED% config\database.php FALTA)
if exist "config\config.php" (echo %GREEN% config\config.php) else (echo %RED% config\config.php FALTA)
if exist "config\TenantResolver.php" (echo %GREEN% config\TenantResolver.php) else (echo %RED% config\TenantResolver.php FALTA)
if exist ".htaccess" (echo %GREEN% .htaccess) else (echo %RED% .htaccess FALTA)
if exist "web.config" (echo %GREEN% web.config) else (echo %RED% web.config FALTA)
if exist "scripts\deployment_check.php" (echo %GREEN% deployment_check.php) else (echo %RED% deployment_check.php FALTA)

echo.

REM Archivos de documentación
if exist "COMIENZA_AQUI.md" (echo %GREEN% COMIENZA_AQUI.md) else (echo %RED% COMIENZA_AQUI.md FALTA)
if exist "PRODUCTION_READINESS.md" (echo %GREEN% PRODUCTION_READINESS.md) else (echo %RED% PRODUCTION_READINESS.md FALTA)
if exist "SETUP_WINDOWS_SERVER.md" (echo %GREEN% SETUP_WINDOWS_SERVER.md) else (echo %RED% SETUP_WINDOWS_SERVER.md FALTA)
if exist "SECURITY_CHECKLIST.md" (echo %GREEN% SECURITY_CHECKLIST.md) else (echo %RED% SECURITY_CHECKLIST.md FALTA)

REM ====================================================================
REM PASO 2: Verificar servicios XAMPP
REM ====================================================================
echo.
echo ════════════════════════════════════════════════════════════════════
echo PASO 2: VERIFICACION DE SERVICIOS XAMPP
echo ════════════════════════════════════════════════════════════════════
echo.

REM Verificar si Apache está corriendo
sc query Apache2.4 >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo %GREEN% Apache2.4 servicio existe
    sc query Apache2.4 | findstr "RUNNING" >nul 2>&1
    if !ERRORLEVEL! EQU 0 (
        echo %GREEN% Apache esta CORRIENDO
    ) else (
        echo %WARN% Apache NO esta corriendo
        echo %INFO% Comando para iniciar: net start Apache2.4
    )
) else (
    echo %RED% Apache2.4 servicio NO encontrado
    echo %INFO% Verifica que XAMPP este instalado
)

echo.

REM Verificar si MySQL está corriendo
sc query MySQL >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo %GREEN% MySQL servicio existe
    sc query MySQL | findstr "RUNNING" >nul 2>&1
    if !ERRORLEVEL! EQU 0 (
        echo %GREEN% MySQL esta CORRIENDO
    ) else (
        echo %WARN% MySQL NO esta corriendo
        echo %INFO% Comando para iniciar: net start MySQL
    )
) else (
    echo %RED% MySQL servicio NO encontrado
    echo %INFO% Verifica que XAMPP este instalado
)

REM ====================================================================
REM PASO 3: Verificar puerto Apache
REM ====================================================================
echo.
echo ════════════════════════════════════════════════════════════════════
echo PASO 3: VERIFICACION DE PUERTO
echo ════════════════════════════════════════════════════════════════════
echo.

if exist "C:\xampp\apache\conf\httpd.conf" (
    findstr /C:"Listen 81" C:\xampp\apache\conf\httpd.conf >nul 2>&1
    if !ERRORLEVEL! EQU 0 (
        echo %GREEN% Apache configurado en puerto 81
    ) else (
        echo %WARN% Apache NO configurado en puerto 81
        echo %INFO% Editar: C:\xampp\apache\conf\httpd.conf
        echo %INFO% Cambiar: Listen 80 a Listen 81
    )
) else (
    echo %WARN% No se encuentra httpd.conf en ubicacion estandar
)

REM ====================================================================
REM PASO 4: Verificar permisos de carpetas
REM ====================================================================
echo.
echo ════════════════════════════════════════════════════════════════════
echo PASO 4: VERIFICACION DE PERMISOS
echo ════════════════════════════════════════════════════════════════════
echo.

echo %INFO% Verificando permisos de escritura...
echo.

REM Crear carpeta logs si no existe
if not exist "logs" (
    mkdir logs 2>nul
    if !ERRORLEVEL! EQU 0 (
        echo %GREEN% Carpeta logs creada
    ) else (
        echo %RED% No se pudo crear carpeta logs
    )
) else (
    echo %GREEN% Carpeta logs existe
)

REM Probar escritura en public\tenants
echo test > public\tenants\test.txt 2>nul
if exist "public\tenants\test.txt" (
    echo %GREEN% public\tenants es escribible
    del public\tenants\test.txt >nul 2>&1
) else (
    echo %WARN% public\tenants NO es escribible
    echo %INFO% Ejecutar: icacls public\tenants /grant "Users":M /T
)

REM Probar escritura en logs
echo test > logs\test.txt 2>nul
if exist "logs\test.txt" (
    echo %GREEN% logs es escribible
    del logs\test.txt >nul 2>&1
) else (
    echo %WARN% logs NO es escribible
    echo %INFO% Ejecutar: icacls logs /grant "Users":M /T
)

REM ====================================================================
REM PASO 5: Ejecutar validador PHP
REM ====================================================================
echo.
echo ════════════════════════════════════════════════════════════════════
echo PASO 5: VALIDACION PHP AUTOMATICA
echo ════════════════════════════════════════════════════════════════════
echo.

where php >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo %GREEN% PHP encontrado en PATH
    echo.
    echo Ejecutando deployment_check.php...
    echo.
    php scripts\deployment_check.php
) else (
    echo %WARN% PHP no encontrado en PATH
    echo %INFO% Intenta: C:\xampp\php\php.exe scripts\deployment_check.php
    echo.
    if exist "C:\xampp\php\php.exe" (
        echo Ejecutando con ruta completa...
        echo.
        C:\xampp\php\php.exe scripts\deployment_check.php
    )
)

REM ====================================================================
REM RESUMEN Y PROXIMOS PASOS
REM ====================================================================
echo.
echo ════════════════════════════════════════════════════════════════════
echo RESUMEN Y PROXIMOS PASOS
echo ════════════════════════════════════════════════════════════════════
echo.

echo COMANDOS RAPIDOS:
echo.
echo 1. Iniciar servicios:
echo    net start Apache2.4
echo    net start MySQL
echo.
echo 2. Configurar permisos:
echo    icacls public\tenants /grant "Users":M /T
echo    icacls logs /grant "Users":M /T
echo.
echo 3. Cambiar puerto Apache (si no esta en 81):
echo    notepad C:\xampp\apache\conf\httpd.conf
echo    Buscar: Listen 80
echo    Cambiar a: Listen 81
echo    Guardar y reiniciar Apache
echo.
echo 4. Acceder a la aplicacion:
echo    http://localhost:81/catalogo2
echo    http://larause:81/catalogo2
echo.
echo 5. Super-Admin:
echo    http://localhost:81/catalogo2/index.php?controller=superAdmin^&action=login
echo    Usuario: superadmin
echo    Password: SuperAdmin123!
echo.
echo 6. Admin Tienda:
echo    http://localhost:81/catalogo2/default/index.php?controller=admin
echo    Usuario: admin
echo    Password: admin123
echo.
echo ════════════════════════════════════════════════════════════════════
echo.
echo %INFO% Lee: COMIENZA_AQUI.md para instrucciones detalladas
echo %INFO% Lee: SETUP_WINDOWS_SERVER.md para configuracion completa
echo %INFO% Lee: SECURITY_CHECKLIST.md ANTES de lanzar a produccion
echo.
echo ════════════════════════════════════════════════════════════════════

pause
