SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `document`.`category`;
DROP TABLE IF EXISTS `document`.`file`;
DROP TABLE IF EXISTS `document`.`folder`;
CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `folder_uuid` char(36) NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `path` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `folder_uuid` (`folder_uuid`) USING BTREE,
  KEY `category_id` (`category_id`) USING BTREE,
  CONSTRAINT `category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  CONSTRAINT `folder_uuid` FOREIGN KEY (`folder_uuid`) REFERENCES `folder` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `folder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
BEGIN;
LOCK TABLES `document`.`category` WRITE;
DELETE FROM `document`.`category`;
INSERT INTO `document`.`category` (`id`,`name`) VALUES (1, '釘選文件'),(2, '文件列表');
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `document`.`file` WRITE;
DELETE FROM `document`.`file`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `document`.`folder` WRITE;
DELETE FROM `document`.`folder`;
UNLOCK TABLES;
COMMIT;
CREATE DEFINER = `root`@`localhost` TRIGGER `file_uuid` BEFORE INSERT ON `file` FOR EACH ROW BEGIN
	SET new.uuid = UUID();
END;;
CREATE DEFINER = `root`@`localhost` TRIGGER `folder_uuid` BEFORE INSERT ON `folder` FOR EACH ROW BEGIN
	SET new.uuid = UUID();
END;;