ALTER TABLE `tec_products` DROP COLUMN `size`;
ALTER TABLE `tec_products` DROP COLUMN `color`;

ALTER TABLE `tec_products` CHANGE COLUMN `barcode_symbology` `barcode_symbology` VARCHAR(20) NOT NULL DEFAULT 'ean13';

UPDATE tec_products SET barcode_symbology = 'ean13';