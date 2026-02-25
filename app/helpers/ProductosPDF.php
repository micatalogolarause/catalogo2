<?php
/**
 * Generador de Reportes de Productos en PDF
 */
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/ReportUtils.php';

class ProductosPDF {
    
    /**
     * Generar reporte de productos en PDF
     * 
     * @param array $productos Lista de productos
     * @param array $tenant Datos del tenant
     * @param string $filtro Tipo de filtro aplicado (activo, inactivo, todos)
     * @return string Ruta del archivo PDF generado
     */
    public static function generar($productos, $tenant, $filtro = 'todos') {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        
        $pdf->SetCreator('Catálogo Digital');
        $pdf->SetAuthor($tenant['nombre']);
        $pdf->SetTitle('Reporte de Productos - ' . ucfirst($filtro));
        $pdf->SetSubject('Reporte de Inventario');
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        
        $pdf->AddPage();
        
        // Colores
        $colorPrimario = ReportUtils::hexToRGB($tenant['tema_color'] ?? 'azul');
        
        // ENCABEZADO
        $pdf->SetFillColor($colorPrimario[0], $colorPrimario[1], $colorPrimario[2]);
        $pdf->Rect(0, 0, 210, 30, 'F');
        
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetXY(10, 8);
        $pdf->Cell(0, 8, $tenant['titulo_empresa'] ?? $tenant['nombre'], 0, 1, 'C');
        
        $pdf->SetFont('helvetica', '', 11);
        $pdf->SetXY(10, 18);
        $pdf->Cell(0, 6, 'Reporte de Productos - ' . ucfirst($filtro), 0, 1, 'C');
        
        $pdf->SetTextColor(0, 0, 0);
        
        // Filtrar productos por tenant y por filtro (activo/inactivo/todos)
        $tenantId = $tenant['id'] ?? $tenant['tenant_id'] ?? null;
        $filtered = array();
        foreach ($productos as $p) {
            // Filtrar por tenant si el producto tiene tenant_id
            if ($tenantId !== null && isset($p['tenant_id']) && (int)$p['tenant_id'] !== (int)$tenantId) {
                continue;
            }
            // Normalizar estado: preferir campo 'activo' (1/0), luego 'estado'/'estado_producto'
            $estadoRaw = $p['activo'] ?? $p['estado'] ?? $p['estado_producto'] ?? null;
            $isActive = false;
            if (is_numeric($estadoRaw)) {
                $isActive = ((int)$estadoRaw === 1);
            } else {
                $estadoStr = is_null($estadoRaw) ? '' : strtolower(trim((string)$estadoRaw));
                $isActive = in_array($estadoStr, ['activo', 'act', '1', 'true', 't', 'si', 'sí', 's', 'on', 'yes', 'y']);
            }
            if ($filtro === 'activo' && !$isActive) continue;
            if ($filtro === 'inactivo' && $isActive) continue;
            $filtered[] = $p;
        }
        $productos = $filtered;

        // INFORMACIÓN DEL REPORTE
        $pdf->SetY(35);
        $pdf->SetFont('helvetica', '', 9);
        $fechaGeneracion = new DateTime();
        $fechaGeneracion->setTimezone(new DateTimeZone('America/Bogota'));
        $pdf->Cell(95, 5, 'Fecha: ' . $fechaGeneracion->format('d/m/Y h:i A'), 0, 0, 'L');
        $pdf->Cell(95, 5, 'Total productos: ' . count($productos), 0, 1, 'R');
        
        // TABLA DE PRODUCTOS
        $pdf->SetY(45);
        
        // Encabezado de tabla
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(8, 7, 'Nº', 1, 0, 'C', true);
        $pdf->Cell(45, 7, 'Nombre', 1, 0, 'L', true);
        $pdf->Cell(28, 7, 'Categoría', 1, 0, 'L', true);
        $pdf->Cell(18, 7, 'Precio', 1, 0, 'R', true);
        $pdf->Cell(12, 7, 'Stock', 1, 0, 'C', true);
        $pdf->Cell(22, 7, 'Valor Total', 1, 0, 'R', true);
        $pdf->Cell(18, 7, 'Estado', 1, 0, 'C', true);
        $pdf->Cell(25, 7, 'Actualizado', 1, 1, 'C', true);
        
        // Filas de productos
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetFillColor(255, 255, 255);
        
        $totalValor = 0;
        
        foreach ($productos as $producto) {
            $displayNumero = $producto['numero_producto'] ?? $producto['numero'] ?? $producto['id'];
            $precio = $producto['precio'] ?? 0;
            $stock = $producto['stock'] ?? 0;
            $valorTotal = $precio * $stock;
            
            $pdf->Cell(8, 6, $displayNumero, 1, 0, 'C');
            $pdf->Cell(45, 6, substr($producto['nombre'], 0, 30), 1, 0, 'L');
            $pdf->Cell(28, 6, substr($producto['categoria'] ?? 'Sin categoría', 0, 16), 1, 0, 'L');
            $pdf->Cell(18, 6, '$' . number_format($precio, 0), 1, 0, 'R');
            $pdf->Cell(12, 6, $stock, 1, 0, 'C');
            $pdf->Cell(22, 6, '$' . number_format($valorTotal, 0), 1, 0, 'R');
            
            // Normalizar estado: preferir 'activo' (1/0), luego 'estado'/'estado_producto'
            $estadoRaw = $producto['activo'] ?? $producto['estado'] ?? $producto['estado_producto'] ?? null;
            if (is_numeric($estadoRaw)) {
                $isActive = ((int)$estadoRaw === 1);
            } else {
                $estadoStr = is_null($estadoRaw) ? '' : strtolower(trim((string)$estadoRaw));
                $isActive = in_array($estadoStr, ['activo', 'act', '1', 'true', 't', 'si', 'sí', 's', 'on', 'yes', 'y']);
            }
            $estadoLabel = $isActive ? 'Activo' : 'Inactivo';
            $estadoColor = $isActive ? [0, 150, 0] : [150, 0, 0];
            $pdf->SetTextColor($estadoColor[0], $estadoColor[1], $estadoColor[2]);
            $pdf->Cell(18, 6, $estadoLabel, 1, 0, 'C');
            $pdf->SetTextColor(0, 0, 0);

            // Fecha actualizado/creado
            $dateStr = $producto['fecha_actualizacion'] ?? $producto['fecha_creacion'] ?? null;
            if ($dateStr) {
                try {
                    $fecha = new DateTime($dateStr);
                    $fechaStr = $fecha->format('d/m/Y');
                } catch (Exception $e) {
                    $fechaStr = '';
                }
            } else {
                $fechaStr = '';
            }
            $pdf->Cell(25, 6, $fechaStr, 1, 1, 'C');

            $totalValor += $valorTotal;
        }
        
        // RESUMEN
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(125, 7, 'Valor total en inventario:', 0, 0, 'R');
        $pdf->Cell(65, 7, '$' . number_format($totalValor, 0), 1, 1, 'R');
        
        // PIE DE PÁGINA
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'I', 7);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 4, 'Reporte generado automáticamente por Catálogo Digital', 0, 1, 'C');
        
        // Guardar PDF
        $dir = APP_ROOT . '/public/reportes';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        
        $nombreArchivo = 'productos_' . $filtro . '_' . date('Ymd_His') . '.pdf';
        $rutaCompleta = $dir . '/' . $nombreArchivo;
        
        $pdf->Output($rutaCompleta, 'F');
        
        return APP_URL . '/public/reportes/' . $nombreArchivo;
    }
    
}
?>
