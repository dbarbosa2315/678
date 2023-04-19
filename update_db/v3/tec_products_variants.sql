CREATE TABLE `tec_products_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_produto` INT(11) NOT NULL,
  `size` varchar(45) NOT NULL,
  `color` varchar(45) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
   PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `tec_products_variants` ADD INDEX `var_id_produto` (`id_produto` ASC);
