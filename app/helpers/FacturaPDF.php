<?php
/**
 * Generador de Cuentas de Cobro en PDF usando TCPDF
 */
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/ReportUtils.php';

class FacturaPDF {
    
    /**
     * Generar factura en PDF
     * 
     * @param array $pedido Datos del pedido
     * @param array $detalles Items del pedido
     * @param array $tenant Datos del tenant
     * @return string Ruta del archivo PDF generado
     */
    public static function generar($pedido, $detalles, $tenant) {
        // Crear instancia de TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        
        // Configuración del documento
        $pdf->SetCreator('Catálogo Digital');
        $pdf->SetAuthor($tenant['nombre']);
        $displayNumber = $pedido['numero_cuenta_cobro'] ?? $pedido['numero_pedido'] ?? $pedido['id'];
        $pdf->SetTitle('Cuenta de Cobro #' . $displayNumber);
        $pdf->SetSubject('Cuenta de Cobro de Pedido');
        
        // Quitar header y footer por defecto
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Configurar márgenes
        $pdf->SetMargins(15, 15, 15);
        // margen izquierdo fijo (usado para calcular anchos y evitar warnings)
        $leftMargin = 15;
        $pdf->SetAutoPageBreak(true, 15);
        
        // Agregar página
        $pdf->AddPage();
        
        // Colores corporativos
        $colorPrimario = ReportUtils::hexToRGB($tenant['tema_color'] ?? 'azul');
        
        // ENCABEZADO
        $pdf->SetFillColor($colorPrimario[0], $colorPrimario[1], $colorPrimario[2]);
        $pdf->Rect(0, 0, 210, 40, 'F');

        // (Usamos un área fija más abajo para el logo; evitar dibujar dos veces)

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->SetXY(15, 12);
        $pdf->Cell(0, 10, $tenant['titulo_empresa'] ?? $tenant['nombre'], 0, 1, 'L');

                    // Reservar un área fija a la derecha para el logo y centrar la imagen dentro de esa área
                    $logoBounds = null; // [x, y, w, h]
                    $logoAreaWidth = 45; // mm
                    $logoAreaMarginRight = 15; // mm
                    $logoAreaX = 210 - $logoAreaMarginRight - $logoAreaWidth; // inicio del área del logo
                    $logoAreaY = 6;
                    $logoAreaH = 28;

                    if (!empty($tenant['logo']) && is_file(APP_ROOT . '/' . $tenant['logo'])) {
                        $logoPath = APP_ROOT . '/' . $tenant['logo'];
                        try {
                            $info = @getimagesize($logoPath);
                            if ($info) {
                                $origW = $info[0];
                                $origH = $info[1];
                                // Calcular tamaño manteniendo proporción y con padding dentro del área
                                $padding = 4; // mm
                                $maxW = max(10, $logoAreaWidth - $padding);
                                $maxH = max(8, $logoAreaH - $padding);
                                $ratio = $origW > 0 ? ($origH / $origW) : 1;
                                $w = $maxW;
                                $h = $w * $ratio;
                                if ($h > $maxH) {
                                    $h = $maxH;
                                    $w = $h / $ratio;
                                }
                                // Centrar dentro del área
                                $x = $logoAreaX + (($logoAreaWidth - $w) / 2);
                                $y = $logoAreaY + (($logoAreaH - $h) / 2);
                                
                                // Detectar tipo de imagen y formato
                                $imageType = $info[2] ?? null;
                                $type = '';
                                
                                // Manejar diferentes tipos de imagen
                                if ($imageType === IMAGETYPE_JPEG || $imageType === IMAGETYPE_JPEG2000) {
                                    $type = 'JPG';
                                } elseif ($imageType === IMAGETYPE_PNG) {
                                    $type = 'PNG';
                                } elseif ($imageType === IMAGETYPE_GIF) {
                                    $type = 'GIF';
                                } elseif ($imageType === IMAGETYPE_WEBP) {
                                    $type = 'WEBP';
                                }
                                
                                // Intentar agregar imagen con manejo de errores para PNG con alfa
                                try {
                                    $pdf->Image($logoPath, $x, $y, $w, $h, $type, '', '', false, 300, '', false, false, 0, false, false, false);
                                } catch (Exception $imgEx) {
                                    // Si falla (ej: PNG con alfa sin GD/Imagick), intentar sin especificar tipo
                                    try {
                                        $pdf->Image($logoPath, $x, $y, $w, $h, '', '', '', false, 300, '', false, false, 0, false, false, false);
                                    } catch (Exception $imgEx2) {
                                        // Silenciar error si no se puede cargar la imagen
                                    }
                                }
                                $logoBounds = array($logoAreaX, $logoAreaY, $logoAreaWidth, $logoAreaH);
                            }
                        } catch (Exception $e) {
                            // Ignorar si hay problema con la imagen
                        }
                    }

                    // Dibujar título derecho (CUENTA DE COBRO) y número justo a la izquierda del área del logo
                    $displayNumber = $pedido['numero_cuenta_cobro'] ?? $pedido['numero_pedido'] ?? $pedido['id'];
                    if ($logoBounds) {
                        $pdf->SetTextColor(255, 255, 255);
                        // Texto principal
                        $titleFontSize = 20;
                        $pdf->SetFont('helvetica', 'B', $titleFontSize);
                        $headerText = 'CUENTA DE COBRO';
                        $headerRight = $logoBounds[0] - 6; // 6mm de separación del área del logo
                        $wHeader = $pdf->GetStringWidth($headerText, 'helvetica', 'B', $titleFontSize);
                        $headerX = max($leftMargin, $headerRight - $wHeader);
                        $pdf->SetXY($headerX, 8);
                        $pdf->Cell($wHeader, 10, $headerText, 0, 1, 'L', false);

                        // Número debajo, alineado a la derecha del header
                        $numFontSize = 11;
                        $pdf->SetFont('helvetica', '', $numFontSize);
                        $numText = 'No. ' . str_pad($displayNumber, 6, '0', STR_PAD_LEFT);
                        $wNum = $pdf->GetStringWidth($numText, 'helvetica', '', $numFontSize);
                        $numX = $headerRight - $wNum;
                        $pdf->SetXY($numX, 20);
                        $pdf->Cell($wNum, 6, $numText, 0, 1, 'L', false);
                    }
        
        $estadoLabel = ReportUtils::getEstadoLabel($pedido['estado']);
        $pdf->SetTextColor($colorPrimario[0], $colorPrimario[1], $colorPrimario[2]);
        $pdf->Cell(0, 5, 'Estado: ' . $estadoLabel, 0, 1, 'L');
                    if ($logoBounds) {
                        $logoX = $logoBounds[0];
                        $availableWidth = max(50, $logoX - $leftMargin - 6); // dejar al menos 50mm
                        $pdf->Cell($availableWidth, 10, $tenant['titulo_empresa'] ?? $tenant['nombre'], 0, 1, 'L');
                    } else {
                        $pdf->Cell(0, 10, $tenant['titulo_empresa'] ?? $tenant['nombre'], 0, 1, 'L');
                    }
        
        // DATOS DEL CLIENTE
        $pdf->Ln(8);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, 'DATOS DEL CLIENTE', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        
        $pdf->Cell(0, 5, 'Nombre: ' . $pedido['nombre'], 0, 1, 'L');
        if (!empty($pedido['email'])) {
            $pdf->Cell(0, 5, 'Email: ' . $pedido['email'], 0, 1, 'L');
        }
        $pdf->Cell(0, 5, 'WhatsApp: ' . $pedido['whatsapp'], 0, 1, 'L');
        
        // Fecha de generación de la factura
        $fechaGeneracion = new DateTime();
        $fechaGeneracion->setTimezone(new DateTimeZone('America/Bogota'));
        $pdf->Cell(0, 5, 'Fecha Cuenta de Cobro: ' . $fechaGeneracion->format('d/m/Y h:i A'), 0, 1, 'L');
        
        if (!empty($pedido['ciudad'])) {
            $pdf->Cell(0, 5, 'Ciudad: ' . $pedido['ciudad'], 0, 1, 'L');
        }
        if (!empty($pedido['direccion'])) {
            $pdf->Cell(0, 5, 'Dirección: ' . $pedido['direccion'], 0, 1, 'L');
        }
        
        // TABLA DE PRODUCTOS
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, 'DETALLE DE PRODUCTOS', 0, 1, 'L');
        
