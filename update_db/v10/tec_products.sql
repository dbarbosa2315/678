ALTER TABLE `tec_products` CHANGE COLUMN `tax` `tax` DECIMAL(25,2) DEFAULT 0.0;

UPDATE `tec_products` SET tax = 0.0;