-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-11-2025 a las 02:01:23
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tdcsa`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinos`
--

CREATE TABLE `destinos` (
  `id` int(11) NOT NULL,
  `denominacion` varchar(120) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `destinos`
--

INSERT INTO `destinos` (`id`, `denominacion`) VALUES
(1, 'Capilla del Monte'),
(2, 'Morteros'),
(4, 'Río Cuarto'),
(3, 'Toledo'),
(5, 'Villa María');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id` int(11) NOT NULL,
  `denominacion` varchar(80) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id`, `denominacion`) VALUES
(1, 'Iveco'),
(3, 'Scania'),
(4, 'Volkswagen'),
(2, 'Volvo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `id` int(11) NOT NULL,
  `denominacion` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `niveles`
--

INSERT INTO `niveles` (`id`, `denominacion`) VALUES
(1, 'Admin'),
(2, 'Operador'),
(3, 'Chofer');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transportes`
--

CREATE TABLE `transportes` (
  `id` int(11) NOT NULL,
  `marca_id` int(11) NOT NULL,
  `modelo` varchar(60) COLLATE utf8mb4_spanish_ci NOT NULL,
  `patente` varchar(10) COLLATE utf8mb4_spanish_ci NOT NULL,
  `anio` smallint(6) DEFAULT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `transportes`
--

INSERT INTO `transportes` (`id`, `marca_id`, `modelo`, `patente`, `anio`, `disponible`, `fecha_creacion`) VALUES
(1, 1, 'Daily Furgón', 'AC020K', 2023, 1, '2024-01-10 08:00:00'),
(2, 3, 'Serie P', 'AA322CX', 2022, 1, '2024-01-12 08:00:00'),
(3, 1, 'Daily Chasis', 'AD698HA', 2021, 1, '2024-01-15 08:00:00'),
(4, 4, 'Delivery', 'AE345BC', 2020, 0, '2024-01-20 08:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `apellido` varchar(80) COLLATE utf8mb4_spanish_ci NOT NULL,
  `nombre` varchar(80) COLLATE utf8mb4_spanish_ci NOT NULL,
  `dni` varchar(10) COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL,
  `clave` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `id_nivel` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `imagen` varchar(150) COLLATE utf8mb4_spanish_ci DEFAULT 'profile-img.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `apellido`, `nombre`, `dni`, `usuario`, `clave`, `activo`, `id_nivel`, `fecha_creacion`, `imagen`) VALUES
(1, 'Palacios', 'Sue', '20123456', 'sue', '12345', 1, 1, '2023-10-01 10:00:00', 'sue.jpg'),
(3, 'Alvarez', 'Marcos', '30111222', 'marcos', '12345', 1, 2, '2023-11-05 09:15:00', 'messages-3.jpg'),
(4, 'Perez', 'Juan', '31222333', 'juan', '12345', 1, 3, '2023-11-06 09:15:00', 'juan.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `viajes`
--

CREATE TABLE `viajes` (
  `id` int(11) NOT NULL,
  `chofer_id` int(11) NOT NULL,
  `transporte_id` int(11) NOT NULL,
  `destino_id` int(11) NOT NULL,
  `fecha_programada` date NOT NULL,
  `costo` decimal(12,2) NOT NULL,
  `porcentaje_chofer` int(11) NOT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `viajes`
--

INSERT INTO `viajes` (`id`, `chofer_id`, `transporte_id`, `destino_id`, `fecha_programada`, `costo`, `porcentaje_chofer`, `creado_por`, `fecha_creacion`) VALUES
(1, 3, 1, 1, '2025-11-02', '300000.00', 10, 1, '2025-10-20 09:00:00'),
(2, 3, 2, 2, '2025-11-03', '100000.00', 15, 1, '2025-10-21 09:15:00'),
(3, 4, 3, 3, '2025-11-05', '250000.00', 10, 2, '2025-10-23 09:30:00'),
(4, 4, 1, 1, '2025-11-04', '150000.00', 10, 2, '2025-10-24 09:45:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `destinos`
--
ALTER TABLE `destinos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_destinos_denominacion` (`denominacion`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_marcas_denominacion` (`denominacion`);

--
-- Indices de la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `transportes`
--
ALTER TABLE `transportes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_transportes_patente` (`patente`),
  ADD KEY `idx_transportes_marca` (`marca_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuarios_dni` (`dni`),
  ADD UNIQUE KEY `uq_usuarios_usuario` (`usuario`),
  ADD KEY `idx_usuarios_nivel` (`id_nivel`);

--
-- Indices de la tabla `viajes`
--
ALTER TABLE `viajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_viajes_fecha` (`fecha_programada`),
  ADD KEY `idx_viajes_destino` (`destino_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `destinos`
--
ALTER TABLE `destinos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `transportes`
--
ALTER TABLE `transportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `viajes`
--
ALTER TABLE `viajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
