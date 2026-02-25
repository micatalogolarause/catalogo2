<?php
/**
 * API - Obtener subcategorías por categoría (AJAX)
 */
require_once 'config/config.php';
require_once 'config/database.php';

header('Content-Type: application/json');

$categoria_id = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : 0;

if (!$categoria_id) {
    echo json_encode(['error' => 'Categoría no especificada']);
    exit;
}

require_once 'app/models/SubcategoriaModel.php';
global $conn;

$subcategoriaModel = new SubcategoriaModel($conn);
$subcategorias = $subcategoriaModel->obtenerPorCategoria($categoria_id);

echo json_encode($subcategorias);
?>
