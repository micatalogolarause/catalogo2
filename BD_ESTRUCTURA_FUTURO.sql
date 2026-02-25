-- ========================================
-- BACKUP Y REFERENCIA DE ESTRUCTURA
-- Base de datos: catalogo_tienda
-- Fecha: 2026-01-09
-- Versión: Multi-Tenancy Phase 1 Completed
-- ========================================

-- INSTRUCCIONES PARA FUTURO:
-- 
-- 1. SI NECESITAS RESTAURAR LA BD COMPLETA:
--    mysql -u root catalogo_tienda < catalogo_tienda_backup_multitenancy.sql
--
-- 2. SI NECESITAS EMPEZAR DE CERO:
--    Ejecuta primero este script, luego:
--    FASE_1_MIGRACION_COMPLETA.sql
--
-- 3. ARQUIVOS GENERADOS EN ESTA SESSION:
--    - catalogo_tienda_backup_multitenancy.sql (Full backup con multi-tenancy)
--    - FASE_1_MIGRACION_MULTITENANCY.sql (Script original de migración)
--    - FASE_1_MIGRACION_COMPLETA.sql (Script completo con manejo de errores)
--    - BD_ESTRUCTURA_FUTURO.sql (Este archivo - referencia)
--
-- ========================================

-- ESTRUCTURA DE TABLAS CON MULTI-TENANCY:

