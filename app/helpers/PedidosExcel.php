<?php
/**
 * Generador de Reportes de Pedidos en Excel
 */
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/ReportUtils.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PedidosExcel {
    
    /**
     * Generar reporte de pedidos en Excel
     * 
     * @param array $pedidos Lista de pedidos
     * @param array $tenant Datos del tenant
     * @param array $filtros Array con filtros aplicados
     * @return string Ruta del archivo Excel generado
     */
    public static function generar($pedidos, $tenant, $filtros = []) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Pedidos');
        
        // ENCABEZADO
        $sheet->setCellValue('A1', $tenant['titulo_empresa'] ?? $tenant['nombre']);
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Reporte de Pedidos');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // INFORMACIÓN DEL REPORTE
        $row = 4;
        $fechaGeneracion = new DateTime();
        $fechaGeneracion->setTimezone(new DateTimeZone('America/Bogota'));
        $sheet->setCellValue('A' . $row, 'Fecha: ' . $fechaGeneracion->format('d/m/Y h:i A'));
        
        if (!empty($filtros['cliente'])) {
            $sheet->setCellValue('C' . $row, 'Cliente: ' . $filtros['cliente']);
        }
        if (!empty($filtros['desde'])) {
            $sheet->setCellValue('E' . $row, 'Desde: ' . $filtros['desde']);
        }
        if (!empty($filtros['hasta'])) {
            $sheet->setCellValue('G' . $row, 'Hasta: ' . $filtros['hasta']);
        }
        
        $row = 5;
        if (!empty($filtros['estado'])) {
            $sheet->setCellValue('A' . $row, 'Estado: ' . $filtros['estado']);
        }
        $sheet->setCellValue('G' . $row, 'Total pedidos: ' . count($pedidos));
        
        // ENCABEZADO DE TABLA
        $row = 7;
        $headers = ['ID', 'Cliente', 'WhatsApp', 'Dirección', 'Fecha', 'Total', 'Estado', 'Productos'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E8E8E8');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $col++;
        }
        
        // DATOS DE PEDIDOS
        $row = 8;
        $totalGeneral = 0;
        
        foreach ($pedidos as $pedido) {
            $sheet->setCellValue('A' . $row, $pedido['id']);
            $sheet->setCellValue('B' . $row, $pedido['nombre'] ?? $pedido['cliente_nombre']);
            $sheet->setCellValue('C' . $row, $pedido['whatsapp']);
            $sheet->setCellValue('D' . $row, $pedido['direccion'] ?? '');
            
            $fecha = new DateTime($pedido['fecha_creacion']);
            $fecha->setTimezone(new DateTimeZone('America/Bogota'));
            $sheet->setCellValue('E' . $row, $fecha->format('d/m/Y'));
            
            $sheet->setCellValue('F' . $row, '$' . number_format($pedido['total'], 0));
            $sheet->setCellValue('G' . $row, ReportUtils::getEstadoLabel($pedido['estado']));
            
            // Productos
            $productosStr = self::formatearProductos($pedido['detalles'] ?? []);
            $sheet->setCellValue('H' . $row, $productosStr);
            
            // Estilo de estado
            $estadoColor = ReportUtils::getEstadoColor($pedido['estado']);
            $hexColor = strtoupper(str_pad(dechex($estadoColor[0]), 2, '0', STR_PAD_LEFT) .
                                  str_pad(dechex($estadoColor[1]), 2, '0', STR_PAD_LEFT) .
                                  str_pad(dechex($estadoColor[2]), 2, '0', STR_PAD_LEFT));
            $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB($hexColor);
            
            // Bordes
            $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Alineaciones
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $totalGeneral += $pedido['total'];
            $row++;
        }
        
        // RESUMEN
        $row += 2;
        $sheet->setCellValue('E' . $row, 'TOTAL GENERAL:');
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        $sheet->setCellValue('F' . $row, '$' . number_format($totalGeneral, 0));
        $sheet->getStyle('F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFEB9C');
        $sheet->getStyle('F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        
        // Ajustar anchos
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(30);
        
        // Guardar archivo
        $dir = APP_ROOT . '/public/reportes';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        
        $nombreArchivo = 'pedidos_' . date('Ymd_His') . '.xlsx';
        $rutaCompleta = $dir . '/' . $nombreArchivo;
        
        // Configurar el writer con opciones de compatibilidad
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        
        // Guardar el archivo
        try {
            $writer->save($rutaCompleta);
        } catch (Exception $e) {
            throw new Exception('Error al guardar archivo Excel: ' . $e->getMessage());
        }
        
        // Verificar que el archivo se creó correctamente
        if (!file_exists($rutaCompleta) || filesize($rutaCompleta) === 0) {
            throw new Exception('El archivo Excel no se generó correctamente');
        }
        
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
        
        return implode(', ', $productos);
    }
}
?>
