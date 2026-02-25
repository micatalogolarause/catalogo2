-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-01-2026 a las 02:58:04
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
  `tenant_id` int(11) NOT NULL DEFAULT 1,
  `session_id` varchar(255) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carrito`
--

INSERT INTO `carrito` (`id`, `tenant_id`, `session_id`, `producto_id`, `cantidad`, `fecha_creacion`) VALUES
(8, 1, 'cku34qijds2lvvl3eshj4k8cmq', 1, 1, '2026-01-05 22:54:03'),
(9, 1, 'cku34qijds2lvvl3eshj4k8cmq', 2, 1, '2026-01-05 22:54:05'),
(11, 1, 'fe96lpto95lvghqo5nosq9i9so', 11, 8, '2026-01-08 15:09:15'),
(21, 1, 'u9enplp6a807mha6nfjbd0crgs', 11, 72, '2026-01-08 17:02:29'),
(28, 4, '2mc3gpg6903sbc126jgk5l25fc', 29, 1, '2026-01-10 19:39:28'),
(31, 2, '2mc3gpg6903sbc126jgk5l25fc', 15, 1, '2026-01-10 20:02:50'),
(32, 2, '2mc3gpg6903sbc126jgk5l25fc', 13, 1, '2026-01-10 20:02:58'),
(33, 1, '2mc3gpg6903sbc126jgk5l25fc', 39, 2, '2026-01-12 00:12:58'),
(34, 1, '2mc3gpg6903sbc126jgk5l25fc', 40, 1, '2026-01-12 00:13:09'),
(36, 2, '0hm6kchob18f2pjeirjaqdr3qh', 128, 2, '2026-01-13 16:35:34'),
(37, 2, '0hm6kchob18f2pjeirjaqdr3qh', 129, 1, '2026-01-13 16:36:13'),
(38, 2, '0hm6kchob18f2pjeirjaqdr3qh', 130, 1, '2026-01-13 16:36:26'),
(98, 2, 'tpn1p3eocci45j49qiue4fhd6h', 398, 1, '2026-01-15 01:57:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL DEFAULT 1,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activa` int(11) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `tenant_id`, `nombre`, `descripcion`, `activa`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 'Electrónica', 'Productos electrónicos de última tecnología', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(2, 1, 'Ropa', 'Prendas de vestir para hombre y mujer', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(3, 1, 'Hogar', 'Artículos y decoración para el hogar', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(4, 2, 'Servicios', 'Servicios de Mauricio', 1, '2026-01-09 21:28:47', '2026-01-09 21:28:47'),
(5, 3, 'Distribuci¾n', 'Productos de Distribuciones EBS', 1, '2026-01-09 21:28:47', '2026-01-09 21:28:47'),
(6, 4, 'Electrónica', 'Productos electrónicos y gadgets', 1, '2026-01-09 22:53:09', '2026-01-09 22:53:09'),
(7, 4, 'Ropa', 'Prendas de vestir y accesorios', 1, '2026-01-09 22:53:09', '2026-01-09 22:53:09'),
(8, 4, 'Hogar', 'Artículos y decoración para el hogar', 1, '2026-01-09 22:53:09', '2026-01-09 22:53:09'),
(9, 2, 'Electrónica', 'Productos electrónicos', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(10, 2, 'Ropa', 'Prendas de vestir', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(11, 2, 'Hogar', 'Artículos para el hogar', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(12, 3, 'Electrónica', 'Productos electrónicos', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(13, 3, 'Ropa', 'Prendas de vestir', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(14, 3, 'Hogar', 'Artículos para el hogar', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(15, 5, 'Electrónica', 'Productos electrónicos y gadgets', 1, '2026-01-14 00:20:34', '2026-01-14 00:20:34'),
(16, 5, 'Ropa', 'Prendas de vestir y accesorios', 1, '2026-01-14 00:20:34', '2026-01-14 00:20:34'),
(17, 5, 'Hogar', 'Artículos y decoración para el hogar', 1, '2026-01-14 00:20:34', '2026-01-14 00:20:34'),
(18, 6, 'Electrónica', 'Productos electrónicos y gadgets', 1, '2026-01-14 00:25:47', '2026-01-14 00:25:47'),
(19, 6, 'Ropa', 'Prendas de vestir y accesorios', 1, '2026-01-14 00:25:47', '2026-01-14 00:25:47'),
(20, 6, 'Hogar', 'Artículos y decoración para el hogar', 1, '2026-01-14 00:25:47', '2026-01-14 00:25:47'),
(21, 6, 'medicamentos', '', 1, '2026-01-14 20:28:29', '2026-01-14 20:28:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL DEFAULT 1,
  `usuario` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` int(11) DEFAULT 1,
  `calificacion` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `tenant_id`, `usuario`, `password`, `nombre`, `email`, `telefono`, `whatsapp`, `ciudad`, `direccion`, `fecha_registro`, `activo`, `calificacion`) VALUES
(1, 1, NULL, NULL, 'eddd', 'admin@colsanitas.com', '3112969569', '+57311296596', 'Suba', '140a76 Carrera 108a', '2025-12-24 18:32:48', 1, 4),
(2, 1, 'usuario1', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c', 'Mauricio', 'admin@colsanitas.com', '3112969569', '+57311296596', 'Suba', '140a76 Carrera 108a', '2025-12-24 19:10:28', 1, 5),
(3, 1, 'usuario2', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c', 'María López', 'usuario2@tienda.local', '3009876543', '573009876543', 'Medellín', 'Carrera 45 #50-60', '2025-12-24 19:10:28', 1, 3),
(4, 1, NULL, NULL, 'Mauricio', 'admin@colsanitas.com', '3112969569', '+57311296596', 'Suba', '140a76 Carrera 108a', '2025-12-24 19:59:24', 1, 4),
(5, 1, NULL, NULL, 'Mauricio', 'mauriciolarause@gmail.com', '3112969569', '+573112969569', 'Suba', '140a76 Carrera 108a', '2025-12-25 00:34:50', 1, 4),
(6, 1, NULL, NULL, 'sdf', 'mauriciolarause@gmail.com', '311223', '+57311296596', 'Bogotá', 'Cra. 108a #140a-76', '2026-01-05 22:51:37', 1, 4),
(7, 1, 'invitado_8f2949ea1e', 'a143fcd6a92eb5af42dd4fed4f86b19f19f27849851bb2b5d567070d5fd97578', 'Efeff', '', '', '+573004583117', NULL, NULL, '2026-01-08 16:58:57', 1, 0),
(8, 2, NULL, NULL, 'Carlos Mendez', 'carlos@mauricio.local', '3115555555', '+573115555555', 'Bogotß', 'Cll 50 # 10-20', '2026-01-09 22:35:53', 1, 0),
(9, 2, NULL, NULL, 'Ana Gonzßlez', 'ana@mauricio.local', '3116666666', '+573116666666', 'Bogotß', 'Cll 80 # 15-30', '2026-01-09 22:35:53', 1, 2),
(10, 3, NULL, NULL, 'Distribuciones XYZ', 'info@distxyz.com', '3117777777', '+573117777777', 'MedellÝn', 'Cra 45 # 50-60', '2026-01-09 22:36:04', 1, 0),
(11, 3, NULL, NULL, 'Comercial ABC', 'ventas@comercialab.com', '3118888888', '+573118888888', 'MedellÝn', 'Cra 50 # 60-70', '2026-01-09 22:36:04', 1, 1),
(12, 1, NULL, NULL, 'Cliente Prueba 1', 'cliente0@test.com', '', '5738905744', NULL, 'Dirección 1, Apto 423', '2026-01-11 20:03:09', 1, 0),
(13, 1, NULL, NULL, 'Cliente Prueba 2', 'cliente1@test.com', '', '5737146005', NULL, 'Dirección 2, Apto 11', '2026-01-11 20:03:09', 1, 0),
(14, 1, NULL, NULL, 'Cliente Prueba 3', 'cliente2@test.com', '', '5730526354', NULL, 'Dirección 3, Apto 484', '2026-01-11 20:03:09', 1, 0),
(15, 1, NULL, NULL, 'Cliente Prueba 4', 'cliente3@test.com', '', '5737827828', NULL, 'Dirección 4, Apto 292', '2026-01-11 20:03:09', 1, 4),
(16, 1, NULL, NULL, 'Cliente Prueba 5', 'cliente4@test.com', '', '5731792418', NULL, 'Dirección 5, Apto 388', '2026-01-11 20:03:09', 1, 0),
(17, 1, NULL, NULL, 'Cliente Prueba 6', 'cliente5@test.com', '', '5736135748', NULL, 'Dirección 6, Apto 189', '2026-01-11 20:03:09', 1, 0),
(18, 1, NULL, NULL, 'Cliente Prueba 7', 'cliente6@test.com', '', '5731642480', NULL, 'Dirección 7, Apto 416', '2026-01-11 20:03:09', 1, 5),
(19, 1, NULL, NULL, 'Cliente Prueba 8', 'cliente7@test.com', '', '5731528556', NULL, 'Dirección 8, Apto 24', '2026-01-11 20:03:09', 1, 0),
(20, 1, NULL, NULL, 'Cliente Prueba 9', 'cliente8@test.com', '', '5737009233', NULL, 'Dirección 9, Apto 188', '2026-01-11 20:03:09', 1, 0),
(21, 1, NULL, NULL, '1', 'Cliente Prueba 10', 'cliente9@test.com', '', '5730805067', '', '2026-01-11 20:03:10', 1, 0),
(22, 2, NULL, NULL, 'Cliente Prueba 1', 'cliente0@test.com', '', '5733802814', NULL, 'Dirección 1, Apto 421', '2026-01-11 20:03:10', 1, 1),
(23, 2, NULL, NULL, 'Cliente Prueba 2', 'cliente1@test.com', '', '5730199585', '', 'Dirección 2, Apto 8', '2026-01-11 20:03:10', 1, 4),
(24, 2, NULL, NULL, 'Cliente Prueba 3', 'cliente2@test.com', '', '5734929459', NULL, 'Dirección 3, Apto 484', '2026-01-11 20:03:10', 1, 0),
(25, 2, NULL, NULL, 'Cliente Prueba 4', 'cliente3@test.com', '', '5733501796', NULL, 'Dirección 4, Apto 10', '2026-01-11 20:03:10', 1, 4),
(26, 2, NULL, NULL, 'Cliente Prueba 5', 'cliente4@test.com', '', '5734903718', '', 'Dirección 5, Apto 297', '2026-01-11 20:03:10', 1, 4),
(27, 2, NULL, NULL, 'Cliente Prueba 6', 'cliente5@test.com', '', '5739280657', NULL, 'Dirección 6, Apto 438', '2026-01-11 20:03:10', 1, 0),
(28, 2, NULL, NULL, 'Cliente Prueba 7', 'cliente6@test.com', '', '5737685776', NULL, 'Dirección 7, Apto 164', '2026-01-11 20:03:10', 1, 5),
(29, 2, NULL, NULL, 'Cliente Prueba 8', 'cliente7@test.com', '', '5738097993', NULL, 'Dirección 8, Apto 460', '2026-01-11 20:03:10', 1, 0),
(30, 2, NULL, NULL, 'Cliente Prueba 9', 'cliente8@test.com', '', '5739587901', NULL, 'Dirección 9, Apto 56', '2026-01-11 20:03:10', 1, 4),
(31, 2, NULL, NULL, 'Cliente Prueba 10', 'cliente9@test.com', '', '5733887854', NULL, 'Dirección 10, Apto 386', '2026-01-11 20:03:10', 1, 0),
(32, 3, NULL, NULL, 'Cliente Prueba 1', 'cliente0@test.com', '', '5732819337', NULL, 'Dirección 1, Apto 482', '2026-01-11 20:03:11', 1, 0),
(33, 3, NULL, NULL, 'Cliente Prueba 2', 'cliente1@test.com', '', '5730928853', NULL, 'Dirección 2, Apto 394', '2026-01-11 20:03:11', 1, 0),
(34, 3, NULL, NULL, 'Cliente Prueba 3', 'cliente2@test.com', '', '5732701970', NULL, 'Dirección 3, Apto 350', '2026-01-11 20:03:11', 1, 2),
(35, 3, NULL, NULL, 'Cliente Prueba 4', 'cliente3@test.com', '', '5732073055', NULL, 'Dirección 4, Apto 45', '2026-01-11 20:03:11', 1, 4),
(36, 3, NULL, NULL, 'Cliente Prueba 5', 'cliente4@test.com', '', '5733888093', NULL, 'Dirección 5, Apto 235', '2026-01-11 20:03:11', 1, 0),
(37, 3, NULL, NULL, 'Cliente Prueba 6', 'cliente5@test.com', '', '5737663266', NULL, 'Dirección 6, Apto 32', '2026-01-11 20:03:11', 1, 4),
(38, 3, NULL, NULL, 'Cliente Prueba 7', 'cliente6@test.com', '', '5730188955', NULL, 'Dirección 7, Apto 385', '2026-01-11 20:03:11', 1, 0),
(39, 3, NULL, NULL, 'Cliente Prueba 8', 'cliente7@test.com', '', '5736682163', NULL, 'Dirección 8, Apto 274', '2026-01-11 20:03:11', 1, 0),
(40, 3, NULL, NULL, 'Cliente Prueba 9', 'cliente8@test.com', '', '5731915344', NULL, 'Dirección 9, Apto 74', '2026-01-11 20:03:11', 1, 4),
(41, 3, NULL, NULL, 'Cliente Prueba 10', 'cliente9@test.com', '', '5738022752', NULL, 'Dirección 10, Apto 293', '2026-01-11 20:03:11', 1, 0),
(42, 4, NULL, NULL, 'Cliente Prueba 1', 'cliente0@test.com', '', '5739171614', NULL, 'Dirección 1, Apto 175', '2026-01-11 20:03:11', 1, 5),
(43, 4, NULL, NULL, 'Cliente Prueba 2', 'cliente1@test.com', '', '5737093803', NULL, 'Dirección 2, Apto 123', '2026-01-11 20:03:11', 1, 5),
(44, 4, NULL, NULL, 'Cliente Prueba 3', 'cliente2@test.com', '', '5736549490', NULL, 'Dirección 3, Apto 399', '2026-01-11 20:03:11', 1, 5),
(45, 4, NULL, NULL, 'Cliente Prueba 4', 'cliente3@test.com', '', '5731316419', NULL, 'Dirección 4, Apto 266', '2026-01-11 20:03:12', 1, 1),
(46, 4, NULL, NULL, 'Cliente Prueba 5', 'cliente4@test.com', '', '5733646749', NULL, 'Dirección 5, Apto 453', '2026-01-11 20:03:12', 1, 0),
(47, 4, NULL, NULL, 'Cliente Prueba 6', 'cliente5@test.com', '', '5735485563', NULL, 'Dirección 6, Apto 7', '2026-01-11 20:03:12', 1, 0),
(48, 4, NULL, NULL, 'Cliente Prueba 7', 'cliente6@test.com', '', '5734572489', NULL, 'Dirección 7, Apto 105', '2026-01-11 20:03:12', 1, 0),
(49, 4, NULL, NULL, 'Cliente Prueba 8', 'cliente7@test.com', '', '5739095445', NULL, 'Dirección 8, Apto 50', '2026-01-11 20:03:12', 1, 2),
(50, 4, NULL, NULL, 'Cliente Prueba 9', 'cliente8@test.com', '', '5734296705', NULL, 'Dirección 9, Apto 333', '2026-01-11 20:03:12', 1, 0),
(51, 4, NULL, NULL, 'Cliente Prueba 10', 'cliente9@test.com', '', '5737661855', NULL, 'Dirección 10, Apto 219', '2026-01-11 20:03:12', 1, 3),
(52, 2, 'invitado_0cfaef7f75', '9727b643b6e53d678c0022bb555f8e80a1b71ed66dcfeb54381575b6d9594b3a', 'edddwin', '', '', '+573004583117', 'bogota', '', '2026-01-13 16:35:54', 1, 4),
(53, 3, 'invitado_6d468ae738', 'b027d170275edc9b4985c45e4d71bde5b29026c8d696671d1135d81328e00f61', 'Mauricio', '', '', '+573112969569', NULL, NULL, '2026-01-13 23:16:42', 1, 4),
(54, 2, 'invitado_fd33352db5', 'f37f3b1b32224de8ce077f7602bc552a829ed6aaf894f8c627031d62efc1ba62', 'rfweegete', '', '', '', '+573112969569', '', '2026-01-13 23:22:09', 1, 5),
(55, 6, 'invitado_f2aea403ec', '791089bad6657c4cfcd594b8daf7040c4108c50dba3a954c91bff3d3da13bfbc', 'edwwww', '', '', '+573004583117', NULL, NULL, '2026-01-14 22:34:41', 1, 0),
(56, 6, 'invitado_3f9b6ea119', '142db32e7aa9d80bf663ead6b81b421863528f0884acd2c54419d561c7db363c', 'juan', '', '', '+573004583116', '', '', '2026-01-15 00:10:29', 1, 3),
(57, 6, 'invitado_8df63f7cb4', 'efe3cdffd9ca9ea43c3c88cdb628747a9f0ae18069dcbb681add24d40c4999fd', 'qndres perez', '', '', '+573114591024', NULL, NULL, '2026-01-15 00:16:36', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `numero_pedido` int(11) NOT NULL DEFAULT 0,
  `tenant_id` int(11) NOT NULL DEFAULT 1,
  `cliente_id` int(11) NOT NULL,
  `estado` enum('en_pedido','alistado','empaquetado','verificado','en_reparto','entregado','cancelado') DEFAULT 'en_pedido',
  `total` decimal(10,2) NOT NULL,
  `notas_cliente` text DEFAULT NULL,
  `notas_admin` text DEFAULT NULL,
  `whatsapp_enviado` int(11) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `numero_cuenta_cobro` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `numero_pedido`, `tenant_id`, `cliente_id`, `estado`, `total`, `notas_cliente`, `notas_admin`, `whatsapp_enviado`, `fecha_creacion`, `fecha_actualizacion`, `numero_cuenta_cobro`) VALUES
(1, 1, 1, 1, 'en_pedido', 999.99, '', NULL, 0, '2025-12-24 18:32:48', '2026-01-14 23:33:58', 1),
(2, 2, 1, 4, 'empaquetado', 5699.94, '', NULL, 0, '2025-12-24 19:59:24', '2026-01-14 23:33:58', 2),
(3, 3, 1, 5, 'en_reparto', 1899.98, '', NULL, 0, '2025-12-25 00:34:50', '2026-01-14 23:33:58', 3),
(4, 4, 1, 6, 'entregado', 1899.98, '', '', 0, '2026-01-05 22:51:37', '2026-01-14 23:33:58', 4),
(5, 5, 1, 2, 'en_reparto', 100000.00, '', '', 0, '2026-01-07 18:19:42', '2026-01-14 23:33:58', 5),
(6, 6, 1, 7, 'entregado', 1022499.82, '', '', 0, '2026-01-08 16:58:57', '2026-01-14 23:33:58', 6),
(7, 7, 1, 5, 'en_pedido', 6917899.50, '', NULL, 0, '2026-01-09 04:19:35', '2026-01-14 23:33:58', 7),
(8, 8, 1, 5, 'en_pedido', 2400000.00, '', NULL, 0, '2026-01-09 04:26:56', '2026-01-14 23:33:58', 8),
(9, 9, 1, 5, 'en_pedido', 100999.99, '', NULL, 0, '2026-01-09 04:46:14', '2026-01-14 23:33:58', 9),
(10, 1, 2, 8, '', 2150.00, NULL, NULL, 0, '2026-01-09 22:36:22', '2026-01-14 23:33:58', 1),
(11, 1, 3, 10, '', 1700.00, NULL, NULL, 0, '2026-01-09 22:36:22', '2026-01-14 23:33:58', 1),
(12, 10, 1, 12, 'en_pedido', 2212.98, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 10),
(13, 11, 1, 13, 'en_pedido', 109399.94, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 11),
(14, 12, 1, 14, 'en_pedido', 3296.95, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 12),
(15, 13, 1, 15, 'en_pedido', 15984.91, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 13),
(16, 14, 1, 16, 'en_pedido', 6189.96, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 14),
(17, 15, 1, 17, 'en_pedido', 7936.91, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 15),
(18, 16, 1, 18, 'en_pedido', 300767.98, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 16),
(19, 17, 1, 19, 'en_pedido', 4039.95, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 17),
(20, 18, 1, 20, 'en_pedido', 3244.93, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 18),
(21, 19, 1, 21, 'en_pedido', 204499.97, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 19),
(22, 2, 2, 22, 'en_pedido', 7445.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 2),
(23, 3, 2, 23, 'en_pedido', 3500.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 3),
(24, 4, 2, 24, 'en_pedido', 1261.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 4),
(25, 5, 2, 25, 'en_pedido', 1839.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 5),
(26, 6, 2, 26, 'en_pedido', 2712.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 6),
(27, 7, 2, 27, 'en_pedido', 7757.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 7),
(28, 8, 2, 28, 'en_pedido', 2496.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 8),
(29, 9, 2, 29, 'en_pedido', 2524.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 9),
(30, 10, 2, 30, 'en_pedido', 2952.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 10),
(31, 11, 2, 31, 'en_pedido', 1198.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-14 23:33:58', 11),
(32, 2, 3, 32, 'en_pedido', 2721.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-14 23:33:58', 2),
(33, 3, 3, 33, 'en_pedido', 2699.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-14 23:33:58', 3),
(34, 4, 3, 34, 'en_pedido', 4225.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-14 23:33:58', 4),
(35, 5, 3, 35, 'en_pedido', 1330.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-14 23:33:58', 5),
(36, 6, 3, 36, 'en_pedido', 977.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-14 23:33:58', 6),
(37, 7, 3, 37, 'en_pedido', 800.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-14 23:33:58', 7),
(38, 8, 3, 38, 'en_pedido', 3069.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-14 23:33:58', 8),
(39, 9, 3, 39, 'en_pedido', 3316.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-14 23:33:58', 9),
(40, 10, 3, 40, 'en_pedido', 3587.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-14 23:33:58', 10),
(41, 11, 3, 41, 'en_pedido', 1598.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-14 23:33:58', 11),
(42, 1, 4, 42, 'en_pedido', 798.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-14 23:33:58', 1),
(43, 2, 4, 43, 'en_pedido', 1269.92, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-14 23:33:58', 2),
(44, 3, 4, 44, 'en_pedido', 1382.96, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-14 23:33:58', 3),
(45, 4, 4, 45, 'en_pedido', 2182.94, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-14 23:33:58', 4),
(46, 5, 4, 46, 'en_pedido', 650.97, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-14 23:33:58', 5),
(47, 6, 4, 47, 'en_pedido', 6899.97, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-14 23:33:58', 6),
(48, 7, 4, 48, 'en_pedido', 1764.95, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-14 23:33:58', 7),
(49, 8, 4, 49, 'en_pedido', 5403.95, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-14 23:33:58', 8),
(50, 9, 4, 50, 'en_pedido', 7445.92, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-14 23:33:58', 9),
(51, 10, 4, 51, 'en_pedido', 2641.90, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-14 23:33:58', 10),
(52, 12, 2, 52, 'en_pedido', 956.00, '', NULL, 0, '2026-01-13 16:35:54', '2026-01-14 23:33:58', 12),
(53, 20, 1, 5, 'en_pedido', 524.00, '', NULL, 0, '2026-01-13 18:20:32', '2026-01-14 23:33:58', 20),
(54, 21, 1, 5, 'en_pedido', 524.00, '', NULL, 0, '2026-01-13 18:20:35', '2026-01-14 23:33:58', 21),
(55, 22, 1, 5, 'en_pedido', 524.00, '', NULL, 0, '2026-01-13 18:27:15', '2026-01-14 23:33:58', 22),
(56, 13, 2, 52, 'en_pedido', 21588.00, '', NULL, 0, '2026-01-13 18:45:39', '2026-01-14 23:33:58', 13),
(57, 14, 2, 52, 'en_pedido', 450.00, '', NULL, 0, '2026-01-13 19:43:59', '2026-01-14 23:33:58', 14),
(58, 15, 2, 52, 'empaquetado', 993.00, '', NULL, 0, '2026-01-13 21:55:49', '2026-01-14 23:33:58', 15),
(59, 16, 2, 52, 'en_reparto', 3241.00, '', '', 0, '2026-01-13 21:58:50', '2026-01-14 23:33:58', 16),
(60, 17, 2, 52, 'verificado', 478.00, '', NULL, 0, '2026-01-13 22:27:04', '2026-01-14 23:33:58', 17),
(61, 18, 2, 52, 'empaquetado', 13079.00, '', NULL, 0, '2026-01-13 22:37:42', '2026-01-14 23:33:58', 18),
(62, 19, 2, 52, 'verificado', 465.00, '', NULL, 0, '2026-01-13 22:39:36', '2026-01-14 23:33:58', 19),
(63, 20, 2, 52, 'empaquetado', 9780.00, '', NULL, 0, '2026-01-13 22:48:37', '2026-01-14 23:33:58', 20),
(64, 21, 2, 52, 'verificado', 16440.00, '', NULL, 0, '2026-01-13 23:02:50', '2026-01-14 23:33:58', 21),
(65, 12, 3, 53, 'empaquetado', 55.00, '', NULL, 0, '2026-01-13 23:16:42', '2026-01-14 23:33:58', 12),
(66, 13, 3, 53, 'en_pedido', 55.00, '', NULL, 0, '2026-01-13 23:17:30', '2026-01-14 23:33:58', 13),
(67, 14, 3, 53, 'en_pedido', 55.00, '', NULL, 0, '2026-01-13 23:18:09', '2026-01-14 23:33:58', 14),
(68, 22, 2, 52, 'empaquetado', 1512000.00, '', NULL, 0, '2026-01-13 23:19:00', '2026-01-14 23:33:58', 22),
(69, 15, 3, 53, 'en_pedido', 283.00, '', NULL, 0, '2026-01-13 23:19:13', '2026-01-14 23:33:58', 15),
(70, 23, 1, 5, 'empaquetado', 5133.00, '', NULL, 0, '2026-01-13 23:19:52', '2026-01-14 23:33:58', 23),
(71, 23, 2, 54, 'en_reparto', 21870.00, '', NULL, 0, '2026-01-13 23:22:09', '2026-01-14 23:33:58', 23),
(72, 24, 2, 52, 'en_pedido', 759906.00, '', NULL, 0, '2026-01-14 18:40:02', '2026-01-14 23:33:58', 24),
(73, 25, 2, 52, 'en_pedido', 3456.00, '', NULL, 0, '2026-01-14 18:40:28', '2026-01-14 23:33:58', 25),
(74, 26, 2, 52, 'en_pedido', 21000.00, '', NULL, 0, '2026-01-14 18:41:00', '2026-01-14 23:33:58', 26),
(75, 27, 2, 52, 'entregado', 756000.00, '', '', 0, '2026-01-14 18:43:20', '2026-01-14 23:33:58', 27),
(76, 28, 2, 52, 'alistado', 2767.00, '', NULL, 0, '2026-01-14 18:49:12', '2026-01-14 23:33:58', 28),
(77, 1, 6, 55, 'entregado', 2100000.00, '', '', 0, '2026-01-14 22:34:41', '2026-01-14 23:33:58', 1),
(78, 2, 6, 55, 'entregado', 264000.00, '', '', 0, '2026-01-14 23:04:32', '2026-01-15 00:07:36', 2),
(79, 3, 6, 55, 'alistado', 60500.00, '', NULL, 0, '2026-01-14 23:59:54', '2026-01-15 00:08:24', 3),
(80, 24, 1, 1, 'en_pedido', 12345.67, 'Prueba automática', NULL, 0, '2026-01-15 00:00:39', '2026-01-15 00:00:39', 24),
(81, 25, 1, 8, 'en_pedido', 12345.67, 'Prueba automática', NULL, 0, '2026-01-15 00:00:39', '2026-01-15 00:00:39', 25),
(82, 26, 1, 10, 'en_pedido', 12345.67, 'Prueba automática', NULL, 0, '2026-01-15 00:00:39', '2026-01-15 00:00:39', 26),
(83, 29, 2, 8, 'en_pedido', 12345.67, 'Prueba automática', NULL, 0, '2026-01-15 00:01:21', '2026-01-15 00:01:21', 29),
(84, 16, 3, 10, 'en_pedido', 12345.67, 'Prueba automática', NULL, 0, '2026-01-15 00:01:49', '2026-01-15 00:01:49', 16),
(85, 4, 6, 56, 'alistado', 168000.00, '', NULL, 0, '2026-01-15 00:10:29', '2026-01-15 00:29:36', 4),
(86, 5, 6, 57, 'en_pedido', 614000.00, '', NULL, 0, '2026-01-15 00:16:36', '2026-01-15 00:16:36', 5),
(87, 6, 6, 55, 'verificado', 252000.00, '', NULL, 0, '2026-01-15 00:28:37', '2026-01-15 00:30:38', 6),
(88, 7, 6, 55, 'en_pedido', 12345.67, 'Prueba automática', NULL, 0, '2026-01-15 00:39:02', '2026-01-15 00:39:02', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalles`
--

CREATE TABLE `pedido_detalles` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL DEFAULT 1,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `cantidad_entregada` int(11) NOT NULL DEFAULT 0,
  `estado_preparacion` varchar(20) DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedido_detalles`
--

INSERT INTO `pedido_detalles` (`id`, `tenant_id`, `pedido_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`, `cantidad_entregada`, `estado_preparacion`) VALUES
(1, 1, 1, 1, 1, 999.99, 999.99, 0, 'pendiente'),
(2, 1, 2, 1, 3, 999.99, 2999.97, 3, 'listo'),
(3, 1, 2, 2, 3, 899.99, 2699.97, 3, 'listo'),
(4, 1, 3, 1, 1, 999.99, 999.99, 1, 'listo'),
(5, 1, 3, 2, 1, 899.99, 899.99, 1, 'listo'),
(6, 1, 4, 1, 1, 999.99, 999.99, 1, 'listo'),
(7, 1, 4, 2, 1, 899.99, 899.99, 1, 'listo'),
(8, 1, 5, 11, 1, 100000.00, 100000.00, 1, 'listo'),
(9, 1, 6, 3, 3, 2499.99, 7499.97, 3, 'listo'),
(10, 1, 6, 4, 4, 1799.99, 7199.96, 4, 'listo'),
(11, 1, 6, 9, 4, 299.99, 1199.96, 4, 'listo'),
(12, 1, 6, 11, 10, 100000.00, 1000000.00, 10, 'listo'),
(13, 1, 6, 2, 4, 899.99, 3599.96, 4, 'listo'),
(14, 1, 6, 1, 3, 999.99, 2999.97, 3, 'listo'),
(15, 1, 7, 11, 69, 100000.00, 6900000.00, 0, 'pendiente'),
(16, 1, 7, 9, 48, 299.99, 14399.52, 0, 'pendiente'),
(17, 1, 7, 1, 1, 999.99, 999.99, 0, 'pendiente'),
(18, 1, 7, 3, 1, 2499.99, 2499.99, 0, 'pendiente'),
(19, 1, 8, 11, 24, 100000.00, 2400000.00, 0, 'pendiente'),
(20, 1, 9, 1, 1, 999.99, 999.99, 0, 'pendiente'),
(21, 1, 9, 11, 1, 100000.00, 100000.00, 0, 'pendiente'),
(22, 1, 12, 45, 2, 67.00, 134.00, 0, 'pendiente'),
(23, 1, 12, 17, 2, 19.99, 39.98, 0, 'pendiente'),
(24, 1, 12, 42, 3, 415.00, 1245.00, 0, 'pendiente'),
(25, 1, 12, 41, 2, 397.00, 794.00, 0, 'pendiente'),
(26, 1, 13, 3, 3, 2499.99, 7499.97, 0, 'pendiente'),
(27, 1, 13, 19, 1, 19.99, 19.99, 0, 'pendiente'),
(28, 1, 13, 6, 1, 79.99, 79.99, 0, 'pendiente'),
(29, 1, 13, 4, 1, 1799.99, 1799.99, 0, 'pendiente'),
(30, 1, 13, 11, 1, 100000.00, 100000.00, 0, 'pendiente'),
(31, 1, 14, 1, 2, 999.99, 1999.98, 0, 'pendiente'),
(32, 1, 14, 41, 1, 397.00, 397.00, 0, 'pendiente'),
(33, 1, 14, 9, 3, 299.99, 899.97, 0, 'pendiente'),
(34, 1, 15, 42, 3, 415.00, 1245.00, 0, 'pendiente'),
(35, 1, 15, 4, 2, 1799.99, 3599.98, 0, 'pendiente'),
(36, 1, 15, 19, 2, 19.99, 39.98, 0, 'pendiente'),
(37, 1, 15, 3, 3, 2499.99, 7499.97, 0, 'pendiente'),
(38, 1, 15, 4, 2, 1799.99, 3599.98, 0, 'pendiente'),
(39, 1, 16, 3, 1, 2499.99, 2499.99, 0, 'pendiente'),
(40, 1, 16, 4, 2, 1799.99, 3599.98, 0, 'pendiente'),
(41, 1, 16, 7, 1, 89.99, 89.99, 0, 'pendiente'),
(42, 1, 17, 4, 2, 1799.99, 3599.98, 0, 'pendiente'),
(43, 1, 17, 44, 1, 487.00, 487.00, 0, 'pendiente'),
(44, 1, 17, 2, 3, 899.99, 2699.97, 0, 'pendiente'),
(45, 1, 17, 5, 3, 49.99, 149.97, 0, 'pendiente'),
(46, 1, 17, 1, 1, 999.99, 999.99, 0, 'pendiente'),
(47, 1, 18, 43, 2, 334.00, 668.00, 0, 'pendiente'),
(48, 1, 18, 5, 2, 49.99, 99.98, 0, 'pendiente'),
(49, 1, 18, 11, 3, 100000.00, 300000.00, 0, 'pendiente'),
(50, 1, 19, 5, 1, 49.99, 49.99, 0, 'pendiente'),
(51, 1, 19, 7, 2, 89.99, 179.98, 0, 'pendiente'),
(52, 1, 19, 40, 2, 105.00, 210.00, 0, 'pendiente'),
(53, 1, 19, 4, 2, 1799.99, 3599.98, 0, 'pendiente'),
(54, 1, 20, 17, 3, 19.99, 59.97, 0, 'pendiente'),
(55, 1, 20, 3, 1, 2499.99, 2499.99, 0, 'pendiente'),
(56, 1, 20, 43, 1, 334.00, 334.00, 0, 'pendiente'),
(57, 1, 20, 45, 3, 67.00, 201.00, 0, 'pendiente'),
(58, 1, 20, 5, 3, 49.99, 149.97, 0, 'pendiente'),
(59, 1, 21, 2, 1, 899.99, 899.99, 0, 'pendiente'),
(60, 1, 21, 11, 2, 100000.00, 200000.00, 0, 'pendiente'),
(61, 1, 21, 4, 2, 1799.99, 3599.98, 0, 'pendiente'),
(62, 2, 22, 15, 3, 2000.00, 6000.00, 0, 'pendiente'),
(63, 2, 22, 145, 1, 173.00, 173.00, 0, 'pendiente'),
(64, 2, 22, 140, 3, 230.00, 690.00, 0, 'pendiente'),
(65, 2, 22, 139, 1, 192.00, 192.00, 0, 'pendiente'),
(66, 2, 22, 144, 3, 130.00, 390.00, 0, 'pendiente'),
(67, 2, 23, 129, 3, 163.00, 489.00, 0, 'pendiente'),
(68, 2, 23, 15, 1, 2000.00, 2000.00, 0, 'pendiente'),
(69, 2, 23, 141, 3, 337.00, 1011.00, 0, 'pendiente'),
(70, 2, 24, 144, 1, 130.00, 130.00, 0, 'pendiente'),
(71, 2, 24, 128, 1, 478.00, 478.00, 0, 'pendiente'),
(72, 2, 24, 132, 1, 393.00, 393.00, 0, 'pendiente'),
(73, 2, 24, 144, 2, 130.00, 260.00, 0, 'pendiente'),
(74, 2, 25, 144, 1, 130.00, 130.00, 0, 'pendiente'),
(75, 2, 25, 13, 1, 150.00, 150.00, 0, 'pendiente'),
(76, 2, 25, 136, 2, 460.00, 920.00, 0, 'pendiente'),
(77, 2, 25, 134, 3, 213.00, 639.00, 0, 'pendiente'),
(78, 2, 26, 139, 2, 192.00, 384.00, 0, 'pendiente'),
(79, 2, 26, 142, 1, 176.00, 176.00, 0, 'pendiente'),
(80, 2, 26, 143, 1, 268.00, 268.00, 0, 'pendiente'),
(81, 2, 26, 135, 1, 450.00, 450.00, 0, 'pendiente'),
(82, 2, 26, 128, 3, 478.00, 1434.00, 0, 'pendiente'),
(83, 2, 27, 141, 3, 337.00, 1011.00, 0, 'pendiente'),
(84, 2, 27, 15, 3, 2000.00, 6000.00, 0, 'pendiente'),
(85, 2, 27, 145, 3, 173.00, 519.00, 0, 'pendiente'),
(86, 2, 27, 137, 1, 227.00, 227.00, 0, 'pendiente'),
(87, 2, 28, 140, 2, 230.00, 460.00, 0, 'pendiente'),
(88, 2, 28, 144, 2, 130.00, 260.00, 0, 'pendiente'),
(89, 2, 28, 136, 2, 460.00, 920.00, 0, 'pendiente'),
(90, 2, 28, 141, 1, 337.00, 337.00, 0, 'pendiente'),
(91, 2, 28, 145, 3, 173.00, 519.00, 0, 'pendiente'),
(92, 2, 29, 140, 1, 230.00, 230.00, 0, 'pendiente'),
(93, 2, 29, 137, 1, 227.00, 227.00, 0, 'pendiente'),
(94, 2, 29, 136, 1, 460.00, 460.00, 0, 'pendiente'),
(95, 2, 29, 145, 1, 173.00, 173.00, 0, 'pendiente'),
(96, 2, 29, 128, 3, 478.00, 1434.00, 0, 'pendiente'),
(97, 2, 30, 131, 2, 435.00, 870.00, 0, 'pendiente'),
(98, 2, 30, 143, 3, 268.00, 804.00, 0, 'pendiente'),
(99, 2, 30, 134, 3, 213.00, 639.00, 0, 'pendiente'),
(100, 2, 30, 134, 3, 213.00, 639.00, 0, 'pendiente'),
(101, 2, 31, 145, 2, 173.00, 346.00, 0, 'pendiente'),
(102, 2, 31, 134, 3, 213.00, 639.00, 0, 'pendiente'),
(103, 2, 31, 134, 1, 213.00, 213.00, 0, 'pendiente'),
(104, 3, 32, 226, 2, 292.00, 584.00, 0, 'pendiente'),
(105, 3, 32, 227, 1, 273.00, 273.00, 0, 'pendiente'),
(106, 3, 32, 14, 2, 500.00, 1000.00, 0, 'pendiente'),
(107, 3, 32, 231, 3, 288.00, 864.00, 0, 'pendiente'),
(108, 3, 33, 226, 1, 292.00, 292.00, 0, 'pendiente'),
(109, 3, 33, 233, 3, 496.00, 1488.00, 0, 'pendiente'),
(110, 3, 33, 231, 3, 288.00, 864.00, 0, 'pendiente'),
(111, 3, 33, 229, 1, 55.00, 55.00, 0, 'pendiente'),
(112, 3, 34, 231, 3, 288.00, 864.00, 0, 'pendiente'),
(113, 3, 34, 233, 3, 496.00, 1488.00, 0, 'pendiente'),
(114, 3, 34, 232, 1, 413.00, 413.00, 0, 'pendiente'),
(115, 3, 34, 226, 2, 292.00, 584.00, 0, 'pendiente'),
(116, 3, 34, 226, 3, 292.00, 876.00, 0, 'pendiente'),
(117, 3, 35, 227, 3, 273.00, 819.00, 0, 'pendiente'),
(118, 3, 35, 227, 1, 273.00, 273.00, 0, 'pendiente'),
(119, 3, 35, 222, 2, 64.00, 128.00, 0, 'pendiente'),
(120, 3, 35, 229, 2, 55.00, 110.00, 0, 'pendiente'),
(121, 3, 36, 231, 2, 288.00, 576.00, 0, 'pendiente'),
(122, 3, 36, 229, 1, 55.00, 55.00, 0, 'pendiente'),
(123, 3, 36, 228, 1, 114.00, 114.00, 0, 'pendiente'),
(124, 3, 36, 225, 2, 116.00, 232.00, 0, 'pendiente'),
(125, 3, 37, 227, 1, 273.00, 273.00, 0, 'pendiente'),
(126, 3, 37, 225, 1, 116.00, 116.00, 0, 'pendiente'),
(127, 3, 37, 224, 3, 61.00, 183.00, 0, 'pendiente'),
(128, 3, 37, 230, 1, 228.00, 228.00, 0, 'pendiente'),
(129, 3, 38, 226, 1, 292.00, 292.00, 0, 'pendiente'),
(130, 3, 38, 14, 3, 500.00, 1500.00, 0, 'pendiente'),
(131, 3, 38, 228, 2, 114.00, 228.00, 0, 'pendiente'),
(132, 3, 38, 226, 3, 292.00, 876.00, 0, 'pendiente'),
(133, 3, 38, 223, 1, 173.00, 173.00, 0, 'pendiente'),
(134, 3, 39, 234, 3, 165.00, 495.00, 0, 'pendiente'),
(135, 3, 39, 228, 2, 114.00, 228.00, 0, 'pendiente'),
(136, 3, 39, 16, 2, 1200.00, 2400.00, 0, 'pendiente'),
(137, 3, 39, 220, 1, 193.00, 193.00, 0, 'pendiente'),
(138, 3, 40, 229, 2, 55.00, 110.00, 0, 'pendiente'),
(139, 3, 40, 235, 3, 207.00, 621.00, 0, 'pendiente'),
(140, 3, 40, 228, 2, 114.00, 228.00, 0, 'pendiente'),
(141, 3, 40, 230, 1, 228.00, 228.00, 0, 'pendiente'),
(142, 3, 40, 16, 2, 1200.00, 2400.00, 0, 'pendiente'),
(143, 3, 41, 227, 2, 273.00, 546.00, 0, 'pendiente'),
(144, 3, 41, 224, 1, 61.00, 61.00, 0, 'pendiente'),
(145, 3, 41, 234, 3, 165.00, 495.00, 0, 'pendiente'),
(146, 3, 41, 233, 1, 496.00, 496.00, 0, 'pendiente'),
(147, 4, 42, 313, 3, 90.00, 270.00, 0, 'pendiente'),
(148, 4, 42, 310, 1, 381.00, 381.00, 0, 'pendiente'),
(149, 4, 42, 315, 1, 147.00, 147.00, 0, 'pendiente'),
(150, 4, 43, 37, 2, 249.99, 499.98, 0, 'pendiente'),
(151, 4, 43, 38, 3, 69.99, 209.97, 0, 'pendiente'),
(152, 4, 43, 38, 3, 69.99, 209.97, 0, 'pendiente'),
(153, 4, 43, 308, 1, 350.00, 350.00, 0, 'pendiente'),
(154, 4, 44, 18, 1, 29.99, 29.99, 0, 'pendiente'),
(155, 4, 44, 310, 3, 381.00, 1143.00, 0, 'pendiente'),
(156, 4, 44, 33, 1, 29.99, 29.99, 0, 'pendiente'),
(157, 4, 44, 35, 2, 89.99, 179.98, 0, 'pendiente'),
(158, 4, 45, 18, 3, 29.99, 89.97, 0, 'pendiente'),
(159, 4, 45, 312, 3, 452.00, 1356.00, 0, 'pendiente'),
(160, 4, 45, 20, 3, 49.99, 149.97, 0, 'pendiente'),
(161, 4, 45, 315, 3, 147.00, 441.00, 0, 'pendiente'),
(162, 4, 45, 311, 1, 146.00, 146.00, 0, 'pendiente'),
(163, 4, 46, 315, 1, 147.00, 147.00, 0, 'pendiente'),
(164, 4, 46, 315, 2, 147.00, 294.00, 0, 'pendiente'),
(165, 4, 46, 38, 3, 69.99, 209.97, 0, 'pendiente'),
(166, 4, 47, 309, 3, 460.00, 1380.00, 0, 'pendiente'),
(167, 4, 47, 30, 3, 1299.99, 3899.97, 0, 'pendiente'),
(168, 4, 47, 308, 2, 350.00, 700.00, 0, 'pendiente'),
(169, 4, 47, 309, 2, 460.00, 920.00, 0, 'pendiente'),
(170, 4, 48, 311, 2, 146.00, 292.00, 0, 'pendiente'),
(171, 4, 48, 310, 3, 381.00, 1143.00, 0, 'pendiente'),
(172, 4, 48, 35, 3, 89.99, 269.97, 0, 'pendiente'),
(173, 4, 48, 33, 2, 29.99, 59.98, 0, 'pendiente'),
(174, 4, 49, 36, 2, 79.99, 159.98, 0, 'pendiente'),
(175, 4, 49, 315, 2, 147.00, 294.00, 0, 'pendiente'),
(176, 4, 49, 30, 3, 1299.99, 3899.97, 0, 'pendiente'),
(177, 4, 49, 308, 3, 350.00, 1050.00, 0, 'pendiente'),
(178, 4, 50, 34, 1, 45.99, 45.99, 0, 'pendiente'),
(179, 4, 50, 29, 3, 799.99, 2399.97, 0, 'pendiente'),
(180, 4, 50, 32, 2, 1199.99, 2399.98, 0, 'pendiente'),
(181, 4, 50, 30, 2, 1299.99, 2599.98, 0, 'pendiente'),
(182, 4, 51, 312, 1, 452.00, 452.00, 0, 'pendiente'),
(183, 4, 51, 36, 3, 79.99, 239.97, 0, 'pendiente'),
(184, 4, 51, 29, 2, 799.99, 1599.98, 0, 'pendiente'),
(185, 4, 51, 38, 2, 69.99, 139.98, 0, 'pendiente'),
(186, 4, 51, 38, 3, 69.99, 209.97, 0, 'pendiente'),
(187, 1, 55, 39, 1, 419.00, 419.00, 0, 'pendiente'),
(188, 1, 55, 40, 1, 105.00, 105.00, 0, 'pendiente'),
(189, 2, 56, 128, 12, 478.00, 5736.00, 0, 'pendiente'),
(190, 2, 56, 129, 12, 163.00, 1956.00, 0, 'pendiente'),
(191, 2, 56, 130, 12, 225.00, 2700.00, 0, 'pendiente'),
(192, 2, 56, 132, 12, 393.00, 4716.00, 0, 'pendiente'),
(193, 2, 56, 133, 12, 80.00, 960.00, 0, 'pendiente'),
(194, 2, 56, 136, 12, 460.00, 5520.00, 0, 'pendiente'),
(195, 2, 57, 130, 2, 225.00, 450.00, 0, 'pendiente'),
(196, 2, 58, 128, 1, 478.00, 478.00, 1, 'listo'),
(197, 2, 58, 131, 1, 435.00, 435.00, 1, 'listo'),
(198, 2, 58, 133, 1, 80.00, 80.00, 0, 'pendiente'),
(199, 2, 59, 128, 1, 478.00, 478.00, 1, 'listo'),
(200, 2, 59, 132, 1, 393.00, 393.00, 1, 'listo'),
(201, 2, 59, 133, 24, 80.00, 1920.00, 24, 'listo'),
(202, 2, 59, 135, 1, 450.00, 450.00, 1, 'listo'),
(203, 2, 60, 128, 1, 478.00, 478.00, 1, 'listo'),
(204, 2, 61, 128, 1, 478.00, 478.00, 1, 'listo'),
(205, 2, 61, 130, 12, 225.00, 2700.00, 12, 'listo'),
(206, 2, 61, 131, 11, 435.00, 4785.00, 11, 'listo'),
(207, 2, 61, 132, 12, 393.00, 4716.00, 12, 'listo'),
(208, 2, 61, 133, 5, 80.00, 400.00, 0, 'pendiente'),
(209, 2, 62, 130, 1, 225.00, 225.00, 1, 'listo'),
(210, 2, 62, 133, 3, 80.00, 240.00, 0, 'pendiente'),
(211, 2, 63, 131, 8, 435.00, 3480.00, 0, 'pendiente'),
(212, 2, 63, 135, 14, 450.00, 6300.00, 14, 'listo'),
(213, 2, 64, 135, 12, 450.00, 5400.00, 12, 'listo'),
(214, 2, 64, 136, 24, 460.00, 11040.00, 0, 'pendiente'),
(215, 3, 65, 229, 1, 55.00, 55.00, 1, 'listo'),
(216, 3, 66, 229, 1, 55.00, 55.00, 0, 'pendiente'),
(217, 3, 67, 229, 1, 55.00, 55.00, 0, 'pendiente'),
(218, 2, 68, 398, 72, 21000.00, 1512000.00, 72, 'listo'),
(219, 3, 69, 229, 1, 55.00, 55.00, 0, 'pendiente'),
(220, 3, 69, 230, 1, 228.00, 228.00, 0, 'pendiente'),
(221, 1, 70, 39, 12, 419.00, 5028.00, 12, 'listo'),
(222, 1, 70, 40, 1, 105.00, 105.00, 0, 'pendiente'),
(223, 2, 71, 131, 2, 435.00, 870.00, 1, 'pendiente'),
(224, 2, 71, 398, 1, 21000.00, 21000.00, 1, 'listo'),
(225, 2, 72, 134, 12, 213.00, 2556.00, 0, 'pendiente'),
(226, 2, 72, 135, 3, 450.00, 1350.00, 0, 'pendiente'),
(227, 2, 72, 398, 36, 21000.00, 756000.00, 0, 'pendiente'),
(228, 2, 73, 134, 12, 213.00, 2556.00, 0, 'pendiente'),
(229, 2, 73, 135, 2, 450.00, 900.00, 0, 'pendiente'),
(230, 2, 74, 398, 1, 21000.00, 21000.00, 0, 'pendiente'),
(231, 2, 75, 398, 36, 21000.00, 756000.00, 36, 'pendiente'),
(232, 2, 76, 133, 1, 80.00, 80.00, 1, 'listo'),
(233, 2, 76, 134, 8, 213.00, 1704.00, 8, 'listo'),
(234, 2, 76, 138, 1, 378.00, 378.00, 1, 'listo'),
(235, 2, 76, 141, 1, 337.00, 337.00, 1, 'listo'),
(236, 2, 76, 143, 1, 268.00, 268.00, 1, 'listo'),
(237, 6, 77, 399, 300, 7000.00, 2100000.00, 300, 'listo'),
(238, 6, 78, 400, 48, 5500.00, 264000.00, 0, 'pendiente'),
(239, 6, 79, 400, 11, 5500.00, 60500.00, 10, 'listo'),
(240, 6, 85, 399, 24, 7000.00, 168000.00, 24, 'pendiente'),
(241, 6, 86, 399, 50, 7000.00, 350000.00, 0, 'pendiente'),
(242, 6, 86, 400, 48, 5500.00, 264000.00, 0, 'pendiente'),
(243, 6, 87, 399, 36, 7000.00, 252000.00, 36, 'listo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_historial`
--

CREATE TABLE `pedido_historial` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL DEFAULT 1,
  `pedido_id` int(11) NOT NULL,
  `estado` enum('en_pedido','alistado','empaquetado','verificado','en_reparto','entregado','cancelado') DEFAULT NULL,
  `nota` text DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedido_historial`
--

INSERT INTO `pedido_historial` (`id`, `tenant_id`, `pedido_id`, `estado`, `nota`, `usuario_id`, `fecha`) VALUES
(1, 1, 5, 'alistado', '', 1, '2026-01-08 14:33:34'),
(2, 1, 4, 'entregado', '', 1, '2026-01-08 14:34:03'),
(3, 1, 5, 'alistado', '', 1, '2026-01-08 16:12:21'),
(4, 1, 6, 'en_pedido', 'Pedido creado', NULL, '2026-01-08 16:58:57'),
(5, 1, 6, 'en_reparto', '', 1, '2026-01-08 17:15:42'),
(6, 1, 5, 'en_reparto', '', 1, '2026-01-08 22:24:55'),
(7, 1, 6, 'alistado', '', 1, '2026-01-08 22:44:31'),
(8, 1, 6, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-09 00:29:58'),
(9, 1, 6, 'verificado', 'Estado cambiado a verificado', NULL, '2026-01-09 00:30:07'),
(10, 1, 3, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-09 02:00:43'),
(11, 1, 3, 'verificado', 'Estado cambiado a verificado', NULL, '2026-01-09 02:00:45'),
(12, 1, 6, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-09 02:06:13'),
(13, 1, 6, 'verificado', 'Estado cambiado a verificado', NULL, '2026-01-09 02:06:20'),
(14, 1, 6, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-09 02:06:39'),
(15, 1, 6, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-09 02:11:18'),
(16, 1, 6, 'verificado', 'Estado cambiado a verificado', NULL, '2026-01-09 02:11:26'),
(17, 1, 6, 'en_reparto', '', 1, '2026-01-09 02:11:35'),
(18, 1, 6, 'en_reparto', '', 1, '2026-01-09 02:26:05'),
(19, 1, 6, 'entregado', '', 1, '2026-01-09 02:27:39'),
(20, 1, 6, 'entregado', '', 1, '2026-01-09 02:27:41'),
(21, 1, 3, 'en_reparto', 'Pedido listo para entrega', NULL, '2026-01-09 02:29:49'),
(22, 1, 2, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-09 02:30:03'),
(23, 1, 7, 'en_pedido', 'Pedido creado', 1, '2026-01-09 04:19:35'),
(24, 1, 8, 'en_pedido', 'Pedido creado', 1, '2026-01-09 04:26:56'),
(25, 1, 9, 'en_pedido', 'Pedido creado', 1, '2026-01-09 04:46:14'),
(26, 2, 52, 'en_pedido', 'Pedido creado', NULL, '2026-01-13 16:35:54'),
(27, 1, 53, 'en_pedido', 'Pedido creado', 1, '2026-01-13 18:20:32'),
(28, 1, 54, 'en_pedido', 'Pedido creado', 1, '2026-01-13 18:20:35'),
(29, 1, 55, 'en_pedido', 'Pedido creado', 1, '2026-01-13 18:27:15'),
(30, 2, 56, 'en_pedido', 'Pedido creado', NULL, '2026-01-13 18:45:39'),
(31, 2, 57, 'en_pedido', 'Pedido creado', NULL, '2026-01-13 19:43:59'),
(32, 2, 58, 'en_pedido', 'Pedido creado', NULL, '2026-01-13 21:55:49'),
(33, 2, 59, 'en_pedido', 'Pedido creado', NULL, '2026-01-13 21:58:50'),
(34, 1, 59, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 22:25:05'),
(35, 1, 59, 'verificado', 'Estado cambiado a verificado', NULL, '2026-01-13 22:25:50'),
(36, 2, 59, 'en_reparto', '', 1, '2026-01-13 22:26:00'),
(37, 2, 60, 'en_pedido', 'Pedido creado', 1, '2026-01-13 22:27:04'),
(38, 1, 60, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 22:28:02'),
(39, 1, 58, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 22:34:12'),
(40, 1, 60, 'verificado', '✅ PEDIDO COMPLETO\r\n\r\nTodos los 1 producto(s) fueron entregados.', NULL, '2026-01-13 22:35:05'),
(41, 2, 61, 'en_pedido', 'Pedido creado', 1, '2026-01-13 22:37:42'),
(42, 1, 61, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 22:38:44'),
(43, 2, 62, 'en_pedido', 'Pedido creado', 1, '2026-01-13 22:39:36'),
(44, 1, 62, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 22:41:10'),
(45, 1, 62, 'alistado', 'Estado cambiado a alistado', NULL, '2026-01-13 22:41:18'),
(46, 1, 62, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 22:43:57'),
(47, 1, 62, 'alistado', 'Estado cambiado a alistado', NULL, '2026-01-13 22:44:07'),
(48, 1, 62, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 22:45:54'),
(49, 1, 62, 'verificado', '⚠️ PEDIDO INCOMPLETO\r\n\r\n✅ Productos entregados: 1\r\n❌ Productos faltantes: 1\r\n\r\nProductos NO entregados:\r\n  • Motorola Edge\r\n\r\nSe cobra solo por los 1 producto(s) entregado(s).', NULL, '2026-01-13 22:46:19'),
(50, 2, 63, 'en_pedido', 'Pedido creado', 1, '2026-01-13 22:48:37'),
(51, 1, 63, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 22:49:18'),
(52, 2, 64, 'en_pedido', 'Pedido creado', 1, '2026-01-13 23:02:50'),
(53, 1, 64, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 23:11:58'),
(54, 1, 64, 'verificado', '⚠️ PEDIDO INCOMPLETO\r\n\r\n✅ Productos entregados: 1\r\n❌ Productos faltantes: 1\r\n\r\nProductos NO entregados:\r\n  • Sony Xperia\r\n\r\nSe cobra solo por los 1 producto(s) entregado(s).', NULL, '2026-01-13 23:13:26'),
(55, 3, 65, 'en_pedido', 'Pedido creado', 1, '2026-01-13 23:16:42'),
(56, 3, 66, 'en_pedido', 'Pedido creado', 1, '2026-01-13 23:17:30'),
(57, 3, 67, 'en_pedido', 'Pedido creado', 1, '2026-01-13 23:18:09'),
(58, 2, 68, 'en_pedido', 'Pedido creado', 1, '2026-01-13 23:19:00'),
(59, 3, 69, 'en_pedido', 'Pedido creado', 1, '2026-01-13 23:19:13'),
(60, 1, 70, 'en_pedido', 'Pedido creado', 1, '2026-01-13 23:19:52'),
(61, 2, 71, 'en_pedido', 'Pedido creado', 1, '2026-01-13 23:22:09'),
(62, 1, 71, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 23:30:12'),
(63, 1, 71, 'verificado', '⚠️ PEDIDO INCOMPLETO\r\n\r\n✅ Cantidad enviada: 2 de 3\r\n❌ Cantidad faltante: 1\r\n\r\nFaltantes por producto:\r\n  • Google Pixel 6 (-1)\r\n\r\nSe cobra solo por lo enviado (2).', NULL, '2026-01-13 23:30:39'),
(64, 1, 71, 'en_reparto', 'Pedido listo para entrega', NULL, '2026-01-13 23:30:42'),
(65, 1, 70, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 23:30:55'),
(66, 1, 68, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 23:31:24'),
(67, 1, 65, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-13 23:33:05'),
(68, 2, 72, 'en_pedido', 'Pedido creado', NULL, '2026-01-14 18:40:02'),
(69, 2, 73, 'en_pedido', 'Pedido creado', NULL, '2026-01-14 18:40:28'),
(70, 2, 74, 'en_pedido', 'Pedido creado', NULL, '2026-01-14 18:41:00'),
(71, 2, 75, 'en_pedido', 'Pedido creado', 1, '2026-01-14 18:43:20'),
(72, 2, 75, 'en_reparto', '', 1, '2026-01-14 18:44:19'),
(73, 2, 75, 'entregado', '', 1, '2026-01-14 18:47:26'),
(74, 2, 76, 'en_pedido', 'Pedido creado', 1, '2026-01-14 18:49:12'),
(75, 1, 76, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-14 18:50:47'),
(76, 1, 76, 'alistado', 'Estado cambiado a alistado', NULL, '2026-01-14 20:29:42'),
(77, 6, 77, 'en_pedido', 'Pedido creado', 5, '2026-01-14 22:34:41'),
(78, 1, 77, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-14 22:36:57'),
(79, 1, 77, 'verificado', '✅ PEDIDO COMPLETO\r\n\r\nSe envían 300 de 300. Todos listos.', NULL, '2026-01-14 22:37:59'),
(80, 6, 78, 'en_pedido', 'Pedido creado', 5, '2026-01-14 23:04:32'),
(81, 6, 77, 'entregado', '', 5, '2026-01-14 23:23:56'),
(82, 6, 79, 'en_pedido', 'Pedido creado', 1, '2026-01-14 23:59:54'),
(83, 1, 80, 'en_pedido', 'Pedido creado', NULL, '2026-01-15 00:00:39'),
(84, 2, 81, 'en_pedido', 'Pedido creado', NULL, '2026-01-15 00:00:39'),
(85, 3, 82, 'en_pedido', 'Pedido creado', NULL, '2026-01-15 00:00:39'),
(86, 2, 83, 'en_pedido', 'Pedido creado', NULL, '2026-01-15 00:01:21'),
(87, 3, 84, 'en_pedido', 'Pedido creado', NULL, '2026-01-15 00:01:49'),
(88, 1, 79, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-15 00:03:49'),
(89, 1, 79, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-15 00:03:52'),
(90, 6, 78, 'entregado', '', 5, '2026-01-15 00:07:36'),
(91, 1, 79, 'alistado', 'Estado cambiado a alistado', NULL, '2026-01-15 00:08:24'),
(92, 6, 85, 'en_pedido', 'Pedido creado', 1, '2026-01-15 00:10:29'),
(93, 1, 85, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-15 00:12:36'),
(94, 6, 86, 'en_pedido', 'Pedido creado', 1, '2026-01-15 00:16:36'),
(95, 6, 87, 'en_pedido', 'Pedido creado', 1, '2026-01-15 00:28:37'),
(96, 1, 85, 'alistado', 'Estado cambiado a alistado', NULL, '2026-01-15 00:29:36'),
(97, 1, 87, 'empaquetado', 'Estado cambiado a empaquetado', NULL, '2026-01-15 00:30:28'),
(98, 1, 87, 'verificado', '✅ PEDIDO COMPLETO\r\n\r\nSe envían 36 de 36. Todos listos.', NULL, '2026-01-15 00:30:38'),
(99, 6, 88, 'en_pedido', 'Pedido creado', NULL, '2026-01-15 00:39:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `numero_producto` int(11) NOT NULL DEFAULT 0,
  `tenant_id` int(11) NOT NULL DEFAULT 1,
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

INSERT INTO `productos` (`id`, `numero_producto`, `tenant_id`, `categoria_id`, `subcategoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 1, 1, 1, 'iPhone 15 Pro', 'Último modelo de Apple con chip A17 Pro y cámara avanzada.', 999.99, 50, NULL, 1, '2025-12-24 17:35:46', '2026-01-14 22:58:01'),
(2, 2, 1, 1, 1, 'Samsung Galaxy S24', 'Teléfono Android con pantalla AMOLED y procesador Snapdragon.', 899.99, 45, 'samsung_s24.jpg', 1, '2025-12-24 17:35:46', '2026-01-14 22:58:01'),
(3, 3, 1, 1, 2, 'MacBook Pro 16', 'Laptop de alta rendimiento con chip M3 Max para profesionales.', 2499.99, 20, 'macbook_pro.jpg', 1, '2025-12-24 17:35:46', '2026-01-14 22:58:01'),
(4, 4, 1, 1, 2, 'Dell XPS 15', 'Computadora portátil con procesador Intel y pantalla 4K.', 1799.99, 25, 'dell_xps.jpg', 1, '2025-12-24 17:35:46', '2026-01-14 22:58:01'),
(5, 5, 1, 2, 3, 'Camiseta Premium Hombre', 'Camiseta de algodón 100% de alta calidad para hombre.', 49.99, 100, 'camiseta_hombre.jpg', 1, '2025-12-24 17:35:46', '2026-01-14 22:58:01'),
(6, 6, 1, 2, 3, 'Pantalón Casual Hombre', 'Pantalón casual de tela resistente, perfecto para uso diario.', 79.99, 80, 'pantalon_hombre.jpg', 1, '2025-12-24 17:35:46', '2026-01-14 22:58:01'),
(7, 7, 1, 2, 4, 'Vestido Casual Mujer', 'Vestido elegante y cómodo para cualquier ocasión casual.', 89.99, 60, 'vestido_mujer.jpg', 1, '2025-12-24 17:35:46', '2026-01-14 22:58:01'),
(8, 8, 1, 2, 4, 'Jeans Premium Mujer', 'Jeans de marca reconocida, cómodos y de excelente calidad.', 99.99, 75, 'jeans_mujer.jpg', 1, '2025-12-24 17:35:46', '2026-01-14 22:58:02'),
(9, 9, 1, 3, 5, 'Horno Eléctrico', 'Horno eléctrico con múltiples funciones para cocinar.', 299.99, 15, 'horno_electrico.jpg', 1, '2025-12-24 17:35:46', '2026-01-14 22:58:02'),
(10, 10, 1, 3, 6, 'Juego de Cama King', 'Juego de sábanas y almohadas tamaño king de algodón.', 199.99, 30, 'juego_cama.jpg', 1, '2025-12-24 17:35:46', '2026-01-14 22:58:02'),
(11, 11, 1, 1, 2, 'computador', 'buygugu', 100000.00, 50, NULL, 1, '2026-01-07 18:05:16', '2026-01-14 22:58:02'),
(13, 1, 2, 4, 7, 'Servicio de ConsultorÝa', 'ConsultorÝa profesional por horas', 150.00, 998, NULL, 1, '2026-01-09 21:43:48', '2026-01-14 22:58:02'),
(14, 1, 3, 5, 8, 'Lote Mayorista', 'Distribuci¾n por cajas de 20 unidades', 500.00, 100, NULL, 1, '2026-01-09 21:43:55', '2026-01-14 22:58:02'),
(15, 2, 2, 4, 9, 'App Web Custom', 'Desarrollo de aplicaci¾n web personalizada', 2000.00, 49, NULL, 1, '2026-01-09 22:33:44', '2026-01-14 22:58:02'),
(16, 2, 3, 5, 10, 'Monitor 24 Bulk', 'Monitores 24 pulgadas lote de 10 unidades', 1200.00, 200, NULL, 0, '2026-01-09 22:33:54', '2026-01-14 22:58:02'),
(17, 12, 1, 1, 1, 'Prod img tenant1', 'test img tenant1', 19.99, 5, 'public/tenants/1/images/upl_6961914b505a3_test-img1.png', 1, '2026-01-09 23:37:47', '2026-01-14 22:58:02'),
(18, 1, 4, 6, 11, 'Prod img tenant4', 'test img tenant4', 29.99, 7, 'public/tenants/4/images/upl_6961914b8133b_test-img2.png', 1, '2026-01-09 23:37:47', '2026-01-14 22:58:02'),
(19, 13, 1, 1, 1, 'Prod img tenant1', 'test img tenant1', 19.99, 5, 'public/tenants/1/images/upl_696191548a190_test-img1.png', 1, '2026-01-09 23:37:56', '2026-01-14 22:58:02'),
(20, 2, 4, 6, 11, 'Prod img tenant4 edit', 'edit test 2', 49.99, 8, 'public/tenants/4/images/upl_696191f2ef230_test-img4.png', 1, '2026-01-09 23:38:54', '2026-01-14 22:58:02'),
(29, 3, 4, 6, 12, 'Laptop HP 15', 'Laptop HP 15 pulgadas, Intel Core i5, 8GB RAM, 256GB SSD. Perfecta para trabajo y estudio.', 799.99, 15, 'dell_xps.jpg', 1, '2026-01-10 17:34:23', '2026-01-14 22:58:02'),
(30, 4, 4, 6, 12, 'MacBook Air M2', 'MacBook Air con chip M2, 13 pulgadas, 8GB RAM, 512GB SSD. Ultra delgada y potente.', 1299.99, 8, 'macbook_pro.jpg', 1, '2026-01-10 17:34:23', '2026-01-14 22:58:02'),
(31, 5, 4, 6, 11, 'iPhone 15 Pro', 'iPhone 15 Pro 128GB, Titanio Azul. Cßmara profesional de 48MP.', 999.99, 20, 'samsung_s24.jpg', 1, '2026-01-10 17:34:23', '2026-01-14 22:58:02'),
(32, 6, 4, 6, 11, 'Samsung Galaxy S24', 'Samsung Galaxy S24 Ultra 256GB. Pantalla AMOLED 120Hz, S Pen incluido.', 1199.99, 12, 'samsung_s24.jpg', 1, '2026-01-10 17:34:23', '2026-01-14 22:58:02'),
(33, 7, 4, 7, 13, 'Camisa Polo Hombre', 'Camisa polo 100% algod¾n peinado, talla M. Disponible en varios colores.', 29.99, 50, 'camiseta_hombre.jpg', 1, '2026-01-10 17:34:23', '2026-01-14 22:58:02'),
(34, 8, 4, 7, 14, 'Vestido Casual Mujer', 'Vestido casual elegante, talla S. Perfecto para cualquier ocasi¾n.', 45.99, 30, 'jeans_mujer.jpg', 1, '2026-01-10 17:34:23', '2026-01-14 22:58:02'),
(35, 9, 4, 8, 15, 'Licuadora Oster', 'Licuadora de 3 velocidades, 600W. Vaso de vidrio resistente.', 89.99, 25, 'horno_electrico.jpg', 1, '2026-01-10 17:34:23', '2026-01-14 22:58:02'),
(36, 10, 4, 8, 16, 'Edred¾n King Size', 'Edred¾n suave tama±o King, color gris. Material hipoalergÚnico.', 79.99, 18, 'juego_cama.jpg', 1, '2026-01-10 17:34:23', '2026-01-14 22:58:02'),
(37, 11, 4, 6, 11, 'AirPods Pro', 'AirPods Pro con cancelaci¾n de ruido activa. Estuche con MagSafe.', 249.99, 35, 'samsung_s24.jpg', 1, '2026-01-10 17:34:23', '2026-01-14 22:58:02'),
(38, 12, 4, 7, 13, 'Jeans Levi 501', 'Jeans Levi 501 clßsicos, corte regular. Talla 32.', 69.99, 40, 'jeans_mujer.jpg', 1, '2026-01-10 17:34:23', '2026-01-14 22:58:02'),
(39, 14, 1, 1, 1, 'Samsung Galaxy S21', 'Descripción de Samsung Galaxy S21', 419.00, 16, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(40, 15, 1, 1, 1, 'iPhone 13 Pro', 'Descripción de iPhone 13 Pro', 105.00, 34, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(41, 16, 1, 1, 1, 'Xiaomi 12', 'Descripción de Xiaomi 12', 397.00, 15, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(42, 17, 1, 1, 1, 'Google Pixel 6', 'Descripción de Google Pixel 6', 415.00, 19, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(43, 18, 1, 1, 1, 'OnePlus 9', 'Descripción de OnePlus 9', 334.00, 33, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(44, 19, 1, 1, 1, 'Motorola Edge', 'Descripción de Motorola Edge', 487.00, 50, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(45, 20, 1, 1, 1, 'Realme GT', 'Descripción de Realme GT', 67.00, 39, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(46, 21, 1, 1, 1, 'Nothing Phone', 'Descripción de Nothing Phone', 394.00, 49, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(47, 22, 1, 1, 1, 'Sony Xperia', 'Descripción de Sony Xperia', 424.00, 25, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(48, 23, 1, 1, 1, 'OPPO Find X3', 'Descripción de OPPO Find X3', 422.00, 25, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(49, 24, 1, 1, 2, 'Dell XPS 13', 'Descripción de Dell XPS 13', 131.00, 20, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(50, 25, 1, 1, 2, 'HP Pavilion 15', 'Descripción de HP Pavilion 15', 440.00, 19, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(51, 26, 1, 1, 2, 'Lenovo ThinkPad', 'Descripción de Lenovo ThinkPad', 163.00, 37, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(52, 27, 1, 1, 2, 'ASUS VivoBook', 'Descripción de ASUS VivoBook', 205.00, 15, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(53, 28, 1, 1, 2, 'Acer Aspire 5', 'Descripción de Acer Aspire 5', 382.00, 20, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(54, 29, 1, 1, 2, 'MSI GS66', 'Descripción de MSI GS66', 186.00, 10, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(55, 30, 1, 1, 2, 'Razer Blade', 'Descripción de Razer Blade', 477.00, 11, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(56, 31, 1, 1, 2, 'LG Gram', 'Descripción de LG Gram', 371.00, 43, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(57, 32, 1, 1, 2, 'ROG Gaming Laptop', 'Descripción de ROG Gaming Laptop', 113.00, 17, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(58, 33, 1, 1, 17, 'Cargador Rápido', 'Descripción de Cargador Rápido', 62.00, 20, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(59, 34, 1, 1, 17, 'Cable USB-C', 'Descripción de Cable USB-C', 85.00, 50, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(60, 35, 1, 1, 17, 'Protector Pantalla', 'Descripción de Protector Pantalla', 240.00, 44, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(61, 36, 1, 1, 17, 'Funda Teléfono', 'Descripción de Funda Teléfono', 333.00, 38, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(62, 37, 1, 1, 17, 'Audífonos Inalámbricos', 'Descripción de Audífonos Inalámbricos', 231.00, 43, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(63, 38, 1, 1, 17, 'Power Bank', 'Descripción de Power Bank', 283.00, 15, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(64, 39, 1, 1, 17, 'Soporte Móvil', 'Descripción de Soporte Móvil', 345.00, 25, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(65, 40, 1, 1, 17, 'Protector Cámara', 'Descripción de Protector Cámara', 71.00, 33, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(66, 41, 1, 1, 17, 'Anillo Soporte', 'Descripción de Anillo Soporte', 194.00, 44, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(67, 42, 1, 1, 17, 'Cable HDMI', 'Descripción de Cable HDMI', 430.00, 14, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(68, 43, 1, 2, 18, 'Camiseta Básica', 'Descripción de Camiseta Básica', 72.00, 40, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(69, 44, 1, 2, 18, 'Pantalón Denim', 'Descripción de Pantalón Denim', 257.00, 27, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(70, 45, 1, 2, 18, 'Camisa Social', 'Descripción de Camisa Social', 261.00, 33, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(71, 46, 1, 2, 18, 'Polo Premium', 'Descripción de Polo Premium', 342.00, 10, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(72, 47, 1, 2, 18, 'Shorts Deportivos', 'Descripción de Shorts Deportivos', 334.00, 49, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(73, 48, 1, 2, 18, 'Chaqueta Casual', 'Descripción de Chaqueta Casual', 151.00, 27, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(74, 49, 1, 2, 18, 'Suéter Lana', 'Descripción de Suéter Lana', 388.00, 34, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(75, 50, 1, 2, 18, 'Bermudas', 'Descripción de Bermudas', 75.00, 24, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(76, 51, 1, 2, 18, 'Camiseta Deportiva', 'Descripción de Camiseta Deportiva', 371.00, 34, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(77, 52, 1, 2, 18, 'Pantalón Cargo', 'Descripción de Pantalón Cargo', 492.00, 24, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(78, 53, 1, 2, 19, 'Blusa Elegante', 'Descripción de Blusa Elegante', 458.00, 14, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(79, 54, 1, 2, 19, 'Jeans Skinny', 'Descripción de Jeans Skinny', 202.00, 34, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(80, 55, 1, 2, 19, 'Vestido Casual', 'Descripción de Vestido Casual', 287.00, 25, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(81, 56, 1, 2, 19, 'Top Deportivo', 'Descripción de Top Deportivo', 498.00, 16, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(82, 57, 1, 2, 19, 'Falda Midi', 'Descripción de Falda Midi', 117.00, 24, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(83, 58, 1, 2, 19, 'Chaqueta Mezclilla', 'Descripción de Chaqueta Mezclilla', 180.00, 41, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(84, 59, 1, 2, 19, 'Leggings Premium', 'Descripción de Leggings Premium', 234.00, 14, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(85, 60, 1, 2, 19, 'Cardigan', 'Descripción de Cardigan', 84.00, 37, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(86, 61, 1, 2, 19, 'Blusa Floral', 'Descripción de Blusa Floral', 179.00, 14, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(87, 62, 1, 2, 19, 'Pantalón Palazzo', 'Descripción de Pantalón Palazzo', 257.00, 15, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(88, 63, 1, 2, 20, 'Camiseta Niño', 'Descripción de Camiseta Niño', 336.00, 23, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(89, 64, 1, 2, 20, 'Pantalón Niño', 'Descripción de Pantalón Niño', 410.00, 41, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(90, 65, 1, 2, 20, 'Sudadera Infantil', 'Descripción de Sudadera Infantil', 307.00, 27, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(91, 66, 1, 2, 20, 'Shorts Niño', 'Descripción de Shorts Niño', 361.00, 12, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(92, 67, 1, 2, 20, 'Falda Niña', 'Descripción de Falda Niña', 399.00, 46, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(93, 68, 1, 2, 20, 'Blusa Niña', 'Descripción de Blusa Niña', 295.00, 48, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(94, 69, 1, 2, 20, 'Pantalón Niña', 'Descripción de Pantalón Niña', 86.00, 19, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(95, 70, 1, 2, 20, 'Chaqueta Infantil', 'Descripción de Chaqueta Infantil', 104.00, 16, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(96, 71, 1, 2, 20, 'Buzo Infantil', 'Descripción de Buzo Infantil', 153.00, 40, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(97, 72, 1, 2, 20, 'Leggings Niña', 'Descripción de Leggings Niña', 248.00, 19, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(98, 73, 1, 3, 5, 'Licuadora', 'Descripción de Licuadora', 359.00, 34, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(99, 74, 1, 3, 5, 'Microondas', 'Descripción de Microondas', 137.00, 42, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(100, 75, 1, 3, 5, 'Cafetera', 'Descripción de Cafetera', 451.00, 39, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(101, 76, 1, 3, 5, 'Tostador', 'Descripción de Tostador', 301.00, 29, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(102, 77, 1, 3, 5, 'Olla Arrocera', 'Descripción de Olla Arrocera', 461.00, 43, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(103, 78, 1, 3, 5, 'Plancha Eléctrica', 'Descripción de Plancha Eléctrica', 101.00, 16, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(104, 79, 1, 3, 5, 'Freidora Aire', 'Descripción de Freidora Aire', 407.00, 35, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(105, 80, 1, 3, 5, 'Batidora', 'Descripción de Batidora', 200.00, 35, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(106, 81, 1, 3, 5, 'Exprimidor', 'Descripción de Exprimidor', 179.00, 25, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(107, 82, 1, 3, 5, 'Hervidor Eléctrico', 'Descripción de Hervidor Eléctrico', 274.00, 45, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(108, 83, 1, 3, 21, 'Sofá 3 Puestos', 'Descripción de Sofá 3 Puestos', 463.00, 17, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(109, 84, 1, 3, 21, 'Mesa Comedor', 'Descripción de Mesa Comedor', 475.00, 27, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(110, 85, 1, 3, 21, 'Silla Oficina', 'Descripción de Silla Oficina', 399.00, 42, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(111, 86, 1, 3, 21, 'Cama Queen', 'Descripción de Cama Queen', 440.00, 38, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(112, 87, 1, 3, 21, 'Closet Madera', 'Descripción de Closet Madera', 489.00, 24, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(113, 88, 1, 3, 21, 'Biblioteca', 'Descripción de Biblioteca', 156.00, 10, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(114, 89, 1, 3, 21, 'Mesita Noche', 'Descripción de Mesita Noche', 262.00, 19, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(115, 90, 1, 3, 21, 'Escritorio', 'Descripción de Escritorio', 328.00, 40, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(116, 91, 1, 3, 21, 'Estantería', 'Descripción de Estantería', 239.00, 26, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(117, 92, 1, 3, 21, 'Modular Tv', 'Descripción de Modular Tv', 413.00, 45, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(118, 93, 1, 3, 22, 'Cuadro Moderno', 'Descripción de Cuadro Moderno', 150.00, 41, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(119, 94, 1, 3, 22, 'Espejo Decorativo', 'Descripción de Espejo Decorativo', 442.00, 12, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(120, 95, 1, 3, 22, 'Lámpara Piso', 'Descripción de Lámpara Piso', 270.00, 47, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(121, 96, 1, 3, 22, 'Cojín', 'Descripción de Cojín', 216.00, 33, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(122, 97, 1, 3, 22, 'Cortina Premium', 'Descripción de Cortina Premium', 421.00, 39, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(123, 98, 1, 3, 22, 'Tapete', 'Descripción de Tapete', 126.00, 49, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(124, 99, 1, 3, 22, 'Jarrón Cerámica', 'Descripción de Jarrón Cerámica', 257.00, 42, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(125, 100, 1, 3, 22, 'Alfombra', 'Descripción de Alfombra', 346.00, 23, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(126, 101, 1, 3, 22, 'Vela Aromática', 'Descripción de Vela Aromática', 320.00, 15, NULL, 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(127, 102, 1, 3, 5, 'Reloj Pared', 'Descripción de Reloj Pared', 446.00, 31, 'public/tenants/1/images/upl_69668775db0df_4554.jpg', 1, '2026-01-11 20:00:15', '2026-01-14 22:58:02'),
(128, 3, 2, 9, 23, 'Samsung Galaxy S21', 'Descripción de Samsung Galaxy S21', 478.00, 0, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(129, 4, 2, 9, 23, 'iPhone 13 Pro', 'Descripción de iPhone 13 Pro', 163.00, 0, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(130, 5, 2, 9, 23, 'Xiaomi 12', 'Descripción de Xiaomi 12', 225.00, 0, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(131, 6, 2, 9, 23, 'Google Pixel 6', 'Descripción de Google Pixel 6', 435.00, 0, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(132, 7, 2, 9, 23, 'OnePlus 9', 'Descripción de OnePlus 9', 393.00, 0, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(133, 8, 2, 9, 23, 'Motorola Edge', 'Descripción de Motorola Edge', 80.00, 0, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(134, 9, 2, 9, 23, 'Realme GT', 'Descripción de Realme GT', 213.00, 1, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(135, 10, 2, 9, 23, 'Nothing Phone', 'Descripción de Nothing Phone', 450.00, 1, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(136, 11, 2, 9, 23, 'Sony Xperia', 'Descripción de Sony Xperia', 460.00, 5, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(137, 12, 2, 9, 23, 'OPPO Find X3', 'Descripción de OPPO Find X3', 227.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(138, 13, 2, 9, 24, 'MacBook Pro 16', 'Descripción de MacBook Pro 16', 378.00, 37, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(139, 14, 2, 9, 24, 'Dell XPS 13', 'Descripción de Dell XPS 13', 192.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(140, 15, 2, 9, 24, 'HP Pavilion 15', 'Descripción de HP Pavilion 15', 230.00, 25, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(141, 16, 2, 9, 24, 'Lenovo ThinkPad', 'Descripción de Lenovo ThinkPad', 337.00, 46, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(142, 17, 2, 9, 24, 'ASUS VivoBook', 'Descripción de ASUS VivoBook', 176.00, 36, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(143, 18, 2, 9, 24, 'Acer Aspire 5', 'Descripción de Acer Aspire 5', 268.00, 31, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(144, 19, 2, 9, 24, 'MSI GS66', 'Descripción de MSI GS66', 130.00, 48, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(145, 20, 2, 9, 24, 'Razer Blade', 'Descripción de Razer Blade', 173.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(146, 21, 2, 9, 24, 'LG Gram', 'Descripción de LG Gram', 129.00, 29, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(147, 22, 2, 9, 24, 'ROG Gaming Laptop', 'Descripción de ROG Gaming Laptop', 95.00, 39, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(148, 23, 2, 9, 25, 'Cargador Rápido', 'Descripción de Cargador Rápido', 192.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(149, 24, 2, 9, 25, 'Cable USB-C', 'Descripción de Cable USB-C', 381.00, 45, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(150, 25, 2, 9, 25, 'Protector Pantalla', 'Descripción de Protector Pantalla', 327.00, 32, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(151, 26, 2, 9, 25, 'Funda Teléfono', 'Descripción de Funda Teléfono', 314.00, 14, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(152, 27, 2, 9, 25, 'Audífonos Inalámbricos', 'Descripción de Audífonos Inalámbricos', 242.00, 41, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(153, 28, 2, 9, 25, 'Power Bank', 'Descripción de Power Bank', 104.00, 13, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(154, 29, 2, 9, 25, 'Soporte Móvil', 'Descripción de Soporte Móvil', 353.00, 13, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(155, 30, 2, 9, 25, 'Protector Cámara', 'Descripción de Protector Cámara', 432.00, 19, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(156, 31, 2, 9, 25, 'Anillo Soporte', 'Descripción de Anillo Soporte', 56.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(157, 32, 2, 9, 25, 'Cable HDMI', 'Descripción de Cable HDMI', 254.00, 36, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(158, 33, 2, 10, 26, 'Camiseta Básica', 'Descripción de Camiseta Básica', 180.00, 28, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(159, 34, 2, 10, 26, 'Pantalón Denim', 'Descripción de Pantalón Denim', 200.00, 37, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(160, 35, 2, 10, 26, 'Camisa Social', 'Descripción de Camisa Social', 247.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(161, 36, 2, 10, 26, 'Polo Premium', 'Descripción de Polo Premium', 56.00, 31, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(162, 37, 2, 10, 26, 'Shorts Deportivos', 'Descripción de Shorts Deportivos', 176.00, 43, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(163, 38, 2, 10, 26, 'Chaqueta Casual', 'Descripción de Chaqueta Casual', 288.00, 43, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(164, 39, 2, 10, 26, 'Suéter Lana', 'Descripción de Suéter Lana', 450.00, 38, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(165, 40, 2, 10, 26, 'Bermudas', 'Descripción de Bermudas', 214.00, 29, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(166, 41, 2, 10, 26, 'Camiseta Deportiva', 'Descripción de Camiseta Deportiva', 402.00, 34, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(167, 42, 2, 10, 26, 'Pantalón Cargo', 'Descripción de Pantalón Cargo', 218.00, 24, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(168, 43, 2, 10, 27, 'Blusa Elegante', 'Descripción de Blusa Elegante', 231.00, 36, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(169, 44, 2, 10, 27, 'Jeans Skinny', 'Descripción de Jeans Skinny', 188.00, 11, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(170, 45, 2, 10, 27, 'Vestido Casual', 'Descripción de Vestido Casual', 179.00, 12, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(171, 46, 2, 10, 27, 'Top Deportivo', 'Descripción de Top Deportivo', 50.00, 24, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(172, 47, 2, 10, 27, 'Falda Midi', 'Descripción de Falda Midi', 149.00, 23, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(173, 48, 2, 10, 27, 'Chaqueta Mezclilla', 'Descripción de Chaqueta Mezclilla', 176.00, 25, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(174, 49, 2, 10, 27, 'Leggings Premium', 'Descripción de Leggings Premium', 472.00, 10, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(175, 50, 2, 10, 27, 'Cardigan', 'Descripción de Cardigan', 244.00, 15, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(176, 51, 2, 10, 27, 'Blusa Floral', 'Descripción de Blusa Floral', 149.00, 33, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(177, 52, 2, 10, 27, 'Pantalón Palazzo', 'Descripción de Pantalón Palazzo', 476.00, 38, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(178, 53, 2, 10, 28, 'Camiseta Niño', 'Descripción de Camiseta Niño', 184.00, 34, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(179, 54, 2, 10, 28, 'Pantalón Niño', 'Descripción de Pantalón Niño', 74.00, 45, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(180, 55, 2, 10, 28, 'Sudadera Infantil', 'Descripción de Sudadera Infantil', 298.00, 41, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(181, 56, 2, 10, 28, 'Shorts Niño', 'Descripción de Shorts Niño', 69.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(182, 57, 2, 10, 28, 'Falda Niña', 'Descripción de Falda Niña', 284.00, 39, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(183, 58, 2, 10, 28, 'Blusa Niña', 'Descripción de Blusa Niña', 497.00, 48, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(184, 59, 2, 10, 28, 'Pantalón Niña', 'Descripción de Pantalón Niña', 257.00, 31, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(185, 60, 2, 10, 28, 'Chaqueta Infantil', 'Descripción de Chaqueta Infantil', 57.00, 11, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(186, 61, 2, 10, 28, 'Buzo Infantil', 'Descripción de Buzo Infantil', 498.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(187, 62, 2, 10, 28, 'Leggings Niña', 'Descripción de Leggings Niña', 377.00, 45, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(188, 63, 2, 11, 29, 'Licuadora', 'Descripción de Licuadora', 315.00, 33, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(189, 64, 2, 11, 29, 'Microondas', 'Descripción de Microondas', 494.00, 45, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(190, 65, 2, 11, 29, 'Cafetera', 'Descripción de Cafetera', 60.00, 23, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(191, 66, 2, 11, 29, 'Tostador', 'Descripción de Tostador', 190.00, 23, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(192, 67, 2, 11, 29, 'Olla Arrocera', 'Descripción de Olla Arrocera', 313.00, 27, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(193, 68, 2, 11, 29, 'Plancha Eléctrica', 'Descripción de Plancha Eléctrica', 84.00, 32, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(194, 69, 2, 11, 29, 'Freidora Aire', 'Descripción de Freidora Aire', 375.00, 46, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(195, 70, 2, 11, 29, 'Batidora', 'Descripción de Batidora', 135.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(196, 71, 2, 11, 29, 'Exprimidor', 'Descripción de Exprimidor', 181.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(197, 72, 2, 11, 29, 'Hervidor Eléctrico', 'Descripción de Hervidor Eléctrico', 254.00, 28, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(198, 73, 2, 11, 30, 'Sofá 3 Puestos', 'Descripción de Sofá 3 Puestos', 493.00, 20, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(199, 74, 2, 11, 30, 'Mesa Comedor', 'Descripción de Mesa Comedor', 437.00, 22, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(200, 75, 2, 11, 30, 'Silla Oficina', 'Descripción de Silla Oficina', 451.00, 41, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(201, 76, 2, 11, 30, 'Cama Queen', 'Descripción de Cama Queen', 223.00, 42, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(202, 77, 2, 11, 30, 'Closet Madera', 'Descripción de Closet Madera', 169.00, 45, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(203, 78, 2, 11, 30, 'Biblioteca', 'Descripción de Biblioteca', 106.00, 16, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(204, 79, 2, 11, 30, 'Mesita Noche', 'Descripción de Mesita Noche', 341.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(205, 80, 2, 11, 30, 'Escritorio', 'Descripción de Escritorio', 471.00, 25, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(206, 81, 2, 11, 30, 'Estantería', 'Descripción de Estantería', 200.00, 16, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(207, 82, 2, 11, 30, 'Modular Tv', 'Descripción de Modular Tv', 211.00, 35, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(208, 83, 2, 11, 31, 'Cuadro Moderno', 'Descripción de Cuadro Moderno', 383.00, 11, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(209, 84, 2, 11, 31, 'Espejo Decorativo', 'Descripción de Espejo Decorativo', 104.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(210, 85, 2, 11, 31, 'Lámpara Piso', 'Descripción de Lámpara Piso', 353.00, 44, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(211, 86, 2, 11, 31, 'Cojín', 'Descripción de Cojín', 482.00, 15, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(212, 87, 2, 11, 31, 'Cortina Premium', 'Descripción de Cortina Premium', 193.00, 42, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(213, 88, 2, 11, 31, 'Tapete', 'Descripción de Tapete', 408.00, 49, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(214, 89, 2, 11, 31, 'Jarrón Cerámica', 'Descripción de Jarrón Cerámica', 260.00, 19, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(215, 90, 2, 11, 31, 'Alfombra', 'Descripción de Alfombra', 135.00, 13, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(216, 91, 2, 11, 31, 'Vela Aromática', 'Descripción de Vela Aromática', 462.00, 39, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(217, 92, 2, 11, 31, 'Reloj Pared', 'Descripción de Reloj Pared', 148.00, 15, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(218, 3, 3, 12, 32, 'Samsung Galaxy S21', 'Descripción de Samsung Galaxy S21', 407.00, 45, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(219, 4, 3, 12, 32, 'iPhone 13 Pro', 'Descripción de iPhone 13 Pro', 322.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(220, 5, 3, 12, 32, 'Xiaomi 12', 'Descripción de Xiaomi 12', 193.00, 32, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(221, 6, 3, 12, 32, 'Google Pixel 6', 'Descripción de Google Pixel 6', 413.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(222, 7, 3, 12, 32, 'OnePlus 9', 'Descripción de OnePlus 9', 64.00, 38, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(223, 8, 3, 12, 32, 'Motorola Edge', 'Descripción de Motorola Edge', 173.00, 50, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(224, 9, 3, 12, 32, 'Realme GT', 'Descripción de Realme GT', 61.00, 47, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(225, 10, 3, 12, 32, 'Nothing Phone', 'Descripción de Nothing Phone', 116.00, 46, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(226, 11, 3, 12, 32, 'Sony Xperia', 'Descripción de Sony Xperia', 292.00, 11, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(227, 12, 3, 12, 32, 'OPPO Find X3', 'Descripción de OPPO Find X3', 273.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-14 22:58:02'),
(228, 13, 3, 12, 33, 'MacBook Pro 16', 'Descripción de MacBook Pro 16', 114.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(229, 14, 3, 12, 33, 'Dell XPS 13', 'Descripción de Dell XPS 13', 55.00, 6, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(230, 15, 3, 12, 33, 'HP Pavilion 15', 'Descripción de HP Pavilion 15', 228.00, 32, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(231, 16, 3, 12, 33, 'Lenovo ThinkPad', 'Descripción de Lenovo ThinkPad', 288.00, 41, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(232, 17, 3, 12, 33, 'ASUS VivoBook', 'Descripción de ASUS VivoBook', 413.00, 12, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(233, 18, 3, 12, 33, 'Acer Aspire 5', 'Descripción de Acer Aspire 5', 496.00, 45, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(234, 19, 3, 12, 33, 'MSI GS66', 'Descripción de MSI GS66', 165.00, 23, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(235, 20, 3, 12, 33, 'Razer Blade', 'Descripción de Razer Blade', 207.00, 41, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(236, 21, 3, 12, 33, 'LG Gram', 'Descripción de LG Gram', 249.00, 30, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(237, 22, 3, 12, 33, 'ROG Gaming Laptop', 'Descripción de ROG Gaming Laptop', 85.00, 16, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(238, 23, 3, 12, 34, 'Cargador Rápido', 'Descripción de Cargador Rápido', 494.00, 44, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(239, 24, 3, 12, 34, 'Cable USB-C', 'Descripción de Cable USB-C', 221.00, 44, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(240, 25, 3, 12, 34, 'Protector Pantalla', 'Descripción de Protector Pantalla', 496.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(241, 26, 3, 12, 34, 'Funda Teléfono', 'Descripción de Funda Teléfono', 310.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(242, 27, 3, 12, 34, 'Audífonos Inalámbricos', 'Descripción de Audífonos Inalámbricos', 288.00, 19, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(243, 28, 3, 12, 34, 'Power Bank', 'Descripción de Power Bank', 496.00, 43, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(244, 29, 3, 12, 34, 'Soporte Móvil', 'Descripción de Soporte Móvil', 170.00, 44, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(245, 30, 3, 12, 34, 'Protector Cámara', 'Descripción de Protector Cámara', 431.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(246, 31, 3, 12, 34, 'Anillo Soporte', 'Descripción de Anillo Soporte', 203.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(247, 32, 3, 12, 34, 'Cable HDMI', 'Descripción de Cable HDMI', 278.00, 12, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(248, 33, 3, 13, 35, 'Camiseta Básica', 'Descripción de Camiseta Básica', 332.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(249, 34, 3, 13, 35, 'Pantalón Denim', 'Descripción de Pantalón Denim', 118.00, 46, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(250, 35, 3, 13, 35, 'Camisa Social', 'Descripción de Camisa Social', 192.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(251, 36, 3, 13, 35, 'Polo Premium', 'Descripción de Polo Premium', 308.00, 12, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(252, 37, 3, 13, 35, 'Shorts Deportivos', 'Descripción de Shorts Deportivos', 367.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(253, 38, 3, 13, 35, 'Chaqueta Casual', 'Descripción de Chaqueta Casual', 97.00, 50, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(254, 39, 3, 13, 35, 'Suéter Lana', 'Descripción de Suéter Lana', 476.00, 48, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(255, 40, 3, 13, 35, 'Bermudas', 'Descripción de Bermudas', 415.00, 41, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(256, 41, 3, 13, 35, 'Camiseta Deportiva', 'Descripción de Camiseta Deportiva', 425.00, 37, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(257, 42, 3, 13, 35, 'Pantalón Cargo', 'Descripción de Pantalón Cargo', 256.00, 10, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(258, 43, 3, 13, 36, 'Blusa Elegante', 'Descripción de Blusa Elegante', 139.00, 46, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(259, 44, 3, 13, 36, 'Jeans Skinny', 'Descripción de Jeans Skinny', 460.00, 10, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(260, 45, 3, 13, 36, 'Vestido Casual', 'Descripción de Vestido Casual', 86.00, 47, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(261, 46, 3, 13, 36, 'Top Deportivo', 'Descripción de Top Deportivo', 211.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(262, 47, 3, 13, 36, 'Falda Midi', 'Descripción de Falda Midi', 323.00, 27, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(263, 48, 3, 13, 36, 'Chaqueta Mezclilla', 'Descripción de Chaqueta Mezclilla', 69.00, 21, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(264, 49, 3, 13, 36, 'Leggings Premium', 'Descripción de Leggings Premium', 273.00, 21, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(265, 50, 3, 13, 36, 'Cardigan', 'Descripción de Cardigan', 281.00, 42, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(266, 51, 3, 13, 36, 'Blusa Floral', 'Descripción de Blusa Floral', 498.00, 26, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(267, 52, 3, 13, 36, 'Pantalón Palazzo', 'Descripción de Pantalón Palazzo', 432.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(268, 53, 3, 13, 37, 'Camiseta Niño', 'Descripción de Camiseta Niño', 163.00, 17, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(269, 54, 3, 13, 37, 'Pantalón Niño', 'Descripción de Pantalón Niño', 179.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(270, 55, 3, 13, 37, 'Sudadera Infantil', 'Descripción de Sudadera Infantil', 318.00, 47, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(271, 56, 3, 13, 37, 'Shorts Niño', 'Descripción de Shorts Niño', 141.00, 38, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(272, 57, 3, 13, 37, 'Falda Niña', 'Descripción de Falda Niña', 129.00, 26, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(273, 58, 3, 13, 37, 'Blusa Niña', 'Descripción de Blusa Niña', 371.00, 27, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(274, 59, 3, 13, 37, 'Pantalón Niña', 'Descripción de Pantalón Niña', 423.00, 20, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(275, 60, 3, 13, 37, 'Chaqueta Infantil', 'Descripción de Chaqueta Infantil', 136.00, 25, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(276, 61, 3, 13, 37, 'Buzo Infantil', 'Descripción de Buzo Infantil', 399.00, 13, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(277, 62, 3, 13, 37, 'Leggings Niña', 'Descripción de Leggings Niña', 431.00, 14, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(278, 63, 3, 14, 38, 'Licuadora', 'Descripción de Licuadora', 171.00, 50, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(279, 64, 3, 14, 38, 'Microondas', 'Descripción de Microondas', 408.00, 12, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(280, 65, 3, 14, 38, 'Cafetera', 'Descripción de Cafetera', 483.00, 10, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(281, 66, 3, 14, 38, 'Tostador', 'Descripción de Tostador', 59.00, 48, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(282, 67, 3, 14, 38, 'Olla Arrocera', 'Descripción de Olla Arrocera', 258.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(283, 68, 3, 14, 38, 'Plancha Eléctrica', 'Descripción de Plancha Eléctrica', 54.00, 34, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(284, 69, 3, 14, 38, 'Freidora Aire', 'Descripción de Freidora Aire', 255.00, 44, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(285, 70, 3, 14, 38, 'Batidora', 'Descripción de Batidora', 432.00, 48, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(286, 71, 3, 14, 38, 'Exprimidor', 'Descripción de Exprimidor', 157.00, 17, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(287, 72, 3, 14, 38, 'Hervidor Eléctrico', 'Descripción de Hervidor Eléctrico', 218.00, 39, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(288, 73, 3, 14, 39, 'Sofá 3 Puestos', 'Descripción de Sofá 3 Puestos', 276.00, 35, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(289, 74, 3, 14, 39, 'Mesa Comedor', 'Descripción de Mesa Comedor', 410.00, 31, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(290, 75, 3, 14, 39, 'Silla Oficina', 'Descripción de Silla Oficina', 132.00, 43, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(291, 76, 3, 14, 39, 'Cama Queen', 'Descripción de Cama Queen', 100.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(292, 77, 3, 14, 39, 'Closet Madera', 'Descripción de Closet Madera', 105.00, 40, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(293, 78, 3, 14, 39, 'Biblioteca', 'Descripción de Biblioteca', 352.00, 31, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(294, 79, 3, 14, 39, 'Mesita Noche', 'Descripción de Mesita Noche', 336.00, 27, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(295, 80, 3, 14, 39, 'Escritorio', 'Descripción de Escritorio', 342.00, 24, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(296, 81, 3, 14, 39, 'Estantería', 'Descripción de Estantería', 266.00, 50, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(297, 82, 3, 14, 39, 'Modular Tv', 'Descripción de Modular Tv', 476.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(298, 83, 3, 14, 40, 'Cuadro Moderno', 'Descripción de Cuadro Moderno', 380.00, 24, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(299, 84, 3, 14, 40, 'Espejo Decorativo', 'Descripción de Espejo Decorativo', 218.00, 41, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(300, 85, 3, 14, 40, 'Lámpara Piso', 'Descripción de Lámpara Piso', 294.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(301, 86, 3, 14, 40, 'Cojín', 'Descripción de Cojín', 112.00, 48, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(302, 87, 3, 14, 40, 'Cortina Premium', 'Descripción de Cortina Premium', 211.00, 18, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(303, 88, 3, 14, 40, 'Tapete', 'Descripción de Tapete', 449.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(304, 89, 3, 14, 40, 'Jarrón Cerámica', 'Descripción de Jarrón Cerámica', 349.00, 46, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(305, 90, 3, 14, 40, 'Alfombra', 'Descripción de Alfombra', 87.00, 46, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(306, 91, 3, 14, 40, 'Vela Aromática', 'Descripción de Vela Aromática', 478.00, 15, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(307, 92, 3, 14, 40, 'Reloj Pared', 'Descripción de Reloj Pared', 203.00, 49, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(308, 13, 4, 6, 11, 'Samsung Galaxy S21', 'Descripción de Samsung Galaxy S21', 350.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(309, 14, 4, 6, 11, 'iPhone 13 Pro', 'Descripción de iPhone 13 Pro', 460.00, 34, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(310, 15, 4, 6, 11, 'Xiaomi 12', 'Descripción de Xiaomi 12', 381.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(311, 16, 4, 6, 11, 'Google Pixel 6', 'Descripción de Google Pixel 6', 146.00, 47, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(312, 17, 4, 6, 11, 'OnePlus 9', 'Descripción de OnePlus 9', 452.00, 35, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(313, 18, 4, 6, 11, 'Motorola Edge', 'Descripción de Motorola Edge', 90.00, 15, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(314, 19, 4, 6, 11, 'Realme GT', 'Descripción de Realme GT', 410.00, 14, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(315, 20, 4, 6, 11, 'Nothing Phone', 'Descripción de Nothing Phone', 147.00, 45, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(316, 21, 4, 6, 11, 'Sony Xperia', 'Descripción de Sony Xperia', 480.00, 16, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(317, 22, 4, 6, 11, 'OPPO Find X3', 'Descripción de OPPO Find X3', 186.00, 31, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(318, 23, 4, 6, 12, 'MacBook Pro 16', 'Descripción de MacBook Pro 16', 50.00, 31, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(319, 24, 4, 6, 12, 'Dell XPS 13', 'Descripción de Dell XPS 13', 355.00, 37, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(320, 25, 4, 6, 12, 'HP Pavilion 15', 'Descripción de HP Pavilion 15', 125.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(321, 26, 4, 6, 12, 'Lenovo ThinkPad', 'Descripción de Lenovo ThinkPad', 286.00, 18, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(322, 27, 4, 6, 12, 'ASUS VivoBook', 'Descripción de ASUS VivoBook', 97.00, 28, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(323, 28, 4, 6, 12, 'Acer Aspire 5', 'Descripción de Acer Aspire 5', 68.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(324, 29, 4, 6, 12, 'MSI GS66', 'Descripción de MSI GS66', 113.00, 40, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(325, 30, 4, 6, 12, 'Razer Blade', 'Descripción de Razer Blade', 477.00, 50, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(326, 31, 4, 6, 12, 'LG Gram', 'Descripción de LG Gram', 59.00, 27, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(327, 32, 4, 6, 12, 'ROG Gaming Laptop', 'Descripción de ROG Gaming Laptop', 112.00, 47, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(328, 33, 4, 6, 41, 'Cargador Rápido', 'Descripción de Cargador Rápido', 235.00, 15, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(329, 34, 4, 6, 41, 'Cable USB-C', 'Descripción de Cable USB-C', 189.00, 44, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(330, 35, 4, 6, 41, 'Protector Pantalla', 'Descripción de Protector Pantalla', 461.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(331, 36, 4, 6, 41, 'Funda Teléfono', 'Descripción de Funda Teléfono', 315.00, 50, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(332, 37, 4, 6, 41, 'Audífonos Inalámbricos', 'Descripción de Audífonos Inalámbricos', 69.00, 30, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(333, 38, 4, 6, 41, 'Power Bank', 'Descripción de Power Bank', 195.00, 31, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(334, 39, 4, 6, 41, 'Soporte Móvil', 'Descripción de Soporte Móvil', 211.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(335, 40, 4, 6, 41, 'Protector Cámara', 'Descripción de Protector Cámara', 109.00, 44, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(336, 41, 4, 6, 41, 'Anillo Soporte', 'Descripción de Anillo Soporte', 465.00, 39, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(337, 42, 4, 6, 41, 'Cable HDMI', 'Descripción de Cable HDMI', 395.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(338, 43, 4, 7, 42, 'Camiseta Básica', 'Descripción de Camiseta Básica', 481.00, 12, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(339, 44, 4, 7, 42, 'Pantalón Denim', 'Descripción de Pantalón Denim', 235.00, 30, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(340, 45, 4, 7, 42, 'Camisa Social', 'Descripción de Camisa Social', 154.00, 21, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(341, 46, 4, 7, 42, 'Polo Premium', 'Descripción de Polo Premium', 392.00, 24, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(342, 47, 4, 7, 42, 'Shorts Deportivos', 'Descripción de Shorts Deportivos', 106.00, 47, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(343, 48, 4, 7, 42, 'Chaqueta Casual', 'Descripción de Chaqueta Casual', 404.00, 18, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(344, 49, 4, 7, 42, 'Suéter Lana', 'Descripción de Suéter Lana', 326.00, 28, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(345, 50, 4, 7, 42, 'Bermudas', 'Descripción de Bermudas', 242.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(346, 51, 4, 7, 42, 'Camiseta Deportiva', 'Descripción de Camiseta Deportiva', 459.00, 45, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(347, 52, 4, 7, 42, 'Pantalón Cargo', 'Descripción de Pantalón Cargo', 267.00, 34, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(348, 53, 4, 7, 43, 'Blusa Elegante', 'Descripción de Blusa Elegante', 99.00, 30, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(349, 54, 4, 7, 43, 'Jeans Skinny', 'Descripción de Jeans Skinny', 286.00, 15, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(350, 55, 4, 7, 43, 'Vestido Casual', 'Descripción de Vestido Casual', 184.00, 13, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(351, 56, 4, 7, 43, 'Top Deportivo', 'Descripción de Top Deportivo', 222.00, 35, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(352, 57, 4, 7, 43, 'Falda Midi', 'Descripción de Falda Midi', 152.00, 20, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(353, 58, 4, 7, 43, 'Chaqueta Mezclilla', 'Descripción de Chaqueta Mezclilla', 349.00, 41, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(354, 59, 4, 7, 43, 'Leggings Premium', 'Descripción de Leggings Premium', 326.00, 19, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(355, 60, 4, 7, 43, 'Cardigan', 'Descripción de Cardigan', 73.00, 16, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(356, 61, 4, 7, 43, 'Blusa Floral', 'Descripción de Blusa Floral', 58.00, 16, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(357, 62, 4, 7, 43, 'Pantalón Palazzo', 'Descripción de Pantalón Palazzo', 494.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(358, 63, 4, 7, 44, 'Camiseta Niño', 'Descripción de Camiseta Niño', 126.00, 12, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(359, 64, 4, 7, 44, 'Pantalón Niño', 'Descripción de Pantalón Niño', 333.00, 50, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(360, 65, 4, 7, 44, 'Sudadera Infantil', 'Descripción de Sudadera Infantil', 442.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(361, 66, 4, 7, 44, 'Shorts Niño', 'Descripción de Shorts Niño', 56.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(362, 67, 4, 7, 44, 'Falda Niña', 'Descripción de Falda Niña', 397.00, 28, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(363, 68, 4, 7, 44, 'Blusa Niña', 'Descripción de Blusa Niña', 374.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(364, 69, 4, 7, 44, 'Pantalón Niña', 'Descripción de Pantalón Niña', 315.00, 34, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(365, 70, 4, 7, 44, 'Chaqueta Infantil', 'Descripción de Chaqueta Infantil', 373.00, 13, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(366, 71, 4, 7, 44, 'Buzo Infantil', 'Descripción de Buzo Infantil', 133.00, 39, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(367, 72, 4, 7, 44, 'Leggings Niña', 'Descripción de Leggings Niña', 216.00, 35, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(368, 73, 4, 8, 15, 'Licuadora', 'Descripción de Licuadora', 404.00, 10, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(369, 74, 4, 8, 15, 'Microondas', 'Descripción de Microondas', 123.00, 17, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(370, 75, 4, 8, 15, 'Cafetera', 'Descripción de Cafetera', 399.00, 37, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02');
INSERT INTO `productos` (`id`, `numero_producto`, `tenant_id`, `categoria_id`, `subcategoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(371, 76, 4, 8, 15, 'Tostador', 'Descripción de Tostador', 445.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(372, 77, 4, 8, 15, 'Olla Arrocera', 'Descripción de Olla Arrocera', 241.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(373, 78, 4, 8, 15, 'Plancha Eléctrica', 'Descripción de Plancha Eléctrica', 54.00, 19, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(374, 79, 4, 8, 15, 'Freidora Aire', 'Descripción de Freidora Aire', 173.00, 15, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(375, 80, 4, 8, 15, 'Batidora', 'Descripción de Batidora', 142.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(376, 81, 4, 8, 15, 'Exprimidor', 'Descripción de Exprimidor', 63.00, 19, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(377, 82, 4, 8, 15, 'Hervidor Eléctrico', 'Descripción de Hervidor Eléctrico', 214.00, 37, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(378, 83, 4, 8, 45, 'Sofá 3 Puestos', 'Descripción de Sofá 3 Puestos', 260.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(379, 84, 4, 8, 45, 'Mesa Comedor', 'Descripción de Mesa Comedor', 420.00, 32, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(380, 85, 4, 8, 45, 'Silla Oficina', 'Descripción de Silla Oficina', 383.00, 10, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(381, 86, 4, 8, 45, 'Cama Queen', 'Descripción de Cama Queen', 414.00, 15, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:02'),
(382, 87, 4, 8, 45, 'Closet Madera', 'Descripción de Closet Madera', 339.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(383, 88, 4, 8, 45, 'Biblioteca', 'Descripción de Biblioteca', 60.00, 35, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(384, 89, 4, 8, 45, 'Mesita Noche', 'Descripción de Mesita Noche', 373.00, 19, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(385, 90, 4, 8, 45, 'Escritorio', 'Descripción de Escritorio', 479.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(386, 91, 4, 8, 45, 'Estantería', 'Descripción de Estantería', 288.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(387, 92, 4, 8, 45, 'Modular Tv', 'Descripción de Modular Tv', 385.00, 48, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(388, 93, 4, 8, 46, 'Cuadro Moderno', 'Descripción de Cuadro Moderno', 387.00, 13, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(389, 94, 4, 8, 46, 'Espejo Decorativo', 'Descripción de Espejo Decorativo', 86.00, 47, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(390, 95, 4, 8, 46, 'Lámpara Piso', 'Descripción de Lámpara Piso', 493.00, 13, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(391, 96, 4, 8, 46, 'Cojín', 'Descripción de Cojín', 186.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(392, 97, 4, 8, 46, 'Cortina Premium', 'Descripción de Cortina Premium', 99.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(393, 98, 4, 8, 46, 'Tapete', 'Descripción de Tapete', 286.00, 38, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(394, 99, 4, 8, 46, 'Jarrón Cerámica', 'Descripción de Jarrón Cerámica', 131.00, 13, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(395, 100, 4, 8, 46, 'Alfombra', 'Descripción de Alfombra', 339.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(396, 101, 4, 8, 46, 'Vela Aromática', 'Descripción de Vela Aromática', 155.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(397, 102, 4, 8, 46, 'Reloj Pared', 'Descripción de Reloj Pared', 220.00, 28, NULL, 1, '2026-01-11 20:03:11', '2026-01-14 22:58:03'),
(398, 93, 2, 9, 25, 'ACID MANTLE BABY', 'caja x 12', 21000.00, 353, 'public/tenants/2/images/upl_6966d29d1663f_Captura_de_pantalla_2025-10-29_a_la_s__11.13.09___a.__m..png', 1, '2026-01-13 23:17:49', '2026-01-15 01:57:20'),
(399, 1, 6, 21, 59, 'acetaminofen', 'caja x 50', 7000.00, 3590, 'public/tenants/6/images/upl_6967fcba29c3b_WhatsApp_Image_2025-12-12_at_10.59.48_AM__1.jpeg', 1, '2026-01-14 20:29:46', '2026-01-15 00:26:19'),
(400, 2, 6, 21, 59, 'amoxicilina', 'caja x30', 5500.00, 4893, 'public/tenants/6/images/upl_6967fce2434ea_WhatsApp_Image_2025-12-12_at_11.01.41_AM.jpeg', 0, '2026-01-14 20:30:26', '2026-01-15 01:38:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategorias`
--

CREATE TABLE `subcategorias` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL DEFAULT 1,
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

INSERT INTO `subcategorias` (`id`, `tenant_id`, `categoria_id`, `nombre`, `descripcion`, `activa`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 1, 'Smartphones', 'Teléfonos inteligentes de última generación', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(2, 1, 1, 'Laptops', 'Computadoras portátiles para trabajo y entretenimiento', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(3, 1, 2, 'Hombre', 'Ropa y accesorios para caballeros', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(4, 1, 2, 'Mujer', 'Ropa y accesorios para damas', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(5, 1, 3, 'Cocina', 'Electrodomésticos y utensilios de cocina', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(6, 1, 3, 'Dormitorio', 'Muebles y accesorios para dormitorio', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(7, 2, 4, 'ConsultorÝa', NULL, 1, '2026-01-09 21:42:09', '2026-01-09 21:42:09'),
(8, 3, 5, 'Mayorista', NULL, 1, '2026-01-09 21:42:53', '2026-01-09 21:42:53'),
(9, 2, 4, 'Desarrollo', NULL, 1, '2026-01-09 22:31:51', '2026-01-09 22:31:51'),
(10, 3, 5, 'Electr¾nica', NULL, 1, '2026-01-09 22:31:51', '2026-01-09 22:31:51'),
(11, 4, 6, 'Smartphones', 'Teléfonos inteligentes', 1, '2026-01-09 22:53:09', '2026-01-09 22:53:09'),
(12, 4, 6, 'Laptops', 'Computadoras portátiles', 1, '2026-01-09 22:53:09', '2026-01-09 22:53:09'),
(13, 4, 7, 'Hombre', 'Ropa para caballeros', 1, '2026-01-09 22:53:09', '2026-01-09 22:53:09'),
(14, 4, 7, 'Mujer', 'Ropa para damas', 1, '2026-01-09 22:53:09', '2026-01-09 22:53:09'),
(15, 4, 8, 'Cocina', 'Electrodomésticos de cocina', 1, '2026-01-09 22:53:09', '2026-01-09 22:53:09'),
(16, 4, 8, 'Dormitorio', 'Muebles de dormitorio', 1, '2026-01-09 22:53:09', '2026-01-09 22:53:09'),
(17, 1, 1, 'Accesorios', 'Accesorios electrónicos', 1, '2026-01-11 17:59:51', '2026-01-11 17:59:51'),
(18, 1, 2, 'Hombres', 'Ropa para hombres', 1, '2026-01-11 17:59:51', '2026-01-11 17:59:51'),
(19, 1, 2, 'Mujeres', 'Ropa para mujeres', 1, '2026-01-11 17:59:51', '2026-01-11 17:59:51'),
(20, 1, 2, 'Niños', 'Ropa para niños', 1, '2026-01-11 17:59:51', '2026-01-11 17:59:51'),
(21, 1, 3, 'Muebles', 'Muebles para el hogar', 1, '2026-01-11 17:59:51', '2026-01-11 17:59:51'),
(22, 1, 3, 'Decoración', 'Artículos de decoración', 1, '2026-01-11 17:59:51', '2026-01-11 17:59:51'),
(23, 2, 9, 'Smartphones', 'Teléfonos móviles', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(24, 2, 9, 'Laptops', 'Computadoras portátiles', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(25, 2, 9, 'Accesorios', 'Accesorios electrónicos', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(26, 2, 10, 'Hombres', 'Ropa para hombres', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(27, 2, 10, 'Mujeres', 'Ropa para mujeres', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(28, 2, 10, 'Niños', 'Ropa para niños', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(29, 2, 11, 'Cocina', 'Artículos de cocina', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(30, 2, 11, 'Muebles', 'Muebles para el hogar', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(31, 2, 11, 'Decoración', 'Artículos de decoración', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(32, 3, 12, 'Smartphones', 'Teléfonos móviles', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(33, 3, 12, 'Laptops', 'Computadoras portátiles', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(34, 3, 12, 'Accesorios', 'Accesorios electrónicos', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(35, 3, 13, 'Hombres', 'Ropa para hombres', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(36, 3, 13, 'Mujeres', 'Ropa para mujeres', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(37, 3, 13, 'Niños', 'Ropa para niños', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(38, 3, 14, 'Cocina', 'Artículos de cocina', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(39, 3, 14, 'Muebles', 'Muebles para el hogar', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(40, 3, 14, 'Decoración', 'Artículos de decoración', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(41, 4, 6, 'Accesorios', 'Accesorios electrónicos', 1, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(42, 4, 7, 'Hombres', 'Ropa para hombres', 1, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(43, 4, 7, 'Mujeres', 'Ropa para mujeres', 1, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(44, 4, 7, 'Niños', 'Ropa para niños', 1, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(45, 4, 8, 'Muebles', 'Muebles para el hogar', 1, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(46, 4, 8, 'Decoración', 'Artículos de decoración', 1, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(47, 5, 15, 'Smartphones', 'Teléfonos inteligentes', 1, '2026-01-14 00:20:34', '2026-01-14 00:20:34'),
(48, 5, 15, 'Laptops', 'Computadoras portátiles', 1, '2026-01-14 00:20:34', '2026-01-14 00:20:34'),
(49, 5, 16, 'Hombre', 'Ropa para caballeros', 1, '2026-01-14 00:20:34', '2026-01-14 00:20:34'),
(50, 5, 16, 'Mujer', 'Ropa para damas', 1, '2026-01-14 00:20:34', '2026-01-14 00:20:34'),
(51, 5, 17, 'Cocina', 'Electrodomésticos de cocina', 1, '2026-01-14 00:20:34', '2026-01-14 00:20:34'),
(52, 5, 17, 'Dormitorio', 'Muebles de dormitorio', 1, '2026-01-14 00:20:34', '2026-01-14 00:20:34'),
(53, 6, 18, 'Smartphones', 'Teléfonos inteligentes', 1, '2026-01-14 00:25:47', '2026-01-14 00:25:47'),
(54, 6, 18, 'Laptops', 'Computadoras portátiles', 1, '2026-01-14 00:25:47', '2026-01-14 00:25:47'),
(55, 6, 19, 'Hombre', 'Ropa para caballeros', 1, '2026-01-14 00:25:47', '2026-01-14 00:25:47'),
(56, 6, 19, 'Mujer', 'Ropa para damas', 1, '2026-01-14 00:25:47', '2026-01-14 00:25:47'),
(57, 6, 20, 'Cocina', 'Electrodomésticos de cocina', 1, '2026-01-14 00:25:47', '2026-01-14 00:25:47'),
(58, 6, 20, 'Dormitorio', 'Muebles de dormitorio', 1, '2026-01-14 00:25:47', '2026-01-14 00:25:47'),
(59, 6, 21, 'genericos', '', 1, '2026-01-14 20:28:54', '2026-01-14 20:28:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tenants`
--

CREATE TABLE `tenants` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `titulo_empresa` varchar(200) DEFAULT NULL,
  `slug` varchar(50) NOT NULL,
  `whatsapp_phone` varchar(20) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `tema` varchar(50) DEFAULT 'default',
  `tema_color` varchar(50) DEFAULT 'azul',
  `estado` enum('activo','inactivo','bloqueado') DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tenants`
--

INSERT INTO `tenants` (`id`, `nombre`, `titulo_empresa`, `slug`, `whatsapp_phone`, `logo`, `tema`, `tema_color`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Tienda Default', 'Tienda Default', 'default', '+573112969569', NULL, 'claro', 'grafito', 'activo', '2026-01-09 20:44:02', '2026-01-12 00:31:14'),
(2, 'Mauricio', 'Mauricio', 'mauricio', '+57573112969569', NULL, 'oscuro', 'rojo', 'activo', '2026-01-09 21:11:21', '2026-01-14 19:00:48'),
(3, 'Distribuciones EBS', 'Distribuciones EBS', 'distribuciones-ebs', '+573112969569', NULL, 'claro', 'gris', 'activo', '2026-01-09 21:11:21', '2026-01-13 23:21:33'),
(4, 'Tech Store - Prueba', 'Tech Store - Prueba', 'tech-store', '+573334567890', NULL, 'oscuro', 'naranja', 'activo', '2026-01-09 22:53:09', '2026-01-10 19:43:06'),
(5, 'larause', 'larause', 'larause', '3112969569', NULL, 'oscuro', 'petroleo', 'activo', '2026-01-14 00:20:34', '2026-01-14 00:20:34'),
(6, 'la77', 'la77', 'la77', '+573004583117', 'public/tenants/la77/logo.jpg', 'oscuro', 'morado', 'activo', '2026-01-14 00:25:47', '2026-01-14 23:20:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `rol` enum('superadmin','admin','editor','viewer','usuario') NOT NULL DEFAULT 'usuario',
  `activo` int(11) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `tenant_id`, `usuario`, `email`, `password`, `nombre`, `rol`, `activo`, `fecha_creacion`, `ultimo_acceso`) VALUES
(1, 1, 'admin', 'admin@tienda.local', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'Administrador', 'admin', 1, '2025-12-24 17:35:46', '2026-01-14 23:21:24'),
(2, 4, 'admin_tech', 'admin@techstore.local', 'e937eb7cc144efab9efc6ea003bf88b027a3d7061d283365fe23f1f660740994', 'Tech Store - Prueba', 'admin', 1, '2026-01-09 22:53:09', '2026-01-13 23:20:46'),
(3, NULL, 'superadmin', 'superadmin@sistema.local', 'd357150517d3e65ae84985f7b705ad99fdc38372a22ecea0cecaf8aaf820a249', 'Super Administrador', 'superadmin', 1, '2026-01-09 23:49:15', '2026-01-14 23:20:37'),
(4, 5, 'larause', 'larause@gmail.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'larause', 'admin', 1, '2026-01-14 00:20:34', NULL),
(5, 6, 'admin', '3marin7@gmail.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'la77', 'admin', 1, '2026-01-14 00:25:47', '2026-01-14 22:35:49');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_tenant_carrito` (`tenant_id`,`session_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `idx_tenant_carrito` (`tenant_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_tenant_categoria_nombre` (`tenant_id`,`nombre`),
  ADD KEY `idx_tenant_categorias` (`tenant_id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_tenant_cliente_usuario` (`tenant_id`,`usuario`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_whatsapp` (`whatsapp`),
  ADD KEY `idx_usuario` (`usuario`),
  ADD KEY `idx_tenant_clientes` (`tenant_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha` (`fecha_creacion`),
  ADD KEY `idx_tenant_pedidos` (`tenant_id`);

--
-- Indices de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `idx_pedido` (`pedido_id`),
  ADD KEY `idx_tenant_pedido_detalles` (`tenant_id`);

--
-- Indices de la tabla `pedido_historial`
--
ALTER TABLE `pedido_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_historial_pedido` (`pedido_id`),
  ADD KEY `idx_historial_fecha` (`fecha`),
  ADD KEY `idx_tenant_pedido_historial` (`tenant_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_subcategoria` (`subcategoria_id`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_tenant_productos` (`tenant_id`);

--
-- Indices de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subcategoria` (`categoria_id`,`nombre`),
  ADD KEY `idx_tenant_subcategorias` (`tenant_id`);

--
-- Indices de la tabla `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `slug_unique` (`slug`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_tenant_usuario` (`tenant_id`,`usuario`),
  ADD UNIQUE KEY `uk_tenant_email` (`tenant_id`,`email`),
  ADD KEY `idx_usuario` (`usuario`),
  ADD KEY `idx_tenant_usuarios` (`tenant_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=244;

--
-- AUTO_INCREMENT de la tabla `pedido_historial`
--
ALTER TABLE `pedido_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=401;

--
-- AUTO_INCREMENT de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_carrito_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `fk_categorias_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_clientes_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

--
-- Filtros para la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD CONSTRAINT `fk_pedido_detalles_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_detalles_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `pedido_historial`
--
ALTER TABLE `pedido_historial`
  ADD CONSTRAINT `fk_pedido_historial_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_historial_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`subcategoria_id`) REFERENCES `subcategorias` (`id`);

--
-- Filtros para la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  ADD CONSTRAINT `fk_subcategorias_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subcategorias_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
