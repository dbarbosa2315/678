ALTER TABLE `tec_suspended_sales` CHANGE COLUMN `total_quantity` `total_quantity` INT NOT NULL;

ALTER TABLE `tec_suspended_sales` ADD COLUMN `seller_id` INT NOT NULL AFTER `customer_name`;
ALTER TABLE `tec_suspended_sales` ADD COLUMN `total_cash` DECIMAL(25,2) NOT NULL DEFAULT 0.0;
ALTER TABLE `tec_suspended_sales` ADD COLUMN `total_credit` DECIMAL(25,2) NOT NULL DEFAULT 0.0;
ALTER TABLE `tec_suspended_sales` ADD COLUMN `total_debit` DECIMAL(25,2) NOT NULL DEFAULT 0.0;
ALTER TABLE `tec_suspended_sales` ADD COLUMN `total_transfer` DECIMAL(25,2) NOT NULL DEFAULT 0.0;
ALTER TABLE `tec_suspended_sales` ADD COLUMN `instalments_credit` INT NOT NULL DEFAULT 0;