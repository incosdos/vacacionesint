-- =====================================================================
--  Actualización 01 · Módulo de vacaciones
--  Agrega la fecha de ingreso del empleado (para calcular días por antigüedad).
--
--  Úsela SOLO si ya importó vacacionesint.sql antes de esta versión.
--  En phpMyAdmin: seleccione la base "vacacionesint" -> Importar -> este archivo.
--  (Equivale a ejecutar: php spark migrate)
-- =====================================================================

USE `vacacionesint`;

ALTER TABLE `usuarios`
  ADD COLUMN `fecha_ingreso` DATE NULL DEFAULT NULL COMMENT 'Fecha de ingreso a la empresa' AFTER `celular`;

INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
VALUES ('2026-10-27-120000', 'App\\Database\\Migrations\\AgregarFechaIngreso', 'default', 'App', UNIX_TIMESTAMP(), 2);
