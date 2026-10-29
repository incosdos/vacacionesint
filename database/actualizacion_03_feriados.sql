-- =====================================================================
--  Actualización 03 · Feriados y días no laborables
--  Crea la tabla `feriados` (administrable por el Administrador desde
--  Administración → Feriados), la carga con los feriados de 2026 (con el
--  DS 5521) y 2027, y agrega el permiso `feriados.gestionar`.
--
--  Requisito: haber aplicado antes las actualizaciones 01 y 02.
--  En phpMyAdmin: seleccione la base "vacacionesint" -> Importar -> este archivo.
--  (Equivale a ejecutar: php spark migrate)
-- =====================================================================

USE `vacacionesint`;

CREATE TABLE `feriados` (
  `idferiado` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `tipo` enum('nacional','departamental','no_laborable') NOT NULL DEFAULT 'nacional',
  `observacion` varchar(255) DEFAULT NULL COMMENT 'Decreto o motivo',
  `registrado_por` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idferiado`),
  UNIQUE KEY `uq_feriados_fecha` (`fecha`),
  KEY `fk_feriados_usuario` (`registrado_por`),
  CONSTRAINT `fk_feriados_usuario` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`idusuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Feriados y días no laborables';

INSERT INTO `feriados` (`fecha`, `nombre`, `tipo`, `observacion`) VALUES
  ('2026-01-01', 'Año Nuevo', 'nacional', NULL),
  ('2026-01-23', 'Día del Estado Plurinacional (trasladado)', 'nacional', 'Decreto Supremo N° 5521'),
  ('2026-02-16', 'Carnaval', 'nacional', NULL),
  ('2026-02-17', 'Carnaval', 'nacional', NULL),
  ('2026-04-03', 'Viernes Santo', 'nacional', NULL),
  ('2026-05-01', 'Día del Trabajo', 'nacional', NULL),
  ('2026-06-04', 'Corpus Christi', 'nacional', NULL),
  ('2026-06-05', 'Corpus Christi (feriado adicional)', 'nacional', 'Decreto Supremo N° 5521'),
  ('2026-06-22', 'Año Nuevo Andino Amazónico (trasladado)', 'nacional', 'Decreto Supremo N° 5521'),
  ('2026-08-06', 'Día de la Independencia', 'nacional', NULL),
  ('2026-08-07', 'Día de la Independencia (feriado adicional)', 'nacional', 'Decreto Supremo N° 5521'),
  ('2026-09-14', 'Aniversario de Cochabamba', 'departamental', NULL),
  ('2026-11-02', 'Día de Todos los Difuntos', 'nacional', NULL),
  ('2026-12-25', 'Navidad', 'nacional', NULL),
  ('2027-01-01', 'Año Nuevo', 'nacional', NULL),
  ('2027-01-22', 'Día del Estado Plurinacional', 'nacional', NULL),
  ('2027-02-08', 'Carnaval', 'nacional', NULL),
  ('2027-02-09', 'Carnaval', 'nacional', NULL),
  ('2027-03-26', 'Viernes Santo', 'nacional', NULL),
  ('2027-05-01', 'Día del Trabajo', 'nacional', NULL),
  ('2027-05-27', 'Corpus Christi', 'nacional', NULL),
  ('2027-06-21', 'Año Nuevo Andino Amazónico', 'nacional', NULL),
  ('2027-08-06', 'Día de la Independencia', 'nacional', NULL),
  ('2027-09-14', 'Aniversario de Cochabamba', 'departamental', NULL),
  ('2027-11-02', 'Día de Todos los Difuntos', 'nacional', NULL),
  ('2027-12-25', 'Navidad', 'nacional', NULL);

INSERT INTO `permisos` (`clave`, `descripcion`) VALUES ('feriados.gestionar', 'Administrar feriados y días no laborables');
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1, LAST_INSERT_ID());

INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
VALUES ('2026-10-29-120000', 'App\\Database\\Migrations\\CrearFeriados', 'default', 'App', UNIX_TIMESTAMP(), 4);
