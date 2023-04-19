ALTER TABLE tec_products_variants ADD COLUMN sku VARCHAR(45);

ALTER TABLE `tec_products_variants` ADD INDEX `sku_idx` (`sku` ASC) ;