-- Actualización de la base de datos para soporte de empaquetado y verificación

-- Agregar columna estado_preparacion si no existe
ALTER TABLE `pedido_detalles` 
ADD COLUMN IF NOT EXISTS `estado_preparacion` ENUM('pendiente', 'listo') DEFAULT 'pendiente';

-- Actualizar el enum de estados en la tabla pedidos para incluir empaquetado y verificado
ALTER TABLE `pedidos` 
MODIFY COLUMN `estado` ENUM('en_pedido', 'alistado', 'empaquetado', 'verificado', 'en_reparto', 'entregado', 'cancelado') DEFAULT 'en_pedido';

-- Actualizar el enum de estados en la tabla pedido_historial
ALTER TABLE `pedido_historial` 
MODIFY COLUMN `estado` ENUM('en_pedido', 'alistado', 'empaquetado', 'verificado', 'en_reparto', 'entregado', 'cancelado');
