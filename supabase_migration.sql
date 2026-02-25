-- ============================================================
-- MIGRACIÓN COMPLETA A SUPABASE (PostgreSQL)
-- Convertido desde catalogo_tienda.sql (MariaDB/MySQL)
-- Fecha: 2026-02-24
-- ============================================================
-- INSTRUCCIONES:
-- 1. Ir a tu proyecto en supabase.com → SQL Editor
-- 2. Ejecutar este script completo
-- ============================================================

-- ============================================================
-- TIPOS PERSONALIZADOS (ENUMS)
-- ============================================================

CREATE TYPE estado_pedido AS ENUM (
    'en_pedido', 'alistado', 'empaquetado', 'verificado',
    'en_reparto', 'entregado', 'cancelado'
);

CREATE TYPE estado_tenant AS ENUM ('activo', 'inactivo', 'bloqueado');

CREATE TYPE rol_usuario AS ENUM ('superadmin', 'admin', 'editor', 'viewer', 'usuario');

CREATE TYPE estado_preparacion AS ENUM ('pendiente', 'listo');

-- ============================================================
-- TABLA: tenants
-- ============================================================
CREATE TABLE tenants (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(150) NOT NULL,
    titulo_empresa VARCHAR(200),
    slug        VARCHAR(50) NOT NULL UNIQUE,
    whatsapp_phone VARCHAR(20) NOT NULL,
    logo        VARCHAR(255),
    tema        VARCHAR(50) DEFAULT 'default',
    tema_color  VARCHAR(50) DEFAULT 'azul',
    estado      estado_tenant DEFAULT 'activo',
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABLA: categorias
-- ============================================================
CREATE TABLE categorias (
    id          SERIAL PRIMARY KEY,
    tenant_id   INTEGER NOT NULL DEFAULT 1 REFERENCES tenants(id) ON DELETE CASCADE,
    nombre      VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activa      INTEGER DEFAULT 1,
    fecha_creacion      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    fecha_actualizacion TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (tenant_id, nombre)
);

-- ============================================================
-- TABLA: subcategorias
-- ============================================================
CREATE TABLE subcategorias (
    id          SERIAL PRIMARY KEY,
    tenant_id   INTEGER NOT NULL DEFAULT 1 REFERENCES tenants(id) ON DELETE CASCADE,
    categoria_id INTEGER NOT NULL REFERENCES categorias(id) ON DELETE CASCADE,
    nombre      VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activa      INTEGER DEFAULT 1,
    fecha_creacion      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    fecha_actualizacion TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (categoria_id, nombre)
);

-- ============================================================
-- TABLA: productos
-- ============================================================
CREATE TABLE productos (
    id              SERIAL PRIMARY KEY,
    numero_producto INTEGER NOT NULL DEFAULT 0,
    tenant_id       INTEGER NOT NULL DEFAULT 1 REFERENCES tenants(id) ON DELETE CASCADE,
    categoria_id    INTEGER NOT NULL REFERENCES categorias(id),
    subcategoria_id INTEGER NOT NULL REFERENCES subcategorias(id),
    nombre          VARCHAR(150) NOT NULL,
    descripcion     TEXT,
    precio          NUMERIC(10,2) NOT NULL,
    stock           INTEGER DEFAULT 0,
    imagen          VARCHAR(255),
    imagen2         VARCHAR(255),
    imagen3         VARCHAR(255),
    activo          INTEGER DEFAULT 1,
    fecha_creacion      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    fecha_actualizacion TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABLA: clientes
-- ============================================================
CREATE TABLE clientes (
    id              SERIAL PRIMARY KEY,
    tenant_id       INTEGER NOT NULL DEFAULT 1 REFERENCES tenants(id) ON DELETE CASCADE,
    usuario         VARCHAR(50),
    password        VARCHAR(255),
    nombre          VARCHAR(150) NOT NULL,
    email           VARCHAR(100) NOT NULL,
    telefono        VARCHAR(20) NOT NULL DEFAULT '',
    whatsapp        VARCHAR(20) NOT NULL DEFAULT '',
    ciudad          VARCHAR(100),
    direccion       TEXT,
    fecha_registro  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    activo          INTEGER DEFAULT 1,
    calificacion    INTEGER NOT NULL DEFAULT 0,
    UNIQUE (tenant_id, usuario)
);

-- ============================================================
-- TABLA: pedidos
-- ============================================================
CREATE TABLE pedidos (
    id              SERIAL PRIMARY KEY,
    numero_pedido   INTEGER NOT NULL DEFAULT 0,
    tenant_id       INTEGER NOT NULL DEFAULT 1 REFERENCES tenants(id) ON DELETE CASCADE,
    cliente_id      INTEGER NOT NULL REFERENCES clientes(id),
    estado          estado_pedido DEFAULT 'en_pedido',
    total           NUMERIC(10,2) NOT NULL,
    notas_cliente   TEXT,
    notas_admin     TEXT,
    whatsapp_enviado INTEGER DEFAULT 0,
    fecha_creacion      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    fecha_actualizacion TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    numero_cuenta_cobro INTEGER
);

-- ============================================================
-- TABLA: pedido_detalles
-- ============================================================
CREATE TABLE pedido_detalles (
    id                  SERIAL PRIMARY KEY,
    tenant_id           INTEGER NOT NULL DEFAULT 1 REFERENCES tenants(id) ON DELETE CASCADE,
    pedido_id           INTEGER NOT NULL REFERENCES pedidos(id) ON DELETE CASCADE,
    producto_id         INTEGER NOT NULL REFERENCES productos(id),
    cantidad            INTEGER NOT NULL,
    precio_unitario     NUMERIC(10,2) NOT NULL,
    subtotal            NUMERIC(10,2) NOT NULL,
    cantidad_entregada  INTEGER NOT NULL DEFAULT 0,
    estado_preparacion  VARCHAR(20) DEFAULT 'pendiente'
);

-- ============================================================
-- TABLA: pedido_historial
-- ============================================================
CREATE TABLE pedido_historial (
    id          SERIAL PRIMARY KEY,
    tenant_id   INTEGER NOT NULL DEFAULT 1 REFERENCES tenants(id) ON DELETE CASCADE,
    pedido_id   INTEGER NOT NULL REFERENCES pedidos(id) ON DELETE CASCADE,
    estado      estado_pedido,
    nota        TEXT,
    usuario_id  INTEGER,
    fecha       TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABLA: carrito
-- ============================================================
CREATE TABLE carrito (
    id              SERIAL PRIMARY KEY,
    tenant_id       INTEGER NOT NULL DEFAULT 1 REFERENCES tenants(id) ON DELETE CASCADE,
    session_id      VARCHAR(255) NOT NULL,
    producto_id     INTEGER NOT NULL REFERENCES productos(id) ON DELETE CASCADE,
    cantidad        INTEGER DEFAULT 1,
    fecha_creacion  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (tenant_id, session_id, producto_id)
);

-- ============================================================
-- TABLA: usuarios
-- ============================================================
CREATE TABLE usuarios (
    id              SERIAL PRIMARY KEY,
    tenant_id       INTEGER REFERENCES tenants(id) ON DELETE CASCADE,
    usuario         VARCHAR(50) NOT NULL,
    email           VARCHAR(100) NOT NULL,
    password        VARCHAR(255) NOT NULL,
    nombre          VARCHAR(100) NOT NULL,
    rol             rol_usuario NOT NULL DEFAULT 'usuario',
    activo          INTEGER DEFAULT 1,
    fecha_creacion  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    ultimo_acceso   TIMESTAMPTZ,
    UNIQUE (tenant_id, usuario),
    UNIQUE (tenant_id, email)
);

-- ============================================================
-- ÍNDICES
-- ============================================================
CREATE INDEX idx_tenant_categorias        ON categorias(tenant_id);
CREATE INDEX idx_tenant_subcategorias     ON subcategorias(tenant_id);
CREATE INDEX idx_tenant_productos         ON productos(tenant_id);
CREATE INDEX idx_categoria_productos      ON productos(categoria_id);
CREATE INDEX idx_subcategoria_productos   ON productos(subcategoria_id);
CREATE INDEX idx_activo_productos         ON productos(activo);
CREATE INDEX idx_tenant_clientes          ON clientes(tenant_id);
CREATE INDEX idx_email_clientes           ON clientes(email);
CREATE INDEX idx_whatsapp_clientes        ON clientes(whatsapp);
CREATE INDEX idx_tenant_pedidos           ON pedidos(tenant_id);
CREATE INDEX idx_estado_pedidos           ON pedidos(estado);
CREATE INDEX idx_fecha_pedidos            ON pedidos(fecha_creacion);
CREATE INDEX idx_tenant_pedido_detalles   ON pedido_detalles(tenant_id);
CREATE INDEX idx_pedido_detalles          ON pedido_detalles(pedido_id);
CREATE INDEX idx_tenant_pedido_historial  ON pedido_historial(tenant_id);
CREATE INDEX idx_historial_pedido         ON pedido_historial(pedido_id);
CREATE INDEX idx_historial_fecha          ON pedido_historial(fecha);
CREATE INDEX idx_tenant_carrito           ON carrito(tenant_id);
CREATE INDEX idx_tenant_usuarios          ON usuarios(tenant_id);
CREATE INDEX idx_usuario_usuarios         ON usuarios(usuario);

-- ============================================================
-- FUNCIÓN: actualizar updated_at/fecha_actualizacion automáticamente
-- ============================================================
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION update_fecha_actualizacion_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.fecha_actualizacion = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_tenants_updated_at
    BEFORE UPDATE ON tenants
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER trg_categorias_fecha_act
    BEFORE UPDATE ON categorias
    FOR EACH ROW EXECUTE FUNCTION update_fecha_actualizacion_column();

CREATE TRIGGER trg_subcategorias_fecha_act
    BEFORE UPDATE ON subcategorias
    FOR EACH ROW EXECUTE FUNCTION update_fecha_actualizacion_column();

CREATE TRIGGER trg_productos_fecha_act
    BEFORE UPDATE ON productos
    FOR EACH ROW EXECUTE FUNCTION update_fecha_actualizacion_column();

CREATE TRIGGER trg_pedidos_fecha_act
    BEFORE UPDATE ON pedidos
    FOR EACH ROW EXECUTE FUNCTION update_fecha_actualizacion_column();

-- ============================================================
-- DATOS: tenants
-- ============================================================
INSERT INTO tenants (id, nombre, titulo_empresa, slug, whatsapp_phone, logo, tema, tema_color, estado, created_at, updated_at) VALUES
(1, 'Tienda Default', 'Tienda Default', 'default', '+573112969569', NULL, 'claro', 'grafito', 'inactivo', '2026-01-09 20:44:02+00', '2026-01-16 02:18:41+00'),
(2, 'Mauricio', 'Mauricio', 'mauricio', '+57573112969569', NULL, 'oscuro', 'rojo', 'inactivo', '2026-01-09 21:11:21+00', '2026-01-16 02:18:48+00'),
(3, 'Distribuciones EBS', 'Distribuciones EBS', 'distribuciones-ebs', '+573004583117', NULL, 'claro', 'gris', 'inactivo', '2026-01-09 21:11:21+00', '2026-01-16 02:18:45+00'),
(4, 'Tech Store - Prueba', 'Tech Store - Prueba', 'tech-store', '+573334567890', NULL, 'oscuro', 'naranja', 'inactivo', '2026-01-09 22:53:09+00', '2026-01-16 02:18:38+00'),
(5, 'larause', 'larause', 'larause', '3112969569', NULL, 'oscuro', 'petroleo', 'activo', '2026-01-14 00:20:34+00', '2026-01-14 00:20:34+00'),
(6, 'la77', 'gramas', 'la77', '+573004583117', 'public/tenants/6/logo_1768532320.jpg', 'claro', 'gris', 'activo', '2026-01-14 00:25:47+00', '2026-01-16 02:58:40+00');

SELECT setval('tenants_id_seq', 7);

-- ============================================================
-- DATOS: categorias
-- ============================================================
INSERT INTO categorias (id, tenant_id, nombre, descripcion, activa, fecha_creacion, fecha_actualizacion) VALUES
(1, 1, 'Electrónica', 'Productos electrónicos de última tecnología', 1, '2025-12-24 17:35:46+00', '2025-12-24 17:35:46+00'),
(2, 1, 'Ropa', 'Prendas de vestir para hombre y mujer', 1, '2025-12-24 17:35:46+00', '2025-12-24 17:35:46+00'),
(3, 1, 'Hogar', 'Artículos y decoración para el hogar', 1, '2025-12-24 17:35:46+00', '2025-12-24 17:35:46+00'),
(4, 2, 'Servicios', 'Servicios de Mauricio', 1, '2026-01-09 21:28:47+00', '2026-01-09 21:28:47+00'),
(5, 3, 'Distribución', 'Productos de Distribuciones EBS', 1, '2026-01-09 21:28:47+00', '2026-01-09 21:28:47+00'),
(6, 4, 'Electrónica', 'Productos electrónicos y gadgets', 1, '2026-01-09 22:53:09+00', '2026-01-09 22:53:09+00'),
(7, 4, 'Ropa', 'Prendas de vestir y accesorios', 1, '2026-01-09 22:53:09+00', '2026-01-09 22:53:09+00'),
(8, 4, 'Hogar', 'Artículos y decoración para el hogar', 1, '2026-01-09 22:53:09+00', '2026-01-09 22:53:09+00'),
(9, 2, 'Electrónica', 'Productos electrónicos', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(10, 2, 'Ropa', 'Prendas de vestir', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(11, 2, 'Hogar', 'Artículos para el hogar', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(12, 3, 'Electrónica', 'Productos electrónicos', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(13, 3, 'Ropa', 'Prendas de vestir', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(14, 3, 'Hogar', 'Artículos para el hogar', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(15, 5, 'Electrónica', 'Productos electrónicos y gadgets', 1, '2026-01-14 00:20:34+00', '2026-01-14 00:20:34+00'),
(16, 5, 'Ropa', 'Prendas de vestir y accesorios', 1, '2026-01-14 00:20:34+00', '2026-01-14 00:20:34+00'),
(17, 5, 'Hogar', 'Artículos y decoración para el hogar', 1, '2026-01-14 00:20:34+00', '2026-01-14 00:20:34+00'),
(18, 6, 'Electrónica', 'Productos electrónicos y gadgets', 0, '2026-01-14 00:25:47+00', '2026-01-15 18:47:27+00'),
(19, 6, 'Ropa', 'Prendas de vestir y accesorios', 0, '2026-01-14 00:25:47+00', '2026-01-15 18:47:35+00'),
(20, 6, 'Hogar', 'Artículos y decoración para el hogar', 0, '2026-01-14 00:25:47+00', '2026-01-15 18:47:31+00'),
(21, 6, 'medicamentos', '', 1, '2026-01-14 20:28:29+00', '2026-01-14 20:28:29+00'),
(22, 6, 'Alfombras', '', 1, '2026-01-15 18:28:40+00', '2026-01-15 18:28:40+00'),
(23, 6, 'DEPORTIVAS', '', 1, '2026-01-16 02:13:43+00', '2026-01-16 02:13:43+00'),
(24, 6, 'CANCHAS', '', 1, '2026-01-16 03:18:53+00', '2026-01-16 03:18:53+00');

SELECT setval('categorias_id_seq', 25);

-- ============================================================
-- DATOS: subcategorias
-- ============================================================
INSERT INTO subcategorias (id, tenant_id, categoria_id, nombre, descripcion, activa, fecha_creacion, fecha_actualizacion) VALUES
(1, 1, 1, 'Smartphones', 'Teléfonos inteligentes de última generación', 1, '2025-12-24 17:35:46+00', '2025-12-24 17:35:46+00'),
(2, 1, 1, 'Laptops', 'Computadoras portátiles para trabajo y entretenimiento', 1, '2025-12-24 17:35:46+00', '2025-12-24 17:35:46+00'),
(3, 1, 2, 'Hombre', 'Ropa y accesorios para caballeros', 1, '2025-12-24 17:35:46+00', '2025-12-24 17:35:46+00'),
(4, 1, 2, 'Mujer', 'Ropa y accesorios para damas', 1, '2025-12-24 17:35:46+00', '2025-12-24 17:35:46+00'),
(5, 1, 3, 'Cocina', 'Electrodomésticos y utensilios de cocina', 1, '2025-12-24 17:35:46+00', '2025-12-24 17:35:46+00'),
(6, 1, 3, 'Dormitorio', 'Muebles y accesorios para dormitorio', 1, '2025-12-24 17:35:46+00', '2025-12-24 17:35:46+00'),
(7, 2, 4, 'Consultoría', NULL, 1, '2026-01-09 21:42:09+00', '2026-01-09 21:42:09+00'),
(8, 3, 5, 'Mayorista', NULL, 1, '2026-01-09 21:42:53+00', '2026-01-09 21:42:53+00'),
(9, 2, 4, 'Desarrollo', NULL, 1, '2026-01-09 22:31:51+00', '2026-01-09 22:31:51+00'),
(10, 3, 5, 'Electrónica', NULL, 1, '2026-01-09 22:31:51+00', '2026-01-09 22:31:51+00'),
(11, 4, 6, 'Smartphones', 'Teléfonos inteligentes', 1, '2026-01-09 22:53:09+00', '2026-01-09 22:53:09+00'),
(12, 4, 6, 'Laptops', 'Computadoras portátiles', 1, '2026-01-09 22:53:09+00', '2026-01-09 22:53:09+00'),
(13, 4, 7, 'Hombre', 'Ropa para caballeros', 1, '2026-01-09 22:53:09+00', '2026-01-09 22:53:09+00'),
(14, 4, 7, 'Mujer', 'Ropa para damas', 1, '2026-01-09 22:53:09+00', '2026-01-09 22:53:09+00'),
(15, 4, 8, 'Cocina', 'Electrodomésticos de cocina', 1, '2026-01-09 22:53:09+00', '2026-01-09 22:53:09+00'),
(16, 4, 8, 'Dormitorio', 'Muebles de dormitorio', 1, '2026-01-09 22:53:09+00', '2026-01-09 22:53:09+00'),
(17, 1, 1, 'Accesorios', 'Accesorios electrónicos', 1, '2026-01-11 17:59:51+00', '2026-01-11 17:59:51+00'),
(18, 1, 2, 'Hombres', 'Ropa para hombres', 1, '2026-01-11 17:59:51+00', '2026-01-11 17:59:51+00'),
(19, 1, 2, 'Mujeres', 'Ropa para mujeres', 1, '2026-01-11 17:59:51+00', '2026-01-11 17:59:51+00'),
(20, 1, 2, 'Niños', 'Ropa para niños', 1, '2026-01-11 17:59:51+00', '2026-01-11 17:59:51+00'),
(21, 1, 3, 'Muebles', 'Muebles para el hogar', 1, '2026-01-11 17:59:51+00', '2026-01-11 17:59:51+00'),
(22, 1, 3, 'Decoración', 'Artículos de decoración', 1, '2026-01-11 17:59:51+00', '2026-01-11 17:59:51+00'),
(23, 2, 9, 'Smartphones', 'Teléfonos móviles', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(24, 2, 9, 'Laptops', 'Computadoras portátiles', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(25, 2, 9, 'Accesorios', 'Accesorios electrónicos', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(26, 2, 10, 'Hombres', 'Ropa para hombres', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(27, 2, 10, 'Mujeres', 'Ropa para mujeres', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(28, 2, 10, 'Niños', 'Ropa para niños', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(29, 2, 11, 'Cocina', 'Artículos de cocina', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(30, 2, 11, 'Muebles', 'Muebles para el hogar', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(31, 2, 11, 'Decoración', 'Artículos de decoración', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(32, 3, 12, 'Smartphones', 'Teléfonos móviles', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(33, 3, 12, 'Laptops', 'Computadoras portátiles', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(34, 3, 12, 'Accesorios', 'Accesorios electrónicos', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(35, 3, 13, 'Hombres', 'Ropa para hombres', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(36, 3, 13, 'Mujeres', 'Ropa para mujeres', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(37, 3, 13, 'Niños', 'Ropa para niños', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(38, 3, 14, 'Cocina', 'Artículos de cocina', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(39, 3, 14, 'Muebles', 'Muebles para el hogar', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(40, 3, 14, 'Decoración', 'Artículos de decoración', 1, '2026-01-11 20:03:10+00', '2026-01-11 20:03:10+00'),
(41, 4, 6, 'Accesorios', 'Accesorios electrónicos', 1, '2026-01-11 20:03:11+00', '2026-01-11 20:03:11+00'),
(42, 4, 7, 'Hombres', 'Ropa para hombres', 1, '2026-01-11 20:03:11+00', '2026-01-11 20:03:11+00'),
(43, 4, 7, 'Mujeres', 'Ropa para mujeres', 1, '2026-01-11 20:03:11+00', '2026-01-11 20:03:11+00'),
(44, 4, 7, 'Niños', 'Ropa para niños', 1, '2026-01-11 20:03:11+00', '2026-01-11 20:03:11+00'),
(45, 4, 8, 'Muebles', 'Muebles para el hogar', 1, '2026-01-11 20:03:11+00', '2026-01-11 20:03:11+00'),
(46, 4, 8, 'Decoración', 'Artículos de decoración', 1, '2026-01-11 20:03:11+00', '2026-01-11 20:03:11+00'),
(47, 5, 15, 'Smartphones', 'Teléfonos inteligentes', 0, '2026-01-14 00:20:34+00', '2026-01-15 18:47:10+00'),
(48, 5, 15, 'Laptops', 'Computadoras portátiles', 1, '2026-01-14 00:20:34+00', '2026-01-14 00:20:34+00'),
(49, 5, 16, 'Hombre', 'Ropa para caballeros', 1, '2026-01-14 00:20:34+00', '2026-01-14 00:20:34+00'),
(50, 5, 16, 'Mujer', 'Ropa para damas', 1, '2026-01-14 00:20:34+00', '2026-01-14 00:20:34+00'),
(51, 5, 17, 'Cocina', 'Electrodomésticos de cocina', 0, '2026-01-14 00:20:34+00', '2026-01-15 18:47:14+00'),
(52, 5, 17, 'Dormitorio', 'Muebles de dormitorio', 1, '2026-01-14 00:20:34+00', '2026-01-14 00:20:34+00'),
(53, 6, 18, 'Smartphones', 'Teléfonos inteligentes', 0, '2026-01-14 00:25:47+00', '2026-01-15 18:47:45+00'),
(54, 6, 18, 'Laptops', 'Computadoras portátiles', 0, '2026-01-14 00:25:47+00', '2026-01-15 18:47:42+00'),
(55, 6, 19, 'Hombre', 'Ropa para caballeros', 0, '2026-01-14 00:25:47+00', '2026-01-15 18:47:54+00'),
(56, 6, 19, 'Mujer', 'Ropa para damas', 0, '2026-01-14 00:25:47+00', '2026-01-15 18:47:56+00'),
(57, 6, 20, 'Cocina', 'Electrodomésticos de cocina', 0, '2026-01-14 00:25:47+00', '2026-01-15 18:47:48+00'),
(58, 6, 20, 'Dormitorio', 'Muebles de dormitorio', 0, '2026-01-14 00:25:47+00', '2026-01-15 18:47:51+00'),
(59, 6, 21, 'genericos', '', 1, '2026-01-14 20:28:54+00', '2026-01-14 20:28:54+00'),
(60, 6, 22, 'Canchas', '', 1, '2026-01-15 18:30:37+00', '2026-01-15 18:30:37+00'),
(61, 6, 22, 'Padel', '', 1, '2026-01-15 18:30:57+00', '2026-01-15 18:30:57+00'),
(62, 6, 23, 'ECORATIVAS', '', 1, '2026-01-16 02:14:22+00', '2026-01-16 02:14:22+00'),
(63, 6, 23, 'JARDINES VERTICALES', '', 1, '2026-01-16 02:14:52+00', '2026-01-16 02:14:52+00'),
(64, 6, 24, 'Canchas Múltiples', '', 1, '2026-01-16 03:19:25+00', '2026-01-16 03:19:25+00');

SELECT setval('subcategorias_id_seq', 65);

-- ============================================================
-- DATOS: usuarios
-- ============================================================
INSERT INTO usuarios (id, tenant_id, usuario, email, password, nombre, rol, activo, fecha_creacion, ultimo_acceso) VALUES
(1, 1, 'admin', 'admin@tienda.local', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'Administrador', 'admin', 1, '2025-12-24 17:35:46+00', '2026-01-19 13:10:35+00'),
(2, 4, 'admin_tech', 'admin@techstore.local', 'e937eb7cc144efab9efc6ea003bf88b027a3d7061d283365fe23f1f660740994', 'Tech Store - Prueba', 'admin', 1, '2026-01-09 22:53:09+00', '2026-01-13 23:20:46+00'),
(3, NULL, 'superadmin', 'superadmin@sistema.local', 'd357150517d3e65ae84985f7b705ad99fdc38372a22ecea0cecaf8aaf820a249', 'Super Administrador', 'superadmin', 1, '2026-01-09 23:49:15+00', '2026-01-16 16:01:40+00'),
(4, 5, 'larause', 'larause@gmail.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'larause', 'admin', 1, '2026-01-14 00:20:34+00', NULL),
(5, 6, 'admin', '3marin7@gmail.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'la77', 'admin', 1, '2026-01-14 00:25:47+00', '2026-01-14 22:35:49+00');

SELECT setval('usuarios_id_seq', 6);

-- ============================================================
-- NOTA: Los datos de productos, clientes, pedidos, pedido_detalles,
-- pedido_historial y carrito son muy extensos (400+ productos,
-- 60+ clientes, 99+ pedidos). Se omiten aquí para brevedad.
-- Para importarlos, usar el script supabase_data_completa.sql
-- o importar directamente desde tu backup.
-- ============================================================
