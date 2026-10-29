-- =====================================================================
--  INTERGRAPHICS - Sistema de Registro de Vacaciones
--  International Graphics Outsourcing S.R.L.
--  Base de datos: vacacionesint  (MySQL / MariaDB de XAMPP)
--
--  Importar en phpMyAdmin: pestaña "Importar" -> elegir este archivo -> Continuar
--  (equivale a: php spark migrate  +  php spark db:seed DatabaseSeeder)
--
--  Usuario administrador inicial:
--     correo:     admin@intergraphics.local
--     contraseña: Admin2026*      <-- cámbiela después del primer ingreso
--
--  Incluye: roles, permisos, usuarios, vacaciones, cargos (act. 02) y feriados 2026-2027 (act. 03).
--  ATENCIÓN: borra y vuelve a crear las tablas. Úselo solo en la instalación inicial.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `vacacionesint`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `vacacionesint`;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `cargos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cargos` (
  `idcargo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = activo, 0 = inactivo',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idcargo`),
  UNIQUE KEY `uq_cargos_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Cargos o puestos de trabajo';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `cargos` WRITE;
/*!40000 ALTER TABLE `cargos` DISABLE KEYS */;
INSERT INTO `cargos` (`idcargo`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES (1,'Administrador','Administración general de la oficina.',1,'2026-10-29 08:45:25',NULL);
INSERT INTO `cargos` (`idcargo`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES (2,'Contador','Contabilidad, impuestos y estados financieros.',1,'2026-10-29 08:45:25',NULL);
INSERT INTO `cargos` (`idcargo`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES (3,'Responsable','Responsable de un área o proceso.',1,'2026-10-29 08:45:25',NULL);
INSERT INTO `cargos` (`idcargo`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES (4,'Supervisor','Supervisión del trabajo y la calidad de la producción.',1,'2026-10-29 08:45:25',NULL);
INSERT INTO `cargos` (`idcargo`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES (5,'Diseñador Gráfico','Diseño de piezas gráficas, anuncios y material publicitario.',1,'2026-10-29 08:45:25',NULL);
INSERT INTO `cargos` (`idcargo`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES (6,'Desarrollador Jr','Desarrollo de software, nivel junior.',1,'2026-10-29 08:45:25',NULL);
INSERT INTO `cargos` (`idcargo`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES (7,'Desarrollador Semi Senior','Desarrollo de software, nivel semi senior.',1,'2026-10-29 08:45:25',NULL);
INSERT INTO `cargos` (`idcargo`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES (8,'Desarrollador Senior','Desarrollo de software, nivel senior.',1,'2026-10-29 08:45:25',NULL);
INSERT INTO `cargos` (`idcargo`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES (9,'Mensajero','Entrega de documentos y trámites externos.',1,'2026-10-29 08:45:25',NULL);
INSERT INTO `cargos` (`idcargo`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES (10,'Asistente de Gerencia','Apoyo administrativo a la gerencia.',1,'2026-10-29 08:45:25',NULL);
INSERT INTO `cargos` (`idcargo`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES (11,'Gerencia','Dirección general de la empresa.',1,'2026-10-29 08:45:25',NULL);
/*!40000 ALTER TABLE `cargos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `feriados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Feriados y días no laborables';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `feriados` WRITE;
/*!40000 ALTER TABLE `feriados` DISABLE KEYS */;
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (1,'2026-01-01','Año Nuevo','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (2,'2026-01-23','Día del Estado Plurinacional (trasladado)','nacional','Decreto Supremo N° 5521',NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (3,'2026-02-16','Carnaval','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (4,'2026-02-17','Carnaval','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (5,'2026-04-03','Viernes Santo','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (6,'2026-05-01','Día del Trabajo','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (7,'2026-06-04','Corpus Christi','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (8,'2026-06-05','Corpus Christi (feriado adicional)','nacional','Decreto Supremo N° 5521',NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (9,'2026-06-22','Año Nuevo Andino Amazónico (trasladado)','nacional','Decreto Supremo N° 5521',NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (10,'2026-08-06','Día de la Independencia','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (11,'2026-08-07','Día de la Independencia (feriado adicional)','nacional','Decreto Supremo N° 5521',NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (12,'2026-09-14','Aniversario de Cochabamba','departamental',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (13,'2026-11-02','Día de Todos los Difuntos','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (14,'2026-12-25','Navidad','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (15,'2027-01-01','Año Nuevo','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (16,'2027-01-22','Día del Estado Plurinacional','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (17,'2027-02-08','Carnaval','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (18,'2027-02-09','Carnaval','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (19,'2027-03-26','Viernes Santo','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (20,'2027-05-01','Día del Trabajo','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (21,'2027-05-27','Corpus Christi','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (22,'2027-06-21','Año Nuevo Andino Amazónico','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (23,'2027-08-06','Día de la Independencia','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (24,'2027-09-14','Aniversario de Cochabamba','departamental',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (25,'2027-11-02','Día de Todos los Difuntos','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
INSERT INTO `feriados` (`idferiado`, `fecha`, `nombre`, `tipo`, `observacion`, `registrado_por`, `created_at`, `updated_at`) VALUES (26,'2027-12-25','Navidad','nacional',NULL,NULL,'2026-10-29 12:31:09',NULL);
/*!40000 ALTER TABLE `feriados` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES (1,'2026-10-27-100000','App\\Database\\Migrations\\CrearRoles','default','App',1793113288,1);
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES (2,'2026-10-27-100100','App\\Database\\Migrations\\CrearPermisos','default','App',1793113288,1);
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES (3,'2026-10-27-100200','App\\Database\\Migrations\\CrearUsuarios','default','App',1793113288,1);
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES (4,'2026-10-27-100300','App\\Database\\Migrations\\CrearVacaciones','default','App',1793113288,1);
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES (5,'2026-10-27-120000','App\\Database\\Migrations\\AgregarFechaIngreso','default','App',1793123479,2);
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES (6,'2026-10-29-090000','App\\Database\\Migrations\\CrearCargos','default','App',1793277925,3);
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES (7,'2026-10-29-120000','App\\Database\\Migrations\\CrearFeriados','default','App',1793291469,4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos` (
  `idpermiso` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(60) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  PRIMARY KEY (`idpermiso`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Permisos del sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` (`idpermiso`, `clave`, `descripcion`) VALUES (1,'panel.ver','Acceder al panel principal');
INSERT INTO `permisos` (`idpermiso`, `clave`, `descripcion`) VALUES (2,'perfil.editar','Ver y editar su propio perfil');
INSERT INTO `permisos` (`idpermiso`, `clave`, `descripcion`) VALUES (3,'vacaciones.solicitar','Registrar solicitudes de vacaciones');
INSERT INTO `permisos` (`idpermiso`, `clave`, `descripcion`) VALUES (4,'vacaciones.ver_propias','Ver sus propias solicitudes de vacaciones');
INSERT INTO `permisos` (`idpermiso`, `clave`, `descripcion`) VALUES (5,'vacaciones.ver_todas','Ver las vacaciones de todos los colaboradores');
INSERT INTO `permisos` (`idpermiso`, `clave`, `descripcion`) VALUES (6,'vacaciones.autorizar','Autorizar o rechazar solicitudes de vacaciones');
INSERT INTO `permisos` (`idpermiso`, `clave`, `descripcion`) VALUES (7,'reportes.ver','Ver reportes de vacaciones');
INSERT INTO `permisos` (`idpermiso`, `clave`, `descripcion`) VALUES (8,'usuarios.ver','Ver la lista de usuarios');
INSERT INTO `permisos` (`idpermiso`, `clave`, `descripcion`) VALUES (9,'usuarios.gestionar','Crear, editar, activar y desactivar usuarios');
INSERT INTO `permisos` (`idpermiso`, `clave`, `descripcion`) VALUES (10,'roles.gestionar','Administrar roles y permisos');
INSERT INTO `permisos` (`idpermiso`, `clave`, `descripcion`) VALUES (11,'cargos.gestionar','Administrar el catálogo de cargos');
INSERT INTO `permisos` (`idpermiso`, `clave`, `descripcion`) VALUES (12,'feriados.gestionar','Administrar feriados y días no laborables');
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `rol_permiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol_permiso` (
  `idrol` int(10) unsigned NOT NULL,
  `idpermiso` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idrol`,`idpermiso`),
  KEY `fk_rolpermiso_permiso` (`idpermiso`),
  CONSTRAINT `fk_rolpermiso_permiso` FOREIGN KEY (`idpermiso`) REFERENCES `permisos` (`idpermiso`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rolpermiso_rol` FOREIGN KEY (`idrol`) REFERENCES `roles` (`idrol`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Permisos asignados a cada rol';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rol_permiso` WRITE;
/*!40000 ALTER TABLE `rol_permiso` DISABLE KEYS */;
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1,1);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1,2);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1,3);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1,4);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1,5);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1,6);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1,7);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1,8);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1,9);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1,10);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1,11);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (1,12);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (2,1);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (2,2);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (2,3);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (2,4);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (2,5);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (2,6);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (2,7);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (2,8);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (3,1);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (3,2);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (3,3);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (3,4);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (4,1);
INSERT INTO `rol_permiso` (`idrol`, `idpermiso`) VALUES (4,2);
/*!40000 ALTER TABLE `rol_permiso` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `idrol` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idrol`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Roles del sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`idrol`, `nombre`, `descripcion`, `created_at`) VALUES (1,'Administrador','Control total del sistema: usuarios, roles, permisos y vacaciones.','2026-10-27 11:01:28');
INSERT INTO `roles` (`idrol`, `nombre`, `descripcion`, `created_at`) VALUES (2,'Coordinador','Revisa y autoriza o rechaza las vacaciones de los colaboradores.','2026-10-27 11:01:28');
INSERT INTO `roles` (`idrol`, `nombre`, `descripcion`, `created_at`) VALUES (3,'Colaborador','Registra y consulta sus propias solicitudes de vacaciones.','2026-10-27 11:01:28');
INSERT INTO `roles` (`idrol`, `nombre`, `descripcion`, `created_at`) VALUES (4,'Invitado','Usuario recién registrado. Solo accede a su perfil hasta que se le asigne un rol.','2026-10-27 11:01:28');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `idusuario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `ci` varchar(20) NOT NULL COMMENT 'Cédula de identidad',
  `email` varchar(150) NOT NULL,
  `celular` bigint(20) unsigned NOT NULL,
  `fecha_ingreso` date DEFAULT NULL COMMENT 'Fecha de ingreso a la empresa',
  `idrol` int(10) unsigned NOT NULL,
  `idcargo` int(10) unsigned DEFAULT NULL COMMENT 'Cargo del usuario',
  `password` varchar(255) NOT NULL COMMENT 'Hash bcrypt',
  `estado` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = activo, 0 = inactivo',
  `email_verificado_en` datetime DEFAULT NULL,
  `token_verificacion` char(64) DEFAULT NULL COMMENT 'SHA-256 del token enviado por correo',
  `token_expira` datetime DEFAULT NULL,
  `ultimo_acceso` datetime DEFAULT NULL,
  `fechaderegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idusuario`),
  UNIQUE KEY `uq_usuarios_ci` (`ci`),
  UNIQUE KEY `uq_usuarios_email` (`email`),
  KEY `fk_usuarios_rol` (`idrol`),
  KEY `ix_usuarios_token` (`token_verificacion`),
  KEY `fk_usuarios_cargo` (`idcargo`),
  CONSTRAINT `fk_usuarios_cargo` FOREIGN KEY (`idcargo`) REFERENCES `cargos` (`idcargo`) ON UPDATE CASCADE,
  CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`idrol`) REFERENCES `roles` (`idrol`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Usuarios del sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` (`idusuario`, `nombres`, `apellidos`, `ci`, `email`, `celular`, `fecha_ingreso`, `idrol`, `idcargo`, `password`, `estado`, `email_verificado_en`, `token_verificacion`, `token_expira`, `ultimo_acceso`, `fechaderegistro`, `updated_at`) VALUES (1,'Administrador','del Sistema','0000000','admin@intergraphics.local',70000000,NULL,1,NULL,'$2y$10$whG7CtnZP4.akozrMgfQ7OejxxPUOQGIKaM0EPJb46yoaLuhr5Uva',1,'2026-10-27 11:01:28',NULL,NULL,NULL,'2026-10-27 11:01:28',NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `vacaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vacaciones` (
  `idvacacion` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idusuario` int(10) unsigned NOT NULL COMMENT 'Colaborador que solicita',
  `fecha_solicitud` date NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `dias_habiles` decimal(4,1) NOT NULL DEFAULT 0.0 COMMENT 'Días hábiles solicitados (admite medio día)',
  `motivo` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','autorizado','rechazado','cancelado') NOT NULL DEFAULT 'pendiente',
  `autorizado_por` int(10) unsigned DEFAULT NULL COMMENT 'Coordinador que autoriza o rechaza',
  `fecha_autorizacion` datetime DEFAULT NULL,
  `observacion_coordinador` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idvacacion`),
  KEY `fk_vacaciones_autorizador` (`autorizado_por`),
  KEY `ix_vacaciones_usuario_inicio` (`idusuario`,`fecha_inicio`),
  KEY `ix_vacaciones_estado` (`estado`),
  CONSTRAINT `fk_vacaciones_autorizador` FOREIGN KEY (`autorizado_por`) REFERENCES `usuarios` (`idusuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_vacaciones_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`) ON UPDATE CASCADE,
  CONSTRAINT `chk_vacaciones_fechas` CHECK (`fecha_fin` >= `fecha_inicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Registro de vacaciones';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `vacaciones` WRITE;
/*!40000 ALTER TABLE `vacaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `vacaciones` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

