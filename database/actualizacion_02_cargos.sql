-- =====================================================================
--  Actualización 02 · Cargos del personal
--  Crea la tabla `cargos` con los cargos de la empresa, agrega el campo
--  `idcargo` a `usuarios` (relacionado con `cargos`) y el permiso
--  `cargos.gestionar` para el rol Administrador.
--
--  Requisito: haber aplicado antes la actualización 01 (fecha de ingreso).
--  En phpMyAdmin: seleccione la base "vacacionesint" -> Importar -> este archivo.
--  (Equivale a ejecutar: php spark migrate)
-- =====================================================================

USE `vacacionesint`;

CREATE TABLE `cargos` (
  `idcargo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = activo, 0 = inactivo',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idcargo`),
  UNIQUE KEY `uq_cargos_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Cargos o puestos de trabajo';

INSERT INTO `cargos` (`nombre`, `descripcion`) VALUES
  ('Administrador', 'Administración general de la oficina.'),
  ('Contador', 'Contabilidad, impuestos y estados financieros.'),
  ('Responsable', 'Responsable de un área o proceso.'),
  ('Supervisor', 'Supervisión del trabajo y la calidad de la producción.'),
  ('Diseñador Gráfico', 'Diseño de piezas gráficas, anuncios y material publicitario.'),
  ('Desarrollador Jr', 'Desarrollo de software, nivel junior.'),
  ('Desarrollador Semi Senior', 'Desarrollo de software, nivel semi senior.'),
  ('Desarrollador Senior', 'Desarrollo de software, nivel senior.'),
  ('Mensajero', 'Entrega de documentos y trámites externos.'),
  ('Asistente de Gerencia', 'Apoyo administrativo a la gerencia.'),
  ('Gerencia', 'Dirección general de la empresa.');

ALTER TABLE `usuarios`
  ADD COLUMN `idcargo` int(10) unsigned DEFAULT NULL COMMENT 'Cargo del usuario' AFTER `idrol`,
  ADD KEY `fk_usuarios_cargo` (`idcargo`),
  ADD CONSTRAINT `fk_usuarios_cargo` FOREIGN KEY (`idcargo`) REFERENCES `cargos` (`idcargo`) ON UPDATE CASCADE;

INSERT INTO `permisos` (`clave`, `descripcion`) VALUES ('cargos.gestionar', 'Administrar el catálogo de cargos');
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1, LAST_INSERT_ID());

INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
VALUES ('2026-10-29-090000', 'App\\Database\\Migrations\\CrearCargos', 'default', 'App', UNIX_TIMESTAMP(), 3);