-- 1. TABLA: tenants (Nueva - Gestión de inquilinos)
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nombre` varchar(150) NOT NULL,
  `slug` varchar(50) NOT NULL UNIQUE,
  `whatsapp_phone` varchar(20) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `tema` varchar(50) DEFAULT 'default',
  `estado` enum('activo','inactivo','bloqueado') DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABLAS CON TENANT_ID AGREGADO (9 tablas):
-- - categorias (tenant_id int, FK a tenants)
-- - subcategorias (tenant_id int, FK a tenants)
-- - productos (tenant_id int, FK a tenants)
-- - clientes (tenant_id int, FK a tenants)
-- - carrito (tenant_id int, FK a tenants)
-- - pedidos (tenant_id int, FK a tenants)
-- - pedido_detalles (tenant_id int, FK a tenants)
-- - usuarios (tenant_id int, FK a tenants)
-- - pedido_historial (tenant_id int, FK a tenants)

-- ========================================
-- PATRON DE QUERIES PARA MULTI-TENANCY
-- ========================================

-- EJEMPLO 1: Obtener categorías de un tenant específico
-- SELECT * FROM categorias WHERE tenant_id = 1 AND activa = 1;

-- EJEMPLO 2: Obtener productos de un tenant con categoría
-- SELECT p.* FROM productos p 
-- JOIN categorias c ON p.categoria_id = c.id AND p.tenant_id = c.tenant_id
-- WHERE p.tenant_id = 1 AND p.activo = 1;

-- EJEMPLO 3: Obtener pedidos y detalles de un tenant
-- SELECT p.*, pd.* FROM pedidos p
-- JOIN pedido_detalles pd ON p.id = pd.pedido_id AND p.tenant_id = pd.tenant_id
-- WHERE p.tenant_id = 1;

-- EJEMPLO 4: Insertar con tenant_id (IMPORTANTE: SIEMPRE INCLUIR)
-- INSERT INTO categorias (tenant_id, nombre, descripcion, activa)
-- VALUES (1, 'Nueva Categoría', 'Descripción', 1);

-- ========================================
-- INFORMACIÓN DE TENANTS ACTUALES
-- ========================================

-- Tenant Default (ID=1):
-- - Nombre: Tienda Default
-- - Slug: default
-- - WhatsApp: 573112969569
-- - Estado: activo
-- - Contiene todos los datos existentes del catálogo original

-- PROXIMOS TENANTS A CREAR:
-- 1. Tenant: Mauricio
--    Slug: mauricio
--    Estado: (crear en Fase 5 - Tenant Registration)
--
-- 2. Tenant: Distribuciones EBS
--    Slug: distribuciones-ebs
--    Estado: (crear en Fase 5 - Tenant Registration)

-- ========================================
-- CHECKLIST DE IMPLEMENTACIÓN MULTI-TENANCY
-- ========================================

-- COMPLETADO:
-- [X] Fase 1: Crear tabla tenants
-- [X] Fase 1: Agregar tenant_id a 9 tablas
-- [X] Fase 1: Crear índices y FKs
-- [X] Fase 1: Insertar tenant default (id=1)

-- PRÓXIMAS FASES:
-- [ ] Fase 2: Middleware de resolución de tenant en index.php
-- [ ] Fase 3: IIS rewrite rules en web.config
-- [ ] Fase 4: Database helpers scoped por tenant
-- [ ] Fase 5: Formulario de registro/provisioning de tenants
-- [ ] Fase 6: Aislar todos los controllers por tenant_id
-- [ ] Fase 7: Crear estructura de carpetas /public/tenants/{tenant_id}
-- [ ] Fase 8: WhatsApp por tenant (número y formato personalizado)
-- [ ] Fase 9: Admin panel validación de tenant
-- [ ] Fase 10: Testing y validación de aislamiento

-- ========================================
-- RUTAS Y URLS ESPERADAS (FUTURO)
-- ========================================

-- Tenant Default (actual):
-- http://localhost/catalogo2
-- http://localhost/catalogo2/tienda
-- http://localhost/catalogo2/tienda/productos

-- Tenant Mauricio (futuro):
-- http://localhost/catalogo2/mauricio
-- http://localhost/catalogo2/mauricio/tienda
-- http://localhost/catalogo2/mauricio/tienda/productos

-- Tenant Distribuciones EBS (futuro):
-- http://localhost/catalogo2/distribuciones-ebs
-- http://localhost/catalogo2/distribuciones-ebs/tienda
-- http://localhost/catalogo2/distribuciones-ebs/tienda/productos

-- ========================================
-- ARCHIVOS A MODIFICAR EN PRÓXIMAS FASES
-- ========================================

-- FASE 2 - PHP Middleware:
-- public/index.php - Detectar tenant desde URL

-- FASE 3 - IIS Routing:
-- public/web.config - Rewrite rules para URLs con slug

-- FASE 4 - Database Helpers:
-- config/database.php - Funciones scoped por tenant_id

-- FASE 5 - Tenant Registration:
-- public/registro-tenant.php - Formulario de registro
-- app/controllers/tenantController.php - Lógica de provisioning

-- FASE 6 - Controller Isolation:
-- app/controllers/tiendaController.php - Agregar tenant_id a queries
-- app/controllers/adminController.php - Validar tenant en constructor
-- Todos los demás controllers

-- FASE 7 - Upload Folders:
-- /public/tenants/{tenant_id}/images/
-- /public/tenants/{tenant_id}/logo/

-- FASE 8 - WhatsApp por Tenant:
-- app/models/Tenants.php - Obtener whatsapp_phone
-- app/controllers/tiendaController.php - Usar whatsapp del tenant

-- ========================================
-- DATOS INICIALES (TENANT DEFAULT = 1)
-- ========================================

-- Categorías: 3 (Electrónica, Ropa, Hogar)
-- Productos: 10 (iPhone, Samsung, MacBook, Dell, Camisetas, Pantalones, Vestidos, Jeans, Horno, Juego Cama)
-- Clientes: 5
-- Pedidos: 3
-- Usuarios (Admins): 1 (admin)

-- ========================================
-- IMPORTANTE PARA FUTURO
-- ========================================

-- 1. SIEMPRE incluir "WHERE tenant_id = {TENANT_ID}" en SELECT
-- 2. SIEMPRE incluir "tenant_id = {TENANT_ID}" en INSERT
-- 3. SIEMPRE incluir "AND tenant_id = {TENANT_ID}" en UPDATE
-- 4. SIEMPRE incluir "AND tenant_id = {TENANT_ID}" en DELETE
-- 5. NUNCA hacer queries sin el filtro de tenant_id
-- 6. SIEMPRE validar que el usuario pertenece al tenant antes de acceder
-- 7. SIEMPRE usar índices compuestos (tenant_id, column) para performance

-- ========================================
-- FIN DE REFERENCIA
-- ========================================
