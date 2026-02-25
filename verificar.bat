@echo off
REM Verificación rápida de instalación en Windows

echo ===============================================
echo VERIFICACION DE INSTALACION - TIENDA VIRTUAL
echo ===============================================
echo.

setlocal enabledelayedexpansion

REM Colores (usando caracteres simples para Windows)
set GREEN=[OK]
set RED=[ERR]

echo Verificando estructura de directorios...
echo.

REM Directorios principales
if exist "app\controllers" (echo %GREEN% app\controllers) else (echo %RED% app\controllers)
if exist "app\models" (echo %GREEN% app\models) else (echo %RED% app\models)
if exist "app\views" (echo %GREEN% app\views) else (echo %RED% app\views)
if exist "config" (echo %GREEN% config) else (echo %RED% config)
if exist "database" (echo %GREEN% database) else (echo %RED% database)
if exist "public\css" (echo %GREEN% public\css) else (echo %RED% public\css)
if exist "public\js" (echo %GREEN% public\js) else (echo %RED% public\js)
if exist "public\images\productos" (echo %GREEN% public\images\productos) else (echo %RED% public\images\productos)

echo.
echo Verificando archivos principales...
echo.

REM Archivos principales
if exist "index.php" (echo %GREEN% index.php) else (echo %RED% index.php)
if exist "config\database.php" (echo %GREEN% config\database.php) else (echo %RED% config\database.php)
if exist "config\config.php" (echo %GREEN% config\config.php) else (echo %RED% config\config.php)
if exist ".htaccess" (echo %GREEN% .htaccess) else (echo %RED% .htaccess)
if exist "web.config" (echo %GREEN% web.config) else (echo %RED% web.config)

echo.
echo Verificando controladores...
echo.

if exist "app\controllers\tiendaController.php" (echo %GREEN% tiendaController.php) else (echo %RED% tiendaController.php)
if exist "app\controllers\adminController.php" (echo %GREEN% adminController.php) else (echo %RED% adminController.php)
if exist "app\controllers\apiController.php" (echo %GREEN% apiController.php) else (echo %RED% apiController.php)

echo.
echo Verificando modelos...
echo.

if exist "app\models\ProductoModel.php" (echo %GREEN% ProductoModel.php) else (echo %RED% ProductoModel.php)
if exist "app\models\CategoriaModel.php" (echo %GREEN% CategoriaModel.php) else (echo %RED% CategoriaModel.php)
if exist "app\models\PedidoModel.php" (echo %GREEN% PedidoModel.php) else (echo %RED% PedidoModel.php)

echo.
echo Verificando vistas...
echo.

if exist "app\views\tienda\inicio.php" (echo %GREEN% tienda\inicio.php) else (echo %RED% tienda\inicio.php)
if exist "app\views\admin\login.php" (echo %GREEN% admin\login.php) else (echo %RED% admin\login.php)
if exist "app\views\admin\inicio.php" (echo %GREEN% admin\inicio.php) else (echo %RED% admin\inicio.php)

echo.
echo ===============================================
echo VERIFICACION COMPLETADA
echo ===============================================
echo.
echo PROXIMOS PASOS:
echo 1. Inicia XAMPP (Apache y MySQL)
echo 2. Accede a: http://localhost/catalogo2
echo 3. La BD se creara automaticamente
echo 4. Login admin: http://localhost/catalogo2/?controller=admin^&action=login
echo    Usuario: admin
echo    Contraseña: admin123
echo.

pause
