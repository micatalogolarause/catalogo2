-- ========================================
-- FASE 1: MIGRACIÓN A MULTI-TENANCY
-- Base de datos: catalogo_tienda
-- ========================================

-- 1. Crear tabla TENANTS
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `slug` varchar(50) NOT NULL UNIQUE,
  `whatsapp_phone` varchar(20) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `tema` varchar(50) DEFAULT 'default',
  `estado` enum('activo','inactivo','bloqueado') DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Insertar tenant DEFAULT para datos existentes
INSERT INTO `tenants` (`id`, `nombre`, `slug`, `whatsapp_phone`, `logo`, `tema`, `estado`, `created_at`, `updated_at`)
VALUES (1, 'Tienda Default', 'default', '573112969569', NULL, 'default', 'activo', NOW(), NOW());

-- 3. Agregar columna tenant_id a CATEGORIAS
ALTER TABLE `categorias` 
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `categorias`
ADD CONSTRAINT `fk_categorias_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `categorias`
ADD KEY `idx_tenant_categorias` (`tenant_id`);

-- Hacer tenant_id parte del índice único de nombre
ALTER TABLE `categorias`
DROP INDEX `nombre`,
ADD UNIQUE KEY `uk_tenant_categoria_nombre` (`tenant_id`, `nombre`);

-- 4. Agregar columna tenant_id a SUBCATEGORIAS
ALTER TABLE `subcategorias`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

-- Primero remover la FK que referencia a subcategorias
ALTER TABLE `productos` DROP FOREIGN KEY `productos_ibfk_2`;

-- Ahora remover el índice único
ALTER TABLE `subcategorias`
DROP INDEX `unique_subcategoria`;

-- Agregar el nuevo índice único con tenant_id
ALTER TABLE `subcategorias`
ADD UNIQUE KEY `uk_tenant_subcat_nombre` (`tenant_id`, `categoria_id`, `nombre`);

-- Recrear la FK en productos
ALTER TABLE `productos`
ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`subcategoria_id`) REFERENCES `subcategorias` (`id`);

-- Agregar tenant FK y índices
ALTER TABLE `subcategorias`
ADD CONSTRAINT `fk_subcategorias_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `subcategorias`
ADD KEY `idx_tenant_subcategorias` (`tenant_id`);

-- 5. Agregar columna tenant_id a PRODUCTOS
ALTER TABLE `productos`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `productos`
ADD CONSTRAINT `fk_productos_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `productos`
ADD KEY `idx_tenant_productos` (`tenant_id`);

-- 6. Agregar columna tenant_id a CLIENTES
ALTER TABLE `clientes`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `clientes`
ADD CONSTRAINT `fk_clientes_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `clientes`
ADD KEY `idx_tenant_clientes` (`tenant_id`);

-- Hacer tenant_id parte de índices únicos
ALTER TABLE `clientes`
DROP INDEX `usuario`,
ADD UNIQUE KEY `uk_tenant_cliente_usuario` (`tenant_id`, `usuario`);

-- 7. Agregar columna tenant_id a CARRITO
ALTER TABLE `carrito`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `carrito`
ADD CONSTRAINT `fk_carrito_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `carrito`
ADD KEY `idx_tenant_carrito` (`tenant_id`);

-- Rehacer índice único con tenant_id
ALTER TABLE `carrito`
DROP INDEX `unique_carrito`,
ADD UNIQUE KEY `uk_tenant_carrito` (`tenant_id`, `session_id`, `producto_id`);

-- 8. Agregar columna tenant_id a PEDIDOS
ALTER TABLE `pedidos`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `pedidos`
ADD CONSTRAINT `fk_pedidos_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `pedidos`
ADD KEY `idx_tenant_pedidos` (`tenant_id`);

-- 9. Agregar columna tenant_id a PEDIDO_DETALLES
ALTER TABLE `pedido_detalles`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `pedido_detalles`
ADD CONSTRAINT `fk_pedido_detalles_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `pedido_detalles`
ADD KEY `idx_tenant_pedido_detalles` (`tenant_id`);

-- 10. Agregar columna tenant_id a USUARIOS (tabla de administradores)
ALTER TABLE `usuarios`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `usuarios`
ADD CONSTRAINT `fk_usuarios_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `usuarios`
ADD KEY `idx_tenant_usuarios` (`tenant_id`);

-- Hacer tenant_id parte de índices únicos
ALTER TABLE `usuarios`
DROP INDEX `usuario`,
DROP INDEX `email`,
ADD UNIQUE KEY `uk_tenant_usuario` (`tenant_id`, `usuario`),
ADD UNIQUE KEY `uk_tenant_email` (`tenant_id`, `email`);

-- ========================================
-- VERIFICACIÓN: Mostrar estructura de tabla tenants
-- ========================================
DESCRIBE `tenants`;

-- ========================================
-- Confirmación de migración completada
-- ========================================
SELECT '✓ MIGRACIÓN COMPLETADA' as estado,
       COUNT(*) as total_tenants,
       (SELECT COUNT(*) FROM `categorias`) as total_categorias,
       (SELECT COUNT(*) FROM `productos`) as total_productos,
       (SELECT COUNT(*) FROM `clientes`) as total_clientes,
       (SELECT COUNT(*) FROM `pedidos`) as total_pedidos
FROM `tenants`;