        // Encabezado de tabla
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(90, 7, 'Producto', 1, 0, 'L', true);
        $pdf->Cell(25, 7, 'Precio Unit.', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Cant.', 1, 0, 'C', true);
        $pdf->Cell(45, 7, 'Subtotal', 1, 1, 'R', true);
        
        // Filas de productos - SOLO LO ENTREGADO (usa cantidad_entregada si existe)
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(255, 255, 255);
        
        $totalReal = 0;
        $productosNoEntregados = array();
        $productosParciales = array();
        
        foreach ($detalles as $item) {
            $cant = isset($item['cantidad']) ? (int)$item['cantidad'] : 0;
            $cantEnt = isset($item['cantidad_entregada']) ? (int)$item['cantidad_entregada'] : ((isset($item['estado_preparacion']) && $item['estado_preparacion'] === 'listo') ? $cant : 0);
            if ($cantEnt > $cant) { $cantEnt = $cant; }
            if ($cantEnt > 0) {
                $pdf->Cell(90, 6, $item['nombre'], 1, 0, 'L');
                $pdf->Cell(25, 6, '$' . number_format($item['precio_unitario'], 0), 1, 0, 'C');
                $pdf->Cell(20, 6, $cantEnt, 1, 0, 'C');
                $subtotal = $item['precio_unitario'] * $cantEnt;
                $pdf->Cell(45, 6, '$' . number_format($subtotal, 0), 1, 1, 'R');
                $totalReal += $subtotal;
                if ($cantEnt < $cant) {
                    $productosParciales[] = $item['nombre'] . ' (enviados ' . $cantEnt . ' de ' . $cant . ')';
                }
            } else {
                $productosNoEntregados[] = $item['nombre'];
            }
        }
        
        // Mostrar nota si hay productos no entregados o parciales
        if (!empty($productosParciales) || !empty($productosNoEntregados)) {
            $pdf->Ln(2);
            $pdf->SetFont('helvetica', 'I', 8);
            $pdf->SetTextColor(200, 0, 0);
            if (!empty($productosParciales)) {
                    $pdf->MultiCell(0, 4, '⚠️ Productos parciales (se incluye en la cuenta de cobro lo enviado): ' . implode(', ', $productosParciales), 0, 'L');
            }
            if (!empty($productosNoEntregados)) {
                $pdf->MultiCell(0, 4, '⚠️ Productos NO incluidos (no entregados): ' . implode(', ', $productosNoEntregados), 0, 'L');
            }
            $pdf->SetTextColor(0, 0, 0);
        }
        
        // TOTAL - usar el total calculado de productos entregados
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(135, 8, '', 0, 0, 'L');
        $pdf->Cell(45, 8, 'TOTAL: $' . number_format($totalReal, 0), 1, 1, 'R', true);
        
        // PIE DE PÁGINA
        $pdf->Ln(15);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->MultiCell(0, 4, 
            "Gracias por su compra.\n" .
            "Esta cuenta de cobro fue generada automáticamente por el sistema de Catálogo Digital.\n" .
            "Para cualquier consulta, contáctenos por WhatsApp: " . $tenant['whatsapp_phone'],
            0, 'C');
        
        // Guardar PDF
        $dir = APP_ROOT . '/public/facturas';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        
        $nombreArchivo = 'cuenta_cobro_' . str_pad($displayNumber, 6, '0', STR_PAD_LEFT) . '_' . date('Ymd_His') . '.pdf';
        $rutaCompleta = $dir . '/' . $nombreArchivo;
        
        $pdf->Output($rutaCompleta, 'F');
        
        return APP_URL . '/public/facturas/' . $nombreArchivo;
    }
}
?>
