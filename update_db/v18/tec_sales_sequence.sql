CREATE TABLE IF NOT EXISTS `tec_sales_sequence` (
  `id` INT NOT NULL,
  `day` DATE NOT NULL,
  PRIMARY KEY (`id` , `day`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;