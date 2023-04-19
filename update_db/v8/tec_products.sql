ALTER TABLE `tec_products` ADD COLUMN `sync_time` INT NOT NULL DEFAULT 0;

ALTER TABLE `tec_products` ADD INDEX `sync_idx` (`sync_time` ASC) ;