CREATE TABLE IF NOT EXISTS `tec_sellers` (
  `id` INT NOT NULL,
  `name` VARCHAR(45) NULL,
  `cod_loja` VARCHAR(45) NOT NULL,
  `status` CHAR(1) NULL,
  `createdAt` DATETIME NULL,
  `updatedAt` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;