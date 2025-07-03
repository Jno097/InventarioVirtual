-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-06-2025 a las 23:04:54
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
-- Base de datos: `inventario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `armarios`
--

CREATE TABLE `armarios` (
  `nombre` text NOT NULL,
  `descrip` text NOT NULL,
  `id_tabla` int(16) NOT NULL,
  `ubicacion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `armarios`
--

INSERT INTO `armarios` (`nombre`, `descrip`, `id_tabla`, `ubicacion`) VALUES
('aa', 'ssss', 1, 'Infor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE `comentario` (
  `nombre` text NOT NULL,
  `curso` text NOT NULL,
  `comentario` text NOT NULL,
  `id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `nombre` text NOT NULL COMMENT 'Nombre del objeto',
  `descrip` text NOT NULL COMMENT 'Descripcion del objeto',
  `stock` int(5) NOT NULL COMMENT 'Cantidad de objetos',
  `categoria` text NOT NULL COMMENT 'Tipo de objeto',
  `estado` text NOT NULL COMMENT 'on es principal, off es secundario',
  `imagen` text DEFAULT NULL,
  `id` int(5) NOT NULL,
  `id_tabla` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`nombre`, `descrip`, `stock`, `categoria`, `estado`, `imagen`, `id`, `id_tabla`) VALUES
('aa', 'dasdasasd', 4, 'dasss', 'on', 'https://i.ibb.co/r2GTjDGt/58ea1316902e.png', 32, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login`
--

CREATE TABLE `login` (
  `nombre` text NOT NULL,
  `mail` text NOT NULL,
  `contraseña` text NOT NULL,
  `cate` text NOT NULL,
  `verificado` int(1) NOT NULL,
  `curso` text DEFAULT NULL,
  `id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `login`
--

INSERT INTO `login` (`nombre`, `mail`, `contraseña`, `cate`, `verificado`, `curso`, `id`) VALUES
('ssss', 'janoheitz@sagradocorazon.edu.ar', 'ssss', 'admin', 1, NULL, 1),
('dfsdfsdfsdfsdfs', 'facundoparedes@sagradocorazon.edu.ar', 'ssss', 'profe', 1, NULL, 3),
('abou abou abou', 'nicolasaboueid@sagradocorazon.edu.ar', 'ssss', 'estu', 1, '7mo info', 4);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `armarios`
--
ALTER TABLE `armarios`
  ADD PRIMARY KEY (`id_tabla`);

--
-- Indices de la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `armarios`
--
ALTER TABLE `armarios`
  MODIFY `id_tabla` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `comentario`
--
ALTER TABLE `comentario`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
