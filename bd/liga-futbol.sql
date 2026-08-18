-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 16-08-2026 a las 00:38:09
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
  `año` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `estadio` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `genero` varchar(1) NOT NULL,
  `idCategoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `horaPartido` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sancion`
--

CREATE TABLE `sancion` (
  `idSancion` int(11) NOT NULL,
  `tipo` varchar(40) NOT NULL,
  `cedulaJugador` varchar(10) NOT NULL,
  `motivo` varchar(150) NOT NULL,
  `fechaSuspencion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD KEY `idClubVisitante` (`idClubVisitante`);

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
  MODIFY `idAdmin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `boletin`
--
ALTER TABLE `boletin`
  MODIFY `idBoletin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carnet`
--
ALTER TABLE `carnet`
  MODIFY `idCarnet` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `club`
--
ALTER TABLE `club`
  MODIFY `idClub` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `formulario`
--
ALTER TABLE `formulario`
  MODIFY `idFormulario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `partido`
--
ALTER TABLE `partido`
  MODIFY `idPartido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sancion`
--
ALTER TABLE `sancion`
  MODIFY `idSancion` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `partido_ibfk_2` FOREIGN KEY (`idClubVisitante`) REFERENCES `club` (`idClub`);

--
-- Filtros para la tabla `sancion`
--
ALTER TABLE `sancion`
  ADD CONSTRAINT `sancion_ibfk_1` FOREIGN KEY (`cedulaJugador`) REFERENCES `jugador` (`cedula`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
