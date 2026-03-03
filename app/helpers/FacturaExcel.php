<?php
/**
 * Generador de Cuentas de Cobro en Excel usando PhpSpreadsheet
 */
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/ReportUtils.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class FacturaExcel {
    
    /**
     * Generar factura en Excel
     * 
     * @param array $pedido Datos del pedido
     * @param array $detalles Items del pedido
     * @param array $tenant Datos del tenant
     * @return string Ruta del archivo Excel generado
     */
    public static function generar($pedido, $detalles, $tenant) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar título de la hoja
        $displayNumber = $pedido['numero_cuenta_cobro'] ?? $pedido['numero_pedido'] ?? $pedido['id'];
        $sheet->setTitle('Cuenta de Cobro ' . $displayNumber);
        
        // ENCABEZADO DE LA EMPRESA
        // Insertar logo si existe
        $logoInsertado = false;
        if (!empty($tenant['logo']) && is_file(APP_ROOT . '/' . $tenant['logo'])) {
            try {
                $logoPath = APP_ROOT . '/' . $tenant['logo'];
                $imageInfo = @getimagesize($logoPath);
                
                // Verificar que es una imagen válida y soportada (JPG, PNG, GIF, WEBP)
                if ($imageInfo && in_array($imageInfo[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP])) {
                    $drawing = new Drawing();
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(60);
                    $drawing->setCoordinates('A1');
                    $drawing->setWorksheet($sheet);
                    $logoInsertado = true;
                    
                    // Ajustar título hacia la derecha
                    $sheet->setCellValue('B1', $tenant['titulo_empresa'] ?? $tenant['nombre']);
                    $sheet->mergeCells('B1:D1');
                    $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(18);
                    $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            } catch (Exception $e) {
                // Si falla la inserción del logo, continuar sin él
                $logoInsertado = false;
            }
        }
        
        // Si no se insertó el logo, centrar el título normalmente
        if (!$logoInsertado) {
            $sheet->setCellValue('A1', $tenant['titulo_empresa'] ?? $tenant['nombre']);
            $sheet->mergeCells('A1:D1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        
        $sheet->setCellValue('A2', 'WhatsApp: ' . $tenant['whatsapp_phone']);
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // NÚMERO DE CUENTA DE COBRO
        $sheet->setCellValue('E1', 'CUENTA DE COBRO');
        $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        $sheet->setCellValue('E2', 'No. ' . str_pad($displayNumber, 6, '0', STR_PAD_LEFT));
        $sheet->getStyle('E2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // INFORMACIÓN DEL PEDIDO
        $row = 4;
        $sheet->setCellValue('A' . $row, 'INFORMACIÓN DEL PEDIDO');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E8E8E8');
        
        $row++;
        $fechaObj = new DateTime($pedido['fecha_creacion']);
        $fechaObj->setTimezone(new DateTimeZone('America/Bogota'));
        $fecha = $fechaObj->format('d/m/Y h:i A');
        
        $sheet->setCellValue('A' . $row, 'Fecha Pedido:');
        $sheet->setCellValue('B' . $row, $fecha);
        $sheet->setCellValue('D' . $row, 'Estado:');
        $sheet->setCellValue('E' . $row, ReportUtils::getEstadoLabel($pedido['estado']));
        
        // DATOS DEL CLIENTE
        $row += 2;
        $sheet->setCellValue('A' . $row, 'DATOS DEL CLIENTE');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E8E8E8');
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Nombre:');
        $sheet->setCellValue('B' . $row, $pedido['nombre']);
        
        $row++;
        if (!empty($pedido['email'])) {
            $sheet->setCellValue('A' . $row, 'Email:');
            $sheet->setCellValue('B' . $row, $pedido['email']);
            $row++;
        }
        
        $sheet->setCellValue('A' . $row, 'WhatsApp:');
        $sheet->setCellValue('B' . $row, $pedido['whatsapp']);
        
        $row++;
        $fechaGeneracion = new DateTime();
        $fechaGeneracion->setTimezone(new DateTimeZone('America/Bogota'));
        $sheet->setCellValue('A' . $row, 'Fecha Cuenta de Cobro:');
        $sheet->setCellValue('B' . $row, $fechaGeneracion->format('d/m/Y h:i A'));
        
        $row++;
        if (!empty($pedido['ciudad'])) {
            $sheet->setCellValue('A' . $row, 'Ciudad:');
            $sheet->setCellValue('B' . $row, $pedido['ciudad']);
            $row++;
        }
        
        if (!empty($pedido['direccion'])) {
            $sheet->setCellValue('A' . $row, 'Dirección:');
            $sheet->setCellValue('B' . $row, $pedido['direccion']);
            $row++;
        }
        
        // TABLA DE PRODUCTOS
        $row += 2;
        $sheet->setCellValue('A' . $row, 'DETALLE DE PRODUCTOS');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E8E8E8');
        
        // Encabezados de tabla
        $row++;
        $headerRow = $row;
        $sheet->setCellValue('A' . $row, 'Producto');
        $sheet->setCellValue('B' . $row, 'Precio Unit.');
        $sheet->setCellValue('C' . $row, 'Cantidad');
        $sheet->setCellValue('D' . $row, 'Subtotal');
        
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F5F5F5');
        $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()
            ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Datos de productos
        foreach ($detalles as $item) {
            $row++;
            $sheet->setCellValue('A' . $row, $item['nombre']);
            $sheet->setCellValue('B' . $row, '$' . number_format($item['precio_unitario'], 0));
            $sheet->setCellValue('C' . $row, $item['cantidad']);
            $sheet->setCellValue('D' . $row, '$' . number_format($item['subtotal'], 0));
            
            $sheet->getStyle('B' . $row . ':D' . $row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
        
        // TOTAL
        $row++;
        $sheet->setCellValue('C' . $row, 'TOTAL:');
        $sheet->setCellValue('D' . $row, '$' . number_format($pedido['total'], 0));
        $sheet->getStyle('C' . $row . ':D' . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('C' . $row . ':D' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFEB9C');
        $sheet->getStyle('C' . $row . ':D' . $row)->getBorders()
            ->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        
        // PIE DE PÁGINA
        $row += 3;
        $sheet->setCellValue('A' . $row, 'Gracias por su compra.');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Esta cuenta de cobro fue generada automáticamente por el sistema de Catálogo Digital.');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(9);
        
        // Ajustar anchos de columna
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        
        $nombreArchivo = 'cuenta_cobro_' . str_pad($displayNumber, 6, '0', STR_PAD_LEFT) . '_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        $writer->save('php://output');
        exit;
    }
}
?>
