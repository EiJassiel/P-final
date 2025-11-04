-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-08-2025 a las 20:19:38
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
-- Base de datos: `mi_base`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_tmdb`
--

CREATE TABLE `cache_tmdb` (
  `id_tmdb` int(10) UNSIGNED NOT NULL,
  `tipo` enum('pelicula','serie') NOT NULL,
  `json_data` longtext NOT NULL,
  `fecha_cache` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(10) DEFAULT 'activo',
  `override_titulo` varchar(255) DEFAULT NULL,
  `override_sinopsis` text DEFAULT NULL,
  `override_imagen` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cache_tmdb`
--

INSERT INTO `cache_tmdb` (`id_tmdb`, `tipo`, `json_data`, `fecha_cache`, `estado`, `override_titulo`, `override_sinopsis`, `override_imagen`) VALUES
(552524, 'pelicula', '{\"titulo\":\"Lilo y Stitch Gentrificada\",\"sinopsis\":\"La conmovedora y divertid\\u00edsima historia de una solitaria ni\\u00f1a hawaiana y el extraterrestre fugitivo que la ayuda a reparar su desestructurada familia.\",\"imagen\":\"https:\\/\\/image.tmdb.org\\/t\\/p\\/w500\\/4oLLOAT55JhAoe73VliaSKFvEEr.jpg\"}', '2025-08-01 12:40:57', 'activo', NULL, NULL, NULL),
(755898, 'pelicula', '{\"titulo\":\"50 CENT EN...\",\"sinopsis\":\"Will Radford, un destacado analista de ciberseguridad, pasa sus d\\u00edas rastreando posibles amenazas a la seguridad nacional a trav\\u00e9s de un programa de vigilancia masiva. Un ataque de una entidad desconocida le lleva a cuestionarse si el gobierno le est\\u00e1 ocultando algo a \\u00e9l... y al resto del mundo.\",\"imagen\":\"https:\\/\\/image.tmdb.org\\/t\\/p\\/w500\\/yvirUYrva23IudARHn3mMGVxWqM.jpg\"}', '2025-08-01 13:02:50', 'activo', NULL, NULL, NULL),
(1061474, 'pelicula', '{\"titulo\":null,\"sinopsis\":null,\"imagen\":null}', '2025-07-28 11:34:45', 'activo', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id_calif` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_tmdb` int(10) UNSIGNED NOT NULL,
  `tipo` enum('pelicula','serie') NOT NULL,
  `puntuacion` tinyint(3) UNSIGNED NOT NULL CHECK (`puntuacion` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id_calif`, `id_usuario`, `id_tmdb`, `tipo`, `puntuacion`, `comentario`, `fecha`) VALUES
(1, 11, 9738, 'pelicula', 5, 'está cool', '2025-07-31 15:48:53'),
(2, 11, 803796, 'pelicula', 5, '10/10 es cine', '2025-07-31 15:51:19'),
(3, 11, 1087192, 'pelicula', 5, 'hermosa', '2025-07-31 15:55:32'),
(4, 11, 1124619, 'pelicula', 3, 'was', '2025-07-31 15:57:23'),
(6, 11, 1391047, 'pelicula', 2, 'no se no la vi', '2025-07-31 19:28:47'),
(7, 10, 1119878, 'pelicula', 3, 'de waos', '2025-07-31 19:29:20'),
(10, 11, 950387, 'pelicula', 5, 'god', '2025-07-31 19:59:23'),
(11, 11, 1311031, 'pelicula', 5, 'wazaaaaaaa', '2025-07-31 20:15:33'),
(12, 11, 1263256, 'pelicula', 3, 'no la vi', '2025-07-31 20:21:34'),
(13, 11, 1119878, 'pelicula', 5, 'ta pro', '2025-07-31 21:02:04'),
(14, 10, 755898, 'pelicula', 2, 'aaa', '2025-07-31 21:50:31'),
(16, 10, 1124619, 'pelicula', 5, '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, ..., 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, ', '2025-08-01 11:17:08'),
(17, 10, 617126, 'pelicula', 5, 'ñañañaña', '2025-08-01 11:20:13'),
(18, 10, 1100988, 'pelicula', 5, 'god', '2025-08-01 11:21:41'),
(19, 10, 1087192, 'pelicula', 5, 'qqwewqe', '2025-08-01 11:30:51'),
(28, 10, 1311031, 'pelicula', 5, 'ta prity', '2025-08-01 12:55:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_local`
--

CREATE TABLE `contenido_local` (
  `id_tmdb` int(10) UNSIGNED NOT NULL,
  `tipo` enum('pelicula','serie') NOT NULL,
  `titulo_alt` varchar(255) DEFAULT NULL,
  `sinopsis_alt` text DEFAULT NULL,
  `imagen_local` varchar(255) DEFAULT NULL,
  `destacado` tinyint(1) DEFAULT 0,
  `fecha_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos`
--

CREATE TABLE `favoritos` (
  `id_favorito` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_tmdb` int(10) UNSIGNED NOT NULL,
  `tipo` enum('pelicula','serie') NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preferencias_usuario`
--

CREATE TABLE `preferencias_usuario` (
  `usuario_id` int(11) NOT NULL,
  `generos` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `preferencias_usuario`
--

INSERT INTO `preferencias_usuario` (`usuario_id`, `generos`) VALUES
(0, '[35,18,27]'),
(1, '[35]'),
(7, '[28,35,18]'),
(10, '[35]'),
(11, '[35]'),
(12, '[35]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena_hash` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') NOT NULL DEFAULT 'usuario',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `contrasena_hash`, `rol`, `fecha_registro`) VALUES
(1, 'Admin', 'admin@watchito.local', '$2y$12$8adVVh0ByrGGhjdhppXFIuHOMgiwqzPKAT6cKEDxkR8E1oqLTS7ee', 'admin', '2025-07-25 06:28:29'),
(2, 'marsanta', 'einer@gmail.com', '$2y$10$6FM18pG5KA3lwjqoVZjU2uKjvoOJc0hGAv41Afm9is6RRnjBDc39S', 'usuario', '2025-07-25 10:09:40'),
(3, 'einer12', 'einer12@gmail.com', '$2y$10$WsoEM418NZYOx/DTybk7QuOjU6kcLEnBuWh07eOa8sK9Zqgbam3S2', 'usuario', '2025-07-25 10:11:25'),
(4, 'einer1212', 'einer1212@gmail.com', '$2y$10$eZm.1J3.dX4ur6n5lgWqO.kKOlIulIYki5FkD5gCAHU/nUFkUjFHO', 'usuario', '2025-07-25 10:13:10'),
(5, 'einer121212', 'einer121212@gmail.com', '$2y$10$9L8LhViNdgHfHfaTwO/06uWqOgCzX9y0zUIoG4tzMPqfNlqDLTMjO', 'usuario', '2025-07-25 10:14:47'),
(6, 'einerr', 'einerr@gmail.com', '$2y$10$pJfOpvPjr.TI2XXqdINHROVsabIowlcSzX.DEcuXc7V6yl48Ci7ae', 'usuario', '2025-07-25 10:15:51'),
(7, 'Einermosquera', 'eineree@gmail.com', '$2y$10$EKQxk2Ip/Ia5/Z7OkhxU8uQ0UloW70KtZ2cmSeIMzjaqM9GGTatU2', 'usuario', '2025-07-25 12:22:29'),
(8, 'Santamar', 'santamar@gmail.com', '$2y$10$W6V4sxJ7bXEkX6A5AssEFu/bpvoZS7lKvf4.SRwl9UkJvghN1Tth6', 'usuario', '2025-07-27 12:40:35'),
(9, 'einerJassiel', 'jassiel@gmail.com', '$2y$10$5bpWaarflJylOzuyRVJ33uyfIj7NvF1wcVj87bx68K5v1fsPSdGyW', 'usuario', '2025-07-27 13:11:45'),
(10, 'Chris1', 'chris@gmail.com', '$2y$10$dmbn.wABEVuIBzkNB7oIXeaoh8NKjRT8dl4OzUoFyWps.tm0O2II.', 'usuario', '2025-07-30 18:11:53'),
(11, 'julia1', 'julia@gmail.com', '$2y$10$72pThd9gA3m4PPn/tw965ua/XKLDxWA6w.XnfF5/qEkgEGcYtqi0S', 'usuario', '2025-07-31 20:19:49'),
(12, 'pepe1', 'pepe@gmail.com', '$2y$10$CKbd/vQiKdQg2XhkfiSR0.I7UeA/TgrxA5.MHS8ZFmC8UHDr5BcMO', 'usuario', '2025-08-01 15:54:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vistos`
--

CREATE TABLE `vistos` (
  `id_visto` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_tmdb` int(10) UNSIGNED NOT NULL,
  `tipo` enum('pelicula','serie') NOT NULL,
  `fecha_visto` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache_tmdb`
--
ALTER TABLE `cache_tmdb`
  ADD PRIMARY KEY (`id_tmdb`);

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id_calif`),
  ADD UNIQUE KEY `uniq_calif` (`id_usuario`,`id_tmdb`,`tipo`);

--
-- Indices de la tabla `contenido_local`
--
ALTER TABLE `contenido_local`
  ADD PRIMARY KEY (`id_tmdb`);

--
-- Indices de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id_favorito`),
  ADD UNIQUE KEY `uniq_fav` (`id_usuario`,`id_tmdb`,`tipo`);

--
-- Indices de la tabla `preferencias_usuario`
--
ALTER TABLE `preferencias_usuario`
  ADD PRIMARY KEY (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `vistos`
--
ALTER TABLE `vistos`
  ADD PRIMARY KEY (`id_visto`),
  ADD UNIQUE KEY `uniq_visto` (`id_usuario`,`id_tmdb`,`tipo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id_calif` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id_favorito` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `vistos`
--
ALTER TABLE `vistos`
  MODIFY `id_visto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `fk_calificaciones_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `vistos`
--
ALTER TABLE `vistos`
  ADD CONSTRAINT `vistos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
