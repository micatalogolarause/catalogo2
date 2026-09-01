-- ========================================
-- FASE 1: MIGRACIÓN A MULTI-TENANCY
-- PARTE 2: Continuar desde donde paró
-- ========================================

-- Verificar estado actual
SELECT 'Verificando tabla categorias...' as paso;
ALTER TABLE `categorias` 
DROP INDEX `uk_tenant_categoria_nombre`;

ALTER TABLE `categorias`
ADD UNIQUE KEY `uk_tenant_categoria_nombre` (`tenant_id`, `nombre`);

-- 5. Agregar columna tenant_id a PRODUCTOS (si no existe)
SELECT 'Agregando tenant_id a productos...' as paso;
ALTER TABLE `productos`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `productos`
ADD CONSTRAINT `fk_productos_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `productos`
ADD KEY `idx_tenant_productos` (`tenant_id`);

-- 6. Agregar columna tenant_id a CLIENTES (si no existe)
SELECT 'Agregando tenant_id a clientes...' as paso;
ALTER TABLE `clientes`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `clientes`
ADD CONSTRAINT `fk_clientes_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `clientes`
ADD KEY `idx_tenant_clientes` (`tenant_id`);

-- Hacer tenant_id parte de índices únicos
ALTER TABLE `clientes`
DROP INDEX `usuario`;

ALTER TABLE `clientes`
ADD UNIQUE KEY `uk_tenant_cliente_usuario` (`tenant_id`, `usuario`);

-- 7. Agregar columna tenant_id a CARRITO (si no existe)
SELECT 'Agregando tenant_id a carrito...' as paso;
ALTER TABLE `carrito`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `carrito`
ADD CONSTRAINT `fk_carrito_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `carrito`
ADD KEY `idx_tenant_carrito` (`tenant_id`);

-- Rehacer índice único con tenant_id
ALTER TABLE `carrito`
DROP INDEX `unique_carrito`;

ALTER TABLE `carrito`
ADD UNIQUE KEY `uk_tenant_carrito` (`tenant_id`, `session_id`, `producto_id`);

-- 8. Agregar columna tenant_id a PEDIDOS (si no existe)
SELECT 'Agregando tenant_id a pedidos...' as paso;
ALTER TABLE `pedidos`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `pedidos`
ADD CONSTRAINT `fk_pedidos_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `pedidos`
ADD KEY `idx_tenant_pedidos` (`tenant_id`);

-- 9. Agregar columna tenant_id a PEDIDO_DETALLES (si no existe)
SELECT 'Agregando tenant_id a pedido_detalles...' as paso;
ALTER TABLE `pedido_detalles`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `pedido_detalles`
ADD CONSTRAINT `fk_pedido_detalles_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `pedido_detalles`
ADD KEY `idx_tenant_pedido_detalles` (`tenant_id`);

-- 10. Agregar columna tenant_id a USUARIOS (si no existe)
SELECT 'Agregando tenant_id a usuarios...' as paso;
ALTER TABLE `usuarios`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `usuarios`
ADD CONSTRAINT `fk_usuarios_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `usuarios`
ADD KEY `idx_tenant_usuarios` (`tenant_id`);

-- Hacer tenant_id parte de índices únicos
ALTER TABLE `usuarios`
DROP INDEX `usuario`;

ALTER TABLE `usuarios`
DROP INDEX `email`;

ALTER TABLE `usuarios`
ADD UNIQUE KEY `uk_tenant_usuario` (`tenant_id`, `usuario`);

ALTER TABLE `usuarios`
ADD UNIQUE KEY `uk_tenant_email` (`tenant_id`, `email`);

-- Si existe la tabla pedido_historial, agregarle tenant_id
SELECT 'Verificando pedido_historial...' as paso;
ALTER TABLE `pedido_historial`
ADD COLUMN `tenant_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;

ALTER TABLE `pedido_historial`
ADD CONSTRAINT `fk_pedido_historial_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

ALTER TABLE `pedido_historial`
ADD KEY `idx_tenant_pedido_historial` (`tenant_id`);

-- ========================================
-- RESUMEN DE MIGRACIÓN
-- ========================================
SELECT '✓ MIGRACIÓN COMPLETADA' as estado;

SELECT 'Total de tablas con tenant_id:' as info;
SELECT TABLE_NAME, COUNT(*) as columnas_totales
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'catalogo_tienda' 
AND TABLE_NAME IN ('tenants', 'categorias', 'subcategorias', 'productos', 'clientes', 'carrito', 'pedidos', 'pedido_detalles', 'usuarios', 'pedido_historial')
GROUP BY TABLE_NAME
ORDER BY TABLE_NAME;

SELECT 'Datos en tenants:' as info;
SELECT * FROM tenants;
