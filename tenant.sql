-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-01-2026 a las 16:47:31
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
(34, 1, '2mc3gpg6903sbc126jgk5l25fc', 40, 1, '2026-01-12 00:13:09');

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
(14, 3, 'Hogar', 'Artículos para el hogar', 1, '2026-01-11 20:03:10', '2026-01-11 20:03:10');

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
  `activo` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `tenant_id`, `usuario`, `password`, `nombre`, `email`, `telefono`, `whatsapp`, `ciudad`, `direccion`, `fecha_registro`, `activo`) VALUES
(1, 1, NULL, NULL, 'eddd', 'admin@colsanitas.com', '3112969569', '+57311296596', 'Suba', '140a76 Carrera 108a', '2025-12-24 18:32:48', 1),
(2, 1, 'usuario1', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c', 'Mauricio', 'admin@colsanitas.com', '3112969569', '+57311296596', 'Suba', '140a76 Carrera 108a', '2025-12-24 19:10:28', 1),
(3, 1, 'usuario2', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c', 'María López', 'usuario2@tienda.local', '3009876543', '573009876543', 'Medellín', 'Carrera 45 #50-60', '2025-12-24 19:10:28', 1),
(4, 1, NULL, NULL, 'Mauricio', 'admin@colsanitas.com', '3112969569', '+57311296596', 'Suba', '140a76 Carrera 108a', '2025-12-24 19:59:24', 1),
(5, 1, NULL, NULL, 'Mauricio', 'mauriciolarause@gmail.com', '3112969569', '+573112969569', 'Suba', '140a76 Carrera 108a', '2025-12-25 00:34:50', 1),
(6, 1, NULL, NULL, 'sdf', 'mauriciolarause@gmail.com', '311223', '+57311296596', 'Bogotá', 'Cra. 108a #140a-76', '2026-01-05 22:51:37', 1),
(7, 1, 'invitado_8f2949ea1e', 'a143fcd6a92eb5af42dd4fed4f86b19f19f27849851bb2b5d567070d5fd97578', 'Efeff', '', '', '+573004583117', NULL, NULL, '2026-01-08 16:58:57', 1),
(8, 2, NULL, NULL, 'Carlos Mendez', 'carlos@mauricio.local', '3115555555', '+573115555555', 'Bogotß', 'Cll 50 # 10-20', '2026-01-09 22:35:53', 1),
(9, 2, NULL, NULL, 'Ana Gonzßlez', 'ana@mauricio.local', '3116666666', '+573116666666', 'Bogotß', 'Cll 80 # 15-30', '2026-01-09 22:35:53', 1),
(10, 3, NULL, NULL, 'Distribuciones XYZ', 'info@distxyz.com', '3117777777', '+573117777777', 'MedellÝn', 'Cra 45 # 50-60', '2026-01-09 22:36:04', 1),
(11, 3, NULL, NULL, 'Comercial ABC', 'ventas@comercialab.com', '3118888888', '+573118888888', 'MedellÝn', 'Cra 50 # 60-70', '2026-01-09 22:36:04', 1),
(12, 1, NULL, NULL, 'Cliente Prueba 1', 'cliente0@test.com', '', '5738905744', NULL, 'Dirección 1, Apto 423', '2026-01-11 20:03:09', 1),
(13, 1, NULL, NULL, 'Cliente Prueba 2', 'cliente1@test.com', '', '5737146005', NULL, 'Dirección 2, Apto 11', '2026-01-11 20:03:09', 1),
(14, 1, NULL, NULL, 'Cliente Prueba 3', 'cliente2@test.com', '', '5730526354', NULL, 'Dirección 3, Apto 484', '2026-01-11 20:03:09', 1),
(15, 1, NULL, NULL, 'Cliente Prueba 4', 'cliente3@test.com', '', '5737827828', NULL, 'Dirección 4, Apto 292', '2026-01-11 20:03:09', 1),
(16, 1, NULL, NULL, 'Cliente Prueba 5', 'cliente4@test.com', '', '5731792418', NULL, 'Dirección 5, Apto 388', '2026-01-11 20:03:09', 1),
(17, 1, NULL, NULL, 'Cliente Prueba 6', 'cliente5@test.com', '', '5736135748', NULL, 'Dirección 6, Apto 189', '2026-01-11 20:03:09', 1),
(18, 1, NULL, NULL, 'Cliente Prueba 7', 'cliente6@test.com', '', '5731642480', NULL, 'Dirección 7, Apto 416', '2026-01-11 20:03:09', 1),
(19, 1, NULL, NULL, 'Cliente Prueba 8', 'cliente7@test.com', '', '5731528556', NULL, 'Dirección 8, Apto 24', '2026-01-11 20:03:09', 1),
(20, 1, NULL, NULL, 'Cliente Prueba 9', 'cliente8@test.com', '', '5737009233', NULL, 'Dirección 9, Apto 188', '2026-01-11 20:03:09', 1),
(21, 1, NULL, NULL, 'Cliente Prueba 10', 'cliente9@test.com', '', '5730805067', NULL, 'Dirección 10, Apto 244', '2026-01-11 20:03:10', 1),
(22, 2, NULL, NULL, 'Cliente Prueba 1', 'cliente0@test.com', '', '5733802814', NULL, 'Dirección 1, Apto 421', '2026-01-11 20:03:10', 1),
(23, 2, NULL, NULL, 'Cliente Prueba 2', 'cliente1@test.com', '', '5730199585', NULL, 'Dirección 2, Apto 8', '2026-01-11 20:03:10', 1),
(24, 2, NULL, NULL, 'Cliente Prueba 3', 'cliente2@test.com', '', '5734929459', NULL, 'Dirección 3, Apto 484', '2026-01-11 20:03:10', 1),
(25, 2, NULL, NULL, 'Cliente Prueba 4', 'cliente3@test.com', '', '5733501796', NULL, 'Dirección 4, Apto 10', '2026-01-11 20:03:10', 1),
(26, 2, NULL, NULL, 'Cliente Prueba 5', 'cliente4@test.com', '', '5734903718', NULL, 'Dirección 5, Apto 297', '2026-01-11 20:03:10', 1),
(27, 2, NULL, NULL, 'Cliente Prueba 6', 'cliente5@test.com', '', '5739280657', NULL, 'Dirección 6, Apto 438', '2026-01-11 20:03:10', 1),
(28, 2, NULL, NULL, 'Cliente Prueba 7', 'cliente6@test.com', '', '5737685776', NULL, 'Dirección 7, Apto 164', '2026-01-11 20:03:10', 1),
(29, 2, NULL, NULL, 'Cliente Prueba 8', 'cliente7@test.com', '', '5738097993', NULL, 'Dirección 8, Apto 460', '2026-01-11 20:03:10', 1),
(30, 2, NULL, NULL, 'Cliente Prueba 9', 'cliente8@test.com', '', '5739587901', NULL, 'Dirección 9, Apto 56', '2026-01-11 20:03:10', 1),
(31, 2, NULL, NULL, 'Cliente Prueba 10', 'cliente9@test.com', '', '5733887854', NULL, 'Dirección 10, Apto 386', '2026-01-11 20:03:10', 1),
(32, 3, NULL, NULL, 'Cliente Prueba 1', 'cliente0@test.com', '', '5732819337', NULL, 'Dirección 1, Apto 482', '2026-01-11 20:03:11', 1),
(33, 3, NULL, NULL, 'Cliente Prueba 2', 'cliente1@test.com', '', '5730928853', NULL, 'Dirección 2, Apto 394', '2026-01-11 20:03:11', 1),
(34, 3, NULL, NULL, 'Cliente Prueba 3', 'cliente2@test.com', '', '5732701970', NULL, 'Dirección 3, Apto 350', '2026-01-11 20:03:11', 1),
(35, 3, NULL, NULL, 'Cliente Prueba 4', 'cliente3@test.com', '', '5732073055', NULL, 'Dirección 4, Apto 45', '2026-01-11 20:03:11', 1),
(36, 3, NULL, NULL, 'Cliente Prueba 5', 'cliente4@test.com', '', '5733888093', NULL, 'Dirección 5, Apto 235', '2026-01-11 20:03:11', 1),
(37, 3, NULL, NULL, 'Cliente Prueba 6', 'cliente5@test.com', '', '5737663266', NULL, 'Dirección 6, Apto 32', '2026-01-11 20:03:11', 1),
(38, 3, NULL, NULL, 'Cliente Prueba 7', 'cliente6@test.com', '', '5730188955', NULL, 'Dirección 7, Apto 385', '2026-01-11 20:03:11', 1),
(39, 3, NULL, NULL, 'Cliente Prueba 8', 'cliente7@test.com', '', '5736682163', NULL, 'Dirección 8, Apto 274', '2026-01-11 20:03:11', 1),
(40, 3, NULL, NULL, 'Cliente Prueba 9', 'cliente8@test.com', '', '5731915344', NULL, 'Dirección 9, Apto 74', '2026-01-11 20:03:11', 1),
(41, 3, NULL, NULL, 'Cliente Prueba 10', 'cliente9@test.com', '', '5738022752', NULL, 'Dirección 10, Apto 293', '2026-01-11 20:03:11', 1),
(42, 4, NULL, NULL, 'Cliente Prueba 1', 'cliente0@test.com', '', '5739171614', NULL, 'Dirección 1, Apto 175', '2026-01-11 20:03:11', 1),
(43, 4, NULL, NULL, 'Cliente Prueba 2', 'cliente1@test.com', '', '5737093803', NULL, 'Dirección 2, Apto 123', '2026-01-11 20:03:11', 1),
(44, 4, NULL, NULL, 'Cliente Prueba 3', 'cliente2@test.com', '', '5736549490', NULL, 'Dirección 3, Apto 399', '2026-01-11 20:03:11', 1),
(45, 4, NULL, NULL, 'Cliente Prueba 4', 'cliente3@test.com', '', '5731316419', NULL, 'Dirección 4, Apto 266', '2026-01-11 20:03:12', 1),
(46, 4, NULL, NULL, 'Cliente Prueba 5', 'cliente4@test.com', '', '5733646749', NULL, 'Dirección 5, Apto 453', '2026-01-11 20:03:12', 1),
(47, 4, NULL, NULL, 'Cliente Prueba 6', 'cliente5@test.com', '', '5735485563', NULL, 'Dirección 6, Apto 7', '2026-01-11 20:03:12', 1),
(48, 4, NULL, NULL, 'Cliente Prueba 7', 'cliente6@test.com', '', '5734572489', NULL, 'Dirección 7, Apto 105', '2026-01-11 20:03:12', 1),
(49, 4, NULL, NULL, 'Cliente Prueba 8', 'cliente7@test.com', '', '5739095445', NULL, 'Dirección 8, Apto 50', '2026-01-11 20:03:12', 1),
(50, 4, NULL, NULL, 'Cliente Prueba 9', 'cliente8@test.com', '', '5734296705', NULL, 'Dirección 9, Apto 333', '2026-01-11 20:03:12', 1),
(51, 4, NULL, NULL, 'Cliente Prueba 10', 'cliente9@test.com', '', '5737661855', NULL, 'Dirección 10, Apto 219', '2026-01-11 20:03:12', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL DEFAULT 1,
  `cliente_id` int(11) NOT NULL,
  `estado` enum('en_pedido','alistado','empaquetado','verificado','en_reparto','entregado','cancelado') DEFAULT 'en_pedido',
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

INSERT INTO `pedidos` (`id`, `tenant_id`, `cliente_id`, `estado`, `total`, `notas_cliente`, `notas_admin`, `whatsapp_enviado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 1, 'en_pedido', 999.99, '', NULL, 0, '2025-12-24 18:32:48', '2026-01-08 14:37:41'),
(2, 1, 4, 'empaquetado', 5699.94, '', NULL, 0, '2025-12-24 19:59:24', '2026-01-09 02:30:03'),
(3, 1, 5, 'en_reparto', 1899.98, '', NULL, 0, '2025-12-25 00:34:50', '2026-01-09 02:29:49'),
(4, 1, 6, 'entregado', 1899.98, '', '', 0, '2026-01-05 22:51:37', '2026-01-08 14:34:03'),
(5, 1, 2, 'en_reparto', 100000.00, '', '', 0, '2026-01-07 18:19:42', '2026-01-08 22:24:55'),
(6, 1, 7, 'entregado', 1022499.82, '', '', 0, '2026-01-08 16:58:57', '2026-01-09 02:27:39'),
(7, 1, 5, 'en_pedido', 6917899.50, '', NULL, 0, '2026-01-09 04:19:35', '2026-01-09 04:19:35'),
(8, 1, 5, 'en_pedido', 2400000.00, '', NULL, 0, '2026-01-09 04:26:56', '2026-01-09 04:26:56'),
(9, 1, 5, 'en_pedido', 100999.99, '', NULL, 0, '2026-01-09 04:46:14', '2026-01-09 04:46:14'),
(10, 2, 8, '', 2150.00, NULL, NULL, 0, '2026-01-09 22:36:22', '2026-01-09 22:36:22'),
(11, 3, 10, '', 1700.00, NULL, NULL, 0, '2026-01-09 22:36:22', '2026-01-09 22:36:22'),
(12, 1, 12, 'en_pedido', 2212.98, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(13, 1, 13, 'en_pedido', 109399.94, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(14, 1, 14, 'en_pedido', 3296.95, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(15, 1, 15, 'en_pedido', 15984.91, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(16, 1, 16, 'en_pedido', 6189.96, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(17, 1, 17, 'en_pedido', 7936.91, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(18, 1, 18, 'en_pedido', 300767.98, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(19, 1, 19, 'en_pedido', 4039.95, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(20, 1, 20, 'en_pedido', 3244.93, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(21, 1, 21, 'en_pedido', 204499.97, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(22, 2, 22, 'en_pedido', 7445.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(23, 2, 23, 'en_pedido', 3500.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(24, 2, 24, 'en_pedido', 1261.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(25, 2, 25, 'en_pedido', 1839.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(26, 2, 26, 'en_pedido', 2712.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(27, 2, 27, 'en_pedido', 7757.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(28, 2, 28, 'en_pedido', 2496.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(29, 2, 29, 'en_pedido', 2524.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(30, 2, 30, 'en_pedido', 2952.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(31, 2, 31, 'en_pedido', 1198.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:10', '2026-01-11 20:03:10'),
(32, 3, 32, 'en_pedido', 2721.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(33, 3, 33, 'en_pedido', 2699.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(34, 3, 34, 'en_pedido', 4225.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(35, 3, 35, 'en_pedido', 1330.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(36, 3, 36, 'en_pedido', 977.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(37, 3, 37, 'en_pedido', 800.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(38, 3, 38, 'en_pedido', 3069.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(39, 3, 39, 'en_pedido', 3316.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(40, 3, 40, 'en_pedido', 3587.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(41, 3, 41, 'en_pedido', 1598.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:11', '2026-01-11 20:03:11'),
(42, 4, 42, 'en_pedido', 798.00, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-11 20:03:12'),
(43, 4, 43, 'en_pedido', 1269.92, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-11 20:03:12'),
(44, 4, 44, 'en_pedido', 1382.96, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-11 20:03:12'),
(45, 4, 45, 'en_pedido', 2182.94, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-11 20:03:12'),
(46, 4, 46, 'en_pedido', 650.97, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-11 20:03:12'),
(47, 4, 47, 'en_pedido', 6899.97, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-11 20:03:12'),
(48, 4, 48, 'en_pedido', 1764.95, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-11 20:03:12'),
(49, 4, 49, 'en_pedido', 5403.95, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-11 20:03:12'),
(50, 4, 50, 'en_pedido', 7445.92, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-11 20:03:12'),
(51, 4, 51, 'en_pedido', 2641.90, 'Pedido de prueba', NULL, 0, '2026-01-11 20:03:12', '2026-01-11 20:03:12');

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
  `estado_preparacion` varchar(20) DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedido_detalles`
--

INSERT INTO `pedido_detalles` (`id`, `tenant_id`, `pedido_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`, `estado_preparacion`) VALUES
(1, 1, 1, 1, 1, 999.99, 999.99, 'pendiente'),
(2, 1, 2, 1, 3, 999.99, 2999.97, 'listo'),
(3, 1, 2, 2, 3, 899.99, 2699.97, 'listo'),
(4, 1, 3, 1, 1, 999.99, 999.99, 'listo'),
(5, 1, 3, 2, 1, 899.99, 899.99, 'listo'),
(6, 1, 4, 1, 1, 999.99, 999.99, 'listo'),
(7, 1, 4, 2, 1, 899.99, 899.99, 'listo'),
(8, 1, 5, 11, 1, 100000.00, 100000.00, 'listo'),
(9, 1, 6, 3, 3, 2499.99, 7499.97, 'listo'),
(10, 1, 6, 4, 4, 1799.99, 7199.96, 'listo'),
(11, 1, 6, 9, 4, 299.99, 1199.96, 'listo'),
(12, 1, 6, 11, 10, 100000.00, 1000000.00, 'listo'),
(13, 1, 6, 2, 4, 899.99, 3599.96, 'listo'),
(14, 1, 6, 1, 3, 999.99, 2999.97, 'listo'),
(15, 1, 7, 11, 69, 100000.00, 6900000.00, 'pendiente'),
(16, 1, 7, 9, 48, 299.99, 14399.52, 'pendiente'),
(17, 1, 7, 1, 1, 999.99, 999.99, 'pendiente'),
(18, 1, 7, 3, 1, 2499.99, 2499.99, 'pendiente'),
(19, 1, 8, 11, 24, 100000.00, 2400000.00, 'pendiente'),
(20, 1, 9, 1, 1, 999.99, 999.99, 'pendiente'),
(21, 1, 9, 11, 1, 100000.00, 100000.00, 'pendiente'),
(22, 1, 12, 45, 2, 67.00, 134.00, 'pendiente'),
(23, 1, 12, 17, 2, 19.99, 39.98, 'pendiente'),
(24, 1, 12, 42, 3, 415.00, 1245.00, 'pendiente'),
(25, 1, 12, 41, 2, 397.00, 794.00, 'pendiente'),
(26, 1, 13, 3, 3, 2499.99, 7499.97, 'pendiente'),
(27, 1, 13, 19, 1, 19.99, 19.99, 'pendiente'),
(28, 1, 13, 6, 1, 79.99, 79.99, 'pendiente'),
(29, 1, 13, 4, 1, 1799.99, 1799.99, 'pendiente'),
(30, 1, 13, 11, 1, 100000.00, 100000.00, 'pendiente'),
(31, 1, 14, 1, 2, 999.99, 1999.98, 'pendiente'),
(32, 1, 14, 41, 1, 397.00, 397.00, 'pendiente'),
(33, 1, 14, 9, 3, 299.99, 899.97, 'pendiente'),
(34, 1, 15, 42, 3, 415.00, 1245.00, 'pendiente'),
(35, 1, 15, 4, 2, 1799.99, 3599.98, 'pendiente'),
(36, 1, 15, 19, 2, 19.99, 39.98, 'pendiente'),
(37, 1, 15, 3, 3, 2499.99, 7499.97, 'pendiente'),
(38, 1, 15, 4, 2, 1799.99, 3599.98, 'pendiente'),
(39, 1, 16, 3, 1, 2499.99, 2499.99, 'pendiente'),
(40, 1, 16, 4, 2, 1799.99, 3599.98, 'pendiente'),
(41, 1, 16, 7, 1, 89.99, 89.99, 'pendiente'),
(42, 1, 17, 4, 2, 1799.99, 3599.98, 'pendiente'),
(43, 1, 17, 44, 1, 487.00, 487.00, 'pendiente'),
(44, 1, 17, 2, 3, 899.99, 2699.97, 'pendiente'),
(45, 1, 17, 5, 3, 49.99, 149.97, 'pendiente'),
(46, 1, 17, 1, 1, 999.99, 999.99, 'pendiente'),
(47, 1, 18, 43, 2, 334.00, 668.00, 'pendiente'),
(48, 1, 18, 5, 2, 49.99, 99.98, 'pendiente'),
(49, 1, 18, 11, 3, 100000.00, 300000.00, 'pendiente'),
(50, 1, 19, 5, 1, 49.99, 49.99, 'pendiente'),
(51, 1, 19, 7, 2, 89.99, 179.98, 'pendiente'),
(52, 1, 19, 40, 2, 105.00, 210.00, 'pendiente'),
(53, 1, 19, 4, 2, 1799.99, 3599.98, 'pendiente'),
(54, 1, 20, 17, 3, 19.99, 59.97, 'pendiente'),
(55, 1, 20, 3, 1, 2499.99, 2499.99, 'pendiente'),
(56, 1, 20, 43, 1, 334.00, 334.00, 'pendiente'),
(57, 1, 20, 45, 3, 67.00, 201.00, 'pendiente'),
(58, 1, 20, 5, 3, 49.99, 149.97, 'pendiente'),
(59, 1, 21, 2, 1, 899.99, 899.99, 'pendiente'),
(60, 1, 21, 11, 2, 100000.00, 200000.00, 'pendiente'),
(61, 1, 21, 4, 2, 1799.99, 3599.98, 'pendiente'),
(62, 2, 22, 15, 3, 2000.00, 6000.00, 'pendiente'),
(63, 2, 22, 145, 1, 173.00, 173.00, 'pendiente'),
(64, 2, 22, 140, 3, 230.00, 690.00, 'pendiente'),
(65, 2, 22, 139, 1, 192.00, 192.00, 'pendiente'),
(66, 2, 22, 144, 3, 130.00, 390.00, 'pendiente'),
(67, 2, 23, 129, 3, 163.00, 489.00, 'pendiente'),
(68, 2, 23, 15, 1, 2000.00, 2000.00, 'pendiente'),
(69, 2, 23, 141, 3, 337.00, 1011.00, 'pendiente'),
(70, 2, 24, 144, 1, 130.00, 130.00, 'pendiente'),
(71, 2, 24, 128, 1, 478.00, 478.00, 'pendiente'),
(72, 2, 24, 132, 1, 393.00, 393.00, 'pendiente'),
(73, 2, 24, 144, 2, 130.00, 260.00, 'pendiente'),
(74, 2, 25, 144, 1, 130.00, 130.00, 'pendiente'),
(75, 2, 25, 13, 1, 150.00, 150.00, 'pendiente'),
(76, 2, 25, 136, 2, 460.00, 920.00, 'pendiente'),
(77, 2, 25, 134, 3, 213.00, 639.00, 'pendiente'),
(78, 2, 26, 139, 2, 192.00, 384.00, 'pendiente'),
(79, 2, 26, 142, 1, 176.00, 176.00, 'pendiente'),
(80, 2, 26, 143, 1, 268.00, 268.00, 'pendiente'),
(81, 2, 26, 135, 1, 450.00, 450.00, 'pendiente'),
(82, 2, 26, 128, 3, 478.00, 1434.00, 'pendiente'),
(83, 2, 27, 141, 3, 337.00, 1011.00, 'pendiente'),
(84, 2, 27, 15, 3, 2000.00, 6000.00, 'pendiente'),
(85, 2, 27, 145, 3, 173.00, 519.00, 'pendiente'),
(86, 2, 27, 137, 1, 227.00, 227.00, 'pendiente'),
(87, 2, 28, 140, 2, 230.00, 460.00, 'pendiente'),
(88, 2, 28, 144, 2, 130.00, 260.00, 'pendiente'),
(89, 2, 28, 136, 2, 460.00, 920.00, 'pendiente'),
(90, 2, 28, 141, 1, 337.00, 337.00, 'pendiente'),
(91, 2, 28, 145, 3, 173.00, 519.00, 'pendiente'),
(92, 2, 29, 140, 1, 230.00, 230.00, 'pendiente'),
(93, 2, 29, 137, 1, 227.00, 227.00, 'pendiente'),
(94, 2, 29, 136, 1, 460.00, 460.00, 'pendiente'),
(95, 2, 29, 145, 1, 173.00, 173.00, 'pendiente'),
(96, 2, 29, 128, 3, 478.00, 1434.00, 'pendiente'),
(97, 2, 30, 131, 2, 435.00, 870.00, 'pendiente'),
(98, 2, 30, 143, 3, 268.00, 804.00, 'pendiente'),
(99, 2, 30, 134, 3, 213.00, 639.00, 'pendiente'),
(100, 2, 30, 134, 3, 213.00, 639.00, 'pendiente'),
(101, 2, 31, 145, 2, 173.00, 346.00, 'pendiente'),
(102, 2, 31, 134, 3, 213.00, 639.00, 'pendiente'),
(103, 2, 31, 134, 1, 213.00, 213.00, 'pendiente'),
(104, 3, 32, 226, 2, 292.00, 584.00, 'pendiente'),
(105, 3, 32, 227, 1, 273.00, 273.00, 'pendiente'),
(106, 3, 32, 14, 2, 500.00, 1000.00, 'pendiente'),
(107, 3, 32, 231, 3, 288.00, 864.00, 'pendiente'),
(108, 3, 33, 226, 1, 292.00, 292.00, 'pendiente'),
(109, 3, 33, 233, 3, 496.00, 1488.00, 'pendiente'),
(110, 3, 33, 231, 3, 288.00, 864.00, 'pendiente'),
(111, 3, 33, 229, 1, 55.00, 55.00, 'pendiente'),
(112, 3, 34, 231, 3, 288.00, 864.00, 'pendiente'),
(113, 3, 34, 233, 3, 496.00, 1488.00, 'pendiente'),
(114, 3, 34, 232, 1, 413.00, 413.00, 'pendiente'),
(115, 3, 34, 226, 2, 292.00, 584.00, 'pendiente'),
(116, 3, 34, 226, 3, 292.00, 876.00, 'pendiente'),
(117, 3, 35, 227, 3, 273.00, 819.00, 'pendiente'),
(118, 3, 35, 227, 1, 273.00, 273.00, 'pendiente'),
(119, 3, 35, 222, 2, 64.00, 128.00, 'pendiente'),
(120, 3, 35, 229, 2, 55.00, 110.00, 'pendiente'),
(121, 3, 36, 231, 2, 288.00, 576.00, 'pendiente'),
(122, 3, 36, 229, 1, 55.00, 55.00, 'pendiente'),
(123, 3, 36, 228, 1, 114.00, 114.00, 'pendiente'),
(124, 3, 36, 225, 2, 116.00, 232.00, 'pendiente'),
(125, 3, 37, 227, 1, 273.00, 273.00, 'pendiente'),
(126, 3, 37, 225, 1, 116.00, 116.00, 'pendiente'),
(127, 3, 37, 224, 3, 61.00, 183.00, 'pendiente'),
(128, 3, 37, 230, 1, 228.00, 228.00, 'pendiente'),
(129, 3, 38, 226, 1, 292.00, 292.00, 'pendiente'),
(130, 3, 38, 14, 3, 500.00, 1500.00, 'pendiente'),
(131, 3, 38, 228, 2, 114.00, 228.00, 'pendiente'),
(132, 3, 38, 226, 3, 292.00, 876.00, 'pendiente'),
(133, 3, 38, 223, 1, 173.00, 173.00, 'pendiente'),
(134, 3, 39, 234, 3, 165.00, 495.00, 'pendiente'),
(135, 3, 39, 228, 2, 114.00, 228.00, 'pendiente'),
(136, 3, 39, 16, 2, 1200.00, 2400.00, 'pendiente'),
(137, 3, 39, 220, 1, 193.00, 193.00, 'pendiente'),
(138, 3, 40, 229, 2, 55.00, 110.00, 'pendiente'),
(139, 3, 40, 235, 3, 207.00, 621.00, 'pendiente'),
(140, 3, 40, 228, 2, 114.00, 228.00, 'pendiente'),
(141, 3, 40, 230, 1, 228.00, 228.00, 'pendiente'),
(142, 3, 40, 16, 2, 1200.00, 2400.00, 'pendiente'),
(143, 3, 41, 227, 2, 273.00, 546.00, 'pendiente'),
(144, 3, 41, 224, 1, 61.00, 61.00, 'pendiente'),
(145, 3, 41, 234, 3, 165.00, 495.00, 'pendiente'),
(146, 3, 41, 233, 1, 496.00, 496.00, 'pendiente'),
(147, 4, 42, 313, 3, 90.00, 270.00, 'pendiente'),
(148, 4, 42, 310, 1, 381.00, 381.00, 'pendiente'),
(149, 4, 42, 315, 1, 147.00, 147.00, 'pendiente'),
(150, 4, 43, 37, 2, 249.99, 499.98, 'pendiente'),
(151, 4, 43, 38, 3, 69.99, 209.97, 'pendiente'),
(152, 4, 43, 38, 3, 69.99, 209.97, 'pendiente'),
(153, 4, 43, 308, 1, 350.00, 350.00, 'pendiente'),
(154, 4, 44, 18, 1, 29.99, 29.99, 'pendiente'),
(155, 4, 44, 310, 3, 381.00, 1143.00, 'pendiente'),
(156, 4, 44, 33, 1, 29.99, 29.99, 'pendiente'),
(157, 4, 44, 35, 2, 89.99, 179.98, 'pendiente'),
(158, 4, 45, 18, 3, 29.99, 89.97, 'pendiente'),
(159, 4, 45, 312, 3, 452.00, 1356.00, 'pendiente'),
(160, 4, 45, 20, 3, 49.99, 149.97, 'pendiente'),
(161, 4, 45, 315, 3, 147.00, 441.00, 'pendiente'),
(162, 4, 45, 311, 1, 146.00, 146.00, 'pendiente'),
(163, 4, 46, 315, 1, 147.00, 147.00, 'pendiente'),
(164, 4, 46, 315, 2, 147.00, 294.00, 'pendiente'),
(165, 4, 46, 38, 3, 69.99, 209.97, 'pendiente'),
(166, 4, 47, 309, 3, 460.00, 1380.00, 'pendiente'),
(167, 4, 47, 30, 3, 1299.99, 3899.97, 'pendiente'),
(168, 4, 47, 308, 2, 350.00, 700.00, 'pendiente'),
(169, 4, 47, 309, 2, 460.00, 920.00, 'pendiente'),
(170, 4, 48, 311, 2, 146.00, 292.00, 'pendiente'),
(171, 4, 48, 310, 3, 381.00, 1143.00, 'pendiente'),
(172, 4, 48, 35, 3, 89.99, 269.97, 'pendiente'),
(173, 4, 48, 33, 2, 29.99, 59.98, 'pendiente'),
(174, 4, 49, 36, 2, 79.99, 159.98, 'pendiente'),
(175, 4, 49, 315, 2, 147.00, 294.00, 'pendiente'),
(176, 4, 49, 30, 3, 1299.99, 3899.97, 'pendiente'),
(177, 4, 49, 308, 3, 350.00, 1050.00, 'pendiente'),
(178, 4, 50, 34, 1, 45.99, 45.99, 'pendiente'),
(179, 4, 50, 29, 3, 799.99, 2399.97, 'pendiente'),
(180, 4, 50, 32, 2, 1199.99, 2399.98, 'pendiente'),
(181, 4, 50, 30, 2, 1299.99, 2599.98, 'pendiente'),
(182, 4, 51, 312, 1, 452.00, 452.00, 'pendiente'),
(183, 4, 51, 36, 3, 79.99, 239.97, 'pendiente'),
(184, 4, 51, 29, 2, 799.99, 1599.98, 'pendiente'),
(185, 4, 51, 38, 2, 69.99, 139.98, 'pendiente'),
(186, 4, 51, 38, 3, 69.99, 209.97, 'pendiente');

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
(25, 1, 9, 'en_pedido', 'Pedido creado', 1, '2026-01-09 04:46:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
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

INSERT INTO `productos` (`id`, `tenant_id`, `categoria_id`, `subcategoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 1, 1, 'iPhone 15 Pro', 'Último modelo de Apple con chip A17 Pro y cámara avanzada.', 999.99, 50, NULL, 1, '2025-12-24 17:35:46', '2026-01-12 00:21:05'),
(2, 1, 1, 1, 'Samsung Galaxy S24', 'Teléfono Android con pantalla AMOLED y procesador Snapdragon.', 899.99, 45, 'samsung_s24.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(3, 1, 1, 2, 'MacBook Pro 16', 'Laptop de alta rendimiento con chip M3 Max para profesionales.', 2499.99, 20, 'macbook_pro.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(4, 1, 1, 2, 'Dell XPS 15', 'Computadora portátil con procesador Intel y pantalla 4K.', 1799.99, 25, 'dell_xps.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(5, 1, 2, 3, 'Camiseta Premium Hombre', 'Camiseta de algodón 100% de alta calidad para hombre.', 49.99, 100, 'camiseta_hombre.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(6, 1, 2, 3, 'Pantalón Casual Hombre', 'Pantalón casual de tela resistente, perfecto para uso diario.', 79.99, 80, 'pantalon_hombre.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(7, 1, 2, 4, 'Vestido Casual Mujer', 'Vestido elegante y cómodo para cualquier ocasión casual.', 89.99, 60, 'vestido_mujer.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(8, 1, 2, 4, 'Jeans Premium Mujer', 'Jeans de marca reconocida, cómodos y de excelente calidad.', 99.99, 75, 'jeans_mujer.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(9, 1, 3, 5, 'Horno Eléctrico', 'Horno eléctrico con múltiples funciones para cocinar.', 299.99, 15, 'horno_electrico.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(10, 1, 3, 6, 'Juego de Cama King', 'Juego de sábanas y almohadas tamaño king de algodón.', 199.99, 30, 'juego_cama.jpg', 1, '2025-12-24 17:35:46', '2025-12-24 17:35:46'),
(11, 1, 1, 2, 'computador', 'buygugu', 100000.00, 50, NULL, 1, '2026-01-07 18:05:16', '2026-01-12 00:21:05'),
(13, 2, 4, 7, 'Servicio de ConsultorÝa', 'ConsultorÝa profesional por horas', 150.00, 998, NULL, 1, '2026-01-09 21:43:48', '2026-01-12 00:21:05'),
(14, 3, 5, 8, 'Lote Mayorista', 'Distribuci¾n por cajas de 20 unidades', 500.00, 100, NULL, 1, '2026-01-09 21:43:55', '2026-01-12 00:21:05'),
(15, 2, 4, 9, 'App Web Custom', 'Desarrollo de aplicaci¾n web personalizada', 2000.00, 49, NULL, 1, '2026-01-09 22:33:44', '2026-01-12 00:21:05'),
(16, 3, 5, 10, 'Monitor 24 Bulk', 'Monitores 24 pulgadas lote de 10 unidades', 1200.00, 200, NULL, 0, '2026-01-09 22:33:54', '2026-01-12 00:21:05'),
(17, 1, 1, 1, 'Prod img tenant1', 'test img tenant1', 19.99, 5, 'public/tenants/1/images/upl_6961914b505a3_test-img1.png', 1, '2026-01-09 23:37:47', '2026-01-09 23:37:47'),
(18, 4, 6, 11, 'Prod img tenant4', 'test img tenant4', 29.99, 7, 'public/tenants/4/images/upl_6961914b8133b_test-img2.png', 1, '2026-01-09 23:37:47', '2026-01-09 23:37:47'),
(19, 1, 1, 1, 'Prod img tenant1', 'test img tenant1', 19.99, 5, 'public/tenants/1/images/upl_696191548a190_test-img1.png', 1, '2026-01-09 23:37:56', '2026-01-09 23:37:56'),
(20, 4, 6, 11, 'Prod img tenant4 edit', 'edit test 2', 49.99, 8, 'public/tenants/4/images/upl_696191f2ef230_test-img4.png', 1, '2026-01-09 23:38:54', '2026-01-09 23:40:34'),
(29, 4, 6, 12, 'Laptop HP 15', 'Laptop HP 15 pulgadas, Intel Core i5, 8GB RAM, 256GB SSD. Perfecta para trabajo y estudio.', 799.99, 15, 'dell_xps.jpg', 1, '2026-01-10 17:34:23', '2026-01-10 17:36:56'),
(30, 4, 6, 12, 'MacBook Air M2', 'MacBook Air con chip M2, 13 pulgadas, 8GB RAM, 512GB SSD. Ultra delgada y potente.', 1299.99, 8, 'macbook_pro.jpg', 1, '2026-01-10 17:34:23', '2026-01-10 17:36:56'),
(31, 4, 6, 11, 'iPhone 15 Pro', 'iPhone 15 Pro 128GB, Titanio Azul. Cßmara profesional de 48MP.', 999.99, 20, 'samsung_s24.jpg', 1, '2026-01-10 17:34:23', '2026-01-10 17:36:56'),
(32, 4, 6, 11, 'Samsung Galaxy S24', 'Samsung Galaxy S24 Ultra 256GB. Pantalla AMOLED 120Hz, S Pen incluido.', 1199.99, 12, 'samsung_s24.jpg', 1, '2026-01-10 17:34:23', '2026-01-10 17:36:56'),
(33, 4, 7, 13, 'Camisa Polo Hombre', 'Camisa polo 100% algod¾n peinado, talla M. Disponible en varios colores.', 29.99, 50, 'camiseta_hombre.jpg', 1, '2026-01-10 17:34:23', '2026-01-10 17:36:56'),
(34, 4, 7, 14, 'Vestido Casual Mujer', 'Vestido casual elegante, talla S. Perfecto para cualquier ocasi¾n.', 45.99, 30, 'jeans_mujer.jpg', 1, '2026-01-10 17:34:23', '2026-01-10 17:36:56'),
(35, 4, 8, 15, 'Licuadora Oster', 'Licuadora de 3 velocidades, 600W. Vaso de vidrio resistente.', 89.99, 25, 'horno_electrico.jpg', 1, '2026-01-10 17:34:23', '2026-01-10 17:36:56'),
(36, 4, 8, 16, 'Edred¾n King Size', 'Edred¾n suave tama±o King, color gris. Material hipoalergÚnico.', 79.99, 18, 'juego_cama.jpg', 1, '2026-01-10 17:34:23', '2026-01-10 17:36:56'),
(37, 4, 6, 11, 'AirPods Pro', 'AirPods Pro con cancelaci¾n de ruido activa. Estuche con MagSafe.', 249.99, 35, 'samsung_s24.jpg', 1, '2026-01-10 17:34:23', '2026-01-10 17:36:56'),
(38, 4, 7, 13, 'Jeans Levi 501', 'Jeans Levi 501 clßsicos, corte regular. Talla 32.', 69.99, 40, 'jeans_mujer.jpg', 1, '2026-01-10 17:34:23', '2026-01-10 17:36:56'),
(39, 1, 1, 1, 'Samsung Galaxy S21', 'Descripción de Samsung Galaxy S21', 419.00, 29, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(40, 1, 1, 1, 'iPhone 13 Pro', 'Descripción de iPhone 13 Pro', 105.00, 36, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(41, 1, 1, 1, 'Xiaomi 12', 'Descripción de Xiaomi 12', 397.00, 15, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(42, 1, 1, 1, 'Google Pixel 6', 'Descripción de Google Pixel 6', 415.00, 19, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(43, 1, 1, 1, 'OnePlus 9', 'Descripción de OnePlus 9', 334.00, 33, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(44, 1, 1, 1, 'Motorola Edge', 'Descripción de Motorola Edge', 487.00, 50, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(45, 1, 1, 1, 'Realme GT', 'Descripción de Realme GT', 67.00, 39, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(46, 1, 1, 1, 'Nothing Phone', 'Descripción de Nothing Phone', 394.00, 49, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(47, 1, 1, 1, 'Sony Xperia', 'Descripción de Sony Xperia', 424.00, 25, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(48, 1, 1, 1, 'OPPO Find X3', 'Descripción de OPPO Find X3', 422.00, 25, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(49, 1, 1, 2, 'Dell XPS 13', 'Descripción de Dell XPS 13', 131.00, 20, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(50, 1, 1, 2, 'HP Pavilion 15', 'Descripción de HP Pavilion 15', 440.00, 19, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(51, 1, 1, 2, 'Lenovo ThinkPad', 'Descripción de Lenovo ThinkPad', 163.00, 37, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(52, 1, 1, 2, 'ASUS VivoBook', 'Descripción de ASUS VivoBook', 205.00, 15, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(53, 1, 1, 2, 'Acer Aspire 5', 'Descripción de Acer Aspire 5', 382.00, 20, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(54, 1, 1, 2, 'MSI GS66', 'Descripción de MSI GS66', 186.00, 10, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(55, 1, 1, 2, 'Razer Blade', 'Descripción de Razer Blade', 477.00, 11, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(56, 1, 1, 2, 'LG Gram', 'Descripción de LG Gram', 371.00, 43, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(57, 1, 1, 2, 'ROG Gaming Laptop', 'Descripción de ROG Gaming Laptop', 113.00, 17, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(58, 1, 1, 17, 'Cargador Rápido', 'Descripción de Cargador Rápido', 62.00, 20, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(59, 1, 1, 17, 'Cable USB-C', 'Descripción de Cable USB-C', 85.00, 50, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(60, 1, 1, 17, 'Protector Pantalla', 'Descripción de Protector Pantalla', 240.00, 44, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(61, 1, 1, 17, 'Funda Teléfono', 'Descripción de Funda Teléfono', 333.00, 38, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(62, 1, 1, 17, 'Audífonos Inalámbricos', 'Descripción de Audífonos Inalámbricos', 231.00, 43, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(63, 1, 1, 17, 'Power Bank', 'Descripción de Power Bank', 283.00, 15, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(64, 1, 1, 17, 'Soporte Móvil', 'Descripción de Soporte Móvil', 345.00, 25, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(65, 1, 1, 17, 'Protector Cámara', 'Descripción de Protector Cámara', 71.00, 33, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(66, 1, 1, 17, 'Anillo Soporte', 'Descripción de Anillo Soporte', 194.00, 44, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(67, 1, 1, 17, 'Cable HDMI', 'Descripción de Cable HDMI', 430.00, 14, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(68, 1, 2, 18, 'Camiseta Básica', 'Descripción de Camiseta Básica', 72.00, 40, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(69, 1, 2, 18, 'Pantalón Denim', 'Descripción de Pantalón Denim', 257.00, 27, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(70, 1, 2, 18, 'Camisa Social', 'Descripción de Camisa Social', 261.00, 33, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(71, 1, 2, 18, 'Polo Premium', 'Descripción de Polo Premium', 342.00, 10, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(72, 1, 2, 18, 'Shorts Deportivos', 'Descripción de Shorts Deportivos', 334.00, 49, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(73, 1, 2, 18, 'Chaqueta Casual', 'Descripción de Chaqueta Casual', 151.00, 27, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(74, 1, 2, 18, 'Suéter Lana', 'Descripción de Suéter Lana', 388.00, 34, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(75, 1, 2, 18, 'Bermudas', 'Descripción de Bermudas', 75.00, 24, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(76, 1, 2, 18, 'Camiseta Deportiva', 'Descripción de Camiseta Deportiva', 371.00, 34, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(77, 1, 2, 18, 'Pantalón Cargo', 'Descripción de Pantalón Cargo', 492.00, 24, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(78, 1, 2, 19, 'Blusa Elegante', 'Descripción de Blusa Elegante', 458.00, 14, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(79, 1, 2, 19, 'Jeans Skinny', 'Descripción de Jeans Skinny', 202.00, 34, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(80, 1, 2, 19, 'Vestido Casual', 'Descripción de Vestido Casual', 287.00, 25, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(81, 1, 2, 19, 'Top Deportivo', 'Descripción de Top Deportivo', 498.00, 16, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(82, 1, 2, 19, 'Falda Midi', 'Descripción de Falda Midi', 117.00, 24, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(83, 1, 2, 19, 'Chaqueta Mezclilla', 'Descripción de Chaqueta Mezclilla', 180.00, 41, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(84, 1, 2, 19, 'Leggings Premium', 'Descripción de Leggings Premium', 234.00, 14, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(85, 1, 2, 19, 'Cardigan', 'Descripción de Cardigan', 84.00, 37, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(86, 1, 2, 19, 'Blusa Floral', 'Descripción de Blusa Floral', 179.00, 14, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(87, 1, 2, 19, 'Pantalón Palazzo', 'Descripción de Pantalón Palazzo', 257.00, 15, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(88, 1, 2, 20, 'Camiseta Niño', 'Descripción de Camiseta Niño', 336.00, 23, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(89, 1, 2, 20, 'Pantalón Niño', 'Descripción de Pantalón Niño', 410.00, 41, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(90, 1, 2, 20, 'Sudadera Infantil', 'Descripción de Sudadera Infantil', 307.00, 27, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(91, 1, 2, 20, 'Shorts Niño', 'Descripción de Shorts Niño', 361.00, 12, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(92, 1, 2, 20, 'Falda Niña', 'Descripción de Falda Niña', 399.00, 46, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(93, 1, 2, 20, 'Blusa Niña', 'Descripción de Blusa Niña', 295.00, 48, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(94, 1, 2, 20, 'Pantalón Niña', 'Descripción de Pantalón Niña', 86.00, 19, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(95, 1, 2, 20, 'Chaqueta Infantil', 'Descripción de Chaqueta Infantil', 104.00, 16, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(96, 1, 2, 20, 'Buzo Infantil', 'Descripción de Buzo Infantil', 153.00, 40, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(97, 1, 2, 20, 'Leggings Niña', 'Descripción de Leggings Niña', 248.00, 19, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(98, 1, 3, 5, 'Licuadora', 'Descripción de Licuadora', 359.00, 34, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(99, 1, 3, 5, 'Microondas', 'Descripción de Microondas', 137.00, 42, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(100, 1, 3, 5, 'Cafetera', 'Descripción de Cafetera', 451.00, 39, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(101, 1, 3, 5, 'Tostador', 'Descripción de Tostador', 301.00, 29, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(102, 1, 3, 5, 'Olla Arrocera', 'Descripción de Olla Arrocera', 461.00, 43, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(103, 1, 3, 5, 'Plancha Eléctrica', 'Descripción de Plancha Eléctrica', 101.00, 16, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(104, 1, 3, 5, 'Freidora Aire', 'Descripción de Freidora Aire', 407.00, 35, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(105, 1, 3, 5, 'Batidora', 'Descripción de Batidora', 200.00, 35, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(106, 1, 3, 5, 'Exprimidor', 'Descripción de Exprimidor', 179.00, 25, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(107, 1, 3, 5, 'Hervidor Eléctrico', 'Descripción de Hervidor Eléctrico', 274.00, 45, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(108, 1, 3, 21, 'Sofá 3 Puestos', 'Descripción de Sofá 3 Puestos', 463.00, 17, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(109, 1, 3, 21, 'Mesa Comedor', 'Descripción de Mesa Comedor', 475.00, 27, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(110, 1, 3, 21, 'Silla Oficina', 'Descripción de Silla Oficina', 399.00, 42, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(111, 1, 3, 21, 'Cama Queen', 'Descripción de Cama Queen', 440.00, 38, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(112, 1, 3, 21, 'Closet Madera', 'Descripción de Closet Madera', 489.00, 24, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(113, 1, 3, 21, 'Biblioteca', 'Descripción de Biblioteca', 156.00, 10, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(114, 1, 3, 21, 'Mesita Noche', 'Descripción de Mesita Noche', 262.00, 19, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(115, 1, 3, 21, 'Escritorio', 'Descripción de Escritorio', 328.00, 40, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(116, 1, 3, 21, 'Estantería', 'Descripción de Estantería', 239.00, 26, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(117, 1, 3, 21, 'Modular Tv', 'Descripción de Modular Tv', 413.00, 45, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(118, 1, 3, 22, 'Cuadro Moderno', 'Descripción de Cuadro Moderno', 150.00, 41, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(119, 1, 3, 22, 'Espejo Decorativo', 'Descripción de Espejo Decorativo', 442.00, 12, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(120, 1, 3, 22, 'Lámpara Piso', 'Descripción de Lámpara Piso', 270.00, 47, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(121, 1, 3, 22, 'Cojín', 'Descripción de Cojín', 216.00, 33, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(122, 1, 3, 22, 'Cortina Premium', 'Descripción de Cortina Premium', 421.00, 39, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(123, 1, 3, 22, 'Tapete', 'Descripción de Tapete', 126.00, 49, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(124, 1, 3, 22, 'Jarrón Cerámica', 'Descripción de Jarrón Cerámica', 257.00, 42, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(125, 1, 3, 22, 'Alfombra', 'Descripción de Alfombra', 346.00, 23, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(126, 1, 3, 22, 'Vela Aromática', 'Descripción de Vela Aromática', 320.00, 15, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(127, 1, 3, 22, 'Reloj Pared', 'Descripción de Reloj Pared', 446.00, 31, NULL, 1, '2026-01-11 20:00:15', '2026-01-12 00:21:05'),
(128, 2, 9, 23, 'Samsung Galaxy S21', 'Descripción de Samsung Galaxy S21', 478.00, 18, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(129, 2, 9, 23, 'iPhone 13 Pro', 'Descripción de iPhone 13 Pro', 163.00, 13, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(130, 2, 9, 23, 'Xiaomi 12', 'Descripción de Xiaomi 12', 225.00, 28, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(131, 2, 9, 23, 'Google Pixel 6', 'Descripción de Google Pixel 6', 435.00, 22, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(132, 2, 9, 23, 'OnePlus 9', 'Descripción de OnePlus 9', 393.00, 25, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(133, 2, 9, 23, 'Motorola Edge', 'Descripción de Motorola Edge', 80.00, 46, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(134, 2, 9, 23, 'Realme GT', 'Descripción de Realme GT', 213.00, 33, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(135, 2, 9, 23, 'Nothing Phone', 'Descripción de Nothing Phone', 450.00, 33, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(136, 2, 9, 23, 'Sony Xperia', 'Descripción de Sony Xperia', 460.00, 41, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(137, 2, 9, 23, 'OPPO Find X3', 'Descripción de OPPO Find X3', 227.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(138, 2, 9, 24, 'MacBook Pro 16', 'Descripción de MacBook Pro 16', 378.00, 38, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(139, 2, 9, 24, 'Dell XPS 13', 'Descripción de Dell XPS 13', 192.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(140, 2, 9, 24, 'HP Pavilion 15', 'Descripción de HP Pavilion 15', 230.00, 25, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(141, 2, 9, 24, 'Lenovo ThinkPad', 'Descripción de Lenovo ThinkPad', 337.00, 47, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(142, 2, 9, 24, 'ASUS VivoBook', 'Descripción de ASUS VivoBook', 176.00, 36, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(143, 2, 9, 24, 'Acer Aspire 5', 'Descripción de Acer Aspire 5', 268.00, 32, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(144, 2, 9, 24, 'MSI GS66', 'Descripción de MSI GS66', 130.00, 48, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(145, 2, 9, 24, 'Razer Blade', 'Descripción de Razer Blade', 173.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(146, 2, 9, 24, 'LG Gram', 'Descripción de LG Gram', 129.00, 29, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(147, 2, 9, 24, 'ROG Gaming Laptop', 'Descripción de ROG Gaming Laptop', 95.00, 39, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(148, 2, 9, 25, 'Cargador Rápido', 'Descripción de Cargador Rápido', 192.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(149, 2, 9, 25, 'Cable USB-C', 'Descripción de Cable USB-C', 381.00, 45, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(150, 2, 9, 25, 'Protector Pantalla', 'Descripción de Protector Pantalla', 327.00, 32, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(151, 2, 9, 25, 'Funda Teléfono', 'Descripción de Funda Teléfono', 314.00, 14, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(152, 2, 9, 25, 'Audífonos Inalámbricos', 'Descripción de Audífonos Inalámbricos', 242.00, 41, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(153, 2, 9, 25, 'Power Bank', 'Descripción de Power Bank', 104.00, 13, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(154, 2, 9, 25, 'Soporte Móvil', 'Descripción de Soporte Móvil', 353.00, 13, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(155, 2, 9, 25, 'Protector Cámara', 'Descripción de Protector Cámara', 432.00, 19, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(156, 2, 9, 25, 'Anillo Soporte', 'Descripción de Anillo Soporte', 56.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(157, 2, 9, 25, 'Cable HDMI', 'Descripción de Cable HDMI', 254.00, 36, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(158, 2, 10, 26, 'Camiseta Básica', 'Descripción de Camiseta Básica', 180.00, 28, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(159, 2, 10, 26, 'Pantalón Denim', 'Descripción de Pantalón Denim', 200.00, 37, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(160, 2, 10, 26, 'Camisa Social', 'Descripción de Camisa Social', 247.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(161, 2, 10, 26, 'Polo Premium', 'Descripción de Polo Premium', 56.00, 31, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(162, 2, 10, 26, 'Shorts Deportivos', 'Descripción de Shorts Deportivos', 176.00, 43, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(163, 2, 10, 26, 'Chaqueta Casual', 'Descripción de Chaqueta Casual', 288.00, 43, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(164, 2, 10, 26, 'Suéter Lana', 'Descripción de Suéter Lana', 450.00, 38, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(165, 2, 10, 26, 'Bermudas', 'Descripción de Bermudas', 214.00, 29, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(166, 2, 10, 26, 'Camiseta Deportiva', 'Descripción de Camiseta Deportiva', 402.00, 34, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(167, 2, 10, 26, 'Pantalón Cargo', 'Descripción de Pantalón Cargo', 218.00, 24, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(168, 2, 10, 27, 'Blusa Elegante', 'Descripción de Blusa Elegante', 231.00, 36, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(169, 2, 10, 27, 'Jeans Skinny', 'Descripción de Jeans Skinny', 188.00, 11, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(170, 2, 10, 27, 'Vestido Casual', 'Descripción de Vestido Casual', 179.00, 12, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(171, 2, 10, 27, 'Top Deportivo', 'Descripción de Top Deportivo', 50.00, 24, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(172, 2, 10, 27, 'Falda Midi', 'Descripción de Falda Midi', 149.00, 23, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(173, 2, 10, 27, 'Chaqueta Mezclilla', 'Descripción de Chaqueta Mezclilla', 176.00, 25, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(174, 2, 10, 27, 'Leggings Premium', 'Descripción de Leggings Premium', 472.00, 10, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(175, 2, 10, 27, 'Cardigan', 'Descripción de Cardigan', 244.00, 15, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(176, 2, 10, 27, 'Blusa Floral', 'Descripción de Blusa Floral', 149.00, 33, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(177, 2, 10, 27, 'Pantalón Palazzo', 'Descripción de Pantalón Palazzo', 476.00, 38, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(178, 2, 10, 28, 'Camiseta Niño', 'Descripción de Camiseta Niño', 184.00, 34, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(179, 2, 10, 28, 'Pantalón Niño', 'Descripción de Pantalón Niño', 74.00, 45, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(180, 2, 10, 28, 'Sudadera Infantil', 'Descripción de Sudadera Infantil', 298.00, 41, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(181, 2, 10, 28, 'Shorts Niño', 'Descripción de Shorts Niño', 69.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(182, 2, 10, 28, 'Falda Niña', 'Descripción de Falda Niña', 284.00, 39, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(183, 2, 10, 28, 'Blusa Niña', 'Descripción de Blusa Niña', 497.00, 48, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(184, 2, 10, 28, 'Pantalón Niña', 'Descripción de Pantalón Niña', 257.00, 31, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(185, 2, 10, 28, 'Chaqueta Infantil', 'Descripción de Chaqueta Infantil', 57.00, 11, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(186, 2, 10, 28, 'Buzo Infantil', 'Descripción de Buzo Infantil', 498.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(187, 2, 10, 28, 'Leggings Niña', 'Descripción de Leggings Niña', 377.00, 45, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(188, 2, 11, 29, 'Licuadora', 'Descripción de Licuadora', 315.00, 33, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(189, 2, 11, 29, 'Microondas', 'Descripción de Microondas', 494.00, 45, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(190, 2, 11, 29, 'Cafetera', 'Descripción de Cafetera', 60.00, 23, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(191, 2, 11, 29, 'Tostador', 'Descripción de Tostador', 190.00, 23, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(192, 2, 11, 29, 'Olla Arrocera', 'Descripción de Olla Arrocera', 313.00, 27, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(193, 2, 11, 29, 'Plancha Eléctrica', 'Descripción de Plancha Eléctrica', 84.00, 32, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(194, 2, 11, 29, 'Freidora Aire', 'Descripción de Freidora Aire', 375.00, 46, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(195, 2, 11, 29, 'Batidora', 'Descripción de Batidora', 135.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(196, 2, 11, 29, 'Exprimidor', 'Descripción de Exprimidor', 181.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(197, 2, 11, 29, 'Hervidor Eléctrico', 'Descripción de Hervidor Eléctrico', 254.00, 28, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(198, 2, 11, 30, 'Sofá 3 Puestos', 'Descripción de Sofá 3 Puestos', 493.00, 20, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(199, 2, 11, 30, 'Mesa Comedor', 'Descripción de Mesa Comedor', 437.00, 22, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(200, 2, 11, 30, 'Silla Oficina', 'Descripción de Silla Oficina', 451.00, 41, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(201, 2, 11, 30, 'Cama Queen', 'Descripción de Cama Queen', 223.00, 42, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(202, 2, 11, 30, 'Closet Madera', 'Descripción de Closet Madera', 169.00, 45, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(203, 2, 11, 30, 'Biblioteca', 'Descripción de Biblioteca', 106.00, 16, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(204, 2, 11, 30, 'Mesita Noche', 'Descripción de Mesita Noche', 341.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(205, 2, 11, 30, 'Escritorio', 'Descripción de Escritorio', 471.00, 25, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(206, 2, 11, 30, 'Estantería', 'Descripción de Estantería', 200.00, 16, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(207, 2, 11, 30, 'Modular Tv', 'Descripción de Modular Tv', 211.00, 35, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(208, 2, 11, 31, 'Cuadro Moderno', 'Descripción de Cuadro Moderno', 383.00, 11, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(209, 2, 11, 31, 'Espejo Decorativo', 'Descripción de Espejo Decorativo', 104.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(210, 2, 11, 31, 'Lámpara Piso', 'Descripción de Lámpara Piso', 353.00, 44, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(211, 2, 11, 31, 'Cojín', 'Descripción de Cojín', 482.00, 15, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(212, 2, 11, 31, 'Cortina Premium', 'Descripción de Cortina Premium', 193.00, 42, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(213, 2, 11, 31, 'Tapete', 'Descripción de Tapete', 408.00, 49, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(214, 2, 11, 31, 'Jarrón Cerámica', 'Descripción de Jarrón Cerámica', 260.00, 19, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(215, 2, 11, 31, 'Alfombra', 'Descripción de Alfombra', 135.00, 13, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(216, 2, 11, 31, 'Vela Aromática', 'Descripción de Vela Aromática', 462.00, 39, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(217, 2, 11, 31, 'Reloj Pared', 'Descripción de Reloj Pared', 148.00, 15, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(218, 3, 12, 32, 'Samsung Galaxy S21', 'Descripción de Samsung Galaxy S21', 407.00, 45, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(219, 3, 12, 32, 'iPhone 13 Pro', 'Descripción de iPhone 13 Pro', 322.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(220, 3, 12, 32, 'Xiaomi 12', 'Descripción de Xiaomi 12', 193.00, 32, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(221, 3, 12, 32, 'Google Pixel 6', 'Descripción de Google Pixel 6', 413.00, 17, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(222, 3, 12, 32, 'OnePlus 9', 'Descripción de OnePlus 9', 64.00, 38, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(223, 3, 12, 32, 'Motorola Edge', 'Descripción de Motorola Edge', 173.00, 50, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(224, 3, 12, 32, 'Realme GT', 'Descripción de Realme GT', 61.00, 47, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(225, 3, 12, 32, 'Nothing Phone', 'Descripción de Nothing Phone', 116.00, 46, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(226, 3, 12, 32, 'Sony Xperia', 'Descripción de Sony Xperia', 292.00, 11, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(227, 3, 12, 32, 'OPPO Find X3', 'Descripción de OPPO Find X3', 273.00, 40, NULL, 1, '2026-01-11 20:03:10', '2026-01-12 00:21:05'),
(228, 3, 12, 33, 'MacBook Pro 16', 'Descripción de MacBook Pro 16', 114.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(229, 3, 12, 33, 'Dell XPS 13', 'Descripción de Dell XPS 13', 55.00, 10, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(230, 3, 12, 33, 'HP Pavilion 15', 'Descripción de HP Pavilion 15', 228.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(231, 3, 12, 33, 'Lenovo ThinkPad', 'Descripción de Lenovo ThinkPad', 288.00, 41, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(232, 3, 12, 33, 'ASUS VivoBook', 'Descripción de ASUS VivoBook', 413.00, 12, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(233, 3, 12, 33, 'Acer Aspire 5', 'Descripción de Acer Aspire 5', 496.00, 45, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(234, 3, 12, 33, 'MSI GS66', 'Descripción de MSI GS66', 165.00, 23, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(235, 3, 12, 33, 'Razer Blade', 'Descripción de Razer Blade', 207.00, 41, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(236, 3, 12, 33, 'LG Gram', 'Descripción de LG Gram', 249.00, 30, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(237, 3, 12, 33, 'ROG Gaming Laptop', 'Descripción de ROG Gaming Laptop', 85.00, 16, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(238, 3, 12, 34, 'Cargador Rápido', 'Descripción de Cargador Rápido', 494.00, 44, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(239, 3, 12, 34, 'Cable USB-C', 'Descripción de Cable USB-C', 221.00, 44, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(240, 3, 12, 34, 'Protector Pantalla', 'Descripción de Protector Pantalla', 496.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(241, 3, 12, 34, 'Funda Teléfono', 'Descripción de Funda Teléfono', 310.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(242, 3, 12, 34, 'Audífonos Inalámbricos', 'Descripción de Audífonos Inalámbricos', 288.00, 19, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(243, 3, 12, 34, 'Power Bank', 'Descripción de Power Bank', 496.00, 43, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(244, 3, 12, 34, 'Soporte Móvil', 'Descripción de Soporte Móvil', 170.00, 44, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(245, 3, 12, 34, 'Protector Cámara', 'Descripción de Protector Cámara', 431.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(246, 3, 12, 34, 'Anillo Soporte', 'Descripción de Anillo Soporte', 203.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(247, 3, 12, 34, 'Cable HDMI', 'Descripción de Cable HDMI', 278.00, 12, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(248, 3, 13, 35, 'Camiseta Básica', 'Descripción de Camiseta Básica', 332.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(249, 3, 13, 35, 'Pantalón Denim', 'Descripción de Pantalón Denim', 118.00, 46, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(250, 3, 13, 35, 'Camisa Social', 'Descripción de Camisa Social', 192.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(251, 3, 13, 35, 'Polo Premium', 'Descripción de Polo Premium', 308.00, 12, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(252, 3, 13, 35, 'Shorts Deportivos', 'Descripción de Shorts Deportivos', 367.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(253, 3, 13, 35, 'Chaqueta Casual', 'Descripción de Chaqueta Casual', 97.00, 50, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(254, 3, 13, 35, 'Suéter Lana', 'Descripción de Suéter Lana', 476.00, 48, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(255, 3, 13, 35, 'Bermudas', 'Descripción de Bermudas', 415.00, 41, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(256, 3, 13, 35, 'Camiseta Deportiva', 'Descripción de Camiseta Deportiva', 425.00, 37, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(257, 3, 13, 35, 'Pantalón Cargo', 'Descripción de Pantalón Cargo', 256.00, 10, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(258, 3, 13, 36, 'Blusa Elegante', 'Descripción de Blusa Elegante', 139.00, 46, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(259, 3, 13, 36, 'Jeans Skinny', 'Descripción de Jeans Skinny', 460.00, 10, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(260, 3, 13, 36, 'Vestido Casual', 'Descripción de Vestido Casual', 86.00, 47, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(261, 3, 13, 36, 'Top Deportivo', 'Descripción de Top Deportivo', 211.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(262, 3, 13, 36, 'Falda Midi', 'Descripción de Falda Midi', 323.00, 27, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(263, 3, 13, 36, 'Chaqueta Mezclilla', 'Descripción de Chaqueta Mezclilla', 69.00, 21, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(264, 3, 13, 36, 'Leggings Premium', 'Descripción de Leggings Premium', 273.00, 21, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(265, 3, 13, 36, 'Cardigan', 'Descripción de Cardigan', 281.00, 42, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(266, 3, 13, 36, 'Blusa Floral', 'Descripción de Blusa Floral', 498.00, 26, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(267, 3, 13, 36, 'Pantalón Palazzo', 'Descripción de Pantalón Palazzo', 432.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(268, 3, 13, 37, 'Camiseta Niño', 'Descripción de Camiseta Niño', 163.00, 17, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(269, 3, 13, 37, 'Pantalón Niño', 'Descripción de Pantalón Niño', 179.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(270, 3, 13, 37, 'Sudadera Infantil', 'Descripción de Sudadera Infantil', 318.00, 47, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(271, 3, 13, 37, 'Shorts Niño', 'Descripción de Shorts Niño', 141.00, 38, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(272, 3, 13, 37, 'Falda Niña', 'Descripción de Falda Niña', 129.00, 26, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(273, 3, 13, 37, 'Blusa Niña', 'Descripción de Blusa Niña', 371.00, 27, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(274, 3, 13, 37, 'Pantalón Niña', 'Descripción de Pantalón Niña', 423.00, 20, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(275, 3, 13, 37, 'Chaqueta Infantil', 'Descripción de Chaqueta Infantil', 136.00, 25, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(276, 3, 13, 37, 'Buzo Infantil', 'Descripción de Buzo Infantil', 399.00, 13, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(277, 3, 13, 37, 'Leggings Niña', 'Descripción de Leggings Niña', 431.00, 14, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(278, 3, 14, 38, 'Licuadora', 'Descripción de Licuadora', 171.00, 50, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(279, 3, 14, 38, 'Microondas', 'Descripción de Microondas', 408.00, 12, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(280, 3, 14, 38, 'Cafetera', 'Descripción de Cafetera', 483.00, 10, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(281, 3, 14, 38, 'Tostador', 'Descripción de Tostador', 59.00, 48, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(282, 3, 14, 38, 'Olla Arrocera', 'Descripción de Olla Arrocera', 258.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(283, 3, 14, 38, 'Plancha Eléctrica', 'Descripción de Plancha Eléctrica', 54.00, 34, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(284, 3, 14, 38, 'Freidora Aire', 'Descripción de Freidora Aire', 255.00, 44, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(285, 3, 14, 38, 'Batidora', 'Descripción de Batidora', 432.00, 48, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(286, 3, 14, 38, 'Exprimidor', 'Descripción de Exprimidor', 157.00, 17, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(287, 3, 14, 38, 'Hervidor Eléctrico', 'Descripción de Hervidor Eléctrico', 218.00, 39, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(288, 3, 14, 39, 'Sofá 3 Puestos', 'Descripción de Sofá 3 Puestos', 276.00, 35, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(289, 3, 14, 39, 'Mesa Comedor', 'Descripción de Mesa Comedor', 410.00, 31, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(290, 3, 14, 39, 'Silla Oficina', 'Descripción de Silla Oficina', 132.00, 43, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(291, 3, 14, 39, 'Cama Queen', 'Descripción de Cama Queen', 100.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(292, 3, 14, 39, 'Closet Madera', 'Descripción de Closet Madera', 105.00, 40, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(293, 3, 14, 39, 'Biblioteca', 'Descripción de Biblioteca', 352.00, 31, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(294, 3, 14, 39, 'Mesita Noche', 'Descripción de Mesita Noche', 336.00, 27, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(295, 3, 14, 39, 'Escritorio', 'Descripción de Escritorio', 342.00, 24, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(296, 3, 14, 39, 'Estantería', 'Descripción de Estantería', 266.00, 50, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(297, 3, 14, 39, 'Modular Tv', 'Descripción de Modular Tv', 476.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(298, 3, 14, 40, 'Cuadro Moderno', 'Descripción de Cuadro Moderno', 380.00, 24, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(299, 3, 14, 40, 'Espejo Decorativo', 'Descripción de Espejo Decorativo', 218.00, 41, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(300, 3, 14, 40, 'Lámpara Piso', 'Descripción de Lámpara Piso', 294.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(301, 3, 14, 40, 'Cojín', 'Descripción de Cojín', 112.00, 48, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(302, 3, 14, 40, 'Cortina Premium', 'Descripción de Cortina Premium', 211.00, 18, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(303, 3, 14, 40, 'Tapete', 'Descripción de Tapete', 449.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(304, 3, 14, 40, 'Jarrón Cerámica', 'Descripción de Jarrón Cerámica', 349.00, 46, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(305, 3, 14, 40, 'Alfombra', 'Descripción de Alfombra', 87.00, 46, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(306, 3, 14, 40, 'Vela Aromática', 'Descripción de Vela Aromática', 478.00, 15, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(307, 3, 14, 40, 'Reloj Pared', 'Descripción de Reloj Pared', 203.00, 49, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(308, 4, 6, 11, 'Samsung Galaxy S21', 'Descripción de Samsung Galaxy S21', 350.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(309, 4, 6, 11, 'iPhone 13 Pro', 'Descripción de iPhone 13 Pro', 460.00, 34, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(310, 4, 6, 11, 'Xiaomi 12', 'Descripción de Xiaomi 12', 381.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(311, 4, 6, 11, 'Google Pixel 6', 'Descripción de Google Pixel 6', 146.00, 47, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(312, 4, 6, 11, 'OnePlus 9', 'Descripción de OnePlus 9', 452.00, 35, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(313, 4, 6, 11, 'Motorola Edge', 'Descripción de Motorola Edge', 90.00, 15, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(314, 4, 6, 11, 'Realme GT', 'Descripción de Realme GT', 410.00, 14, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(315, 4, 6, 11, 'Nothing Phone', 'Descripción de Nothing Phone', 147.00, 45, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(316, 4, 6, 11, 'Sony Xperia', 'Descripción de Sony Xperia', 480.00, 16, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(317, 4, 6, 11, 'OPPO Find X3', 'Descripción de OPPO Find X3', 186.00, 31, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(318, 4, 6, 12, 'MacBook Pro 16', 'Descripción de MacBook Pro 16', 50.00, 31, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(319, 4, 6, 12, 'Dell XPS 13', 'Descripción de Dell XPS 13', 355.00, 37, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(320, 4, 6, 12, 'HP Pavilion 15', 'Descripción de HP Pavilion 15', 125.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(321, 4, 6, 12, 'Lenovo ThinkPad', 'Descripción de Lenovo ThinkPad', 286.00, 18, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(322, 4, 6, 12, 'ASUS VivoBook', 'Descripción de ASUS VivoBook', 97.00, 28, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(323, 4, 6, 12, 'Acer Aspire 5', 'Descripción de Acer Aspire 5', 68.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(324, 4, 6, 12, 'MSI GS66', 'Descripción de MSI GS66', 113.00, 40, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(325, 4, 6, 12, 'Razer Blade', 'Descripción de Razer Blade', 477.00, 50, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(326, 4, 6, 12, 'LG Gram', 'Descripción de LG Gram', 59.00, 27, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(327, 4, 6, 12, 'ROG Gaming Laptop', 'Descripción de ROG Gaming Laptop', 112.00, 47, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(328, 4, 6, 41, 'Cargador Rápido', 'Descripción de Cargador Rápido', 235.00, 15, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(329, 4, 6, 41, 'Cable USB-C', 'Descripción de Cable USB-C', 189.00, 44, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(330, 4, 6, 41, 'Protector Pantalla', 'Descripción de Protector Pantalla', 461.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(331, 4, 6, 41, 'Funda Teléfono', 'Descripción de Funda Teléfono', 315.00, 50, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(332, 4, 6, 41, 'Audífonos Inalámbricos', 'Descripción de Audífonos Inalámbricos', 69.00, 30, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(333, 4, 6, 41, 'Power Bank', 'Descripción de Power Bank', 195.00, 31, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(334, 4, 6, 41, 'Soporte Móvil', 'Descripción de Soporte Móvil', 211.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(335, 4, 6, 41, 'Protector Cámara', 'Descripción de Protector Cámara', 109.00, 44, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(336, 4, 6, 41, 'Anillo Soporte', 'Descripción de Anillo Soporte', 465.00, 39, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(337, 4, 6, 41, 'Cable HDMI', 'Descripción de Cable HDMI', 395.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(338, 4, 7, 42, 'Camiseta Básica', 'Descripción de Camiseta Básica', 481.00, 12, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(339, 4, 7, 42, 'Pantalón Denim', 'Descripción de Pantalón Denim', 235.00, 30, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(340, 4, 7, 42, 'Camisa Social', 'Descripción de Camisa Social', 154.00, 21, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(341, 4, 7, 42, 'Polo Premium', 'Descripción de Polo Premium', 392.00, 24, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(342, 4, 7, 42, 'Shorts Deportivos', 'Descripción de Shorts Deportivos', 106.00, 47, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(343, 4, 7, 42, 'Chaqueta Casual', 'Descripción de Chaqueta Casual', 404.00, 18, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(344, 4, 7, 42, 'Suéter Lana', 'Descripción de Suéter Lana', 326.00, 28, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(345, 4, 7, 42, 'Bermudas', 'Descripción de Bermudas', 242.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(346, 4, 7, 42, 'Camiseta Deportiva', 'Descripción de Camiseta Deportiva', 459.00, 45, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(347, 4, 7, 42, 'Pantalón Cargo', 'Descripción de Pantalón Cargo', 267.00, 34, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(348, 4, 7, 43, 'Blusa Elegante', 'Descripción de Blusa Elegante', 99.00, 30, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(349, 4, 7, 43, 'Jeans Skinny', 'Descripción de Jeans Skinny', 286.00, 15, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(350, 4, 7, 43, 'Vestido Casual', 'Descripción de Vestido Casual', 184.00, 13, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(351, 4, 7, 43, 'Top Deportivo', 'Descripción de Top Deportivo', 222.00, 35, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(352, 4, 7, 43, 'Falda Midi', 'Descripción de Falda Midi', 152.00, 20, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(353, 4, 7, 43, 'Chaqueta Mezclilla', 'Descripción de Chaqueta Mezclilla', 349.00, 41, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(354, 4, 7, 43, 'Leggings Premium', 'Descripción de Leggings Premium', 326.00, 19, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(355, 4, 7, 43, 'Cardigan', 'Descripción de Cardigan', 73.00, 16, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(356, 4, 7, 43, 'Blusa Floral', 'Descripción de Blusa Floral', 58.00, 16, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(357, 4, 7, 43, 'Pantalón Palazzo', 'Descripción de Pantalón Palazzo', 494.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(358, 4, 7, 44, 'Camiseta Niño', 'Descripción de Camiseta Niño', 126.00, 12, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(359, 4, 7, 44, 'Pantalón Niño', 'Descripción de Pantalón Niño', 333.00, 50, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(360, 4, 7, 44, 'Sudadera Infantil', 'Descripción de Sudadera Infantil', 442.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(361, 4, 7, 44, 'Shorts Niño', 'Descripción de Shorts Niño', 56.00, 36, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(362, 4, 7, 44, 'Falda Niña', 'Descripción de Falda Niña', 397.00, 28, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(363, 4, 7, 44, 'Blusa Niña', 'Descripción de Blusa Niña', 374.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(364, 4, 7, 44, 'Pantalón Niña', 'Descripción de Pantalón Niña', 315.00, 34, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(365, 4, 7, 44, 'Chaqueta Infantil', 'Descripción de Chaqueta Infantil', 373.00, 13, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(366, 4, 7, 44, 'Buzo Infantil', 'Descripción de Buzo Infantil', 133.00, 39, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(367, 4, 7, 44, 'Leggings Niña', 'Descripción de Leggings Niña', 216.00, 35, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(368, 4, 8, 15, 'Licuadora', 'Descripción de Licuadora', 404.00, 10, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(369, 4, 8, 15, 'Microondas', 'Descripción de Microondas', 123.00, 17, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(370, 4, 8, 15, 'Cafetera', 'Descripción de Cafetera', 399.00, 37, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(371, 4, 8, 15, 'Tostador', 'Descripción de Tostador', 445.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(372, 4, 8, 15, 'Olla Arrocera', 'Descripción de Olla Arrocera', 241.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(373, 4, 8, 15, 'Plancha Eléctrica', 'Descripción de Plancha Eléctrica', 54.00, 19, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(374, 4, 8, 15, 'Freidora Aire', 'Descripción de Freidora Aire', 173.00, 15, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(375, 4, 8, 15, 'Batidora', 'Descripción de Batidora', 142.00, 11, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(376, 4, 8, 15, 'Exprimidor', 'Descripción de Exprimidor', 63.00, 19, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(377, 4, 8, 15, 'Hervidor Eléctrico', 'Descripción de Hervidor Eléctrico', 214.00, 37, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(378, 4, 8, 45, 'Sofá 3 Puestos', 'Descripción de Sofá 3 Puestos', 260.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(379, 4, 8, 45, 'Mesa Comedor', 'Descripción de Mesa Comedor', 420.00, 32, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(380, 4, 8, 45, 'Silla Oficina', 'Descripción de Silla Oficina', 383.00, 10, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(381, 4, 8, 45, 'Cama Queen', 'Descripción de Cama Queen', 414.00, 15, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(382, 4, 8, 45, 'Closet Madera', 'Descripción de Closet Madera', 339.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05');
INSERT INTO `productos` (`id`, `tenant_id`, `categoria_id`, `subcategoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(383, 4, 8, 45, 'Biblioteca', 'Descripción de Biblioteca', 60.00, 35, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(384, 4, 8, 45, 'Mesita Noche', 'Descripción de Mesita Noche', 373.00, 19, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(385, 4, 8, 45, 'Escritorio', 'Descripción de Escritorio', 479.00, 33, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(386, 4, 8, 45, 'Estantería', 'Descripción de Estantería', 288.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(387, 4, 8, 45, 'Modular Tv', 'Descripción de Modular Tv', 385.00, 48, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(388, 4, 8, 46, 'Cuadro Moderno', 'Descripción de Cuadro Moderno', 387.00, 13, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(389, 4, 8, 46, 'Espejo Decorativo', 'Descripción de Espejo Decorativo', 86.00, 47, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(390, 4, 8, 46, 'Lámpara Piso', 'Descripción de Lámpara Piso', 493.00, 13, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(391, 4, 8, 46, 'Cojín', 'Descripción de Cojín', 186.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(392, 4, 8, 46, 'Cortina Premium', 'Descripción de Cortina Premium', 99.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(393, 4, 8, 46, 'Tapete', 'Descripción de Tapete', 286.00, 38, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(394, 4, 8, 46, 'Jarrón Cerámica', 'Descripción de Jarrón Cerámica', 131.00, 13, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(395, 4, 8, 46, 'Alfombra', 'Descripción de Alfombra', 339.00, 29, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(396, 4, 8, 46, 'Vela Aromática', 'Descripción de Vela Aromática', 155.00, 22, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05'),
(397, 4, 8, 46, 'Reloj Pared', 'Descripción de Reloj Pared', 220.00, 28, NULL, 1, '2026-01-11 20:03:11', '2026-01-12 00:21:05');

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
(46, 4, 8, 'Decoración', 'Artículos de decoración', 1, '2026-01-11 20:03:11', '2026-01-11 20:03:11');

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
(2, 'Mauricio', 'Mauricio', 'mauricio', '573112969569', NULL, 'claro', 'petroleo', 'activo', '2026-01-09 21:11:21', '2026-01-10 19:50:18'),
(3, 'Distribuciones EBS', 'Distribuciones EBS', 'distribuciones-ebs', '+573001234567', NULL, 'claro', 'gris', 'activo', '2026-01-09 21:11:21', '2026-01-12 00:30:48'),
(4, 'Tech Store - Prueba', 'Tech Store - Prueba', 'tech-store', '+573334567890', NULL, 'oscuro', 'naranja', 'activo', '2026-01-09 22:53:09', '2026-01-10 19:43:06');

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
(1, 1, 'admin', 'admin@tienda.local', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'Administrador', 'admin', 1, '2025-12-24 17:35:46', '2026-01-10 19:50:07'),
(2, 4, 'admin_tech', 'admin@techstore.local', 'e937eb7cc144efab9efc6ea003bf88b027a3d7061d283365fe23f1f660740994', 'Tech Store - Prueba', 'admin', 1, '2026-01-09 22:53:09', '2026-01-09 23:38:54'),
(3, NULL, 'superadmin', 'superadmin@sistema.local', 'd357150517d3e65ae84985f7b705ad99fdc38372a22ecea0cecaf8aaf820a249', 'Super Administrador', 'superadmin', 1, '2026-01-09 23:49:15', '2026-01-12 00:08:54');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT de la tabla `pedido_historial`
--
ALTER TABLE `pedido_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=398;

--
-- AUTO_INCREMENT de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
