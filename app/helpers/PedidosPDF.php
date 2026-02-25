<?php
/**
 * Generador de Reportes de Pedidos en PDF
 */
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/ReportUtils.php';

class PedidosPDF {
    
    /**
     * Generar reporte de pedidos en PDF
     * 
     * @param array $pedidos Lista de pedidos
     * @param array $tenant Datos del tenant
     * @param array $filtros Array con filtros aplicados
     * @return string Ruta del archivo PDF generado
     */
    public static function generar($pedidos, $tenant, $filtros = []) {
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        
        $pdf->SetCreator('Catálogo Digital');
        $pdf->SetAuthor($tenant['nombre']);
        $pdf->SetTitle('Reporte de Pedidos');
        $pdf->SetSubject('Reporte de Pedidos');
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(8, 8, 8);
        $pdf->SetAutoPageBreak(true, 8);
        
        $pdf->AddPage();
        
        // Colores
        $colorPrimario = ReportUtils::hexToRGB($tenant['tema_color'] ?? 'azul');
        
        // ENCABEZADO
        $pdf->SetFillColor($colorPrimario[0], $colorPrimario[1], $colorPrimario[2]);
        $pdf->Rect(0, 0, 297, 25, 'F');
        
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetXY(8, 6);
        $pdf->Cell(0, 8, $tenant['titulo_empresa'] ?? $tenant['nombre'], 0, 1, 'C');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetXY(8, 15);
        $pdf->Cell(0, 5, 'Reporte de Pedidos', 0, 1, 'C');
        
        $pdf->SetTextColor(0, 0, 0);
        
        // INFORMACIÓN DEL REPORTE
        $pdf->SetY(30);
        $pdf->SetFont('helvetica', '', 8);
        $fechaGeneracion = new DateTime();
        $fechaGeneracion->setTimezone(new DateTimeZone('America/Bogota'));
        
        $infoFiltros = "Fecha: " . $fechaGeneracion->format('d/m/Y h:i A');
        if (!empty($filtros['cliente'])) {
            $infoFiltros .= " | Cliente: " . $filtros['cliente'];
        }
        if (!empty($filtros['desde'])) {
            $infoFiltros .= " | Desde: " . $filtros['desde'];
        }
        if (!empty($filtros['hasta'])) {
            $infoFiltros .= " | Hasta: " . $filtros['hasta'];
        }
        if (!empty($filtros['estado'])) {
            $infoFiltros .= " | Estado: " . $filtros['estado'];
        }
        
        $pdf->MultiCell(0, 4, $infoFiltros, 0, 'L');
        
        // TABLA DE PEDIDOS
        $pdf->SetY(42);
        
        // Encabezado de tabla
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell(12, 6, 'ID', 1, 0, 'C', true);
        $pdf->Cell(25, 6, 'Cliente', 1, 0, 'L', true);
        $pdf->Cell(18, 6, 'WhatsApp', 1, 0, 'L', true);
        $pdf->Cell(25, 6, 'Dirección', 1, 0, 'L', true);
        $pdf->Cell(20, 6, 'Fecha', 1, 0, 'C', true);
        $pdf->Cell(15, 6, 'Total', 1, 0, 'R', true);
        $pdf->Cell(20, 6, 'Estado', 1, 0, 'C', true);
        $pdf->Cell(30, 6, 'Productos', 1, 1, 'L', true);
        
        // Filas de pedidos
        $pdf->SetFont('helvetica', '', 6);
        $pdf->SetFillColor(255, 255, 255);
        
        $totalGeneral = 0;
        
        foreach ($pedidos as $pedido) {
            $pdf->Cell(12, 6, $pedido['id'], 1, 0, 'C');
            $pdf->Cell(25, 6, substr($pedido['nombre'] ?? $pedido['cliente_nombre'], 0, 25), 1, 0, 'L');
            $pdf->Cell(18, 6, substr($pedido['whatsapp'] ?? '', 0, 15), 1, 0, 'L');
            $pdf->Cell(25, 6, substr($pedido['direccion'] ?? '', 0, 25), 1, 0, 'L');
            
            $fecha = new DateTime($pedido['fecha_creacion']);
            $fecha->setTimezone(new DateTimeZone('America/Bogota'));
            $pdf->Cell(20, 6, $fecha->format('d/m/Y'), 1, 0, 'C');
            
            $pdf->Cell(15, 6, '$' . number_format($pedido['total'], 0), 1, 0, 'R');
            
            // Estado con color
            $estadoLabel = ReportUtils::getEstadoLabel($pedido['estado']);
            $estadoColor = ReportUtils::getEstadoColor($pedido['estado']);
            $pdf->SetTextColor($estadoColor[0], $estadoColor[1], $estadoColor[2]);
            $pdf->Cell(20, 6, $estadoLabel, 1, 0, 'C');
            $pdf->SetTextColor(0, 0, 0);
            
            // Productos
            $productosStr = self::formatearProductos($pedido['detalles'] ?? []);
            $pdf->Cell(30, 6, $productosStr, 1, 1, 'L');
            
            $totalGeneral += $pedido['total'];
        }
        
        // TOTAL
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(165, 8, '', 0, 0, 'L');
        $pdf->Cell(50, 8, 'TOTAL GENERAL: $' . number_format($totalGeneral, 0), 1, 1, 'R', true);
        
        // PIE DE PÁGINA
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'I', 6);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 3, 'Reporte generado automáticamente por Catálogo Digital | Total pedidos: ' . count($pedidos), 0, 1, 'C');
        
        // Guardar PDF
        $dir = APP_ROOT . '/public/reportes';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        
        $nombreArchivo = 'pedidos_' . date('Ymd_His') . '.pdf';
        $rutaCompleta = $dir . '/' . $nombreArchivo;
        
        $pdf->Output($rutaCompleta, 'F');
        
        return APP_URL . '/public/reportes/' . $nombreArchivo;
    }
    
    /**
     * Formatear productos en una línea
     */
    private static function formatearProductos($detalles) {
        if (empty($detalles)) return 'Sin productos';
        
        $productos = [];
        foreach ($detalles as $d) {
            $productos[] = $d['nombre'] . ' x' . $d['cantidad'];
        }
        
        return substr(implode(', ', $productos), 0, 30);
    }
}
?>
