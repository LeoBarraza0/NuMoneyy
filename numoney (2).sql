-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-11-2024 a las 05:15:58
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
-- Base de datos: `numoney`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `IdCliente` varchar(255) NOT NULL,
  `NombreCompleto` varchar(255) NOT NULL,
  `Tipo_Documento` varchar(255) NOT NULL,
  `Correo` varchar(255) NOT NULL,
  `Direccion` varchar(255) NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  `Estado` enum('Activo','Inactivo','','') NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `Telefono` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`IdCliente`, `NombreCompleto`, `Tipo_Documento`, `Correo`, `Direccion`, `Fecha_Registro`, `Estado`, `contrasena`, `Telefono`) VALUES
('1067', 'anasofia', 'CC', 'ana@gmail.com', '123', '2024-11-26 03:27:40', 'Activo', '$2y$10$PcKU842jJX1ihV7HtiDO7elxmJmmj7r3igs5Kn87fPHuHHN5TBjAC', '123'),
('2022', 'yo', 'CC', 'yo@gmail.com', '123', '2024-11-26 03:40:08', 'Activo', '$2y$10$4X7XZLrM/.9FAbaou7WlSOB8hHypoatAne2Az0pZe/639DM1fvH6m', '333'),
('2071', 'Leonardo Barraza', 'CC', 'leonardo@gmail.com', 'cra 11', '2024-11-26 02:39:50', 'Activo', '$2y$10$4quun3zRqThm/qvttwsdXuq1OR2gJB835OyyMYnGD/h7LmCZQXAUq', '3012804688'),
('2462', 'Emanuel barranco', 'CC', 'emanuel@barranco', '123', '2024-11-26 04:04:57', 'Activo', '$2y$10$/IggpSqe5KZXQUhAnmOVOumZ1G0dKJOYE6VvGKhYU.IssqyQSJPde', '1234'),
('7492', 'yyy', 'CC', 'aaa@gmail.com', '123', '2024-11-26 04:10:05', 'Activo', '$2y$10$wrOQsP9Lj.GgmlO6mFnBYuGVyduLJFyCPNqceC/pR5C.QIGhttjd2', '10912'),
('7945', 'nuevo', 'CC', 'nuevo@gmail.com', '123', '2024-11-26 04:35:55', 'Activo', '$2y$10$py5X64jn5DnaNaGjZX5i5.cpTA6iLgDeLfXOB2cN.bCC6AMoVl77a', '1111');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta`
--

CREATE TABLE `cuenta` (
  `NumeroCuenta` varchar(255) NOT NULL,
  `TipoCuenta` varchar(255) NOT NULL,
  `SaldoActual` double NOT NULL,
  `Estado` enum('Activa','Inactiva') NOT NULL,
  `FechaApertura` datetime NOT NULL,
  `IdCliente_fk` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuenta`
--

