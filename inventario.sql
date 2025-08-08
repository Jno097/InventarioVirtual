-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-08-2025 a las 20:45:13
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
('FIlamentos', 'Armario en el que se guardan los filamentos', 1, 'Armario de sala de profes'),
('Componentes de Arduino', 'Armario en donde se guardan los componentes para profesores', 2, 'Sala de servicio técnico'),
('Armario de Herramientas', 'Armario de las herramientas que hay en el laboratorio', 3, 'Sala de servicio técnico');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE `comentario` (
  `titulo` text NOT NULL,
  `id_com` int(5) NOT NULL,
  `descripcion` text NOT NULL,
  `id_tabla` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `leido` enum('si','no') NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentario`
--

INSERT INTO `comentario` (`titulo`, `id_com`, `descripcion`, `id_tabla`, `id`, `fecha`, `leido`) VALUES
('sasadsda', 1, 'sdadsasdadsasda', 1, 1, '2025-07-17 19:44:21', 'no');

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
('Filamento Azul', 'El PLA es un plástico derivado del maíz, muy fácil de imprimir, no tóxico, biobasado y compostable.\r\n\r\nLos filamentos producidos en PLA (ácido poliláctico) son los de mayor uso por su facilidad de impresión, excelente adherencia entre capas y origen vegetal, que los convierten en un polímero eco-amigable.\r\n\r\nLos filamentos de PLA PrintaLot® están aditivados con biopolímeros de última generación que le aumentan la fluidez y mejoran la resistencia de la pieza impresa y la estabilidad dimensional. Otra gran ventaja frente al PLA convencional es que tiene menor contracción y es menos propenso al warping.', 1, 'Filamento', 'on', 'https://i.ibb.co/KzqBJTbg/232840a2fbe2.jpg', 34, 1),
('filamento naranja', 'El PLA es un plástico derivado del maíz, muy fácil de imprimir, no tóxico, biobasado y compostable.\r\n\r\nLos filamentos producidos en PLA (ácido poliláctico) son los de mayor uso por su facilidad de impresión, excelente adherencia entre capas y origen vegetal, que los convierten en un polímero eco-amigable.\r\n\r\nLos filamentos de PLA PrintaLot® están aditivados con biopolímeros de última generación que le aumentan la fluidez y mejoran la resistencia de la pieza impresa y la estabilidad dimensional. Otra gran ventaja frente al PLA convencional es que tiene menor contracción y es menos propenso al warping.', 1, 'Filamento', 'on', 'https://i.ibb.co/XfYNy918/6d4d8b9539ef.jpg', 35, 1),
('filamento verde', 'El PLA es un plástico derivado del maíz, muy fácil de imprimir, no tóxico, biobasado y compostable.\r\n\r\nLos filamentos producidos en PLA (ácido poliláctico) son los de mayor uso por su facilidad de impresión, excelente adherencia entre capas y origen vegetal, que los convierten en un polímero eco-amigable.\r\n\r\nLos filamentos de PLA PrintaLot® están aditivados con biopolímeros de última generación que le aumentan la fluidez y mejoran la resistencia de la pieza impresa y la estabilidad dimensional. Otra gran ventaja frente al PLA convencional es que tiene menor contracción y es menos propenso al warping.', 1, 'Filamento', 'on', 'https://i.ibb.co/93tC4cRv/01ebcf74d5f9.jpg', 36, 1),
('filamento rojo', 'El PLA es un plástico derivado del maíz, muy fácil de imprimir, no tóxico, biobasado y compostable.\r\n\r\nLos filamentos producidos en PLA (ácido poliláctico) son los de mayor uso por su facilidad de impresión, excelente adherencia entre capas y origen vegetal, que los convierten en un polímero eco-amigable.\r\n\r\nLos filamentos de PLA PrintaLot® están aditivados con biopolímeros de última generación que le aumentan la fluidez y mejoran la resistencia de la pieza impresa y la estabilidad dimensional. Otra gran ventaja frente al PLA convencional es que tiene menor contracción y es menos propenso al warping.', 1, 'Filamento', 'on', 'https://i.ibb.co/cSxW9CQY/dde2362b333b.jpg', 37, 1),
('Filamento amarillo', 'El PLA es un plástico derivado del maíz, muy fácil de imprimir, no tóxico, biobasado y compostable.\r\n\r\nLos filamentos producidos en PLA (ácido poliláctico) son los de mayor uso por su facilidad de impresión, excelente adherencia entre capas y origen vegetal, que los convierten en un polímero eco-amigable.\r\n\r\nLos filamentos de PLA PrintaLot® están aditivados con biopolímeros de última generación que le aumentan la fluidez y mejoran la resistencia de la pieza impresa y la estabilidad dimensional. Otra gran ventaja frente al PLA convencional es que tiene menor contracción y es menos propenso al warping.', 1, 'Filamento', 'on', 'https://i.ibb.co/6RvTD24R/0c82ef9149dd.jpg', 38, 1),
('Filamento de acqua', 'El PLA es un plástico derivado del maíz, muy fácil de imprimir, no tóxico, biobasado y compostable.\r\n\r\nLos filamentos producidos en PLA (ácido poliláctico) son los de mayor uso por su facilidad de impresión, excelente adherencia entre capas y origen vegetal, que los convierten en un polímero eco-amigable.\r\n\r\nLos filamentos de PLA PrintaLot® están aditivados con biopolímeros de última generación que le aumentan la fluidez y mejoran la resistencia de la pieza impresa y la estabilidad dimensional. Otra gran ventaja frente al PLA convencional es que tiene menor contracción y es menos propenso al warping.', 1, 'Filamento', 'on', 'https://i.ibb.co/xKnHVs5N/9db727eb2a21.jpg', 39, 1),
('Filamento Violeta', 'El PLA es un plástico derivado del maíz, muy fácil de imprimir, no tóxico, biobasado y compostable.\r\n\r\nLos filamentos producidos en PLA (ácido poliláctico) son los de mayor uso por su facilidad de impresión, excelente adherencia entre capas y origen vegetal, que los convierten en un polímero eco-amigable.\r\n\r\nLos filamentos de PLA PrintaLot® están aditivados con biopolímeros de última generación que le aumentan la fluidez y mejoran la resistencia de la pieza impresa y la estabilidad dimensional. Otra gran ventaja frente al PLA convencional es que tiene menor contracción y es menos propenso al warping.', 1, 'Filamento', 'on', 'https://i.ibb.co/Fqw6MLrJ/4c61d351d470.jpg', 40, 1),
('Filamento Negro', 'El PLA es un plástico derivado del maíz, muy fácil de imprimir, no tóxico, biobasado y compostable.\r\n\r\nLos filamentos producidos en PLA (ácido poliláctico) son los de mayor uso por su facilidad de impresión, excelente adherencia entre capas y origen vegetal, que los convierten en un polímero eco-amigable.\r\n\r\nLos filamentos de PLA PrintaLot® están aditivados con biopolímeros de última generación que le aumentan la fluidez y mejoran la resistencia de la pieza impresa y la estabilidad dimensional. Otra gran ventaja frente al PLA convencional es que tiene menor contracción y es menos propenso al warping.', 1, 'Filamento', 'on', 'https://i.ibb.co/0jYwCZS6/a165ce3fe7cb.jpg', 42, 1),
('Filamento blanco', 'El PLA es un plástico derivado del maíz, muy fácil de imprimir, no tóxico, biobasado y compostable.\r\n\r\nLos filamentos producidos en PLA (ácido poliláctico) son los de mayor uso por su facilidad de impresión, excelente adherencia entre capas y origen vegetal, que los convierten en un polímero eco-amigable.\r\n\r\nLos filamentos de PLA PrintaLot® están aditivados con biopolímeros de última generación que le aumentan la fluidez y mejoran la resistencia de la pieza impresa y la estabilidad dimensional. Otra gran ventaja frente al PLA convencional es que tiene menor contracción y es menos propenso al warping.', 1, 'Filamento', 'on', 'https://i.ibb.co/1J7dBtgj/486cb9ad5045.jpg', 43, 1),
('Filamento Gris', 'El PLA es un plástico derivado del maíz, muy fácil de imprimir, no tóxico, biobasado y compostable.\r\n\r\nLos filamentos producidos en PLA (ácido poliláctico) son los de mayor uso por su facilidad de impresión, excelente adherencia entre capas y origen vegetal, que los convierten en un polímero eco-amigable.\r\n\r\nLos filamentos de PLA PrintaLot® están aditivados con biopolímeros de última generación que le aumentan la fluidez y mejoran la resistencia de la pieza impresa y la estabilidad dimensional. Otra gran ventaja frente al PLA convencional es que tiene menor contracción y es menos propenso al warping.', 1, 'Filamento', 'on', 'https://i.ibb.co/dwc0TzLz/0a269b127d82.png', 44, 1),
('Arduino', 'es una plataforma de creación de prototipos electrónicos de código abierto basada en la flexibilidad, con hardware y software fáciles de usar. Está dirigido a artistas, diseñadores, aficionados y cualquier persona interesada en crear objetos o entornos interactivos.', 1, 'Arduino', 'on', 'https://i.ibb.co/5gp5DvT2/73c6935b9293.png', 45, 2),
('Sensor ultrasonico', 'El sensor ultrasónico HC-SR04 proporciona mediciones de distancia desde 2cm hasta 500cm con una precisión cercana a los 3mm. Este módulo se diferencia al contar con pines separados para la señal de entrada y salida.', 1, 'Arduino', 'on', 'https://i.ibb.co/f7hrQ86/d5dcd6a41ab2.png', 46, 2),
('sensor de movimiento', 'El sensor PIR detecta movimiento de personas hasta 7 metros de distancia utilizando una lente de Fresnel y un elemento sensible al infrarrojo para identificar cambios en los patrones de radiación infrarroja emitida por objetos cercanos. Es una opción económica y sencilla para sistemas de seguridad, iluminación activada por movimiento y accesorios para eventos.', 1, 'Arduino', 'on', 'https://i.ibb.co/PBNvkYF/1bdec9d521b7.png', 47, 2),
('Rueda de Goma', 'Rueda para el Arduino', 1, 'Arduino', 'on', 'https://i.ibb.co/YB2xJzNN/88a7ad72a8c4.jpg', 48, 2),
('Detector de Obstaculos', 'ste Modulo sensor infrarrojo emisor y receptor se puede adaptar a luz ambiente y a la distancia de deteccion de objetos a traves de un potenciometro que viene en su PCB, La distancia de deteccion puede variarse entre 2cm y 30cm, con un angulo de detección de 35°.', 1, 'Arduino', 'on', 'https://i.ibb.co/nN4YKX92/6ec25b882ce0.jpg', 49, 2),
('Placa de Arduino', 'es una plataforma de creación de prototipos electrónicos de código abierto basada en la flexibilidad, con hardware y software fáciles de usar.', 1, 'Arduino', 'on', 'https://i.ibb.co/zWm1HJVQ/feacb9414e1d.jpg', 50, 2),
('Caja Reductora', 'ideal para proyectos de robótica con ruedas. Este motor cuenta con un diseño de doble eje que permite la adición de un ENCODER para un control preciso de la velocidad.', 1, 'Arduino', 'on', 'https://i.ibb.co/Zp2t8KRW/ce695490682d.jpg', 51, 2),
('Pack de destornilladores', 'SET X6 DE DESTORNILLADORES MANGO DE GOMA ANTI RESBALANTE Y PUNTA IMANTADA', 1, 'Herramientas', 'on', 'https://i.ibb.co/bMnP0jQN/5c58a9e6f521.jpg', 52, 3),
('Pack de llaves', 'Este set de tubos de 108 piezas está construido en cromo vanadio, incluyendo las piezas más utilizadas en mecánica. De excelente calidad, viene en un maletín plástico con doble traba para mayor seguridad.', 1, 'Herramientas', 'on', 'https://i.ibb.co/zHsF0wft/243841f4b56b.jpg', 53, 3),
('pack de pinza', 'es la herramienta ideal para cualquier proyecto de bricolaje. Gracias a su diseño compacto y ergonómico, estas pinzas alicates facilitan la realización de diversas tareas con facilidad y precisión.', 1, 'Herramientas', 'on', 'https://i.ibb.co/NgQc76HQ/0c18b3c2f8e6.jpg', 54, 3);

-- --------------------------------------------------------

--