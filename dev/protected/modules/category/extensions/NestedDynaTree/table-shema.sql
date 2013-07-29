CREATE TABLE `cathegories` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `lft` INT(10) UNSIGNED NOT NULL,
  `rgt` INT(10) UNSIGNED NOT NULL,
  `level` SMALLINT(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `level` (`level`)
);

INSERT INTO `cathegories` (`name`, `lft`, `rgt`, `level`) VALUES ('root', 1, 2, 0);