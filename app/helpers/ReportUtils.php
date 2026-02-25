<?php
/**
 * Helper de utilidades compartidas para reportes y cuentas de cobro
 */
class ReportUtils {
    
    /**
     * Colores corporativos disponibles
     */
    private static $colores = [
        'azul' => '#007bff',
        'verde' => '#28a745',
        'rojo' => '#dc3545',
        'morado' => '#6f42c1',
        'naranja' => '#fd7e14',
        'marino' => '#1f3b70',
        'grafito' => '#3a3f44',
        'petroleo' => '#0f4c5c',
        'acero' => '#4682b4',
        'gris' => '#6c757d'
    ];
    
    /**
     * Etiquetas de estado
     */
    private static $estados = [
        'en_pedido' => 'En Pedido',
        'alistado' => 'Alistado',
        'empaquetado' => 'Empaquetado',
        'verificado' => 'Verificado',
        'en_reparto' => 'En Reparto',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado'
    ];
    
    /**
     * Colores por estado para reportes
     */
    private static $coloresEstado = [
        'en_pedido' => [255, 152, 0],       // Naranja
        'alistado' => [76, 175, 80],        // Verde
        'empaquetado' => [33, 150, 243],    // Azul
        'verificado' => [76, 175, 80],      // Verde
        'en_reparto' => [156, 39, 176],     // Púrpura
        'entregado' => [0, 150, 0],         // Verde oscuro
        'cancelado' => [244, 67, 54]        // Rojo
    ];
    
    /**
     * Convertir color hexadecimal/nombre a RGB
     */
    public static function hexToRGB($color) {
        $hex = self::$colores[$color] ?? $color;
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) == 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    }
    
    /**
     * Obtener etiqueta legible del estado
     */
    public static function getEstadoLabel($estado) {
        return self::$estados[$estado] ?? ucfirst($estado);
    }
    
    /**
     * Obtener color RGB según estado
     */
    public static function getEstadoColor($estado) {
        return self::$coloresEstado[$estado] ?? [100, 100, 100];
    }
    
    /**
     * Convertir RGB a HEX
     */
    public static function rgbToHex($r, $g, $b) {
        return strtoupper(str_pad(dechex($r), 2, '0', STR_PAD_LEFT) .
                         str_pad(dechex($g), 2, '0', STR_PAD_LEFT) .
                         str_pad(dechex($b), 2, '0', STR_PAD_LEFT));
    }
}
?>
