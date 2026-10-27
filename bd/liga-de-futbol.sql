-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-10-2026 a las 11:39:14
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
-- Base de datos: `liga-de-futbol`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `idAdmin` int(11) NOT NULL,
  `nombreCompleto` varchar(100) NOT NULL,
  `nombreUsuario` varchar(20) NOT NULL,
  `contraseña` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`idAdmin`, `nombreCompleto`, `nombreUsuario`, `contraseña`) VALUES
(1, 'Italo Ruis', 'italo', '123456'),
(2, 'Pepe Sanchez', 'Pepe', '123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boletin`
--

CREATE TABLE `boletin` (
  `idBoletin` int(11) NOT NULL,
  `idAdmin` int(11) NOT NULL,
  `fechaSubida` date NOT NULL,
  `titulo` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `boletin`
--

INSERT INTO `boletin` (`idBoletin`, `idAdmin`, `fechaSubida`, `titulo`) VALUES
(1, 1, '2026-09-26', 'Se aplazo la fecha de inicio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carnet`
--

CREATE TABLE `carnet` (
  `idCarnet` int(11) NOT NULL,
  `fechaSubida` date NOT NULL,
  `fechaVencimiento` date NOT NULL,
  `cedulaJugador` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `idCategoria` int(11) NOT NULL,
  `año` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`idCategoria`, `año`) VALUES
(1, '2013'),
(2, '2014'),
(3, '2015'),
(4, '2016'),
(5, '2017'),
(6, '2018'),
(7, '2019'),
(8, '2020'),
(9, 'Femenino');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `club`
--

CREATE TABLE `club` (
  `idClub` int(11) NOT NULL,
  `nombreClub` varchar(40) NOT NULL,
  `contraseñaClub` varchar(20) NOT NULL,
  `nombrePresidente` varchar(40) NOT NULL,
  `añoCreacion` int(4) NOT NULL,
  `estadio` varchar(50) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `logoClub` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `club`
--

INSERT INTO `club` (`idClub`, `nombreClub`, `contraseñaClub`, `nombrePresidente`, `añoCreacion`, `estadio`, `activo`, `logoClub`) VALUES
(1, 'Liverpool', 'hola123', 'Pepe Gervasio', 1920, 'Santiago Bernabeu', 1, 'admin/club/logos/e0ab5ffabf4d7667.png'),
(2, 'Racing', '123456', 'Pepe Gervasio Segundo', 1921, 'Santiago Bernabeu ll', 1, NULL),
(7, 'Nacional', '1234', 'Bairo', 1899, 'Gran Parque Central', 1, 'admin/club/logos/27f99962f63706c8.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `club_categoria`
--

CREATE TABLE `club_categoria` (
  `idClub` int(11) NOT NULL,
  `idCategoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `club_categoria`
--

INSERT INTO `club_categoria` (`idClub`, `idCategoria`) VALUES
(1, 1),
(1, 2),
(1, 5),
(2, 1),
(7, 1),
(7, 6),
(7, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `club_competicion`
--

CREATE TABLE `club_competicion` (
  `idClub` int(11) NOT NULL,
  `idCompeticion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `club_competicion`
--

INSERT INTO `club_competicion` (`idClub`, `idCompeticion`) VALUES
(1, 1),
(2, 1),
(7, 1),
(7, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `competicion`
--

CREATE TABLE `competicion` (
  `idCompeticion` int(11) NOT NULL,
  `nombreCompeticion` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `competicion`
--

INSERT INTO `competicion` (`idCompeticion`, `nombreCompeticion`) VALUES
(1, 'Campeonato'),
(2, 'Copa de oro'),
(3, 'Copa de plata');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formulario`
--

CREATE TABLE `formulario` (
  `idFormulario` int(11) NOT NULL,
  `clubSubida` int(11) NOT NULL,
  `fechaSubida` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugador`
--

CREATE TABLE `jugador` (
  `nombre` varchar(40) NOT NULL,
  `apellido` varchar(40) NOT NULL,
  `cedula` varchar(10) NOT NULL,
  `idClub` int(11) NOT NULL,
  `fechaNacimiento` date NOT NULL,
  `genero` varchar(15) NOT NULL,
  `idCategoria` int(11) NOT NULL,
  `masa` decimal(6,2) NOT NULL,
  `altura` decimal(4,2) NOT NULL,
  `velocidad` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugador`
--

INSERT INTO `jugador` (`nombre`, `apellido`, `cedula`, `idClub`, `fechaNacimiento`, `genero`, `idCategoria`, `masa`, `altura`, `velocidad`) VALUES
('Pepe', 'Hernandez', '23848043', 7, '2016-12-12', 'M', 4, 80.00, 1.70, 7.00),
('italo', 'ruis', '57705017', 1, '2014-12-08', 'M', 1, 70.00, 1.70, 6.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partido`
--

CREATE TABLE `partido` (
  `idPartido` int(11) NOT NULL,
  `idClubLocal` int(11) NOT NULL,
  `idClubVisitante` int(11) NOT NULL,
  `golesLocal` int(11) NOT NULL,
  `golesVisitante` int(11) NOT NULL,
  `duracionPartido` int(11) NOT NULL,
  `arbitro` varchar(40) NOT NULL,
  `estadio` varchar(40) NOT NULL,
  `fechaPartido` date NOT NULL,
  `horaPartido` int(11) NOT NULL,
  `jornada` int(11) NOT NULL DEFAULT 1,
  `idCompeticion` int(11) NOT NULL,
  `jugado` tinyint(1) NOT NULL,
  `idCategoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partido`
--

INSERT INTO `partido` (`idPartido`, `idClubLocal`, `idClubVisitante`, `golesLocal`, `golesVisitante`, `duracionPartido`, `arbitro`, `estadio`, `fechaPartido`, `horaPartido`, `jornada`, `idCompeticion`, `jugado`, `idCategoria`) VALUES
(7, 1, 2, 2, 0, 0, 'Gustavo Herrera', 'Santiago Bernabeu', '2026-12-02', 1230, 1, 1, 1, 1),
(9, 1, 2, 0, 0, 0, 'Gustavo Herrera', 'Arena do Gremio', '2026-12-12', 1230, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sancion`
--

CREATE TABLE `sancion` (
  `idSancion` int(11) NOT NULL,
  `tipo` varchar(40) NOT NULL,
  `cedulaJugador` varchar(10) NOT NULL,
  `motivo` varchar(150) NOT NULL,
  `fechaSuspencion` int(11) NOT NULL,
  `clubSancion` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sancion`
--

INSERT INTO `sancion` (`idSancion`, `tipo`, `cedulaJugador`, `motivo`, `fechaSuspencion`, `clubSancion`) VALUES
(6, 'Amarilla', '57705017', 'Golpeó a un rival.', 4, '1'),
(8, 'Amarilla', '57705017', 'Golpeó a un rival.', 0, '1'),
(9, 'Roja', '23848043', 'Golpeó a un rival.', 2, '7');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`idAdmin`);

--
-- Indices de la tabla `boletin`
--
ALTER TABLE `boletin`
  ADD PRIMARY KEY (`idBoletin`),
  ADD KEY `idAdmin` (`idAdmin`);

--
-- Indices de la tabla `carnet`
--
ALTER TABLE `carnet`
  ADD PRIMARY KEY (`idCarnet`),
  ADD KEY `cedulaJugador` (`cedulaJugador`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`idCategoria`);

--
-- Indices de la tabla `club`
--
ALTER TABLE `club`
  ADD PRIMARY KEY (`idClub`);

--
-- Indices de la tabla `club_categoria`
--
ALTER TABLE `club_categoria`
  ADD PRIMARY KEY (`idClub`,`idCategoria`),
  ADD KEY `idCategoria` (`idCategoria`);

--
-- Indices de la tabla `club_competicion`
--
ALTER TABLE `club_competicion`
  ADD PRIMARY KEY (`idClub`,`idCompeticion`),
  ADD KEY `idCompeticion` (`idCompeticion`);

--
-- Indices de la tabla `competicion`
--
ALTER TABLE `competicion`
  ADD PRIMARY KEY (`idCompeticion`);

--
-- Indices de la tabla `formulario`
--
ALTER TABLE `formulario`
  ADD PRIMARY KEY (`idFormulario`);

--
-- Indices de la tabla `jugador`
--
ALTER TABLE `jugador`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `jugador_ibfk_1` (`idCategoria`),
  ADD KEY `idClub` (`idClub`);

--
-- Indices de la tabla `partido`
--
ALTER TABLE `partido`
  ADD PRIMARY KEY (`idPartido`),
  ADD KEY `idClubLocal` (`idClubLocal`),
  ADD KEY `idClubVisitante` (`idClubVisitante`),
  ADD KEY `idCompeticion` (`idCompeticion`),
  ADD KEY `idCategoria` (`idCategoria`);

--
-- Indices de la tabla `sancion`
--
ALTER TABLE `sancion`
  ADD PRIMARY KEY (`idSancion`),
  ADD KEY `cedulaJugador` (`cedulaJugador`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `idAdmin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `boletin`
--
ALTER TABLE `boletin`
  MODIFY `idBoletin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `carnet`
--
ALTER TABLE `carnet`
  MODIFY `idCarnet` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `club`
--
ALTER TABLE `club`
  MODIFY `idClub` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `competicion`
--
ALTER TABLE `competicion`
  MODIFY `idCompeticion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `formulario`
--
ALTER TABLE `formulario`
  MODIFY `idFormulario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `partido`
--
ALTER TABLE `partido`
  MODIFY `idPartido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `sancion`
--
ALTER TABLE `sancion`
  MODIFY `idSancion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `boletin`
--
ALTER TABLE `boletin`
  ADD CONSTRAINT `boletin_ibfk_1` FOREIGN KEY (`idAdmin`) REFERENCES `administrador` (`idAdmin`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `carnet`
--
ALTER TABLE `carnet`
  ADD CONSTRAINT `carnet_ibfk_1` FOREIGN KEY (`cedulaJugador`) REFERENCES `jugador` (`cedula`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `club_categoria`
--
ALTER TABLE `club_categoria`
  ADD CONSTRAINT `club_categoria_ibfk_1` FOREIGN KEY (`idClub`) REFERENCES `club` (`idClub`) ON DELETE CASCADE,
  ADD CONSTRAINT `club_categoria_ibfk_2` FOREIGN KEY (`idCategoria`) REFERENCES `categoria` (`idCategoria`) ON DELETE CASCADE;

--
-- Filtros para la tabla `club_competicion`
--
ALTER TABLE `club_competicion`
  ADD CONSTRAINT `club_competicion_ibfk_1` FOREIGN KEY (`idClub`) REFERENCES `club` (`idClub`) ON DELETE CASCADE,
  ADD CONSTRAINT `club_competicion_ibfk_2` FOREIGN KEY (`idCompeticion`) REFERENCES `competicion` (`idCompeticion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `jugador`
--
ALTER TABLE `jugador`
  ADD CONSTRAINT `jugador_ibfk_1` FOREIGN KEY (`idCategoria`) REFERENCES `categoria` (`idCategoria`) ON UPDATE CASCADE,
  ADD CONSTRAINT `jugador_ibfk_2` FOREIGN KEY (`idClub`) REFERENCES `club` (`idClub`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `partido`
--
ALTER TABLE `partido`
  ADD CONSTRAINT `partido_ibfk_1` FOREIGN KEY (`idClubLocal`) REFERENCES `club` (`idClub`) ON UPDATE CASCADE,
  ADD CONSTRAINT `partido_ibfk_2` FOREIGN KEY (`idClubVisitante`) REFERENCES `club` (`idClub`) ON UPDATE CASCADE,
  ADD CONSTRAINT `partido_ibfk_3` FOREIGN KEY (`idCompeticion`) REFERENCES `competicion` (`idCompeticion`) ON UPDATE CASCADE,
  ADD CONSTRAINT `partido_ibfk_4` FOREIGN KEY (`idCategoria`) REFERENCES `categoria` (`idCategoria`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `sancion`
--
ALTER TABLE `sancion`
  ADD CONSTRAINT `sancion_ibfk_1` FOREIGN KEY (`cedulaJugador`) REFERENCES `jugador` (`cedula`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