INSERT INTO `cuenta` (`NumeroCuenta`, `TipoCuenta`, `SaldoActual`, `Estado`, `FechaApertura`, `IdCliente_fk`) VALUES
('', 'Cuenta ahorros', 0, 'Activa', '0000-00-00 00:00:00', '2071'),
('AH-1067', 'Cuenta ahorros', 0, 'Activa', '0000-00-00 00:00:00', '1067'),
('AH-2022', 'Cuenta ahorros', 0, 'Activa', '0000-00-00 00:00:00', '2022'),
('AH-7492', 'Cuenta ahorros', 0, 'Activa', '0000-00-00 00:00:00', '7492'),
('AH-7945', 'Cuenta ahorros', 0, 'Activa', '0000-00-00 00:00:00', '7945'),
('CO-1067', 'Cuenta corriente', 0, 'Activa', '0000-00-00 00:00:00', '1067'),
('CO-2022', 'Cuenta corriente', 0, 'Activa', '0000-00-00 00:00:00', '2022'),
('CO-7492', 'Cuenta corriente', 0, 'Activa', '0000-00-00 00:00:00', '7492'),
('CO-7945', 'Cuenta corriente', 0, 'Activa', '0000-00-00 00:00:00', '7945'),
('IN-1067', 'Cuenta inversión', 0, 'Activa', '0000-00-00 00:00:00', '1067'),
('IN-2022', 'Cuenta inversión', 0, 'Activa', '0000-00-00 00:00:00', '2022'),
('IN-7492', 'Cuenta inversión', 0, 'Activa', '0000-00-00 00:00:00', '7492'),
('IN-7945', 'Cuenta inversión', 0, 'Activa', '0000-00-00 00:00:00', '7945');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientocuenta`
--

CREATE TABLE `movimientocuenta` (
  `IdMovimiento` varchar(255) NOT NULL,
  `IdCuenta_fk` varchar(255) NOT NULL,
  `IdTransaccion_fk` varchar(255) DEFAULT NULL,
  `FechaHora` datetime NOT NULL,
  `TipoMovimiento` enum('Deposito','Retiro','Transferencia','Pago','Recarga','Compra') NOT NULL,
  `SaldoPrevio` double NOT NULL,
  `SaldoPosterior` double NOT NULL,
  `EstadoMovimiento` enum('Pendiente','Completado','Fallido') NOT NULL,
  `Descripcion` varchar(255) DEFAULT NULL,
  `ReferenciaExterna` varchar(255) DEFAULT NULL,
  `IdServicioFK` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `IdServicio` varchar(255) NOT NULL,
  `NombreServicio` varchar(255) NOT NULL,
  `Monto` double NOT NULL,
  `FechaHora` datetime NOT NULL,
  `IdTipoS_fk` varchar(255) NOT NULL,
  `IdProveedor_fk` varchar(255) NOT NULL,
  `IdCliente_fk` varchar(255) NOT NULL,
  `EstadoServicio` enum('Activo','Inactivo','Pendiente') NOT NULL,
  `Descripcion` varchar(255) DEFAULT NULL,
  `Comision` double DEFAULT NULL,
  `ReferenciaExterna` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicioproveedor`
--

