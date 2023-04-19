CREATE TABLE IF NOT EXISTS `tec_cards_tax` (
  `id` INT NOT NULL,
  `type` VARCHAR(45) NULL,
  `tax_client` DECIMAL(4,2) NULL,
  `tax_real` DECIMAL(4,2) NULL,
  `createdAt` DATETIME NULL,
  `updatedAt` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;