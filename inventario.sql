-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-08-2025 a las 22:54:57
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
-- Estructura de tabla para la tabla `login`
--

CREATE TABLE `login` (
  `nombre` text NOT NULL,
  `mail` text NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `cate` text NOT NULL,
  `verificado` int(1) NOT NULL,
  `curso` text DEFAULT NULL,
  `id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `login`
--

INSERT INTO `login` (`nombre`, `mail`, `contrasena`, `cate`, `verificado`, `curso`, `id`) VALUES
('ssss', 'janoheitz@sagradocorazon.edu.ar', '$2y$10$SrWV015ZY.AtNwCjC7DZDOTJZhqol.aL7OGeeuEtQLXQ7qfql.hBq', 'admin', 1, NULL, 1),
('dfsdfsdfsdfsdfs', 'facundoparedes@sagradocorazon.edu.ar', '$2y$10$yCPyctBbs2tk2B0F6jQZJuBY8hQ68mZQ1S4udon3Ykvi.yk/Wl8K6', 'profe', 1, NULL, 3),
('Pepe Juan 43 03', 'nicolasaboueid@sagradocorazon.edu.ar', '$2y$10$fXcrXwDSQCDuaeVsF.LZLe7zxHc4T0yczAZwJ5q1R8568Qz/OxxQa', 'estu', 1, '7mo info', 5);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;