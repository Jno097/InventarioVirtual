-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-08-2025 a las 21:04:52
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
('Faltan filamentos PLA azul', 2, 'Se agotó el rollo de PLA azul. Urge para proyecto de maquetas.', 1, 8, '2025-03-15 08:20:00', 'no'),
('No hay servomotores SG90', 3, 'No quedan servomotores en el armario de Arduino. Necesarios para robótica.', 2, 14, '2025-04-22 11:45:00', 'no'),
('Desapareció el soldador', 4, 'El soldador de estaño no está en su sitio. Último uso registrado hace 2 semanas.', 3, 19, '2025-05-30 14:10:00', 'no'),
('Falta filamento PETG transparente', 5, 'Se terminó el PETG transparente para prototipos.', 1, 11, '2025-01-10 16:30:00', 'no'),
('Sin capacitores 10μF', 6, 'No hay capacitores electrolíticos de 10μF. Faltan para prácticas de electrónica.', 2, 22, '2025-02-18 09:55:00', 'no'),
('Pinza amperimétrica dañada', 7, 'La pinza amperimétrica no enciende. Posible batería agotada o falla interna.', 3, 7, '2025-06-05 13:25:00', 'no'),
('Filamento flexible agotado', 8, 'TPU negro terminado. Se usa en próxima clase de diseños ergonómicos.', 1, 25, '2025-07-12 10:15:00', 'no'),
('Faltan módulos Bluetooth HC-05', 9, 'No hay módulos Bluetooth para proyecto de comunicación inalámbrica.', 2, 16, '2025-08-20 17:40:00', 'no'),
('Destornillador plano perdido', 10, 'El destornillador plano de 3mm no aparece en el kit.', 3, 10, '2025-09-03 12:00:00', 'no'),
('PLA negro bajo stock', 11, 'Solo queda 15% del rollo de PLA negro. Comprar antes de fin de mes.', 1, 13, '2025-10-11 15:50:00', 'no'),
('Sin sensores de temperatura DHT11', 12, 'No hay sensores DHT11 para el taller de IoT.', 2, 21, '2025-11-25 08:05:00', 'no'),
('Llave Allen faltante', 13, 'Falta la llave Allen de 4mm del juego completo.', 3, 18, '2025-12-07 19:30:00', 'no'),
('Filamento ABS rojo agotado', 14, 'ABS rojo terminado. Se usa en impresiones de alta resistencia.', 1, 9, '2025-04-08 07:45:00', 'no'),
('No hay pantallas LCD 16x2', 15, 'No quedan pantallas LCD para proyectos de visualización.', 2, 24, '2025-05-19 20:20:00', 'no'),
('Alicate de corte oxidado', 16, 'El alicate de corte tiene óxido y no corta bien. Necesita reemplazo.', 3, 20, '2025-08-14 18:15:00', 'no');

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
('pack de pinza', 'es la herramienta ideal para cualquier proyecto de bricolaje. Gracias a su diseño compacto y ergonómico, estas pinzas alicates facilitan la realización de diversas tareas con facilidad y precisión.', 1, 'Herramientas', 'on', 'https://i.ibb.co/NgQc76HQ/0c18b3c2f8e6.jpg', 54, 3),
('Pack de llaves', 'Este set de tubos de 108 piezas está construido en cromo vanadio, incluyendo las piezas más utilizadas en mecánica. De excelente calidad, viene en un maletín plástico con doble traba para mayor seguridad.', 1, 'Herramientas', 'off', 'https://i.ibb.co/zHsF0wft/243841f4b56b.jpg', 55, 3),
('pack de pinza', 'es la herramienta ideal para cualquier proyecto de bricolaje. Gracias a su diseño compacto y ergonómico, estas pinzas alicates facilitan la realización de diversas tareas con facilidad y precisión.', 1, 'Herramientas', 'off', 'https://i.ibb.co/NgQc76HQ/0c18b3c2f8e6.jpg', 56, 3);

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
('admin', 'janoheitz@sagradocorazon.edu.ar', '$2y$10$SrWV015ZY.AtNwCjC7DZDOTJZhqol.aL7OGeeuEtQLXQ7qfql.hBq', 'admin', 1, NULL, 1),
('Dr. Facundo', 'facundoparedes@sagradocorazon.edu.ar', '$2y$10$yCPyctBbs2tk2B0F6jQZJuBY8hQ68mZQ1S4udon3Ykvi.yk/Wl8K6', 'profe', 1, NULL, 3),
('Demian', 'yonawf326@aravites.com', '$2y$10$Ocs1WlLrLKMc/ihaGdAJl.o48CH7VsvZVhU0w94I/1j5sysPXssGi', 'estu', 1, '4to sociales', 5),
('Juan Pérez', 'juan.perez@example.com', '$2y$10$XLBRJtnJ84votaOey1epKeuNjhYGi06qJzZfxWkL5.o2EhTANl39m', 'profe', 0, NULL, 6),
('María González', 'maria.gonzalez@testmail.com', '$2y$10$frbHZuplSDCVBB4f7VgIeOF21fNv1T3nFc1sfrN84kqCltOrikjKO', 'estu', 1, '1ero SOC', 7),
('Carlos López', 'carlos.lopez@fakedomain.com', '$2y$10$KE7u9W0e3jw4wFkUSqHNUugPJHxvwFUV21l0XrL3UInZeRyHWjbgq', 'estu', 1, '2do ECO', 8),
('Ana Martínez', 'ana.martinez@demoemail.net', '$2y$10$tXTKV6ql2u2IuCz27bAbteaw/F8uegI0V3LJYyg65nJRXgl1ezT5C', 'profe', 1, NULL, 9),
('Luis Rodríguez', 'luis.rodriguez@mockmail.org', '$2y$10$iEYxrGrjY8.5MwlDZBbG9.MEjwcicE74/WEQrkOLQG78CyVlz6gY.', 'estu', 1, '3ro ELEC', 10),
('Laura Sánchez', 'laura.sanchez@pruebacorreo.com', '$2y$10$Z.VkSdATexAbQn5EBxjsTu.gPjHriSrdubS.vHDfxSamdGq/mWKL2', 'estu', 1, '4to INFO', 11),
('Pedro Ramírez', 'pedro.ramirez@ficticiomail.com', '$2y$10$9.uPaSC39kUeznuoQp2Ge.0pFgMt01G9rjs6wdJ/hOcNDHvAxczvq', 'profe', 0, NULL, 12),
('Sofía Díaz', 'sofia.diaz@testingemail.co', '$2y$10$GpcjnW0uYHBk3o7/Yl8keuwnSwtEp.mnZRQ1jtiyz6pl1fFpH.eua', 'estu', 1, '5to ARTE', 13),
('Jorge Ruiz', 'jorge.ruiz@ejemplomail.com', '$2y$10$sDB26.GTh5VJktJpwyrAru5Rajaw8RnSdwENmO2k.BeukzLOEHTx2', 'estu', 1, '6to SOC', 14),
('Elena Castro', 'elena.castro@falsemail.net', '$2y$10$sxTXGaTYi31uHsujBYITfOlRQjRXvdyb9RA4VrqDSb.Kxgdty.Xei', 'estu', 1, '7mo ECO', 15),
('Diego Morales', 'diego.morales@testfake.com', '$2y$10$/.hOSnIwmfqsHpdtFAgI..V12wfTJBGRgLYk/kt7F.sle/XF6lSGu', 'estu', 1, '2do ELEC', 16),
('Valeria Rojas', 'valeria.rojas@fakemail.org', '$2y$10$1Qgxm1lrUAg.7Hh27p5Ny.c56M312Zcgq82jWcvmY4qVbdrLQJtwm', 'profe', 1, NULL, 17),
('Andrés Herrera', 'andres.herrera@demoexample.net', '$2y$10$zq.pSkIFGpM92AW1Q1LXCuQYDuSbhGBtMazNBwX2OVg1AUTTDxqzi', 'estu', 1, '3ro INFO', 18),
('Camila Silva', 'camila.silva@mocktest.com', '$2y$10$YLcPNP3db4T4a81sScUuK.o.ds7rH93.U9.z7o9WzV.PWT7K69TuW', 'estu', 1, '1ero ARTE', 19),
('Ricardo Méndez', 'ricardo.mendez@pruebaemail.com', '$2y$10$1..jO3WTlVIAYEJAa9q1oOtZFdaSm37yr8CbirveUtj4ET0PNibWK', 'profe', 1, NULL, 20),
('Lucía Fernández', 'lucia.fernandez@ficticiodemo.net', '$2y$10$xD0VjLWEbiAaa2Z5c2OnZeOvZbLatR/pIPIBoqlshs7fliDtnpA7q', 'estu', 1, '4to ECO', 21),
('Fernando Castro', 'fernando.castro@testingdata.co', '$2y$10$UgdaborUC.9z.FLtVIMIJuXAV8ChVS/Bo/pncBDkpg21ff9QCveUa', 'estu', 1, '5to SOC', 22),
('Gabriela Ortega', 'gabriela.ortega@exampletest.org', '$2y$10$JWK4yaaxz6jarU8yR7cmrOzy5As.f0MViIYu/Sv6V9B5RADw6Rf6G', 'profe', 1, NULL, 23),
('Martín Vargas', 'martin.vargas@falsedata.com', '$2y$10$MpqK3AFOU61JDBQDxXmLJ.FHgx0jyIqqN2Z9GJi4GIm8U1h5GRJIa', 'estu', 1, '6to ELEC', 24),
('Daniela Paredes', 'daniela.paredes@demotestmail.net', '$2y$10$RTCf4KHv3SDHmRZI9/yEteyF5lG5NdoPkoJGhKIxPsShYEJqNykae', 'estu', 1, '7mo INFO', 25),
('Roberto Núñez', 'roberto.nunez@mockdemo.com', '$2y$10$clS5mNIZ0WEoqMsD5i22eOHk39lrQEV7q8FeM9BneQWXUZ/TVFmgW', 'profe', 0, NULL, 26),
('Isabel Jiménez', 'isabel.jimenez@testficticio.org', '$2y$10$QxlpIq5v9JxjSXhspjKjpuIqz/V/VixNYVdpiDzT8NiuEFGsRAKZm', 'estu', 1, '2do ARTE', 27),
('Hugo Soto', 'hugo.soto@examplefake.com', '$2y$10$KuRFd6DAxcOnv/n/gAoBOOEzUn.mXIUZC66lQqg.1M4QcQXogwGaC', 'estu', 1, '3ro ECO', 28),
('Adriana Romero', 'adriana.romero@faketesting.net', '$2y$10$.H7HP23uZj0Ek1tTZOmdYuzDJwURH9rdSFZT7HK22pXgrBNPb6H2m', 'profe', 0, NULL, 29),
('Sergio Mendoza', 'sergio.mendoza@pruebafake.com', '$2y$10$LNUmKPdrlZXHOOja2HUimOHBgUG8peAp9jUn6SFTtQlgY7.TcXlm6', 'estu', 1, '4to SOC', 30),
('Patricia Guzmán', 'patricia.guzman@demoficticio.org', '$2y$10$ravfpsReqk6bxAl0AVMtEuY2p9lNEtXacPfvoFzebbTMFMkheqHYK', 'estu', 1, '5to ELEC', 31),
('Raúl Delgado', 'raul.delgado@testexample.co', '$2y$10$GzWQJ9YNFbbl/cE3Qbvoueocf6AgOu2eFWiN1KEThF5Azbjdyv0f.', 'profe', 1, NULL, 32),
('Natalia Ríos', 'natalia.rios@fakedemo.net', '$2y$10$YByDMKKIMTrcwHP7Rpkw4O9kZYI0ZC.PIezSC2ILc5.jEf.QzGZFO', 'estu', 1, '6to INFO', 33),
('Javier Campos', 'javier.campos@mockprueba.com', '$2y$10$o168MCIU9Jz3apDOKPLaheUYIzY9B4950ITVF1V2KxjsughGVemou', 'estu', 1, '7mo ARTE', 34),
('Mónica Vega', 'monica.vega@exampleficticio.org', '$2y$10$MaY0rYNUqt43yCiOl8tMM.xxMrw1sf61QSSdtbfMV9awBvPhwforW', 'profe', 1, NULL, 35);

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
  ADD PRIMARY KEY (`id_com`),
  ADD KEY `fk_comentario_armarios` (`id_tabla`),
  ADD KEY `fk_comentario_login` (`id`);

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
  MODIFY `id_tabla` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `comentario`
--
ALTER TABLE `comentario`
  MODIFY `id_com` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD CONSTRAINT `fk_comentario_armarios` FOREIGN KEY (`id_tabla`) REFERENCES `armarios` (`id_tabla`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comentario_login` FOREIGN KEY (`id`) REFERENCES `login` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