CREATE TABLE `servicioproveedor` (
  `IdProveedor` varchar(255) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Direccion` varchar(255) NOT NULL,
  `Descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicioproveedor`
--

INSERT INTO `servicioproveedor` (`IdProveedor`, `Nombre`, `Direccion`, `Descripcion`) VALUES
('001', 'Triple A', 'Cra 10', 'Empresa prestadora de servicio de agua'),
('002', 'Aire', 'Cr 2 # 3', 'Empresa prestadora de servicio electrico');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonos`
--

CREATE TABLE `telefonos` (
  `IdRepTel` varchar(255) NOT NULL,
  `Telefono` varchar(255) NOT NULL,
  `IdClienteFk` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `telefonos`
--

INSERT INTO `telefonos` (`IdRepTel`, `Telefono`, `IdClienteFk`) VALUES
('001', '3012804666', '001'),
('002', '3102743598', '001'),
('003', '3871231212', '002');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposervicio`
--

CREATE TABLE `tiposervicio` (
  `IdTipoS` varchar(255) NOT NULL,
  `NombreTipoServicio` varchar(255) NOT NULL,
  `Descripcion` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposervicio`
--

INSERT INTO `tiposervicio` (`IdTipoS`, `NombreTipoServicio`, `Descripcion`) VALUES
('001', 'Telefonia', 'Servicios telefonicos'),
('002', 'Agua', 'Servicio de acueducto'),
('003', 'Luz', 'Servicio de electricidad');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaccion`
--

CREATE TABLE `transaccion` (
  `IdTransaccion` varchar(255) NOT NULL,
  `IdCuentaOrigen_fk` varchar(255) NOT NULL,
  `IdCuentaDestino_fk` varchar(255) NOT NULL,
  `TipoTransaccion` enum('Deposito','Retiro','Transferencia','','','') NOT NULL,
  `MontoAplica` double NOT NULL,
  `FechaHora` datetime NOT NULL,
  `Metodo` enum('Tarjeta credito','Tarjeta debito','Transferencia bancaria','NFC','QR','Criptomoneda') NOT NULL,
  `Descripcion` varchar(255) NOT NULL,
  `EstadoTransaccion` enum('Pendiente','Completada','Fallida','') NOT NULL,
  `Comision` double NOT NULL,
  `ReferenciaExterna` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`IdCliente`);

--
-- Indices de la tabla `cuenta`
--
ALTER TABLE `cuenta`
  ADD PRIMARY KEY (`NumeroCuenta`),
  ADD KEY `IdCliente_fk` (`IdCliente_fk`);

--
-- Indices de la tabla `movimientocuenta`
--
ALTER TABLE `movimientocuenta`
  ADD PRIMARY KEY (`IdMovimiento`),
  ADD KEY `IdTransaccion_fk` (`IdTransaccion_fk`),
  ADD KEY `IdCuenta_fk` (`IdCuenta_fk`),
  ADD KEY `IdServicio` (`IdServicioFK`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`IdServicio`),
  ADD KEY `IdCliente_fk` (`IdCliente_fk`),
  ADD KEY `IdTipoS_fk` (`IdTipoS_fk`),
  ADD KEY `IdProveedor_fk` (`IdProveedor_fk`);

--
-- Indices de la tabla `servicioproveedor`
--
ALTER TABLE `servicioproveedor`
  ADD PRIMARY KEY (`IdProveedor`);

--
-- Indices de la tabla `telefonos`
--
ALTER TABLE `telefonos`
  ADD PRIMARY KEY (`IdRepTel`),
  ADD KEY `idclientefk` (`IdClienteFk`),
  ADD KEY `IdClienteFk_2` (`IdClienteFk`);

--
-- Indices de la tabla `tiposervicio`
--
ALTER TABLE `tiposervicio`
  ADD PRIMARY KEY (`IdTipoS`);

--
-- Indices de la tabla `transaccion`
--
ALTER TABLE `transaccion`
  ADD PRIMARY KEY (`IdTransaccion`),
  ADD KEY `Cuentas` (`IdCuentaOrigen_fk`,`IdCuentaDestino_fk`),
  ADD KEY `IdCuentaDestino_fk` (`IdCuentaDestino_fk`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cuenta`
--
ALTER TABLE `cuenta`
  ADD CONSTRAINT `cuenta_ibfk_1` FOREIGN KEY (`IdCliente_fk`) REFERENCES `cliente` (`IdCliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientocuenta`
--
ALTER TABLE `movimientocuenta`
  ADD CONSTRAINT `movimientocuenta_ibfk_1` FOREIGN KEY (`IdTransaccion_fk`) REFERENCES `transaccion` (`IdTransaccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movimientocuenta_ibfk_2` FOREIGN KEY (`IdCuenta_fk`) REFERENCES `cuenta` (`NumeroCuenta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movimientocuenta_ibfk_3` FOREIGN KEY (`IdServicioFK`) REFERENCES `servicio` (`IdServicio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD CONSTRAINT `servicio_ibfk_1` FOREIGN KEY (`IdCliente_fk`) REFERENCES `cliente` (`IdCliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `servicio_ibfk_2` FOREIGN KEY (`IdTipoS_fk`) REFERENCES `tiposervicio` (`IdTipoS`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `servicio_ibfk_3` FOREIGN KEY (`IdProveedor_fk`) REFERENCES `servicioproveedor` (`IdProveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `telefonos`
--
ALTER TABLE `telefonos`
  ADD CONSTRAINT `telefonos_ibfk_1` FOREIGN KEY (`IdClienteFk`) REFERENCES `servicioproveedor` (`IdProveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `transaccion`
--
ALTER TABLE `transaccion`
  ADD CONSTRAINT `transaccion_ibfk_1` FOREIGN KEY (`IdCuentaOrigen_fk`) REFERENCES `cuenta` (`NumeroCuenta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaccion_ibfk_2` FOREIGN KEY (`IdCuentaDestino_fk`) REFERENCES `cuenta` (`NumeroCuenta`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
