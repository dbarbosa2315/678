ALTER TABLE `tec_products` CHANGE COLUMN `code` `ean` VARCHAR(50) NOT NULL;
ALTER TABLE `tec_products` CHANGE COLUMN `name` `code` CHAR(255) NOT NULL;
ALTER TABLE `tec_products` CHANGE COLUMN `details` `name` VARCHAR(50) NOT NULL;

ALTER TABLE `tec_products` ADD COLUMN `model` VARCHAR(45) NULL AFTER `category_id`;
ALTER TABLE `tec_products` ADD COLUMN `material` VARCHAR(45) NULL AFTER `model`;
ALTER TABLE `tec_products` ADD COLUMN `color` VARCHAR(45) NULL AFTER `material`;
ALTER TABLE `tec_products` ADD COLUMN `size` VARCHAR(45) NULL AFTER `color`;
ALTER TABLE `tec_products` ADD COLUMN `manga` VARCHAR(45) NULL AFTER `size`;
ALTER TABLE `tec_products` ADD COLUMN `season` VARCHAR(45) NULL AFTER `manga`;
ALTER TABLE `tec_products` ADD COLUMN `retail_tax` DECIMAL(4,2) NOT NULL DEFAULT 5.0;

/* remove auto-increment */
ALTER TABLE `tec_products` CHANGE COLUMN `id` `id` INT(11) NOT NULL;
