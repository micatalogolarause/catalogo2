-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-12-2025 a las 17:42:14
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `catalogo_tienda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activa` int(11) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `activa`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Electrónica', 'Productos electrónicos de última tecnología', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(2, 'Ropa', 'Prendas de vestir para hombre y mujer', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(3, 'Hogar', 'Artículos y decoración para el hogar', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `usuario`, `password`, `nombre`, `email`, `telefono`, `whatsapp`, `ciudad`, `direccion`, `fecha_registro`, `activo`) VALUES
(1, NULL, NULL, 'eddd', 'admin@colsanitas.com', '3112969569', '+57311296596', 'Suba', '140a76 Carrera 108a', '2025-12-24 18:32:48', 1),
(2, 'usuario1', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c', 'Juan García', 'usuario1@tienda.local', '3001234567', '573001234567', 'Bogotá', 'Calle 10 #20-30', '2025-12-24 19:10:28', 1),
(3, 'usuario2', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c', 'María López', 'usuario2@tienda.local', '3009876543', '573009876543', 'Medellín', 'Carrera 45 #50-60', '2025-12-24 19:10:28', 1),
(4, NULL, NULL, 'Mauricio', 'admin@colsanitas.com', '3112969569', '+57311296596', 'Suba', '140a76 Carrera 108a', '2025-12-24 19:59:24', 1),
(5, NULL, NULL, 'Mauricio', 'mauriciolarause@gmail.com', '3112969569', '+573112969569', 'Suba', '140a76 Carrera 108a', '2025-12-25 00:34:50', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `estado` enum('pendiente','procesando','enviado','entregado','cancelado') DEFAULT 'pendiente',
  `total` decimal(10,2) NOT NULL,
  `notas_cliente` text DEFAULT NULL,
  `notas_admin` text DEFAULT NULL,
  `whatsapp_enviado` int(11) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente_id`, `estado`, `total`, `notas_cliente`, `notas_admin`, `whatsapp_enviado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 'pendiente', 999.99, '', NULL, 0, '2025-12-24 18:32:48', '2025-12-24 18:32:48'),
(2, 4, 'pendiente', 5699.94, '', NULL, 0, '2025-12-24 19:59:24', '2025-12-24 19:59:24'),
(3, 5, 'pendiente', 1899.98, '', NULL, 0, '2025-12-25 00:34:50', '2025-12-25 00:34:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalles`
--

CREATE TABLE `pedido_detalles` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedido_detalles`
--

INSERT INTO `pedido_detalles` (`id`, `pedido_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 1, 1, 999.99, 999.99),
(2, 2, 1, 3, 999.99, 2999.97),
(3, 2, 2, 3, 899.99, 2699.97),
(4, 3, 1, 1, 999.99, 999.99),
(5, 3, 2, 1, 899.99, 899.99);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `subcategoria_id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` int(11) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `categoria_id`, `subcategoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 1, 'iPhone 15 Pro', 'Último modelo de Apple con chip A17 Pro y cámara avanzada.', 999.99, 50, 'producto_694c375e793ca.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 18:56:30'),
(2, 1, 1, 'Samsung Galaxy S24', 'Teléfono Android con pantalla AMOLED y procesador Snapdragon.', 899.99, 45, 'samsung_s24.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(3, 1, 2, 'MacBook Pro 16', 'Laptop de alta rendimiento con chip M3 Max para profesionales.', 2499.99, 20, 'macbook_pro.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(4, 1, 2, 'Dell XPS 15', 'Computadora portátil con procesador Intel y pantalla 4K.', 1799.99, 25, 'dell_xps.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(5, 2, 3, 'Camiseta Premium Hombre', 'Camiseta de algodón 100% de alta calidad para hombre.', 49.99, 100, 'camiseta_hombre.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(6, 2, 3, 'Pantalón Casual Hombre', 'Pantalón casual de tela resistente, perfecto para uso diario.', 79.99, 80, 'pantalon_hombre.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(7, 2, 4, 'Vestido Casual Mujer', 'Vestido elegante y cómodo para cualquier ocasión casual.', 89.99, 60, 'vestido_mujer.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(8, 2, 4, 'Jeans Premium Mujer', 'Jeans de marca reconocida, cómodos y de excelente calidad.', 99.99, 75, 'jeans_mujer.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(9, 3, 5, 'Horno Eléctrico', 'Horno eléctrico con múltiples funciones para cocinar.', 299.99, 15, 'horno_electrico.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(10, 3, 6, 'Juego de Cama King', 'Juego de sábanas y almohadas tamaño king de algodón.', 199.99, 30, 'juego_cama.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategorias`
--

CREATE TABLE `subcategorias` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activa` int(11) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `subcategorias`
--

INSERT INTO `subcategorias` (`id`, `categoria_id`, `nombre`, `descripcion`, `activa`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 'Smartphones', 'Teléfonos inteligentes de última generación', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(2, 1, 'Laptops', 'Computadoras portátiles para trabajo y entretenimiento', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(3, 2, 'Hombre', 'Ropa y accesorios para caballeros', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(4, 2, 'Mujer', 'Ropa y accesorios para damas', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(5, 3, 'Cocina', 'Electrodomésticos y utensilios de cocina', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(6, 3, 'Dormitorio', 'Muebles y accesorios para dormitorio', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `rol` enum('admin','usuario') DEFAULT 'usuario',
  `activo` int(11) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `email`, `password`, `nombre`, `rol`, `activo`, `fecha_creacion`, `ultimo_acceso`) VALUES
(1, 'admin', 'admin@tienda.local', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'Administrador', 'admin', 1, '2025-12-24 17:35:46', '2025-12-24 18:38:31');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_carrito` (`session_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_whatsapp` (`whatsapp`),
  ADD KEY `idx_usuario` (`usuario`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha` (`fecha_creacion`);

--
-- Indices de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `idx_pedido` (`pedido_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_subcategoria` (`subcategoria_id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subcategoria` (`categoria_id`,`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

--
-- Filtros para la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD CONSTRAINT `pedido_detalles_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`subcategoria_id`) REFERENCES `subcategorias` (`id`);

--
-- Filtros para la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  ADD CONSTRAINT `subcategorias_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
