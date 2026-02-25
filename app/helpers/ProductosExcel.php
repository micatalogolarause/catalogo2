<?php
/**
 * Generador de Reportes de Productos en Excel
 */
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/ReportUtils.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProductosExcel {
    
    /**
     * Generar reporte de productos en Excel
     * 
     * @param array $productos Lista de productos
     * @param array $tenant Datos del tenant
     * @param string $filtro Tipo de filtro aplicado (activo, inactivo, todos)
     * @return string Ruta del archivo Excel generado
     */
    public static function generar($productos, $tenant, $filtro = 'todos') {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Productos ' . ucfirst($filtro));
        
        // ENCABEZADO
        $sheet->setCellValue('A1', $tenant['titulo_empresa'] ?? $tenant['nombre']);
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Reporte de Productos - ' . ucfirst($filtro));
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Filtrar productos por tenant y por filtro (activo/inactivo/todos)
        $tenantId = $tenant['id'] ?? $tenant['tenant_id'] ?? null;
        $filtered = array();
        foreach ($productos as $p) {
            if ($tenantId !== null && isset($p['tenant_id']) && (int)$p['tenant_id'] !== (int)$tenantId) continue;
            $estadoRaw = $p['activo'] ?? $p['estado'] ?? $p['estado_producto'] ?? null;
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
        $row = 4;
        $fechaGeneracion = new DateTime();
        $fechaGeneracion->setTimezone(new DateTimeZone('America/Bogota'));
        $sheet->setCellValue('A' . $row, 'Fecha: ' . $fechaGeneracion->format('d/m/Y h:i A'));
        $sheet->setCellValue('F' . $row, 'Total productos: ' . count($productos));
        
        // ENCABEZADO DE TABLA
        $row = 6;
        $headers = ['Núm.', 'Nombre', 'Categoría', 'Precio', 'Stock', 'Valor Total', 'Estado', 'Actualizado'];
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
        
        // DATOS DE PRODUCTOS
        $row = 7;
        $totalValor = 0;
        
        foreach ($productos as $producto) {
            $displayNumero = $producto['numero_producto'] ?? $producto['numero'] ?? $producto['id'];
            $precio = $producto['precio'] ?? 0;
            $stock = $producto['stock'] ?? 0;
            $valorTotal = $precio * $stock;
            
            $sheet->setCellValue('A' . $row, $displayNumero);
            $sheet->setCellValue('B' . $row, $producto['nombre']);
            $sheet->setCellValue('C' . $row, $producto['categoria'] ?? 'Sin categoría');
            $sheet->setCellValue('D' . $row, '$' . number_format($precio, 0));
            $sheet->setCellValue('E' . $row, $stock);
            $sheet->setCellValue('F' . $row, '$' . number_format($valorTotal, 0));
            $estadoRaw = $producto['activo'] ?? $producto['estado'] ?? $producto['estado_producto'] ?? null;
            if (is_numeric($estadoRaw)) {
                $isActive = ((int)$estadoRaw === 1);
            } else {
                $estadoStr = is_null($estadoRaw) ? '' : strtolower(trim((string)$estadoRaw));
                $isActive = in_array($estadoStr, ['activo', 'act', '1', 'true', 't', 'si', 'sí', 's', 'on', 'yes', 'y']);
            }
            $sheet->setCellValue('G' . $row, $isActive ? 'Activo' : 'Inactivo');

            $dateStr = $producto['fecha_actualizacion'] ?? $producto['fecha_creacion'] ?? null;
            if ($dateStr) {
                try {
                    $fecha = new DateTime($dateStr);
                    $sheet->setCellValue('H' . $row, $fecha->format('d/m/Y'));
                } catch (Exception $e) {
                    $sheet->setCellValue('H' . $row, '');
                }
            } else {
                $sheet->setCellValue('H' . $row, '');
            }

            // Estilo de estado
            if ($isActive) {
                $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('008000');
            } else {
                $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('FF0000');
            }
            
            // Bordes
            $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Alineaciones
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $totalValor += $valorTotal;
            $row++;
        }
        
        // RESUMEN
        $row += 2;
        $sheet->setCellValue('C' . $row, 'Valor total en inventario:');
        $sheet->getStyle('C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        $sheet->setCellValue('D' . $row, '$' . number_format($totalValor, 0));
        $sheet->getStyle('D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('D' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFEB9C');
        $sheet->getStyle('D' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        
        // Ajustar anchos
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(15);
        
        // Guardar archivo
        $dir = APP_ROOT . '/public/reportes';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        
        $nombreArchivo = 'productos_' . $filtro . '_' . date('Ymd_His') . '.xlsx';
        $rutaCompleta = $dir . '/' . $nombreArchivo;
        
        // Configurar el writer con opciones de compatibilidad
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false); // No pre-calcular fórmulas
        
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
}
?>
