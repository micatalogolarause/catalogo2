#!/bin/bash
# Script de verificación rápida de instalación

echo "==============================================="
echo "VERIFICACIÓN DE INSTALACIÓN - TIENDA VIRTUAL"
echo "==============================================="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} $1"
        return 0
    else
        echo -e "${RED}✗${NC} $1 - NO ENCONTRADO"
        return 1
    fi
}

check_dir() {
    if [ -d "$1" ]; then
        echo -e "${GREEN}✓${NC} $1"
        return 0
    else
        echo -e "${RED}✗${NC} $1 - DIRECTORIO NO ENCONTRADO"
        return 1
    fi
}

echo "Verificando estructura de directorios..."
echo ""

# Directorios
check_dir "app/controllers"
check_dir "app/models"
check_dir "app/views"
check_dir "app/views/admin"
check_dir "app/views/tienda"
check_dir "app/views/layout"
check_dir "config"
check_dir "database"
check_dir "public/css"
check_dir "public/js"
check_dir "public/images/productos"

echo ""
echo "Verificando archivos de configuración..."
echo ""

# Configuración
check_file "index.php"
check_file "config/database.php"
check_file "config/config.php"
check_file "config/installer.php"
check_file "config/generate_images.php"
check_file ".htaccess"
check_file "web.config"

echo ""
echo "Verificando controladores..."
echo ""

# Controladores
check_file "app/controllers/tiendaController.php"
check_file "app/controllers/adminController.php"
check_file "app/controllers/apiController.php"

echo ""
echo "Verificando modelos..."
echo ""

# Modelos
check_file "app/models/ProductoModel.php"
check_file "app/models/CategoriaModel.php"
check_file "app/models/SubcategoriaModel.php"
check_file "app/models/PedidoModel.php"
check_file "app/models/ClienteModel.php"

echo ""
echo "Verificando vistas..."
echo ""

# Vistas tienda
check_file "app/views/tienda/inicio.php"
check_file "app/views/tienda/categoria.php"
check_file "app/views/tienda/subcategoria.php"
check_file "app/views/tienda/producto.php"
check_file "app/views/tienda/buscar.php"
check_file "app/views/tienda/carrito.php"

# Vistas admin
check_file "app/views/admin/login.php"
check_file "app/views/admin/inicio.php"
check_file "app/views/admin/categorias.php"
check_file "app/views/admin/productos.php"
check_file "app/views/admin/pedidos.php"

echo ""
echo "Verificando recursos estáticos..."
echo ""

# CSS y JS
check_file "public/css/estilos.css"
check_file "public/css/admin.css"
check_file "public/js/main.js"
check_file "public/js/admin.js"

echo ""
echo "Verificando documentación..."
echo ""

# Documentación
check_file "README.md"
check_file "INSTALACION.md"
check_file "PLAN_TRABAJO.md"

echo ""
echo "==============================================="
echo "VERIFICACIÓN COMPLETADA"
echo "==============================================="
echo ""
echo "Próximos pasos:"
echo "1. Inicia XAMPP (Apache y MySQL)"
echo "2. Accede a: http://localhost/catalogo2"
echo "3. Login admin: http://localhost/catalogo2/?controller=admin&action=login"
echo "   Usuario: admin"
echo "   Contraseña: admin123"
echo ""
